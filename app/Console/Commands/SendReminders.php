<?php

namespace App\Console\Commands;

use App\Models\DietTracker\Reminder;
use App\Services\TelegramService;
use Illuminate\Console\Command;

class SendReminders extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Kirim pengingat diet yang sudah waktunya';

    public function handle(): void
    {
        $telegram = new TelegramService();
        $reminders = Reminder::where('aktif', true)
            ->with('profile')
            ->get();

        $sent = 0;

        foreach ($reminders as $reminder) {
            if (!$reminder->profile || !$reminder->shouldSendNow()) {
                continue;
            }

            $text = "⏰ <b>{$reminder->judul}</b>\n\n{$reminder->pesan}";
            $result = $telegram->sendMessage($reminder->profile->telegram_chat_id, $text);

            if ($result['ok'] ?? false) {
                $reminder->update(['last_sent_at' => now()]);
                $sent++;
            }
        }

        if ($sent > 0) {
            $this->info("Sent {$sent} reminders.");
        }
    }
}
