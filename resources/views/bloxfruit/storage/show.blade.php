@extends('layouts.app')
@section('title', 'Storage: ' . $storage->nama_akun)

@section('content')
{{-- Quick Sell Modal --}}
<div x-data="quickSell()" @quick-sell.window="openSell($event.detail)" x-cloak>
    <div x-show="open" x-transition:enter="transition-opacity duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm" @click="open = false"></div>
    <div x-show="open" x-transition class="fixed inset-x-4 top-1/4 z-50 mx-auto max-w-sm rounded-2xl bg-white dark:bg-slate-800 shadow-2xl border border-gray-200 dark:border-slate-700 p-5">
        <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Jual <span x-text="nama" class="text-indigo-600"></span></h3>
        <p class="text-[11px] text-gray-400 mb-4">Stok: <span x-text="stokSekarang"></span> | Stok berkurang & transaksi tercatat otomatis</p>
        <form method="POST" action="{{ route('bloxfruit.quicksell') }}" class="space-y-3">
            @csrf
            <input type="hidden" name="tipe" :value="tipe">
            <input type="hidden" name="stock_id" :value="stockId">
            <input type="hidden" name="harga_modal" :value="hargaModal">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">Jumlah Jual</label>
                    <input type="number" name="jumlah" x-model.number="jumlah" min="1" :max="stokSekarang" class="w-full rounded-lg border-gray-300 text-center text-sm font-bold h-10 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">Harga Jual /pcs</label>
                    <input type="number" name="harga_jual" x-model.number="hargaJual" min="0" class="w-full rounded-lg border-gray-300 text-center text-sm font-bold h-10 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">Metode Bayar <span class="font-normal text-gray-400">- opsional</span></label>
                <select name="metode_bayar" class="w-full rounded-lg border-gray-300 text-sm h-10 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">-- Pilih --</option>
                    <option value="dana">Dana</option><option value="gopay">GoPay</option><option value="shopeepay">ShopeePay</option>
                    <option value="seabank">SeaBank</option><option value="bank_kalsel">Bank Kalsel</option><option value="bri">BRI</option>
                    <option value="qris">QRIS</option><option value="cash">Cash</option>
                </select>
            </div>
            <div class="rounded-xl p-3 text-center" :class="(hargaJual - hargaModal) * jumlah >= 0 ? 'bg-emerald-50 dark:bg-emerald-950/30' : 'bg-red-50 dark:bg-red-950/30'">
                <p class="text-[10px] text-gray-500">Total Pendapatan</p>
                <p class="text-lg font-extrabold text-blue-600" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(hargaJual * jumlah)"></p>
                <p class="text-[10px] mt-1" :class="(hargaJual - hargaModal) * jumlah >= 0 ? 'text-emerald-600' : 'text-red-600'">Untung: <span x-text="'Rp ' + new Intl.NumberFormat('id-ID').format((hargaJual - hargaModal) * jumlah)"></span></p>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-emerald-700">Jual Sekarang</button>
                <button type="button" @click="open = false" class="rounded-lg bg-gray-100 dark:bg-slate-700 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300">Batal</button>
            </div>
        </form>
    </div>
</div>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <p class="text-sm text-gray-500">Username: <span class="font-medium text-gray-700 dark:text-gray-300">{{ $storage->username ?? '-' }}</span> &middot; Kapasitas: <span class="font-bold text-indigo-600">{{ $storage->kapasitas_storage }}</span>/item</p>
        @if($storage->catatan)<p class="text-xs text-gray-400 mt-0.5">{{ $storage->catatan }}</p>@endif
    </div>
    <div class="flex gap-2">
        <form method="POST" action="{{ route('bloxfruit.storage.clear', $storage) }}" onsubmit="return confirm('Kosongkan SEMUA stok (fruit, skin, gamepass, permanent) dari akun ini?\n\nData keuangan & lainnya TIDAK terpengaruh.')">
            @csrf @method('DELETE')
            <button type="submit" class="rounded-lg bg-red-50 dark:bg-red-950/30 px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-100 dark:hover:bg-red-900/30">Kosongkan Stok</button>
        </form>
        <a href="{{ route('bloxfruit.storage.edit', $storage) }}" class="rounded-lg bg-gray-100 dark:bg-slate-700 px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-slate-600">Edit</a>
        <a href="{{ route('bloxfruit.storage.index') }}" class="rounded-lg bg-gray-100 dark:bg-slate-700 px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-slate-600">Kembali</a>
    </div>
