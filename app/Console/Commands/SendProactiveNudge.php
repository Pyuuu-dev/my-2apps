<?php

namespace App\Console\Commands;

use App\Models\DietTracker\UserProfile;
use App\Models\DietTracker\FoodLog;
use App\Services\TelegramService;
use Illuminate\Console\Command;

class SendProactiveNudge extends Command
{
    protected $signature = 'diet:nudge';
    protected $description = 'Kirim nudge proaktif jika user belum makan pada jam tertentu';

    public function handle(): void
    {
        $telegram = new TelegramService();
        $now = now('Asia/Singapore');
        $hour = (int) $now->format('H');
        $today = $now->toDateString();

        // Hanya nudge pada jam tertentu
        $nudgeConfig = [
            9 => ['waktu' => 'sarapan', 'pesan' => '🌅 Sudah sarapan? Jangan skip ya! Sarapan penting untuk energi pagi.'],
            13 => ['waktu' => 'makan_siang', 'pesan' => '☀️ Sudah makan siang? Jangan lupa catat ya!'],
            20 => ['waktu' => 'makan_malam', 'pesan' => '🌙 Sudah makan malam? Catat sebelum lupa!'],
        ];

        if (!isset($nudgeConfig[$hour])) return;

        $config = $nudgeConfig[$hour];

        $profiles = UserProfile::where('aktif', true)
            ->where('proactive_nudge', true)
            ->whereNotNull('kalori_target')
            ->get();

        $sent = 0;

        foreach ($profiles as $profile) {
            // Cek apakah sudah ada log untuk waktu makan ini
            $hasLog = FoodLog::where('profile_id', $profile->id)
                ->whereDate('tanggal', $today)
                ->where('waktu_makan', $config['waktu'])
                ->exists();

            if ($hasLog) continue;

            $text = "{$config['pesan']}\n\nKetik /makan atau kirim foto makanan 📸";

            $keyboard = [
                [
                    ['text' => '🍽 Catat Sekarang', 'callback_data' => 'log_food'],
                    ['text' => '⭐ Quick Add', 'callback_data' => 'quick_add_menu'],
                ],
            ];

            $result = $telegram->sendMessageWithKeyboard($profile->telegram_chat_id, $text, $keyboard);
            if ($result['ok'] ?? false) $sent++;
        }

        if ($sent > 0) {
            $this->info("Sent {$sent} nudges for {$config['waktu']}.");
        }
    }
}
