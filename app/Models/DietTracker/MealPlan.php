<?php

namespace App\Models\DietTracker;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MealPlan extends Model
{
    protected $table = 'diet_meal_plans';

    protected $fillable = [
        'profile_id', 'tanggal', 'sarapan', 'makan_siang',
        'makan_malam', 'snack', 'total_kalori', 'total_protein',
        'catatan_ai', 'completed',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'sarapan' => 'array',
        'makan_siang' => 'array',
        'makan_malam' => 'array',
        'snack' => 'array',
        'completed' => 'boolean',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(UserProfile::class, 'profile_id');
    }
}
