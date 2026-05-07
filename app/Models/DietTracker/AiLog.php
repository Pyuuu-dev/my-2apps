<?php

namespace App\Models\DietTracker;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiLog extends Model
{
    protected $table = 'diet_ai_logs';

    protected $fillable = [
        'profile_id', 'tipe', 'model_used', 'prompt',
        'response', 'tokens_used', 'response_time_ms',
        'success', 'error_message',
    ];

    protected $casts = [
        'success' => 'boolean',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(UserProfile::class, 'profile_id');
    }
}
