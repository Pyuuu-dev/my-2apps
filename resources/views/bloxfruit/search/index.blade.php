@extends('layouts.app')
@section('title', 'Cari Stok')

@section('content')
<div>

<x-page-header eyebrow="Pencarian" title="Cari Stok" subtitle="Cari akun storage yang punya item tertentu, atau yang masih punya slot kosong">
</x-page-header>

{{-- ============================================================
     MOBILE VIEW — Tampilan ringkas untuk HP (md:hidden)
     ============================================================ --}}
<div class="md:hidden" x-data="{
    mobileTab: '{{ $hasEmptySearch && !$hasSearch ? 'kosong' : 'punya' }}',
    pickerTab: 'buah',
    q: '',
    mode: '{{ $mode ?? 'semua' }}',
    showFilter: false,

    // Keranjang 'Punya Item'
    fruits: @js(array_values($fruitIds ?? [])),
    skins: @js(array_values($skinIds ?? [])),
    gamepasses: @js(array_values($gpIds ?? [])),
    permanents: @js(array_values($permIds ?? [])),

    // Keranjang 'Slot Kosong'
    e_fruits: @js(array_values($emptyFruitIds ?? [])),
    e_skins: @js(array_values($emptySkinIds ?? [])),
    e_gamepasses: @js(array_values($emptyGpIds ?? [])),
    e_permanents: @js(array_values($emptyPermIds ?? [])),

    fruitName(id) { return @js($allFruits->pluck('nama', 'id'))[id] || id; },
    skinName(id) { return @js($allSkins->pluck('nama_skin', 'id'))[id] || id; },
    gpName(id) { return @js($allGamepasses->pluck('nama', 'id'))[id] || id; },
    permName(id) { return @js($allPermanents->pluck('nama', 'id'))[id] || id; },

    listFor(type) {
        const prefix = this.mobileTab === 'kosong' ? 'e_' : '';
        const map = { fruit: 'fruits', skin: 'skins', gamepass: 'gamepasses', permanent: 'permanents' };
        return prefix + map[type];
    },
    isActive(id, type) { return this[this.listFor(type)].includes(String(id)); },
    toggleActive(id, type) {
        const k = this.listFor(type);
        const s = String(id);
        const i = this[k].indexOf(s);
        if (i >= 0) this[k].splice(i, 1); else this[k].push(s);
    },
    removeAt(type, idx) { this[this.listFor(type)].splice(idx, 1); },
    clearActive() {
        if (this.mobileTab === 'kosong') { this.e_fruits = []; this.e_skins = []; this.e_gamepasses = []; this.e_permanents = []; }
        else { this.fruits = []; this.skins = []; this.gamepasses = []; this.permanents = []; }
    },
    activeFruits() { return this.mobileTab === 'kosong' ? this.e_fruits : this.fruits; },
    activeSkins() { return this.mobileTab === 'kosong' ? this.e_skins : this.skins; },
    activeGp() { return this.mobileTab === 'kosong' ? this.e_gamepasses : this.gamepasses; },
    activePerm() { return this.mobileTab === 'kosong' ? this.e_permanents : this.permanents; },
    totalActive() { return this.activeFruits().length + this.activeSkins().length + this.activeGp().length + this.activePerm().length; },
    matches(name) {
        const q = (this.q || '').trim().toLowerCase();
        if (!q) return true;
        return String(name).toLowerCase().includes(q);
    }
}" x-cloak>

    {{-- Segmented control: Punya Item / Slot Kosong --}}
    <div class="grid grid-cols-2 gap-1 p-1 bg-[var(--surface-2)] rounded-xl mb-3 border border-[var(--border)]">
        <button type="button" @click="mobileTab='punya'; q=''"
            :class="mobileTab==='punya' ? 'bg-[var(--surface)] text-[var(--accent)] shadow-sm' : 'text-[var(--text-muted)]'"
            class="h-10 rounded-lg text-sm font-semibold transition-colors flex items-center justify-center gap-1.5">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <span>Punya Item</span>
        </button>
        <button type="button" @click="mobileTab='kosong'; q=''"
            :class="mobileTab==='kosong' ? 'bg-[var(--surface)] text-[var(--success)] shadow-sm' : 'text-[var(--text-muted)]'"
            class="h-10 rounded-lg text-sm font-semibold transition-colors flex items-center justify-center gap-1.5">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
            <span>Slot Kosong</span>
        </button>
    </div>

    {{-- Sub-title kontekstual --}}
    <p class="text-xs text-[var(--text-muted)] mb-3 px-1">
        <span x-show="mobileTab==='punya'">Cari akun yang sudah memiliki item.</span>
        <span x-show="mobileTab==='kosong'" x-cloak>Cari akun yang masih punya slot untuk item ini.</span>
    </p>

    {{-- Chip strip — sticky di bawah topbar saat scroll --}}
    <div class="sticky top-12 z-20 -mx-4 px-4 py-2 bg-[var(--bg)]/95 backdrop-blur border-b border-[var(--border)] mb-3"
         :class="totalActive() === 0 ? 'opacity-0 pointer-events-none h-0 py-0 border-0 overflow-hidden' : ''">
        <div class="flex items-center gap-2">
            <span class="chip shrink-0 text-xs">
                <span class="num font-bold" x-text="totalActive()"></span>
                <span class="ml-1">item</span>
            </span>
            <div class="flex-1 flex gap-1.5 overflow-x-auto whitespace-nowrap [&::-webkit-scrollbar]:hidden [scrollbar-width:none]">
                <template x-for="(id, i) in activeFruits()" :key="'mf'+id">
                    <span :class="mobileTab==='kosong' ? 'bg-[var(--success-soft)] text-[var(--success)]' : 'bg-[var(--accent-soft)] text-[var(--accent)]'"
                          class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium shrink-0">
                        <span x-text="fruitName(id)"></span>
                        <button type="button" @click="removeAt('fruit', i)" class="hover:opacity-70 leading-none text-base">&times;</button>
                    </span>
                </template>
                <template x-for="(id, i) in activeSkins()" :key="'ms'+id">
                    <span :class="mobileTab==='kosong' ? 'bg-[var(--success-soft)] text-[var(--success)]' : 'bg-pink-500/10 text-pink-600 dark:text-pink-400'"
                          class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium shrink-0">
                        <span x-text="skinName(id)"></span>
                        <button type="button" @click="removeAt('skin', i)" class="hover:opacity-70 leading-none text-base">&times;</button>
                    </span>
                </template>
                <template x-for="(id, i) in activeGp()" :key="'mg'+id">
                    <span :class="mobileTab==='kosong' ? 'bg-[var(--success-soft)] text-[var(--success)]' : 'bg-[var(--info-soft)] text-[var(--info)]'"
                          class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium shrink-0">
                        <span x-text="gpName(id)"></span>
                        <button type="button" @click="removeAt('gamepass', i)" class="hover:opacity-70 leading-none text-base">&times;</button>
                    </span>
                </template>
                <template x-for="(id, i) in activePerm()" :key="'mp'+id">
                    <span :class="mobileTab==='kosong' ? 'bg-[var(--success-soft)] text-[var(--success)]' : 'bg-[var(--warning-soft)] text-[var(--warning)]'"
                          class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium shrink-0">
                        Perm <span x-text="permName(id)"></span>
                        <button type="button" @click="removeAt('permanent', i)" class="hover:opacity-70 leading-none text-base">&times;</button>
                    </span>
                </template>
            </div>
            <button type="button" @click="clearActive()" class="text-[11px] text-[var(--danger)] hover:opacity-70 shrink-0 font-semibold">Hapus</button>
        </div>
    </div>

    {{-- Filter input --}}
    <div class="relative mb-3">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-[var(--text-subtle)]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="search" x-model="q" placeholder="Cari nama item…"
               class="w-full h-11 pl-10 pr-9 rounded-xl bg-[var(--surface)] border border-[var(--border)] text-sm text-[var(--text)] placeholder:text-[var(--text-subtle)] focus:outline-none focus:border-[var(--border-hover)]">
        <button type="button" x-show="q" @click="q=''" x-cloak
                class="absolute right-3 top-1/2 -translate-y-1/2 h-5 w-5 rounded-full bg-[var(--surface-2)] flex items-center justify-center text-[var(--text-muted)] hover:text-[var(--text)]">
            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- Tab pills (Buah / Skin / Gamepass / Permanent) --}}
    <div class="flex gap-1.5 overflow-x-auto [&::-webkit-scrollbar]:hidden [scrollbar-width:none] mb-3 -mx-4 px-4 pb-1">
        <button type="button" @click="pickerTab='buah'"
            :class="pickerTab==='buah' ? (mobileTab==='kosong' ? 'bg-[var(--success)] text-white' : 'bg-[var(--accent)] text-white') : 'bg-[var(--surface)] text-[var(--text-muted)] border border-[var(--border)]'"
            class="shrink-0 h-9 px-3.5 rounded-full text-xs font-semibold transition-colors flex items-center gap-1.5">
            Buah
            <span x-show="activeFruits().length > 0" x-cloak class="num bg-white/25 rounded-full px-1.5 text-[10px]" x-text="activeFruits().length"></span>
        </button>
        <button type="button" @click="pickerTab='skin'"
            :class="pickerTab==='skin' ? (mobileTab==='kosong' ? 'bg-[var(--success)] text-white' : 'bg-[var(--accent)] text-white') : 'bg-[var(--surface)] text-[var(--text-muted)] border border-[var(--border)]'"
            class="shrink-0 h-9 px-3.5 rounded-full text-xs font-semibold transition-colors flex items-center gap-1.5">
            Skin
            <span x-show="activeSkins().length > 0" x-cloak class="num bg-white/25 rounded-full px-1.5 text-[10px]" x-text="activeSkins().length"></span>
        </button>
        <button type="button" @click="pickerTab='gamepass'"
            :class="pickerTab==='gamepass' ? (mobileTab==='kosong' ? 'bg-[var(--success)] text-white' : 'bg-[var(--accent)] text-white') : 'bg-[var(--surface)] text-[var(--text-muted)] border border-[var(--border)]'"
            class="shrink-0 h-9 px-3.5 rounded-full text-xs font-semibold transition-colors flex items-center gap-1.5">
            Gamepass
            <span x-show="activeGp().length > 0" x-cloak class="num bg-white/25 rounded-full px-1.5 text-[10px]" x-text="activeGp().length"></span>
        </button>
        <button type="button" @click="pickerTab='permanent'"
            :class="pickerTab==='permanent' ? (mobileTab==='kosong' ? 'bg-[var(--success)] text-white' : 'bg-[var(--accent)] text-white') : 'bg-[var(--surface)] text-[var(--text-muted)] border border-[var(--border)]'"
            class="shrink-0 h-9 px-3.5 rounded-full text-xs font-semibold transition-colors flex items-center gap-1.5">
            Permanent
            <span x-show="activePerm().length > 0" x-cloak class="num bg-white/25 rounded-full px-1.5 text-[10px]" x-text="activePerm().length"></span>
        </button>
    </div>

    {{-- Picker grids --}}
    {{-- BUAH --}}
    <div x-show="pickerTab==='buah'" class="grid grid-cols-2 gap-2">
        @foreach($allFruits->groupBy('rarity') as $rarity => $fruitsByR)
            @php
                $rarityClass = match($rarity) {
                    'Mythical' => 'text-[var(--danger)]',
                    'Legendary' => 'text-[var(--warning)]',
                    'Rare' => 'text-[var(--info)]',
                    'Uncommon' => 'text-[var(--success)]',
                    default => 'text-[var(--text-subtle)]'
                };
                $namesJson = $fruitsByR->pluck('nama')->values()->toJson();
            @endphp
            <div class="col-span-full mt-2 first:mt-0 flex items-center gap-2"
                 x-show='{{ $namesJson }}.some(n => matches(n))'>
                <span class="text-[10px] font-bold uppercase tracking-wider {{ $rarityClass }}">{{ $rarity }}</span>
                <span class="flex-1 h-px bg-[var(--border)]"></span>
            </div>
            @foreach($fruitsByR as $fruit)
            <button type="button"
                x-show="matches({{ json_encode($fruit->nama) }})"
                @click="toggleActive('{{ $fruit->id }}', 'fruit')"
                :class="isActive('{{ $fruit->id }}', 'fruit')
                    ? (mobileTab==='kosong' ? 'border-[var(--success)] bg-[var(--success-soft)] text-[var(--success)]' : 'border-[var(--accent)] bg-[var(--accent-soft)] text-[var(--accent)]')
                    : 'border-[var(--border)] bg-[var(--surface)] text-[var(--text-muted)]'"
                class="min-h-[52px] rounded-xl border px-3 py-2 text-left transition-colors flex flex-col justify-center">
                <div class="text-sm font-semibold truncate">{{ $fruit->nama }}</div>
                <div class="text-[10px] text-[var(--text-subtle)] font-normal mt-0.5">{{ $fruit->rarity }}</div>
            </button>
            @endforeach
        @endforeach
    </div>

    {{-- SKIN --}}
    <div x-show="pickerTab==='skin'" x-cloak class="grid grid-cols-2 gap-2">
        @foreach($allSkins as $skin)
        <button type="button"
            x-show="matches({{ json_encode($skin->nama_skin) }}) || matches({{ json_encode($skin->fruit->nama ?? '') }})"
            @click="toggleActive('{{ $skin->id }}', 'skin')"
            :class="isActive('{{ $skin->id }}', 'skin')
                ? (mobileTab==='kosong' ? 'border-[var(--success)] bg-[var(--success-soft)] text-[var(--success)]' : 'border-pink-500 bg-pink-500/10 text-pink-600 dark:text-pink-400')
                : 'border-[var(--border)] bg-[var(--surface)] text-[var(--text-muted)]'"
            class="min-h-[52px] rounded-xl border px-3 py-2 text-left transition-colors flex flex-col justify-center">
            <div class="text-sm font-semibold truncate">{{ $skin->nama_skin }}</div>
            <div class="text-[10px] text-[var(--text-subtle)] font-normal truncate mt-0.5">{{ $skin->fruit->nama ?? '' }}</div>
        </button>
        @endforeach
    </div>

    {{-- GAMEPASS --}}
    <div x-show="pickerTab==='gamepass'" x-cloak class="grid grid-cols-2 gap-2">
        @foreach($allGamepasses as $gp)
        <button type="button"
            x-show="matches({{ json_encode($gp->nama) }})"
            @click="toggleActive('{{ $gp->id }}', 'gamepass')"
            :class="isActive('{{ $gp->id }}', 'gamepass')
                ? (mobileTab==='kosong' ? 'border-[var(--success)] bg-[var(--success-soft)] text-[var(--success)]' : 'border-[var(--info)] bg-[var(--info-soft)] text-[var(--info)]')
                : 'border-[var(--border)] bg-[var(--surface)] text-[var(--text-muted)]'"
            class="min-h-[52px] rounded-xl border px-3 py-2 text-left transition-colors flex items-center">
            <div class="text-sm font-semibold truncate w-full">{{ $gp->nama }}</div>
        </button>
        @endforeach
    </div>

    {{-- PERMANENT --}}
    <div x-show="pickerTab==='permanent'" x-cloak class="grid grid-cols-2 gap-2">
        @foreach($allPermanents as $perm)
        <button type="button"
            x-show="matches({{ json_encode($perm->nama) }})"
            @click="toggleActive('{{ $perm->id }}', 'permanent')"
            :class="isActive('{{ $perm->id }}', 'permanent')
                ? (mobileTab==='kosong' ? 'border-[var(--success)] bg-[var(--success-soft)] text-[var(--success)]' : 'border-[var(--warning)] bg-[var(--warning-soft)] text-[var(--warning)]')
                : 'border-[var(--border)] bg-[var(--surface)] text-[var(--text-muted)]'"
            class="min-h-[52px] rounded-xl border px-3 py-2 text-left transition-colors flex flex-col justify-center">
            <div class="text-sm font-semibold truncate">{{ $perm->nama }}</div>
            <div class="text-[10px] text-[var(--text-subtle)] font-normal num mt-0.5">{{ format_angka($perm->harga_robux) }} R$</div>
        </button>
        @endforeach
    </div>

    {{-- Mode (hanya tab Punya Item) --}}
    <div x-show="mobileTab==='punya'" class="mt-5 pt-4 border-t border-[var(--border)]">
        <p class="text-[11px] uppercase tracking-wider font-semibold text-[var(--text-subtle)] mb-2">Mode pencarian</p>
        <div class="grid grid-cols-2 gap-2">
            <label class="cursor-pointer" :class="mode==='semua' ? '' : ''">
                <input type="radio" x-model="mode" value="semua" class="peer sr-only">
                <div class="h-12 rounded-xl border-2 px-3 flex items-center justify-center text-sm font-semibold transition-colors peer-checked:border-[var(--accent)] peer-checked:bg-[var(--accent-soft)] peer-checked:text-[var(--accent)] border-[var(--border)] bg-[var(--surface)] text-[var(--text-muted)]">
                    Punya semua
                </div>
            </label>
            <label class="cursor-pointer">
                <input type="radio" x-model="mode" value="sebagian" class="peer sr-only">
                <div class="h-12 rounded-xl border-2 px-3 flex items-center justify-center text-sm font-semibold transition-colors peer-checked:border-[var(--accent)] peer-checked:bg-[var(--accent-soft)] peer-checked:text-[var(--accent)] border-[var(--border)] bg-[var(--surface)] text-[var(--text-muted)]">
                    Punya sebagian
                </div>
            </label>
        </div>
    </div>

    {{-- Spacer agar konten terakhir tidak ketutup CTA bar --}}
    <div class="h-28"></div>

    {{-- Sticky bottom CTA bar --}}
    <div class="fixed inset-x-0 bottom-0 z-30 px-4 pt-3 pb-[max(0.75rem,env(safe-area-inset-bottom))] bg-[var(--bg)]/95 backdrop-blur border-t border-[var(--border)]">
        {{-- Form: Punya Item --}}
        <form x-show="mobileTab==='punya'" method="GET" action="{{ route('bloxfruit.search') }}">
            <template x-for="id in fruits" :key="'sf'+id"><input type="hidden" name="fruits[]" :value="id"></template>
            <template x-for="id in skins" :key="'ss'+id"><input type="hidden" name="skins[]" :value="id"></template>
            <template x-for="id in gamepasses" :key="'sg'+id"><input type="hidden" name="gamepasses[]" :value="id"></template>
            <template x-for="id in permanents" :key="'sp'+id"><input type="hidden" name="permanents[]" :value="id"></template>
            <input type="hidden" name="mode" :value="mode">
            <button type="submit" :disabled="totalActive()===0"
                class="w-full h-12 rounded-xl bg-[var(--accent)] text-white font-semibold text-sm flex items-center justify-center gap-2 hover:bg-[var(--accent-hover)] disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                <span>Cari Stok</span>
                <span x-show="totalActive()>0" class="num bg-white/25 rounded-full px-2 py-0.5 text-xs" x-text="totalActive()"></span>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </button>
        </form>
        {{-- Form: Slot Kosong --}}
        <form x-show="mobileTab==='kosong'" x-cloak method="GET" action="{{ route('bloxfruit.search') }}">
            <template x-for="id in e_fruits" :key="'esf'+id"><input type="hidden" name="empty_fruits[]" :value="id"></template>
            <template x-for="id in e_skins" :key="'ess'+id"><input type="hidden" name="empty_skins[]" :value="id"></template>
            <template x-for="id in e_gamepasses" :key="'esg'+id"><input type="hidden" name="empty_gamepasses[]" :value="id"></template>
            <template x-for="id in e_permanents" :key="'esp'+id"><input type="hidden" name="empty_permanents[]" :value="id"></template>
            <button type="submit" :disabled="totalActive()===0"
                class="w-full h-12 rounded-xl bg-[var(--success)] text-white font-semibold text-sm flex items-center justify-center gap-2 hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed transition-opacity">
                <span>Cari Slot Kosong</span>
                <span x-show="totalActive()>0" class="num bg-white/25 rounded-full px-2 py-0.5 text-xs" x-text="totalActive()"></span>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </button>
        </form>
    </div>
