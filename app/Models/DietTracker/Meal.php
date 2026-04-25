<?php

namespace App\Models\DietTracker;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Meal extends Model
{
    protected $fillable = [
        'diet_plan_id', 'tanggal', 'waktu_makan', 'nama_makanan',
        'kalori', 'protein', 'karbohidrat', 'lemak', 'porsi', 'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function dietPlan(): BelongsTo
    {
        return $this->belongsTo(DietPlan::class);
    }
}
