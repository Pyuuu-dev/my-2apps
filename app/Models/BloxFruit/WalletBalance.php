<?php

namespace App\Models\BloxFruit;

use Illuminate\Database\Eloquent\Model;

class WalletBalance extends Model
{
    protected $fillable = [
        'tanggal', 'dana', 'gopay', 'shopeepay', 'seabank',
        'bank_kalsel', 'bri', 'qris', 'cash', 'catatan',
    ];

    protected $casts = ['tanggal' => 'date'];

    public function getTotalAttribute(): int
    {
        return $this->dana + $this->gopay + $this->shopeepay + $this->seabank
            + $this->bank_kalsel + $this->bri + $this->qris + $this->cash;
    }
}
