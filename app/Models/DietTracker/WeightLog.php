<?php

namespace App\Models\DietTracker;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeightLog extends Model
{
    protected $fillable = [
        'diet_plan_id', 'tanggal', 'berat', 'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function dietPlan(): BelongsTo
    {
        return $this->belongsTo(DietPlan::class);
    }
}
