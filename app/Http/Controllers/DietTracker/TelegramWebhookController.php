<?php

namespace App\Http\Controllers\DietTracker;

use App\Http\Controllers\Controller;
use App\Models\DietTracker\DietPlan;
use App\Models\DietTracker\DailyActivity;
use App\Models\DietTracker\Exercise;
use App\Models\DietTracker\Meal;
use App\Models\DietTracker\WaterLog;
use App\Models\DietTracker\FoodDatabase;
use App\Services\TelegramService;
use App\Services\AiNutritionService;
use Illuminate\Http\Request;

class TelegramWebhookController extends Controller
{
    /**
     * Handle incoming Telegram webhook
     * Commands:
     *   /minum [ml]         - catat minum air (default 250ml)
     *   /makan [nama]       - catat makan dari database
     *   /kalori [nama] [kkal] - catat makan manual dengan kalori
     *   /status             - lihat progress hari ini
     *   /help               - daftar command
     */
    public function handle(Request $request)
    {
        $data = $request->all();
        $message = $data['message'] ?? null;

        if (!$message) {
            return response('ok');
        }

        $chatId = (string) $message['chat']['id'];
        $configChatId = config('services.telegram.chat_id');

        // Only respond to configured chat
        if ($chatId !== $configChatId) {
            return response('ok');
        }

        $telegram = new TelegramService();

        // Handle photo
        if (isset($message['photo'])) {
            return $this->handlePhoto($message, $telegram);
        }

        if (!isset($message['text'])) {
            return response('ok');
        }

        $text = trim($message['text']);

        // Parse command
        if (str_starts_with($text, '/minum')) {
            return $this->handleMinum($text, $telegram);
        } elseif (str_starts_with($text, '/makan')) {
            return $this->handleMakan($text, $telegram);
        } elseif (str_starts_with($text, '/kalori')) {
            return $this->handleKaloriManual($text, $telegram);
        } elseif (str_starts_with($text, '/berat')) {
            return $this->handleBerat($text, $telegram);
        } elseif (str_starts_with($text, '/olahraga')) {
            return $this->handleOlahraga($text, $telegram);
        } elseif (str_starts_with($text, '/hapus')) {
            return $this->handleHapus($telegram);
        } elseif (str_starts_with($text, '/reset_air')) {
            return $this->handleResetAir($telegram);
        } elseif (str_starts_with($text, '/riwayat')) {
            return $this->handleRiwayat($telegram);
        } elseif (str_starts_with($text, '/target')) {
            return $this->handleTarget($telegram);
        } elseif (str_starts_with($text, '/saran')) {
            return $this->handleSaran($telegram);
        } elseif (str_starts_with($text, '/status')) {
            return $this->handleStatus($telegram);
        } elseif (str_starts_with($text, '/help') || $text === '/start') {
            return $this->handleHelp($telegram);
        }

        // If no command prefix, try AI estimation directly
        if (!str_starts_with($text, '/')) {
            return $this->handleMakan('/makan ' . $text, $telegram);
        }

        return response('ok');
    }

