<?php

namespace App\Http\Controllers\DietTracker;

use App\Http\Controllers\Controller;
use App\Models\DietTracker\UserProfile;
use App\Models\DietTracker\FoodLog;
use App\Models\DietTracker\FoodFavorite;
use App\Models\DietTracker\WeightLog;
use App\Models\DietTracker\WaterLog;
use App\Models\DietTracker\ExerciseLog;
use App\Models\DietTracker\FastingLog;
use App\Models\DietTracker\SleepLog;
use App\Models\DietTracker\DailySummary;
use App\Models\DietTracker\AiLog;
use App\Models\DietTracker\Badge;
use App\Models\DietTracker\Streak;
use App\Models\DietTracker\Reminder;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = UserProfile::withCount(['foodLogs', 'weightLogs', 'badges', 'exerciseLogs'])
            ->orderByDesc('updated_at')
            ->get();

        return view('diet.users.index', compact('users'));
    }

    public function show(UserProfile $profile)
    {
        $today = now('Asia/Singapore')->toDateString();

        $todayFood = FoodLog::where('profile_id', $profile->id)->whereDate('tanggal', $today)->get();
        $todayWater = WaterLog::where('profile_id', $profile->id)->whereDate('tanggal', $today)->sum('jumlah_ml');
        $todayExercise = ExerciseLog::where('profile_id', $profile->id)->whereDate('tanggal', $today)->get();

        $recentFood = FoodLog::where('profile_id', $profile->id)
            ->where('tanggal', '>=', now()->subDays(7)->toDateString())
            ->orderByDesc('tanggal')->orderByDesc('created_at')
            ->limit(50)->get();

        $weightHistory = WeightLog::where('profile_id', $profile->id)->orderBy('tanggal')->get();

        $summaries = DailySummary::where('profile_id', $profile->id)
            ->where('tanggal', '>=', now()->subDays(14)->toDateString())
            ->orderBy('tanggal')->get();

        $badges = Badge::where('profile_id', $profile->id)->orderByDesc('earned_at')->get();
        $streak = Streak::where('profile_id', $profile->id)->first();
        $reminders = Reminder::where('profile_id', $profile->id)->get();
        $favorites = FoodFavorite::where('profile_id', $profile->id)->orderByDesc('use_count')->limit(10)->get();
        $sleepLogs = SleepLog::where('profile_id', $profile->id)->orderByDesc('tanggal')->limit(7)->get();

        // Counts
        $counts = [
            'food_logs' => FoodLog::where('profile_id', $profile->id)->count(),
            'weight_logs' => WeightLog::where('profile_id', $profile->id)->count(),
            'water_logs' => WaterLog::where('profile_id', $profile->id)->count(),
            'exercise_logs' => ExerciseLog::where('profile_id', $profile->id)->count(),
            'ai_requests' => AiLog::where('profile_id', $profile->id)->count(),
            'fasting_logs' => FastingLog::where('profile_id', $profile->id)->count(),
            'sleep_logs' => SleepLog::where('profile_id', $profile->id)->count(),
        ];

        return view('diet.users.show', compact(
            'profile', 'todayFood', 'todayWater', 'todayExercise',
            'recentFood', 'weightHistory', 'summaries', 'badges', 'streak',
            'reminders', 'favorites', 'sleepLogs', 'counts', 'today'
        ));
    }

    public function update(Request $request, UserProfile $profile)
    {
        $validated = $request->validate([
            'nama' => 'nullable|string|max:100',
            'gender' => 'nullable|in:pria,wanita',
            'umur' => 'nullable|integer|min:10|max:100',
            'tinggi_cm' => 'nullable|numeric|min:100|max:250',
            'berat_kg' => 'nullable|numeric|min:20|max:300',
            'berat_target' => 'nullable|numeric|min:20|max:300',
            'level_aktivitas' => 'nullable|in:sedentary,light,moderate,active,very_active',
            'goal' => 'nullable|in:cutting,bulking,maintenance,diet',
            'kalori_target' => 'nullable|integer|min:500|max:10000',
            'protein_target' => 'nullable|integer|min:0',
            'karbo_target' => 'nullable|integer|min:0',
            'lemak_target' => 'nullable|integer|min:0',
            'air_target_ml' => 'nullable|integer|min:500|max:10000',
            'max_ai_requests' => 'nullable|integer|min:1|max:500',
            'proactive_nudge' => 'nullable|boolean',
            'aktif' => 'nullable|boolean',
        ]);

        $validated['proactive_nudge'] = $request->has('proactive_nudge');
        $validated['aktif'] = $request->has('aktif');

        $profile->update($validated);

        return back()->with('sukses', "Profil {$profile->nama} berhasil diupdate.");
    }

    public function recalculate(UserProfile $profile)
    {
        $profile->recalculate();
        $profile->update(['air_target_ml' => (int) round($profile->berat_kg * 33)]);

        return back()->with('sukses', "Profil {$profile->nama} dihitung ulang. Target: {$profile->kalori_target} kkal.");
    }

    public function sendMessage(Request $request, UserProfile $profile)
    {
        $request->validate(['message' => 'required|string|max:2000']);

        $telegram = new TelegramService();
        $result = $telegram->sendMessage($profile->telegram_chat_id, $request->message);

        if ($result['ok'] ?? false) {
            return back()->with('sukses', 'Pesan terkirim ke ' . $profile->nama);
        }

        return back()->with('error', 'Gagal kirim pesan: ' . json_encode($result));
    }

    public function resetData(Request $request, UserProfile $profile)
    {
        $type = $request->input('type', 'all');

        switch ($type) {
            case 'food':
                FoodLog::where('profile_id', $profile->id)->delete();
                DailySummary::where('profile_id', $profile->id)->delete();
                break;
            case 'weight':
                WeightLog::where('profile_id', $profile->id)->delete();
                break;
            case 'exercise':
                ExerciseLog::where('profile_id', $profile->id)->delete();
                break;
            case 'water':
                WaterLog::where('profile_id', $profile->id)->delete();
                break;
            case 'badges':
                Badge::where('profile_id', $profile->id)->delete();
                Streak::where('profile_id', $profile->id)->delete();
                break;
            case 'reminders':
                Reminder::where('profile_id', $profile->id)->delete();
                break;
            case 'favorites':
                FoodFavorite::where('profile_id', $profile->id)->delete();
                break;
            case 'all':
                FoodLog::where('profile_id', $profile->id)->delete();
                WeightLog::where('profile_id', $profile->id)->delete();
                WaterLog::where('profile_id', $profile->id)->delete();
                ExerciseLog::where('profile_id', $profile->id)->delete();
                FastingLog::where('profile_id', $profile->id)->delete();
                SleepLog::where('profile_id', $profile->id)->delete();
                DailySummary::where('profile_id', $profile->id)->delete();
                Badge::where('profile_id', $profile->id)->delete();
                Streak::where('profile_id', $profile->id)->delete();
                Reminder::where('profile_id', $profile->id)->delete();
                FoodFavorite::where('profile_id', $profile->id)->delete();
                break;
        }

        return back()->with('sukses', "Data '{$type}' untuk {$profile->nama} berhasil direset.");
    }

    public function destroy(UserProfile $profile)
    {
        $nama = $profile->nama;
        // Cascade delete will handle related records
        $profile->delete();

        return redirect()->route('diet.users.index')->with('sukses', "User '{$nama}' dihapus.");
    }

    public function broadcast(Request $request)
    {
        $request->validate(['message' => 'required|string|max:2000']);

        $telegram = new TelegramService();
        $profiles = UserProfile::where('aktif', true)->get();
        $sent = 0;

        foreach ($profiles as $profile) {
            $result = $telegram->sendMessage($profile->telegram_chat_id, $request->message);
            if ($result['ok'] ?? false) $sent++;
        }

        return back()->with('sukses', "Broadcast terkirim ke {$sent}/{$profiles->count()} user.");
    }
}
