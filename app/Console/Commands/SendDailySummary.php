<?php

namespace App\Console\Commands;

use App\Models\DietTracker\UserProfile;
use App\Models\DietTracker\FoodLog;
use App\Models\DietTracker\WaterLog;
use App\Models\DietTracker\ExerciseLog;
use App\Models\DietTracker\DailySummary;
use App\Services\TelegramService;
use Illuminate\Console\Command;

class SendDailySummary extends Command
{
    protected $signature = 'reminders:daily-summary';
    protected $description = 'Kirim ringkasan harian ke semua user aktif';

    public function handle(): void
    {
        $telegram = new TelegramService();
        $profiles = UserProfile::where('aktif', true)
            ->whereNotNull('kalori_target')
            ->get();

        $sent = 0;
        $today = now('Asia/Singapore')->toDateString();

        foreach ($profiles as $profile) {
            DailySummary::recalculate($profile->id, $today);

            $foodLogs = FoodLog::where('profile_id', $profile->id)->whereDate('tanggal', $today)->get();
            $waterTotal = WaterLog::where('profile_id', $profile->id)->whereDate('tanggal', $today)->sum('jumlah_ml');
            $exerciseTotal = ExerciseLog::where('profile_id', $profile->id)->whereDate('tanggal', $today)->get();

            $totalKal = $foodLogs->sum('kalori');
            $targetKal = $profile->kalori_target;
            $pct = $targetKal > 0 ? round(($totalKal / $targetKal) * 100) : 0;
            $streak = $profile->streak;

            $text = "📊 <b>Ringkasan Hari Ini</b>\n";
            $text .= "📅 " . now('Asia/Singapore')->translatedFormat('l, d F Y') . "\n";
            $text .= "━━━━━━━━━━━━━━━\n\n";

            $text .= "🔥 Kalori: <b>{$totalKal}</b> / {$targetKal} kkal ({$pct}%)\n";
            $text .= "🥩 Protein: " . round($foodLogs->sum('protein'), 1) . "g\n";
            $text .= "🍚 Karbo: " . round($foodLogs->sum('karbohidrat'), 1) . "g\n";
            $text .= "🧈 Lemak: " . round($foodLogs->sum('lemak'), 1) . "g\n";
            $text .= "💧 Air: {$waterTotal}ml / " . $profile->getAirTarget() . "ml\n";

            if ($exerciseTotal->count() > 0) {
                $text .= "🏃 Olahraga: " . $exerciseTotal->sum('durasi_menit') . " menit\n";
            }

            $text .= "\n";

            // Evaluasi
            if ($pct >= 85 && $pct <= 110) {
                $text .= "✅ Bagus! Target tercapai.\n";
            } elseif ($pct < 50 && $foodLogs->count() > 0) {
                $text .= "⚠️ Kalori terlalu rendah. Jangan skip makan!\n";
            } elseif ($pct > 120) {
                $text .= "⚠️ Over kalori. Besok lebih dijaga ya!\n";
            } elseif ($foodLogs->count() === 0) {
                $text .= "📝 Belum ada catatan hari ini.\n";
            }

            // Streak info
            if ($streak && $streak->current_streak > 0) {
                $text .= "🔥 Streak: {$streak->current_streak} hari\n";
            }

            // Motivasi quotes
            $motivasi = [
                "💪 Konsistensi mengalahkan kesempurnaan!",
                "🌟 Setiap langkah kecil mendekatkan ke tujuan.",
                "🎯 Fokus pada progress, bukan perfection.",
                "💡 Tubuh sehat dimulai dari kebiasaan kecil.",
                "🚀 Kamu lebih kuat dari yang kamu kira!",
                "🌈 Hari baru, kesempatan baru untuk lebih baik.",
                "⭐ Jaga pola makan = investasi kesehatan.",
                "🏆 Champions are made in the kitchen!",
            ];
            $text .= "\n" . $motivasi[array_rand($motivasi)];
            $text .= "\n\nSelamat istirahat! 😴";

            $result = $telegram->sendMessage($profile->telegram_chat_id, $text);
            if ($result['ok'] ?? false) $sent++;
        }

        $this->info("Sent daily summary to {$sent} users.");
    }
}
