<?php

namespace App\Models\DietTracker;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BodyMeasurement extends Model
{
    protected $table = 'diet_body_measurements';

    protected $fillable = [
        'profile_id', 'tanggal', 'lingkar_pinggang', 'lingkar_dada',
        'lingkar_lengan', 'lingkar_paha', 'lingkar_pinggul', 'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(UserProfile::class, 'profile_id');
    }
}
