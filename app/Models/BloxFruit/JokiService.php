<?php

namespace App\Models\BloxFruit;

use Illuminate\Database\Eloquent\Model;

class JokiService extends Model
{
    protected $fillable = ['kategori', 'nama', 'harga', 'keterangan', 'aktif'];
    protected $casts = ['aktif' => 'boolean'];
}
