<?php

namespace App\Models\DietTracker;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoodLog extends Model
{
    protected $table = 'diet_food_logs';

    protected $fillable = [
        'profile_id', 'tanggal', 'waktu_makan', 'nama_makanan',
        'porsi', 'satuan_porsi', 'kalori', 'protein', 'karbohidrat',
        'lemak', 'foto_url', 'sumber', 'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'porsi' => 'float',
        'kalori' => 'integer',
        'protein' => 'float',
        'karbohidrat' => 'float',
        'lemak' => 'float',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(UserProfile::class, 'profile_id');
    }

    public function scopeToday($query, $profileId)
    {
        return $query->where('profile_id', $profileId)
            ->whereDate('tanggal', now()->toDateString());
    }

    public function scopeByDate($query, $profileId, $date)
    {
        return $query->where('profile_id', $profileId)
            ->whereDate('tanggal', $date);
    }
}
