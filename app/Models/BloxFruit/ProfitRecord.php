<?php

namespace App\Models\BloxFruit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasSlug;

class ProfitRecord extends Model
{
    use HasSlug, SoftDeletes;

    protected $fillable = [
        'tanggal', 'kategori', 'keterangan', 'modal',
        'pendapatan', 'keuntungan', 'metode_bayar', 'slug',
    ];

    protected $casts = ['tanggal' => 'date'];
}
