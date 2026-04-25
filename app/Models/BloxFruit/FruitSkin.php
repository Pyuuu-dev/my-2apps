<?php

namespace App\Models\BloxFruit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\HasSlug;

class FruitSkin extends Model
{
    use HasSlug;

    protected string $slugSource = 'nama_skin';

    protected $fillable = [
        'blox_fruit_id', 'nama_skin', 'harga_beli', 'harga_jual', 'gambar', 'keterangan', 'aktif', 'slug',
    ];

    protected $casts = ['aktif' => 'boolean'];

    public function fruit(): BelongsTo
    {
        return $this->belongsTo(BloxFruit::class, 'blox_fruit_id');
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(SkinStock::class, 'fruit_skin_id');
    }

    public function getTotalStokAttribute(): int
    {
        return $this->stocks()->sum('jumlah');
    }
}
