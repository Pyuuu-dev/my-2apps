@extends('layouts.app')
@section('title', 'Pengingat Telegram')

@section('content')
{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-lg font-bold text-gray-900">Pengingat via Telegram</h2>
        <p class="text-sm text-gray-500">Atur pengingat otomatis yang dikirim ke Telegram kamu</p>
    </div>
    <a href="{{ route('diet.reminders.create') }}" class="btn-success inline-flex items-center gap-1.5 text-sm">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Buat Pengingat
    </a>
</div>

{{-- Setup Telegram --}}
<div x-data="{ showSetup: {{ $telegramConfigured ? 'false' : 'true' }} }" class="mb-6">
    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="px-5 py-3 flex items-center justify-between cursor-pointer {{ $telegramConfigured ? 'border-b border-gray-100/50' : '' }}" @click="showSetup = !showSetup">
            <div class="flex items-center gap-3">
                <div class="h-9 w-9 rounded-lg flex items-center justify-center {{ $telegramConfigured ? 'bg-emerald-100' : 'bg-amber-100' }}">
                    @if($telegramConfigured)
                    <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    @else
                    <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    @endif
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900">Telegram Bot {{ $telegramConfigured ? '(Terhubung)' : '(Belum Setup)' }}</p>
                    <p class="text-[11px] text-gray-400">{{ $telegramConfigured ? 'Bot sudah terhubung dan siap mengirim pengingat' : 'Setup bot Telegram untuk menerima pengingat' }}</p>
                </div>
            </div>
            <svg class="h-4 w-4 text-gray-400 transition-transform" :class="showSetup && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </div>

        <div x-show="showSetup" x-collapse x-cloak>
            <div class="px-5 py-4">
                {{-- Panduan --}}
                <div class="rounded-xl bg-blue-50 border border-blue-200 p-4 mb-4">
                    <p class="text-xs font-bold text-blue-900 mb-2">Cara Setup Telegram Bot:</p>
                    <ol class="text-xs text-blue-800 space-y-1.5 list-decimal list-inside">
                        <li>Buka Telegram, cari <b>@BotFather</b></li>
                        <li>Kirim <code>/newbot</code> lalu ikuti instruksi untuk buat bot baru</li>
                        <li>Copy <b>Bot Token</b> yang diberikan BotFather</li>
                        <li>Buka bot yang baru dibuat, kirim pesan apa saja (misal: /start)</li>
                        <li>Buka browser: <code>https://api.telegram.org/bot&lt;TOKEN&gt;/getUpdates</code></li>
                        <li>Cari <b>"chat":{"id": XXXXXXX}</b> - itu Chat ID kamu</li>
                        <li>Paste keduanya di form di bawah</li>
                    </ol>
                </div>

                <form method="POST" action="{{ route('diet.reminders.telegram.config') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Bot Token</label>
                        <input type="text" name="bot_token" value="{{ config('services.telegram.bot_token') }}" placeholder="123456789:ABCdefGHIjklMNOpqrsTUVwxyz" class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Chat ID</label>
                        <input type="text" name="chat_id" value="{{ config('services.telegram.chat_id') }}" placeholder="123456789" class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                    </div>
                    <button type="submit" class="btn-success text-xs px-4 py-2">Simpan</button>
                </form>

                @if($telegramConfigured)
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <form method="POST" action="{{ route('diet.reminders.telegram.test') }}" class="inline">
                        @csrf
                        <button type="submit" class="rounded-lg bg-blue-100 px-4 py-2 text-xs font-medium text-blue-700 hover:bg-blue-200">Test Kirim Pesan</button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Preset Pengingat --}}
