<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected string $token;
    protected string $chatId;

    public function __construct()
    {
        $this->token = config('services.telegram.bot_token', '');
        $this->chatId = config('services.telegram.chat_id', '');
    }

    /**
     * Cek apakah Telegram sudah dikonfigurasi
     */
    public function isConfigured(): bool
    {
        return !empty($this->token) && !empty($this->chatId);
    }

    /**
     * Kirim pesan teks biasa
     */
    public function sendMessage(string $text, ?string $chatId = null): bool
    {
        if (!$this->isConfigured() && !$chatId) {
            Log::warning('Telegram not configured');
            return false;
        }

        try {
            $response = Http::timeout(10)->post(
                "https://api.telegram.org/bot{$this->token}/sendMessage",
                [
                    'chat_id' => $chatId ?? $this->chatId,
                    'text' => $text,
                    'parse_mode' => 'HTML',
                    'disable_web_page_preview' => true,
                ]
            );

            if ($response->successful()) {
                return true;
            }

            Log::error('Telegram send failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Telegram error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim pengingat makan
     */
    public function sendMakanReminder(string $judul, string $pesan, array $stats = []): bool
    {
        $text = "🍽 <b>{$judul}</b>\n\n{$pesan}";

        if (!empty($stats)) {
            $text .= "\n\n📊 <b>Status Hari Ini:</b>";
            if (isset($stats['kalori_masuk'])) {
                $text .= "\n• Kalori masuk: {$stats['kalori_masuk']} / {$stats['target_kalori']} kkal";
            }
            if (isset($stats['sisa_kalori'])) {
                $text .= "\n• Sisa budget: {$stats['sisa_kalori']} kkal";
            }
        }

        return $this->sendMessage($text);
    }

    /**
     * Kirim pengingat olahraga
     */
    public function sendOlahragaReminder(string $judul, string $pesan, array $stats = []): bool
    {
        $text = "🏃 <b>{$judul}</b>\n\n{$pesan}";

        if (!empty($stats)) {
            if (isset($stats['kalori_terbakar'])) {
                $text .= "\n\n🔥 Kalori terbakar hari ini: {$stats['kalori_terbakar']} kkal";
            }
        }

        return $this->sendMessage($text);
    }

    /**
     * Kirim pengingat minum air
     */
    public function sendMinumReminder(string $judul, string $pesan, array $stats = []): bool
    {
        $text = "💧 <b>{$judul}</b>\n\n{$pesan}";

        if (!empty($stats)) {
            if (isset($stats['total_minum'])) {
                $persen = $stats['target_air'] > 0 ? round(($stats['total_minum'] / $stats['target_air']) * 100) : 0;
                $text .= "\n\n📊 Progress: {$stats['total_minum']}ml / {$stats['target_air']}ml ({$persen}%)";
                $sisa = max(0, $stats['target_air'] - $stats['total_minum']);
                if ($sisa > 0) {
                    $gelasLagi = ceil($sisa / 250);
                    $text .= "\n• Sisa: {$sisa}ml (~{$gelasLagi} gelas lagi)";
                } else {
                    $text .= "\n✅ Target tercapai!";
                }
            }
        }

        return $this->sendMessage($text);
    }

    /**
     * Kirim pengingat timbang badan
     */
    public function sendTimbangReminder(string $judul, string $pesan, array $stats = []): bool
    {
        $text = "⚖️ <b>{$judul}</b>\n\n{$pesan}";

        if (!empty($stats)) {
            if (isset($stats['berat_sekarang'])) {
                $text .= "\n\n📊 Berat terakhir: {$stats['berat_sekarang']} kg";
                $text .= "\n• Target: {$stats['berat_target']} kg";
                $sisa = round($stats['berat_sekarang'] - $stats['berat_target'], 1);
                $text .= "\n• Sisa: {$sisa} kg lagi";
            }
        }

        return $this->sendMessage($text);
    }

    /**
     * Kirim pengingat tidur
     */
    public function sendTidurReminder(string $judul, string $pesan): bool
    {
        $text = "😴 <b>{$judul}</b>\n\n{$pesan}";
        return $this->sendMessage($text);
    }

    /**
     * Kirim ringkasan harian
     */
    public function sendDailySummary(array $data): bool
    {
        $text = "📋 <b>Ringkasan Hari Ini</b>\n";
        $text .= "📅 " . now()->translatedFormat('l, d F Y') . "\n\n";

        $text .= "🍽 <b>Makan:</b>\n";
        $text .= "• Kalori masuk: " . number_format($data['kalori_masuk']) . " / " . number_format($data['target_kalori']) . " kkal\n";
        $text .= "• Sisa budget: " . number_format($data['sisa_kalori']) . " kkal\n\n";

        $text .= "🏃 <b>Olahraga:</b>\n";
        $text .= "• Kalori terbakar: " . number_format($data['kalori_terbakar']) . " kkal\n\n";

        $text .= "💧 <b>Minum:</b>\n";
        $text .= "• Total: " . number_format($data['total_minum']) . " / " . number_format($data['target_air']) . "ml\n\n";

        $text .= "⚖️ <b>Berat:</b> {$data['berat_sekarang']} kg (target: {$data['berat_target']} kg)\n";

        $status = $data['sisa_kalori'] >= 0 ? '✅ On Track' : '⚠️ Kalori Berlebih';
        $text .= "\n<b>Status: {$status}</b>";

        return $this->sendMessage($text);
    }

    /**
     * Test koneksi
     */
    /**
     * Kirim file/dokumen
     */
    public function sendDocument(string $filePath, ?string $caption = null, ?string $chatId = null): bool
    {
        if (!$this->isConfigured() && !$chatId) {
            Log::warning('Telegram not configured');
            return false;
        }

        if (!file_exists($filePath)) {
            Log::error('Telegram sendDocument: file not found: ' . $filePath);
            return false;
        }

        try {
            $response = Http::timeout(30)
                ->attach('document', file_get_contents($filePath), basename($filePath))
                ->post("https://api.telegram.org/bot{$this->token}/sendDocument", [
                    'chat_id' => $chatId ?? $this->chatId,
                    'caption' => $caption,
                    'parse_mode' => 'HTML',
                ]);

            if ($response->successful()) {
                return true;
            }

            Log::error('Telegram sendDocument failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Telegram sendDocument error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim pesan dengan token custom (untuk bot lain)
     */
    public function sendMessageWithToken(string $token, string $chatId, string $text): bool
    {
        try {
            $response = Http::timeout(10)->post(
                "https://api.telegram.org/bot{$token}/sendMessage",
                [
                    'chat_id' => $chatId,
                    'text' => $text,
                    'parse_mode' => 'HTML',
                    'disable_web_page_preview' => true,
                ]
            );
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Telegram error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim file dengan token custom (untuk bot lain)
     */
    public function sendDocumentWithToken(string $token, string $chatId, string $filePath, ?string $caption = null): bool
    {
        if (!file_exists($filePath)) {
            return false;
        }

        try {
            $response = Http::timeout(30)
                ->attach('document', file_get_contents($filePath), basename($filePath))
                ->post("https://api.telegram.org/bot{$token}/sendDocument", [
                    'chat_id' => $chatId,
                    'caption' => $caption,
                    'parse_mode' => 'HTML',
                ]);
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Telegram sendDocument error: ' . $e->getMessage());
            return false;
        }
    }

    public function testConnection(): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'Token atau Chat ID belum diisi'];
        }

        $sent = $this->sendMessage("✅ <b>Test Koneksi Berhasil!</b>\n\nBot pengingat diet sudah terhubung.\n🕐 " . now()->format('H:i:s d/m/Y'));

        return [
            'success' => $sent,
            'message' => $sent ? 'Pesan test berhasil dikirim!' : 'Gagal mengirim pesan. Cek token dan chat_id.',
        ];
    }
}
