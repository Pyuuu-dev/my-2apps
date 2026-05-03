<?php

namespace App\Models\BloxFruit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\HasSlug;

class StorageAccount extends Model
{
    use HasSlug;

    protected string $slugSource = 'nama_akun';

    protected $fillable = [
        'nama_akun', 'username', 'catatan', 'aktif', 'kapasitas_storage',
    ];

    protected $casts = ['aktif' => 'boolean'];

    public function fruitStocks(): HasMany
    {
        return $this->hasMany(FruitStock::class, 'storage_account_id');
    }

    public function skinStocks(): HasMany
    {
        return $this->hasMany(SkinStock::class, 'storage_account_id');
    }

    public function gamepassStocks(): HasMany
    {
        return $this->hasMany(GamepassStock::class, 'storage_account_id');
    }

    public function permanentStocks(): HasMany
    {
        return $this->hasMany(PermanentFruitStock::class, 'storage_account_id');
    }
}
