@extends('layouts.app')
@section('title', 'Cari Stok')

@section('content')
<form method="GET" action="{{ route('bloxfruit.search') }}" x-data="{
    fruits: @js($fruitIds ?? []),
    skins: @js($skinIds ?? []),
    gamepasses: @js($gpIds ?? []),
    permanents: @js($permIds ?? []),
    tab: 'buah',
    addItem(list, val) { if (val && !this[list].includes(val)) this[list].push(val); },
    removeItem(list, idx) { this[list].splice(idx, 1); },
    fruitName(id) { return @js($allFruits->pluck('nama', 'id'))[id] || id; },
    skinName(id) { return @js($allSkins->pluck('nama_skin', 'id'))[id] || id; },
    gpName(id) { return @js($allGamepasses->pluck('nama', 'id'))[id] || id; },
    permName(id) { return @js($allPermanents->pluck('nama', 'id'))[id] || id; },
    get totalItems() { return this.fruits.length + this.skins.length + this.gamepasses.length + this.permanents.length; }
}">

{{-- Keranjang Pencarian --}}
<div class="rounded-xl bg-white shadow-sm border border-gray-100 p-4 mb-6">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-semibold text-gray-900">Item yang dicari <span class="text-gray-400" x-text="'(' + totalItems + ' item)'"></span></h3>
        <div class="flex items-center gap-3">
            <label class="flex items-center gap-1.5 text-sm">
                <input type="radio" name="mode" value="semua" {{ ($mode ?? 'semua') === 'semua' ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500">
                <span class="text-gray-700">Punya semua</span>
            </label>
            <label class="flex items-center gap-1.5 text-sm">
                <input type="radio" name="mode" value="sebagian" {{ ($mode ?? '') === 'sebagian' ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500">
                <span class="text-gray-700">Punya sebagian</span>
            </label>
        </div>
    </div>

    {{-- Selected items tags --}}
    <div class="flex flex-wrap gap-1.5 mb-4 min-h-[32px]" x-show="totalItems > 0">
        <template x-for="(id, i) in fruits" :key="'f'+id">
            <span class="inline-flex items-center gap-1 rounded-full bg-indigo-100 text-indigo-700 px-2.5 py-1 text-xs font-medium">
                <span x-text="fruitName(id)"></span>
                <button type="button" @click="removeItem('fruits', i)" class="hover:text-indigo-900">&times;</button>
                <input type="hidden" name="fruits[]" :value="id">
            </span>
        </template>
        <template x-for="(id, i) in skins" :key="'s'+id">
            <span class="inline-flex items-center gap-1 rounded-full bg-pink-100 text-pink-700 px-2.5 py-1 text-xs font-medium">
                <span x-text="skinName(id)"></span>
                <button type="button" @click="removeItem('skins', i)" class="hover:text-pink-900">&times;</button>
                <input type="hidden" name="skins[]" :value="id">
            </span>
        </template>
        <template x-for="(id, i) in gamepasses" :key="'g'+id">
            <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 text-blue-700 px-2.5 py-1 text-xs font-medium">
                <span x-text="gpName(id)"></span>
                <button type="button" @click="removeItem('gamepasses', i)" class="hover:text-blue-900">&times;</button>
                <input type="hidden" name="gamepasses[]" :value="id">
            </span>
        </template>
        <template x-for="(id, i) in permanents" :key="'p'+id">
            <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 text-amber-700 px-2.5 py-1 text-xs font-medium">
                Perm <span x-text="permName(id)"></span>
                <button type="button" @click="removeItem('permanents', i)" class="hover:text-amber-900">&times;</button>
                <input type="hidden" name="permanents[]" :value="id">
            </span>
        </template>
    </div>
    <p x-show="totalItems === 0" class="text-sm text-gray-400 mb-4">Pilih item dari tab di bawah untuk mulai mencari.</p>

    {{-- Tab selector --}}
    <div class="flex gap-1 border-b border-gray-200 mb-4">
        <button type="button" @click="tab='buah'" :class="tab==='buah' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-3 py-2 text-xs font-medium border-b-2">Buah</button>
        <button type="button" @click="tab='skin'" :class="tab==='skin' ? 'border-pink-600 text-pink-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-3 py-2 text-xs font-medium border-b-2">Skin</button>
        <button type="button" @click="tab='gamepass'" :class="tab==='gamepass' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-3 py-2 text-xs font-medium border-b-2">Gamepass</button>
        <button type="button" @click="tab='permanent'" :class="tab==='permanent' ? 'border-amber-600 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-3 py-2 text-xs font-medium border-b-2">Permanent</button>
    </div>

    {{-- Buah picker --}}
    <div x-show="tab==='buah'" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-7 gap-2">
        @php $cr = ''; @endphp
        @foreach($allFruits as $fruit)
            @if($fruit->rarity !== $cr)
                @php $cr = $fruit->rarity; @endphp
                <div class="col-span-full mt-2 first:mt-0">
                    <span class="text-[11px] font-bold uppercase tracking-wide {{ $cr === 'Mythical' ? 'text-red-600' : ($cr === 'Legendary' ? 'text-yellow-600' : ($cr === 'Rare' ? 'text-blue-600' : ($cr === 'Uncommon' ? 'text-green-600' : 'text-gray-500'))) }}">{{ $cr }}</span>
                </div>
            @endif
            <button type="button"
                @click="addItem('fruits', '{{ $fruit->id }}')"
                :class="fruits.includes('{{ $fruit->id }}') ? 'border-indigo-500 bg-indigo-50 ring-1 ring-indigo-500' : 'border-gray-200 bg-white hover:border-indigo-300 hover:bg-indigo-50'"
                class="rounded-lg border px-2 py-1.5 text-xs font-medium text-gray-800 text-center transition-all">
                {{ $fruit->nama }}
            </button>
        @endforeach
    </div>

    {{-- Skin picker --}}
    <div x-show="tab==='skin'" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2">
        @foreach($allSkins as $skin)
        <button type="button"
            @click="addItem('skins', '{{ $skin->id }}')"
            :class="skins.includes('{{ $skin->id }}') ? 'border-pink-500 bg-pink-50 ring-1 ring-pink-500' : 'border-gray-200 bg-white hover:border-pink-300 hover:bg-pink-50'"
            class="rounded-lg border px-2 py-1.5 text-xs font-medium text-gray-800 text-center transition-all">
            {{ $skin->nama_skin }}
            <span class="block text-[11px] text-gray-400">{{ $skin->fruit->nama ?? '' }}</span>
        </button>
        @endforeach
    </div>

    {{-- Gamepass picker --}}
    <div x-show="tab==='gamepass'" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
        @foreach($allGamepasses as $gp)
        <button type="button"
            @click="addItem('gamepasses', '{{ $gp->id }}')"
            :class="gamepasses.includes('{{ $gp->id }}') ? 'border-blue-500 bg-blue-50 ring-1 ring-blue-500' : 'border-gray-200 bg-white hover:border-blue-300 hover:bg-blue-50'"
            class="rounded-lg border px-2 py-1.5 text-xs font-medium text-gray-800 text-center transition-all">
            {{ $gp->nama }}
        </button>
        @endforeach
    </div>

    {{-- Permanent picker --}}
    <div x-show="tab==='permanent'" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-7 gap-2">
        @foreach($allPermanents as $perm)
        <button type="button"
            @click="addItem('permanents', '{{ $perm->id }}')"
            :class="permanents.includes('{{ $perm->id }}') ? 'border-amber-500 bg-amber-50 ring-1 ring-amber-500' : 'border-gray-200 bg-white hover:border-amber-300 hover:bg-amber-50'"
            class="rounded-lg border px-2 py-1.5 text-xs font-medium text-gray-800 text-center transition-all">
            {{ $perm->nama }}
            <span class="block text-[10px] text-gray-400">{{ number_format($perm->harga_robux) }} R$</span>
        </button>
        @endforeach
    </div>

    {{-- Search button --}}
    <div class="mt-4 pt-4 border-t border-gray-100">
        <button type="submit" x-bind:disabled="totalItems === 0"
            class="w-full rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 shadow-sm disabled:opacity-40 disabled:cursor-not-allowed">
            Cari Stok
        </button>
    </div>
</div>
</form>

{{-- ===================== HASIL PENCARIAN ===================== --}}
@if($hasSearch)
<div class="mb-4">
    <h3 class="text-base font-semibold text-gray-900 mb-1">Hasil Pencarian</h3>
    <p class="text-sm text-gray-500">
        Mencari {{ count($searchedItems) }} item, mode: <strong>{{ $mode === 'semua' ? 'punya semua' : 'punya sebagian' }}</strong>
        &mdash; ditemukan <strong>{{ $results->count() }}</strong> akun
    </p>
</div>

{{-- Item yang dicari --}}
<div class="flex flex-wrap gap-1.5 mb-4">
    @foreach($searchedItems as $item)
    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium
        {{ $item['tipe'] === 'Buah' ? 'bg-indigo-100 text-indigo-700' : ($item['tipe'] === 'Skin' ? 'bg-pink-100 text-pink-700' : ($item['tipe'] === 'Gamepass' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700')) }}">
        {{ $item['nama'] }}
    </span>
    @endforeach
</div>

@if($results->isEmpty())
<div class="rounded-xl bg-gray-50 border border-gray-200 p-8 text-center">
    <p class="text-gray-500">Tidak ada akun yang {{ $mode === 'semua' ? 'punya semua item' : 'punya item tersebut' }}.</p>
    @if($mode === 'semua')
    <p class="text-sm text-gray-400 mt-1">Coba ganti mode ke "Punya sebagian" untuk melihat akun yang punya beberapa item.</p>
    @endif
</div>
@else
<div class="space-y-3">
    @foreach($results as $akun)
    <div class="rounded-xl bg-white shadow-sm border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-3">
            <div>
                <a href="{{ route('bloxfruit.storage.show', $akun) }}" class="font-semibold text-gray-900 hover:text-indigo-600">{{ $akun->nama_akun }}</a>
                <span class="text-sm text-gray-500 ml-2">{{ $akun->username }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold {{ $akun->total_matched >= count($searchedItems) ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                    {{ $akun->total_matched }}/{{ count($searchedItems) }} cocok
                </span>
                <a href="{{ route('bloxfruit.storage.show', $akun) }}" class="text-xs text-indigo-600 hover:underline">Buka</a>
            </div>
        </div>

        {{-- Detail item yang ditemukan --}}
        <div class="flex flex-wrap gap-1.5">
            @foreach($akun->fruitStocks as $stock)
            <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 text-indigo-700 px-2 py-0.5 text-xs">
                {{ $stock->fruit->nama ?? '?' }} <span class="font-bold">x{{ $stock->jumlah }}</span>
            </span>
            @endforeach
            @foreach($akun->skinStocks as $stock)
            <span class="inline-flex items-center gap-1 rounded-full bg-pink-50 text-pink-700 px-2 py-0.5 text-xs">
                {{ $stock->skin->nama_skin ?? '?' }} <span class="font-bold">x{{ $stock->jumlah }}</span>
            </span>
            @endforeach
            @foreach($akun->gamepassStocks as $stock)
            <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 text-blue-700 px-2 py-0.5 text-xs">
                {{ $stock->gamepass->nama ?? '?' }} <span class="font-bold">x{{ $stock->jumlah }}</span>
            </span>
            @endforeach
            @foreach($akun->permanentStocks as $stock)
            <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 text-amber-700 px-2 py-0.5 text-xs">
                Perm {{ $stock->permanentPrice->nama ?? '?' }} <span class="font-bold">x{{ $stock->jumlah }}</span>
            </span>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endif
@endif
@endsection
