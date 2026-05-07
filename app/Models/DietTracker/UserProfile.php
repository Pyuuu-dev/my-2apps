<?php

namespace App\Models\DietTracker;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserProfile extends Model
{
    protected $table = 'diet_user_profiles';

    protected $fillable = [
        'telegram_chat_id', 'nama', 'username', 'gender', 'umur',
        'tinggi_cm', 'berat_kg', 'berat_target', 'level_aktivitas', 'goal',
        'kalori_target', 'protein_target', 'karbo_target', 'lemak_target',
        'air_target_ml', 'bmr', 'tdee', 'bmi', 'body_fat_pct', 'timezone', 'aktif',
        'state', 'state_data', 'ai_requests_today', 'ai_requests_date',
        'max_ai_requests', 'proactive_nudge', 'fasting_type', 'fasting_active',
    ];

    protected $casts = [
        'state_data' => 'array',
        'aktif' => 'boolean',
        'proactive_nudge' => 'boolean',
        'fasting_active' => 'boolean',
        'berat_kg' => 'float',
        'berat_target' => 'float',
        'tinggi_cm' => 'float',
        'bmr' => 'float',
        'tdee' => 'float',
        'bmi' => 'float',
        'body_fat_pct' => 'float',
        'ai_requests_date' => 'date',
    ];

    public static function findByChatId(string $chatId): ?self
    {
        return static::where('telegram_chat_id', $chatId)->first();
    }

    public static function findOrCreateByChatId(string $chatId, array $data = []): self
    {
        return static::firstOrCreate(
            ['telegram_chat_id' => $chatId],
            array_merge(['aktif' => true], $data)
        );
    }

    public function foodLogs(): HasMany
    {
        return $this->hasMany(FoodLog::class, 'profile_id');
    }

    public function weightLogs(): HasMany
    {
        return $this->hasMany(WeightLog::class, 'profile_id');
    }

    public function waterLogs(): HasMany
    {
        return $this->hasMany(WaterLog::class, 'profile_id');
    }

