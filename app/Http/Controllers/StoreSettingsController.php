<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use enshrined\svgSanitize\Sanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StoreSettingsController extends Controller
{
    public function edit()
    {
        $settings = Setting::orderBy('group')->orderBy('id')->get()->groupBy('group');
        $groupLabels = [
            'store' => ['label' => 'Brand & Identity', 'desc' => 'Nama brand, aplikasi, dan tagline'],
            'branding' => ['label' => 'Logo & Branding', 'desc' => 'Logo navbar dan warna theme browser'],
            'kontak' => ['label' => 'Kontak & Sosial Media', 'desc' => 'WhatsApp, TikTok, Instagram, Discord, Drive'],
            'copy' => ['label' => 'Template Marketing', 'desc' => 'Header & format copy untuk share stok'],
            'app' => ['label' => 'Aplikasi', 'desc' => 'Konfigurasi umum aplikasi'],
            'general' => ['label' => 'Lainnya', 'desc' => ''],
        ];

        return view('settings.store', compact('settings', 'groupLabels'));
    }

    public function update(Request $request)
    {
        $values = $request->input('settings', []);

        foreach ($values as $key => $value) {
            $existing = Setting::where('key', $key)->first();
            if (!$existing) {
                continue;
            }

            $value = is_string($value) ? trim($value) : $value;

            // URL fields: only allow http(s) scheme — blocks javascript:, data:, file: etc.
            if ($existing->type === 'url' && $value !== '') {
                $scheme = parse_url($value, PHP_URL_SCHEME);
                if (!in_array(strtolower((string) $scheme), ['http', 'https'], true)
                    || !filter_var($value, FILTER_VALIDATE_URL)) {
                    return back()
                        ->with('error', "URL '{$existing->label}' tidak valid (harus diawali http:// atau https://).")
                        ->withInput();
                }
            }

            // WhatsApp number: digit only, must start with country code 62, 8-15 total digits.
            if ($key === 'store.wa_number' && $value !== '') {
                if (!preg_match('/^62\d{6,13}$/', $value)) {
                    return back()
                        ->with('error', 'Nomor WhatsApp harus diawali 62 dan hanya berisi angka (contoh: 6282353085502).')
                        ->withInput();
                }
            }

            // SVG: pre-check + sanitize against XSS dengan diagnosis spesifik
            if ($existing->type === 'svg' && $value !== '') {
                // Temporary logging untuk diagnosa kasus user
                \Illuminate\Support\Facades\Log::info('SVG setting submitted', [
                    'key' => $key,
                    'length' => strlen($value),
                    'preview' => substr($value, 0, 200),
                    'has_svg_tag' => stripos($value, '<svg') !== false,
                    'starts_with' => substr(ltrim($value), 0, 30),
                ]);

                $trimmed = ltrim($value);

                // Pre-check 1: User salah field — paste URL bukan markup SVG
                if (preg_match('~^https?://~i', $trimmed)) {
                    return back()
                        ->with('error', "Untuk paste link gambar, pakai field 'Logo URL' di atas. Field 'Logo SVG Inline' khusus untuk markup <svg>...</svg>.")
                        ->withInput();
                }

                // Pre-check 2: Tidak ada tag <svg
                if (stripos($value, '<svg') === false) {
                    return back()
                        ->with('error', "Logo SVG Inline harus berisi markup <svg>...</svg>. Contoh: <svg viewBox=\"0 0 24 24\"><path d=\"...\"/></svg>")
                        ->withInput();
                }

                // Strip UTF-8 BOM yang umum di file SVG export
                $value = preg_replace('/^\xEF\xBB\xBF/', '', $value);

                $sanitizer = new Sanitizer();
                $sanitizer->minify(true);
                $sanitizer->removeRemoteReferences(true);
                $clean = $sanitizer->sanitize($value);

                if ($clean === false) {
                    \Illuminate\Support\Facades\Log::warning('SVG sanitize parse failed', [
                        'key' => $key,
                        'preview' => substr($value, 0, 300),
                    ]);
                    return back()
                        ->with('error', "Markup SVG syntax invalid (XML parse gagal). Cek tag terbuka/tertutup, atau coba export ulang dari editor.")
                        ->withInput();
                }

                $cleanTrimmed = trim($clean);

                if ($cleanTrimmed === '') {
                    \Illuminate\Support\Facades\Log::warning('SVG fully stripped by sanitizer', [
                        'key' => $key,
                        'original_length' => strlen($value),
                        'preview' => substr($value, 0, 300),
                    ]);
                    return back()
                        ->with('error', "SVG kosong setelah sanitize. Coba SVG lebih sederhana (hanya <path>, <circle>, <rect>, <polygon>) atau gunakan 'Logo URL' untuk hosting di server lain.")
                        ->withInput();
                }

                if (stripos($cleanTrimmed, '<svg') === false) {
                    \Illuminate\Support\Facades\Log::warning('SVG tag missing after sanitize', [
                        'key' => $key,
                        'preview' => substr($cleanTrimmed, 0, 200),
                    ]);
                    return back()
                        ->with('error', "Tag <svg> hilang setelah sanitize (markup tidak valid sebagai SVG).")
                        ->withInput();
                }

                $value = $cleanTrimmed;
            }

            // Color: must be 6-digit hex (server-side only, browser pattern attribute removed)
            if ($existing->type === 'color' && $value !== '') {
                if (!preg_match('/^#[0-9a-fA-F]{6}$/', $value)) {
                    return back()
                        ->with('error', "Warna '{$existing->label}' harus format hex 6-digit (contoh #020617).")
                        ->withInput();
                }
            }

            $existing->update(['value' => $value]);
        }

        // Force invalidate cache (Setting::saved event triggers per-model save)
        Cache::forget('settings.all');

        return redirect()->route('settings.store.edit')->with('sukses', 'Pengaturan store berhasil disimpan.');
    }
}
