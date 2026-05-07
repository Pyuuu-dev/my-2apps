<?php

namespace App\Models\DietTracker;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Badge extends Model
{
    protected $table = 'diet_badges';

    protected $fillable = [
        'profile_id', 'badge_code', 'badge_name', 'badge_icon',
        'deskripsi', 'earned_at',
    ];

    protected $casts = [
        'earned_at' => 'datetime',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(UserProfile::class, 'profile_id');
    }
}
