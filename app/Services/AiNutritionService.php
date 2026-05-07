<?php

namespace App\Services;

use App\Models\DietTracker\AiLog;
use App\Models\DietTracker\FoodDatabase;
use App\Models\DietTracker\FoodLog;
use App\Models\DietTracker\UserProfile;
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
     * Estimasi nutrisi dari nama makanan (text) - support multi-food dengan "+"
     */
    public function estimateFromText(string $foodDescription, ?int $profileId = null): array
    {
        // Detect multi-food input: "nasi goreng + ayam geprek + es teh"
        $foods = $this->parseMultiFood($foodDescription);

        if (count($foods) > 1) {
            return $this->estimateMultiFood($foods, $profileId);
        }

        // Single food - cek database lokal dulu
        $dbResult = FoodDatabase::search($foodDescription, 1)->first();
        if ($dbResult && $this->isFuzzyMatch($foodDescription, $dbResult->nama)) {
            // Tambah 10% margin kalori dari database (conservative untuk diet)
            $kaloriMargin = (int) ceil($dbResult->kalori * 1.10);
            return [
                'success' => true,
                'source' => 'database',
                'data' => [
                    'nama' => $dbResult->nama,
                    'kalori' => $kaloriMargin,
                    'protein' => $dbResult->protein,
                    'karbohidrat' => $dbResult->karbohidrat,
                    'lemak' => $dbResult->lemak,
                    'porsi' => 1,
                    'satuan_porsi' => $dbResult->satuan_porsi,
                ],
            ];
        }

        $prompt = <<<PROMPT
Kamu adalah ahli gizi Indonesia yang KETAT untuk program diet.

Makanan: {$foodDescription}

ATURAN PENTING:
- SELALU estimasi kalori di BATAS ATAS (upper bound) - lebih baik overestimate daripada underestimate
- Asumsi porsi BESAR jika tidak disebutkan spesifik (porsi warung/restoran, bukan porsi diet)
- Hitung termasuk minyak goreng, santan, gula, saus yang biasa dipakai
- Jika gorengan: tambahkan kalori minyak serap (~50-80 kkal extra)
- Jika minuman manis: asumsi gula penuh kecuali disebutkan "less sugar"
- Kalori dalam kcal, protein/karbohidrat/lemak dalam gram

Berikan response dalam format JSON SAJA:
{
    "nama": "nama makanan",
    "kalori": angka_kalori_UPPER_BOUND,
    "protein": angka_gram,
    "karbohidrat": angka_gram,
    "lemak": angka_gram,
    "porsi": jumlah_porsi,
    "satuan_porsi": "deskripsi porsi (asumsi besar)",
    "kategori": "nasi/lauk/sayur/mie/snack/minuman/buah/roti/fast_food/suplemen",
    "berat_gram": estimasi_berat_gram,
    "peringatan": "peringatan diet jika makanan ini tinggi kalori/gula/lemak (null jika aman)"
}
PROMPT;

        $result = $this->callTextApi($prompt, $profileId);

        // Auto-learn: simpan ke database jika berhasil
        if ($result['success'] && $result['source'] === 'ai') {
            $this->autoLearnFood($result['data']);
        }

        return $result;
    }

    /**
     * Estimasi multi-food sekaligus: "nasi goreng + ayam geprek + es teh"
     */
    private function estimateMultiFood(array $foods, ?int $profileId = null): array
    {
        $results = [];
        $totalKalori = 0;
        $totalProtein = 0;
        $totalKarbo = 0;
        $totalLemak = 0;

        foreach ($foods as $food) {
            $food = trim($food);
            if (empty($food)) continue;

            // Cek database dulu
            $dbResult = FoodDatabase::search($food, 1)->first();
            if ($dbResult && $this->isFuzzyMatch($food, $dbResult->nama)) {
                $item = [
                    'nama' => $dbResult->nama,
                    'kalori' => (int) ceil($dbResult->kalori * 1.10), // +10% margin
                    'protein' => $dbResult->protein,
                    'karbohidrat' => $dbResult->karbohidrat,
                    'lemak' => $dbResult->lemak,
                    'porsi' => 1,
                    'satuan_porsi' => $dbResult->satuan_porsi,
                    'source' => 'database',
                ];
            } else {
                $item = ['nama' => $food, 'source' => 'pending'];
            }
            $results[] = $item;
        }

        // Batch AI call untuk yang belum ada di database
        $pendingFoods = array_filter($results, fn($r) => $r['source'] === 'pending');
        if (!empty($pendingFoods)) {
            $pendingNames = implode(', ', array_column($pendingFoods, 'nama'));
            $count = count($pendingFoods);

            $prompt = <<<PROMPT
Kamu adalah ahli gizi Indonesia yang KETAT untuk program diet.
Estimasi nutrisi UPPER BOUND untuk {$count} makanan:

{$pendingNames}

ATURAN: Selalu estimasi kalori di BATAS ATAS. Asumsi porsi besar (warung/restoran). Hitung minyak, santan, gula.

Berikan response JSON SAJA:
{
    "items": [
        {
            "nama": "nama makanan",
            "kalori": angka_UPPER_BOUND,
            "protein": angka_gram,
            "karbohidrat": angka_gram,
            "lemak": angka_gram,
            "porsi": 1,
            "satuan_porsi": "porsi besar",
            "kategori": "kategori",
            "berat_gram": estimasi_gram
        }
    ]
}
PROMPT;

            $aiResult = $this->callTextApi($prompt, $profileId);
            if ($aiResult['success'] && isset($aiResult['data']['items'])) {
                $aiItems = $aiResult['data']['items'];
                $aiIndex = 0;
                foreach ($results as &$r) {
                    if ($r['source'] === 'pending' && isset($aiItems[$aiIndex])) {
                        $ai = $aiItems[$aiIndex];
                        $r = array_merge($r, [
                            'nama' => $ai['nama'] ?? $r['nama'],
                            'kalori' => (int) ($ai['kalori'] ?? 0),
                            'protein' => (float) ($ai['protein'] ?? 0),
                            'karbohidrat' => (float) ($ai['karbohidrat'] ?? 0),
                            'lemak' => (float) ($ai['lemak'] ?? 0),
                            'porsi' => (float) ($ai['porsi'] ?? 1),
                            'satuan_porsi' => $ai['satuan_porsi'] ?? 'porsi',
                            'source' => 'ai',
                        ]);
                        // Auto-learn
                        $this->autoLearnFood($ai);
                        $aiIndex++;
                    }
                }
                unset($r);
            }
        }

        // Calculate totals
        foreach ($results as $r) {
            $totalKalori += $r['kalori'] ?? 0;
            $totalProtein += $r['protein'] ?? 0;
            $totalKarbo += $r['karbohidrat'] ?? 0;
            $totalLemak += $r['lemak'] ?? 0;
        }

        return [
            'success' => true,
            'source' => 'multi',
            'data' => [
                'items' => $results,
                'nama' => implode(' + ', array_column($results, 'nama')),
                'kalori' => $totalKalori,
                'protein' => round($totalProtein, 1),
                'karbohidrat' => round($totalKarbo, 1),
                'lemak' => round($totalLemak, 1),
                'porsi' => 1,
                'satuan_porsi' => count($results) . ' item',
            ],
        ];
    }

    /**
     * Auto-learn: simpan makanan baru ke database
     */
    private function autoLearnFood(array $data): void
    {
        $nama = $data['nama'] ?? null;
        if (!$nama || strlen($nama) < 3) return;

        // Cek apakah sudah ada di database
        $exists = FoodDatabase::where('nama', $nama)->exists();
        if ($exists) return;

        try {
            FoodDatabase::create([
                'nama' => $nama,
                'kategori' => $data['kategori'] ?? null,
                'kalori' => (int) ($data['kalori'] ?? 0),
                'protein' => (float) ($data['protein'] ?? 0),
                'karbohidrat' => (float) ($data['karbohidrat'] ?? 0),
                'lemak' => (float) ($data['lemak'] ?? 0),
                'satuan_porsi' => $data['satuan_porsi'] ?? '1 porsi',
                'berat_gram' => isset($data['berat_gram']) ? (int) $data['berat_gram'] : null,
            ]);
            Log::info("Auto-learned food: {$nama}");
        } catch (\Exception $e) {
            Log::error("Failed to auto-learn food: {$nama}", ['error' => $e->getMessage()]);
        }
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
        {"nama": "nama makanan Indonesia", "kalori": angka, "protein": angka_gram, "alasan": "kenapa cocok"}
    ],
    "tips": "saran singkat actionable",
    "warning": "peringatan jika ada (null jika tidak ada)"
}

