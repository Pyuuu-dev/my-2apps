<?php

namespace App\Models\DietTracker;

use Illuminate\Database\Eloquent\Model;

class FoodDatabase extends Model
{
    protected $table = 'diet_food_database';

    protected $fillable = [
        'nama', 'kategori', 'kalori', 'protein', 'karbohidrat',
        'lemak', 'satuan_porsi', 'berat_gram',
    ];

    public static function search(string $query, int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('nama', 'like', "%{$query}%")
            ->orderBy('nama')
            ->limit($limit)
            ->get();
    }
}
