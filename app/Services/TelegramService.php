<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    private string $botToken;
    private string $baseUrl;

    public function __construct(?string $token = null)
    {
        $this->botToken = $token ?? config('services.telegram.bot_token');
        $this->baseUrl = "https://api.telegram.org/bot{$this->botToken}";
    }

    /**
     * Kirim pesan teks
     */
    public function sendMessage(string $chatId, string $text, array $options = []): array
    {
        $payload = array_merge([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ], $options);

        return $this->request('sendMessage', $payload);
    }

    /**
     * Kirim pesan dengan inline keyboard
     */
    public function sendMessageWithKeyboard(string $chatId, string $text, array $keyboard): array
    {
        return $this->sendMessage($chatId, $text, [
            'reply_markup' => json_encode([
                'inline_keyboard' => $keyboard,
            ]),
        ]);
    }

    /**
     * Kirim pesan dengan reply keyboard
     */
    public function sendMessageWithReplyKeyboard(string $chatId, string $text, array $keyboard, bool $oneTime = true): array
    {
        return $this->sendMessage($chatId, $text, [
            'reply_markup' => json_encode([
                'keyboard' => $keyboard,
                'resize_keyboard' => true,
                'one_time_keyboard' => $oneTime,
            ]),
        ]);
    }

    /**
     * Hapus reply keyboard
     */
    public function removeKeyboard(string $chatId, string $text): array
    {
        return $this->sendMessage($chatId, $text, [
            'reply_markup' => json_encode([
                'remove_keyboard' => true,
            ]),
        ]);
    }

    /**
     * Edit pesan yang sudah terkirim
     */
    public function editMessage(string $chatId, int $messageId, string $text, ?array $keyboard = null): array
    {
        $payload = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ];

        if ($keyboard) {
            $payload['reply_markup'] = json_encode([
                'inline_keyboard' => $keyboard,
            ]);
        }

        return $this->request('editMessageText', $payload);
    }

    /**
     * Answer callback query
     */
    public function answerCallback(string $callbackId, string $text = '', bool $showAlert = false): array
    {
        return $this->request('answerCallbackQuery', [
            'callback_query_id' => $callbackId,
            'text' => $text,
            'show_alert' => $showAlert,
        ]);
    }

    /**
     * Kirim foto
     */
    public function sendPhoto(string $chatId, string $photoUrl, string $caption = ''): array
    {
        return $this->request('sendPhoto', [
            'chat_id' => $chatId,
            'photo' => $photoUrl,
            'caption' => $caption,
            'parse_mode' => 'HTML',
        ]);
    }

    /**
     * Kirim action (typing, upload_photo, etc)
     */
    public function sendChatAction(string $chatId, string $action = 'typing'): array
    {
        return $this->request('sendChatAction', [
            'chat_id' => $chatId,
            'action' => $action,
        ]);
    }

    /**
     * Get file URL from file_id
     */
    public function getFileUrl(string $fileId): ?string
    {
        $result = $this->request('getFile', ['file_id' => $fileId]);

        if (isset($result['result']['file_path'])) {
            return "https://api.telegram.org/file/bot{$this->botToken}/{$result['result']['file_path']}";
        }

        return null;
    }

    /**
     * Set webhook
     */
    public function setWebhook(string $url): array
    {
        return $this->request('setWebhook', [
            'url' => $url,
            'allowed_updates' => ['message', 'callback_query'],
        ]);
    }

    /**
     * Delete webhook
     */
    public function deleteWebhook(): array
    {
        return $this->request('deleteWebhook');
    }

    /**
     * Get webhook info
     */
    public function getWebhookInfo(): array
    {
        return $this->request('getWebhookInfo');
    }

    /**
     * Set bot commands menu
     */
    public function setMyCommands(): array
    {
        $commands = [
            ['command' => 'menu', 'description' => 'Menu utama'],
            ['command' => 'makan', 'description' => 'Catat makanan (support: nasi + ayam + teh)'],
            ['command' => 'air', 'description' => 'Catat air minum (ml)'],
            ['command' => 'berat', 'description' => 'Catat berat badan (kg)'],
            ['command' => 'olahraga', 'description' => 'Catat olahraga'],
            ['command' => 'd', 'description' => 'Dashboard hari ini'],
            ['command' => 'stats', 'description' => 'Statistik mingguan'],
            ['command' => 'riwayat', 'description' => 'Riwayat makan (+ kemarin/3 hari)'],
            ['command' => 'fav', 'description' => 'Quick add dari favorit'],
            ['command' => 'rekomendasi', 'description' => 'AI rekomendasi menu'],
            ['command' => 'smartreminder', 'description' => 'AI saran jadwal reminder'],
            ['command' => 'timeline', 'description' => 'Estimasi capai target berat'],
            ['command' => 'motivasi', 'description' => 'Motivasi harian AI'],
            ['command' => 'puasa', 'description' => 'Intermittent fasting tracker'],
            ['command' => 'tidur', 'description' => 'Catat tidur (23:00 06:30)'],
            ['command' => 'profil', 'description' => 'Lihat profil & body metrics'],
            ['command' => 'badge', 'description' => 'Achievement badges'],
            ['command' => 'help', 'description' => 'Panduan lengkap'],
        ];

        return $this->request('setMyCommands', ['commands' => $commands]);
    }

    /**
     * Send document
     */
    public function sendDocument(string $chatId, string $filePath, string $caption = '', ?string $token = null): array
    {
        $url = $token
            ? "https://api.telegram.org/bot{$token}/sendDocument"
            : "{$this->baseUrl}/sendDocument";

        try {
            $response = Http::timeout(30)
                ->attach('document', file_get_contents($filePath), basename($filePath))
                ->post($url, [
                    'chat_id' => $chatId,
                    'caption' => $caption,
                ]);

            return $response->json() ?? [];
        } catch (\Exception $e) {
            Log::error('Telegram sendDocument error', ['error' => $e->getMessage()]);
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Make API request
     */
    private function request(string $method, array $params = []): array
    {
        try {
            $response = Http::timeout(15)
                ->post("{$this->baseUrl}/{$method}", $params);

            return $response->json() ?? [];
        } catch (\Exception $e) {
            Log::error("Telegram API error [{$method}]", ['error' => $e->getMessage()]);
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }
}
