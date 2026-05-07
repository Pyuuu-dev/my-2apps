<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiNutritionService
{
    private string $apiUrl;
    private string $apiKey;
    private string $model;
    private string $visionModel;

    public function __construct()
    {
        $this->apiUrl = config('services.enowx.api_url', 'http://localhost:1430/v1');
        $this->apiKey = config('services.enowx.api_key', '');
        $this->model = config('services.enowx.model', 'deepseek-3.2');
        $this->visionModel = config('services.enowx.vision_model', 'claude-haiku-4.5');
    }

    /**
     * Estimasi kalori dari nama makanan
     */
    public function estimateCalories(string $foodName): ?array
    {
        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiUrl . '/chat/completions', [
                    'model' => $this->model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Kamu adalah ahli nutrisi Indonesia. Jawab HANYA dalam format JSON tanpa markdown: {"nama": "nama makanan", "kalori": angka, "protein": angka_gram, "karbohidrat": angka_gram, "lemak": angka_gram, "porsi": "deskripsi 1 porsi standar"}. Estimasi per 1 porsi standar Indonesia. Jika tidak yakin, berikan estimasi terbaik.'
                        ],
                        ['role' => 'user', 'content' => $foodName]
                    ],
                    'max_tokens' => 200,
                    'temperature' => 0.3,
                ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content', '');
                // Clean markdown code blocks if present
                $content = preg_replace('/```json\s*/', '', $content);
                $content = preg_replace('/```\s*/', '', $content);
                $content = trim($content);

                $data = json_decode($content, true);
                if ($data && isset($data['kalori'])) {
                    return $data;
                }
            }
        } catch (\Exception $e) {
            Log::warning('AI Nutrition API error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Estimasi kalori dari foto makanan (vision)
     */
    public function estimateFromImage(string $imageUrl): ?array
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiUrl . '/chat/completions', [
                    'model' => $this->visionModel,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Kamu adalah ahli nutrisi Indonesia. Analisis foto makanan dan jawab HANYA dalam format JSON tanpa markdown: {"nama": "nama makanan yang terlihat", "kalori": angka_total, "protein": angka_gram, "karbohidrat": angka_gram, "lemak": angka_gram, "porsi": "deskripsi porsi yang terlihat", "detail": "deskripsi singkat apa saja yang ada di foto"}. Jika ada beberapa item, jumlahkan total kalorinya. Estimasi sebaik mungkin.'
                        ],
                        [
                            'role' => 'user',
                            'content' => [
                                ['type' => 'text', 'text' => 'Analisis makanan di foto ini dan estimasi kalorinya:'],
                                ['type' => 'image_url', 'image_url' => ['url' => $imageUrl]],
                            ]
                        ]
                    ],
                    'max_tokens' => 300,
                    'temperature' => 0.3,
                ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content', '');
                $content = preg_replace('/```json\s*/', '', $content);
                $content = preg_replace('/```\s*/', '', $content);
                $content = trim($content);

                $data = json_decode($content, true);
                if ($data && isset($data['kalori'])) {
                    return $data;
                }
            }
        } catch (\Exception $e) {
            Log::warning('AI Vision API error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Saran makanan berdasarkan sisa kalori
     */
    public function suggestMeals(int $sisaKalori, string $waktuMakan): ?string
    {
        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiUrl . '/chat/completions', [
                    'model' => $this->model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Kamu adalah ahli nutrisi Indonesia. Berikan 3 saran makanan singkat (maks 2 kalimat per saran) yang cocok untuk situasi ini. Jawab dalam bahasa Indonesia, format sederhana tanpa markdown.'
                        ],
                        ['role' => 'user', 'content' => "Sisa kalori hari ini: {$sisaKalori} kkal. Waktu: {$waktuMakan}. Sarankan 3 makanan yang pas."]
                    ],
                    'max_tokens' => 300,
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content', '');
            }
        } catch (\Exception $e) {
            Log::warning('AI Suggestion API error: ' . $e->getMessage());
        }

        return null;
    }
}