<div x-data="{ showPreset: false }" class="mb-6">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-bold text-gray-900">Template Pengingat</h3>
        <button @click="showPreset = !showPreset" class="text-xs font-medium text-emerald-600 hover:text-emerald-800" x-text="showPreset ? 'Tutup' : 'Lihat Template'"></button>
    </div>

    <div x-show="showPreset" x-collapse x-cloak>
        {{-- Tambah Semua --}}
        <div class="flex items-center gap-3 mb-4">
            <form method="POST" action="{{ route('diet.reminders.preset') }}">
                @csrf
                <input type="hidden" name="preset_index" value="all">
                <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-xs font-semibold text-white hover:bg-emerald-700">Tambah Semua ({{ count($presets) }} template)</button>
            </form>
            <span class="text-[11px] text-gray-400">atau pilih satu-satu di bawah</span>
        </div>

        {{-- Per Kategori --}}
        @php
            $catBg = ['makan' => 'from-orange-50 to-amber-50 border-orange-200', 'minum' => 'from-blue-50 to-cyan-50 border-blue-200', 'olahraga' => 'from-red-50 to-rose-50 border-red-200', 'timbang' => 'from-purple-50 to-violet-50 border-purple-200', 'tidur' => 'from-indigo-50 to-blue-50 border-indigo-200'];
        @endphp
        <div class="space-y-4">
            @foreach($presetCategories as $catKey => $cat)
            @if(count($cat['items']) > 0)
            <div x-data="{ open: false }">
                <div class="flex items-center justify-between rounded-t-xl bg-gradient-to-r {{ $catBg[$catKey] ?? 'from-gray-50 to-gray-50 border-gray-200' }} border px-4 py-2.5 cursor-pointer" @click="open = !open">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">{{ $cat['icon'] }}</span>
                        <div>
                            <p class="text-sm font-bold text-gray-900">{{ $cat['label'] }}</p>
                            <p class="text-[10px] text-gray-500">{{ count($cat['items']) }} template</p>
                        </div>
                    </div>
                    <svg class="h-4 w-4 text-gray-400 transition-transform" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
                <div x-show="open" x-collapse x-cloak class="border border-t-0 rounded-b-xl {{ $catBg[$catKey] ?? 'border-gray-200' }} overflow-hidden">
                    @foreach($cat['items'] as $preset)
                    @php $sudahAda = in_array($preset['judul'], $existingJudul); @endphp
                    <div class="flex items-center justify-between px-4 py-2.5 border-b border-white/50 last:border-b-0 {{ $sudahAda ? 'opacity-50' : '' }}">
                        <div class="flex-1 min-w-0 mr-3">
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-semibold text-gray-900">{{ $preset['judul'] }}</p>
                                <span class="shrink-0 rounded bg-white/80 px-1.5 py-0.5 text-[10px] font-bold text-gray-600">{{ $preset['waktu'] }}</span>
                                <span class="shrink-0 text-[10px] text-gray-400">{{ ucfirst(str_replace('_', ' ', $preset['hari_aktif'])) }}</span>
                            </div>
                            <p class="text-[11px] text-gray-500 mt-0.5 line-clamp-1">{{ $preset['pesan'] }}</p>
                        </div>
                        @if(!$sudahAda)
                        <form method="POST" action="{{ route('diet.reminders.preset') }}" class="shrink-0">
                            @csrf
                            <input type="hidden" name="preset_index" value="{{ $preset['index'] }}">
                            <button type="submit" class="rounded-lg bg-white/80 border border-gray-200 px-3 py-1 text-[11px] font-semibold text-emerald-700 hover:bg-emerald-100 hover:border-emerald-300">+ Tambah</button>
                        </form>
                        @else
                        <span class="shrink-0 inline-flex items-center gap-1 text-[10px] text-emerald-600">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Aktif
                        </span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            @endforeach
        </div>
    </div>
</div>

{{-- Daftar Pengingat Aktif --}}
<div class="flex items-center justify-between mb-3">
    <h3 class="text-sm font-bold text-gray-900">Pengingat Kamu ({{ $reminders->count() }})</h3>
    @if($reminders->count() > 0)
    <form method="POST" action="{{ route('diet.reminders.destroyAll') }}" onsubmit="return confirm('Hapus semua {{ $reminders->count() }} pengingat? Tindakan ini tidak bisa dibatalkan.')">
        @csrf
        @method('DELETE')
        <button type="submit" class="rounded-lg bg-red-100 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-200">Hapus Semua</button>
    </form>
    @endif
