@extends('layouts.app')
@section('title', 'Tampilan & Tema')

@php
    use App\Http\Controllers\ThemeSettingsController as TC;

    $tokens = TC::COLOR_TOKENS;

    // Default fallback warna kalau setting kosong (cocok dgn app.css)
    $defaultLight = [
        'accent' => '#4f46e5', 'bg' => '#fafaf9', 'surface' => '#ffffff',
        'text' => '#0a0a0a', 'success' => '#059669', 'warning' => '#d97706',
        'danger' => '#dc2626', 'info' => '#0284c7',
    ];
    $defaultDark = [
        'accent' => '#6366f1', 'bg' => '#0a0a0a', 'surface' => '#141414',
        'text' => '#fafafa', 'success' => '#10b981', 'warning' => '#f59e0b',
        'danger' => '#ef4444', 'info' => '#0ea5e9',
    ];

    $light = [];
    $dark = [];
    foreach ($tokens as $t) {
        $light[$t] = $values["theme.light.$t"] ?? '';
        $dark[$t]  = $values["theme.dark.$t"]  ?? '';
    }
    $mode = $values['theme.mode_default'] ?? 'light';
    $preset = $values['theme.preset'] ?? 'indigo';
    $radius = $values['theme.layout.radius'] ?? 'md';
    $density = $values['theme.layout.density'] ?? 'normal';
    $fontFamily = $values['theme.layout.font_family'] ?? 'inter';
    $sidebarVariant = $values['theme.layout.sidebar_variant'] ?? 'subtle';
    $reduceMotion = ($values['theme.layout.reduce_motion'] ?? '0') === '1';
    $userPresets = $userPresets ?? [];
@endphp

