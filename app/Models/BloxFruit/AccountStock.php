<?php

namespace App\Models\BloxFruit;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasSlug;

class AccountStock extends Model
{
    use HasSlug;

    protected string $slugSource = 'judul';

    protected $fillable = [
        'judul', 'level', 'daftar_buah', 'daftar_gamepass',
        'harga', 'status', 'keterangan', 'gambar', 'slug',
    ];
}