</div>

@if($reminders->isEmpty())
<div class="rounded-2xl bg-gray-50 border border-gray-200 p-8 text-center">
    <div class="inline-flex items-center justify-center h-12 w-12 rounded-2xl bg-gray-100 mb-3">
        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
    </div>
    <p class="text-sm font-medium text-gray-600 mb-1">Belum ada pengingat</p>
    <p class="text-xs text-gray-400 mb-3">Tambah pengingat dari template atau buat sendiri.</p>
</div>
@else
@php
    $icons = ['makan' => '🍽', 'olahraga' => '🏃', 'minum' => '💧', 'timbang' => '⚖️', 'tidur' => '😴'];
    $bgColors = ['makan' => 'bg-orange-50 border-orange-100', 'olahraga' => 'bg-red-50 border-red-100', 'minum' => 'bg-blue-50 border-blue-100', 'timbang' => 'bg-purple-50 border-purple-100', 'tidur' => 'bg-indigo-50 border-indigo-100'];
@endphp
<div class="space-y-2">
    @foreach($reminders as $rem)
    <div class="rounded-xl border p-4 {{ $rem->aktif ? ($bgColors[$rem->tipe] ?? 'bg-white border-gray-100') : 'bg-gray-50 border-gray-200 opacity-60' }}">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <span class="text-xl">{{ $icons[$rem->tipe] ?? '🔔' }}</span>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-bold text-gray-900 truncate">{{ $rem->judul }}</p>
                        <span class="shrink-0 text-lg font-extrabold text-gray-700">{{ \Carbon\Carbon::parse($rem->waktu)->format('H:i') }}</span>
                    </div>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="text-[11px] text-gray-500">{{ ucfirst(str_replace('_', ' ', $rem->hari_aktif)) }}</span>
                        @if($rem->last_sent_at)
                        <span class="text-[10px] text-gray-400">&middot; Terakhir: {{ $rem->last_sent_at->diffForHumans() }}</span>
                        @endif
                    </div>
                    @if($rem->pesan)
                    <p class="text-[11px] text-gray-500 mt-1 line-clamp-1">{{ $rem->pesan }}</p>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                {{-- Toggle --}}
                <form method="POST" action="{{ route('diet.reminders.toggle', $rem) }}">
                    @csrf @method('PATCH')
                    <button class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $rem->aktif ? 'bg-emerald-500' : 'bg-gray-300' }}">
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow {{ $rem->aktif ? 'translate-x-6' : 'translate-x-1' }}"></span>
                    </button>
                </form>
                {{-- Edit --}}
                <a href="{{ route('diet.reminders.edit', $rem) }}" class="rounded-md p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </a>
                {{-- Hapus --}}
                <form method="POST" action="{{ route('diet.reminders.destroy', $rem) }}" onsubmit="return confirm('Hapus pengingat {{ $rem->judul }}?')">
                    @csrf @method('DELETE')
                    <button class="rounded-md p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Info --}}
<div class="mt-6 rounded-xl bg-blue-50 border border-blue-100 p-4">
    <p class="text-xs font-bold text-blue-900 mb-2">Cara Kerja Pengingat:</p>
    <ul class="text-xs text-blue-800 space-y-1">
        <li>1. Pengingat dikirim otomatis ke Telegram sesuai waktu yang diatur</li>
        <li>2. Setiap pesan dilengkapi status diet hari ini (kalori, minum, berat)</li>
        <li>3. Ringkasan harian dikirim otomatis jam 21:00 setiap malam</li>
        <li>4. Matikan toggle untuk pause pengingat tanpa menghapusnya</li>
    </ul>
</div>
@endsection
