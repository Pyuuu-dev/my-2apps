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

        // Get all profiles (admin view)
        $profiles = UserProfile::withCount(['foodLogs', 'weightLogs', 'badges'])->get();
        $activeProfile = $profiles->first();

        // Stats overview
        $stats = [
            'total_users' => UserProfile::count(),
            'active_users' => UserProfile::where('aktif', true)->count(),
            'total_food_logs' => FoodLog::count(),
            'total_food_today' => FoodLog::whereDate('tanggal', $today)->count(),
            'total_ai_requests' => AiLog::count(),
            'ai_requests_today' => AiLog::whereDate('created_at', $today)->count(),
            'ai_success_rate' => AiLog::count() > 0
                ? round(AiLog::where('success', true)->count() / AiLog::count() * 100, 1)
                : 0,
            'avg_response_time' => round(AiLog::where('success', true)->avg('response_time_ms') ?? 0),
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
