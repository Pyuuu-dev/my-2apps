<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database';
    protected $description = 'Backup database SQLite dan kirim ke Telegram (bot backup)';

    public function handle(): int
    {
        $token = config('services.telegram_backup.bot_token');
        $chatId = config('services.telegram_backup.chat_id');

        if (empty($token) || empty($chatId)) {
            $this->info('Bot Telegram Backup belum dikonfigurasi. Skip.');
            return 0;
        }

        $dbPath = database_path('database.sqlite');
        if (!file_exists($dbPath)) {
            $this->error('Database tidak ditemukan!');
            return 1;
        }

        $filename = 'backup_' . now()->format('Y-m-d_H-i-s') . '.sqlite';
        $backupPath = storage_path('app/' . $filename);
        copy($dbPath, $backupPath);

        $size = round(filesize($backupPath) / 1024, 1);

        $caption = "💾 <b>Auto Backup Harian</b>\n\n"
            . "📅 " . now()->translatedFormat('l, d F Y H:i') . "\n"
            . "📦 Ukuran: {$size} KB\n"
            . "🤖 Otomatis setiap hari jam 02:00";

        $telegram = new TelegramService();
        $sent = $telegram->sendDocumentWithToken($token, $chatId, $backupPath, $caption);

        if (file_exists($backupPath)) {
            unlink($backupPath);
        }

        if ($sent) {
            $this->info("Backup berhasil dikirim ke Telegram ({$size} KB)");
            return 0;
        }

        $this->error('Gagal mengirim backup ke Telegram.');
        return 1;
    }
}
