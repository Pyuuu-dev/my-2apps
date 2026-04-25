<?php

namespace App\Models\DietTracker;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyLog extends Model
{
    protected $fillable = [
        'diet_plan_id', 'bulan', 'berat_awal_bulan', 'berat_akhir_bulan',
        'berat_turun', 'target_kalori', 'avg_kalori_masuk', 'avg_kalori_keluar',
        'total_hari_olahraga', 'total_hari_catat', 'konsistensi_persen',
        'avg_langkah', 'avg_tidur', 'avg_air_minum', 'total_hari_aktivitas',
        'catatan', 'status',
    ];

    public function dietPlan(): BelongsTo
    {
        return $this->belongsTo(DietPlan::class);
    }
}
