<?php

namespace App\Models\DietTracker;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reminder extends Model
{
    protected $fillable = [
        'diet_plan_id', 'judul', 'pesan', 'waktu',
        'hari_aktif', 'tipe', 'aktif', 'last_sent_at',
    ];

    protected $casts = [
        'aktif' => 'boolean',
        'last_sent_at' => 'datetime',
    ];

    /**
     * Cek apakah pengingat harus dikirim hari ini
     */
    public function shouldSendToday(): bool
    {
        if (!$this->aktif) return false;

        $dayOfWeek = now()->dayOfWeekIso; // 1=Senin, 7=Minggu

        return match ($this->hari_aktif) {
            'setiap_hari' => true,
            'senin_jumat' => $dayOfWeek >= 1 && $dayOfWeek <= 5,
            'weekend' => $dayOfWeek >= 6,
            default => true,
        };
    }

    /**
     * Cek apakah sudah dikirim hari ini
     */
    public function alreadySentToday(): bool
    {
        return $this->last_sent_at && $this->last_sent_at->isToday();
    }

    public function dietPlan(): BelongsTo
    {
        return $this->belongsTo(DietPlan::class);
    }
}