@section('content')
<div class="space-y-6"
     x-data="themeCustomizer({
        light: @js($light),
        dark: @js($dark),
        defaultLight: @js($defaultLight),
        defaultDark: @js($defaultDark),
        presets: @js($presets),
        mode: @js($mode),
        preset: @js($preset),
        radius: @js($radius),
        density: @js($density),
        fontFamily: @js($fontFamily),
        sidebarVariant: @js($sidebarVariant),
        reduceMotion: @js($reduceMotion),
     })">

    <x-page-header eyebrow="Pengaturan" title="Tampilan & Tema" subtitle="Atur warna, tipografi, dan layout dashboard. Live preview di samping membantu kamu mencoba sebelum menyimpan.">
    </x-page-header>

    <form method="POST" action="{{ route('settings.theme.update') }}" class="space-y-5" id="theme-form">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">
            {{-- ============ KIRI: FORM ============ --}}
            <div class="lg:col-span-3 space-y-5">

                {{-- Preset --}}
                <div class="card overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-[var(--border)]">
                        <h3 class="text-sm font-semibold text-[var(--text)] section-bar">Preset Warna</h3>
                        <p class="text-xs text-[var(--text-muted)] mt-1">Pilih salah satu untuk auto-isi warna accent. Boleh override per-warna di bawah.</p>
                    </div>
                    <div class="p-5 grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                        @foreach($presets as $key => $p)
                        <button type="button"
                            @click="applyPreset('{{ $key }}')"
                            :class="preset === '{{ $key }}' ? 'border-[var(--accent)] ring-2 ring-[var(--accent-soft)]' : 'border-[var(--border)] hover:border-[var(--border-hover)]'"
                            class="relative text-left rounded-lg border bg-[var(--surface)] p-3 transition-all">
                            <div class="flex items-center gap-2 mb-1.5">
                                <span class="h-5 w-5 rounded-md shrink-0" style="background: {{ $p['light']['accent'] }}"></span>
                                <span class="h-5 w-5 rounded-md shrink-0" style="background: {{ $p['dark']['accent'] }}"></span>
                                <p class="text-sm font-semibold text-[var(--text)]">{{ $p['label'] }}</p>
                            </div>
                            <p class="text-[11px] text-[var(--text-muted)] line-clamp-2">{{ $p['description'] }}</p>
                        </button>
                        @endforeach

                        <button type="button"
                            @click="preset = 'custom'"
                            :class="preset === 'custom' ? 'border-[var(--accent)] ring-2 ring-[var(--accent-soft)]' : 'border-[var(--border)] hover:border-[var(--border-hover)]'"
                            class="relative text-left rounded-lg border bg-[var(--surface)] p-3 transition-all">
                            <div class="flex items-center gap-2 mb-1.5">
                                <svg class="h-5 w-5 text-[var(--text-muted)]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                                <p class="text-sm font-semibold text-[var(--text)]">Custom</p>
                            </div>
                            <p class="text-[11px] text-[var(--text-muted)]">Atur warna sendiri di bawah.</p>
                        </button>
                    </div>
                    <input type="hidden" name="settings[theme.preset]" :value="preset">
                </div>

                {{-- User Custom Presets --}}
                @if(count($userPresets) > 0)
                <div class="card overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-[var(--border)]">
                        <h3 class="text-sm font-semibold text-[var(--text)] section-bar">Preset Tersimpan</h3>
                        <p class="text-xs text-[var(--text-muted)] mt-1">Preset yang kamu simpan sebelumnya.</p>
                    </div>
                    <div class="p-5 space-y-2">
                        @foreach($userPresets as $slug => $up)
                        <div class="flex items-center gap-3 rounded-lg border border-[var(--border)] bg-[var(--surface-2)] px-3 py-2">
                            <div class="flex items-center gap-1.5 shrink-0">
                                <span class="h-4 w-4 rounded shrink-0" style="background: {{ $up['colors']['theme.light.accent'] ?? '#4f46e5' }}"></span>
                                <span class="h-4 w-4 rounded shrink-0" style="background: {{ $up['colors']['theme.dark.accent'] ?? '#6366f1' }}"></span>
                            </div>
                            <p class="text-sm font-semibold text-[var(--text)] truncate flex-1">{{ $up['label'] }}</p>
                            <button type="button"
                                @click="userPresetSlug = '{{ $slug }}'; $nextTick(() => document.getElementById('user-preset-apply-form').submit())"
                                class="text-[11px] font-semibold text-[var(--accent)] hover:underline">Pakai</button>
                            <button type="button"
                                @click="if (confirm('Hapus preset ini?')) { userPresetSlug = '{{ $slug }}'; $nextTick(() => document.getElementById('user-preset-delete-form').submit()) }"
                                class="text-[11px] font-semibold text-[var(--text-muted)] hover:text-[var(--danger)]">Hapus</button>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Mode default --}}
                <div class="card overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-[var(--border)]">
                        <h3 class="text-sm font-semibold text-[var(--text)] section-bar">Mode Default</h3>
                        <p class="text-xs text-[var(--text-muted)] mt-1">Mode awal saat user pertama kali buka dashboard.</p>
                    </div>
                    <div class="p-5 grid grid-cols-3 gap-2">
                        @foreach(['light' => ['Terang', 'M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z'], 'dark' => ['Gelap', 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z'], 'system' => ['Ikut Sistem', 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z']] as $key => $info)
                        <label class="cursor-pointer" :class="mode === '{{ $key }}' ? 'ring-2 ring-[var(--accent-soft)] rounded-lg' : ''">
                            <input type="radio" name="settings[theme.mode_default]" value="{{ $key }}" x-model="mode" class="sr-only">
                            <div :class="mode === '{{ $key }}' ? 'border-[var(--accent)] bg-[var(--accent-soft)] text-[var(--text)]' : 'border-[var(--border)] text-[var(--text-muted)]'"
                                class="rounded-lg border p-3 text-center transition-all hover:border-[var(--border-hover)]">
                                <svg class="h-5 w-5 mx-auto mb-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $info[1] }}"/></svg>
                                <p class="text-xs font-semibold">{{ $info[0] }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Color tabs Light/Dark --}}
                <div class="card overflow-hidden" x-data="{ activeTab: 'light' }">
                    <div class="px-5 py-3.5 border-b border-[var(--border)] flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-[var(--text)] section-bar">Warna Token</h3>
                            <p class="text-xs text-[var(--text-muted)] mt-1">Override per-token. Kosongkan untuk pakai default.</p>
                        </div>
                        <div class="flex rounded-lg bg-[var(--surface-2)] p-0.5 text-xs">
                            <button type="button" @click="activeTab = 'light'" :class="activeTab === 'light' ? 'bg-[var(--surface)] shadow-sm text-[var(--text)]' : 'text-[var(--text-muted)]'" class="px-3 py-1 rounded-md font-semibold transition-colors">Light</button>
                            <button type="button" @click="activeTab = 'dark'" :class="activeTab === 'dark' ? 'bg-[var(--surface)] shadow-sm text-[var(--text)]' : 'text-[var(--text-muted)]'" class="px-3 py-1 rounded-md font-semibold transition-colors">Dark</button>
                        </div>
                    </div>

                    <div class="p-5 space-y-3" x-show="activeTab === 'light'">
                        @foreach($tokens as $t)
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider w-20 shrink-0">{{ $t }}</span>
                            <input type="color"
                                :value="light.{{ $t }} || '{{ $defaultLight[$t] }}'"
                                @input="light.{{ $t }} = $event.target.value; preset = 'custom'"
                                class="h-9 w-14 rounded-lg cursor-pointer border border-[var(--border)] bg-transparent shrink-0">
                            <input type="text"
                                name="settings[theme.light.{{ $t }}]"
                                x-model="light.{{ $t }}"
                                @input="preset = 'custom'"
                                placeholder="{{ $defaultLight[$t] }}"
                                class="flex-1 h-9 px-3 rounded-lg bg-[var(--surface)] border border-[var(--border)] text-[var(--text)] text-sm placeholder:text-[var(--text-subtle)] focus:border-[var(--accent)] focus:ring-0 focus:outline-none transition-colors font-mono">
                            <button type="button" @click="light.{{ $t }} = ''" class="text-[10px] text-[var(--text-muted)] hover:text-[var(--danger)] px-1 shrink-0">Reset</button>
                        </div>
                        @endforeach
                    </div>

                    <div class="p-5 space-y-3" x-show="activeTab === 'dark'" x-cloak>
                        @foreach($tokens as $t)
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider w-20 shrink-0">{{ $t }}</span>
                            <input type="color"
                                :value="dark.{{ $t }} || '{{ $defaultDark[$t] }}'"
                                @input="dark.{{ $t }} = $event.target.value; preset = 'custom'"
                                class="h-9 w-14 rounded-lg cursor-pointer border border-[var(--border)] bg-transparent shrink-0">
                            <input type="text"
                                name="settings[theme.dark.{{ $t }}]"
                                x-model="dark.{{ $t }}"
                                @input="preset = 'custom'"
                                placeholder="{{ $defaultDark[$t] }}"
                                class="flex-1 h-9 px-3 rounded-lg bg-[var(--surface)] border border-[var(--border)] text-[var(--text)] text-sm placeholder:text-[var(--text-subtle)] focus:border-[var(--accent)] focus:ring-0 focus:outline-none transition-colors font-mono">
                            <button type="button" @click="dark.{{ $t }} = ''" class="text-[10px] text-[var(--text-muted)] hover:text-[var(--danger)] px-1 shrink-0">Reset</button>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Layout --}}
                <div class="card overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-[var(--border)]">
                        <h3 class="text-sm font-semibold text-[var(--text)] section-bar">Layout & Tipografi</h3>
                        <p class="text-xs text-[var(--text-muted)] mt-1">Kelengkungan, kepadatan, font, dan gaya sidebar.</p>
                    </div>
                    <div class="p-5 space-y-5">

                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-[var(--text-muted)] mb-2">Border Radius</p>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach(['sm' => 'Tajam', 'md' => 'Normal', 'lg' => 'Bulat'] as $key => $label)
                                <label class="cursor-pointer">
                                    <input type="radio" name="settings[theme.layout.radius]" value="{{ $key }}" x-model="radius" class="sr-only">
                                    <div :class="radius === '{{ $key }}' ? 'border-[var(--accent)] bg-[var(--accent-soft)] text-[var(--text)]' : 'border-[var(--border)] text-[var(--text-muted)]'"
                                        class="rounded-lg border p-3 text-center transition-all hover:border-[var(--border-hover)]">
                                        <div class="h-6 w-12 mx-auto mb-1.5 bg-[var(--accent)]" style="border-radius: {{ $key === 'sm' ? '4px' : ($key === 'md' ? '8px' : '14px') }}"></div>
                                        <p class="text-xs font-semibold">{{ $label }}</p>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-[var(--text-muted)] mb-2">Density</p>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach(['compact' => 'Compact', 'normal' => 'Normal', 'comfortable' => 'Longgar'] as $key => $label)
                                <label class="cursor-pointer">
                                    <input type="radio" name="settings[theme.layout.density]" value="{{ $key }}" x-model="density" class="sr-only">
                                    <div :class="density === '{{ $key }}' ? 'border-[var(--accent)] bg-[var(--accent-soft)] text-[var(--text)]' : 'border-[var(--border)] text-[var(--text-muted)]'"
                                        class="rounded-lg border p-3 text-center transition-all hover:border-[var(--border-hover)]">
                                        <div class="space-y-1 mb-1.5 mt-1">
                                            <div class="h-1 bg-[var(--text-subtle)] rounded" style="width: 80%; margin-left: auto; margin-right: auto;"></div>
                                            <div class="h-1 bg-[var(--text-subtle)] rounded" style="width: 60%; margin-left: auto; margin-right: auto;"></div>
                                            <div class="h-1 bg-[var(--text-subtle)] rounded" style="width: 70%; margin-left: auto; margin-right: auto;"></div>
                                        </div>
                                        <p class="text-xs font-semibold">{{ $label }}</p>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-[var(--text-muted)] mb-2">Font Family</p>
                            <select name="settings[theme.layout.font_family]" x-model="fontFamily"
                                class="w-full h-9 px-3 rounded-lg bg-[var(--surface)] border border-[var(--border)] text-[var(--text)] text-sm focus:border-[var(--accent)] focus:ring-0 focus:outline-none transition-colors">
                                @foreach($fonts as $key => $info)
                                <option value="{{ $key }}">{{ $info['label'] }}</option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-[var(--text-subtle)] mt-1.5">Font dimuat dari Google Fonts kecuali "System Default".</p>
                        </div>

                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-[var(--text-muted)] mb-2">Sidebar Variant</p>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach(['subtle' => 'Subtle', 'solid' => 'Solid', 'accent-tint' => 'Accent'] as $key => $label)
                                <label class="cursor-pointer">
                                    <input type="radio" name="settings[theme.layout.sidebar_variant]" value="{{ $key }}" x-model="sidebarVariant" class="sr-only">
                                    <div :class="sidebarVariant === '{{ $key }}' ? 'border-[var(--accent)] bg-[var(--accent-soft)] text-[var(--text)]' : 'border-[var(--border)] text-[var(--text-muted)]'"
                                        class="rounded-lg border p-3 text-center transition-all hover:border-[var(--border-hover)]">
                                        <p class="text-xs font-semibold">{{ $label }}</p>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Reduce Motion --}}
                        <div>
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox"
                                    x-model="reduceMotion"
                                    class="mt-0.5 h-4 w-4 rounded border-[var(--border)] text-[var(--accent)] focus:ring-[var(--accent)]">
                                <div>
                                    <p class="text-sm font-semibold text-[var(--text)]">Reduce Motion</p>
                                    <p class="text-[11px] text-[var(--text-muted)] mt-0.5">Matikan animasi pulse, fade, dan transisi. Hemat baterai dan ramah untuk user yang sensitif gerakan.</p>
                                </div>
                            </label>
                            <input type="hidden" name="settings[theme.layout.reduce_motion]" :value="reduceMotion ? '1' : '0'">
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 sticky bottom-4 bg-[var(--bg)] py-2 -mx-1 px-1 z-10">
                    <x-btn type="submit" variant="primary" size="lg" icon="M5 13l4 4L19 7">Simpan Tema</x-btn>
                    <x-btn :href="route('dashboard')" variant="secondary" size="lg">Batal</x-btn>

                    <div class="ml-auto flex flex-wrap items-center gap-2 relative">
                        <button type="button" @click.stop="openSave = !openSave; openImport = false"
                            class="inline-flex items-center gap-1.5 h-9 px-3 rounded-lg bg-transparent border border-[var(--border)] text-[var(--text-muted)] hover:bg-[var(--surface-2)] hover:text-[var(--text)] text-xs font-semibold transition-colors">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                            Simpan Preset
                        </button>
                        <a href="{{ route('settings.theme.export') }}"
                            class="inline-flex items-center gap-1.5 h-9 px-3 rounded-lg bg-transparent border border-[var(--border)] text-[var(--text-muted)] hover:bg-[var(--surface-2)] hover:text-[var(--text)] text-xs font-semibold transition-colors">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            Export
                        </a>
                        <button type="button" @click.stop="openImport = !openImport; openSave = false"
                            class="inline-flex items-center gap-1.5 h-9 px-3 rounded-lg bg-transparent border border-[var(--border)] text-[var(--text-muted)] hover:bg-[var(--surface-2)] hover:text-[var(--text)] text-xs font-semibold transition-colors">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            Import
                        </button>

                        {{-- Save Preset Popover (form di luar form utama, terhubung via attribute form="...") --}}
                        <div x-show="openSave" @click.away="openSave = false" x-transition x-cloak
                            class="absolute right-0 bottom-full mb-2 w-72 rounded-lg shadow-[var(--elev-2)] z-50 bg-[var(--surface)] border border-[var(--border)] p-3">
                            <p class="text-xs font-semibold text-[var(--text)] mb-2">Simpan Preset Custom</p>
                            <p class="text-[10px] text-[var(--text-muted)] mb-2.5">Simpan kombinasi warna saat ini sebagai preset yang bisa dipakai ulang. Maks 10 preset.</p>
                            <p class="text-[10px] text-[var(--warning)] mb-2.5"><strong>Catatan:</strong> Simpan tema dulu sebelum menyimpan sebagai preset.</p>
                            <div class="flex gap-1.5">
                                <input type="text" form="preset-save-form" name="name" placeholder="Nama preset" maxlength="40" required
                                    class="flex-1 h-8 px-2 rounded-md text-xs bg-[var(--surface-2)] border border-[var(--border)] text-[var(--text)]">
                                <button type="submit" form="preset-save-form" class="h-8 px-3 rounded-md bg-[var(--accent)] text-white text-xs font-semibold">Simpan</button>
                            </div>
                        </div>

                        {{-- Import Popover --}}
                        <div x-show="openImport" @click.away="openImport = false" x-transition x-cloak
                            class="absolute right-0 bottom-full mb-2 w-72 rounded-lg shadow-[var(--elev-2)] z-50 bg-[var(--surface)] border border-[var(--border)] p-3">
                            <p class="text-xs font-semibold text-[var(--text)] mb-2">Import Tema dari JSON</p>
                            <p class="text-[10px] text-[var(--text-muted)] mb-2.5">Upload file <code class="bg-[var(--surface-2)] px-1 rounded">.json</code> hasil export sebelumnya.</p>
                            <div class="space-y-1.5">
                                <input type="file" form="theme-import-form" name="theme_file" accept="application/json,.json" required
                                    class="w-full text-xs text-[var(--text-muted)] file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:bg-[var(--surface-2)] file:text-xs file:font-semibold file:text-[var(--text)]">
                                <button type="submit" form="theme-import-form" class="w-full h-8 rounded-md bg-[var(--accent)] text-white text-xs font-semibold">Upload &amp; Apply</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============ KANAN: LIVE PREVIEW ============ --}}
            <div class="lg:col-span-2">
                <div class="lg:sticky lg:top-16 space-y-3">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-[var(--text-muted)]">Live Preview</p>
                        <div class="flex items-center gap-1.5">
                            <div class="flex rounded-md bg-[var(--surface-2)] p-0.5 text-[10px]">
                                <button type="button" @click="previewSize = 'desktop'" :class="previewSize === 'desktop' ? 'bg-[var(--surface)] shadow-sm text-[var(--text)]' : 'text-[var(--text-muted)]'" class="px-2 py-0.5 rounded font-semibold transition-colors" title="Desktop">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </button>
                                <button type="button" @click="previewSize = 'mobile'" :class="previewSize === 'mobile' ? 'bg-[var(--surface)] shadow-sm text-[var(--text)]' : 'text-[var(--text-muted)]'" class="px-2 py-0.5 rounded font-semibold transition-colors" title="Mobile">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                </button>
                            </div>
                            <div class="flex rounded-md bg-[var(--surface-2)] p-0.5 text-[10px]">
                                <button type="button" @click="previewMode = 'light'" :class="previewMode === 'light' ? 'bg-[var(--surface)] shadow-sm text-[var(--text)]' : 'text-[var(--text-muted)]'" class="px-2.5 py-0.5 rounded font-semibold transition-colors">Light</button>
                                <button type="button" @click="previewMode = 'dark'" :class="previewMode === 'dark' ? 'bg-[var(--surface)] shadow-sm text-[var(--text)]' : 'text-[var(--text-muted)]'" class="px-2.5 py-0.5 rounded font-semibold transition-colors">Dark</button>
                            </div>
                        </div>
                    </div>

                    {{-- Contrast Warnings --}}
                    <div x-show="contrastWarnings.length > 0" x-cloak class="rounded-lg border border-[var(--warning)] bg-[var(--warning-soft)] p-2.5">
                        <div class="flex items-start gap-2">
                            <svg class="h-3.5 w-3.5 mt-0.5 shrink-0 text-[var(--warning)]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <div class="min-w-0">
                                <p class="text-[11px] font-semibold text-[var(--warning)]">Peringatan Kontras (WCAG AA)</p>
                                <ul class="text-[10px] text-[var(--text-muted)] mt-0.5 space-y-0.5">
                                    <template x-for="w in contrastWarnings" :key="w.label">
                                        <li>• <span x-text="w.label"></span>: ratio <span class="num font-semibold" x-text="w.ratio.toFixed(2)"></span> (min 4.5)</li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl overflow-hidden border border-[var(--border)] mx-auto transition-all"
                        :class="previewMode === 'dark' ? 'preview-scope dark' : 'preview-scope'"
                        :style="{ ...previewStyle(), maxWidth: previewSize === 'mobile' ? '320px' : '100%' }">

                        <div class="flex" style="background: var(--bg);" :class="previewSize === 'mobile' ? 'flex-col' : ''">
                            {{-- Mini sidebar --}}
                            <div :class="previewSize === 'mobile' ? 'w-full p-2 flex gap-1 overflow-x-auto' : 'w-32 shrink-0 p-2 space-y-0.5'"
                                :style="`background: ${ sidebarVariant === 'solid' ? 'var(--surface)' : sidebarVariant === 'accent-tint' ? 'var(--accent-soft)' : 'var(--surface-2)' }; border-${ previewSize === 'mobile' ? 'bottom' : 'right' }: 1px solid var(--border);`">
                                <div class="flex items-center gap-1.5 px-2 py-1.5 mb-0 shrink-0" :class="previewSize === 'mobile' ? '' : 'mb-1'">
                                    <div class="h-5 w-5 rounded shrink-0" style="background: var(--accent);"></div>
                                    <span class="text-[10px] font-bold whitespace-nowrap" style="color: var(--text);">Brand</span>
                                </div>
                                <div class="px-2 py-1.5 text-[10px] font-semibold rounded-md whitespace-nowrap shrink-0" style="background: var(--accent-soft); color: var(--text);">
                                    <span style="color: var(--accent);">●</span> Beranda
                                </div>
                                <div class="px-2 py-1.5 text-[10px] rounded-md whitespace-nowrap shrink-0" style="color: var(--text-muted);">○ Stok</div>
                                <div class="px-2 py-1.5 text-[10px] rounded-md whitespace-nowrap shrink-0" style="color: var(--text-muted);">○ Joki</div>
                                <div class="px-2 py-1.5 text-[10px] rounded-md whitespace-nowrap shrink-0" style="color: var(--text-muted);">○ Profit</div>
                            </div>

                            {{-- Mini content --}}
                            <div class="flex-1 p-3 space-y-2.5 min-w-0" style="background: var(--bg); color: var(--text);">
                                <div class="flex items-center justify-between">
                                    <p class="text-[11px] font-bold" style="color: var(--text);">Dashboard</p>
                                    <button type="button" class="text-[9px] font-semibold px-2 py-0.5 rounded-md text-white shrink-0"
                                        :style="`background: var(--accent); border-radius: var(--r-sm);`">Aksi</button>
                                </div>

                                <div class="grid grid-cols-2 gap-1.5">
                                    <div class="p-2" :style="`background: var(--surface); border: 1px solid var(--border); border-radius: var(--r-md);`">
                                        <p class="text-[8px] uppercase tracking-wider" style="color: var(--text-subtle);">Pendapatan</p>
                                        <p class="text-[11px] font-bold mt-0.5 num" style="color: var(--text);">Rp 4.2jt</p>
                                        <span class="inline-block mt-1 text-[8px] font-semibold px-1 rounded" style="background: var(--success-soft); color: var(--success);">+12%</span>
                                    </div>
                                    <div class="p-2" :style="`background: var(--surface); border: 1px solid var(--border); border-radius: var(--r-md);`">
                                        <p class="text-[8px] uppercase tracking-wider" style="color: var(--text-subtle);">Profit</p>
                                        <p class="text-[11px] font-bold mt-0.5 num" style="color: var(--text);">Rp 1.8jt</p>
                                        <span class="inline-block mt-1 text-[8px] font-semibold px-1 rounded" style="background: var(--accent-soft); color: var(--accent);">Hot</span>
                                    </div>
                                </div>

                                <div class="p-2" :style="`background: var(--surface); border: 1px solid var(--border); border-radius: var(--r-md);`">
                                    <p class="text-[10px] font-semibold mb-1.5" style="color: var(--text);">Status Joki</p>
                                    <div class="space-y-1">
                                        <div class="flex items-center justify-between text-[9px]">
                                            <span style="color: var(--text-muted);">Pelanggan A</span>
                                            <span class="px-1.5 py-0.5 rounded font-semibold" style="background: var(--info-soft); color: var(--info);">Proses</span>
                                        </div>
                                        <div class="flex items-center justify-between text-[9px]">
                                            <span style="color: var(--text-muted);">Pelanggan B</span>
                                            <span class="px-1.5 py-0.5 rounded font-semibold" style="background: var(--warning-soft); color: var(--warning);">Antri</span>
                                        </div>
                                        <div class="flex items-center justify-between text-[9px]">
                                            <span style="color: var(--text-muted);">Pelanggan C</span>
                                            <span class="px-1.5 py-0.5 rounded font-semibold" style="background: var(--success-soft); color: var(--success);">Selesai</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Form input sample --}}
                                <div class="p-2 space-y-1.5" :style="`background: var(--surface); border: 1px solid var(--border); border-radius: var(--r-md);`">
                                    <p class="text-[8px] uppercase tracking-wider font-bold" style="color: var(--text-muted);">Input Sample</p>
                                    <input type="text" value="Contoh teks" readonly
                                        class="w-full text-[9px] px-2 py-1 outline-none"
                                        :style="`background: var(--surface); border: 1px solid var(--border); border-radius: var(--r-sm); color: var(--text);`">
                                </div>

                                <div class="flex gap-1">
                                    <button type="button" class="flex-1 h-6 text-[9px] font-semibold text-white"
                                        :style="`background: var(--accent); border-radius: var(--r-sm);`">Primary</button>
                                    <button type="button" class="flex-1 h-6 text-[9px] font-semibold"
                                        :style="`background: transparent; border: 1px solid var(--border); color: var(--text); border-radius: var(--r-sm);`">Outline</button>
                                    <button type="button" class="flex-1 h-6 text-[9px] font-semibold text-white"
                                        :style="`background: var(--danger); border-radius: var(--r-sm);`">Danger</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-[10px] text-[var(--text-subtle)] px-1">
                        <p>Preview menggunakan token CSS yang sama dengan dashboard sungguhan. Border-radius mengikuti pengaturan radius global.</p>
                    </div>

                    <button type="submit" form="theme-reset-form"
                        onclick="return confirm('Reset tema ke default? Semua perubahan warna akan hilang.')"
                        class="w-full text-xs text-[var(--text-muted)] hover:text-[var(--danger)] py-2 transition-colors">
                        Reset ke Default
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- ===========================================================
         Form-form pendamping di LUAR form utama (HTML5: hindari nested forms).
         Tombol/input di dalam wrapper Alpine pakai attribute form="..."
         untuk asosiasi remote.
         =========================================================== --}}

    <form id="preset-save-form" method="POST" action="{{ route('settings.theme.preset.save') }}" class="hidden">
        @csrf
    </form>

    <form id="theme-import-form" method="POST" action="{{ route('settings.theme.import') }}" enctype="multipart/form-data" class="hidden"
        onsubmit="return confirm('Import akan menimpa setting tema saat ini. Lanjutkan?')">
        @csrf
    </form>

    <form id="theme-reset-form" method="POST" action="{{ route('settings.theme.reset') }}" class="hidden">
        @csrf
    </form>

    <form id="user-preset-apply-form" method="POST" action="{{ route('settings.theme.preset.apply') }}" class="hidden">
        @csrf
        <input type="hidden" name="slug" :value="userPresetSlug">
    </form>

    <form id="user-preset-delete-form" method="POST" action="{{ route('settings.theme.preset.delete') }}" class="hidden">
        @csrf
        <input type="hidden" name="slug" :value="userPresetSlug">
    </form>
