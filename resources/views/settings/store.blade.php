@extends('layouts.app')
@section('title', 'Pengaturan Store')

@section('content')
<div class="max-w-3xl space-y-6">

    <x-page-header eyebrow="Pengaturan" title="Pengaturan Store" subtitle="Edit brand, kontak, logo, dan template marketing yang dipakai di seluruh aplikasi">
    </x-page-header>

    <form method="POST" action="{{ route('settings.store.update') }}" class="space-y-6" enctype="multipart/form-data">
        @csrf

        @foreach($settings as $group => $items)
        @php $info = $groupLabels[$group] ?? ['label' => ucfirst($group), 'desc' => '']; @endphp
        <div class="card overflow-hidden">
            <div class="px-5 py-3.5 border-b border-[var(--border)]">
                <h3 class="text-sm font-semibold text-[var(--text)] section-bar">{{ $info['label'] }}</h3>
                @if($info['desc'])
                <p class="text-xs text-[var(--text-muted)] mt-1">{{ $info['desc'] }}</p>
                @endif
            </div>
            <div class="p-5 space-y-4">
                @foreach($items as $setting)
                <div>
                    <x-form-label :for="'setting_' . $setting->id">{{ $setting->label ?? $setting->key }}</x-form-label>

                    @if($setting->type === 'textarea')
                        <textarea
                            id="setting_{{ $setting->id }}"
                            name="settings[{{ $setting->key }}]"
                            rows="4"
                            class="w-full px-3 py-2 rounded-lg bg-[var(--surface)] border border-[var(--border)] text-[var(--text)] text-sm placeholder:text-[var(--text-subtle)] focus:border-[var(--accent)] focus:ring-0 focus:outline-none transition-colors font-mono">{{ $setting->value }}</textarea>

                    @elseif($setting->type === 'svg')
                        <textarea
                            id="setting_{{ $setting->id }}"
                            name="settings[{{ $setting->key }}]"
                            rows="6"
                            placeholder='<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="..."/></svg>'
                            class="w-full px-3 py-2 rounded-lg bg-[var(--surface)] border border-[var(--border)] text-[var(--text)] text-xs placeholder:text-[var(--text-subtle)] focus:border-[var(--accent)] focus:ring-0 focus:outline-none transition-colors font-mono">{{ $setting->value }}</textarea>
                        @if(!empty($setting->value))
                        <div class="mt-2 inline-flex items-center gap-3 rounded-lg bg-[var(--surface)] border border-[var(--border)] px-3 py-2">
                            <span class="text-[10px] uppercase tracking-wider text-[var(--text-muted)] font-bold">Preview</span>
                            <span class="h-8 w-8 rounded-md bg-[var(--accent)] flex items-center justify-center text-white">
                                {!! $setting->value !!}
                            </span>
                            <span class="h-8 w-8 rounded-md bg-slate-900 flex items-center justify-center text-white">
                                {!! $setting->value !!}
                            </span>
                        </div>
                        @endif
                        <p class="text-[10px] text-[var(--text-muted)] mt-1.5">Paste markup &lt;svg&gt;...&lt;/svg&gt;. Akan di-sanitize otomatis saat simpan.</p>

                    @elseif($setting->type === 'image')
                        @php
                            $hasFile = $setting->value && \Illuminate\Support\Facades\Storage::disk('public')->exists($setting->value);
                            $previewUrl = $hasFile
                                ? asset('storage/' . $setting->value) . '?v=' . filemtime(storage_path('app/public/' . $setting->value))
                                : null;
                            // Map setting key -> upload field name. e.g. store.favicon_path -> favicon_source
                            $uploadFieldMap = [
                                'store.favicon_path' => 'favicon_source',
                                'store.og_image_path' => 'og_image',
                            ];
                            $uploadField = $uploadFieldMap[$setting->key] ?? str_replace(['store.', '_path'], '', $setting->key);
                        @endphp
                        @if($hasFile)
                        <div class="mb-2 inline-flex items-center gap-3 rounded-lg bg-[var(--surface)] border border-[var(--border)] p-2">
                            <img src="{{ $previewUrl }}" class="h-14 w-14 rounded object-cover bg-slate-900" alt="Current">
                            <div class="min-w-0">
                                <p class="text-xs text-[var(--text)] font-mono truncate">{{ basename($setting->value) }}</p>
                                <p class="text-[10px] text-[var(--text-muted)]">Upload baru untuk replace</p>
                            </div>
                        </div>
                        @endif
                        <input type="file"
                            id="setting_{{ $setting->id }}"
                            name="{{ $uploadField }}"
                            accept="image/png,image/jpeg"
                            class="block w-full text-xs text-[var(--text-muted)] file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[var(--accent)] file:text-white hover:file:bg-[var(--accent-hover)] file:cursor-pointer cursor-pointer">
                        <p class="text-[10px] text-[var(--text-muted)] mt-1.5">
                            @if($setting->key === 'store.favicon_path')
                                PNG/JPG square min 512x512, maks 1MB. Otomatis di-resize ke 32, 180, 192, 512 + favicon.ico.
                            @elseif($setting->key === 'store.og_image_path')
                                PNG/JPG min 600x315 (rekomendasi 1200x630), maks 2MB. Tampil saat link di-share di WhatsApp/Facebook.
                            @else
                                PNG/JPG dengan ukuran sesuai kebutuhan.
                            @endif
                        </p>

                    @elseif($setting->type === 'color')
                        <div class="flex gap-2 items-center">
                            <input type="color"
                                value="{{ $setting->value ?: '#020617' }}"
                                oninput="document.getElementById('setting_{{ $setting->id }}').value = this.value"
                                class="h-9 w-14 rounded-lg cursor-pointer border border-[var(--border)] bg-transparent">
                            <input
                                id="setting_{{ $setting->id }}"
                                type="text"
                                name="settings[{{ $setting->key }}]"
                                value="{{ $setting->value }}"
                                pattern="^#[0-9a-fA-F]{6}$"
                                placeholder="#020617"
                                class="flex-1 h-9 px-3 rounded-lg bg-[var(--surface)] border border-[var(--border)] text-[var(--text)] text-sm placeholder:text-[var(--text-subtle)] focus:border-[var(--accent)] focus:ring-0 focus:outline-none transition-colors font-mono">
                        </div>

                    @else
                        <input
                            id="setting_{{ $setting->id }}"
                            type="{{ $setting->type === 'tel' ? 'tel' : ($setting->type === 'url' ? 'url' : ($setting->type === 'email' ? 'email' : 'text')) }}"
                            name="settings[{{ $setting->key }}]"
                            value="{{ $setting->value }}"
                            class="w-full h-9 px-3 rounded-lg bg-[var(--surface)] border border-[var(--border)] text-[var(--text)] text-sm placeholder:text-[var(--text-subtle)] focus:border-[var(--accent)] focus:ring-0 focus:outline-none transition-colors">
                    @endif

                    <p class="text-[10px] text-[var(--text-subtle)] font-mono mt-1.5">{{ $setting->key }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <div class="flex items-center gap-2 sticky bottom-4 bg-[var(--bg)] py-2 -mx-1 px-1 z-10">
            <x-btn type="submit" variant="primary" size="lg" icon="M5 13l4 4L19 7">
                Simpan Pengaturan
            </x-btn>
            <x-btn :href="route('dashboard')" variant="secondary" size="lg">Batal</x-btn>
        </div>
    </form>

    {{-- Info card --}}
    <div class="card p-4 bg-[var(--accent-soft)] border-[var(--accent)]/20">
        <div class="flex items-start gap-3">
            <span class="icon-ring icon-ring-accent shrink-0">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
            <div>
                <p class="text-sm font-semibold text-[var(--text)]">Cara Kerja</p>
                <p class="text-xs text-[var(--text-muted)] mt-1">
                    Nilai pengaturan disimpan di database dan auto-cache. Perubahan langsung berlaku ke seluruh aplikasi
                    (sidebar logo, header copy stok, kontak landing page) tanpa perlu deploy ulang.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