    private function handlePhoto(array $message, TelegramService $telegram)
    {
        $plan = DietPlan::getActivePlan();
        if (!$plan) {
            $telegram->sendMessage("Belum ada program diet aktif.");
            return response('ok');
        }

        // Get largest photo (last in array)
        $photos = $message['photo'];
        $photo = end($photos);
        $fileId = $photo['file_id'];

        // Get file URL from Telegram
        $botToken = config('services.telegram.bot_token');
        $fileInfo = \Illuminate\Support\Facades\Http::get("https://api.telegram.org/bot{$botToken}/getFile", ['file_id' => $fileId])->json();

        if (!($fileInfo['ok'] ?? false)) {
            $telegram->sendMessage("Gagal memproses foto. Coba lagi.");
            return response('ok');
        }

        $filePath = $fileInfo['result']['file_path'];
        $fileUrl = "https://api.telegram.org/file/bot{$botToken}/{$filePath}";

        $telegram->sendMessage("📸 Menganalisis foto makanan...");

        // Send to AI Vision
        $ai = new AiNutritionService();
        $estimation = $ai->estimateFromImage($fileUrl);

        if ($estimation) {
            Meal::create([
                'diet_plan_id' => $plan->id,
                'tanggal' => now()->toDateString(),
                'waktu_makan' => $this->guessWaktuMakan(),
                'nama_makanan' => ucfirst($estimation['nama'] ?? 'Makanan dari foto'),
                'kalori' => (int) ($estimation['kalori'] ?? 0),
                'protein' => (float) ($estimation['protein'] ?? 0),
                'karbohidrat' => (float) ($estimation['karbohidrat'] ?? 0),
                'lemak' => (float) ($estimation['lemak'] ?? 0),
                'porsi' => 1,
            ]);

            $totalKalori = Meal::where('diet_plan_id', $plan->id)->whereDate('tanggal', now()->toDateString())->sum('kalori');
            $nama = ucfirst($estimation['nama'] ?? 'Makanan');
            $detail = $estimation['detail'] ?? '';

            $msg = "📸 <b>Foto Dianalisis!</b>\n\n";
            $msg .= "🍽 <b>{$nama}</b>\n";
            if ($detail) $msg .= "📝 {$detail}\n";
            $msg .= "🔥 {$estimation['kalori']} kkal\n";
            $msg .= "📊 P:{$estimation['protein']}g K:{$estimation['karbohidrat']}g L:{$estimation['lemak']}g\n";
            $msg .= "📏 Porsi: {$estimation['porsi']}\n\n";
            $msg .= "Total hari ini: {$totalKalori} / {$plan->kalori_harian_target} kkal";

            $telegram->sendMessage($msg);
        } else {
            $telegram->sendMessage("Tidak bisa menganalisis foto ini. Coba:\n- Foto lebih jelas/terang\n- Atau ketik manual: /makan [nama makanan]");
        }

        return response('ok');
    }

    private function handleMinum(string $text, TelegramService $telegram)
    {
        $plan = DietPlan::getActivePlan();
        if (!$plan) {
            $telegram->sendMessage("Belum ada program diet aktif.");
            return response('ok');
        }

        // Parse: /minum 500 or /minum (default 250)
        $parts = explode(' ', $text, 2);
        $ml = isset($parts[1]) && is_numeric(trim($parts[1])) ? (int) trim($parts[1]) : 250;
        $ml = max(50, min(2000, $ml));

        WaterLog::create([
            'diet_plan_id' => $plan->id,
            'tanggal' => now()->toDateString(),
            'jumlah_ml' => $ml,
        ]);

        $totalHariIni = WaterLog::where('diet_plan_id', $plan->id)
            ->whereDate('tanggal', now()->toDateString())
            ->sum('jumlah_ml');

        $telegram->sendMessage("💧 +{$ml}ml tercatat!\n\nTotal hari ini: {$totalHariIni}ml");
        return response('ok');
    }

