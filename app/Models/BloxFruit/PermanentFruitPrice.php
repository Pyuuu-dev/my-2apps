<?php

namespace App\Models\BloxFruit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\HasSlug;

class PermanentFruitPrice extends Model
{
    use HasSlug;

    protected string $slugSource = 'nama';

    protected $fillable = [
        'nama', 'harga_robux', 'harga_beli', 'harga_jual', 'aktif', 'slug',
    ];

    protected $casts = ['aktif' => 'boolean'];

    public function stocks(): HasMany
    {
        return $this->hasMany(PermanentFruitStock::class, 'permanent_fruit_price_id');
    }

    public function getTotalStokAttribute(): int
    {
        return $this->stocks()->sum('jumlah');
    }
}
