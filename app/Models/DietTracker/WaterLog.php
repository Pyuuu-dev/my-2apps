<?php

namespace App\Models\DietTracker;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaterLog extends Model
{
    protected $fillable = [
        'diet_plan_id', 'tanggal', 'jumlah_ml', 'waktu',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function dietPlan(): BelongsTo
    {
        return $this->belongsTo(DietPlan::class);
    }
}
