<?php

namespace App\Models\DietTracker;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Streak extends Model
{
    protected $table = 'diet_streaks';

    protected $fillable = [
        'profile_id', 'current_streak', 'longest_streak',
        'last_log_date', 'total_days_logged',
    ];

    protected $casts = [
        'last_log_date' => 'date',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(UserProfile::class, 'profile_id');
    }

    public function recordActivity(): array
    {
        $today = now('Asia/Singapore')->toDateString();
        $newBadges = [];

        if ($this->last_log_date === $today) {
            return $newBadges; // Already logged today
        }

        $yesterday = now('Asia/Singapore')->subDay()->toDateString();

        if ($this->last_log_date && $this->last_log_date->toDateString() === $yesterday) {
            $this->current_streak++;
        } else {
            $this->current_streak = 1;
        }

        if ($this->current_streak > $this->longest_streak) {
            $this->longest_streak = $this->current_streak;
        }

        $this->last_log_date = $today;
        $this->total_days_logged++;
        $this->save();

        // Check for streak badges
        $streakBadges = [
            3 => ['code' => 'streak_3', 'name' => '3 Hari Berturut!', 'icon' => '🔥'],
            7 => ['code' => 'streak_7', 'name' => 'Seminggu Konsisten!', 'icon' => '⭐'],
            14 => ['code' => 'streak_14', 'name' => '2 Minggu Non-stop!', 'icon' => '🌟'],
            30 => ['code' => 'streak_30', 'name' => 'Sebulan Penuh!', 'icon' => '💎'],
            60 => ['code' => 'streak_60', 'name' => '60 Hari Legend!', 'icon' => '👑'],
            100 => ['code' => 'streak_100', 'name' => '100 Hari Master!', 'icon' => '🏆'],
        ];

        foreach ($streakBadges as $days => $badge) {
            if ($this->current_streak >= $days) {
                $earned = Badge::firstOrCreate(
                    ['profile_id' => $this->profile_id, 'badge_code' => $badge['code']],
                    ['badge_name' => $badge['name'], 'badge_icon' => $badge['icon'], 'deskripsi' => "Streak {$days} hari berturut-turut!", 'earned_at' => now()]
                );
                if ($earned->wasRecentlyCreated) {
                    $newBadges[] = $earned;
                }
            }
        }

        return $newBadges;
    }
}
