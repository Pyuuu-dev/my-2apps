@extends('layouts.app')
@section('title', 'Daftar Buah')

@section('content')
<div x-data="stockPage(@js($fruitsForCopy), @js($skinsForCopy), @js($gamepassesForCopy), @js($permanentsForCopy))">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
        <p class="text-sm text-gray-500">{{ $fruits->total() }} buah &middot; Total stok: <span class="font-bold text-indigo-600">{{ $totalStok }}</span></p>
        <div class="flex items-center gap-2">
            <button @click="showCopy = true" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                Copy Teks
            </button>
            <a href="{{ route('bloxfruit.fruits.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah
            </a>
        </div>
    </div>

    {{-- Filter --}}
    <form method="GET" class="flex flex-wrap gap-2 mb-4">
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari buah..." class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-40">
        <select name="rarity" class="rounded-lg border-gray-300 text-sm shadow-sm" onchange="this.form.submit()">
            <option value="">Semua Rarity</option>
            @foreach(['Mythical','Legendary','Rare','Uncommon','Common'] as $r)
            <option value="{{ $r }}" {{ request('rarity') == $r ? 'selected' : '' }}>{{ $r }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-lg bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Filter</button>
    </form>

    {{-- Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
        @foreach($fruits as $fruit)
        @php
            $stok = $fruit->total_stok ?? 0;
            $rarityColor = match($fruit->rarity) {
                'Mythical' => 'border-fuchsia-300 dark:border-fuchsia-800',
                'Legendary' => 'border-amber-300 dark:border-amber-800',
                'Rare' => 'border-blue-300 dark:border-blue-800',
                'Uncommon' => 'border-emerald-300 dark:border-emerald-800',
                default => 'border-gray-200 dark:border-gray-700',
            };
            $rarityBadge = match($fruit->rarity) {
                'Mythical' => 'bg-fuchsia-100 text-fuchsia-700 dark:bg-fuchsia-900/30 dark:text-fuchsia-400',
                'Legendary' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                'Rare' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                'Uncommon' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                default => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
            };
        @endphp
        <div class="glass-card rounded-xl p-3 border-l-4 {{ $rarityColor }}">
            <div class="flex items-start justify-between mb-2">
                <div>
                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $fruit->nama }}</p>
                    <span class="inline-block rounded-full px-1.5 py-0.5 text-[9px] font-bold {{ $rarityBadge }}">{{ $fruit->rarity }}</span>
                </div>
                <div class="text-right">
                    <p class="text-lg font-black {{ $stok > 0 ? 'text-emerald-600' : 'text-gray-300' }}">{{ $stok }}</p>
                    <p class="text-[9px] text-gray-400">stok</p>
                </div>
            </div>
            <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-slate-700">
                <p class="text-xs font-semibold text-emerald-600">Rp {{ number_format($fruit->harga_jual, 0, ',', '.') }}</p>
                <div class="flex items-center gap-1.5">
                    <a href="{{ route('bloxfruit.fruits.edit', $fruit) }}" class="text-[10px] text-indigo-600 hover:text-indigo-800">Edit</a>
                    <form method="POST" action="{{ route('bloxfruit.fruits.destroy', $fruit) }}" onsubmit="return confirm('Hapus {{ $fruit->nama }}?')">
                        @csrf @method('DELETE')
                        <button class="text-[10px] text-red-500 hover:text-red-700">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $fruits->links() }}</div>

    @include('bloxfruit.partials.copy-stock-modal')
</div>

@include('bloxfruit.partials.copy-stock-script')
@endsection
