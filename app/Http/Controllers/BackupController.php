<?php

namespace App\Http\Controllers;

use App\Services\TelegramService;
use Illuminate\Http\Request;

class BackupController extends Controller
{
    /**
     * Download backup
     */
    public function download()
    {
        $dbPath = database_path('database.sqlite');
        if (!file_exists($dbPath)) {
            return redirect()->back()->with('error', 'Database tidak ditemukan!');
        }

        $filename = 'backup_' . now()->format('Y-m-d_H-i-s') . '.sqlite';
        $backupPath = storage_path('app/' . $filename);
        copy($dbPath, $backupPath);

        return response()->download($backupPath, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Kirim backup ke Telegram (bot backup)
     */
    public function sendToTelegram()
    {
        $token = config('services.telegram_backup.bot_token');
        $chatId = config('services.telegram_backup.chat_id');

        if (empty($token) || empty($chatId)) {
            return redirect()->back()->with('error', 'Bot Telegram Backup belum dikonfigurasi! Isi token di menu Pengingat > Backup.');
        }

        $dbPath = database_path('database.sqlite');
        if (!file_exists($dbPath)) {
            return redirect()->back()->with('error', 'Database tidak ditemukan!');
        }

        $filename = 'backup_' . now()->format('Y-m-d_H-i-s') . '.sqlite';
        $backupPath = storage_path('app/' . $filename);
        copy($dbPath, $backupPath);

        $size = round(filesize($backupPath) / 1024, 1);

        $caption = "💾 <b>Backup Database</b>\n\n"
            . "📅 " . now()->format('d/m/Y H:i:s') . "\n"
            . "📦 Ukuran: {$size} KB\n"
            . "🔧 Manual backup dari web";

        // Pakai bot backup
        $telegram = new TelegramService();
        $sent = $telegram->sendDocumentWithToken($token, $chatId, $backupPath, $caption);

        if (file_exists($backupPath)) {
            unlink($backupPath);
        }

        if ($sent) {
            return redirect()->back()->with('sukses', 'Backup berhasil dikirim ke Telegram! (' . $size . ' KB)');
        }

        return redirect()->back()->with('error', 'Gagal mengirim backup ke Telegram. Cek token bot backup.');
    }

    /**
     * Simpan config bot backup
     */
    public function saveConfig(Request $request)
    {
        $validated = $request->validate([
            'backup_bot_token' => 'required|string',
            'backup_chat_id' => 'required|string',
        ]);

        $envPath = base_path('.env');
        $env = file_get_contents($envPath);

        $env = preg_replace('/^TELEGRAM_BACKUP_BOT_TOKEN=.*/m', 'TELEGRAM_BACKUP_BOT_TOKEN=' . $validated['backup_bot_token'], $env);
        $env = preg_replace('/^TELEGRAM_BACKUP_CHAT_ID=.*/m', 'TELEGRAM_BACKUP_CHAT_ID=' . $validated['backup_chat_id'], $env);

        file_put_contents($envPath, $env);
        \Artisan::call('config:cache');

        return redirect()->back()->with('sukses', 'Config bot backup berhasil disimpan!');
    }

    /**
     * Test bot backup
     */
    public function testBackupBot()
    {
        $token = config('services.telegram_backup.bot_token');
        $chatId = config('services.telegram_backup.chat_id');

        if (empty($token) || empty($chatId)) {
            return redirect()->back()->with('error', 'Bot Telegram Backup belum dikonfigurasi!');
        }

        $telegram = new TelegramService();
        $sent = $telegram->sendMessageWithToken($token, $chatId, "✅ <b>Bot Backup Terhubung!</b>\n\n🕐 " . now()->format('H:i:s d/m/Y') . "\nBot ini akan mengirim backup database otomatis setiap hari.");

        return redirect()->back()->with($sent ? 'sukses' : 'error', $sent ? 'Test bot backup berhasil!' : 'Gagal. Cek token dan chat_id.');
    }
}
