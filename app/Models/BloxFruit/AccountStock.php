<?php

namespace App\Models\BloxFruit;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasSlug;

class AccountStock extends Model
{
    use HasSlug;

    protected string $slugSource = 'username_roblox';

    protected $fillable = [
        'judul', 'username_roblox', 'password_roblox',
        'sword_gun', 'fruit', 'belly', 'fragment', 'race',
        'level', 'daftar_buah', 'daftar_gamepass',
        'harga_beli', 'harga_jual', 'status', 'keterangan', 'gambar', 'slug',
    ];

    protected $casts = [
        'password_roblox' => 'encrypted',
    ];
}
