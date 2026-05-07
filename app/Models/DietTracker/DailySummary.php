<?php

namespace App\Models\DietTracker;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailySummary extends Model
{
    protected $table = 'diet_daily_summaries';

    protected $fillable = [
        'profile_id', 'tanggal', 'total_kalori', 'total_protein',
        'total_karbo', 'total_lemak', 'total_air_ml',
        'total_exercise_menit', 'total_kalori_terbakar',
        'sisa_kalori', 'pct_target',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(UserProfile::class, 'profile_id');
    }

    public static function recalculate(int $profileId, string $tanggal): self
    {
        $profile = UserProfile::findOrFail($profileId);
        $dateStr = \Carbon\Carbon::parse($tanggal)->toDateString();

        $foodLogs = FoodLog::where('profile_id', $profileId)
            ->whereDate('tanggal', $dateStr)->get();

        $waterLogs = WaterLog::where('profile_id', $profileId)
            ->whereDate('tanggal', $dateStr)->get();

        $exerciseLogs = ExerciseLog::where('profile_id', $profileId)
            ->whereDate('tanggal', $dateStr)->get();

        $totalKalori = $foodLogs->sum('kalori');
        $targetKalori = $profile->kalori_target ?: 2000;

        // Use manual find + update/create to avoid date format issues
        $existing = static::where('profile_id', $profileId)
            ->whereDate('tanggal', $dateStr)->first();

        $attrs = [
            'total_kalori' => $totalKalori,
            'total_protein' => $foodLogs->sum('protein'),
            'total_karbo' => $foodLogs->sum('karbohidrat'),
            'total_lemak' => $foodLogs->sum('lemak'),
            'total_air_ml' => $waterLogs->sum('jumlah_ml'),
            'total_exercise_menit' => $exerciseLogs->sum('durasi_menit'),
            'total_kalori_terbakar' => $exerciseLogs->sum('kalori_terbakar'),
            'sisa_kalori' => $targetKalori - $totalKalori + $exerciseLogs->sum('kalori_terbakar'),
            'pct_target' => $targetKalori > 0 ? round(($totalKalori / $targetKalori) * 100, 1) : 0,
        ];

        if ($existing) {
            $existing->update($attrs);
            return $existing;
        }

        return static::create(array_merge(['profile_id' => $profileId, 'tanggal' => $dateStr], $attrs));
    }
}