    public function exerciseLogs(): HasMany
    {
        return $this->hasMany(ExerciseLog::class, 'profile_id');
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class, 'profile_id');
    }

    public function streak(): HasOne
    {
        return $this->hasOne(Streak::class, 'profile_id');
    }

    public function badges(): HasMany
    {
        return $this->hasMany(Badge::class, 'profile_id');
    }

    public function dailySummaries(): HasMany
    {
        return $this->hasMany(DailySummary::class, 'profile_id');
    }

    public function foodFavorites(): HasMany
    {
        return $this->hasMany(FoodFavorite::class, 'profile_id');
    }

    public function fastingLogs(): HasMany
    {
        return $this->hasMany(FastingLog::class, 'profile_id');
    }

    public function sleepLogs(): HasMany
    {
        return $this->hasMany(SleepLog::class, 'profile_id');
    }

    // === HELPER METHODS ===

    public function getAirTarget(): int
    {
        if ($this->air_target_ml) return $this->air_target_ml;
        // 30-35ml per kg berat badan
        if ($this->berat_kg) return (int) round($this->berat_kg * 33);
        return 2500;
    }

    public function canUseAi(): bool
    {
        $today = now('Asia/Singapore')->toDateString();
        if ($this->ai_requests_date?->toDateString() !== $today) {
            $this->update(['ai_requests_today' => 0, 'ai_requests_date' => $today]);
            return true;
        }
        return $this->ai_requests_today < ($this->max_ai_requests ?? 50);
    }

    public function incrementAiUsage(): void
    {
        $today = now('Asia/Singapore')->toDateString();
        if ($this->ai_requests_date?->toDateString() !== $today) {
            $this->update(['ai_requests_today' => 1, 'ai_requests_date' => $today]);
        } else {
            $this->increment('ai_requests_today');
        }
    }

    public function getActiveFasting(): ?FastingLog
    {
        if (!$this->fasting_active) return null;
        return FastingLog::where('profile_id', $this->id)
            ->where('completed', false)
            ->orderByDesc('created_at')
            ->first();
    }

    public function aiLogs(): HasMany
    {
        return $this->hasMany(AiLog::class, 'profile_id');
    }

    // === KALKULASI ===

    public function hitungBMR(): float
    {
        if (!$this->gender || !$this->umur || !$this->tinggi_cm || !$this->berat_kg) {
            return 0;
        }

        // Mifflin-St Jeor
        if ($this->gender === 'pria') {
            return (10 * $this->berat_kg) + (6.25 * $this->tinggi_cm) - (5 * $this->umur) + 5;
        }
        return (10 * $this->berat_kg) + (6.25 * $this->tinggi_cm) - (5 * $this->umur) - 161;
    }

    public function hitungTDEE(): float
    {
        $bmr = $this->hitungBMR();
        $multipliers = [
            'sedentary' => 1.2,
            'light' => 1.375,
            'moderate' => 1.55,
            'active' => 1.725,
            'very_active' => 1.9,
        ];

        return $bmr * ($multipliers[$this->level_aktivitas] ?? 1.55);
    }

    public function hitungBMI(): float
    {
        if (!$this->tinggi_cm || !$this->berat_kg) return 0;
        $tinggiM = $this->tinggi_cm / 100;
        return round($this->berat_kg / ($tinggiM * $tinggiM), 1);
    }

    public function hitungBodyFat(): float
    {
        $bmi = $this->hitungBMI();
        if (!$bmi || !$this->umur || !$this->gender) return 0;

        // Formula Deurenberg
        $genderFactor = $this->gender === 'pria' ? 1 : 0;
        return round((1.20 * $bmi) + (0.23 * $this->umur) - (10.8 * $genderFactor) - 5.4, 1);
    }

    public function hitungKaloriTarget(): int
    {
        $tdee = $this->hitungTDEE();

        return match ($this->goal) {
            'cutting', 'diet' => (int) round($tdee - 500),
            'bulking' => (int) round($tdee + 300),
            'maintenance' => (int) round($tdee),
            default => (int) round($tdee - 500),
        };
    }

    public function hitungMacroTargets(): array
    {
        $kalori = $this->kalori_target ?: $this->hitungKaloriTarget();

        return match ($this->goal) {
            'cutting', 'diet' => [
                'protein' => (int) round($this->berat_kg * 2.2), // 2.2g/kg
                'lemak' => (int) round($kalori * 0.25 / 9),
                'karbo' => (int) round(($kalori - ($this->berat_kg * 2.2 * 4) - ($kalori * 0.25)) / 4),
            ],
            'bulking' => [
                'protein' => (int) round($this->berat_kg * 1.8),
                'lemak' => (int) round($kalori * 0.25 / 9),
                'karbo' => (int) round(($kalori - ($this->berat_kg * 1.8 * 4) - ($kalori * 0.25)) / 4),
            ],
            default => [
                'protein' => (int) round($this->berat_kg * 2.0),
                'lemak' => (int) round($kalori * 0.25 / 9),
                'karbo' => (int) round(($kalori - ($this->berat_kg * 2.0 * 4) - ($kalori * 0.25)) / 4),
            ],
        };
    }

    public function recalculate(): void
    {
        $this->bmr = $this->hitungBMR();
        $this->tdee = $this->hitungTDEE();
        $this->bmi = $this->hitungBMI();
        $this->body_fat_pct = $this->hitungBodyFat();
        $this->kalori_target = $this->hitungKaloriTarget();

        $macros = $this->hitungMacroTargets();
        $this->protein_target = $macros['protein'];
        $this->karbo_target = $macros['karbo'];
        $this->lemak_target = $macros['lemak'];

        $this->save();
    }

    public function getLevel(): array
    {
        $totalDays = $this->streak?->total_days_logged ?? 0;

        if ($totalDays >= 365) return ['level' => 10, 'nama' => 'Legend', 'icon' => '👑'];
        if ($totalDays >= 180) return ['level' => 9, 'nama' => 'Master', 'icon' => '🏆'];
        if ($totalDays >= 90) return ['level' => 8, 'nama' => 'Expert', 'icon' => '💎'];
        if ($totalDays >= 60) return ['level' => 7, 'nama' => 'Pro', 'icon' => '🌟'];
        if ($totalDays >= 45) return ['level' => 6, 'nama' => 'Advanced', 'icon' => '⭐'];
        if ($totalDays >= 30) return ['level' => 5, 'nama' => 'Intermediate', 'icon' => '🔥'];
        if ($totalDays >= 21) return ['level' => 4, 'nama' => 'Committed', 'icon' => '💪'];
        if ($totalDays >= 14) return ['level' => 3, 'nama' => 'Regular', 'icon' => '🎯'];
        if ($totalDays >= 7) return ['level' => 2, 'nama' => 'Starter', 'icon' => '🌱'];
        return ['level' => 1, 'nama' => 'Newbie', 'icon' => '🐣'];
    }
}