</div>

{{-- ============================================================
     MOBILE — Hasil Pencarian
     ============================================================ --}}
@if($hasSearch || ($emptyResults !== null) || $hasEmptySearch)
<div class="md:hidden mt-4" id="m-hasil"
     x-data x-init="$nextTick(() => setTimeout(() => $el.scrollIntoView({behavior:'smooth', block:'start'}), 150))">

    {{-- Hasil 'Punya Item' --}}
    @if($hasSearch)
    <div class="mb-5">
        <div class="flex items-baseline justify-between gap-2 mb-2">
            <h3 class="text-base font-semibold text-[var(--text)] section-bar">Hasil — Punya Item</h3>
            <span class="text-[11px] text-[var(--text-subtle)] num">{{ $results->count() }} akun</span>
        </div>
        <p class="text-xs text-[var(--text-muted)] mb-3">
            <span class="num font-semibold text-[var(--text)]">{{ count($searchedItems) }}</span> item dicari · mode
            <span class="chip text-[10px] px-1.5 py-0.5">{{ $mode === 'semua' ? 'punya semua' : 'punya sebagian' }}</span>
        </p>

        <div class="flex flex-wrap gap-1 mb-3">
            @foreach($searchedItems as $item)
            @php
                $tone = match($item['tipe']) {
                    'Buah' => 'bg-[var(--accent-soft)] text-[var(--accent)]',
                    'Skin' => 'bg-pink-500/10 text-pink-600 dark:text-pink-400',
                    'Gamepass' => 'bg-[var(--info-soft)] text-[var(--info)]',
                    default => 'bg-[var(--warning-soft)] text-[var(--warning)]',
                };
            @endphp
            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium {{ $tone }}">{{ $item['nama'] }}</span>
            @endforeach
        </div>

        @if($results->isEmpty())
        <div class="card p-6">
            <x-empty-state icon="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" title="Tidak ditemukan"
                :message="$mode === 'semua' ? 'Tidak ada akun yang punya semua item. Coba ganti mode ke punya sebagian.' : 'Tidak ada akun yang punya item tersebut.'" />
        </div>
        @else
        <div class="space-y-2.5">
            @foreach($results as $akun)
            <a href="{{ route('bloxfruit.storage.show', $akun) }}" class="card p-3 block hover-lift">
                <div class="flex items-center justify-between gap-2 mb-2">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-[var(--text)] truncate">{{ $akun->nama_akun }}</p>
                        @if($akun->username)<p class="text-[11px] text-[var(--text-subtle)] truncate">{{ $akun->username }}</p>@endif
                    </div>
                    @php $matched = $akun->total_matched >= count($searchedItems); @endphp
                    <span class="rounded-full px-2 py-0.5 text-[11px] font-bold shrink-0 {{ $matched ? 'bg-[var(--success-soft)] text-[var(--success)]' : 'bg-[var(--warning-soft)] text-[var(--warning)]' }}">
                        <span class="num">{{ $akun->total_matched }}/{{ count($searchedItems) }}</span>
                    </span>
                </div>
                <div class="flex flex-wrap gap-1">
                    @foreach($akun->fruitStocks as $stock)
                    <span class="inline-flex items-center gap-1 rounded-full bg-[var(--accent-soft)] text-[var(--accent)] px-1.5 py-0.5 text-[10px]">
                        {{ $stock->fruit->nama ?? '?' }}<span class="font-bold num">×{{ $stock->jumlah }}</span>
                    </span>
                    @endforeach
                    @foreach($akun->skinStocks as $stock)
                    <span class="inline-flex items-center gap-1 rounded-full bg-pink-500/10 text-pink-600 dark:text-pink-400 px-1.5 py-0.5 text-[10px]">
                        {{ $stock->skin->nama_skin ?? '?' }}<span class="font-bold num">×{{ $stock->jumlah }}</span>
                    </span>
                    @endforeach
                    @foreach($akun->gamepassStocks as $stock)
                    <span class="inline-flex items-center gap-1 rounded-full bg-[var(--info-soft)] text-[var(--info)] px-1.5 py-0.5 text-[10px]">
                        {{ $stock->gamepass->nama ?? '?' }}<span class="font-bold num">×{{ $stock->jumlah }}</span>
                    </span>
                    @endforeach
                    @foreach($akun->permanentStocks as $stock)
                    <span class="inline-flex items-center gap-1 rounded-full bg-[var(--warning-soft)] text-[var(--warning)] px-1.5 py-0.5 text-[10px]">
                        Perm {{ $stock->permanentPrice->nama ?? '?' }}<span class="font-bold num">×{{ $stock->jumlah }}</span>
                    </span>
                    @endforeach
                </div>
            </a>
            @endforeach
        </div>
        @endif
    </div>
    @endif

    {{-- Hasil 'Slot Kosong' --}}
    @if($emptyResults !== null)
    <div class="mb-5">
        <div class="flex items-baseline justify-between gap-2 mb-2">
            <h3 class="text-base font-semibold text-[var(--text)] section-bar">Hasil — Slot Kosong</h3>
            <span class="text-[11px] text-[var(--text-subtle)] num">{{ $emptyResults->count() }} akun</span>
        </div>
        @php $totalSelectedEmpty = collect($searchedEmptyItems)->sum(fn($c) => $c->count()); @endphp
        <p class="text-xs text-[var(--text-muted)] mb-3"><span class="num font-semibold text-[var(--text)]">{{ $totalSelectedEmpty }}</span> item dicari</p>

        @if($emptyResults->count() > 0)
        <div class="space-y-2.5">
            @foreach($emptyResults as $r)
            <a href="{{ route('bloxfruit.storage.show', $r['akun']) }}" class="card p-3 block hover-lift">
                <div class="flex items-center justify-between gap-2 mb-2 pb-2 border-b border-[var(--border)]">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-[var(--text)] truncate">{{ $r['akun']->username ?? $r['akun']->nama_akun }}</p>
                        <p class="text-[10px] text-[var(--text-subtle)] truncate">{{ $r['akun']->nama_akun }}</p>
                    </div>
                    <span class="chip shrink-0 text-[11px]"><span class="num">{{ $r['total_available'] }}</span> slot</span>
                </div>

                @foreach($searchedEmptyItems['fruits'] as $sf)
                @php $d = $r['details']['fruits'][$sf->id] ?? ['current' => 0, 'available' => 0]; @endphp
                <div class="flex items-center gap-2 text-[11px] py-0.5">
                    <span class="dot dot-accent shrink-0"></span>
                    <span class="text-[var(--text-muted)] flex-1 truncate">{{ $sf->nama }}</span>
                    <div class="progress w-12 shrink-0">
                        <div class="progress-bar {{ $d['current'] >= $r['capacity'] ? 'progress-bar-danger' : '' }}" style="width: {{ round(($d['current'] / max(1, $r['capacity'])) * 100) }}%"></div>
                    </div>
                    <span class="num shrink-0 w-10 text-right {{ $d['available'] > 0 ? 'text-[var(--success)] font-bold' : 'text-[var(--text-subtle)]' }}">{{ $d['available'] > 0 ? '+' . $d['available'] : 'Penuh' }}</span>
                </div>
                @endforeach
                @foreach($searchedEmptyItems['skins'] as $ss)
                @php $d = $r['details']['skins'][$ss->id] ?? ['current' => 0, 'available' => 0]; @endphp
                <div class="flex items-center gap-2 text-[11px] py-0.5">
                    <span class="dot bg-pink-500 shrink-0"></span>
                    <span class="text-[var(--text-muted)] flex-1 truncate">{{ $ss->nama_skin }}</span>
                    <div class="progress w-12 shrink-0">
                        <div class="progress-bar {{ $d['current'] >= $r['capacity'] ? 'progress-bar-danger' : '' }}" style="width: {{ round(($d['current'] / max(1, $r['capacity'])) * 100) }}%; background: {{ $d['current'] > 0 && $d['current'] < $r['capacity'] ? '#ec4899' : '' }}"></div>
                    </div>
                    <span class="num shrink-0 w-10 text-right {{ $d['available'] > 0 ? 'text-[var(--success)] font-bold' : 'text-[var(--text-subtle)]' }}">{{ $d['available'] > 0 ? '+' . $d['available'] : 'Penuh' }}</span>
                </div>
                @endforeach
                @foreach($searchedEmptyItems['gamepasses'] as $sg)
                @php $d = $r['details']['gamepasses'][$sg->id] ?? ['current' => 0, 'available' => 0]; @endphp
                <div class="flex items-center gap-2 text-[11px] py-0.5">
                    <span class="dot dot-info shrink-0"></span>
                    <span class="text-[var(--text-muted)] flex-1 truncate">{{ $sg->nama }}</span>
                    <div class="progress w-12 shrink-0">
                        <div class="progress-bar progress-bar-info" style="width: {{ round(($d['current'] / max(1, $r['capacity'])) * 100) }}%"></div>
                    </div>
                    <span class="num shrink-0 w-10 text-right {{ $d['available'] > 0 ? 'text-[var(--success)] font-bold' : 'text-[var(--text-subtle)]' }}">{{ $d['available'] > 0 ? '+' . $d['available'] : 'Penuh' }}</span>
                </div>
                @endforeach
                @foreach($searchedEmptyItems['permanents'] as $sp)
                @php $d = $r['details']['permanents'][$sp->id] ?? ['current' => 0, 'available' => 0]; @endphp
                <div class="flex items-center gap-2 text-[11px] py-0.5">
                    <span class="dot dot-warning shrink-0"></span>
                    <span class="text-[var(--text-muted)] flex-1 truncate">{{ $sp->nama }}</span>
                    <div class="progress w-12 shrink-0">
                        <div class="progress-bar progress-bar-warning" style="width: {{ round(($d['current'] / max(1, $r['capacity'])) * 100) }}%"></div>
                    </div>
                    <span class="num shrink-0 w-10 text-right {{ $d['available'] > 0 ? 'text-[var(--success)] font-bold' : 'text-[var(--text-subtle)]' }}">{{ $d['available'] > 0 ? '+' . $d['available'] : 'Penuh' }}</span>
                </div>
                @endforeach

                <div class="flex items-center justify-between mt-2 pt-2 border-t border-[var(--border)]">
                    <span class="text-[10px] text-[var(--text-subtle)]">Kapasitas <span class="num font-semibold text-[var(--text-muted)]">{{ $r['capacity'] }}</span></span>
                    <span class="text-[10px] text-[var(--accent)] font-semibold">Buka →</span>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <div class="card p-6">
            <x-empty-state icon="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8" title="Semua akun penuh" message="Tidak ada akun dengan slot kosong untuk item yang dipilih." />
        </div>
        @endif
    </div>
    @elseif($hasEmptySearch)
    <div class="card p-6">
        <x-empty-state icon="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" message="Tidak ada hasil." />
    </div>
    @endif