</div>

<script>
function themeCustomizer(initial) {
    return {
        light: { ...initial.defaultLight, ...stripEmpty(initial.light) },
        dark: { ...initial.defaultDark, ...stripEmpty(initial.dark) },
        defaultLight: initial.defaultLight,
        defaultDark: initial.defaultDark,
        presets: initial.presets,
        mode: initial.mode || 'light',
        preset: initial.preset || 'indigo',
        radius: initial.radius || 'md',
        density: initial.density || 'normal',
        fontFamily: initial.fontFamily || 'inter',
        sidebarVariant: initial.sidebarVariant || 'subtle',
        reduceMotion: !!initial.reduceMotion,
        previewMode: initial.mode === 'dark' ? 'dark' : 'light',
        previewSize: 'desktop',
        openSave: false,
        openImport: false,
        userPresetSlug: '',

        applyPreset(key) {
            this.preset = key;
            const p = this.presets[key];
            if (!p) return;
            // Hanya overwrite accent (sesuai design preset)
            if (p.light && p.light.accent) this.light.accent = p.light.accent;
            if (p.dark && p.dark.accent) this.dark.accent = p.dark.accent;
        },

        get contrastWarnings() {
            const checks = [];
            const src = this.previewMode === 'dark' ? this.dark : this.light;
            const fb  = this.previewMode === 'dark' ? this.defaultDark : this.defaultLight;
            const get = (k) => src[k] || fb[k];

            const pairs = [
                { fg: get('text'), bg: get('bg'), label: 'Text on Background' },
                { fg: get('text'), bg: get('surface'), label: 'Text on Surface' },
                { fg: '#ffffff',   bg: get('accent'),  label: 'White on Accent' },
                { fg: '#ffffff',   bg: get('danger'),  label: 'White on Danger' },
                { fg: '#ffffff',   bg: get('success'), label: 'White on Success' },
            ];

            const out = [];
            pairs.forEach(p => {
                const r = contrastRatio(p.fg, p.bg);
                if (r < 4.5) out.push({ label: p.label, ratio: r });
            });
            return out;
        },

        previewStyle() {
            const src = this.previewMode === 'dark' ? this.dark : this.light;
            const fb  = this.previewMode === 'dark' ? this.defaultDark : this.defaultLight;
            const get = (k) => src[k] || fb[k];

            const accentRgb = hexToRgb(get('accent'));
            const successRgb = hexToRgb(get('success'));
            const warningRgb = hexToRgb(get('warning'));
            const dangerRgb = hexToRgb(get('danger'));
            const infoRgb = hexToRgb(get('info'));

            const isDark = this.previewMode === 'dark';
            const softAlpha = isDark ? 0.18 : 0.10;

            const radiusMap = { sm: '4px', md: '8px', lg: '14px' };
            const r = radiusMap[this.radius] || radiusMap.md;
            const radiusSmMap = { sm: '3px', md: '6px', lg: '10px' };
            const rSm = radiusSmMap[this.radius] || radiusSmMap.md;

            const surfaceFallback = isDark ? '#141414' : '#ffffff';
            const bgFallback = isDark ? '#0a0a0a' : '#fafaf9';
            const textFallback = isDark ? '#fafafa' : '#0a0a0a';
            const textMuted = isDark ? '#a3a3a3' : '#525252';
            const textSubtle = isDark ? '#737373' : '#a3a3a3';
            const borderColor = isDark ? '#262626' : '#e7e5e4';
            const surface2 = isDark ? '#1c1c1c' : '#f5f5f4';

            const accentHex = get('accent');
            const accentHover = isDark ? shadeHex(accentHex, 0.10) : shadeHex(accentHex, -0.08);

            return {
                '--accent': accentHex,
                '--accent-hover': accentHover,
                '--accent-soft': `rgba(${accentRgb}, ${softAlpha})`,
                '--bg': src.bg || bgFallback,
                '--surface': src.surface || surfaceFallback,
                '--surface-2': surface2,
                '--text': src.text || textFallback,
                '--text-muted': textMuted,
                '--text-subtle': textSubtle,
                '--border': borderColor,
                '--success': get('success'),
                '--success-soft': `rgba(${successRgb}, ${softAlpha})`,
                '--warning': get('warning'),
                '--warning-soft': `rgba(${warningRgb}, ${softAlpha})`,
                '--danger': get('danger'),
                '--danger-soft': `rgba(${dangerRgb}, ${softAlpha})`,
                '--info': get('info'),
                '--info-soft': `rgba(${infoRgb}, ${softAlpha})`,
                '--r-sm': rSm,
                '--r-md': r,
            };
        },
    };
}

