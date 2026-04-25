<?php

namespace App\Models\BloxFruit;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasSlug;

class JokiService extends Model
{
    use HasSlug;

    protected string $slugSource = 'nama';

    protected $fillable = ['kategori', 'nama', 'harga', 'keterangan', 'aktif', 'slug'];
    protected $casts = ['aktif' => 'boolean'];
}
