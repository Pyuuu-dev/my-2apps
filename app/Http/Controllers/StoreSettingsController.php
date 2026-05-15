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
            if ($existing) {
                $existing->update(['value' => $value]);
            }
        }

        // Force invalidate cache (Setting::saved event triggers per-model save)
        \Illuminate\Support\Facades\Cache::forget('settings.all');

        return redirect()->route('settings.store.edit')->with('sukses', 'Pengaturan store berhasil disimpan.');
    }
}
