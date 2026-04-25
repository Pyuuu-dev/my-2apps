<?php

namespace App\Models\DietTracker;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DietPlan extends Model
{
    protected $fillable = [
        'nama', 'gender', 'umur', 'tinggi_cm', 'level_aktivitas',
        'tanggal_mulai', 'tanggal_selesai', 'berat_awal',
        'berat_target', 'berat_sekarang', 'kalori_harian_target',
        'bmr', 'tdee', 'catatan', 'status',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function meals(): HasMany
    {
        return $this->hasMany(Meal::class);
    }

    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(DailyActivity::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
    }

    public function weightLogs(): HasMany
    {
        return $this->hasMany(WeightLog::class);
    }

    public function monthlyLogs(): HasMany
    {
        return $this->hasMany(MonthlyLog::class);
    }

    public function waterLogs(): HasMany
    {
        return $this->hasMany(WaterLog::class);
    }

    public function fastingLogs(): HasMany
    {
        return $this->hasMany(FastingLog::class);
    }

    /**
     * Get the active diet plan (always returns the first/only plan)
     */
    public static function getActivePlan(): ?self
    {
        return self::orderBy('id', 'desc')->first();
    }

    /**
     * Check if user has any diet plan
     */
    public static function hasPlan(): bool
    {
        return self::exists();
    }
}