    private function handleMakan(string $text, TelegramService $telegram)
    {
        $plan = DietPlan::getActivePlan();
        if (!$plan) {
            $telegram->sendMessage("Belum ada program diet aktif.");
            return response('ok');
        }

        // Parse: /makan nasi goreng
        $parts = explode(' ', $text, 2);
        $query = trim($parts[1] ?? '');

        if (empty($query)) {
            $telegram->sendMessage("Format: /makan [nama makanan]\nContoh: /makan nasi goreng\n\nAI akan otomatis estimasi kalori jika tidak ada di database.");
            return response('ok');
        }

        // Search food database first
        $food = FoodDatabase::where('nama', 'like', "%{$query}%")->first();

        if ($food) {
            // Found in database
            Meal::create([
                'diet_plan_id' => $plan->id,
                'tanggal' => now()->toDateString(),
                'waktu_makan' => $this->guessWaktuMakan(),
                'nama_makanan' => $food->nama,
                'kalori' => $food->kalori,
                'protein' => $food->protein,
                'karbohidrat' => $food->karbohidrat,
                'lemak' => $food->lemak,
                'porsi' => 1,
            ]);

            $totalKalori = Meal::where('diet_plan_id', $plan->id)->whereDate('tanggal', now()->toDateString())->sum('kalori');
            $telegram->sendMessage("🍽 {$food->nama} ({$food->kalori} kkal) tercatat!\n📊 P:{$food->protein}g K:{$food->karbohidrat}g L:{$food->lemak}g\n\nTotal hari ini: {$totalKalori} / {$plan->kalori_harian_target} kkal");
        } else {
            // Not in database - use AI estimation
            $ai = new AiNutritionService();
            $estimation = $ai->estimateCalories($query);

            if ($estimation) {
                Meal::create([
                    'diet_plan_id' => $plan->id,
                    'tanggal' => now()->toDateString(),
                    'waktu_makan' => $this->guessWaktuMakan(),
                    'nama_makanan' => ucfirst($estimation['nama'] ?? $query),
                    'kalori' => (int) ($estimation['kalori'] ?? 0),
                    'protein' => (float) ($estimation['protein'] ?? 0),
                    'karbohidrat' => (float) ($estimation['karbohidrat'] ?? 0),
                    'lemak' => (float) ($estimation['lemak'] ?? 0),
                    'porsi' => 1,
                ]);

                $totalKalori = Meal::where('diet_plan_id', $plan->id)->whereDate('tanggal', now()->toDateString())->sum('kalori');
                $nama = ucfirst($estimation['nama'] ?? $query);
                $telegram->sendMessage("🤖 AI Estimasi: {$nama}\n🔥 {$estimation['kalori']} kkal | P:{$estimation['protein']}g K:{$estimation['karbohidrat']}g L:{$estimation['lemak']}g\n📏 Porsi: {$estimation['porsi']}\n\nTotal hari ini: {$totalKalori} / {$plan->kalori_harian_target} kkal");
            } else {
                $telegram->sendMessage("Tidak bisa estimasi '{$query}'.\n\nCoba:\n/makan [nama lebih spesifik]\n/kalori {$query} [kkal]");
            }
        }

        return response('ok');
    }

    private function handleKaloriManual(string $text, TelegramService $telegram)
    {
        $plan = DietPlan::getActivePlan();
        if (!$plan) {
            $telegram->sendMessage("Belum ada program diet aktif.");
            return response('ok');
        }

        // Parse: /kalori nasi goreng 350
        $text = trim(str_replace('/kalori', '', $text));
        // Last word should be number (kalori)
        $parts = explode(' ', $text);
        $kalori = 0;
        $nama = $text;

        if (count($parts) >= 2 && is_numeric(end($parts))) {
            $kalori = (int) array_pop($parts);
            $nama = implode(' ', $parts);
        }

        if (empty($nama) || $kalori <= 0) {
            $telegram->sendMessage("Format: /kalori [nama makanan] [jumlah kkal]\nContoh: /kalori nasi goreng 350");
            return response('ok');
        }

        Meal::create([
            'diet_plan_id' => $plan->id,
            'tanggal' => now()->toDateString(),
            'waktu_makan' => $this->guessWaktuMakan(),
            'nama_makanan' => ucfirst($nama),
            'kalori' => $kalori,
            'protein' => 0,
            'karbohidrat' => 0,
            'lemak' => 0,
            'porsi' => 1,
        ]);

        $totalKalori = Meal::where('diet_plan_id', $plan->id)
            ->whereDate('tanggal', now()->toDateString())
            ->sum('kalori');

        $telegram->sendMessage("🍽 " . ucfirst($nama) . " ({$kalori} kkal) tercatat!\n\nTotal kalori hari ini: {$totalKalori} kkal");
        return response('ok');
    }

