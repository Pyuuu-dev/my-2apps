<?php

namespace App\Models\BloxFruit;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasSlug;

class ProfitRecord extends Model
{
    use HasSlug;

    protected $fillable = [
        'tanggal', 'kategori', 'keterangan', 'modal',
        'pendapatan', 'keuntungan', 'metode_bayar', 'slug',
    ];

    protected $casts = ['tanggal' => 'date'];
}