</div>
@endif

{{-- ============================================================
     DESKTOP VIEW — Tampilan asli (hidden md:block)
     ============================================================ --}}
<div class="hidden md:block space-y-6">

{{-- ============================================================
     KERANJANG — Cari akun yang PUNYA item
     ============================================================ --}}
<form method="GET" action="{{ route('bloxfruit.search') }}" x-data="{
    fruits: @js(array_values($fruitIds ?? [])),
    skins: @js(array_values($skinIds ?? [])),
    gamepasses: @js(array_values($gpIds ?? [])),
    permanents: @js(array_values($permIds ?? [])),
    tab: 'buah',
    fruitName(id) { return @js($allFruits->pluck('nama', 'id'))[id] || id; },
    skinName(id) { return @js($allSkins->pluck('nama_skin', 'id'))[id] || id; },
    gpName(id) { return @js($allGamepasses->pluck('nama', 'id'))[id] || id; },
    permName(id) { return @js($allPermanents->pluck('nama', 'id'))[id] || id; },
    toggle(list, val) {
        const i = this[list].indexOf(val);
        if (i >= 0) this[list].splice(i, 1);
        else this[list].push(val);
    },
    remove(list, idx) { this[list].splice(idx, 1); },
    clearAll() { this.fruits = []; this.skins = []; this.gamepasses = []; this.permanents = []; },
    get totalItems() { return this.fruits.length + this.skins.length + this.gamepasses.length + this.permanents.length; }
}">
<div class="card p-5 accent-glow card-hairline">
    <div class="flex items-start justify-between gap-3 mb-4">
        <div>
            <h3 class="text-sm font-semibold text-[var(--text)] flex items-center gap-2 section-bar">Punya Item Tertentu</h3>
            <p class="text-xs text-[var(--text-muted)] mt-1">Cari akun yang sudah memiliki item-item berikut</p>
        </div>
        <span class="chip"><span class="num" x-text="totalItems"></span> item</span>
    </div>

    {{-- Selected items tags --}}
    <div class="flex flex-wrap gap-1.5 mb-4 min-h-[28px]" x-show="totalItems > 0" x-cloak>
        <template x-for="(id, i) in fruits" :key="'f'+id">
            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium bg-[var(--accent-soft)] text-[var(--accent)]">
                <span x-text="fruitName(id)"></span>
                <button type="button" @click="remove('fruits', i)" class="hover:opacity-70 leading-none">&times;</button>
                <input type="hidden" name="fruits[]" :value="id">
            </span>
        </template>
        <template x-for="(id, i) in skins" :key="'s'+id">
            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium bg-pink-500/10 text-pink-600 dark:text-pink-400">
                <span x-text="skinName(id)"></span>
                <button type="button" @click="remove('skins', i)" class="hover:opacity-70 leading-none">&times;</button>
                <input type="hidden" name="skins[]" :value="id">
            </span>
        </template>
        <template x-for="(id, i) in gamepasses" :key="'g'+id">
            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium bg-[var(--info-soft)] text-[var(--info)]">
                <span x-text="gpName(id)"></span>
                <button type="button" @click="remove('gamepasses', i)" class="hover:opacity-70 leading-none">&times;</button>
                <input type="hidden" name="gamepasses[]" :value="id">
            </span>
        </template>
        <template x-for="(id, i) in permanents" :key="'p'+id">
            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium bg-[var(--warning-soft)] text-[var(--warning)]">
                Perm <span x-text="permName(id)"></span>
                <button type="button" @click="remove('permanents', i)" class="hover:opacity-70 leading-none">&times;</button>
                <input type="hidden" name="permanents[]" :value="id">
            </span>
        </template>
        <button type="button" @click="clearAll()" class="text-[11px] text-[var(--text-subtle)] hover:text-[var(--danger)] ml-auto self-center">Hapus semua</button>
    </div>
    <p x-show="totalItems === 0" class="text-xs text-[var(--text-subtle)] mb-4">Pilih item dari tab di bawah untuk mulai mencari.</p>

    {{-- Mode --}}
    <div class="flex flex-wrap items-center gap-3 mb-4 pb-3 border-b border-[var(--border)]">
        <label class="text-[11px] uppercase tracking-wider font-semibold text-[var(--text-subtle)]">Mode:</label>
        <label class="flex items-center gap-1.5 text-sm cursor-pointer">
            <input type="radio" name="mode" value="semua" {{ ($mode ?? 'semua') === 'semua' ? 'checked' : '' }} class="text-[var(--accent)] focus:ring-[var(--accent)]">
            <span class="text-[var(--text-muted)]">Punya semua</span>
        </label>
        <label class="flex items-center gap-1.5 text-sm cursor-pointer">
            <input type="radio" name="mode" value="sebagian" {{ ($mode ?? '') === 'sebagian' ? 'checked' : '' }} class="text-[var(--accent)] focus:ring-[var(--accent)]">
            <span class="text-[var(--text-muted)]">Punya sebagian</span>
        </label>
    </div>

    {{-- Tab selector --}}
    <div class="flex gap-1 border-b border-[var(--border)] mb-4 overflow-x-auto">
        <button type="button" @click="tab='buah'" :class="tab==='buah' ? 'border-[var(--accent)] text-[var(--accent)]' : 'border-transparent text-[var(--text-muted)] hover:text-[var(--text)]'" class="px-3 py-2 text-xs font-semibold border-b-2 whitespace-nowrap transition-colors">
            Buah <span class="text-[var(--text-subtle)] num" x-text="'(' + fruits.length + ')'"></span>
        </button>
        <button type="button" @click="tab='skin'" :class="tab==='skin' ? 'border-[var(--accent)] text-[var(--accent)]' : 'border-transparent text-[var(--text-muted)] hover:text-[var(--text)]'" class="px-3 py-2 text-xs font-semibold border-b-2 whitespace-nowrap transition-colors">
            Skin <span class="text-[var(--text-subtle)] num" x-text="'(' + skins.length + ')'"></span>
        </button>
        <button type="button" @click="tab='gamepass'" :class="tab==='gamepass' ? 'border-[var(--accent)] text-[var(--accent)]' : 'border-transparent text-[var(--text-muted)] hover:text-[var(--text)]'" class="px-3 py-2 text-xs font-semibold border-b-2 whitespace-nowrap transition-colors">
            Gamepass <span class="text-[var(--text-subtle)] num" x-text="'(' + gamepasses.length + ')'"></span>
        </button>
        <button type="button" @click="tab='permanent'" :class="tab==='permanent' ? 'border-[var(--accent)] text-[var(--accent)]' : 'border-transparent text-[var(--text-muted)] hover:text-[var(--text)]'" class="px-3 py-2 text-xs font-semibold border-b-2 whitespace-nowrap transition-colors">
            Permanent <span class="text-[var(--text-subtle)] num" x-text="'(' + permanents.length + ')'"></span>
        </button>
    </div>

    {{-- Picker grids --}}
    <div x-show="tab==='buah'" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-7 gap-2">
        @php $cr = ''; @endphp
        @foreach($allFruits as $fruit)
            @if($fruit->rarity !== $cr)
                @php $cr = $fruit->rarity; @endphp
                @php $rarityClass = match($cr) { 'Mythical' => 'text-[var(--danger)]', 'Legendary' => 'text-[var(--warning)]', 'Rare' => 'text-[var(--info)]', 'Uncommon' => 'text-[var(--success)]', default => 'text-[var(--text-subtle)]' }; @endphp
                <div class="col-span-full mt-2 first:mt-0 flex items-center gap-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider {{ $rarityClass }}">{{ $cr }}</span>
                    <span class="flex-1 h-px bg-[var(--border)]"></span>
                </div>
            @endif
            <button type="button"
                @click="toggle('fruits', '{{ $fruit->id }}')"
                :class="fruits.includes('{{ $fruit->id }}') ? 'border-[var(--accent)] bg-[var(--accent-soft)] text-[var(--accent)]' : 'border-[var(--border)] bg-[var(--surface)] text-[var(--text-muted)] hover:border-[var(--border-hover)] hover:text-[var(--text)]'"
                class="rounded-lg border px-2 py-1.5 text-xs font-semibold text-center transition-colors">
                {{ $fruit->nama }}
            </button>
        @endforeach
    </div>

    <div x-show="tab==='skin'" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2">
        @foreach($allSkins as $skin)
        <button type="button"
            @click="toggle('skins', '{{ $skin->id }}')"
            :class="skins.includes('{{ $skin->id }}') ? 'border-pink-500 bg-pink-500/10 text-pink-600 dark:text-pink-400' : 'border-[var(--border)] bg-[var(--surface)] text-[var(--text-muted)] hover:border-[var(--border-hover)] hover:text-[var(--text)]'"
            class="rounded-lg border px-2 py-1.5 text-xs font-semibold text-center transition-colors">
            {{ $skin->nama_skin }}
            <span class="block text-[10px] text-[var(--text-subtle)] font-normal">{{ $skin->fruit->nama ?? '' }}</span>
        </button>
        @endforeach
    </div>

    <div x-show="tab==='gamepass'" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
        @foreach($allGamepasses as $gp)
        <button type="button"
            @click="toggle('gamepasses', '{{ $gp->id }}')"
            :class="gamepasses.includes('{{ $gp->id }}') ? 'border-[var(--info)] bg-[var(--info-soft)] text-[var(--info)]' : 'border-[var(--border)] bg-[var(--surface)] text-[var(--text-muted)] hover:border-[var(--border-hover)] hover:text-[var(--text)]'"
            class="rounded-lg border px-2 py-1.5 text-xs font-semibold text-center transition-colors">
            {{ $gp->nama }}
        </button>
        @endforeach
    </div>

    <div x-show="tab==='permanent'" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-7 gap-2">
        @foreach($allPermanents as $perm)
        <button type="button"
            @click="toggle('permanents', '{{ $perm->id }}')"
            :class="permanents.includes('{{ $perm->id }}') ? 'border-[var(--warning)] bg-[var(--warning-soft)] text-[var(--warning)]' : 'border-[var(--border)] bg-[var(--surface)] text-[var(--text-muted)] hover:border-[var(--border-hover)] hover:text-[var(--text)]'"
            class="rounded-lg border px-2 py-1.5 text-xs font-semibold text-center transition-colors">
            {{ $perm->nama }}
            <span class="block text-[10px] text-[var(--text-subtle)] font-normal num">{{ format_angka($perm->harga_robux) }} R$</span>
        </button>
        @endforeach
    </div>

    {{-- Submit --}}
    <div class="mt-5 pt-4 border-t border-[var(--border)]">
        <x-btn type="submit" variant="primary" size="lg" class="w-full" x-bind:disabled="totalItems === 0" iconRight="M17 8l4 4m0 0l-4 4m4-4H3">
            Cari Stok
        </x-btn>
    </div>
