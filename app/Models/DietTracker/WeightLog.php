<?php

namespace App\Models\DietTracker;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeightLog extends Model
{
    protected $table = 'diet_weight_logs';

    protected $fillable = [
        'profile_id', 'tanggal', 'berat_kg', 'bmi', 'body_fat_pct', 'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'berat_kg' => 'float',
        'bmi' => 'float',
        'body_fat_pct' => 'float',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(UserProfile::class, 'profile_id');
    }
}