</div>

<div class="flex gap-1 mb-6 border-b border-gray-200 dark:border-slate-700 overflow-x-auto">
    @foreach(['buah' => 'Stok Buah', 'skin' => 'Skin Buah', 'gamepass' => 'Gamepass', 'permanent' => 'Permanent'] as $key => $label)
    <a href="{{ route('bloxfruit.storage.show', ['storage' => $storage, 'tab' => $key]) }}" class="px-4 py-2.5 text-sm font-medium whitespace-nowrap border-b-2 transition-colors {{ $tab === $key ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">{{ $label }}</a>
    @endforeach
</div>

@if($tab === 'buah')
<form method="POST" action="{{ route('bloxfruit.storage.fruit.bulk', $storage) }}">
    @csrf
    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-gray-500">Klik <span class="text-emerald-600 font-semibold">Jual</span> untuk jual cepat.</p>
        <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-medium text-white hover:bg-indigo-700 shadow-sm">Simpan Semua</button>
    </div>
    @php $cr = ''; @endphp
    @foreach($allFruits as $fruit)
        @if($fruit->rarity !== $cr)
            @php $cr = $fruit->rarity; @endphp
            @if(!$loop->first)</div>@endif
            <div class="mb-2 mt-5"><span class="inline-flex rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide {{ $cr === 'Mythical' ? 'bg-red-100 text-red-700' : ($cr === 'Legendary' ? 'bg-yellow-100 text-yellow-700' : ($cr === 'Rare' ? 'bg-blue-100 text-blue-700' : ($cr === 'Uncommon' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600'))) }}">{{ $cr }}</span></div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
        @endif
        @php $qty = $fruitStocks[$fruit->id] ?? 0; $sid = $fruitStockIds[$fruit->id] ?? 0; @endphp
        <div class="rounded-lg border p-3 {{ $qty > 0 ? 'border-indigo-300 bg-indigo-50 dark:bg-indigo-950/30 dark:border-indigo-800' : 'border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800' }}">
            <div class="flex items-center justify-between mb-1">
                <p class="text-sm font-semibold text-gray-900">{{ $fruit->nama }}</p>
                @if($qty > 0 && $sid)
                <button type="button" @click="$dispatch('quick-sell', {tipe:'fruit', stockId:{{ $sid }}, nama:'{{ addslashes($fruit->nama) }}', stok:{{ $qty }}, hargaJual:{{ $fruit->harga_jual }}, hargaModal:{{ $fruit->harga_beli }}})" class="rounded bg-emerald-100 dark:bg-emerald-900/40 px-1.5 py-0.5 text-[10px] font-bold text-emerald-700 dark:text-emerald-400 hover:bg-emerald-200">Jual</button>
                @endif
            </div>
            <p class="text-[11px] text-gray-400 mb-1">{{ $fruit->tipe }} &middot; Jual: {{ number_format($fruit->harga_jual) }}</p>
            <div class="h-1.5 w-full rounded-full bg-gray-100 dark:bg-slate-700 mb-2 overflow-hidden">
                <div class="h-1.5 rounded-full {{ $qty >= $storage->kapasitas_storage ? 'bg-red-500' : ($qty > 0 ? 'bg-indigo-500' : 'bg-gray-200') }}" style="width: {{ min(100, round(($qty / max(1, $storage->kapasitas_storage)) * 100)) }}%"></div>
            </div>
            <input type="number" name="fruits[{{ $fruit->id }}]" value="{{ $qty }}" min="0" max="{{ $storage->kapasitas_storage }}" class="w-full rounded-md border-gray-300 text-center text-sm font-bold shadow-sm focus:border-indigo-500 focus:ring-indigo-500 h-9" placeholder="0">
        </div>
        @if($loop->last)</div>@endif
    @endforeach
    <div class="mt-6 sticky bottom-4"><button type="submit" class="w-full rounded-lg bg-indigo-600 px-5 py-3 text-sm font-medium text-white hover:bg-indigo-700 shadow-lg">Simpan Semua Stok Buah</button></div>
</form>
@endif

