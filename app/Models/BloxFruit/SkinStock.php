<?php

namespace App\Models\BloxFruit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkinStock extends Model
{
    protected $fillable = [
        'storage_account_id', 'fruit_skin_id', 'jumlah', 'catatan',
    ];

    public function storageAccount(): BelongsTo
    {
        return $this->belongsTo(StorageAccount::class);
    }

    public function skin(): BelongsTo
    {
        return $this->belongsTo(FruitSkin::class, 'fruit_skin_id');
    }
}
