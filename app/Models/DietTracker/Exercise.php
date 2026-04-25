<?php

namespace App\Models\DietTracker;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exercise extends Model
{
    protected $fillable = [
        'diet_plan_id', 'tanggal', 'jenis_olahraga', 'durasi_menit',
        'kalori_terbakar', 'intensitas', 'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function dietPlan(): BelongsTo
    {
        return $this->belongsTo(DietPlan::class);
    }
}
