<?php

namespace App\Http\Controllers\DietTracker;

use App\Http\Controllers\Controller;
use App\Models\DietTracker\UserProfile;
use App\Models\DietTracker\FoodLog;
use App\Models\DietTracker\DailySummary;
use App\Models\DietTracker\AiLog;
use App\Models\DietTracker\WaterLog;
use App\Models\DietTracker\ExerciseLog;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function index()
    {
        $today = now('Asia/Singapore');

        // Overall stats
        $totalFoodLogs = FoodLog::count();
        $totalFoodToday = FoodLog::whereDate('tanggal', $today->toDateString())->count();
        $totalAiRequests = AiLog::count();
        $aiToday = AiLog::whereDate('created_at', $today->toDateString())->count();

        // Daily calories chart data (14 days)
        $chartData = DailySummary::where('tanggal', '>=', $today->copy()->subDays(14)->toDateString())
            ->orderBy('tanggal')
            ->get()
            ->groupBy(fn($s) => $s->tanggal->format('d/m'))
            ->map(fn($group) => [
                'kalori' => round($group->avg('total_kalori')),
                'protein' => round($group->avg('total_protein'), 1),
                'air' => round($group->avg('total_air_ml')),
            ]);

        // Top foods
        $topFoods = FoodLog::selectRaw('nama_makanan, COUNT(*) as total, AVG(kalori) as avg_kalori')
            ->groupBy('nama_makanan')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // AI usage by model
        $aiByModel = AiLog::selectRaw('model_used, COUNT(*) as total, AVG(response_time_ms) as avg_time, SUM(tokens_used) as total_tokens')
            ->groupBy('model_used')
            ->get();

        // Hourly activity
        $hourlyActivity = FoodLog::selectRaw("strftime('%H', created_at) as hour, COUNT(*) as total")
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->pluck('total', 'hour');

        return view('diet.stats.index', compact(
            'totalFoodLogs', 'totalFoodToday', 'totalAiRequests', 'aiToday',
            'chartData', 'topFoods', 'aiByModel', 'hourlyActivity'
        ));
    }
}
