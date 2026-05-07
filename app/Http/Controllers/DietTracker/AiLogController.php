<?php

namespace App\Http\Controllers\DietTracker;

use App\Http\Controllers\Controller;
use App\Models\DietTracker\AiLog;
use Illuminate\Http\Request;

class AiLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = AiLog::with('profile')
            ->orderByDesc('created_at')
            ->paginate(50);

        $totalLogs = AiLog::count();
        $successCount = AiLog::where('success', true)->count();
        $failCount = AiLog::where('success', false)->count();
        $avgResponseTime = round(AiLog::where('success', true)->avg('response_time_ms') ?? 0);
        $totalTokens = AiLog::sum('tokens_used') ?? 0;

        return view('diet.ai-logs', compact(
            'logs', 'totalLogs', 'successCount', 'failCount',
            'avgResponseTime', 'totalTokens'
        ));
    }
}