    private function handleStatus(TelegramService $telegram)
    {
        $plan = DietPlan::getActivePlan();
        if (!$plan) {
            $telegram->sendMessage("Belum ada program diet aktif.");
            return response('ok');
        }

        $today = now()->toDateString();
        $kalori = Meal::where('diet_plan_id', $plan->id)->whereDate('tanggal', $today)->sum('kalori');
        $air = WaterLog::where('diet_plan_id', $plan->id)->whereDate('tanggal', $today)->sum('jumlah_ml');
        $meals = Meal::where('diet_plan_id', $plan->id)->whereDate('tanggal', $today)->count();

        $pctKalori = round(($kalori / max(1, $plan->kalori_harian_target)) * 100);
        $targetAir = round(($plan->berat_sekarang ?? $plan->berat_awal) * 33);
        $pctAir = round(($air / max(1, $targetAir)) * 100);

        $msg = "📊 <b>Status Diet Hari Ini</b>\n\n";
        $msg .= "🍽 Kalori: {$kalori} / {$plan->kalori_harian_target} kkal ({$pctKalori}%)\n";
        $msg .= "💧 Air: {$air} / {$targetAir} ml ({$pctAir}%)\n";
        $msg .= "📝 Catatan makan: {$meals}x\n";
        $msg .= "\n⚖️ Berat: " . ($plan->berat_sekarang ?? $plan->berat_awal) . " kg → Target: {$plan->berat_target} kg";

        $telegram->sendMessage($msg);
        return response('ok');
    }

    private function handleBerat(string $text, TelegramService $telegram)
    {
        $plan = DietPlan::getActivePlan();
        if (!$plan) {
            $telegram->sendMessage("Belum ada program diet aktif.");
            return response('ok');
        }

        $parts = explode(' ', $text, 2);
        $berat = isset($parts[1]) ? (float) trim($parts[1]) : 0;

        if ($berat < 20 || $berat > 300) {
            $telegram->sendMessage("Format: /berat [kg]\nContoh: /berat 72.5");
            return response('ok');
        }

        $beratLama = $plan->berat_sekarang ?? $plan->berat_awal;
        $plan->update(['berat_sekarang' => $berat]);

        // Catat ke daily activity
        DailyActivity::updateOrCreate(
            ['diet_plan_id' => $plan->id, 'tanggal' => now()->toDateString()],
            ['berat_badan' => $berat]
        );

        $selisih = $berat - $beratLama;
        $emoji = $selisih < 0 ? '📉' : ($selisih > 0 ? '📈' : '➡️');
        $selisihText = $selisih != 0 ? ' (' . ($selisih > 0 ? '+' : '') . number_format($selisih, 1) . ' kg)' : '';
        $sisaTarget = $berat - $plan->berat_target;

        $msg = "⚖️ Berat badan diupdate!\n\n";
        $msg .= "{$emoji} {$berat} kg{$selisihText}\n";
        $msg .= "🎯 Target: {$plan->berat_target} kg\n";
        $msg .= "📏 Sisa: " . number_format(abs($sisaTarget), 1) . " kg " . ($sisaTarget > 0 ? 'lagi' : '(sudah tercapai!)');

        $telegram->sendMessage($msg);
        return response('ok');
    }

    private function handleOlahraga(string $text, TelegramService $telegram)
    {
        $plan = DietPlan::getActivePlan();
        if (!$plan) {
            $telegram->sendMessage("Belum ada program diet aktif.");
            return response('ok');
        }

        // Parse: /olahraga jogging 30 or /olahraga push up 15
        $text = trim(str_replace('/olahraga', '', $text));
        $parts = explode(' ', $text);

        $durasi = 0;
        $nama = $text;

        if (count($parts) >= 2 && is_numeric(end($parts))) {
            $durasi = (int) array_pop($parts);
            $nama = implode(' ', $parts);
        }

        if (empty($nama) || $durasi <= 0) {
            $telegram->sendMessage("Format: /olahraga [nama] [menit]\nContoh: /olahraga jogging 30\n/olahraga push up 15");
            return response('ok');
        }

        // Estimasi kalori terbakar (rough: 5-8 kal/menit tergantung intensitas)
        $kalPerMenit = 6;
        $kaloriTerbakar = $durasi * $kalPerMenit;

        Exercise::create([
            'diet_plan_id' => $plan->id,
            'tanggal' => now()->toDateString(),
            'jenis_olahraga' => ucfirst($nama),
            'durasi_menit' => $durasi,
            'kalori_terbakar' => $kaloriTerbakar,
            'intensitas' => $durasi >= 30 ? 'sedang' : 'ringan',
        ]);

        $totalHariIni = Exercise::where('diet_plan_id', $plan->id)
            ->whereDate('tanggal', now()->toDateString())
            ->sum('kalori_terbakar');

        $msg = "🏃 Olahraga tercatat!\n\n";
        $msg .= "💪 " . ucfirst($nama) . "\n";
        $msg .= "⏱ {$durasi} menit\n";
        $msg .= "🔥 ~{$kaloriTerbakar} kkal terbakar\n\n";
        $msg .= "Total kalori terbakar hari ini: {$totalHariIni} kkal";

        $telegram->sendMessage($msg);
        return response('ok');
    }

