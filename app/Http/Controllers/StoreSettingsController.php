<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class StoreSettingsController extends Controller
{
    public function edit()
    {
        $settings = Setting::orderBy('group')->orderBy('id')->get()->groupBy('group');
        $groupLabels = [
            'store' => ['label' => 'Brand & Identity', 'desc' => 'Nama brand, aplikasi, dan tagline'],
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

            $existing->update(['value' => $value]);
        }

        // Force invalidate cache (Setting::saved event triggers per-model save)
        \Illuminate\Support\Facades\Cache::forget('settings.all');

        return redirect()->route('settings.store.edit')->with('sukses', 'Pengaturan store berhasil disimpan.');
    }
}
