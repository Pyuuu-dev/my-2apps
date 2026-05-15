<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group', 'label', 'type'];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('settings.all'));
        static::deleted(fn () => Cache::forget('settings.all'));
    }

    /**
     * Ambil seluruh setting sebagai array key=>value (cached).
     */
    public static function allCached(): array
    {
        return Cache::rememberForever('settings.all', function () {
            return static::pluck('value', 'key')->toArray();
        });
    }

    /**
     * Get nilai setting by key.
     */
    public static function get(string $key, $default = null)
    {
        return static::allCached()[$key] ?? $default;
    }

    /**
     * Set/update nilai setting.
     */
    public static function put(string $key, $value, string $group = 'general', string $type = 'text'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group, 'type' => $type]
        );
    }
}
