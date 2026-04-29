@extends('layouts.app')
@section('title', 'Daftar Buah')

@section('content')
<div x-data="fruitPage()">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <p class="text-sm text-gray-500">{{ $fruits->total() }} buah &middot; Total stok: <span class="font-bold text-indigo-600">{{ $totalStok }}</span></p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="showCopy = true" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                Copy Teks Stok
            </button>
            <a href="{{ route('bloxfruit.fruits.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
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

    {{-- Stok Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 mb-6">
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
                <p class="text-xs text-gray-500">Rp {{ number_format($fruit->harga_jual / 1000, 1) }}k</p>
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
    <div>{{ $fruits->links() }}</div>

    {{-- ============ MODAL COPY TEKS STOK ============ --}}
    <div x-show="showCopy" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="showCopy = false">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showCopy = false"></div>
        <div class="relative w-full max-w-2xl max-h-[85vh] rounded-2xl bg-white dark:bg-slate-900 shadow-2xl overflow-hidden flex flex-col">
            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 dark:border-slate-700 shrink-0">
                <h3 class="font-bold text-gray-900 dark:text-white">Copy Teks Stok</h3>
                <button @click="showCopy = false" class="rounded-lg p-1.5 text-gray-400 hover:text-gray-700 hover:bg-gray-100 dark:hover:bg-slate-800">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Options --}}
            <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 shrink-0">
                <div class="flex flex-wrap gap-2">
                    <label class="flex items-center gap-1.5 text-xs">
                        <input type="checkbox" x-model="sections.header" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"> Header
                    </label>
                    <label class="flex items-center gap-1.5 text-xs">
                        <input type="checkbox" x-model="sections.fruit" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"> Fruit
                    </label>
                    <label class="flex items-center gap-1.5 text-xs">
                        <input type="checkbox" x-model="sections.skin" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"> Skin
                    </label>
                    <label class="flex items-center gap-1.5 text-xs">
                        <input type="checkbox" x-model="sections.gamepass" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"> Gamepass
                    </label>
                    <label class="flex items-center gap-1.5 text-xs">
                        <input type="checkbox" x-model="sections.permanent" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"> Permanent
                    </label>
                    <label class="flex items-center gap-1.5 text-xs">
                        <input type="checkbox" x-model="showZeroStock" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"> Tampilkan stok 0
                    </label>
                </div>
            </div>

            {{-- Preview --}}
            <div class="flex-1 overflow-y-auto px-5 py-3">
                <pre class="text-xs text-gray-700 dark:text-gray-300 whitespace-pre-wrap font-mono leading-relaxed" x-text="generatedText"></pre>
            </div>

            {{-- Footer --}}
            <div class="px-5 py-3 border-t border-gray-100 dark:border-slate-700 shrink-0 flex items-center gap-3">
                <button @click="copyText()" class="flex-1 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-emerald-700 transition-colors">
                    <span x-show="!copied">Copy ke Clipboard</span>
                    <span x-show="copied" x-cloak>Tersalin!</span>
                </button>
                <button @click="showCopy = false" class="rounded-lg bg-gray-100 dark:bg-slate-800 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function fruitPage() {
    return {
        showCopy: false,
        copied: false,
        showZeroStock: false,
        sections: { header: true, fruit: true, skin: true, gamepass: true, permanent: true },

        // Data from server
        fruits: @json($fruitsForCopy),
        skins: @json($skinsForCopy),
        gamepasses: @json($gamepassesForCopy),
        permanents: @json($permanentsForCopy),

        get generatedText() {
            let text = '';

            if (this.sections.header) {
                text += `📩 DM ON Instagram : https://www.instagram.com/ldcstoree/\n`;
                text += `📩 DM ON Tiktok : https://www.tiktok.com/@ldc_storee\n`;
                text += `📩 Admin WhatsApp : https://wa.me/6282353085502?text=Min%20Saya%20ingin%20beli\n\n`;
                text += `⭐ Testi/Vouch ? Cek dibawah 1690+\n`;
                text += `    - Chuni Server *GA https://discord.gg/YAj7Dzhbw4\n`;
                text += `    - Google Drive https://shorturl.at/dwEeB\n`;
                text += `💳 Payment : GOPAY / DANA / Qris ALL PAYMENT FREE TAX\n\n`;
            }

            if (this.sections.fruit) {
                const items = this.showZeroStock ? this.fruits : this.fruits.filter(f => f.stok > 0);
                if (items.length > 0) {
                    text += `"🍎 FRUIT\n`;
                    items.forEach(f => {
                        text += `🔥 ${f.nama} → ${this.formatHarga(f.harga_jual)}${f.stok > 0 ? ' ('+f.stok+')' : ''}\n`;
                    });
                    text += `\n`;
                }
            }

            if (this.sections.skin) {
                const items = this.showZeroStock ? this.skins : this.skins.filter(s => s.stok > 0);
                if (items.length > 0) {
                    text += `🎨 SKIN\n`;
                    items.forEach(s => {
                        text += `🔥 ${s.nama} → ${this.formatHarga(s.harga_jual)}${s.stok > 0 ? ' ('+s.stok+')' : ''}\n`;
                    });
                    text += `\n`;
                }
            }

            if (this.sections.gamepass) {
                text += `🎮 GAMEPASS\n`;
                this.gamepasses.forEach(g => {
                    text += `🔥 ${g.nama} → ${this.formatHarga(g.harga_jual)}\n`;
                });
                text += `\n`;
            }

            if (this.sections.permanent) {
                const items = this.permanents.filter(p => p.harga_jual > 0);
                if (items.length > 0) {
                    text += `💎 PERMANEN\n`;
                    items.forEach(p => {
                        text += `🔥 Perm ${p.nama} → ${this.formatHarga(p.harga_jual)}\n`;
                    });
                }
            }

            return text.replace(/\n$/, '') + `"`;
        },

        formatHarga(n) {
            return new Intl.NumberFormat('id-ID').format(n);
        },

        copyText() {
            navigator.clipboard.writeText(this.generatedText).then(() => {
                this.copied = true;
                setTimeout(() => this.copied = false, 2000);
            });
        }
    }
}
</script>
@endsection
