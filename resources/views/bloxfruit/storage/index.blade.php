@extends('layouts.app')
@section('title', 'Akun Storage')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <form method="GET" class="flex gap-2">
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari browser / username..." class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <button type="submit" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Cari</button>
        @if(request('cari'))
        <a href="{{ route('bloxfruit.storage.index') }}" class="rounded-lg bg-gray-100 px-3 py-2 text-sm text-gray-500 hover:bg-gray-200">Reset</a>
        @endif
    </form>
    <div class="flex items-center gap-2">
        <button type="button" @click="$dispatch('open-modal-clear-all-stocks')" class="inline-flex items-center gap-1.5 rounded-lg bg-red-50 dark:bg-red-950/30 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Kosongkan Semua
        </button>
        <a href="{{ route('bloxfruit.storage.create') }}" class="btn-primary inline-flex items-center gap-2">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Akun
        </a>
    </div>
</div>

{{-- ============================================================
     Modal Konfirmasi Kosongkan Semua — ketik manual untuk safety
     ============================================================ --}}
<x-modal name="clear-all-stocks" title="Kosongkan Semua Stok">
    <div class="p-5" x-data="{ confirmText: '' }">
        <div class="rounded-lg bg-[var(--danger-soft)] border border-[var(--danger)]/20 p-4 mb-4">
            <div class="flex items-start gap-2.5">
                <svg class="h-5 w-5 text-[var(--danger)] shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div>
                    <p class="text-sm font-semibold text-[var(--danger)]">Aksi Tidak Bisa Dibatalkan</p>
                    <p class="text-xs text-[var(--text-muted)] mt-1 leading-relaxed">
                        Semua stok <strong>fruit, skin, gamepass, permanent</strong> dari <strong>SEMUA akun storage</strong>
                        akan dihapus permanent. Data keuangan, joki order, dan akun jual <strong>tidak terpengaruh</strong>.
                    </p>
                </div>
            </div>
        </div>

        <p class="text-sm text-[var(--text-muted)] mb-2">
            Untuk konfirmasi, ketik <code class="bg-[var(--surface-2)] px-2 py-0.5 rounded text-[var(--danger)] font-bold tracking-wide">HAPUS SEMUA STOK</code> di bawah:
        </p>
        <input
            type="text"
            x-model="confirmText"
            placeholder="Ketik di sini..."
            autocomplete="off"
            class="w-full h-10 px-3 rounded-lg bg-[var(--surface)] border border-[var(--border)] text-[var(--text)] text-sm placeholder:text-[var(--text-subtle)] focus:border-[var(--danger)] focus:ring-0 focus:outline-none transition-colors font-mono">

        <form method="POST" action="{{ route('bloxfruit.storage.clearAll') }}" class="flex gap-2 mt-4">
            @csrf
            @method('DELETE')
            <button
                type="submit"
                :disabled="confirmText !== 'HAPUS SEMUA STOK'"
                :class="confirmText === 'HAPUS SEMUA STOK' ? 'bg-[var(--danger)] hover:opacity-90 cursor-pointer' : 'bg-[var(--surface-2)] text-[var(--text-subtle)] cursor-not-allowed'"
                class="flex-1 h-10 rounded-lg font-semibold text-sm transition-all flex items-center justify-center gap-1.5"
                :class="confirmText === 'HAPUS SEMUA STOK' ? 'text-white' : ''">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Kosongkan Semua
            </button>
            <button type="button" @click="$dispatch('close-modal-clear-all-stocks'); confirmText = ''" class="h-10 px-4 rounded-lg border border-[var(--border)] text-[var(--text-muted)] hover:bg-[var(--surface-2)] text-sm font-semibold transition-colors">
                Batal
            </button>
        </form>
    </div>
</x-modal>

@php
    function getBrowserKey($name) {
        return str_contains(strtoupper($name), 'EDGE') ? 'EDGE' : 'CHROME';
    }
@endphp