</div>
</form>

{{-- ============================================================
     CARI SLOT KOSONG — multi-tipe (mirip keranjang utama)
     ============================================================ --}}
<form method="GET" action="{{ route('bloxfruit.search') }}" x-data="{
    e_fruits: @js(array_values($emptyFruitIds ?? [])),
    e_skins: @js(array_values($emptySkinIds ?? [])),
    e_gamepasses: @js(array_values($emptyGpIds ?? [])),
    e_permanents: @js(array_values($emptyPermIds ?? [])),
    e_tab: 'buah',
    fruitName(id) { return @js($allFruits->pluck('nama', 'id'))[id] || id; },
    skinName(id) { return @js($allSkins->pluck('nama_skin', 'id'))[id] || id; },
    gpName(id) { return @js($allGamepasses->pluck('nama', 'id'))[id] || id; },
    permName(id) { return @js($allPermanents->pluck('nama', 'id'))[id] || id; },
    toggle(list, val) {
        const i = this[list].indexOf(val);
        if (i >= 0) this[list].splice(i, 1);
        else this[list].push(val);
    },
    remove(list, idx) { this[list].splice(idx, 1); },
    clearAll() { this.e_fruits = []; this.e_skins = []; this.e_gamepasses = []; this.e_permanents = []; },
    get totalItems() { return this.e_fruits.length + this.e_skins.length + this.e_gamepasses.length + this.e_permanents.length; }
}">
<div class="card p-5 card-hairline">
    <div class="flex items-start justify-between gap-3 mb-4">
        <div>
            <h3 class="text-sm font-semibold text-[var(--text)] flex items-center gap-2 section-bar">Cari Slot Kosong</h3>
            <p class="text-xs text-[var(--text-muted)] mt-1">Cari akun yang masih punya kapasitas untuk menyimpan item</p>
        </div>
        <span class="chip"><span class="num" x-text="totalItems"></span> item</span>
    </div>

    {{-- Selected items tags --}}
    <div class="flex flex-wrap gap-1.5 mb-4 min-h-[28px]" x-show="totalItems > 0" x-cloak>
        <template x-for="(id, i) in e_fruits" :key="'ef'+id">
            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium bg-[var(--accent-soft)] text-[var(--accent)]">
                <span x-text="fruitName(id)"></span>
                <button type="button" @click="remove('e_fruits', i)" class="hover:opacity-70 leading-none">&times;</button>
                <input type="hidden" name="empty_fruits[]" :value="id">
            </span>
        </template>
        <template x-for="(id, i) in e_skins" :key="'es'+id">
            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium bg-pink-500/10 text-pink-600 dark:text-pink-400">
                <span x-text="skinName(id)"></span>
                <button type="button" @click="remove('e_skins', i)" class="hover:opacity-70 leading-none">&times;</button>
                <input type="hidden" name="empty_skins[]" :value="id">
            </span>
        </template>
        <template x-for="(id, i) in e_gamepasses" :key="'eg'+id">
            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium bg-[var(--info-soft)] text-[var(--info)]">
                <span x-text="gpName(id)"></span>
                <button type="button" @click="remove('e_gamepasses', i)" class="hover:opacity-70 leading-none">&times;</button>
                <input type="hidden" name="empty_gamepasses[]" :value="id">
            </span>
        </template>
        <template x-for="(id, i) in e_permanents" :key="'ep'+id">
            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium bg-[var(--warning-soft)] text-[var(--warning)]">
                Perm <span x-text="permName(id)"></span>
                <button type="button" @click="remove('e_permanents', i)" class="hover:opacity-70 leading-none">&times;</button>
                <input type="hidden" name="empty_permanents[]" :value="id">
            </span>
        </template>
        <button type="button" @click="clearAll()" class="text-[11px] text-[var(--text-subtle)] hover:text-[var(--danger)] ml-auto self-center">Hapus semua</button>
    </div>
    <p x-show="totalItems === 0" class="text-xs text-[var(--text-subtle)] mb-4">Pilih item yang ingin dicarikan slot kosongnya.</p>

    {{-- Tab selector --}}
    <div class="flex gap-1 border-b border-[var(--border)] mb-4 overflow-x-auto">
        <button type="button" @click="e_tab='buah'" :class="e_tab==='buah' ? 'border-[var(--success)] text-[var(--success)]' : 'border-transparent text-[var(--text-muted)] hover:text-[var(--text)]'" class="px-3 py-2 text-xs font-semibold border-b-2 whitespace-nowrap transition-colors">
            Buah <span class="text-[var(--text-subtle)] num" x-text="'(' + e_fruits.length + ')'"></span>
        </button>
        <button type="button" @click="e_tab='skin'" :class="e_tab==='skin' ? 'border-[var(--success)] text-[var(--success)]' : 'border-transparent text-[var(--text-muted)] hover:text-[var(--text)]'" class="px-3 py-2 text-xs font-semibold border-b-2 whitespace-nowrap transition-colors">
            Skin <span class="text-[var(--text-subtle)] num" x-text="'(' + e_skins.length + ')'"></span>
        </button>
        <button type="button" @click="e_tab='gamepass'" :class="e_tab==='gamepass' ? 'border-[var(--success)] text-[var(--success)]' : 'border-transparent text-[var(--text-muted)] hover:text-[var(--text)]'" class="px-3 py-2 text-xs font-semibold border-b-2 whitespace-nowrap transition-colors">
            Gamepass <span class="text-[var(--text-subtle)] num" x-text="'(' + e_gamepasses.length + ')'"></span>
        </button>
        <button type="button" @click="e_tab='permanent'" :class="e_tab==='permanent' ? 'border-[var(--success)] text-[var(--success)]' : 'border-transparent text-[var(--text-muted)] hover:text-[var(--text)]'" class="px-3 py-2 text-xs font-semibold border-b-2 whitespace-nowrap transition-colors">
            Permanent <span class="text-[var(--text-subtle)] num" x-text="'(' + e_permanents.length + ')'"></span>
        </button>
    </div>

    {{-- Picker grids --}}
    <div x-show="e_tab==='buah'" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-7 gap-2">
        @php $cr = ''; @endphp
        @foreach($allFruits as $fruit)
            @if($fruit->rarity !== $cr)
                @php $cr = $fruit->rarity; @endphp
                @php $rarityClass = match($cr) { 'Mythical' => 'text-[var(--danger)]', 'Legendary' => 'text-[var(--warning)]', 'Rare' => 'text-[var(--info)]', 'Uncommon' => 'text-[var(--success)]', default => 'text-[var(--text-subtle)]' }; @endphp
                <div class="col-span-full mt-2 first:mt-0 flex items-center gap-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider {{ $rarityClass }}">{{ $cr }}</span>
                    <span class="flex-1 h-px bg-[var(--border)]"></span>
                </div>
            @endif
            <button type="button"
                @click="toggle('e_fruits', '{{ $fruit->id }}')"
                :class="e_fruits.includes('{{ $fruit->id }}') ? 'border-[var(--success)] bg-[var(--success-soft)] text-[var(--success)]' : 'border-[var(--border)] bg-[var(--surface)] text-[var(--text-muted)] hover:border-[var(--border-hover)] hover:text-[var(--text)]'"
                class="rounded-lg border px-2 py-1.5 text-xs font-semibold text-center transition-colors">
                {{ $fruit->nama }}
            </button>
        @endforeach
    </div>

    <div x-show="e_tab==='skin'" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2">
        @foreach($allSkins as $skin)
        <button type="button"
            @click="toggle('e_skins', '{{ $skin->id }}')"
            :class="e_skins.includes('{{ $skin->id }}') ? 'border-[var(--success)] bg-[var(--success-soft)] text-[var(--success)]' : 'border-[var(--border)] bg-[var(--surface)] text-[var(--text-muted)] hover:border-[var(--border-hover)] hover:text-[var(--text)]'"
            class="rounded-lg border px-2 py-1.5 text-xs font-semibold text-center transition-colors">
            {{ $skin->nama_skin }}
            <span class="block text-[10px] text-[var(--text-subtle)] font-normal">{{ $skin->fruit->nama ?? '' }}</span>
        </button>
        @endforeach
    </div>

    <div x-show="e_tab==='gamepass'" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
        @foreach($allGamepasses as $gp)
        <button type="button"
            @click="toggle('e_gamepasses', '{{ $gp->id }}')"
            :class="e_gamepasses.includes('{{ $gp->id }}') ? 'border-[var(--success)] bg-[var(--success-soft)] text-[var(--success)]' : 'border-[var(--border)] bg-[var(--surface)] text-[var(--text-muted)] hover:border-[var(--border-hover)] hover:text-[var(--text)]'"
            class="rounded-lg border px-2 py-1.5 text-xs font-semibold text-center transition-colors">
            {{ $gp->nama }}
        </button>
        @endforeach
    </div>

    <div x-show="e_tab==='permanent'" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-7 gap-2">
        @foreach($allPermanents as $perm)
        <button type="button"
            @click="toggle('e_permanents', '{{ $perm->id }}')"
            :class="e_permanents.includes('{{ $perm->id }}') ? 'border-[var(--success)] bg-[var(--success-soft)] text-[var(--success)]' : 'border-[var(--border)] bg-[var(--surface)] text-[var(--text-muted)] hover:border-[var(--border-hover)] hover:text-[var(--text)]'"
            class="rounded-lg border px-2 py-1.5 text-xs font-semibold text-center transition-colors">
            {{ $perm->nama }}
            <span class="block text-[10px] text-[var(--text-subtle)] font-normal num">{{ format_angka($perm->harga_robux) }} R$</span>
        </button>
        @endforeach
    </div>

    {{-- Submit --}}
    <div class="mt-5 pt-4 border-t border-[var(--border)]">
        <x-btn type="submit" variant="success" size="lg" class="w-full" x-bind:disabled="totalItems === 0" iconRight="M17 8l4 4m0 0l-4 4m4-4H3">
            Cari Slot Kosong
        </x-btn>
    </div>

    {{-- Result --}}
    @if($emptyResults !== null)
    <div class="mt-6 pt-5 border-t border-[var(--border)]">
        <div class="flex items-baseline justify-between gap-2 mb-3">
            <h4 class="text-sm font-semibold text-[var(--text)]">Hasil — {{ $emptyResults->count() }} akun</h4>
            @php
                $totalSelected = collect($searchedEmptyItems)->sum(fn($c) => $c->count());
            @endphp
            <p class="text-[11px] text-[var(--text-subtle)]">{{ $totalSelected }} item dicari</p>
        </div>

        @if($emptyResults->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($emptyResults as $r)
            <a href="{{ route('bloxfruit.storage.show', $r['akun']) }}" class="card p-3.5 hover-lift block group">
                <div class="flex items-center justify-between mb-3 pb-2 border-b border-[var(--border)]">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-[var(--text)] truncate">{{ $r['akun']->username ?? $r['akun']->nama_akun }}</p>
                        <p class="text-[10px] text-[var(--text-subtle)]">{{ $r['akun']->nama_akun }}</p>
                    </div>
                    <div class="text-right shrink-0 ml-2">
                        <span class="chip"><span class="num">{{ $r['total_available'] }}</span> slot</span>
                    </div>
                </div>

                @php $rowClass = 'flex items-center gap-2 text-[11px] py-1'; @endphp

                {{-- Fruits --}}
                @foreach($searchedEmptyItems['fruits'] as $sf)
                @php $d = $r['details']['fruits'][$sf->id] ?? ['current' => 0, 'available' => 0]; @endphp
                <div class="{{ $rowClass }}">
                    <span class="dot dot-accent shrink-0"></span>
                    <span class="text-[var(--text-muted)] flex-1 truncate">{{ $sf->nama }}</span>
                    <div class="progress w-16">
                        <div class="progress-bar {{ $d['current'] >= $r['capacity'] ? 'progress-bar-danger' : ($d['current'] > 0 ? 'progress-bar' : '') }}" style="width: {{ round(($d['current'] / max(1, $r['capacity'])) * 100) }}%"></div>
                    </div>
                    <span class="num shrink-0 w-12 text-right {{ $d['available'] > 0 ? 'text-[var(--success)] font-bold' : 'text-[var(--text-subtle)]' }}">{{ $d['available'] > 0 ? '+' . $d['available'] : 'Penuh' }}</span>
                </div>
                @endforeach

                {{-- Skins --}}
                @foreach($searchedEmptyItems['skins'] as $ss)
                @php $d = $r['details']['skins'][$ss->id] ?? ['current' => 0, 'available' => 0]; @endphp
                <div class="{{ $rowClass }}">
                    <span class="dot bg-pink-500 shrink-0"></span>
                    <span class="text-[var(--text-muted)] flex-1 truncate">{{ $ss->nama_skin }}</span>
                    <div class="progress w-16">
                        <div class="progress-bar {{ $d['current'] >= $r['capacity'] ? 'progress-bar-danger' : '' }}" style="width: {{ round(($d['current'] / max(1, $r['capacity'])) * 100) }}%; background: {{ $d['current'] > 0 && $d['current'] < $r['capacity'] ? '#ec4899' : '' }}"></div>
                    </div>
                    <span class="num shrink-0 w-12 text-right {{ $d['available'] > 0 ? 'text-[var(--success)] font-bold' : 'text-[var(--text-subtle)]' }}">{{ $d['available'] > 0 ? '+' . $d['available'] : 'Penuh' }}</span>
                </div>
                @endforeach

                {{-- Gamepasses --}}
                @foreach($searchedEmptyItems['gamepasses'] as $sg)
                @php $d = $r['details']['gamepasses'][$sg->id] ?? ['current' => 0, 'available' => 0]; @endphp
                <div class="{{ $rowClass }}">
                    <span class="dot dot-info shrink-0"></span>
                    <span class="text-[var(--text-muted)] flex-1 truncate">{{ $sg->nama }}</span>
                    <div class="progress w-16">
                        <div class="progress-bar progress-bar-info" style="width: {{ round(($d['current'] / max(1, $r['capacity'])) * 100) }}%"></div>
                    </div>
                    <span class="num shrink-0 w-12 text-right {{ $d['available'] > 0 ? 'text-[var(--success)] font-bold' : 'text-[var(--text-subtle)]' }}">{{ $d['available'] > 0 ? '+' . $d['available'] : 'Penuh' }}</span>
                </div>
                @endforeach

                {{-- Permanents --}}
                @foreach($searchedEmptyItems['permanents'] as $sp)
                @php $d = $r['details']['permanents'][$sp->id] ?? ['current' => 0, 'available' => 0]; @endphp
                <div class="{{ $rowClass }}">
                    <span class="dot dot-warning shrink-0"></span>
                    <span class="text-[var(--text-muted)] flex-1 truncate">{{ $sp->nama }}</span>
                    <div class="progress w-16">
                        <div class="progress-bar progress-bar-warning" style="width: {{ round(($d['current'] / max(1, $r['capacity'])) * 100) }}%"></div>
                    </div>
                    <span class="num shrink-0 w-12 text-right {{ $d['available'] > 0 ? 'text-[var(--success)] font-bold' : 'text-[var(--text-subtle)]' }}">{{ $d['available'] > 0 ? '+' . $d['available'] : 'Penuh' }}</span>
                </div>
                @endforeach

                <div class="flex items-center justify-between mt-2 pt-2 border-t border-[var(--border)]">
                    <span class="text-[10px] text-[var(--text-subtle)]">Kapasitas <span class="num font-semibold text-[var(--text-muted)]">{{ $r['capacity'] }}</span></span>
                    <span class="text-[10px] text-[var(--accent)] group-hover:underline font-semibold">Buka →</span>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <x-empty-state icon="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8" title="Semua akun penuh" message="Tidak ada akun dengan slot kosong untuk item yang dipilih." />
        @endif
    </div>
    @elseif($hasEmptySearch)
    <div class="mt-6 pt-5 border-t border-[var(--border)]">
        <x-empty-state icon="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" message="Tidak ada hasil." />
    </div>
    @endif
</div>
</form>

{{-- ============================================================
     HASIL PENCARIAN — Akun yang punya item
     ============================================================ --}}
@if($hasSearch)
<div>
    <div class="flex items-baseline justify-between gap-2 mb-3">
        <div>
            <h3 class="text-base font-semibold text-[var(--text)] section-bar">Hasil Pencarian</h3>
            <p class="text-xs text-[var(--text-muted)] mt-1">
                Mencari <span class="num font-semibold text-[var(--text)]">{{ count($searchedItems) }}</span> item, mode
                <span class="chip">{{ $mode === 'semua' ? 'punya semua' : 'punya sebagian' }}</span>
                — ditemukan <span class="num font-semibold text-[var(--text)]">{{ $results->count() }}</span> akun
            </p>
        </div>
    </div>

    <div class="flex flex-wrap gap-1.5 mb-4">
        @foreach($searchedItems as $item)
        @php
            $tone = match($item['tipe']) {
                'Buah' => 'bg-[var(--accent-soft)] text-[var(--accent)]',
                'Skin' => 'bg-pink-500/10 text-pink-600 dark:text-pink-400',
                'Gamepass' => 'bg-[var(--info-soft)] text-[var(--info)]',
                default => 'bg-[var(--warning-soft)] text-[var(--warning)]',
            };
        @endphp
        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium {{ $tone }}">{{ $item['nama'] }}</span>
        @endforeach
    </div>

    @if($results->isEmpty())
    <div class="card p-8">
        <x-empty-state icon="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" title="Tidak ditemukan" :message="$mode === 'semua' ? 'Tidak ada akun yang punya semua item. Coba ganti mode ke punya sebagian.' : 'Tidak ada akun yang punya item tersebut.'" />
    </div>
    @else
    <div class="space-y-3">
        @foreach($results as $akun)
        <div class="card p-4 hover-lift">
            <div class="flex items-center justify-between gap-3 mb-3">
                <div class="min-w-0">
                    <a href="{{ route('bloxfruit.storage.show', $akun) }}" class="font-semibold text-[var(--text)] hover:text-[var(--accent)] truncate">{{ $akun->nama_akun }}</a>
                    @if($akun->username)<span class="text-sm text-[var(--text-subtle)] ml-2">{{ $akun->username }}</span>@endif
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    @php $matched = $akun->total_matched >= count($searchedItems); @endphp
                    <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $matched ? 'bg-[var(--success-soft)] text-[var(--success)]' : 'bg-[var(--warning-soft)] text-[var(--warning)]' }}">
                        <span class="num">{{ $akun->total_matched }}/{{ count($searchedItems) }}</span> cocok
                    </span>
                    <a href="{{ route('bloxfruit.storage.show', $akun) }}" class="text-xs link-soft">Buka →</a>
                </div>
            </div>

            <div class="flex flex-wrap gap-1.5">
                @foreach($akun->fruitStocks as $stock)
                <span class="inline-flex items-center gap-1 rounded-full bg-[var(--accent-soft)] text-[var(--accent)] px-2 py-0.5 text-xs">
                    {{ $stock->fruit->nama ?? '?' }} <span class="font-bold num">×{{ $stock->jumlah }}</span>
                </span>
                @endforeach
                @foreach($akun->skinStocks as $stock)
                <span class="inline-flex items-center gap-1 rounded-full bg-pink-500/10 text-pink-600 dark:text-pink-400 px-2 py-0.5 text-xs">
                    {{ $stock->skin->nama_skin ?? '?' }} <span class="font-bold num">×{{ $stock->jumlah }}</span>
                </span>
                @endforeach
                @foreach($akun->gamepassStocks as $stock)
                <span class="inline-flex items-center gap-1 rounded-full bg-[var(--info-soft)] text-[var(--info)] px-2 py-0.5 text-xs">
                    {{ $stock->gamepass->nama ?? '?' }} <span class="font-bold num">×{{ $stock->jumlah }}</span>
                </span>
                @endforeach
                @foreach($akun->permanentStocks as $stock)
                <span class="inline-flex items-center gap-1 rounded-full bg-[var(--warning-soft)] text-[var(--warning)] px-2 py-0.5 text-xs">
                    Perm {{ $stock->permanentPrice->nama ?? '?' }} <span class="font-bold num">×{{ $stock->jumlah }}</span>
                </span>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endif

</div>{{-- /desktop wrapper --}}
</div>{{-- /root --}}
@endsection
