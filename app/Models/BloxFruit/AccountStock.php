<?php

namespace App\Models\BloxFruit;

use Illuminate\Database\Eloquent\Model;

class AccountStock extends Model
{
    protected $fillable = [
        'judul', 'level', 'daftar_buah', 'daftar_gamepass',
        'harga', 'status', 'keterangan', 'gambar',
    ];
}
