<?php

namespace App\Http\Controllers\DietTracker;

use App\Http\Controllers\Controller;
use App\Models\DietTracker\UserProfile;
use App\Models\DietTracker\FoodLog;
use App\Models\DietTracker\WeightLog;
use App\Models\DietTracker\WaterLog;
use App\Models\DietTracker\ExerciseLog;
use App\Models\DietTracker\DailySummary;
use App\Models\DietTracker\AiLog;
use App\Models\DietTracker\Badge;
use App\Models\DietTracker\Streak;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = now('Asia/Singapore')->toDateString();
        $date = $request->get('date', $today);

        // Get all profiles + counts + today kalori sum (fix N+1 di blade)
        $profiles = UserProfile::withCount(['foodLogs', 'weightLogs', 'badges'])
            ->withSum([
                'foodLogs as today_kalori' => fn ($q) => $q->whereDate('tanggal', $today),
            ], 'kalori')
            ->get();
        $activeProfile = $profiles->first();

        // Stats overview — single aggregate query untuk AI logs
        $aiAggregate = AiLog::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN DATE(created_at) = ? THEN 1 ELSE 0 END) as today,
            SUM(CASE WHEN success = 1 THEN 1 ELSE 0 END) as success_count,
            AVG(CASE WHEN success = 1 THEN response_time_ms END) as avg_response
        ", [$today])->first();

        $aiTotal = (int) ($aiAggregate->total ?? 0);
        $aiSuccess = (int) ($aiAggregate->success_count ?? 0);

        $stats = [
            'total_users' => UserProfile::count(),
            'active_users' => UserProfile::where('aktif', true)->count(),
            'total_food_logs' => FoodLog::count(),
            'total_food_today' => FoodLog::whereDate('tanggal', $today)->count(),
            'total_ai_requests' => $aiTotal,
            'ai_requests_today' => (int) ($aiAggregate->today ?? 0),
            'ai_success_rate' => $aiTotal > 0 ? round($aiSuccess / $aiTotal * 100, 1) : 0,
            'avg_response_time' => round((float) ($aiAggregate->avg_response ?? 0)),
        ];

        // Recent food logs
        $recentFoodLogs = FoodLog::with('profile')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        // Recent AI logs
        $recentAiLogs = AiLog::with('profile')
            ->orderByDesc('created_at')
            ->limit(15)
            ->get();

        // Weight progress (if active profile exists)
        $weightProgress = [];
        if ($activeProfile) {
            $weightProgress = WeightLog::where('profile_id', $activeProfile->id)
                ->orderBy('tanggal')
                ->limit(30)
                ->get();
        }

        // Daily summaries for chart
        $dailySummaries = DailySummary::where('tanggal', '>=', now()->subDays(14)->toDateString())
            ->orderBy('tanggal')
            ->get()
            ->groupBy('tanggal');

        return view('diet.dashboard.index', compact(
            'profiles', 'stats', 'recentFoodLogs', 'recentAiLogs',
            'weightProgress', 'dailySummaries', 'date', 'today'
        ));
    }

    /**
     * Setup webhook via admin panel
     */
    public function setupWebhook()
    {
        $telegram = new TelegramService();
        $webhookUrl = config('app.url') . '/webhook/telegram-diet';

        $result = $telegram->setWebhook($webhookUrl);
        $telegram->setMyCommands();

        if ($result['ok'] ?? false) {
            return back()->with('sukses', "Webhook berhasil diset: {$webhookUrl}");
        }

        return back()->with('error', 'Gagal set webhook: ' . json_encode($result));
    }

    /**
     * Get webhook info
     */
    public function webhookInfo()
    {
        $telegram = new TelegramService();
        $info = $telegram->getWebhookInfo();

        return response()->json($info);
    }
}
