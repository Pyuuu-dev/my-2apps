<?php

namespace App\Services;

use App\Models\DietTracker\UserProfile;
use App\Models\DietTracker\DailySummary;
use App\Models\DietTracker\FoodLog;
use App\Models\DietTracker\WeightLog;
use App\Models\DietTracker\WaterLog;
use App\Models\DietTracker\ExerciseLog;

class DietHelperService
{
    /**
     * Get daily stats for a profile
     */
    public static function getDailyStats(UserProfile $profile, ?string $date = null): array
    {
        $date = $date ?? now('Asia/Singapore')->toDateString();

        $foodLogs = FoodLog::where('profile_id', $profile->id)
            ->whereDate('tanggal', $date)->get();

        $waterTotal = WaterLog::where('profile_id', $profile->id)
            ->whereDate('tanggal', $date)->sum('jumlah_ml');

        $exerciseLogs = ExerciseLog::where('profile_id', $profile->id)
            ->whereDate('tanggal', $date)->get();

        $totalKalori = $foodLogs->sum('kalori');
        $targetKalori = $profile->kalori_target ?: 2000;

        return [
            'tanggal' => $date,
            'total_kalori' => $totalKalori,
            'total_protein' => round($foodLogs->sum('protein'), 1),
            'total_karbo' => round($foodLogs->sum('karbohidrat'), 1),
            'total_lemak' => round($foodLogs->sum('lemak'), 1),
            'total_air_ml' => $waterTotal,
            'total_exercise_menit' => $exerciseLogs->sum('durasi_menit'),
            'total_kalori_terbakar' => $exerciseLogs->sum('kalori_terbakar'),
            'target_kalori' => $targetKalori,
            'sisa_kalori' => $targetKalori - $totalKalori,
            'pct_target' => $targetKalori > 0 ? round(($totalKalori / $targetKalori) * 100, 1) : 0,
            'jumlah_makan' => $foodLogs->count(),
            'food_logs' => $foodLogs,
            'exercise_logs' => $exerciseLogs,
        ];
    }

    /**
     * Get weekly stats
     */
    public static function getWeeklyStats(UserProfile $profile): array
    {
        $today = now('Asia/Singapore');
        $weekStart = $today->copy()->startOfWeek();

        $summaries = DailySummary::where('profile_id', $profile->id)
            ->whereBetween('tanggal', [$weekStart->toDateString(), $today->toDateString()])
            ->orderBy('tanggal')
            ->get();

        $weights = WeightLog::where('profile_id', $profile->id)
            ->where('tanggal', '>=', $weekStart->toDateString())
            ->orderBy('tanggal')
            ->get();

        return [
            'period' => $weekStart->format('d/m') . ' - ' . $today->format('d/m/Y'),
            'days_logged' => $summaries->count(),
            'avg_kalori' => round($summaries->avg('total_kalori') ?? 0),
            'avg_protein' => round($summaries->avg('total_protein') ?? 0, 1),
            'avg_karbo' => round($summaries->avg('total_karbo') ?? 0, 1),
            'avg_lemak' => round($summaries->avg('total_lemak') ?? 0, 1),
            'avg_air' => round($summaries->avg('total_air_ml') ?? 0),
            'total_exercise' => $summaries->sum('total_exercise_menit'),
            'total_kalori_terbakar' => $summaries->sum('total_kalori_terbakar'),
            'summaries' => $summaries,
            'weights' => $weights,
        ];
    }

    /**
     * Generate daily summary text for Telegram
     */
    public static function generateDailySummaryText(UserProfile $profile): string
    {
        $stats = self::getDailyStats($profile);

        $text = "📊 <b>Ringkasan Hari Ini</b>\n";
        $text .= "📅 " . now('Asia/Singapore')->translatedFormat('l, d F Y') . "\n";
        $text .= "━━━━━━━━━━━━━━━\n\n";

        $pct = $stats['pct_target'];
        $text .= "🔥 Kalori: {$stats['total_kalori']} / {$stats['target_kalori']} kkal ({$pct}%)\n";
        $text .= "🥩 Protein: {$stats['total_protein']}g\n";
        $text .= "🍚 Karbo: {$stats['total_karbo']}g\n";
        $text .= "🧈 Lemak: {$stats['total_lemak']}g\n";
        $text .= "💧 Air: {$stats['total_air_ml']}ml\n";

        if ($stats['total_exercise_menit'] > 0) {
            $text .= "🏃 Olahraga: {$stats['total_exercise_menit']} menit ({$stats['total_kalori_terbakar']} kkal)\n";
        }

        $text .= "\n";

        // Evaluation
        if ($pct >= 80 && $pct <= 110) {
            $text .= "✅ Bagus! Target kalori tercapai dengan baik.\n";
        } elseif ($pct < 60) {
            $text .= "⚠️ Kalori terlalu rendah. Jangan skip makan ya!\n";
        } elseif ($pct > 120) {
            $text .= "⚠️ Over kalori hari ini. Besok lebih dijaga ya!\n";
        }

        $text .= "\nSelamat istirahat! 😴💤";

        return $text;
    }
}
