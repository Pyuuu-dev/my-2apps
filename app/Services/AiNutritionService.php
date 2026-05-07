<?php

namespace App\Services;

use App\Models\DietTracker\AiLog;
use App\Models\DietTracker\FoodDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiNutritionService
{
    private string $apiUrl;
    private string $apiKey;
    private string $textModel;
    private string $visionModel;

    public function __construct()
    {
        $this->apiUrl = config('services.enowx.api_url');
        $this->apiKey = config('services.enowx.api_key');
        $this->textModel = config('services.enowx.model');
        $this->visionModel = config('services.enowx.vision_model');
    }

    /**
     * Estimasi nutrisi dari nama makanan (text)
     */
    public function estimateFromText(string $foodDescription, ?int $profileId = null): array
    {
        // Cek database lokal dulu
        $dbResult = FoodDatabase::search($foodDescription, 1)->first();
        if ($dbResult) {
            return [
                'success' => true,
                'source' => 'database',
                'data' => [
                    'nama' => $dbResult->nama,
                    'kalori' => $dbResult->kalori,
                    'protein' => $dbResult->protein,
                    'karbohidrat' => $dbResult->karbohidrat,
                    'lemak' => $dbResult->lemak,
                    'porsi' => 1,
                    'satuan_porsi' => $dbResult->satuan_porsi,
                ],
            ];
        }

        $prompt = <<<PROMPT
Kamu adalah ahli gizi Indonesia. Analisis makanan berikut dan berikan estimasi nutrisi.

Makanan: {$foodDescription}

Berikan response dalam format JSON SAJA (tanpa markdown, tanpa penjelasan):
{
    "nama": "nama makanan yang terdeteksi",
    "kalori": angka_kalori_per_porsi,
    "protein": angka_gram_protein,
    "karbohidrat": angka_gram_karbohidrat,
    "lemak": angka_gram_lemak,
    "porsi": 1,
    "satuan_porsi": "deskripsi porsi",
    "confidence": "high/medium/low"
}

Penting:
- Gunakan data nutrisi makanan Indonesia yang akurat
- Kalori dalam kcal
- Protein, karbohidrat, lemak dalam gram
- Jika ada jumlah porsi disebutkan, kalikan nutrisinya
PROMPT;

        return $this->callTextApi($prompt, $profileId);
    }

    /**
     * Estimasi nutrisi dari foto makanan (vision)
     */
    public function estimateFromImage(string $imageUrl, ?int $profileId = null): array
    {
        $prompt = <<<PROMPT
Kamu adalah ahli gizi Indonesia. Analisis foto makanan ini dan identifikasi semua makanan yang terlihat.

Berikan response dalam format JSON SAJA (tanpa markdown, tanpa penjelasan):
{
    "items": [
        {
            "nama": "nama makanan",
            "kalori": angka_kalori,
            "protein": angka_gram,
            "karbohidrat": angka_gram,
            "lemak": angka_gram,
            "porsi": 1,
            "satuan_porsi": "deskripsi porsi"
        }
    ],
    "total_kalori": total_semua_kalori,
    "total_protein": total_protein,
    "total_karbohidrat": total_karbohidrat,
    "total_lemak": total_lemak,
    "deskripsi": "deskripsi singkat makanan yang terlihat",
    "confidence": "high/medium/low"
}

Penting:
- Identifikasi semua makanan yang terlihat di foto
- Gunakan data nutrisi makanan Indonesia yang akurat
- Estimasi porsi berdasarkan ukuran visual
PROMPT;

        return $this->callVisionApi($prompt, $imageUrl, $profileId);
    }

    /**
     * Rekomendasi menu berdasarkan sisa kalori
     */
    public function recommendMenu(int $sisaKalori, string $goal, array $currentMacros = [], ?int $profileId = null): array
    {
        $macroInfo = '';
        if (!empty($currentMacros)) {
            $macroInfo = "Protein sudah: {$currentMacros['protein']}g, Karbo sudah: {$currentMacros['karbo']}g, Lemak sudah: {$currentMacros['lemak']}g.";
        }

        $prompt = <<<PROMPT
Kamu adalah personal nutrition coach Indonesia. Berikan rekomendasi menu makanan.

Kondisi:
- Sisa kalori hari ini: {$sisaKalori} kkal
- Goal: {$goal}
- {$macroInfo}

Berikan response dalam format JSON SAJA:
{
    "rekomendasi": [
        {
            "nama": "nama makanan",
            "kalori": angka,
            "protein": angka_gram,
            "alasan": "kenapa cocok"
        }
    ],
    "tips": "saran singkat untuk hari ini",
    "warning": "peringatan jika ada (null jika tidak ada)"
}

Penting:
- Rekomendasikan 3-5 makanan Indonesia yang mudah didapat
- Sesuaikan dengan goal (cutting = tinggi protein rendah karbo, bulking = tinggi kalori protein)
- Berikan tips yang actionable
PROMPT;

        return $this->callTextApi($prompt, $profileId, 'recommendation');
    }

    /**
     * Analisis pola makan mingguan
     */
    public function analyzeWeeklyPattern(array $weeklyData, ?int $profileId = null): array
    {
        $dataJson = json_encode($weeklyData, JSON_PRETTY_PRINT);

        $prompt = <<<PROMPT
Kamu adalah personal nutrition coach Indonesia. Analisis pola makan seminggu ini.

Data mingguan:
{$dataJson}

Berikan response dalam format JSON SAJA:
{
    "analisis": "ringkasan pola makan dalam 2-3 kalimat",
    "kelebihan": ["hal positif 1", "hal positif 2"],
    "kekurangan": ["hal yang perlu diperbaiki 1", "hal yang perlu diperbaiki 2"],
    "saran": ["saran spesifik 1", "saran spesifik 2", "saran spesifik 3"],
    "skor_kesehatan": angka_1_sampai_10,
    "emoji_mood": "emoji yang menggambarkan kondisi"
}
PROMPT;

        return $this->callTextApi($prompt, $profileId, 'recommendation');
    }

    /**
     * Call text completion API
     */
    private function callTextApi(string $prompt, ?int $profileId = null, string $tipe = 'text'): array
    {
        $startTime = microtime(true);

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$this->apiKey}",
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->apiUrl}/chat/completions", [
                    'model' => $this->textModel,
                    'messages' => [
                        ['role' => 'system', 'content' => 'Kamu adalah ahli gizi dan personal nutrition coach Indonesia. Selalu respond dalam format JSON yang valid.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.3,
                    'max_tokens' => 1000,
                ]);

            $responseTime = (int) ((microtime(true) - $startTime) * 1000);

            if (!$response->successful()) {
                $this->logRequest($profileId, $tipe, $this->textModel, $prompt, null, $responseTime, false, "HTTP {$response->status()}");
                return ['success' => false, 'error' => "API error: {$response->status()}"];
            }

            $body = $response->json();
            $content = $body['choices'][0]['message']['content'] ?? '';
            $tokens = $body['usage']['total_tokens'] ?? null;

            // Parse JSON from response
            $parsed = $this->parseJsonResponse($content);

            $this->logRequest($profileId, $tipe, $this->textModel, $prompt, $content, $responseTime, true, null, $tokens);

            if ($parsed === null) {
                return ['success' => false, 'error' => 'Gagal parse response AI', 'raw' => $content];
            }

            return ['success' => true, 'source' => 'ai', 'data' => $parsed];

        } catch (\Exception $e) {
            $responseTime = (int) ((microtime(true) - $startTime) * 1000);
            $this->logRequest($profileId, $tipe, $this->textModel, $prompt, null, $responseTime, false, $e->getMessage());
            Log::error('AI Nutrition Service Error', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Call vision API
     */
    private function callVisionApi(string $prompt, string $imageUrl, ?int $profileId = null): array
    {
        $startTime = microtime(true);

        try {
            $response = Http::timeout(60)
                ->withHeaders([
                    'Authorization' => "Bearer {$this->apiKey}",
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->apiUrl}/chat/completions", [
                    'model' => $this->visionModel,
                    'messages' => [
                        ['role' => 'system', 'content' => 'Kamu adalah ahli gizi Indonesia. Analisis foto makanan dan berikan estimasi nutrisi dalam format JSON.'],
                        [
                            'role' => 'user',
                            'content' => [
                                ['type' => 'text', 'text' => $prompt],
                                ['type' => 'image_url', 'image_url' => ['url' => $imageUrl]],
                            ],
                        ],
                    ],
                    'temperature' => 0.3,
                    'max_tokens' => 1500,
                ]);

            $responseTime = (int) ((microtime(true) - $startTime) * 1000);

            if (!$response->successful()) {
                $this->logRequest($profileId, 'vision', $this->visionModel, $prompt, null, $responseTime, false, "HTTP {$response->status()}");
                return ['success' => false, 'error' => "API error: {$response->status()}"];
            }

            $body = $response->json();
            $content = $body['choices'][0]['message']['content'] ?? '';
            $tokens = $body['usage']['total_tokens'] ?? null;

            $parsed = $this->parseJsonResponse($content);

            $this->logRequest($profileId, 'vision', $this->visionModel, $prompt, $content, $responseTime, true, null, $tokens);

            if ($parsed === null) {
                return ['success' => false, 'error' => 'Gagal parse response AI', 'raw' => $content];
            }

            return ['success' => true, 'source' => 'ai_vision', 'data' => $parsed];

        } catch (\Exception $e) {
            $responseTime = (int) ((microtime(true) - $startTime) * 1000);
            $this->logRequest($profileId, 'vision', $this->visionModel, $prompt, null, $responseTime, false, $e->getMessage());
            Log::error('AI Vision Service Error', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Parse JSON from AI response (handles markdown code blocks)
     */
    private function parseJsonResponse(string $content): ?array
    {
        // Remove markdown code blocks if present
        $content = trim($content);
        if (str_starts_with($content, '```')) {
            $content = preg_replace('/^```(?:json)?\s*\n?/', '', $content);
            $content = preg_replace('/\n?```\s*$/', '', $content);
        }

        $decoded = json_decode(trim($content), true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        // Try to extract JSON from text
        if (preg_match('/\{[\s\S]*\}/', $content, $matches)) {
            $decoded = json_decode($matches[0], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        return null;
    }

    /**
     * Log AI request
     */
    private function logRequest(?int $profileId, string $tipe, string $model, ?string $prompt, ?string $response, int $responseTime, bool $success, ?string $error = null, ?int $tokens = null): void
    {
        try {
            AiLog::create([
                'profile_id' => $profileId,
                'tipe' => $tipe,
                'model_used' => $model,
                'prompt' => $prompt ? substr($prompt, 0, 2000) : null,
                'response' => $response ? substr($response, 0, 5000) : null,
                'tokens_used' => $tokens,
                'response_time_ms' => $responseTime,
                'success' => $success,
                'error_message' => $error,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log AI request', ['error' => $e->getMessage()]);
        }
    }
}
