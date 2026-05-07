<?php

namespace App\Console\Commands;

use App\Models\DietTracker\UserProfile;
use App\Models\DietTracker\DailySummary;
use App\Models\DietTracker\WeightLog;
use App\Services\TelegramService;
use Illuminate\Console\Command;

class SendWeeklyReport extends Command
{
    protected $signature = 'diet:weekly-report';
    protected $description = 'Kirim laporan mingguan ke semua user aktif (Minggu malam)';

    public function handle(): void
    {
        $telegram = new TelegramService();
        $profiles = UserProfile::where('aktif', true)
            ->whereNotNull('kalori_target')
            ->get();

        $sent = 0;
        $weekStart = now('Asia/Singapore')->startOfWeek();
        $weekEnd = now('Asia/Singapore');

        foreach ($profiles as $profile) {
            $summaries = DailySummary::where('profile_id', $profile->id)
                ->whereBetween('tanggal', [$weekStart->toDateString(), $weekEnd->toDateString()])
                ->get();

            if ($summaries->isEmpty()) continue;

            $weights = WeightLog::where('profile_id', $profile->id)
                ->whereBetween('tanggal', [$weekStart->toDateString(), $weekEnd->toDateString()])
                ->orderBy('tanggal')
                ->get();

            $avgKal = round($summaries->avg('total_kalori'));
            $avgP = round($summaries->avg('total_protein'), 1);
            $avgAir = round($summaries->avg('total_air_ml'));
            $totalEx = $summaries->sum('total_exercise_menit');
            $daysLogged = $summaries->count();
            $targetKal = $profile->kalori_target;
            $consistency = round(($daysLogged / 7) * 100);

            $text = "📊 <b>Laporan Mingguan</b>\n";
            $text .= "📅 {$weekStart->format('d/m')} - {$weekEnd->format('d/m/Y')}\n";
            $text .= "━━━━━━━━━━━━━━━\n\n";

            $text .= "📈 <b>Ringkasan:</b>\n";
            $text .= "   🔥 Rata-rata kalori: {$avgKal}/{$targetKal} kkal\n";
            $text .= "   🥩 Rata-rata protein: {$avgP}g\n";
            $text .= "   💧 Rata-rata air: {$avgAir}ml\n";
            $text .= "   🏃 Total olahraga: {$totalEx} menit\n";
            $text .= "   📅 Hari aktif: {$daysLogged}/7 ({$consistency}%)\n\n";

            if ($weights->count() >= 2) {
                $diff = round($weights->last()->berat_kg - $weights->first()->berat_kg, 1);
                $arrow = $diff > 0 ? '📈 +' : ($diff < 0 ? '📉 ' : '➡️ ');
                $text .= "⚖️ Berat: {$weights->first()->berat_kg} → {$weights->last()->berat_kg} ({$arrow}{$diff}kg)\n\n";
            }

            // Evaluation
            if ($consistency >= 80) {
                $text .= "🏆 <b>Konsistensi luar biasa!</b> Terus pertahankan!\n";
            } elseif ($consistency >= 50) {
                $text .= "👍 Lumayan! Coba tingkatkan konsistensi minggu depan.\n";
            } else {
                $text .= "💪 Ayo lebih rajin catat minggu depan!\n";
            }

            $text .= "\nSemangat minggu depan! 🚀";

            $result = $telegram->sendMessage($profile->telegram_chat_id, $text);
            if ($result['ok'] ?? false) $sent++;
        }

        $this->info("Sent weekly report to {$sent} users.");
    }
}