    private function handleHapus(TelegramService $telegram)
    {
        $plan = DietPlan::getActivePlan();
        if (!$plan) {
            $telegram->sendMessage("Belum ada program diet aktif.");
            return response('ok');
        }

        $lastMeal = Meal::where('diet_plan_id', $plan->id)
            ->whereDate('tanggal', now()->toDateString())
            ->orderByDesc('id')
            ->first();

        if (!$lastMeal) {
            $telegram->sendMessage("Tidak ada catatan makan hari ini yang bisa dihapus.");
            return response('ok');
        }

        $nama = $lastMeal->nama_makanan;
        $kalori = $lastMeal->kalori;
        $lastMeal->delete();

        $totalKalori = Meal::where('diet_plan_id', $plan->id)
            ->whereDate('tanggal', now()->toDateString())
            ->sum('kalori');

        $telegram->sendMessage("🗑 Dihapus: {$nama} ({$kalori} kkal)\n\nTotal kalori sekarang: {$totalKalori} kkal");
        return response('ok');
    }

    private function handleResetAir(TelegramService $telegram)
    {
        $plan = DietPlan::getActivePlan();
        if (!$plan) {
            $telegram->sendMessage("Belum ada program diet aktif.");
            return response('ok');
        }

        $count = WaterLog::where('diet_plan_id', $plan->id)
            ->whereDate('tanggal', now()->toDateString())
            ->delete();

        $telegram->sendMessage("💧 Air minum hari ini di-reset! ({$count} catatan dihapus)\n\nTotal sekarang: 0 ml");
        return response('ok');
    }

    private function handleRiwayat(TelegramService $telegram)
    {
        $plan = DietPlan::getActivePlan();
        if (!$plan) {
            $telegram->sendMessage("Belum ada program diet aktif.");
            return response('ok');
        }

        $meals = Meal::where('diet_plan_id', $plan->id)
            ->whereDate('tanggal', now()->toDateString())
            ->orderBy('created_at')
            ->get();

        if ($meals->isEmpty()) {
            $telegram->sendMessage("Belum ada catatan makan hari ini.");
            return response('ok');
        }

        $msg = "📋 <b>Riwayat Makan Hari Ini</b>\n\n";
        $labels = ['sarapan' => '🌅', 'makan_siang' => '☀️', 'makan_malam' => '🌙', 'snack' => '🍪'];
        $total = 0;

        foreach ($meals as $i => $meal) {
            $emoji = $labels[$meal->waktu_makan] ?? '🍽';
            $msg .= ($i + 1) . ". {$emoji} {$meal->nama_makanan} — {$meal->kalori} kkal\n";
            $total += $meal->kalori;
        }

        $msg .= "\n<b>Total: {$total} / {$plan->kalori_harian_target} kkal</b>";
        $sisa = $plan->kalori_harian_target - $total;
        if ($sisa > 0) {
            $msg .= "\nSisa: {$sisa} kkal";
        } else {
            $msg .= "\n⚠️ Sudah melebihi target!";
        }

        $telegram->sendMessage($msg);
        return response('ok');
    }

