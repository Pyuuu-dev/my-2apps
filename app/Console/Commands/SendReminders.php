<?php

namespace App\Console\Commands;

use App\Models\DietTracker\DietPlan;
use App\Models\DietTracker\Meal;
use App\Models\DietTracker\Exercise;
use App\Models\DietTracker\WaterLog;
use App\Models\DietTracker\Reminder;
use App\Services\DietHelperService;
use App\Services\TelegramService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendReminders extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Kirim pengingat diet via Telegram';

    public function handle(): int
    {
        $telegram = new TelegramService();

        if (!$telegram->isConfigured()) {
            $this->info('Telegram belum dikonfigurasi. Skip.');
            return 0;
        }

        $now = now();
        $currentTime = $now->format('H:i');
        $today = $now->toDateString();

        $plan = DietPlan::getActivePlan();
        if (!$plan) {
            $this->info('Tidak ada program diet aktif.');
            return 0;
        }

        // Ambil pengingat yang waktunya sekarang (toleransi 1 menit)
        $reminders = Reminder::where('diet_plan_id', $plan->id)
            ->where('aktif', true)
            ->get()
            ->filter(function ($rem) use ($currentTime, $now) {
                // Cek waktu (toleransi 1 menit)
                $reminderTime = Carbon::parse($rem->waktu)->format('H:i');
                if ($reminderTime !== $currentTime) return false;

                // Cek hari
                if (!$rem->shouldSendToday()) return false;

                // Cek belum dikirim hari ini
                if ($rem->alreadySentToday()) return false;

                return true;
            });

        if ($reminders->isEmpty()) {
            $this->info("Tidak ada pengingat untuk dikirim saat ini ({$currentTime}).");
            return 0;
        }

        // Siapkan stats
        $stats = $this->getStats($plan, $today);

        $sent = 0;
        foreach ($reminders as $reminder) {
            $success = match ($reminder->tipe) {
                'makan' => $telegram->sendMakanReminder($reminder->judul, $reminder->pesan ?? 'Waktunya makan! Jangan lupa catat makananmu.', $stats),
                'olahraga' => $telegram->sendOlahragaReminder($reminder->judul, $reminder->pesan ?? 'Waktunya olahraga! Tetap semangat!', $stats),
                'minum' => $telegram->sendMinumReminder($reminder->judul, $reminder->pesan ?? 'Jangan lupa minum air putih!', $stats),
                'timbang' => $telegram->sendTimbangReminder($reminder->judul, $reminder->pesan ?? 'Waktunya timbang berat badan.', $stats),
                'tidur' => $telegram->sendTidurReminder($reminder->judul, $reminder->pesan ?? 'Waktunya istirahat. Tidur cukup penting untuk diet!'),
                default => $telegram->sendMessage("🔔 <b>{$reminder->judul}</b>\n\n{$reminder->pesan}"),
            };

            if ($success) {
                $reminder->update(['last_sent_at' => now()]);
                $sent++;
                $this->info("✓ Terkirim: {$reminder->judul} ({$reminder->tipe})");
            } else {
                $this->error("✗ Gagal: {$reminder->judul}");
            }
        }

        $this->info("Selesai. {$sent}/{$reminders->count()} pengingat terkirim.");
        return 0;
    }

    /**
     * Ambil stats hari ini untuk dilampirkan di pesan
     */
    private function getStats(DietPlan $plan, string $today): array
    {
        $kaloriMasuk = Meal::where('diet_plan_id', $plan->id)
            ->whereDate('tanggal', $today)->sum('kalori');
        $kaloriTerbakar = Exercise::where('diet_plan_id', $plan->id)
            ->whereDate('tanggal', $today)->sum('kalori_terbakar');
        $totalMinum = WaterLog::where('diet_plan_id', $plan->id)
            ->whereDate('tanggal', $today)->sum('jumlah_ml');

        $smart = DietHelperService::generateSmartPlan(
            $plan->gender, $plan->umur, $plan->tinggi_cm,
            $plan->berat_sekarang ?? $plan->berat_awal, $plan->level_aktivitas
        );

        return [
            'kalori_masuk' => number_format($kaloriMasuk),
            'target_kalori' => number_format($plan->kalori_harian_target),
            'sisa_kalori' => number_format($plan->kalori_harian_target - $kaloriMasuk),
            'kalori_terbakar' => number_format($kaloriTerbakar),
            'total_minum' => number_format($totalMinum),
            'target_air' => number_format($smart['target_harian']['air_ml']),
            'berat_sekarang' => $plan->berat_sekarang ?? $plan->berat_awal,
            'berat_target' => $plan->berat_target,
        ];
    }
}
