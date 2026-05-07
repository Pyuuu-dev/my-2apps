<?php

namespace App\Models\DietTracker;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExerciseLog extends Model
{
    protected $table = 'diet_exercise_logs';

    protected $fillable = [
        'profile_id', 'tanggal', 'jenis_olahraga', 'durasi_menit',
        'kalori_terbakar', 'intensitas', 'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(UserProfile::class, 'profile_id');
    }
}