@if($tab === 'skin')
@if($allSkins->isEmpty())
<div class="rounded-xl bg-amber-50 border border-amber-200 p-6 text-center"><p class="text-amber-800 mb-3">Belum ada master data skin.</p><a href="{{ route('bloxfruit.skins.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Tambah Skin Dulu</a></div>
@else
<form method="POST" action="{{ route('bloxfruit.storage.skin.bulk', $storage) }}">
    @csrf
    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-gray-500">Klik <span class="text-emerald-600 font-semibold">Jual</span> untuk jual cepat.</p>
        <button type="submit" class="rounded-lg bg-pink-600 px-5 py-2 text-sm font-medium text-white hover:bg-pink-700 shadow-sm">Simpan Semua</button>
    </div>
    @php $grouped = $allSkins->groupBy(fn($s) => $s->fruit->nama ?? 'Lainnya'); @endphp
    @foreach($grouped as $fruitName => $skins)
    <div class="mb-2 mt-5"><span class="inline-flex rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide bg-pink-100 text-pink-700">{{ $fruitName }}</span></div>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
        @foreach($skins as $skin)
        @php $qty = $skinStocks[$skin->id] ?? 0; $sid = $skinStockIds[$skin->id] ?? 0; @endphp
        <div class="rounded-lg border p-3 {{ $qty > 0 ? 'border-pink-300 bg-pink-50 dark:bg-pink-950/30 dark:border-pink-800' : 'border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800' }}">
            <div class="flex items-center justify-between mb-1">
                <p class="text-sm font-semibold text-gray-900">{{ $skin->nama_skin }}</p>
                @if($qty > 0 && $sid)
                <button type="button" @click="$dispatch('quick-sell', {tipe:'skin', stockId:{{ $sid }}, nama:'{{ addslashes($skin->nama_skin) }}', stok:{{ $qty }}, hargaJual:{{ $skin->harga_jual }}, hargaModal:{{ $skin->harga_beli }}})" class="rounded bg-emerald-100 dark:bg-emerald-900/40 px-1.5 py-0.5 text-[10px] font-bold text-emerald-700 dark:text-emerald-400 hover:bg-emerald-200">Jual</button>
                @endif
            </div>
            <p class="text-[11px] text-gray-400 mb-1">Jual: {{ number_format($skin->harga_jual) }}</p>
            <div class="h-1.5 w-full rounded-full bg-gray-100 dark:bg-slate-700 mb-2 overflow-hidden">
                <div class="h-1.5 rounded-full {{ $qty >= $storage->kapasitas_storage ? 'bg-red-500' : ($qty > 0 ? 'bg-pink-500' : 'bg-gray-200') }}" style="width: {{ min(100, round(($qty / max(1, $storage->kapasitas_storage)) * 100)) }}%"></div>
            </div>
            <input type="number" name="skins[{{ $skin->id }}]" value="{{ $qty }}" min="0" max="{{ $storage->kapasitas_storage }}" class="w-full rounded-md border-gray-300 text-center text-sm font-bold shadow-sm focus:border-pink-500 focus:ring-pink-500 h-9" placeholder="0">
        </div>
        @endforeach
    </div>
    @endforeach
    <div class="mt-6 sticky bottom-4"><button type="submit" class="w-full rounded-lg bg-pink-600 px-5 py-3 text-sm font-medium text-white hover:bg-pink-700 shadow-lg">Simpan Semua Stok Skin</button></div>
</form>
@endif
@endif

@if($tab === 'gamepass')
@if($allGamepasses->isEmpty())
<div class="rounded-xl bg-amber-50 border border-amber-200 p-6 text-center"><p class="text-amber-800 mb-3">Belum ada master data gamepass.</p><a href="{{ route('bloxfruit.gamepasses.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Tambah Gamepass Dulu</a></div>
@else
<form method="POST" action="{{ route('bloxfruit.storage.gamepass.bulk', $storage) }}">
    @csrf
    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-gray-500">Klik <span class="text-emerald-600 font-semibold">Jual</span> untuk jual cepat.</p>
        <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-medium text-white hover:bg-blue-700 shadow-sm">Simpan Semua</button>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
        @foreach($allGamepasses as $gp)
        @php $qty = $gamepassStocks[$gp->id] ?? 0; $sid = $gamepassStockIds[$gp->id] ?? 0; @endphp
        <div class="rounded-lg border p-3 {{ $qty > 0 ? 'border-blue-300 bg-blue-50 dark:bg-blue-950/30 dark:border-blue-800' : 'border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800' }}">
            <div class="flex items-center justify-between mb-1">
                <p class="text-sm font-semibold text-gray-900">{{ $gp->nama }}</p>
                @if($qty > 0 && $sid)
                <button type="button" @click="$dispatch('quick-sell', {tipe:'gamepass', stockId:{{ $sid }}, nama:'{{ addslashes($gp->nama) }}', stok:{{ $qty }}, hargaJual:{{ $gp->harga_jual }}, hargaModal:{{ $gp->harga_beli }}})" class="rounded bg-emerald-100 dark:bg-emerald-900/40 px-1.5 py-0.5 text-[10px] font-bold text-emerald-700 dark:text-emerald-400 hover:bg-emerald-200">Jual</button>
                @endif
            </div>
            <p class="text-[11px] text-gray-400 mb-2">{{ number_format($gp->harga_robux) }} R$ &middot; Jual: {{ number_format($gp->harga_jual) }}</p>
            <input type="number" name="gamepasses[{{ $gp->id }}]" value="{{ $qty }}" min="0" class="w-full rounded-md border-gray-300 text-center text-sm font-bold shadow-sm focus:border-blue-500 focus:ring-blue-500 h-9" placeholder="0">
        </div>
        @endforeach
    </div>
    <div class="mt-6 sticky bottom-4"><button type="submit" class="w-full rounded-lg bg-blue-600 px-5 py-3 text-sm font-medium text-white hover:bg-blue-700 shadow-lg">Simpan Semua Stok Gamepass</button></div>
