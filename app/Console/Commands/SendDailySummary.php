<?php

namespace App\Console\Commands;

use App\Models\DietTracker\UserProfile;
use App\Models\DietTracker\DailySummary;
use App\Services\DietHelperService;
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

        foreach ($profiles as $profile) {
            // Recalculate daily summary
            $today = now('Asia/Singapore')->toDateString();
            DailySummary::recalculate($profile->id, $today);

            // Generate and send summary
            $text = DietHelperService::generateDailySummaryText($profile);
            $result = $telegram->sendMessage($profile->telegram_chat_id, $text);

            if ($result['ok'] ?? false) {
                $sent++;
            }
        }

        $this->info("Sent daily summary to {$sent} users.");
    }
}
