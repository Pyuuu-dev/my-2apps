<?php

namespace App\Models\BloxFruit;

use Illuminate\Database\Eloquent\Model;

class JokiOrder extends Model
{
    protected $fillable = [
        'nama_pelanggan', 'kontak', 'username_roblox', 'password_roblox',
        'jenis_joki', 'detail_pesanan', 'harga', 'status',
        'tanggal_mulai', 'tanggal_selesai', 'catatan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];
}