</form>
@endif
@endif

@if($tab === 'permanent')
<form method="POST" action="{{ route('bloxfruit.storage.permanent.bulk', $storage) }}">
    @csrf
    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-gray-500">Klik <span class="text-emerald-600 font-semibold">Jual</span> untuk jual cepat.</p>
        <button type="submit" class="rounded-lg bg-amber-600 px-5 py-2 text-sm font-medium text-white hover:bg-amber-700 shadow-sm">Simpan Semua</button>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
        @foreach($allPermanents as $pf)
        @php $stok = $permanentStocks[$pf->id] ?? null; $qty = $stok->jumlah ?? 0; @endphp
        <div class="rounded-lg border p-3 {{ $qty > 0 ? 'border-amber-300 bg-amber-50 dark:bg-amber-950/30 dark:border-amber-800' : 'border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800' }}">
            <div class="flex items-center justify-between mb-1">
                <p class="text-sm font-semibold text-gray-900">{{ $pf->nama }}</p>
                @if($qty > 0 && $stok)
                <button type="button" @click="$dispatch('quick-sell', {tipe:'permanent', stockId:{{ $stok->id }}, nama:'{{ addslashes($pf->nama) }}', stok:{{ $qty }}, hargaJual:{{ $pf->harga_jual }}, hargaModal:{{ $pf->harga_beli }}})" class="rounded bg-emerald-100 dark:bg-emerald-900/40 px-1.5 py-0.5 text-[10px] font-bold text-emerald-700 dark:text-emerald-400 hover:bg-emerald-200">Jual</button>
                @endif
            </div>
            <div class="flex items-center gap-2 mb-2">
                <span class="text-[10px] font-semibold text-purple-600">{{ number_format($pf->harga_robux) }} R$</span>
                <span class="text-[10px] text-gray-400">Jual: {{ number_format($pf->harga_jual) }}</span>
            </div>
            <div>
                <input type="number" name="permanents[{{ $pf->id }}][jumlah]" value="{{ $qty }}" min="0" class="w-full rounded border-gray-300 text-center text-xs font-bold shadow-sm focus:border-amber-500 focus:ring-amber-500 h-8">
                <input type="hidden" name="permanents[{{ $pf->id }}][harga_robux]" value="{{ $pf->harga_robux }}">
                <input type="hidden" name="permanents[{{ $pf->id }}][harga_idr]" value="{{ $pf->harga_jual }}">
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-6 sticky bottom-4"><button type="submit" class="w-full rounded-lg bg-amber-600 px-5 py-3 text-sm font-medium text-white hover:bg-amber-700 shadow-lg">Simpan Semua Stok Permanent</button></div>
</form>
@endif

<script>
function quickSell() {
    return {
        open: false, tipe: '', stockId: 0, nama: '', stokSekarang: 0,
        jumlah: 1, hargaJual: 0, hargaModal: 0,
        openSell(data) {
            this.tipe = data.tipe;
            this.stockId = data.stockId;
            this.nama = data.nama;
            this.stokSekarang = data.stok;
            this.hargaJual = data.hargaJual;
            this.hargaModal = data.hargaModal;
            this.jumlah = 1;
            this.open = true;
            document.body.style.overflow = 'hidden';
        },
        close() { this.open = false; document.body.style.overflow = ''; }
    }
}
</script>
@endsection
