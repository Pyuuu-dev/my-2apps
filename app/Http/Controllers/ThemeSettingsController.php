<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ThemeSettingsController extends Controller
{
    /**
     * Daftar preset siap-pakai. Hanya accent yang berubah supaya
     * kontras text/surface tetap aman.
     */
    public const PRESETS = [
        'indigo' => [
            'label' => 'Indigo',
            'description' => 'Default. Modern, profesional, ungu kebiruan.',
            'light' => ['accent' => '#4f46e5'],
            'dark'  => ['accent' => '#6366f1'],
        ],
        'emerald' => [
            'label' => 'Emerald',
            'description' => 'Hijau segar, cocok untuk store/keuangan.',
            'light' => ['accent' => '#059669'],
            'dark'  => ['accent' => '#10b981'],
        ],
        'rose' => [
            'label' => 'Rose',
            'description' => 'Merah jambu hangat, energik.',
            'light' => ['accent' => '#e11d48'],
            'dark'  => ['accent' => '#f43f5e'],
        ],
        'amber' => [
            'label' => 'Amber',
            'description' => 'Kuning keemasan, premium dan ceria.',
            'light' => ['accent' => '#d97706'],
            'dark'  => ['accent' => '#f59e0b'],
        ],
        'slate' => [
            'label' => 'Slate',
            'description' => 'Abu-abu netral, minimalis profesional.',
            'light' => ['accent' => '#475569'],
            'dark'  => ['accent' => '#94a3b8'],
        ],
        'ocean' => [
            'label' => 'Ocean',
            'description' => 'Biru laut, calm & trustworthy.',
            'light' => ['accent' => '#0284c7'],
            'dark'  => ['accent' => '#38bdf8'],
        ],
    ];

    public const FONTS = [
        'inter'         => ['label' => 'Inter',          'stack' => "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif", 'google' => 'Inter:wght@400;500;600;700'],
        'manrope'       => ['label' => 'Manrope',        'stack' => "'Manrope', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif", 'google' => 'Manrope:wght@400;500;600;700;800'],
        'plus-jakarta'  => ['label' => 'Plus Jakarta',   'stack' => "'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif", 'google' => 'Plus+Jakarta+Sans:wght@400;500;600;700;800'],
        'dm-sans'       => ['label' => 'DM Sans',        'stack' => "'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif", 'google' => 'DM+Sans:wght@400;500;600;700'],
        'system'        => ['label' => 'System Default', 'stack' => "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif", 'google' => null],
    ];

    public const ENUMS = [
        'theme.mode_default'           => ['light', 'dark', 'system'],
        'theme.layout.radius'          => ['sm', 'md', 'lg'],
        'theme.layout.density'         => ['compact', 'normal', 'comfortable'],
        'theme.layout.sidebar_variant' => ['subtle', 'solid', 'accent-tint'],
        'theme.layout.reduce_motion'   => ['0', '1'],
    ];

    /**
     * Token color yang dikelola.
     */
    public const COLOR_TOKENS = ['accent', 'bg', 'surface', 'text', 'success', 'warning', 'danger', 'info'];

    public function edit()
    {
        // Ensure semua key default ter-seed; aman dipanggil berkali-kali.
        $this->ensureSeeded();

        $values = Setting::where('key', 'like', 'theme.%')
            ->pluck('value', 'key')
            ->toArray();

        $presets = self::PRESETS;
        $fonts = self::FONTS;
        $userPresets = $this->loadUserPresets();

        return view('settings.theme', compact('values', 'presets', 'fonts', 'userPresets'));
    }

    public function update(Request $request)
    {
        $input = $request->input('settings', []);

        foreach ($input as $key => $value) {
            if (!str_starts_with($key, 'theme.')) {
                continue;
            }

            $value = is_string($value) ? trim($value) : $value;

            // Enum check
            if (isset(self::ENUMS[$key])) {
                if ($value !== '' && !in_array($value, self::ENUMS[$key], true)) {
                    return back()
                        ->with('error', "Nilai '{$key}' tidak valid.")
                        ->withInput();
                }
            }

            // Preset check
            if ($key === 'theme.preset' && $value !== '' && !array_key_exists($value, self::PRESETS) && $value !== 'custom') {
                return back()->with('error', 'Preset tidak dikenal.')->withInput();
            }

            // Font check
            if ($key === 'theme.layout.font_family' && $value !== '' && !array_key_exists($value, self::FONTS)) {
                return back()->with('error', 'Font tidak dikenal.')->withInput();
            }

            // Color check (hex 6-digit), kosong = pakai default
            if ((str_starts_with($key, 'theme.light.') || str_starts_with($key, 'theme.dark.')) && $value !== '') {
                if (!preg_match('/^#[0-9a-fA-F]{6}$/', $value)) {
                    return back()
                        ->with('error', "Warna '{$key}' harus format hex 6-digit (contoh #4f46e5).")
                        ->withInput();
                }
                $value = strtolower($value);
            }

            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Cache::forget('settings.all');

        return redirect()->route('settings.theme.edit')->with('sukses', 'Tema berhasil disimpan.');
    }

    public function reset()
    {
        // Reset semua color & layout ke kosong (= pakai default app.css)
        $resetKeys = [
            'theme.preset' => 'indigo',
            'theme.mode_default' => 'light',
            'theme.layout.radius' => 'md',
            'theme.layout.density' => 'normal',
            'theme.layout.font_family' => 'inter',
            'theme.layout.sidebar_variant' => 'subtle',
            'theme.layout.reduce_motion' => '0',
        ];

        foreach ($resetKeys as $key => $val) {
            Setting::where('key', $key)->update(['value' => $val]);
        }

        // Kosongkan semua color override
        Setting::where('key', 'like', 'theme.light.%')->orWhere('key', 'like', 'theme.dark.%')
            ->update(['value' => '']);

        Cache::forget('settings.all');

        return redirect()->route('settings.theme.edit')->with('sukses', 'Tema dikembalikan ke default.');
    }

    /**
     * Quick-apply preset dari topbar atau halaman lain.
     */
    public function quickApply(Request $request)
    {
        $preset = (string) $request->input('preset', '');
        if (!array_key_exists($preset, self::PRESETS)) {
            return back()->with('error', 'Preset tidak dikenal.');
        }
        $p = self::PRESETS[$preset];

        Setting::updateOrCreate(['key' => 'theme.preset'], ['value' => $preset]);
        if (!empty($p['light']['accent'])) {
            Setting::updateOrCreate(['key' => 'theme.light.accent'], ['value' => $p['light']['accent']]);
        }
        if (!empty($p['dark']['accent'])) {
            Setting::updateOrCreate(['key' => 'theme.dark.accent'], ['value' => $p['dark']['accent']]);
        }

        Cache::forget('settings.all');

        return back()->with('sukses', "Preset '{$p['label']}' diterapkan.");
    }

    /**
     * Export tema sekarang sebagai JSON file.
     */
    public function export()
    {
        $values = Setting::where('key', 'like', 'theme.%')
            ->where('key', '!=', 'theme.user_presets')
            ->pluck('value', 'key')
            ->toArray();

        $payload = [
            'version' => 1,
            'exported_at' => now()->toIso8601String(),
            'theme' => $values,
        ];

        $filename = 'theme-' . now()->format('Y-m-d-His') . '.json';

        return response()->json($payload, 200, [
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Import tema dari upload JSON.
     */
    public function import(Request $request)
    {
        $request->validate([
            'theme_file' => 'required|file|mimes:json,txt|max:64',
        ]);

        $content = file_get_contents($request->file('theme_file')->getRealPath());
        $data = json_decode($content, true);

        if (!is_array($data) || !isset($data['theme']) || !is_array($data['theme'])) {
            return back()->with('error', 'Format file tidak valid.');
        }

        $applied = 0;
        foreach ($data['theme'] as $key => $value) {
            if (!is_string($key) || !str_starts_with($key, 'theme.')) continue;
            if (!is_string($value) && !is_int($value)) continue;
            $value = (string) $value;

            // Same validation rules as update()
            if (isset(self::ENUMS[$key]) && $value !== '' && !in_array($value, self::ENUMS[$key], true)) continue;
            if ($key === 'theme.preset' && $value !== '' && !array_key_exists($value, self::PRESETS) && $value !== 'custom') continue;
            if ($key === 'theme.layout.font_family' && $value !== '' && !array_key_exists($value, self::FONTS)) continue;
            if ((str_starts_with($key, 'theme.light.') || str_starts_with($key, 'theme.dark.')) && $value !== '' && !preg_match('/^#[0-9a-fA-F]{6}$/', $value)) continue;

            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
            $applied++;
        }

        Cache::forget('settings.all');

        return redirect()->route('settings.theme.edit')->with('sukses', "Tema diimpor. {$applied} setting diterapkan.");
    }

    /**
     * Save current state as a user preset.
     */
    public function savePreset(Request $request)
    {
        $name = trim((string) $request->input('name', ''));
        if ($name === '' || strlen($name) > 40) {
            return back()->with('error', 'Nama preset wajib diisi (max 40 karakter).');
        }

        $current = Setting::where('key', 'like', 'theme.light.%')
            ->orWhere('key', 'like', 'theme.dark.%')
            ->pluck('value', 'key')
            ->toArray();

        $presets = $this->loadUserPresets();

        // Limit max 10 user presets
        if (count($presets) >= 10) {
            return back()->with('error', 'Maksimal 10 preset custom. Hapus salah satu dulu.');
        }

        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name)) . '-' . substr(md5(uniqid()), 0, 6);

        $presets[$slug] = [
            'label' => $name,
            'colors' => $current,
            'created_at' => now()->toIso8601String(),
        ];

        Setting::updateOrCreate(['key' => 'theme.user_presets'], ['value' => json_encode($presets)]);
        Cache::forget('settings.all');

        return back()->with('sukses', "Preset '{$name}' disimpan.");
    }

    /**
     * Delete a user preset by slug.
     */
    public function deletePreset(Request $request)
    {
        $slug = (string) $request->input('slug', '');
        $presets = $this->loadUserPresets();

        if (!isset($presets[$slug])) {
            return back()->with('error', 'Preset tidak ditemukan.');
        }

        unset($presets[$slug]);
        Setting::updateOrCreate(['key' => 'theme.user_presets'], ['value' => json_encode($presets)]);
        Cache::forget('settings.all');

        return back()->with('sukses', 'Preset dihapus.');
    }

    /**
     * Apply user preset by slug.
     */
    public function applyUserPreset(Request $request)
    {
        $slug = (string) $request->input('slug', '');
        $presets = $this->loadUserPresets();

        if (!isset($presets[$slug])) {
            return back()->with('error', 'Preset tidak ditemukan.');
        }

        Setting::updateOrCreate(['key' => 'theme.preset'], ['value' => 'custom']);
        foreach ($presets[$slug]['colors'] as $key => $value) {
            if (!str_starts_with($key, 'theme.light.') && !str_starts_with($key, 'theme.dark.')) continue;
            if ($value !== '' && !preg_match('/^#[0-9a-fA-F]{6}$/', $value)) continue;
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Cache::forget('settings.all');

        return back()->with('sukses', "Preset '{$presets[$slug]['label']}' diterapkan.");
    }

    public function loadUserPresets(): array
    {
        $raw = Setting::where('key', 'theme.user_presets')->value('value');
        if (!$raw) return [];
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Pastikan semua key default ter-seed.
     */
    protected function ensureSeeded(): void
    {
        $existing = Setting::where('key', 'like', 'theme.%')->pluck('key')->toArray();
        if (count($existing) >= 24) {
            return;
        }
        (new \Database\Seeders\ThemeSettingsSeeder())->run();
        Cache::forget('settings.all');
    }
}
