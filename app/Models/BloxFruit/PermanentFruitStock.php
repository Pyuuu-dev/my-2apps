<?php

namespace App\Models\BloxFruit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermanentFruitStock extends Model
{
    protected $fillable = [
        'storage_account_id', 'blox_fruit_id', 'permanent_fruit_price_id',
        'harga_robux', 'harga_idr', 'jumlah', 'catatan',
    ];

    public function storageAccount(): BelongsTo
    {
        return $this->belongsTo(StorageAccount::class);
    }

    public function fruit(): BelongsTo
    {
        return $this->belongsTo(BloxFruit::class, 'blox_fruit_id');
    }

    public function permanentPrice(): BelongsTo
    {
        return $this->belongsTo(PermanentFruitPrice::class, 'permanent_fruit_price_id');
    }
}
