<?php

namespace App\Http\Controllers\DietTracker;

use App\Http\Controllers\Controller;
use App\Models\DietTracker\DietPlan;
use App\Models\DietTracker\Meal;
use App\Models\DietTracker\WaterLog;
use App\Models\DietTracker\FoodDatabase;
use App\Services\TelegramService;
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

        if (!$message || !isset($message['text'])) {
            return response('ok');
        }

        $chatId = (string) $message['chat']['id'];
        $configChatId = config('services.telegram.chat_id');

        // Only respond to configured chat
        if ($chatId !== $configChatId) {
            return response('ok');
        }

        $text = trim($message['text']);
        $telegram = new TelegramService();

        // Parse command
        if (str_starts_with($text, '/minum')) {
            return $this->handleMinum($text, $telegram);
        } elseif (str_starts_with($text, '/makan')) {
            return $this->handleMakan($text, $telegram);
        } elseif (str_starts_with($text, '/kalori')) {
            return $this->handleKaloriManual($text, $telegram);
        } elseif (str_starts_with($text, '/status')) {
            return $this->handleStatus($telegram);
        } elseif (str_starts_with($text, '/help') || $text === '/start') {
            return $this->handleHelp($telegram);
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
            $telegram->sendMessage("Format: /makan [nama makanan]\nContoh: /makan nasi goreng");
            return response('ok');
        }

        // Search food database
        $food = FoodDatabase::where('nama', 'like', "%{$query}%")->first();

        if (!$food) {
            // Show suggestions
            $suggestions = FoodDatabase::where('nama', 'like', "%{$query}%")->limit(5)->pluck('nama')->implode("\n- ");
            if ($suggestions) {
                $telegram->sendMessage("Tidak ditemukan persis. Mungkin maksud:\n- {$suggestions}\n\nAtau pakai /kalori [nama] [kkal] untuk input manual.");
            } else {
                $telegram->sendMessage("Makanan '{$query}' tidak ditemukan di database.\n\nPakai /kalori {$query} [kkal] untuk input manual.");
            }
            return response('ok');
        }

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

        $totalKalori = Meal::where('diet_plan_id', $plan->id)
            ->whereDate('tanggal', now()->toDateString())
            ->sum('kalori');

        $telegram->sendMessage("🍽 {$food->nama} ({$food->kalori} kkal) tercatat!\n\nTotal kalori hari ini: {$totalKalori} kkal\nTarget: {$plan->kalori_harian_target} kkal");
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

    private function handleHelp(TelegramService $telegram)
    {
        $msg = "🤖 <b>Diet Bot Commands</b>\n\n";
        $msg .= "/minum [ml] - Catat minum air (default 250ml)\n";
        $msg .= "/makan [nama] - Catat makan dari database\n";
        $msg .= "/kalori [nama] [kkal] - Catat makan manual\n";
        $msg .= "/status - Lihat progress hari ini\n";
        $msg .= "/help - Tampilkan bantuan ini\n\n";
        $msg .= "<b>Contoh:</b>\n";
        $msg .= "/minum 500\n";
        $msg .= "/makan nasi goreng\n";
        $msg .= "/kalori ayam bakar 250\n";

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