    private function handleTarget(TelegramService $telegram)
    {
        $plan = DietPlan::getActivePlan();
        if (!$plan) {
            $telegram->sendMessage("Belum ada program diet aktif.");
            return response('ok');
        }

        $berat = $plan->berat_sekarang ?? $plan->berat_awal;
        $targetAir = round($berat * 33);

        $msg = "🎯 <b>Target Diet Kamu</b>\n\n";
        $msg .= "🔥 Kalori harian: {$plan->kalori_harian_target} kkal\n";
        $msg .= "💧 Air minum: {$targetAir} ml/hari\n";
        $msg .= "⚖️ Berat sekarang: {$berat} kg\n";
        $msg .= "🏁 Berat target: {$plan->berat_target} kg\n";
        $msg .= "📏 Sisa: " . number_format(abs($berat - $plan->berat_target), 1) . " kg\n\n";
        $msg .= "📅 Program: " . ($plan->tanggal_mulai ? $plan->tanggal_mulai->format('d/m/Y') : '-') . "\n";
        $msg .= "🏋️ Level aktivitas: {$plan->level_aktivitas}";

        $telegram->sendMessage($msg);
        return response('ok');
    }

    private function handleSaran(TelegramService $telegram)
    {
        $plan = DietPlan::getActivePlan();
        if (!$plan) {
            $telegram->sendMessage("Belum ada program diet aktif.");
            return response('ok');
        }

        $today = now()->toDateString();
        $kaloriMasuk = Meal::where('diet_plan_id', $plan->id)->whereDate('tanggal', $today)->sum('kalori');
        $sisaKalori = max(0, $plan->kalori_harian_target - $kaloriMasuk);
        $waktu = $this->guessWaktuMakan();

        $ai = new AiNutritionService();
        $saran = $ai->suggestMeals($sisaKalori, $waktu);

        if ($saran) {
            $msg = "💡 <b>Saran Makanan</b>\n\n";
            $msg .= "Sisa kalori: {$sisaKalori} kkal\nWaktu: " . ucfirst(str_replace('_', ' ', $waktu)) . "\n\n";
            $msg .= $saran;
            $telegram->sendMessage($msg);
        } else {
            $telegram->sendMessage("Tidak bisa generate saran saat ini. Coba lagi nanti.");
        }

        return response('ok');
    }

    private function handleHelp(TelegramService $telegram)
    {
        $msg = "🤖 <b>Smart Diet Bot - Full Control</b>\n\n";
        $msg .= "<b>📝 Input:</b>\n";
        $msg .= "📸 Kirim foto → AI analisis kalori\n";
        $msg .= "Ketik nama makanan → AI estimasi\n";
        $msg .= "/makan [nama] - Catat makan\n";
        $msg .= "/kalori [nama] [kkal] - Input manual\n";
        $msg .= "/minum [ml] - Catat air (default 250)\n";
        $msg .= "/berat [kg] - Update berat badan\n";
        $msg .= "/olahraga [nama] [menit] - Catat olahraga\n\n";
        $msg .= "<b>📊 Lihat:</b>\n";
        $msg .= "/status - Progress hari ini\n";
        $msg .= "/riwayat - Daftar makan hari ini\n";
        $msg .= "/target - Target diet kamu\n";
        $msg .= "/saran - AI saran makanan\n\n";
        $msg .= "<b>🗑 Edit/Hapus:</b>\n";
        $msg .= "/hapus - Hapus makan terakhir\n";
        $msg .= "/reset_air - Reset air hari ini\n\n";
        $msg .= "<b>Contoh:</b>\n";
        $msg .= "nasi goreng\n";
        $msg .= "/minum 500\n";
        $msg .= "/berat 72.5\n";
        $msg .= "/olahraga jogging 30\n";

        $telegram->sendMessage($msg);
        return response('ok');
    }

    private function guessWaktuMakan(): string
    {
        $hour = (int) now()->format('H');
        if ($hour < 10) return 'sarapan';
        if ($hour < 14) return 'makan_siang';
        if ($hour < 18) return 'snack';
        return 'makan_malam';
    }
}
