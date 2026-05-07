<?php

namespace App\Models\DietTracker;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reminder extends Model
{
    protected $table = 'diet_reminders';

    protected $fillable = [
        'profile_id', 'tipe', 'judul', 'pesan', 'waktu',
        'hari_aktif', 'aktif', 'last_sent_at',
    ];

    protected $casts = [
        'hari_aktif' => 'array',
        'aktif' => 'boolean',
        'last_sent_at' => 'datetime',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(UserProfile::class, 'profile_id');
    }

    public function shouldSendNow(): bool
    {
        if (!$this->aktif) return false;

        $now = now('Asia/Singapore');
        $currentTime = $now->format('H:i');
        $currentDay = $now->dayOfWeekIso; // 1=Monday, 7=Sunday

        // Check day
        if ($this->hari_aktif && !in_array($currentDay, $this->hari_aktif)) {
            return false;
        }

        // Check time (within 1 minute window)
        if ($currentTime !== substr($this->waktu, 0, 5)) {
            return false;
        }

        // Check if already sent today
        if ($this->last_sent_at && $this->last_sent_at->isToday()) {
            return false;
        }

        return true;
    }
}
