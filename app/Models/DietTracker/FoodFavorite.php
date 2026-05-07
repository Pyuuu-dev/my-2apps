<?php

namespace App\Models\DietTracker;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoodFavorite extends Model
{
    protected $table = 'diet_food_favorites';

    protected $fillable = [
        'profile_id', 'nama_makanan', 'waktu_makan', 'kalori',
        'protein', 'karbohidrat', 'lemak', 'porsi', 'satuan_porsi', 'use_count',
    ];

    protected $casts = [
        'porsi' => 'float',
        'protein' => 'float',
        'karbohidrat' => 'float',
        'lemak' => 'float',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(UserProfile::class, 'profile_id');
    }

    public static function getTopFavorites(int $profileId, int $limit = 5)
    {
        return static::where('profile_id', $profileId)
            ->orderByDesc('use_count')
            ->limit($limit)
            ->get();
    }

    public function incrementUse(): void
    {
        $this->increment('use_count');
    }
}
