<?php

namespace App\Models\DietTracker;

use Illuminate\Database\Eloquent\Model;

class FoodDatabase extends Model
{
    protected $table = 'food_database';

    protected $fillable = [
        'nama', 'kategori', 'kalori', 'protein', 'karbohidrat',
        'lemak', 'satuan_porsi', 'berat_gram',
    ];

    public function scopeKategori($query, string $kategori)
    {
        return $query->where('kategori', $kategori);
    }
}
