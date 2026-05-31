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
        'theme.login_template'         => ['modern', 'split', 'minimal', 'image', 'glass', 'brutalist', 'neumorphism', 'cyberpunk', 'terminal', 'editorial', 'nature', 'layered', 'manga', 'glasslight', 'paper', 'gradient', 'corporate', 'arcade', 'sketch', 'holographic'],
    ];

    /**
     * Token color yang dikelola.
     */
    public const COLOR_TOKENS = ['accent', 'bg', 'surface', 'text', 'success', 'warning', 'danger', 'info'];

    /**
     * Daftar template halaman login yang tersedia. Digunakan untuk picker
     * di /settings/theme dan validasi di update().
     */
    public const LOGIN_TEMPLATES = [
        'modern' => [
            'label' => 'Modern Gradient',
            'description' => 'Gradient gelap full-screen dengan glassmorphism card. Vibe SaaS premium.',
            'palette' => ['#0f172a', '#1e1b4b', '#6366f1'],
            'group' => 'professional',
        ],
        'split' => [
            'label' => 'Split Screen',
            'description' => 'Dua kolom: brand showcase di kiri, form di kanan. Vibe corporate professional.',
            'palette' => ['#4f46e5', '#ffffff', '#0f172a'],
            'group' => 'professional',
        ],
        'minimal' => [
            'label' => 'Minimal Clean',
            'description' => 'Background terang, card kecil, monochrome. Vibe Apple-style.',
            'palette' => ['#fafafa', '#ffffff', '#171717'],
            'group' => 'professional',
        ],
        'image' => [
            'label' => 'Card on Image',
            'description' => 'Background full-image gaming dengan overlay card semi-transparent.',
            'palette' => ['#1e293b', '#dc2626', '#facc15'],
            'group' => 'atmospheric',
        ],
        'glass' => [
            'label' => 'Floating Glass',
            'description' => 'Animated gradient + glassmorphism card. Vibe futuristic gaming-tech.',
            'palette' => ['#0f172a', '#7c3aed', '#06b6d4'],
            'group' => 'atmospheric',
        ],
        'brutalist' => [
            'label' => 'Brutalist Bold',
            'description' => 'Color block solid, border tebal, font chunky. Statement-making.',
            'palette' => ['#facc15', '#000000', '#ec4899'],
            'group' => 'playful',
        ],
        'neumorphism' => [
            'label' => 'Neumorphism Soft',
            'description' => 'Soft UI dengan shadow inset/outset. Friendly dan tactile.',
            'palette' => ['#e0e5ec', '#ffffff', '#a3b1c6'],
            'group' => 'tactile',
        ],
        'cyberpunk' => [
            'label' => 'Cyberpunk Neon',
            'description' => 'Black bg + neon pink/cyan glow + scanlines. Futuristic gaming edgy.',
            'palette' => ['#0a0014', '#ff006e', '#00ffff'],
            'group' => 'themed',
        ],
        'terminal' => [
            'label' => 'Vintage Terminal',
            'description' => 'CRT terminal hijau monospace + blinking cursor. Hacker nostalgia.',
            'palette' => ['#000000', '#33ff33', '#0a0a0a'],
            'group' => 'themed',
        ],
        'editorial' => [
            'label' => 'Magazine Editorial',
            'description' => 'Layout asimetris typography-heavy, big serif. High-end editorial.',
            'palette' => ['#fef3c7', '#171717', '#dc2626'],
            'group' => 'professional',
        ],
        'nature' => [
            'label' => 'Nature Organic',
            'description' => 'Gradient warm sunset, sage/terracotta/cream. Calm wellness.',
            'palette' => ['#fef3c7', '#84cc16', '#c2410c'],
            'group' => 'themed',
        ],
        'layered' => [
            'label' => '3D Layered',
            'description' => 'Stacked cards dengan transforms, hover lift. Playful dimensional.',
            'palette' => ['#fafafa', '#6366f1', '#ec4899'],
            'group' => 'playful',
        ],
        'manga' => [
            'label' => 'Anime Manga',
            'description' => 'Screentone bg + border komik-style + speed lines. Kawaii gaming-anime.',
            'palette' => ['#ffffff', '#000000', '#ff4081'],
            'group' => 'playful',
        ],
        'glasslight' => [
            'label' => 'Glassmorphism Light',
            'description' => 'Pastel gradient terang + frosted glass putih. Airy soft modern.',
            'palette' => ['#fce7f3', '#dbeafe', '#ffffff'],
            'group' => 'atmospheric',
        ],
        'paper' => [
            'label' => 'Paper / Stationery',
            'description' => 'Tekstur kertas vintage notebook, garis tepi sketsa, warm cream.',
            'palette' => ['#fefae0', '#283618', '#bc6c25'],
            'group' => 'themed',
        ],
        'gradient' => [
            'label' => 'Bold Mesh Gradient',
            'description' => 'Mesh gradient multi-warna psychedelic, playful dan modern.',
            'palette' => ['#ff006e', '#fb5607', '#3a86ff'],
            'group' => 'atmospheric',
        ],
        'corporate' => [
            'label' => 'Corporate Premium',
            'description' => 'Dark navy + gold accent, formal banking/fintech vibe.',
            'palette' => ['#0c1e3e', '#d4af37', '#ffffff'],
            'group' => 'professional',
        ],
        'arcade' => [
            'label' => 'Retro Arcade 80s',
            'description' => 'Synthwave grid horizon + neon sun. Retro 80s gaming.',
            'palette' => ['#2d1b4e', '#ff006e', '#fbbf24'],
            'group' => 'themed',
        ],
        'sketch' => [
            'label' => 'Hand-drawn Sketch',
            'description' => 'Wobbly border, marker font, lucu kasar. Casual handcrafted.',
            'palette' => ['#fffdf7', '#1f2937', '#f59e0b'],
            'group' => 'playful',
        ],
        'holographic' => [
            'label' => 'Holographic Foil',
            'description' => 'Iridescent rainbow gradient yang shift. Futuristik foil.',
            'palette' => ['#fce7f3', '#a5f3fc', '#ddd6fe'],
            'group' => 'atmospheric',
        ],
    ];

    /**
     * Group label & order untuk UI picker.
     */
    public const LOGIN_TEMPLATE_GROUPS = [
        'professional' => 'Professional',
        'playful' => 'Playful & Bold',
        'themed' => 'Themed / Vibes',
        'atmospheric' => 'Atmospheric',
        'tactile' => 'Soft & Tactile',
    ];

    public function edit()
    {
        // Ensure semua key default ter-seed; aman dipanggil berkali-kali.
        $this->ensureSeeded();

        $values = Setting::where('key', 'like', 'theme.%')
            ->pluck('value', 'key')
            ->toArray();

        $presets = self::PRESETS;
        $fonts = self::FONTS;
        $loginTemplates = self::LOGIN_TEMPLATES;
        $loginGroups = self::LOGIN_TEMPLATE_GROUPS;
        $userPresets = $this->loadUserPresets();

        return view('settings.theme', compact('values', 'presets', 'fonts', 'loginTemplates', 'loginGroups', 'userPresets'));
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
            'theme.login_template' => 'modern',
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
        if (count($existing) >= 25) {
            return;
        }
        (new \Database\Seeders\ThemeSettingsSeeder())->run();
        Cache::forget('settings.all');
    }
}
