<?php

namespace App\Models\BloxFruit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BloxFruit extends Model
{
    protected $fillable = [
        'nama', 'tipe', 'rarity', 'harga_beli', 'harga_jual',
        'gambar', 'keterangan', 'aktif',
    ];

    protected $casts = ['aktif' => 'boolean'];

    public function skins(): HasMany
    {
        return $this->hasMany(FruitSkin::class, 'blox_fruit_id');
    }

    public function fruitStocks(): HasMany
    {
        return $this->hasMany(FruitStock::class, 'blox_fruit_id');
    }

    public function permanentStocks(): HasMany
    {
        return $this->hasMany(PermanentFruitStock::class, 'blox_fruit_id');
    }

    public function getTotalStokAttribute(): int
    {
        return $this->fruitStocks()->sum('jumlah');
    }

    public function scopeOrderByRarity($query, string $direction = 'desc')
    {
        return $query->orderByRaw("CASE rarity WHEN 'Common' THEN 1 WHEN 'Uncommon' THEN 2 WHEN 'Rare' THEN 3 WHEN 'Legendary' THEN 4 WHEN 'Mythical' THEN 5 END " . $direction);
    }
}
