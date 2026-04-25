<?php

namespace App\Models\BloxFruit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GamepassStock extends Model
{
    protected $fillable = [
        'storage_account_id', 'gamepass_id', 'jumlah', 'catatan',
    ];

    public function storageAccount(): BelongsTo
    {
        return $this->belongsTo(StorageAccount::class);
    }

    public function gamepass(): BelongsTo
    {
        return $this->belongsTo(Gamepass::class);
    }
}