RULES:
- 3-5 makanan Indonesia yang mudah didapat
- Sesuaikan goal: cutting=tinggi protein rendah karbo, bulking=tinggi kalori, diet=defisit kalori
- Tips harus spesifik dan actionable
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

Data: {$dataJson}

Berikan response JSON SAJA:
{
    "analisis": "ringkasan 2-3 kalimat",
    "kelebihan": ["positif 1", "positif 2"],
    "kekurangan": ["perbaiki 1", "perbaiki 2"],
    "saran": ["saran spesifik 1", "saran 2", "saran 3"],
    "skor_kesehatan": angka_1_10,
    "emoji_mood": "emoji"
}
PROMPT;

        return $this->callTextApi($prompt, $profileId, 'recommendation');
    }

    /**
     * AI-powered smart reminder suggestions berdasarkan pola user
     */
    public function suggestReminders(array $userPattern, ?int $profileId = null): array
    {
        $patternJson = json_encode($userPattern, JSON_PRETTY_PRINT);

        $prompt = <<<PROMPT
Kamu adalah personal nutrition coach. Berdasarkan pola aktivitas user, sarankan jadwal reminder yang optimal.

Pola user: {$patternJson}

Berikan response JSON SAJA:
{
    "reminders": [
        {"tipe": "minum/makan/olahraga/tidur", "waktu": "HH:MM", "judul": "judul singkat", "pesan": "pesan motivasi", "alasan": "kenapa waktu ini"}
    ],
    "tips_umum": "saran umum tentang jadwal"
}

RULES:
- Sarankan 5-8 reminder yang realistis
- Sesuaikan dengan pola tidur dan makan user
- Reminder minum air setiap 2-3 jam saat bangun
- Reminder makan 3x sehari + snack jika perlu
- 1 reminder olahraga dan 1 reminder tidur
PROMPT;

        return $this->callTextApi($prompt, $profileId, 'recommendation');
    }

    /**
     * Generate daily motivation berdasarkan progress
     */
    public function generateMotivation(array $progressData, ?int $profileId = null): array
    {
        $dataJson = json_encode($progressData, JSON_PRETTY_PRINT);

        $prompt = <<<PROMPT
Kamu adalah personal coach yang supportive. Berikan motivasi harian berdasarkan progress diet user.

Data: {$dataJson}

Berikan response JSON SAJA:
{
    "motivasi": "pesan motivasi 1-2 kalimat dalam Bahasa Indonesia yang personal dan encouraging",
    "emoji": "emoji yang sesuai mood",
    "tip_hari_ini": "1 tip spesifik actionable untuk hari ini"
}

RULES:
- Bahasa casual dan friendly, seperti teman
- Jika progress bagus: puji dan dorong konsistensi
- Jika kurang: supportive tanpa judgmental, kasih solusi
- Jangan generic, harus berdasarkan data
PROMPT;

        return $this->callTextApi($prompt, $profileId, 'recommendation');
    }

    /**
     * Estimasi timeline mencapai goal berat badan
     */
    public function estimateGoalTimeline(array $userData, ?int $profileId = null): array
    {
        // Inject tanggal hari ini agar AI tidak salah tahun
        $userData['tanggal_hari_ini'] = now('Asia/Singapore')->format('Y-m-d');
        $userData['tahun_sekarang'] = (int) now('Asia/Singapore')->format('Y');
        $dataJson = json_encode($userData, JSON_PRETTY_PRINT);

        $todayStr = now('Asia/Singapore')->format('Y-m-d');

        $prompt = <<<PROMPT
Kamu adalah ahli gizi. Hitung estimasi timeline mencapai target berat badan.

TANGGAL HARI INI: {$todayStr}

Data: {$dataJson}

Berikan response JSON SAJA:
{
    "estimasi_minggu": angka_minggu,
    "estimasi_tanggal": "YYYY-MM-DD (HARUS setelah {$todayStr})",
    "defisit_harian": angka_kkal_defisit_per_hari,
    "kg_per_minggu": angka_kg_turun_per_minggu,
    "realistis": true/false,
    "saran": "saran jika tidak realistis atau tips percepat",
    "peringatan": "peringatan kesehatan jika ada (null jika aman)"
}

RULES:
- PENTING: Tanggal estimasi HARUS di masa depan dari {$todayStr}
- 1 kg lemak = 7700 kkal defisit
- Max aman turun 0.5-1 kg/minggu
- Jika target naik (bulking), surplus 300-500 kkal/hari
- Pertimbangkan umur, gender, level aktivitas
PROMPT;

        $result = $this->callTextApi($prompt, $profileId, 'recommendation');

        // Safety: fix tanggal jika AI masih return tahun lama
        if ($result['success'] && isset($result['data']['estimasi_tanggal'])) {
            $estimasi = $result['data']['estimasi_tanggal'];
            try {
                $estDate = \Carbon\Carbon::parse($estimasi);
                $today = now('Asia/Singapore');
                if ($estDate->lt($today)) {
                    // Hitung ulang: tambahkan minggu ke hari ini
                    $weeks = $result['data']['estimasi_minggu'] ?? 12;
                    $result['data']['estimasi_tanggal'] = $today->copy()->addWeeks((int) $weeks)->format('Y-m-d');
                }
            } catch (\Exception $e) {
                // Fallback
                $weeks = $result['data']['estimasi_minggu'] ?? 12;
                $result['data']['estimasi_tanggal'] = now('Asia/Singapore')->addWeeks((int) $weeks)->format('Y-m-d');
            }
        }

        return $result;
    }

    /**
     * Parse multi-food input: "nasi goreng + ayam geprek + es teh"
     */
    private function parseMultiFood(string $input): array
    {
        // Split by +, &, dan, comma
        $foods = preg_split('/\s*[\+\&]\s*|\s*,\s*|\s+dan\s+/i', $input);
        return array_filter(array_map('trim', $foods), fn($f) => !empty($f));
    }

    /**
     * Fuzzy match check - apakah input cukup mirip dengan nama di database
     */
    private function isFuzzyMatch(string $input, string $dbName): bool
    {
        $input = strtolower(trim($input));
        $dbName = strtolower(trim($dbName));

        // Exact or contains
        if ($input === $dbName || str_contains($dbName, $input) || str_contains($input, $dbName)) {
            return true;
        }

        // Similar text > 70%
        similar_text($input, $dbName, $percent);
        return $percent > 70;
    }

    /**
     * Estimasi nutrisi dari foto (vision) - kept for future use
     */
    public function estimateFromImage(string $imageUrl, ?int $profileId = null): array
    {
        $prompt = <<<PROMPT
Kamu adalah ahli gizi Indonesia. Analisis foto makanan dan identifikasi semua makanan.

Berikan response JSON SAJA:
{
    "items": [{"nama":"nama","kalori":angka,"protein":angka,"karbohidrat":angka,"lemak":angka,"porsi":1,"satuan_porsi":"porsi"}],
    "total_kalori": total,
    "deskripsi": "deskripsi singkat"
}
PROMPT;

        return $this->callVisionApi($prompt, $imageUrl, $profileId);
    }

    // ==========================================
    // API CALLERS
    // ==========================================

    private function callTextApi(string $prompt, ?int $profileId = null, string $tipe = 'text'): array
    {
        $startTime = microtime(true);

        try {
            $response = Http::timeout(45)
                ->withHeaders([
                    'Authorization' => "Bearer {$this->apiKey}",
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->apiUrl}/chat/completions", [
                    'model' => $this->textModel,
                    'messages' => [
                        ['role' => 'system', 'content' => 'Kamu adalah ahli gizi dan personal nutrition coach Indonesia. SELALU respond dalam format JSON yang valid tanpa markdown. Jangan tambahkan teks apapun di luar JSON.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.3,
                    'max_tokens' => 1500,
                ]);

            $responseTime = (int) ((microtime(true) - $startTime) * 1000);

            if (!$response->successful()) {
                $this->logRequest($profileId, $tipe, $this->textModel, $prompt, null, $responseTime, false, "HTTP {$response->status()}");
                return ['success' => false, 'error' => "API error: {$response->status()}"];
            }

            $body = $response->json();
            $content = $body['choices'][0]['message']['content'] ?? '';
            $tokens = $body['usage']['total_tokens'] ?? null;

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

    private function callVisionApi(string $prompt, string $imageUrl, ?int $profileId = null): array
    {
        $startTime = microtime(true);
        try {
            $base64 = $this->imageToBase64($imageUrl);
            $imgPayload = $base64 ? ['url' => $base64] : ['url' => $imageUrl];

            $response = Http::timeout(90)
                ->withHeaders(['Authorization' => "Bearer {$this->apiKey}", 'Content-Type' => 'application/json'])
                ->post("{$this->apiUrl}/chat/completions", [
                    'model' => $this->visionModel,
                    'messages' => [
                        ['role' => 'system', 'content' => 'Ahli gizi Indonesia. Respond JSON only.'],
                        ['role' => 'user', 'content' => [['type' => 'text', 'text' => $prompt], ['type' => 'image_url', 'image_url' => $imgPayload]]],
                    ],
                    'temperature' => 0.3, 'max_tokens' => 1500,
                ]);

            $responseTime = (int) ((microtime(true) - $startTime) * 1000);
            if (!$response->successful()) {
                $this->logRequest($profileId, 'vision', $this->visionModel, $prompt, null, $responseTime, false, "HTTP {$response->status()}");
                return ['success' => false, 'error' => "Vision error: HTTP {$response->status()}"];
            }

            $content = $response->json()['choices'][0]['message']['content'] ?? '';
            $tokens = $response->json()['usage']['total_tokens'] ?? null;
            $parsed = $this->parseJsonResponse($content);
            $this->logRequest($profileId, 'vision', $this->visionModel, $prompt, $content, $responseTime, $parsed !== null, $parsed === null ? 'Non-JSON vision response' : null, $tokens);

            if ($parsed === null) return ['success' => false, 'error' => 'Vision model tidak support. Ketik manual nama makanannya.'];
            return ['success' => true, 'source' => 'ai_vision', 'data' => $parsed];
        } catch (\Exception $e) {
            $responseTime = (int) ((microtime(true) - $startTime) * 1000);
            $this->logRequest($profileId, 'vision', $this->visionModel, $prompt, null, $responseTime, false, $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function imageToBase64(string $imageUrl): ?string
    {
        try {
            $response = Http::timeout(15)->get($imageUrl);
            if (!$response->successful()) return null;
            $mime = $response->header('Content-Type') ?? 'image/jpeg';
            if (!str_starts_with($mime, 'image/')) $mime = 'image/jpeg';
            return "data:{$mime};base64," . base64_encode($response->body());
        } catch (\Exception $e) { return null; }
    }

    private function parseJsonResponse(string $content): ?array
    {
        $content = trim($content);
        if (str_starts_with($content, '```')) {
            $content = preg_replace('/^```(?:json)?\s*\n?/', '', $content);
            $content = preg_replace('/\n?```\s*$/', '', $content);
        }

        $decoded = json_decode(trim($content), true);
        if (json_last_error() === JSON_ERROR_NONE) return $decoded;

        if (preg_match('/\{[\s\S]*\}/', $content, $matches)) {
            $decoded = json_decode($matches[0], true);
            if (json_last_error() === JSON_ERROR_NONE) return $decoded;
        }

        return null;
    }

    private function logRequest(?int $profileId, string $tipe, string $model, ?string $prompt, ?string $response, int $responseTime, bool $success, ?string $error = null, ?int $tokens = null): void
    {
        try {
            AiLog::create([
                'profile_id' => $profileId, 'tipe' => $tipe, 'model_used' => $model,
                'prompt' => $prompt ? substr($prompt, 0, 2000) : null,
                'response' => $response ? substr($response, 0, 5000) : null,
                'tokens_used' => $tokens, 'response_time_ms' => $responseTime,
                'success' => $success, 'error_message' => $error,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log AI request', ['error' => $e->getMessage()]);
        }
    }
}
