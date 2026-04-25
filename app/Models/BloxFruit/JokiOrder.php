<?php

namespace App\Models\BloxFruit;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasSlug;

class JokiOrder extends Model
{
    use HasSlug;

    protected string $slugSource = 'nama_pelanggan';

    protected $fillable = [
        'nama_pelanggan', 'kontak', 'username_roblox', 'password_roblox',
        'jenis_joki', 'detail_pesanan', 'harga', 'status',
        'tanggal_mulai', 'tanggal_selesai', 'catan', 'slug',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];
}
