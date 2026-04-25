<?php

namespace App\Models\BloxFruit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\HasSlug;

class Gamepass extends Model
{
    use HasSlug;

    protected string $slugSource = 'nama';

    protected $fillable = [
        'nama', 'harga_robux', 'harga_beli', 'harga_jual', 'deskripsi', 'gambar', 'aktif', 'slug',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    public function stocks(): HasMany
    {
        return $this->hasMany(GamepassStock::class, 'gamepass_id');
    }

    public function getTotalStokAttribute(): int
    {
        return $this->stocks()->sum('jumlah');
    }
}
