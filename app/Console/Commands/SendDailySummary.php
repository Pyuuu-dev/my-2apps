<?php

namespace App\Console\Commands;

use App\Models\DietTracker\DietPlan;
use App\Models\DietTracker\Meal;
use App\Models\DietTracker\Exercise;
use App\Models\DietTracker\WaterLog;
use App\Services\DietHelperService;
use App\Services\TelegramService;
use Illuminate\Console\Command;

class SendDailySummary extends Command
{
    protected $signature = 'reminders:daily-summary';
    protected $description = 'Kirim ringkasan harian via Telegram';

    public function handle(): int
    {
        $telegram = new TelegramService();

        if (!$telegram->isConfigured()) {
            $this->info('Telegram belum dikonfigurasi. Skip.');
            return 0;
        }

        $plan = DietPlan::getActivePlan();
        if (!$plan) {
            $this->info('Tidak ada program diet aktif.');
            return 0;
        }

        $today = now()->toDateString();

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

        $success = $telegram->sendDailySummary([
            'kalori_masuk' => $kaloriMasuk,
            'target_kalori' => $plan->kalori_harian_target,
            'sisa_kalori' => $plan->kalori_harian_target - $kaloriMasuk,
            'kalori_terbakar' => $kaloriTerbakar,
            'total_minum' => $totalMinum,
            'target_air' => $smart['target_harian']['air_ml'],
            'berat_sekarang' => $plan->berat_sekarang ?? $plan->berat_awal,
            'berat_target' => $plan->berat_target,
        ]);

        if ($success) {
            $this->info('Ringkasan harian berhasil dikirim!');
        } else {
            $this->error('Gagal mengirim ringkasan harian.');
        }

        return $success ? 0 : 1;
    }
}
