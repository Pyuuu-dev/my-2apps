<?php

namespace App\Http\Controllers\DietTracker;

use App\Http\Controllers\Controller;
use App\Models\DietTracker\UserProfile;
use App\Models\DietTracker\FoodLog;
use App\Models\DietTracker\WeightLog;
use App\Models\DietTracker\WaterLog;
use App\Models\DietTracker\ExerciseLog;
use App\Models\DietTracker\Streak;
use App\Models\DietTracker\Badge;
use App\Models\DietTracker\DailySummary;
use App\Models\DietTracker\FoodDatabase;
use App\Services\AiNutritionService;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    private TelegramService $telegram;
    private AiNutritionService $ai;

    public function __construct()
    {
        $this->telegram = new TelegramService();
        $this->ai = new AiNutritionService();
    }

    public function handle(Request $request)
    {
        $update = $request->all();

        try {
            // Handle callback query (inline button press)
            if (isset($update['callback_query'])) {
                return $this->handleCallback($update['callback_query']);
            }

            // Handle message
            if (isset($update['message'])) {
                return $this->handleMessage($update['message']);
            }
        } catch (\Exception $e) {
            Log::error('Telegram webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return response()->json(['ok' => true]);
    }

    // ==========================================
    // MESSAGE HANDLER
    // ==========================================

    private function handleMessage(array $message): \Illuminate\Http\JsonResponse
    {
        $chatId = (string) $message['chat']['id'];
        $text = $message['text'] ?? '';
        $photo = $message['photo'] ?? null;

        // Get or create profile
        $profile = UserProfile::findOrCreateByChatId($chatId, [
            'nama' => $message['from']['first_name'] ?? 'User',
            'username' => $message['from']['username'] ?? null,
        ]);

        // Handle photo (food recognition)
        if ($photo) {
            return $this->handlePhoto($profile, $message);
        }

        // Handle conversation state
        if ($profile->state) {
            return $this->handleState($profile, $text);
        }

        // Handle commands
        if (str_starts_with($text, '/')) {
            return $this->handleCommand($profile, $text);
        }

        // Handle natural text (try to detect food)
        return $this->handleNaturalText($profile, $text);
    }

    // ==========================================
    // COMMAND HANDLERS
    // ==========================================

    private function handleCommand(UserProfile $profile, string $text): \Illuminate\Http\JsonResponse
    {
        $parts = explode(' ', $text, 2);
        $command = strtolower(str_replace('@' . env('TELEGRAM_BOT_USERNAME', ''), '', $parts[0]));
        $args = $parts[1] ?? '';

        return match ($command) {
            '/start' => $this->cmdStart($profile),
            '/menu' => $this->cmdMenu($profile),
            '/makan' => $this->cmdMakan($profile, $args),
            '/air' => $this->cmdAir($profile, $args),
            '/berat' => $this->cmdBerat($profile, $args),
            '/olahraga' => $this->cmdOlahraga($profile, $args),
            '/dashboard' => $this->cmdDashboard($profile),
            '/stats' => $this->cmdStats($profile),
            '/profil' => $this->cmdProfil($profile),
            '/target' => $this->cmdTarget($profile),
            '/riwayat' => $this->cmdRiwayat($profile),
            '/rekomendasi' => $this->cmdRekomendasi($profile),
            '/badge' => $this->cmdBadge($profile),
            '/reminder' => $this->cmdReminder($profile),
            '/help' => $this->cmdHelp($profile),
            '/setup' => $this->cmdSetup($profile),
            '/hapus' => $this->cmdHapus($profile, $args),
            '/reset' => $this->cmdReset($profile),
            default => $this->cmdUnknown($profile),
        };
    }

    private function cmdStart(UserProfile $profile): \Illuminate\Http\JsonResponse
    {
        $nama = $profile->nama ?? 'Kamu';

        if (!$profile->tinggi_cm || !$profile->berat_kg) {
            $text = "🎉 <b>Selamat datang di Diet Tracker Bot, {$nama}!</b>\n\n";
            $text .= "Aku adalah personal nutrition coach kamu. Aku bisa bantu:\n\n";
            $text .= "🍽 Catat makanan & hitung kalori otomatis\n";
            $text .= "📸 Analisis foto makanan dengan AI\n";
            $text .= "⚖️ Pantau berat badan & BMI\n";
            $text .= "💧 Tracking minum air\n";
            $text .= "🏃 Catat olahraga\n";
            $text .= "📊 Statistik & grafik progress\n";
            $text .= "🏆 Achievement & gamification\n";
            $text .= "⏰ Reminder otomatis\n\n";
            $text .= "Yuk setup profil dulu supaya aku bisa hitung target kalori kamu!\n\n";
            $text .= "Ketik /setup untuk mulai 👇";

            $this->telegram->sendMessage($profile->telegram_chat_id, $text);
        } else {
            $this->cmdMenu($profile);
        }

        return response()->json(['ok' => true]);
    }

    private function cmdMenu(UserProfile $profile): \Illuminate\Http\JsonResponse
    {
        $level = $profile->getLevel();
        $streak = $profile->streak;

        $text = "📋 <b>Menu Utama</b>\n";
        $text .= "━━━━━━━━━━━━━━━\n";
        $text .= "{$level['icon']} Level: {$level['nama']} | 🔥 Streak: " . ($streak->current_streak ?? 0) . " hari\n\n";

        $keyboard = [
            [
                ['text' => '📊 Dashboard', 'callback_data' => 'dashboard'],
                ['text' => '🍽 Catat Makan', 'callback_data' => 'log_food'],
            ],
            [
                ['text' => '💧 Minum Air', 'callback_data' => 'log_water'],
                ['text' => '⚖️ Berat Badan', 'callback_data' => 'log_weight'],
            ],
            [
                ['text' => '🏃 Olahraga', 'callback_data' => 'log_exercise'],
                ['text' => '🎯 Target', 'callback_data' => 'target'],
            ],
            [
                ['text' => '📈 Statistik', 'callback_data' => 'stats'],
                ['text' => '🏆 Badge', 'callback_data' => 'badges'],
            ],
            [
                ['text' => '💡 Rekomendasi AI', 'callback_data' => 'recommend'],
                ['text' => '👤 Profil', 'callback_data' => 'profile'],
            ],
            [
                ['text' => '⏰ Reminder', 'callback_data' => 'reminder_menu'],
                ['text' => '❓ Bantuan', 'callback_data' => 'help'],
            ],
        ];

        $this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id, $text, $keyboard);
        return response()->json(['ok' => true]);
    }

    private function cmdMakan(UserProfile $profile, string $args): \Illuminate\Http\JsonResponse
    {
        if (empty($args)) {
            $profile->update(['state' => 'waiting_food', 'state_data' => null]);

            $text = "🍽 <b>Catat Makanan</b>\n\n";
            $text .= "Ketik nama makanan yang kamu makan.\n";
            $text .= "Contoh:\n";
            $text .= "• <code>nasi goreng 1 porsi</code>\n";
            $text .= "• <code>ayam geprek + es teh</code>\n";
            $text .= "• <code>2 potong tempe goreng</code>\n\n";
            $text .= "Atau kirim 📸 <b>foto makanan</b> untuk analisis otomatis!\n\n";
            $text .= "Ketik /batal untuk membatalkan.";

            $keyboard = [
                [
                    ['text' => '🌅 Sarapan', 'callback_data' => 'meal_sarapan'],
                    ['text' => '☀️ Siang', 'callback_data' => 'meal_siang'],
                ],
                [
                    ['text' => '🌙 Malam', 'callback_data' => 'meal_malam'],
                    ['text' => '🍪 Snack', 'callback_data' => 'meal_snack'],
                ],
            ];

            $this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id, $text, $keyboard);
            return response()->json(['ok' => true]);
        }

        // Direct food logging
        return $this->logFood($profile, $args);
    }

    private function cmdAir(UserProfile $profile, string $args): \Illuminate\Http\JsonResponse
    {
        if (empty($args)) {
            $keyboard = [
                [
                    ['text' => '🥤 250ml', 'callback_data' => 'water_250'],
                    ['text' => '🥤 500ml', 'callback_data' => 'water_500'],
                ],
                [
                    ['text' => '🥤 750ml', 'callback_data' => 'water_750'],
                    ['text' => '🥤 1000ml', 'callback_data' => 'water_1000'],
                ],
            ];

            $todayWater = WaterLog::where('profile_id', $profile->id)
                ->whereDate('tanggal', now('Asia/Singapore')->toDateString())
                ->sum('jumlah_ml');

            $target = 2500; // ml
            $pct = min(100, round(($todayWater / $target) * 100));

            $text = "💧 <b>Catat Minum Air</b>\n\n";
            $text .= "Hari ini: {$todayWater}ml / {$target}ml ({$pct}%)\n";
            $text .= $this->progressBar($pct) . "\n\n";
            $text .= "Pilih jumlah atau ketik: <code>/air 300</code>";

            $this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id, $text, $keyboard);
            return response()->json(['ok' => true]);
        }

        $ml = (int) $args;
        if ($ml <= 0 || $ml > 5000) {
            $this->telegram->sendMessage($profile->telegram_chat_id, "❌ Jumlah tidak valid. Masukkan angka 1-5000 ml.");
            return response()->json(['ok' => true]);
        }

        return $this->logWater($profile, $ml);
    }

    private function cmdBerat(UserProfile $profile, string $args): \Illuminate\Http\JsonResponse
    {
        if (empty($args)) {
            $profile->update(['state' => 'waiting_weight', 'state_data' => null]);

            $lastWeight = WeightLog::where('profile_id', $profile->id)
                ->orderByDesc('tanggal')->first();

            $text = "⚖️ <b>Catat Berat Badan</b>\n\n";
            if ($lastWeight) {
                $text .= "Terakhir: <b>{$lastWeight->berat_kg} kg</b> ({$lastWeight->tanggal->format('d/m/Y')})\n";
            }
            if ($profile->berat_target) {
                $text .= "Target: <b>{$profile->berat_target} kg</b>\n";
            }
            $text .= "\nKetik berat badan kamu (dalam kg):\n";
            $text .= "Contoh: <code>70.5</code>\n\n";
            $text .= "Ketik /batal untuk membatalkan.";

            $this->telegram->sendMessage($profile->telegram_chat_id, $text);
            return response()->json(['ok' => true]);
        }

        $berat = (float) $args;
        return $this->logWeight($profile, $berat);
    }

    private function cmdOlahraga(UserProfile $profile, string $args): \Illuminate\Http\JsonResponse
    {
        if (empty($args)) {
            $profile->update(['state' => 'waiting_exercise', 'state_data' => null]);

            $text = "🏃 <b>Catat Olahraga</b>\n\n";
            $text .= "Ketik jenis olahraga dan durasi:\n";
            $text .= "Contoh:\n";
            $text .= "• <code>lari 30 menit</code>\n";
            $text .= "• <code>gym 60 menit</code>\n";
            $text .= "• <code>renang 45 menit</code>\n\n";
            $text .= "Ketik /batal untuk membatalkan.";

            $keyboard = [
                [
                    ['text' => '🏃 Lari', 'callback_data' => 'exercise_lari'],
                    ['text' => '🚶 Jalan', 'callback_data' => 'exercise_jalan'],
                ],
                [
                    ['text' => '🏋️ Gym', 'callback_data' => 'exercise_gym'],
                    ['text' => '🏊 Renang', 'callback_data' => 'exercise_renang'],
                ],
                [
                    ['text' => '🚴 Sepeda', 'callback_data' => 'exercise_sepeda'],
                    ['text' => '🧘 Yoga', 'callback_data' => 'exercise_yoga'],
                ],
            ];

            $this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id, $text, $keyboard);
            return response()->json(['ok' => true]);
        }

        return $this->logExercise($profile, $args);
    }

    private function cmdDashboard(UserProfile $profile): \Illuminate\Http\JsonResponse
    {
        $today = now('Asia/Singapore')->toDateString();

        $foodLogs = FoodLog::where('profile_id', $profile->id)
            ->whereDate('tanggal', $today)->get();

        $waterTotal = WaterLog::where('profile_id', $profile->id)
            ->whereDate('tanggal', $today)->sum('jumlah_ml');

        $exerciseTotal = ExerciseLog::where('profile_id', $profile->id)
            ->whereDate('tanggal', $today)->get();

        $totalKalori = $foodLogs->sum('kalori');
        $totalProtein = $foodLogs->sum('protein');
        $totalKarbo = $foodLogs->sum('karbohidrat');
        $totalLemak = $foodLogs->sum('lemak');
        $totalExerciseCal = $exerciseTotal->sum('kalori_terbakar');

        $targetKalori = $profile->kalori_target ?: 2000;
        $targetProtein = $profile->protein_target ?: 100;
        $targetAir = 2500;

        $sisaKalori = $targetKalori - $totalKalori + $totalExerciseCal;
        $pctKalori = min(100, round(($totalKalori / $targetKalori) * 100));
        $pctProtein = min(100, round(($totalProtein / max(1, $targetProtein)) * 100));
        $pctAir = min(100, round(($waterTotal / $targetAir) * 100));

        $streak = $profile->streak;
        $level = $profile->getLevel();

        $text = "📊 <b>Dashboard Hari Ini</b>\n";
        $text .= "📅 " . now('Asia/Singapore')->translatedFormat('l, d F Y') . "\n";
        $text .= "━━━━━━━━━━━━━━━\n\n";

        // Level & Streak
        $text .= "{$level['icon']} {$level['nama']} | 🔥 Streak: " . ($streak->current_streak ?? 0) . " hari\n\n";

        // Kalori
        $text .= "🔥 <b>Kalori</b>\n";
        $text .= $this->progressBar($pctKalori) . " {$pctKalori}%\n";
        $text .= "   {$totalKalori} / {$targetKalori} kkal";
        if ($totalExerciseCal > 0) {
            $text .= " (-{$totalExerciseCal} olahraga)";
        }
        $text .= "\n   Sisa: <b>{$sisaKalori} kkal</b>\n\n";

        // Macros
        $text .= "🥩 <b>Protein:</b> {$totalProtein}g / {$targetProtein}g ({$pctProtein}%)\n";
        $text .= $this->progressBar($pctProtein, 15) . "\n";
        $text .= "🍚 <b>Karbo:</b> {$totalKarbo}g / " . ($profile->karbo_target ?: 250) . "g\n";
        $text .= "🧈 <b>Lemak:</b> {$totalLemak}g / " . ($profile->lemak_target ?: 65) . "g\n\n";

        // Air
        $text .= "💧 <b>Air:</b> {$waterTotal}ml / {$targetAir}ml ({$pctAir}%)\n";
        $text .= $this->progressBar($pctAir, 15) . "\n\n";

        // Olahraga
        if ($exerciseTotal->count() > 0) {
            $text .= "🏃 <b>Olahraga:</b> " . $exerciseTotal->sum('durasi_menit') . " menit ({$totalExerciseCal} kkal)\n\n";
        }

        // Warning
        if ($pctKalori > 100) {
            $over = $totalKalori - $targetKalori;
            $text .= "⚠️ <b>OVER KALORI!</b> Kelebihan {$over} kkal\n";
            $text .= "💡 Coba olahraga ringan atau kurangi porsi makan berikutnya.\n\n";
        } elseif ($pctKalori >= 80 && $pctKalori <= 100) {
            $text .= "✅ Bagus! Kamu hampir mencapai target hari ini.\n\n";
        }

        // Quick actions
        $keyboard = [
            [
                ['text' => '🍽 + Makan', 'callback_data' => 'log_food'],
                ['text' => '💧 + Air', 'callback_data' => 'log_water'],
            ],
            [
                ['text' => '💡 Rekomendasi', 'callback_data' => 'recommend'],
                ['text' => '📋 Riwayat', 'callback_data' => 'history'],
            ],
        ];

        $this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id, $text, $keyboard);

        // Update daily summary
        DailySummary::recalculate($profile->id, $today);

        return response()->json(['ok' => true]);
    }

    private function cmdStats(UserProfile $profile): \Illuminate\Http\JsonResponse
    {
        $today = now('Asia/Singapore');
        $weekStart = $today->copy()->startOfWeek();

        $summaries = DailySummary::where('profile_id', $profile->id)
            ->whereBetween('tanggal', [$weekStart->toDateString(), $today->toDateString()])
            ->orderBy('tanggal')
            ->get();

        $weightLogs = WeightLog::where('profile_id', $profile->id)
            ->where('tanggal', '>=', $today->copy()->subDays(30)->toDateString())
            ->orderBy('tanggal')
            ->get();

        $text = "📈 <b>Statistik Mingguan</b>\n";
        $text .= "📅 {$weekStart->format('d/m')} - {$today->format('d/m/Y')}\n";
        $text .= "━━━━━━━━━━━━━━━\n\n";

        if ($summaries->isEmpty()) {
            $text .= "Belum ada data minggu ini.\nMulai catat makanan dengan /makan\n";
        } else {
            $avgKalori = round($summaries->avg('total_kalori'));
            $avgProtein = round($summaries->avg('total_protein'), 1);
            $avgAir = round($summaries->avg('total_air_ml'));
            $totalExercise = $summaries->sum('total_exercise_menit');

            $text .= "📊 <b>Rata-rata Harian:</b>\n";
            $text .= "   🔥 Kalori: {$avgKalori} kkal\n";
            $text .= "   🥩 Protein: {$avgProtein}g\n";
            $text .= "   💧 Air: {$avgAir}ml\n";
            $text .= "   🏃 Total olahraga: {$totalExercise} menit\n\n";

            // Daily breakdown
            $text .= "📅 <b>Per Hari:</b>\n";
            $days = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
            foreach ($summaries as $s) {
                $dayName = $days[$s->tanggal->dayOfWeekIso - 1];
                $bar = $this->miniBar($s->pct_target);
                $text .= "   {$dayName}: {$bar} {$s->total_kalori} kkal\n";
            }
            $text .= "\n";
        }

        // Weight trend
        if ($weightLogs->count() >= 2) {
            $first = $weightLogs->first()->berat_kg;
            $last = $weightLogs->last()->berat_kg;
            $diff = round($last - $first, 1);
            $arrow = $diff > 0 ? '📈' : ($diff < 0 ? '📉' : '➡️');
            $sign = $diff > 0 ? '+' : '';

            $text .= "⚖️ <b>Berat Badan (30 hari):</b>\n";
            $text .= "   {$first} kg → {$last} kg ({$arrow} {$sign}{$diff} kg)\n";
        }

        $keyboard = [
            [
                ['text' => '📊 Dashboard', 'callback_data' => 'dashboard'],
                ['text' => '💡 Analisis AI', 'callback_data' => 'analyze_weekly'],
            ],
        ];

        $this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id, $text, $keyboard);
        return response()->json(['ok' => true]);
    }

    private function cmdProfil(UserProfile $profile): \Illuminate\Http\JsonResponse
    {
        $bmi = $profile->bmi ?: $profile->hitungBMI();
        $bmiCategory = $this->getBmiCategory($bmi);
        $level = $profile->getLevel();

        $text = "👤 <b>Profil Kamu</b>\n";
        $text .= "━━━━━━━━━━━━━━━\n\n";
        $text .= "{$level['icon']} Level: <b>{$level['nama']}</b>\n\n";
        $text .= "📝 Nama: <b>" . ($profile->nama ?? '-') . "</b>\n";
        $text .= "👤 Gender: <b>" . ($profile->gender ? ucfirst($profile->gender) : '-') . "</b>\n";
        $text .= "🎂 Umur: <b>" . ($profile->umur ?? '-') . " tahun</b>\n";
        $text .= "📏 Tinggi: <b>" . ($profile->tinggi_cm ?? '-') . " cm</b>\n";
        $text .= "⚖️ Berat: <b>" . ($profile->berat_kg ?? '-') . " kg</b>\n";
        $text .= "🎯 Target: <b>" . ($profile->berat_target ?? '-') . " kg</b>\n";
        $text .= "🏃 Aktivitas: <b>" . ($profile->level_aktivitas ?? '-') . "</b>\n";
        $text .= "🎯 Goal: <b>" . ($profile->goal ?? '-') . "</b>\n\n";

        $text .= "📊 <b>Body Metrics:</b>\n";
        $text .= "   BMI: <b>{$bmi}</b> ({$bmiCategory})\n";
        $text .= "   BMR: <b>" . round($profile->bmr ?? 0) . "</b> kkal/hari\n";
        $text .= "   TDEE: <b>" . round($profile->tdee ?? 0) . "</b> kkal/hari\n";
        $text .= "   Body Fat: <b>" . ($profile->body_fat_pct ?? '-') . "%</b>\n\n";

        $text .= "🎯 <b>Target Harian:</b>\n";
        $text .= "   Kalori: <b>" . ($profile->kalori_target ?? '-') . "</b> kkal\n";
        $text .= "   Protein: <b>" . ($profile->protein_target ?? '-') . "</b>g\n";
        $text .= "   Karbo: <b>" . ($profile->karbo_target ?? '-') . "</b>g\n";
        $text .= "   Lemak: <b>" . ($profile->lemak_target ?? '-') . "</b>g\n";

        $keyboard = [
            [
                ['text' => '✏️ Edit Profil', 'callback_data' => 'edit_profile'],
                ['text' => '🔄 Recalculate', 'callback_data' => 'recalculate'],
            ],
            [
                ['text' => '◀️ Menu', 'callback_data' => 'menu'],
            ],
        ];

        $this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id, $text, $keyboard);
        return response()->json(['ok' => true]);
    }

    private function cmdTarget(UserProfile $profile): \Illuminate\Http\JsonResponse
    {
        $today = now('Asia/Singapore')->toDateString();

        $foodLogs = FoodLog::where('profile_id', $profile->id)
            ->whereDate('tanggal', $today)->get();

        $totalKalori = $foodLogs->sum('kalori');
        $totalProtein = $foodLogs->sum('protein');
        $totalKarbo = $foodLogs->sum('karbohidrat');
        $totalLemak = $foodLogs->sum('lemak');

        $targetKalori = $profile->kalori_target ?: 2000;
        $targetProtein = $profile->protein_target ?: 100;
        $targetKarbo = $profile->karbo_target ?: 250;
        $targetLemak = $profile->lemak_target ?: 65;

        $text = "🎯 <b>Target Harian</b>\n";
        $text .= "━━━━━━━━━━━━━━━\n\n";

        $text .= "🔥 <b>Kalori:</b> {$totalKalori} / {$targetKalori} kkal\n";
        $text .= $this->progressBar(min(100, round(($totalKalori / $targetKalori) * 100))) . "\n\n";

        $text .= "🥩 <b>Protein:</b> {$totalProtein}g / {$targetProtein}g\n";
        $text .= $this->progressBar(min(100, round(($totalProtein / max(1, $targetProtein)) * 100))) . "\n\n";

        $text .= "🍚 <b>Karbohidrat:</b> {$totalKarbo}g / {$targetKarbo}g\n";
        $text .= $this->progressBar(min(100, round(($totalKarbo / max(1, $targetKarbo)) * 100))) . "\n\n";

        $text .= "🧈 <b>Lemak:</b> {$totalLemak}g / {$targetLemak}g\n";
        $text .= $this->progressBar(min(100, round(($totalLemak / max(1, $targetLemak)) * 100))) . "\n";

        $this->telegram->sendMessage($profile->telegram_chat_id, $text);
        return response()->json(['ok' => true]);
    }

    private function cmdRiwayat(UserProfile $profile): \Illuminate\Http\JsonResponse
    {
        $today = now('Asia/Singapore')->toDateString();
        $logs = FoodLog::where('profile_id', $profile->id)
            ->whereDate('tanggal', $today)
            ->orderBy('created_at')
            ->get();

        $text = "📋 <b>Riwayat Makan Hari Ini</b>\n";
        $text .= "━━━━━━━━━━━━━━━\n\n";

        if ($logs->isEmpty()) {
            $text .= "Belum ada catatan hari ini.\n";
            $text .= "Mulai catat dengan /makan\n";
        } else {
            $grouped = $logs->groupBy('waktu_makan');
            $labels = [
                'sarapan' => '🌅 Sarapan',
                'makan_siang' => '☀️ Makan Siang',
                'makan_malam' => '🌙 Makan Malam',
                'snack' => '🍪 Snack',
            ];

            foreach ($labels as $key => $label) {
                if (isset($grouped[$key])) {
                    $text .= "<b>{$label}:</b>\n";
                    foreach ($grouped[$key] as $log) {
                        $text .= "  • {$log->nama_makanan} ({$log->kalori} kkal)\n";
                    }
                    $text .= "  Subtotal: " . $grouped[$key]->sum('kalori') . " kkal\n\n";
                }
            }

            $text .= "━━━━━━━━━━━━━━━\n";
            $text .= "📊 <b>Total: {$logs->sum('kalori')} kkal</b>\n";
        }

        $keyboard = [
            [
                ['text' => '🗑 Hapus Terakhir', 'callback_data' => 'delete_last_food'],
                ['text' => '📊 Dashboard', 'callback_data' => 'dashboard'],
            ],
        ];

        $this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id, $text, $keyboard);
        return response()->json(['ok' => true]);
    }

    private function cmdRekomendasi(UserProfile $profile): \Illuminate\Http\JsonResponse
    {
        $this->telegram->sendChatAction($profile->telegram_chat_id, 'typing');

        $today = now('Asia/Singapore')->toDateString();
        $totalKalori = FoodLog::where('profile_id', $profile->id)
            ->whereDate('tanggal', $today)->sum('kalori');

        $targetKalori = $profile->kalori_target ?: 2000;
        $sisaKalori = $targetKalori - $totalKalori;

        $currentMacros = [
            'protein' => FoodLog::where('profile_id', $profile->id)->whereDate('tanggal', $today)->sum('protein'),
            'karbo' => FoodLog::where('profile_id', $profile->id)->whereDate('tanggal', $today)->sum('karbohidrat'),
            'lemak' => FoodLog::where('profile_id', $profile->id)->whereDate('tanggal', $today)->sum('lemak'),
        ];

        $result = $this->ai->recommendMenu($sisaKalori, $profile->goal ?? 'diet', $currentMacros, $profile->id);

        if (!$result['success']) {
            $text = "💡 <b>Rekomendasi Menu</b>\n\n";
            $text .= "Sisa kalori: <b>{$sisaKalori} kkal</b>\n\n";
            $text .= "Maaf, AI sedang tidak tersedia. Berikut saran umum:\n\n";

            if ($sisaKalori > 500) {
                $text .= "• Makan lengkap: nasi + lauk + sayur\n";
                $text .= "• Pilih protein tinggi: ayam bakar, ikan\n";
            } elseif ($sisaKalori > 200) {
                $text .= "• Snack sehat: buah, yogurt, kacang\n";
                $text .= "• Hindari gorengan\n";
            } else {
                $text .= "• Target hampir tercapai!\n";
                $text .= "• Minum air putih saja\n";
            }
        } else {
            $data = $result['data'];
            $text = "💡 <b>Rekomendasi AI</b>\n";
            $text .= "Sisa kalori: <b>{$sisaKalori} kkal</b>\n";
            $text .= "━━━━━━━━━━━━━━━\n\n";

            if (isset($data['rekomendasi'])) {
                foreach ($data['rekomendasi'] as $i => $rec) {
                    $num = $i + 1;
                    $text .= "{$num}. <b>{$rec['nama']}</b> (~{$rec['kalori']} kkal)\n";
                    if (isset($rec['protein'])) {
                        $text .= "   Protein: {$rec['protein']}g\n";
                    }
                    if (isset($rec['alasan'])) {
                        $text .= "   💬 {$rec['alasan']}\n";
                    }
                    $text .= "\n";
                }
            }

            if (isset($data['tips'])) {
                $text .= "💡 <b>Tips:</b> {$data['tips']}\n";
            }
            if (isset($data['warning']) && $data['warning']) {
                $text .= "\n⚠️ {$data['warning']}\n";
            }
        }

        $this->telegram->sendMessage($profile->telegram_chat_id, $text);
        return response()->json(['ok' => true]);
    }

    private function cmdBadge(UserProfile $profile): \Illuminate\Http\JsonResponse
    {
        $badges = Badge::where('profile_id', $profile->id)
            ->orderByDesc('earned_at')->get();

        $level = $profile->getLevel();
        $streak = $profile->streak;

        $text = "🏆 <b>Achievement Badges</b>\n";
        $text .= "━━━━━━━━━━━━━━━\n\n";
        $text .= "{$level['icon']} Level: <b>{$level['nama']}</b> (Lv.{$level['level']})\n";
        $text .= "🔥 Streak: <b>" . ($streak->current_streak ?? 0) . "</b> hari\n";
        $text .= "📅 Total hari aktif: <b>" . ($streak->total_days_logged ?? 0) . "</b>\n";
        $text .= "🏅 Longest streak: <b>" . ($streak->longest_streak ?? 0) . "</b> hari\n\n";

        if ($badges->isEmpty()) {
            $text .= "Belum ada badge. Terus konsisten untuk mendapatkan badge! 💪\n\n";
            $text .= "🔓 <b>Badge yang bisa didapat:</b>\n";
            $text .= "• 🔥 Streak 3 hari\n";
            $text .= "• ⭐ Streak 7 hari\n";
            $text .= "• 🌟 Streak 14 hari\n";
            $text .= "• 💎 Streak 30 hari\n";
            $text .= "• 👑 Streak 60 hari\n";
            $text .= "• 🏆 Streak 100 hari\n";
        } else {
            $text .= "🎖 <b>Badge Kamu ({$badges->count()}):</b>\n\n";
            foreach ($badges as $badge) {
                $text .= "{$badge->badge_icon} <b>{$badge->badge_name}</b>\n";
                $text .= "   {$badge->deskripsi}\n";
                $text .= "   📅 {$badge->earned_at->format('d/m/Y')}\n\n";
            }
        }

        $this->telegram->sendMessage($profile->telegram_chat_id, $text);
        return response()->json(['ok' => true]);
    }

    private function cmdReminder(UserProfile $profile): \Illuminate\Http\JsonResponse
    {
        $text = "⏰ <b>Pengingat</b>\n";
        $text .= "━━━━━━━━━━━━━━━\n\n";
        $text .= "Pilih jenis pengingat yang ingin diatur:";

        $keyboard = [
            [
                ['text' => '💧 Minum Air', 'callback_data' => 'reminder_water'],
                ['text' => '🍽 Jam Makan', 'callback_data' => 'reminder_meal'],
            ],
            [
                ['text' => '🏃 Olahraga', 'callback_data' => 'reminder_exercise'],
                ['text' => '😴 Tidur', 'callback_data' => 'reminder_sleep'],
            ],
            [
                ['text' => '📋 Lihat Semua', 'callback_data' => 'reminder_list'],
                ['text' => '🗑 Hapus Semua', 'callback_data' => 'reminder_clear'],
            ],
        ];

        $this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id, $text, $keyboard);
        return response()->json(['ok' => true]);
    }

    private function cmdHelp(UserProfile $profile): \Illuminate\Http\JsonResponse
    {
        $text = "❓ <b>Panduan Penggunaan Bot</b>\n";
        $text .= "━━━━━━━━━━━━━━━━━━━\n\n";

        $text .= "🍽 <b>Catat Makanan:</b>\n";
        $text .= "• /makan nasi goreng 1 porsi\n";
        $text .= "• /makan ayam geprek + es teh\n";
        $text .= "• Kirim foto makanan langsung\n";
        $text .= "• Ketik nama makanan langsung\n\n";

        $text .= "💧 <b>Catat Air:</b>\n";
        $text .= "• /air 500 (dalam ml)\n";
        $text .= "• /air (pilih dari tombol)\n\n";

        $text .= "⚖️ <b>Berat Badan:</b>\n";
        $text .= "• /berat 70.5\n\n";

        $text .= "🏃 <b>Olahraga:</b>\n";
        $text .= "• /olahraga lari 30 menit\n";
        $text .= "• /olahraga gym 60 menit\n\n";

        $text .= "📊 <b>Informasi:</b>\n";
        $text .= "• /dashboard - Ringkasan hari ini\n";
        $text .= "• /stats - Statistik mingguan\n";
        $text .= "• /profil - Lihat profil\n";
        $text .= "• /target - Target harian\n";
        $text .= "• /riwayat - Riwayat makan\n";
        $text .= "• /rekomendasi - Saran menu AI\n";
        $text .= "• /badge - Achievement\n\n";

        $text .= "⚙️ <b>Pengaturan:</b>\n";
        $text .= "• /setup - Setup/edit profil\n";
        $text .= "• /reminder - Atur pengingat\n";
        $text .= "• /hapus terakhir - Hapus log terakhir\n";
        $text .= "• /menu - Menu utama\n\n";

        $text .= "💡 <b>Tips:</b>\n";
        $text .= "• Kirim foto makanan untuk analisis AI\n";
        $text .= "• Ketik nama makanan langsung tanpa command\n";
        $text .= "• Bot akan otomatis mengenali makanan Indonesia";

        $this->telegram->sendMessage($profile->telegram_chat_id, $text);
        return response()->json(['ok' => true]);
    }

    private function cmdSetup(UserProfile $profile): \Illuminate\Http\JsonResponse
    {
        $profile->update(['state' => 'setup_gender', 'state_data' => []]);

        $text = "⚙️ <b>Setup Profil</b>\n\n";
        $text .= "Mari kita setup profil kamu untuk menghitung target kalori yang tepat.\n\n";
        $text .= "Pilih jenis kelamin:";

        $keyboard = [
            [
                ['text' => '👨 Pria', 'callback_data' => 'setup_gender_pria'],
                ['text' => '👩 Wanita', 'callback_data' => 'setup_gender_wanita'],
            ],
        ];

        $this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id, $text, $keyboard);
        return response()->json(['ok' => true]);
    }

    private function cmdHapus(UserProfile $profile, string $args): \Illuminate\Http\JsonResponse
    {
        if ($args === 'terakhir' || empty($args)) {
            $lastLog = FoodLog::where('profile_id', $profile->id)
                ->orderByDesc('created_at')->first();

            if ($lastLog) {
                $nama = $lastLog->nama_makanan;
                $kalori = $lastLog->kalori;
                $lastLog->delete();

                DailySummary::recalculate($profile->id, now('Asia/Singapore')->toDateString());

                $this->telegram->sendMessage($profile->telegram_chat_id,
                    "🗑 Dihapus: <b>{$nama}</b> ({$kalori} kkal)");
            } else {
                $this->telegram->sendMessage($profile->telegram_chat_id,
                    "❌ Tidak ada catatan untuk dihapus.");
            }
        }

        return response()->json(['ok' => true]);
    }

    private function cmdReset(UserProfile $profile): \Illuminate\Http\JsonResponse
    {
        $profile->update(['state' => null, 'state_data' => null]);
        $this->telegram->sendMessage($profile->telegram_chat_id, "✅ State direset. Ketik /menu untuk kembali.");
        return response()->json(['ok' => true]);
    }

    private function cmdUnknown(UserProfile $profile): \Illuminate\Http\JsonResponse
    {
        $this->telegram->sendMessage($profile->telegram_chat_id,
            "❓ Command tidak dikenal. Ketik /help untuk bantuan atau /menu untuk menu utama.");
        return response()->json(['ok' => true]);
    }

    // ==========================================
    // STATE HANDLERS (Conversation Flow)
    // ==========================================

    private function handleState(UserProfile $profile, string $text): \Illuminate\Http\JsonResponse
    {
        // Cancel command
        if ($text === '/batal' || $text === '/cancel') {
            $profile->update(['state' => null, 'state_data' => null]);
            $this->telegram->sendMessage($profile->telegram_chat_id, "❌ Dibatalkan. Ketik /menu untuk kembali.");
            return response()->json(['ok' => true]);
        }

        // Allow other commands to override state
        if (str_starts_with($text, '/') && !in_array($text, ['/batal', '/cancel'])) {
            $profile->update(['state' => null, 'state_data' => null]);
            return $this->handleCommand($profile, $text);
        }

        return match ($profile->state) {
            'waiting_food' => $this->logFood($profile, $text),
            'waiting_weight' => $this->stateWeight($profile, $text),
            'waiting_exercise' => $this->logExercise($profile, $text),
            'setup_umur' => $this->stateSetupUmur($profile, $text),
            'setup_tinggi' => $this->stateSetupTinggi($profile, $text),
            'setup_berat' => $this->stateSetupBerat($profile, $text),
            'setup_target' => $this->stateSetupTarget($profile, $text),
            'waiting_exercise_duration' => $this->stateExerciseDuration($profile, $text),
            'reminder_time' => $this->stateReminderTime($profile, $text),
            default => $this->handleNaturalText($profile, $text),
        };
    }

    private function stateWeight(UserProfile $profile, string $text): \Illuminate\Http\JsonResponse
    {
        $berat = (float) $text;
        if ($berat < 20 || $berat > 300) {
            $this->telegram->sendMessage($profile->telegram_chat_id,
                "❌ Berat tidak valid. Masukkan angka antara 20-300 kg.\nContoh: <code>70.5</code>");
            return response()->json(['ok' => true]);
        }

        $profile->update(['state' => null, 'state_data' => null]);
        return $this->logWeight($profile, $berat);
    }

    private function stateSetupUmur(UserProfile $profile, string $text): \Illuminate\Http\JsonResponse
    {
        $umur = (int) $text;
        if ($umur < 10 || $umur > 100) {
            $this->telegram->sendMessage($profile->telegram_chat_id, "❌ Umur tidak valid (10-100). Coba lagi:");
            return response()->json(['ok' => true]);
        }

        $data = $profile->state_data ?? [];
        $data['umur'] = $umur;
        $profile->update(['state' => 'setup_tinggi', 'state_data' => $data]);

        $this->telegram->sendMessage($profile->telegram_chat_id,
            "✅ Umur: {$umur} tahun\n\n📏 Berapa tinggi badan kamu (dalam cm)?\nContoh: <code>170</code>");

        return response()->json(['ok' => true]);
    }

    private function stateSetupTinggi(UserProfile $profile, string $text): \Illuminate\Http\JsonResponse
    {
        $tinggi = (float) $text;
        if ($tinggi < 100 || $tinggi > 250) {
            $this->telegram->sendMessage($profile->telegram_chat_id, "❌ Tinggi tidak valid (100-250 cm). Coba lagi:");
            return response()->json(['ok' => true]);
        }

        $data = $profile->state_data ?? [];
        $data['tinggi'] = $tinggi;
        $profile->update(['state' => 'setup_berat', 'state_data' => $data]);

        $this->telegram->sendMessage($profile->telegram_chat_id,
            "✅ Tinggi: {$tinggi} cm\n\n⚖️ Berapa berat badan kamu sekarang (dalam kg)?\nContoh: <code>70.5</code>");

        return response()->json(['ok' => true]);
    }

    private function stateSetupBerat(UserProfile $profile, string $text): \Illuminate\Http\JsonResponse
    {
        $berat = (float) $text;
        if ($berat < 20 || $berat > 300) {
            $this->telegram->sendMessage($profile->telegram_chat_id, "❌ Berat tidak valid (20-300 kg). Coba lagi:");
            return response()->json(['ok' => true]);
        }

        $data = $profile->state_data ?? [];
        $data['berat'] = $berat;
        $profile->update(['state' => 'setup_target', 'state_data' => $data]);

        $this->telegram->sendMessage($profile->telegram_chat_id,
            "✅ Berat: {$berat} kg\n\n🎯 Berapa target berat badan kamu (dalam kg)?\nContoh: <code>65</code>\n\nKetik <code>0</code> jika tidak ada target spesifik.");

        return response()->json(['ok' => true]);
    }

    private function stateSetupTarget(UserProfile $profile, string $text): \Illuminate\Http\JsonResponse
    {
        $target = (float) $text;
        $data = $profile->state_data ?? [];

        if ($target > 0 && ($target < 20 || $target > 300)) {
            $this->telegram->sendMessage($profile->telegram_chat_id, "❌ Target tidak valid. Coba lagi:");
            return response()->json(['ok' => true]);
        }

        $data['target'] = $target > 0 ? $target : null;

        // Determine goal
        $berat = $data['berat'];
        $goal = 'maintenance';
        if ($target > 0) {
            if ($target < $berat) $goal = 'cutting';
            elseif ($target > $berat) $goal = 'bulking';
        }

        $keyboard = [
            [
                ['text' => '🪑 Sedentary (jarang gerak)', 'callback_data' => 'setup_activity_sedentary'],
            ],
            [
                ['text' => '🚶 Light (1-3x/minggu)', 'callback_data' => 'setup_activity_light'],
            ],
            [
                ['text' => '🏃 Moderate (3-5x/minggu)', 'callback_data' => 'setup_activity_moderate'],
            ],
            [
                ['text' => '💪 Active (6-7x/minggu)', 'callback_data' => 'setup_activity_active'],
            ],
            [
                ['text' => '🔥 Very Active (2x/hari)', 'callback_data' => 'setup_activity_very_active'],
            ],
        ];

        $data['goal'] = $goal;
        $profile->update(['state' => 'setup_activity', 'state_data' => $data]);

        $this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id,
            "✅ Target: " . ($target > 0 ? "{$target} kg" : "Tidak ada") . "\n\n🏃 Pilih level aktivitas kamu:", $keyboard);

        return response()->json(['ok' => true]);
    }

    private function stateExerciseDuration(UserProfile $profile, string $text): \Illuminate\Http\JsonResponse
    {
        $durasi = (int) preg_replace('/[^0-9]/', '', $text);
        if ($durasi <= 0 || $durasi > 480) {
            $this->telegram->sendMessage($profile->telegram_chat_id, "❌ Durasi tidak valid (1-480 menit). Coba lagi:");
            return response()->json(['ok' => true]);
        }

        $data = $profile->state_data ?? [];
        $jenis = $data['jenis'] ?? 'olahraga';

        $profile->update(['state' => null, 'state_data' => null]);
        return $this->saveExercise($profile, $jenis, $durasi);
    }

    private function stateReminderTime(UserProfile $profile, string $text): \Illuminate\Http\JsonResponse
    {
        // Validate time format HH:MM
        if (!preg_match('/^([01]?[0-9]|2[0-3]):([0-5][0-9])$/', $text)) {
            $this->telegram->sendMessage($profile->telegram_chat_id, "❌ Format waktu tidak valid. Gunakan format HH:MM\nContoh: <code>08:00</code>");
            return response()->json(['ok' => true]);
        }

        $data = $profile->state_data ?? [];
        $tipe = $data['tipe'] ?? 'custom';

        $reminderMessages = [
            'minum' => ['judul' => 'Waktunya Minum Air! 💧', 'pesan' => 'Jangan lupa minum air putih ya! Target 2.5L per hari.'],
            'makan' => ['judul' => 'Waktunya Makan! 🍽', 'pesan' => 'Jangan skip makan ya! Pilih makanan sehat dan bergizi.'],
            'olahraga' => ['judul' => 'Waktunya Olahraga! 🏃', 'pesan' => 'Yuk gerak! Minimal 30 menit aktivitas fisik.'],
            'tidur' => ['judul' => 'Waktunya Tidur! 😴', 'pesan' => 'Istirahat yang cukup penting untuk recovery. Target 7-8 jam.'],
        ];

        $msg = $reminderMessages[$tipe] ?? ['judul' => 'Pengingat', 'pesan' => 'Jangan lupa!'];

        $profile->reminders()->create([
            'tipe' => $tipe,
            'judul' => $msg['judul'],
            'pesan' => $msg['pesan'],
            'waktu' => $text . ':00',
            'hari_aktif' => [1, 2, 3, 4, 5, 6, 7],
            'aktif' => true,
        ]);

        $profile->update(['state' => null, 'state_data' => null]);

        $this->telegram->sendMessage($profile->telegram_chat_id,
            "✅ Pengingat <b>{$msg['judul']}</b> berhasil diatur!\n⏰ Setiap hari jam {$text}");

        return response()->json(['ok' => true]);
    }

    // ==========================================
    // PHOTO HANDLER
    // ==========================================

    private function handlePhoto(UserProfile $profile, array $message): \Illuminate\Http\JsonResponse
    {
        $this->telegram->sendChatAction($profile->telegram_chat_id, 'typing');

        // Get largest photo
        $photos = $message['photo'];
        $photo = end($photos);
        $fileId = $photo['file_id'];

        // Get file URL
        $fileUrl = $this->telegram->getFileUrl($fileId);
        if (!$fileUrl) {
            $this->telegram->sendMessage($profile->telegram_chat_id, "❌ Gagal mengambil foto. Coba lagi.");
            return response()->json(['ok' => true]);
        }

        // Analyze with AI
        $result = $this->ai->estimateFromImage($fileUrl, $profile->id);

        if (!$result['success']) {
            $this->telegram->sendMessage($profile->telegram_chat_id,
                "❌ Gagal menganalisis foto.\n" . ($result['error'] ?? 'Coba lagi nanti.'));
            return response()->json(['ok' => true]);
        }

        $data = $result['data'];

        // Handle multiple items
        if (isset($data['items']) && is_array($data['items'])) {
            $text = "📸 <b>Hasil Analisis Foto</b>\n";
            $text .= "━━━━━━━━━━━━━━━\n\n";

            if (isset($data['deskripsi'])) {
                $text .= "📝 {$data['deskripsi']}\n\n";
            }

            $today = now('Asia/Singapore')->toDateString();
            $waktuMakan = $this->detectMealTime();

            foreach ($data['items'] as $item) {
                FoodLog::create([
                    'profile_id' => $profile->id,
                    'tanggal' => $today,
                    'waktu_makan' => $waktuMakan,
                    'nama_makanan' => $item['nama'] ?? 'Makanan',
                    'porsi' => $item['porsi'] ?? 1,
                    'satuan_porsi' => $item['satuan_porsi'] ?? 'porsi',
                    'kalori' => $item['kalori'] ?? 0,
                    'protein' => $item['protein'] ?? 0,
                    'karbohidrat' => $item['karbohidrat'] ?? 0,
                    'lemak' => $item['lemak'] ?? 0,
                    'foto_url' => $fileUrl,
                    'sumber' => 'foto',
                ]);

                $text .= "• <b>{$item['nama']}</b>\n";
                $text .= "  {$item['kalori']} kkal | P:{$item['protein']}g K:{$item['karbohidrat']}g L:{$item['lemak']}g\n";
            }

            $text .= "\n━━━━━━━━━━━━━━━\n";
            $text .= "📊 <b>Total: " . ($data['total_kalori'] ?? array_sum(array_column($data['items'], 'kalori'))) . " kkal</b>\n";
            $text .= "✅ Tercatat sebagai " . $this->getMealLabel($waktuMakan);

            // Update streak & summary
            $this->updateStreak($profile);
            DailySummary::recalculate($profile->id, $today);

            $keyboard = [
                [
                    ['text' => '📊 Dashboard', 'callback_data' => 'dashboard'],
                    ['text' => '🗑 Hapus', 'callback_data' => 'delete_last_food'],
                ],
            ];

            $this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id, $text, $keyboard);
        } else {
            // Single item from photo
            $this->telegram->sendMessage($profile->telegram_chat_id,
                "❌ Tidak dapat mengenali makanan dari foto. Coba foto lebih jelas atau ketik manual.");
        }

        return response()->json(['ok' => true]);
    }

    // ==========================================
    // NATURAL TEXT HANDLER
    // ==========================================

    private function handleNaturalText(UserProfile $profile, string $text): \Illuminate\Http\JsonResponse
    {
        // Try to detect if it's a food name
        $text = trim($text);
        if (empty($text)) {
            return response()->json(['ok' => true]);
        }

        // Check if it looks like food (not a random message)
        $foodKeywords = ['nasi', 'mie', 'ayam', 'ikan', 'sayur', 'buah', 'roti', 'telur',
            'tahu', 'tempe', 'bakso', 'soto', 'goreng', 'bakar', 'rebus', 'kukus',
            'makan', 'minum', 'kopi', 'teh', 'susu', 'jus', 'air', 'es ',
            'rendang', 'sate', 'gado', 'pecel', 'rawon', 'gudeg', 'pizza', 'burger',
            'indomie', 'porsi', 'mangkuk', 'potong', 'gelas', 'piring'];

        $isFood = false;
        $lowerText = strtolower($text);
        foreach ($foodKeywords as $keyword) {
            if (str_contains($lowerText, $keyword)) {
                $isFood = true;
                break;
            }
        }

        // Also check food database
        if (!$isFood) {
            $dbCheck = FoodDatabase::where('nama', 'like', "%{$text}%")->exists();
            if ($dbCheck) $isFood = true;
        }

        if ($isFood) {
            return $this->logFood($profile, $text);
        }

        // Not recognized as food
        $this->telegram->sendMessage($profile->telegram_chat_id,
            "🤔 Aku tidak yakin itu makanan.\n\nJika ini makanan, ketik: <code>/makan {$text}</code>\nAtau ketik /menu untuk menu utama.");

        return response()->json(['ok' => true]);
    }

    // ==========================================
    // CALLBACK HANDLER
    // ==========================================

    private function handleCallback(array $callback): \Illuminate\Http\JsonResponse
    {
        $chatId = (string) $callback['message']['chat']['id'];
        $callbackId = $callback['id'];
        $data = $callback['data'];

        $profile = UserProfile::findByChatId($chatId);
        if (!$profile) {
            $this->telegram->answerCallback($callbackId, 'Profil tidak ditemukan');
            return response()->json(['ok' => true]);
        }

        $this->telegram->answerCallback($callbackId);

        // Route callbacks
        return match (true) {
            $data === 'dashboard' => $this->cmdDashboard($profile),
            $data === 'menu' => $this->cmdMenu($profile),
            $data === 'log_food' => $this->cmdMakan($profile, ''),
            $data === 'log_water' => $this->cmdAir($profile, ''),
            $data === 'log_weight' => $this->cmdBerat($profile, ''),
            $data === 'log_exercise' => $this->cmdOlahraga($profile, ''),
            $data === 'target' => $this->cmdTarget($profile),
            $data === 'stats' => $this->cmdStats($profile),
            $data === 'badges' => $this->cmdBadge($profile),
            $data === 'recommend' => $this->cmdRekomendasi($profile),
            $data === 'profile' => $this->cmdProfil($profile),
            $data === 'history' => $this->cmdRiwayat($profile),
            $data === 'help' => $this->cmdHelp($profile),
            $data === 'edit_profile' => $this->cmdSetup($profile),
            $data === 'reminder_menu' => $this->cmdReminder($profile),
            $data === 'delete_last_food' => $this->cmdHapus($profile, 'terakhir'),
            str_starts_with($data, 'water_') => $this->callbackWater($profile, $data),
            str_starts_with($data, 'meal_') => $this->callbackMealTime($profile, $data),
            str_starts_with($data, 'setup_gender_') => $this->callbackSetupGender($profile, $data),
            str_starts_with($data, 'setup_activity_') => $this->callbackSetupActivity($profile, $data),
            str_starts_with($data, 'exercise_') => $this->callbackExercise($profile, $data),
            str_starts_with($data, 'reminder_') => $this->callbackReminder($profile, $data),
            $data === 'recalculate' => $this->callbackRecalculate($profile),
            $data === 'analyze_weekly' => $this->callbackAnalyzeWeekly($profile),
            default => response()->json(['ok' => true]),
        };
    }

    private function callbackWater(UserProfile $profile, string $data): \Illuminate\Http\JsonResponse
    {
        $ml = (int) str_replace('water_', '', $data);
        return $this->logWater($profile, $ml);
    }

    private function callbackMealTime(UserProfile $profile, string $data): \Illuminate\Http\JsonResponse
    {
        $mealMap = [
            'meal_sarapan' => 'sarapan',
            'meal_siang' => 'makan_siang',
            'meal_malam' => 'makan_malam',
            'meal_snack' => 'snack',
        ];

        $waktu = $mealMap[$data] ?? 'snack';
        $stateData = $profile->state_data ?? [];
        $stateData['waktu_makan'] = $waktu;
        $profile->update(['state' => 'waiting_food', 'state_data' => $stateData]);

        $label = $this->getMealLabel($waktu);
        $this->telegram->sendMessage($profile->telegram_chat_id,
            "✅ Waktu makan: <b>{$label}</b>\n\nSekarang ketik nama makanan atau kirim foto:");

        return response()->json(['ok' => true]);
    }

    private function callbackSetupGender(UserProfile $profile, string $data): \Illuminate\Http\JsonResponse
    {
        $gender = str_replace('setup_gender_', '', $data);
        $stateData = $profile->state_data ?? [];
        $stateData['gender'] = $gender;
        $profile->update(['state' => 'setup_umur', 'state_data' => $stateData]);

        $icon = $gender === 'pria' ? '👨' : '👩';
        $this->telegram->sendMessage($profile->telegram_chat_id,
            "✅ Gender: {$icon} " . ucfirst($gender) . "\n\n🎂 Berapa umur kamu?\nContoh: <code>25</code>");

        return response()->json(['ok' => true]);
    }

    private function callbackSetupActivity(UserProfile $profile, string $data): \Illuminate\Http\JsonResponse
    {
        $activity = str_replace('setup_activity_', '', $data);
        $stateData = $profile->state_data ?? [];

        // Save all profile data
        $profile->update([
            'gender' => $stateData['gender'] ?? $profile->gender,
            'umur' => $stateData['umur'] ?? $profile->umur,
            'tinggi_cm' => $stateData['tinggi'] ?? $profile->tinggi_cm,
            'berat_kg' => $stateData['berat'] ?? $profile->berat_kg,
            'berat_target' => $stateData['target'] ?? $profile->berat_target,
            'goal' => $stateData['goal'] ?? $profile->goal,
            'level_aktivitas' => $activity,
            'state' => null,
            'state_data' => null,
        ]);

        // Recalculate all metrics
        $profile->refresh();
        $profile->recalculate();

        $text = "🎉 <b>Setup Selesai!</b>\n";
        $text .= "━━━━━━━━━━━━━━━\n\n";
        $text .= "📊 <b>Hasil Kalkulasi:</b>\n";
        $text .= "   BMR: <b>" . round($profile->bmr) . "</b> kkal/hari\n";
        $text .= "   TDEE: <b>" . round($profile->tdee) . "</b> kkal/hari\n";
        $text .= "   BMI: <b>{$profile->bmi}</b> (" . $this->getBmiCategory($profile->bmi) . ")\n";
        $text .= "   Body Fat: <b>{$profile->body_fat_pct}%</b>\n\n";
        $text .= "🎯 <b>Target Harian:</b>\n";
        $text .= "   Kalori: <b>{$profile->kalori_target}</b> kkal\n";
        $text .= "   Protein: <b>{$profile->protein_target}</b>g\n";
        $text .= "   Karbo: <b>{$profile->karbo_target}</b>g\n";
        $text .= "   Lemak: <b>{$profile->lemak_target}</b>g\n\n";
        $text .= "Goal: <b>" . ucfirst($profile->goal) . "</b>\n\n";
        $text .= "✅ Kamu siap mulai tracking! Ketik /menu untuk mulai.";

        // Create streak record
        Streak::firstOrCreate(['profile_id' => $profile->id]);

        // Award first setup badge
        Badge::firstOrCreate(
            ['profile_id' => $profile->id, 'badge_code' => 'first_setup'],
            ['badge_name' => 'Langkah Pertama!', 'badge_icon' => '🎯', 'deskripsi' => 'Berhasil setup profil pertama kali', 'earned_at' => now()]
        );

        $this->telegram->sendMessage($profile->telegram_chat_id, $text);
        return response()->json(['ok' => true]);
    }

    private function callbackExercise(UserProfile $profile, string $data): \Illuminate\Http\JsonResponse
    {
        $jenis = str_replace('exercise_', '', $data);
        $jenisMap = [
            'lari' => 'Lari',
            'jalan' => 'Jalan Kaki',
            'gym' => 'Gym/Angkat Beban',
            'renang' => 'Renang',
            'sepeda' => 'Bersepeda',
            'yoga' => 'Yoga',
        ];

        $namaJenis = $jenisMap[$jenis] ?? ucfirst($jenis);
        $profile->update([
            'state' => 'waiting_exercise_duration',
            'state_data' => ['jenis' => $namaJenis],
        ]);

        $this->telegram->sendMessage($profile->telegram_chat_id,
            "🏃 Olahraga: <b>{$namaJenis}</b>\n\nBerapa menit durasinya?\nContoh: <code>30</code>");

        return response()->json(['ok' => true]);
    }

    private function callbackReminder(UserProfile $profile, string $data): \Illuminate\Http\JsonResponse
    {
        $action = str_replace('reminder_', '', $data);

        if ($action === 'list') {
            $reminders = $profile->reminders()->where('aktif', true)->get();

            $text = "⏰ <b>Pengingat Aktif</b>\n━━━━━━━━━━━━━━━\n\n";
            if ($reminders->isEmpty()) {
                $text .= "Belum ada pengingat aktif.\n";
            } else {
                foreach ($reminders as $r) {
                    $status = $r->aktif ? '✅' : '❌';
                    $text .= "{$status} <b>{$r->judul}</b>\n";
                    $text .= "   ⏰ {$r->waktu}\n\n";
                }
            }
            $this->telegram->sendMessage($profile->telegram_chat_id, $text);
            return response()->json(['ok' => true]);
        }

        if ($action === 'clear') {
            $profile->reminders()->delete();
            $this->telegram->sendMessage($profile->telegram_chat_id, "🗑 Semua pengingat dihapus.");
            return response()->json(['ok' => true]);
        }

        // Set reminder type
        $tipeMap = ['water' => 'minum', 'meal' => 'makan', 'exercise' => 'olahraga', 'sleep' => 'tidur'];
        $tipe = $tipeMap[$action] ?? 'custom';

        $profile->update([
            'state' => 'reminder_time',
            'state_data' => ['tipe' => $tipe],
        ]);

        $suggestions = match ($tipe) {
            'minum' => "Saran: 08:00, 10:00, 12:00, 14:00, 16:00, 18:00, 20:00",
            'makan' => "Saran: 07:00 (sarapan), 12:00 (siang), 19:00 (malam)",
            'olahraga' => "Saran: 06:00, 17:00",
            'tidur' => "Saran: 22:00, 23:00",
            default => "",
        };

        $this->telegram->sendMessage($profile->telegram_chat_id,
            "⏰ Masukkan jam pengingat (format HH:MM):\n{$suggestions}\n\nContoh: <code>08:00</code>");

        return response()->json(['ok' => true]);
    }

    private function callbackRecalculate(UserProfile $profile): \Illuminate\Http\JsonResponse
    {
        $profile->recalculate();
        $this->telegram->sendMessage($profile->telegram_chat_id,
            "✅ Profil dihitung ulang!\n\nBMI: {$profile->bmi}\nTarget kalori: {$profile->kalori_target} kkal");
        return response()->json(['ok' => true]);
    }

    private function callbackAnalyzeWeekly(UserProfile $profile): \Illuminate\Http\JsonResponse
    {
        $this->telegram->sendChatAction($profile->telegram_chat_id, 'typing');

        $weekStart = now('Asia/Singapore')->startOfWeek();
        $summaries = DailySummary::where('profile_id', $profile->id)
            ->where('tanggal', '>=', $weekStart->toDateString())
            ->get();

        if ($summaries->isEmpty()) {
            $this->telegram->sendMessage($profile->telegram_chat_id, "❌ Belum ada data minggu ini untuk dianalisis.");
            return response()->json(['ok' => true]);
        }

        $weeklyData = [
            'target_kalori' => $profile->kalori_target,
            'goal' => $profile->goal,
            'days' => $summaries->map(fn($s) => [
                'tanggal' => $s->tanggal->format('d/m'),
                'kalori' => $s->total_kalori,
                'protein' => $s->total_protein,
                'air_ml' => $s->total_air_ml,
                'exercise_menit' => $s->total_exercise_menit,
            ])->toArray(),
        ];

        $result = $this->ai->analyzeWeeklyPattern($weeklyData, $profile->id);

        if (!$result['success']) {
            $this->telegram->sendMessage($profile->telegram_chat_id, "❌ AI tidak tersedia saat ini. Coba lagi nanti.");
            return response()->json(['ok' => true]);
        }

        $data = $result['data'];
        $text = "🧠 <b>Analisis AI Mingguan</b>\n";
        $text .= "━━━━━━━━━━━━━━━\n\n";

        if (isset($data['emoji_mood'])) {
            $text .= "{$data['emoji_mood']} ";
        }
        if (isset($data['analisis'])) {
            $text .= "{$data['analisis']}\n\n";
        }

        if (isset($data['skor_kesehatan'])) {
            $text .= "📊 Skor Kesehatan: <b>{$data['skor_kesehatan']}/10</b>\n\n";
        }

        if (isset($data['kelebihan']) && !empty($data['kelebihan'])) {
            $text .= "✅ <b>Kelebihan:</b>\n";
            foreach ($data['kelebihan'] as $item) {
                $text .= "  • {$item}\n";
            }
            $text .= "\n";
        }

        if (isset($data['kekurangan']) && !empty($data['kekurangan'])) {
            $text .= "⚠️ <b>Perlu Diperbaiki:</b>\n";
            foreach ($data['kekurangan'] as $item) {
                $text .= "  • {$item}\n";
            }
            $text .= "\n";
        }

        if (isset($data['saran']) && !empty($data['saran'])) {
            $text .= "💡 <b>Saran:</b>\n";
            foreach ($data['saran'] as $item) {
                $text .= "  • {$item}\n";
            }
        }

        $this->telegram->sendMessage($profile->telegram_chat_id, $text);
        return response()->json(['ok' => true]);
    }

    // ==========================================
    // LOGGING FUNCTIONS
    // ==========================================

    private function logFood(UserProfile $profile, string $foodText): \Illuminate\Http\JsonResponse
    {
        $this->telegram->sendChatAction($profile->telegram_chat_id, 'typing');

        $today = now('Asia/Singapore')->toDateString();
        $stateData = $profile->state_data ?? [];
        $waktuMakan = $stateData['waktu_makan'] ?? $this->detectMealTime();

        // Try AI estimation
        $result = $this->ai->estimateFromText($foodText, $profile->id);

        if (!$result['success']) {
            // Fallback: try to find in database with fuzzy match
            $dbResult = FoodDatabase::where('nama', 'like', "%{$foodText}%")->first();
            if ($dbResult) {
                $result = [
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
            } else {
                $this->telegram->sendMessage($profile->telegram_chat_id,
                    "❌ Gagal mengenali makanan: <b>{$foodText}</b>\n\nCoba ketik lebih spesifik atau kirim foto.");
                return response()->json(['ok' => true]);
            }
        }

        $data = $result['data'];
        $nama = $data['nama'] ?? $foodText;
        $kalori = (int) ($data['kalori'] ?? 0);
        $protein = (float) ($data['protein'] ?? 0);
        $karbo = (float) ($data['karbohidrat'] ?? 0);
        $lemak = (float) ($data['lemak'] ?? 0);
        $porsi = (float) ($data['porsi'] ?? 1);
        $satuanPorsi = $data['satuan_porsi'] ?? 'porsi';

        // Save to database
        FoodLog::create([
            'profile_id' => $profile->id,
            'tanggal' => $today,
            'waktu_makan' => $waktuMakan,
            'nama_makanan' => $nama,
            'porsi' => $porsi,
            'satuan_porsi' => $satuanPorsi,
            'kalori' => $kalori,
            'protein' => $protein,
            'karbohidrat' => $karbo,
            'lemak' => $lemak,
            'sumber' => $result['source'] === 'database' ? 'database' : 'manual',
        ]);

        // Clear state
        $profile->update(['state' => null, 'state_data' => null]);

        // Update streak
        $newBadges = $this->updateStreak($profile);

        // Recalculate daily summary
        $summary = DailySummary::recalculate($profile->id, $today);

        // Build response
        $targetKalori = $profile->kalori_target ?: 2000;
        $sisaKalori = $targetKalori - $summary->total_kalori;
        $pct = min(100, round(($summary->total_kalori / $targetKalori) * 100));

        $source = $result['source'] === 'database' ? '📚' : '🤖';
        $text = "✅ <b>Tercatat!</b> {$source}\n\n";
        $text .= "🍽 <b>{$nama}</b>\n";
        $text .= "   📊 {$kalori} kkal | P:{$protein}g K:{$karbo}g L:{$lemak}g\n";
        $text .= "   ⏰ " . $this->getMealLabel($waktuMakan) . "\n\n";

        $text .= "📊 <b>Progress Hari Ini:</b>\n";
        $text .= $this->progressBar($pct) . " {$pct}%\n";
        $text .= "   {$summary->total_kalori} / {$targetKalori} kkal (sisa: {$sisaKalori})\n";

        // Warning if over
        if ($sisaKalori < 0) {
            $text .= "\n⚠️ <b>Kamu sudah melebihi target!</b> Over " . abs($sisaKalori) . " kkal\n";
        } elseif ($sisaKalori < 200) {
            $text .= "\n💡 Sisa kalori tinggal sedikit. Pilih snack ringan jika masih lapar.\n";
        }

        // New badges
        if (!empty($newBadges)) {
            $text .= "\n🎉 <b>Badge Baru!</b>\n";
            foreach ($newBadges as $badge) {
                $text .= "{$badge->badge_icon} {$badge->badge_name}\n";
            }
        }

        $keyboard = [
            [
                ['text' => '🍽 + Lagi', 'callback_data' => 'log_food'],
                ['text' => '📊 Dashboard', 'callback_data' => 'dashboard'],
            ],
            [
                ['text' => '🗑 Hapus', 'callback_data' => 'delete_last_food'],
            ],
        ];

        $this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id, $text, $keyboard);
        return response()->json(['ok' => true]);
    }

    private function logWater(UserProfile $profile, int $ml): \Illuminate\Http\JsonResponse
    {
        $today = now('Asia/Singapore')->toDateString();

        WaterLog::create([
            'profile_id' => $profile->id,
            'tanggal' => $today,
            'jumlah_ml' => $ml,
            'waktu' => now('Asia/Singapore')->format('H:i:s'),
        ]);

        $totalToday = WaterLog::where('profile_id', $profile->id)
            ->whereDate('tanggal', $today)->sum('jumlah_ml');

        $target = 2500;
        $pct = min(100, round(($totalToday / $target) * 100));

        $text = "💧 +{$ml}ml tercatat!\n\n";
        $text .= "Hari ini: <b>{$totalToday}ml</b> / {$target}ml ({$pct}%)\n";
        $text .= $this->progressBar($pct);

        if ($pct >= 100) {
            $text .= "\n\n🎉 Target air tercapai! Bagus!";

            // Check water badge
            Badge::firstOrCreate(
                ['profile_id' => $profile->id, 'badge_code' => 'water_champion'],
                ['badge_name' => 'Water Champion!', 'badge_icon' => '💧', 'deskripsi' => 'Pertama kali mencapai target air harian', 'earned_at' => now()]
            );
        }

        // Update summary
        DailySummary::recalculate($profile->id, $today);

        $keyboard = [
            [
                ['text' => '💧 +250ml', 'callback_data' => 'water_250'],
                ['text' => '💧 +500ml', 'callback_data' => 'water_500'],
            ],
        ];

        $this->telegram->sendMessageWithKeyboard($profile->telegram_chat_id, $text, $keyboard);
        return response()->json(['ok' => true]);
    }

    private function logWeight(UserProfile $profile, float $berat): \Illuminate\Http\JsonResponse
    {
        if ($berat < 20 || $berat > 300) {
            $this->telegram->sendMessage($profile->telegram_chat_id, "❌ Berat tidak valid (20-300 kg).");
            return response()->json(['ok' => true]);
        }

        $today = now('Asia/Singapore')->toDateString();

        // Get previous weight
        $lastWeight = WeightLog::where('profile_id', $profile->id)
            ->orderByDesc('tanggal')->first();

        // Calculate BMI
        $bmi = $profile->tinggi_cm ? round($berat / (($profile->tinggi_cm / 100) ** 2), 1) : null;

        // Save
        WeightLog::updateOrCreate(
            ['profile_id' => $profile->id, 'tanggal' => $today],
            ['berat_kg' => $berat, 'bmi' => $bmi]
        );

        // Update profile
        $profile->update(['berat_kg' => $berat, 'bmi' => $bmi, 'state' => null, 'state_data' => null]);
        $profile->recalculate();

        $text = "⚖️ <b>Berat Tercatat!</b>\n\n";
        $text .= "📊 Berat: <b>{$berat} kg</b>\n";

        if ($bmi) {
            $text .= "📊 BMI: <b>{$bmi}</b> (" . $this->getBmiCategory($bmi) . ")\n";
        }

        if ($lastWeight) {
            $diff = round($berat - $lastWeight->berat_kg, 1);
            $arrow = $diff > 0 ? '📈 +' : ($diff < 0 ? '📉 ' : '➡️ ');
            $text .= "\n{$arrow}{$diff} kg dari terakhir ({$lastWeight->tanggal->format('d/m')})\n";
        }

        if ($profile->berat_target) {
            $remaining = round($berat - $profile->berat_target, 1);
            if ($remaining > 0 && $profile->goal === 'cutting') {
                $text .= "\n🎯 Menuju target: masih {$remaining} kg lagi!";
            } elseif ($remaining < 0 && $profile->goal === 'bulking') {
                $text .= "\n🎯 Menuju target: masih " . abs($remaining) . " kg lagi!";
            } elseif (abs($remaining) < 0.5) {
                $text .= "\n🎉 <b>TARGET TERCAPAI!</b> Selamat!";

                Badge::firstOrCreate(
                    ['profile_id' => $profile->id, 'badge_code' => 'weight_goal'],
                    ['badge_name' => 'Goal Achieved!', 'badge_icon' => '🎯', 'deskripsi' => 'Berhasil mencapai target berat badan', 'earned_at' => now()]
                );
            }
        }

        $this->telegram->sendMessage($profile->telegram_chat_id, $text);
        return response()->json(['ok' => true]);
    }

    private function logExercise(UserProfile $profile, string $text): \Illuminate\Http\JsonResponse
    {
        // Parse exercise text: "lari 30 menit" or "gym 60"
        preg_match('/(.+?)\s+(\d+)\s*(menit|min|m)?/i', $text, $matches);

        if (empty($matches)) {
            // Just got exercise name, ask for duration
            $profile->update([
                'state' => 'waiting_exercise_duration',
                'state_data' => ['jenis' => $text],
            ]);
            $this->telegram->sendMessage($profile->telegram_chat_id,
                "🏃 Olahraga: <b>{$text}</b>\n\nBerapa menit durasinya?");
            return response()->json(['ok' => true]);
        }

        $jenis = trim($matches[1]);
        $durasi = (int) $matches[2];

        $profile->update(['state' => null, 'state_data' => null]);
        return $this->saveExercise($profile, $jenis, $durasi);
    }

    private function saveExercise(UserProfile $profile, string $jenis, int $durasi): \Illuminate\Http\JsonResponse
    {
        // Estimate calories burned
        $calPerMin = match (strtolower($jenis)) {
            'lari', 'jogging', 'running' => 10,
            'jalan', 'jalan kaki', 'walking' => 5,
            'gym', 'angkat beban', 'weight training' => 7,
            'renang', 'swimming' => 9,
            'sepeda', 'bersepeda', 'cycling' => 8,
            'yoga', 'stretching' => 4,
            'hiit', 'crossfit' => 12,
            'badminton', 'tenis' => 8,
            'futsal', 'sepak bola' => 9,
            'basket' => 8,
            default => 6,
        };

        $kaloriTerbakar = $calPerMin * $durasi;
        $today = now('Asia/Singapore')->toDateString();

        ExerciseLog::create([
            'profile_id' => $profile->id,
            'tanggal' => $today,
            'jenis_olahraga' => ucfirst($jenis),
            'durasi_menit' => $durasi,
            'kalori_terbakar' => $kaloriTerbakar,
            'intensitas' => $calPerMin >= 9 ? 'berat' : ($calPerMin >= 6 ? 'sedang' : 'ringan'),
        ]);

        // Update streak
        $this->updateStreak($profile);
        DailySummary::recalculate($profile->id, $today);

        $text = "🏃 <b>Olahraga Tercatat!</b>\n\n";
        $text .= "🏋️ {$jenis}\n";
        $text .= "⏱ Durasi: {$durasi} menit\n";
        $text .= "🔥 Kalori terbakar: ~{$kaloriTerbakar} kkal\n\n";
        $text .= "💪 Bagus! Terus semangat!";

        // Check exercise badge
        $totalExercise = ExerciseLog::where('profile_id', $profile->id)->count();
        if ($totalExercise === 1) {
            Badge::firstOrCreate(
                ['profile_id' => $profile->id, 'badge_code' => 'first_exercise'],
                ['badge_name' => 'First Workout!', 'badge_icon' => '💪', 'deskripsi' => 'Pertama kali mencatat olahraga', 'earned_at' => now()]
            );
            $text .= "\n\n🎉 Badge baru: 💪 First Workout!";
        }

        $this->telegram->sendMessage($profile->telegram_chat_id, $text);
        return response()->json(['ok' => true]);
    }

    // ==========================================
    // HELPER FUNCTIONS
    // ==========================================

    private function updateStreak(UserProfile $profile): array
    {
        $streak = Streak::firstOrCreate(['profile_id' => $profile->id]);
        return $streak->recordActivity();
    }

    private function detectMealTime(): string
    {
        $hour = (int) now('Asia/Singapore')->format('H');

        if ($hour >= 5 && $hour < 10) return 'sarapan';
        if ($hour >= 10 && $hour < 15) return 'makan_siang';
        if ($hour >= 15 && $hour < 18) return 'snack';
        return 'makan_malam';
    }

    private function getMealLabel(string $waktu): string
    {
        return match ($waktu) {
            'sarapan' => '🌅 Sarapan',
            'makan_siang' => '☀️ Makan Siang',
            'makan_malam' => '🌙 Makan Malam',
            'snack' => '🍪 Snack',
            default => $waktu,
        };
    }

    private function getBmiCategory(float $bmi): string
    {
        if ($bmi < 18.5) return 'Underweight';
        if ($bmi < 25) return 'Normal';
        if ($bmi < 30) return 'Overweight';
        return 'Obese';
    }

    private function progressBar(int $percentage, int $length = 20): string
    {
        $filled = (int) round($percentage / 100 * $length);
        $empty = $length - $filled;

        $bar = str_repeat('█', min($filled, $length));
        $bar .= str_repeat('░', max(0, $empty));

        return $bar;
    }

    private function miniBar(float $percentage): string
    {
        $filled = (int) round(min(100, $percentage) / 100 * 8);
        $bar = str_repeat('▓', $filled) . str_repeat('░', 8 - $filled);

        if ($percentage > 100) return $bar . ' ⚠️';
        if ($percentage >= 80) return $bar . ' ✅';
        return $bar;
    }
}