@forelse($grouped as $browser => $accounts)
@php $bKey = getBrowserKey($browser); @endphp
<div class="mb-8">
    {{-- Browser header --}}
    <div class="rounded-xl p-3 px-4 mb-4 flex items-center justify-between bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 shadow-sm">
        <div class="flex items-center gap-3">
            @if($bKey === 'CHROME')
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #fbbf24, #ef4444, #22c55e);">
                <svg class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3.5" fill="currentColor"/></svg>
            </div>
            @else
            <div class="h-8 w-8 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #3b82f6, #06b6d4);">
                <svg class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
            </div>
            @endif
            <h2 class="text-base font-bold text-gray-900">{{ $browser }}</h2>
        </div>
        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">{{ $accounts->count() }} akun</span>
    </div>

    {{-- Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
        @foreach($accounts as $akun)
        @php $total = $akun->fruit_stocks_count + $akun->skin_stocks_count + $akun->gamepass_stocks_count + $akun->permanent_stocks_count; @endphp
        <div class="group relative rounded-2xl overflow-hidden transition-all duration-200 hover:-translate-y-0.5 hover:shadow-xl bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 shadow-sm">

            {{-- Card body - clickable --}}
            <a href="{{ route('bloxfruit.storage.show', $akun) }}" class="block p-4 pb-3">
                {{-- Username --}}
                <p class="text-sm font-bold text-gray-900 dark:text-gray-100 truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors mb-1">{{ $akun->username ?? '-' }}</p>
                <p class="text-[10px] text-gray-400 mb-2">Storage: {{ $akun->kapasitas_storage }}/item</p>

                {{-- Stats --}}
                <div class="grid grid-cols-2 gap-1.5">
                    <div class="rounded-lg py-1.5 px-2 text-center bg-indigo-50 dark:bg-indigo-950/30">
                        <p class="text-sm font-extrabold text-indigo-600 leading-none">{{ $akun->fruit_stocks_count ?: 0 }}</p>
                        <p class="text-[11px] text-indigo-400 mt-0.5">Fruit</p>
                    </div>
                    <div class="rounded-lg py-1.5 px-2 text-center bg-pink-50 dark:bg-pink-950/30">
                        <p class="text-sm font-extrabold text-pink-600 leading-none">{{ $akun->skin_stocks_count ?: 0 }}</p>
                        <p class="text-[11px] text-pink-400 mt-0.5">Skin</p>
                    </div>
                    <div class="rounded-lg py-1.5 px-2 text-center bg-blue-50 dark:bg-blue-950/30">
                        <p class="text-sm font-extrabold text-blue-600 leading-none">{{ $akun->gamepass_stocks_count ?: 0 }}</p>
                        <p class="text-[11px] text-blue-400 mt-0.5">Gamepass</p>
                    </div>
                    <div class="rounded-lg py-1.5 px-2 text-center bg-amber-50 dark:bg-amber-950/30">
                        <p class="text-sm font-extrabold text-amber-600 leading-none">{{ $akun->permanent_stocks_count ?: 0 }}</p>
                        <p class="text-[11px] text-amber-500 mt-0.5">Permanent</p>
                    </div>
                </div>
            </a>

            {{-- Footer actions --}}
            <div class="flex items-center border-t border-gray-100 dark:border-slate-700">
                <a href="{{ route('bloxfruit.storage.show', $akun) }}" class="flex-1 flex items-center justify-center gap-1 py-2 text-[11px] font-semibold text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-950/30 transition-colors">
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    Stok
                </a>
                <div class="w-px h-5 bg-gray-100 dark:bg-slate-700"></div>
                <a href="{{ route('bloxfruit.storage.edit', $akun) }}" class="flex items-center justify-center px-3 py-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </a>
                <div class="w-px h-5 bg-gray-100 dark:bg-slate-700"></div>
                <form method="POST" action="{{ route('bloxfruit.storage.destroy', $akun) }}" onsubmit="return confirm('Hapus {{ $akun->username }}?\nSemua stok ikut terhapus!')">
                    @csrf @method('DELETE')
                    <button type="submit" class="flex items-center justify-center px-3 py-2 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>
@empty
<div class="py-12 text-center text-sm text-gray-400">Belum ada akun storage</div>
@endforelse
@endsection
