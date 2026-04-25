<?php

namespace App\Models\DietTracker;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FastingLog extends Model
{
    protected $fillable = [
        'diet_plan_id', 'tanggal', 'tipe', 'waktu_sahur',
        'waktu_berbuka', 'completed', 'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'completed' => 'boolean',
    ];

    public function dietPlan(): BelongsTo
    {
        return $this->belongsTo(DietPlan::class);
    }

    /**
     * Cek apakah hari ini puasa
     */
    public static function isFastingToday(?int $planId = null): bool
    {
        if (!$planId) {
            $plan = DietPlan::getActivePlan();
            $planId = $plan?->id;
        }
        if (!$planId) return false;

        return self::where('diet_plan_id', $planId)
            ->whereDate('tanggal', now()->toDateString())
            ->exists();
    }

    /**
     * Ambil data puasa hari ini
     */
    public static function getTodayFasting(?int $planId = null): ?self
    {
        if (!$planId) {
            $plan = DietPlan::getActivePlan();
            $planId = $plan?->id;
        }
        if (!$planId) return null;

        return self::where('diet_plan_id', $planId)
            ->whereDate('tanggal', now()->toDateString())
            ->first();
    }

    /**
     * Label tipe puasa
     */
    public function getLabelTipeAttribute(): string
    {
        return match ($this->tipe) {
            'ramadhan' => 'Ramadhan',
            'senin_kamis' => 'Senin-Kamis',
            'ayyamul_bidh' => 'Ayyamul Bidh (13-15)',
            'daud' => 'Puasa Daud',
            'syawal' => 'Syawal',
            'arafah' => 'Arafah',
            'asyura' => 'Asyura',
            'custom' => 'Custom',
            default => 'Sunnah',
        };
    }
}