function stripEmpty(obj) {
    const out = {};
    for (const k in obj) if (obj[k]) out[k] = obj[k];
    return out;
}

function hexToRgb(hex) {
    if (!hex || hex.length !== 7) return '0,0,0';
    const h = hex.replace('#', '');
    return parseInt(h.substring(0, 2), 16) + ',' + parseInt(h.substring(2, 4), 16) + ',' + parseInt(h.substring(4, 6), 16);
}

function shadeHex(hex, amount) {
    if (!hex || hex.length !== 7) return hex || '#000000';
    const h = hex.replace('#', '');
    let r = parseInt(h.substring(0, 2), 16);
    let g = parseInt(h.substring(2, 4), 16);
    let b = parseInt(h.substring(4, 6), 16);
    const adj = (c) => {
        const v = amount >= 0
            ? Math.round(c + (255 - c) * amount)
            : Math.round(c * (1 + amount));
        return Math.max(0, Math.min(255, v));
    };
    r = adj(r); g = adj(g); b = adj(b);
    return '#' + r.toString(16).padStart(2, '0') + g.toString(16).padStart(2, '0') + b.toString(16).padStart(2, '0');
}

// Relative luminance per WCAG 2.0
function luminance(hex) {
    if (!hex || hex.length !== 7) return 0;
    const h = hex.replace('#', '');
    const rgb = [
        parseInt(h.substring(0, 2), 16) / 255,
        parseInt(h.substring(2, 4), 16) / 255,
        parseInt(h.substring(4, 6), 16) / 255,
    ].map(v => v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4));
    return 0.2126 * rgb[0] + 0.7152 * rgb[1] + 0.0722 * rgb[2];
}

function contrastRatio(fg, bg) {
    const l1 = luminance(fg);
    const l2 = luminance(bg);
    const [a, b] = l1 > l2 ? [l1, l2] : [l2, l1];
    return (a + 0.05) / (b + 0.05);
}
</script>
@endsection
