<?php

namespace App\Models\DietTracker;

use Illuminate\Database\Eloquent\Model;

class ExerciseDatabase extends Model
{
    protected $table = 'exercise_database';

    protected $fillable = [
        'nama', 'kategori', 'intensitas', 'kalori_per_menit', 'deskripsi',
        'instruksi', 'manfaat', 'otot_target', 'durasi_rekomendasi',
        'set_rep', 'level', 'peralatan',
    ];
}
