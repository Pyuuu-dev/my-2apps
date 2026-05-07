<?php

namespace App\Console\Commands;

use App\Models\DietTracker\UserProfile;
use App\Models\DietTracker\WeightLog;
use Illuminate\Console\Command;

class WeeklyRecalculate extends Command
{
    protected $signature = 'diet:recalculate';
    protected $description = 'Auto-recalculate target berdasarkan berat terbaru (mingguan)';

    public function handle(): void
    {
        $profiles = UserProfile::where('aktif', true)
            ->whereNotNull('kalori_target')
            ->get();

        $updated = 0;

        foreach ($profiles as $profile) {
            // Cek apakah ada weight log baru minggu ini
            $latestWeight = WeightLog::where('profile_id', $profile->id)
                ->orderByDesc('tanggal')->first();

            if (!$latestWeight) continue;

            // Update berat di profil jika berbeda
            if ($profile->berat_kg != $latestWeight->berat_kg) {
                $profile->update(['berat_kg' => $latestWeight->berat_kg]);
                $profile->recalculate();
                $profile->update(['air_target_ml' => (int) round($profile->berat_kg * 33)]);
                $updated++;
            }
        }

        $this->info("Recalculated {$updated} profiles.");
    }
}
