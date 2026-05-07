<?php

namespace App\Models\DietTracker;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SleepLog extends Model
{
    protected $table = 'diet_sleep_logs';

    protected $fillable = [
        'profile_id', 'tanggal', 'jam_tidur', 'jam_bangun',
        'durasi_jam', 'kualitas', 'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'durasi_jam' => 'float',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(UserProfile::class, 'profile_id');
    }

    public static function calculateDuration(string $tidur, string $bangun): float
    {
        $t = \Carbon\Carbon::createFromFormat('H:i', $tidur);
        $b = \Carbon\Carbon::createFromFormat('H:i', $bangun);

        // Jika jam bangun lebih kecil dari jam tidur, berarti lewat tengah malam
        if ($b->lt($t)) {
            $b->addDay();
        }

        return round($t->diffInMinutes($b) / 60, 1);
    }
}
