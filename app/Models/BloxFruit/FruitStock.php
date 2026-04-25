<?php

namespace App\Models\BloxFruit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FruitStock extends Model
{
    protected $fillable = [
        'storage_account_id', 'blox_fruit_id', 'jumlah', 'catatan',
    ];

    public function storageAccount(): BelongsTo
    {
        return $this->belongsTo(StorageAccount::class);
    }

    public function fruit(): BelongsTo
    {
        return $this->belongsTo(BloxFruit::class, 'blox_fruit_id');
    }
}
