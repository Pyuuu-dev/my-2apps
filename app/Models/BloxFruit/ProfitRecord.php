<?php

namespace App\Models\BloxFruit;

use Illuminate\Database\Eloquent\Model;

class ProfitRecord extends Model
{
    protected $fillable = [
        'tanggal', 'kategori', 'keterangan', 'modal',
        'pendapatan', 'keuntungan', 'metode_bayar',
    ];

    protected $casts = ['tanggal' => 'date'];
}
