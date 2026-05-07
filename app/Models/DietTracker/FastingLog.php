<?php

namespace App\Models\DietTracker;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FastingLog extends Model
{
    protected $table = 'diet_fasting_logs';

    protected $fillable = [
        'profile_id', 'tanggal', 'tipe', 'mulai_puasa',
        'buka_puasa', 'durasi_menit', 'completed', 'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'mulai_puasa' => 'datetime',
        'buka_puasa' => 'datetime',
        'completed' => 'boolean',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(UserProfile::class, 'profile_id');
    }

    public function getTargetHours(): int
    {
        return match ($this->tipe) {
            '16_8' => 16,
            '18_6' => 18,
            '20_4' => 20,
            'omad' => 23,
            default => 16,
        };
    }

    public function getElapsedMinutes(): int
    {
        if (!$this->mulai_puasa) return 0;
        $end = $this->buka_puasa ?? now('Asia/Singapore');
        return (int) $this->mulai_puasa->diffInMinutes($end);
    }

    public function getProgressPercent(): float
    {
        $targetMinutes = $this->getTargetHours() * 60;
        $elapsed = $this->getElapsedMinutes();
        return min(100, round(($elapsed / $targetMinutes) * 100, 1));
    }
}
