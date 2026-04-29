@extends('layouts.app')
@section('title', 'Skin Buah')

@section('content')
<div x-data="stockPage(@js($fruitsForCopy), @js($skinsForCopy), @js($gamepassesForCopy), @js($permanentsForCopy))">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
        <p class="text-sm text-gray-500">{{ $skins->total() }} skin &middot; Total stok: <span class="font-bold text-pink-600">{{ $totalStok }}</span></p>
        <div class="flex items-center gap-2">
            <button @click="showCopy = true" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                Copy Teks
            </button>
            <a href="{{ route('bloxfruit.skins.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah
            </a>
        </div>
    </div>

    <form method="GET" class="flex flex-wrap gap-2 mb-4">
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari skin..." class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-40">
        <select name="fruit" class="rounded-lg border-gray-300 text-sm shadow-sm" onchange="this.form.submit()">
            <option value="">Semua Buah</option>
            @foreach($fruits as $f)
            <option value="{{ $f->id }}" {{ request('fruit') == $f->id ? 'selected' : '' }}>{{ $f->nama }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-lg bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Filter</button>
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        @forelse($skins as $skin)
        <div class="glass-card rounded-xl p-3 border-l-4 border-pink-300 dark:border-pink-800">
            <div class="flex items-start justify-between mb-2">
                <div>
                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $skin->nama_skin }}</p>
                    <p class="text-[10px] text-gray-400">{{ $skin->fruit->nama ?? '-' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-lg font-black {{ ($skin->total_stok ?? 0) > 0 ? 'text-emerald-600' : 'text-gray-300' }}">{{ $skin->total_stok ?? 0 }}</p>
                    <p class="text-[9px] text-gray-400">stok</p>
                </div>
            </div>
            <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-slate-700">
                <p class="text-xs font-semibold text-emerald-600">Rp {{ number_format($skin->harga_jual, 0, ',', '.') }}</p>
                <div class="flex items-center gap-1.5">
                    <a href="{{ route('bloxfruit.skins.edit', $skin) }}" class="text-[10px] text-indigo-600 hover:text-indigo-800">Edit</a>
                    <form method="POST" action="{{ route('bloxfruit.skins.destroy', $skin) }}" onsubmit="return confirm('Hapus {{ $skin->nama_skin }}?')">
                        @csrf @method('DELETE')
                        <button class="text-[10px] text-red-500 hover:text-red-700">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-8 text-center text-sm text-gray-400">Belum ada data skin</div>
        @endforelse
    </div>
    <div class="mt-4">{{ $skins->links() }}</div>

    @include('bloxfruit.partials.copy-stock-modal')
</div>

@include('bloxfruit.partials.copy-stock-script')
@endsection
