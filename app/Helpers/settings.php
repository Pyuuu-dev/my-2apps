<?php

/**
 * Settings helper — auto-loaded.
 *
 * Usage:
 *   setting('store.wa_number')
 *   setting('store.wa_number', '6282353085502') // with default
 *   setting()->put('store.brand', 'MyApp')
 */

use App\Models\Setting;

if (!function_exists('setting')) {
    /**
     * @return mixed|\App\Models\Setting
     */
    function setting(?string $key = null, $default = null)
    {
        if ($key === null) {
            return new Setting();
        }
        return Setting::get($key, $default);
    }
}
