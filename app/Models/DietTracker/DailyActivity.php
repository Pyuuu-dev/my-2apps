<?php

namespace App\Models\DietTracker;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyActivity extends Model
{
    protected $fillable = [
        'diet_plan_id', 'tanggal', 'langkah_kaki', 'jarak_km',
        'kalori_terbakar', 'berat_badan', 'jam_tidur', 'air_minum_ml', 'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function dietPlan(): BelongsTo
    {
        return $this->belongsTo(DietPlan::class);
    }
}
