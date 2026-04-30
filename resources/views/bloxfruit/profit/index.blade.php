@extends('layouts.app')
@section('title', 'Keuangan')

@section('content')
<div x-data="{ spoiler: true }">
{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div class="flex items-center gap-3">
        <form method="GET" class="flex gap-2 items-center">
            <select name="bulan" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @foreach($bulanList as $b)
                <option value="{{ $b }}" {{ $bulan === $b ? 'selected' : '' }}>{{ \Carbon\Carbon::parse($b . '-01')->translatedFormat('F Y') }}</option>
                @endforeach
            </select>
        </form>
        <button @click="spoiler = !spoiler" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-xs font-medium transition-colors" :class="spoiler ? 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-slate-700 dark:text-gray-400' : 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-400'">
            <svg x-show="spoiler" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
            <svg x-show="!spoiler" x-cloak class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            <span x-text="spoiler ? 'Tampilkan' : 'Sembunyikan'"></span>
        </button>
    </div>
    <a href="{{ route('bloxfruit.profit.create') }}" class="btn-primary inline-flex items-center gap-1.5 text-sm">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Catat Transaksi
    </a>
</div>

{{-- ============ TOTAL ASET ============ --}}
@php
    $totalWallet = $wallet->total ?? 0;
    $totalStok = $nilaiStok['total'];
    $totalAset = $totalWallet + $totalStok;
@endphp
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
    <div class="rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 p-5 text-white text-center shadow-lg">
        <p class="text-xs text-emerald-100">Total Aset (Stok + Saldo)</p>
        <p class="text-2xl font-extrabold transition-all" :class="spoiler && 'blur-md select-none'">Rp {{ number_format($totalAset) }}</p>
        <p class="text-[10px] mt-1 text-emerald-200 transition-all" :class="spoiler && 'blur-sm select-none'">Stok {{ number_format($totalStok) }} + Saldo {{ number_format($totalWallet) }}</p>
    </div>
    <div class="rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 p-5 text-white text-center shadow-lg">
        <p class="text-xs text-blue-200">Total Saldo E-Wallet</p>
        <p class="text-2xl font-extrabold transition-all" :class="spoiler && 'blur-md select-none'">Rp {{ number_format($totalWallet) }}</p>
        <p class="text-[10px] mt-1 text-blue-300 transition-all" :class="spoiler && 'blur-sm select-none'">
            @if($wallet)
            @foreach(['dana'=>'Dana','gopay'=>'GoPay','shopeepay'=>'SPay','seabank'=>'Sea','bank_kalsel'=>'Kalsel','bri'=>'BRI','qris'=>'QRIS','cash'=>'Cash'] as $wk => $wl)
            @if(($wallet->$wk ?? 0) > 0){{ $wl }} {{ number_format($wallet->$wk) }} &middot; @endif
            @endforeach
            @endif
        </p>
    </div>
    <div class="rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 p-5 text-white text-center shadow-lg">
        <p class="text-xs text-amber-100">Total Nilai Stok</p>
        <p class="text-2xl font-extrabold transition-all" :class="spoiler && 'blur-md select-none'">Rp {{ number_format($totalStok) }}</p>
        <p class="text-[10px] mt-1 text-amber-200 transition-all" :class="spoiler && 'blur-sm select-none'">
            @foreach($nilaiStok['items'] as $si)
            @if($si['nilai'] > 0){{ $si['label'] }} {{ number_format($si['nilai']) }} &middot; @endif
            @endforeach
        </p>
    </div>
</div>

{{-- ============ STATS BULAN INI ============ --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
    <div class="stat-card" style="--accent: linear-gradient(90deg, #ef4444, #dc2626)">
        <p class="text-[11px] text-gray-500">Total Modal</p>
        <p class="text-xl font-extrabold text-red-600 transition-all" :class="spoiler && 'blur-md select-none'">{{ number_format($totalBulan['modal']) }}</p>
    </div>
    <div class="stat-card" style="--accent: linear-gradient(90deg, #3b82f6, #6366f1)">
        <p class="text-[11px] text-gray-500">Total Pendapatan</p>
        <p class="text-xl font-extrabold text-blue-600 transition-all" :class="spoiler && 'blur-md select-none'">{{ number_format($totalBulan['pendapatan']) }}</p>
    </div>
    <div class="stat-card" style="--accent: linear-gradient(90deg, #10b981, #059669)">
        <p class="text-[11px] text-gray-500">Total Keuntungan</p>
        <p class="text-xl font-extrabold {{ $totalBulan['keuntungan'] >= 0 ? 'text-emerald-600' : 'text-red-600' }} transition-all" :class="spoiler && 'blur-md select-none'">{{ number_format($totalBulan['keuntungan']) }}</p>
    </div>
    <div class="stat-card" style="--accent: linear-gradient(90deg, #8b5cf6, #6366f1)">
        <p class="text-[11px] text-gray-500">Transaksi</p>
        <p class="text-xl font-extrabold text-purple-600 transition-all" :class="spoiler && 'blur-md select-none'">{{ $totalBulan['transaksi'] }}</p>
    </div>
</div>

{{-- ============ RINGKASAN JOKI ============ --}}
<div class="glass-card rounded-2xl p-5 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-gray-900">Pendapatan Joki</h3>
        <a href="{{ route('bloxfruit.joki.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">Lihat Semua &rarr;</a>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
        <div class="rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 p-4 text-white text-center">
            <p class="text-[10px] text-green-100">Selesai Bulan Ini</p>
            <p class="text-xl font-extrabold transition-all" :class="spoiler && 'blur-md select-none'">Rp {{ number_format($jokiBulanIni['total_selesai']) }}</p>
            <p class="text-[10px] text-green-200">{{ $jokiBulanIni['selesai']->count() }} order</p>
        </div>
        <div class="rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 p-4 text-white text-center">
            <p class="text-[10px] text-blue-100">Sedang Proses</p>
            <p class="text-xl font-extrabold transition-all" :class="spoiler && 'blur-md select-none'">Rp {{ number_format($jokiBulanIni['total_proses']) }}</p>
            <p class="text-[10px] text-blue-200">{{ $jokiBulanIni['proses']->count() }} order</p>
        </div>
        <div class="rounded-xl bg-gradient-to-br from-yellow-500 to-amber-600 p-4 text-white text-center">
            <p class="text-[10px] text-yellow-100">Antrian</p>
            <p class="text-xl font-extrabold transition-all" :class="spoiler && 'blur-md select-none'">Rp {{ number_format($jokiBulanIni['total_antrian']) }}</p>
            <p class="text-[10px] text-yellow-200">{{ $jokiBulanIni['antrian']->count() }} order</p>
        </div>
    </div>
    @if($jokiBulanIni['selesai']->count() > 0)
    <div x-data="{ show: false }">
        <button @click="show = !show" class="text-[11px] font-medium text-indigo-600 hover:text-indigo-800" x-text="show ? 'Tutup Detail' : 'Lihat Detail Selesai'"></button>
        <div x-show="show" x-collapse x-cloak class="mt-3">
            <div class="space-y-1.5">
                @foreach($jokiBulanIni['selesai'] as $jk)
                @php
                    $parts = explode(':', $jk->jenis_joki, 2);
                    $jenisNama = $parts[1] ?? $jk->jenis_joki;
                @endphp
                <div class="flex items-center justify-between rounded-lg bg-gray-50 dark:bg-slate-800 px-3 py-2">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $jk->nama_pelanggan }}</p>
                        <p class="text-[10px] text-gray-400">{{ $jenisNama }}</p>
                    </div>
                    <span class="text-sm font-bold text-emerald-600">{{ number_format($jk->harga) }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

{{-- ============ DETAIL: KATEGORI + METODE BAYAR ============ --}}
@php
    $katLabels = ['fruit' => ['Fruit', 'text-indigo-600', 'bg-indigo-50'], 'skin' => ['Skin', 'text-pink-600', 'bg-pink-50'], 'gamepass' => ['Gamepass', 'text-blue-600', 'bg-blue-50'], 'permanent' => ['Permanent', 'text-amber-600', 'bg-amber-50'], 'joki' => ['Joki', 'text-orange-600', 'bg-orange-50'], 'akun' => ['Akun', 'text-teal-600', 'bg-teal-50'], 'lainnya' => ['Lainnya', 'text-gray-600', 'bg-gray-50']];
    $metodeLabels = ['dana' => ['Dana', '#0070ba'], 'gopay' => ['GoPay', '#00aed6'], 'shopeepay' => ['ShopeePay', '#ee4d2d'], 'seabank' => ['SeaBank', '#00b4d8'], 'bank_kalsel' => ['Bank Kalsel', '#1a5276'], 'bri' => ['BRI', '#003d79'], 'qris' => ['QRIS', '#e31937'], 'cash' => ['Cash', '#6b7280']];
@endphp
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100/50">
            <h3 class="font-semibold text-gray-900">Per Kategori</h3>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($katLabels as $key => [$label, $color, $bg])
            @php $data = $perKategori[$key] ?? null; @endphp
            <div class="px-5 py-2.5 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="rounded-md px-2 py-0.5 text-[10px] font-bold {{ $color }} {{ $bg }}">{{ $label }}</span>
                    <span class="text-[11px] text-gray-400">{{ $data->jumlah ?? 0 }}x</span>
                </div>
                <span class="text-sm font-bold {{ ($data->total_keuntungan ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ number_format($data->total_keuntungan ?? 0) }}</span>
            </div>
            @endforeach
        </div>
    </div>
    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100/50">
            <h3 class="font-semibold text-gray-900">Per Metode Bayar</h3>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($metodeLabels as $key => [$label, $clr])
            @php $total = $perMetode[$key]->total ?? 0; @endphp
            @if($total > 0)
            <div class="px-5 py-2.5 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="h-2.5 w-2.5 rounded-full" style="background: {{ $clr }}"></div>
                    <span class="text-sm text-gray-700">{{ $label }}</span>
                </div>
                <span class="text-sm font-bold text-gray-900">{{ number_format($total) }}</span>
            </div>
            @endif
            @endforeach
            @if($perMetode->isEmpty())
            <p class="px-5 py-4 text-sm text-gray-400 text-center">Belum ada data</p>
            @endif
        </div>
    </div>
</div>

{{-- ============ NILAI STOK + SALDO WALLET ============ --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Nilai Stok --}}
    <div class="glass-card rounded-2xl p-5">
        <h3 class="font-semibold text-gray-900 mb-4">Nilai Stok per Kategori</h3>
        @php
            $stokGradients = ['indigo' => 'from-indigo-500 to-indigo-600', 'pink' => 'from-pink-500 to-pink-600', 'blue' => 'from-blue-500 to-blue-600', 'amber' => 'from-amber-500 to-amber-600', 'teal' => 'from-teal-500 to-teal-600', 'orange' => 'from-orange-500 to-orange-600'];
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            @foreach($nilaiStok['items'] as $item)
            <div class="rounded-xl bg-gradient-to-br {{ $stokGradients[$item['color']] ?? 'from-gray-500 to-gray-600' }} p-3 text-white">
                <p class="text-[10px] text-white/70">{{ $item['label'] }}</p>
                <p class="text-base font-extrabold">{{ number_format($item['nilai']) }}</p>
                <p class="text-[10px] text-white/50">{{ $item['qty'] }} item</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Saldo Wallet --}}
    <div class="glass-card rounded-2xl p-5" x-data="{ showForm: false }">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-900">Saldo Wallet</h3>
            <button @click="showForm = !showForm" class="rounded-lg px-3 py-1.5 text-xs font-medium transition-colors" :class="showForm ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200'" x-text="showForm ? 'Tutup' : 'Update Saldo'"></button>
        </div>

        @php
            $walletCards = [
                'dana' => ['Dana', '#0070ba', 'from-blue-600 to-blue-700'],
                'gopay' => ['GoPay', '#00aed6', 'from-cyan-500 to-cyan-600'],
                'shopeepay' => ['ShopeePay', '#ee4d2d', 'from-orange-500 to-red-500'],
                'seabank' => ['SeaBank', '#00b4d8', 'from-sky-500 to-sky-600'],
                'bank_kalsel' => ['Bank Kalsel', '#1a5276', 'from-slate-600 to-slate-700'],
                'bri' => ['BRI', '#003d79', 'from-blue-800 to-blue-900'],
                'qris' => ['QRIS', '#e31937', 'from-red-600 to-red-700'],
                'cash' => ['Cash', '#6b7280', 'from-gray-500 to-gray-600'],
            ];
        @endphp

        {{-- Display Mode --}}
        <div x-show="!showForm">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                @foreach($walletCards as $key => [$label, $clr, $gradient])
                @php $saldo = $wallet->$key ?? 0; @endphp
                <div class="rounded-xl bg-gradient-to-br {{ $gradient }} p-2.5 text-white">
                    <p class="text-[10px] text-white/70">{{ $label }}</p>
                    <p class="text-sm font-extrabold">{{ number_format($saldo) }}</p>
                </div>
                @endforeach
            </div>
            @if($wallet)
            <p class="text-[10px] text-gray-400 mt-3 text-right">Update: {{ $wallet->tanggal->translatedFormat('d F Y') }}</p>
            @endif
        </div>

        {{-- Edit Mode --}}
        <div x-show="showForm" x-collapse x-cloak>
            <form method="POST" action="{{ route('bloxfruit.profit.wallet') }}" class="space-y-3" x-data="walletForm()">
                @csrf
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    @foreach($walletCards as $key => [$label, $clr, $gradient])
                    <div class="rounded-xl border border-gray-200 dark:border-slate-700 p-2.5">
                        <div class="flex items-center gap-1.5 mb-1.5">
                            <div class="h-2.5 w-2.5 rounded-full" style="background: {{ $clr }}"></div>
                            <label class="text-[10px] font-bold text-gray-700 dark:text-gray-300">{{ $label }}</label>
                        </div>
                        <input type="hidden" name="{{ $key }}" :value="vals['{{ $key }}']">
                        <input type="text" :value="fmt(vals['{{ $key }}'])" @input="vals['{{ $key }}'] = parse($event.target.value); $event.target.value = fmt(vals['{{ $key }}'])" class="w-full rounded-lg border-gray-300 text-xs text-right font-bold h-9 focus:border-indigo-500 focus:ring-indigo-500 pr-2">
                    </div>
                    @endforeach
                </div>
                <div class="rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 p-3 text-center">
                    <p class="text-[10px] text-gray-500">Total Saldo</p>
                    <p class="text-lg font-extrabold text-emerald-600" x-text="'Rp ' + fmt(Object.values(vals).reduce((a,b) => a+b, 0))"></p>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-700">Simpan Saldo</button>
                    <button type="button" @click="showForm = false" class="rounded-lg bg-gray-100 dark:bg-slate-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============ PENDAPATAN PER BULAN ============ --}}
<div class="glass-card rounded-2xl p-5 mb-6">
    <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Pendapatan Per Bulan</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead>
                <tr class="border-b border-gray-200 dark:border-slate-700">
                    <th class="px-3 py-2 text-left text-[11px] font-semibold uppercase text-gray-500">Bulan</th>
                    <th class="px-3 py-2 text-right text-[11px] font-semibold uppercase text-gray-500">Modal</th>
                    <th class="px-3 py-2 text-right text-[11px] font-semibold uppercase text-gray-500">Pendapatan</th>
                    <th class="px-3 py-2 text-right text-[11px] font-semibold uppercase text-gray-500">Keuntungan</th>
                    <th class="px-3 py-2 text-right text-[11px] font-semibold uppercase text-gray-500">Transaksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @foreach($pendapatanPerBulan as $pb)
                <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-800/50 {{ $pb->bulan_key === $bulan ? 'bg-indigo-50/50 dark:bg-indigo-950/20' : '' }}">
                    <td class="px-3 py-2.5 text-sm font-medium text-gray-900 dark:text-white">
                        {{ \Carbon\Carbon::parse($pb->bulan_key . '-01')->translatedFormat('F Y') }}
                        @if($pb->bulan_key === $bulan)<span class="text-[9px] text-indigo-500 ml-1">aktif</span>@endif
                    </td>
                    <td class="px-3 py-2.5 text-sm text-right transition-all" :class="spoiler && 'blur-md select-none'"><span class="text-red-600">{{ number_format($pb->total_modal) }}</span></td>
                    <td class="px-3 py-2.5 text-sm text-right transition-all" :class="spoiler && 'blur-md select-none'"><span class="text-blue-600 font-semibold">{{ number_format($pb->total_pendapatan) }}</span></td>
                    <td class="px-3 py-2.5 text-sm text-right font-bold transition-all" :class="spoiler && 'blur-md select-none'"><span class="{{ $pb->total_keuntungan >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ number_format($pb->total_keuntungan) }}</span></td>
                    <td class="px-3 py-2.5 text-sm text-right text-gray-500">{{ $pb->jumlah }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ============ RIWAYAT TRANSAKSI ============ --}}
@php
    $katTabs = [];
    foreach ($katLabels as $key => [$label, $color, $bg]) {
        if (isset($perKategori[$key])) {
            $katTabs[$key] = ['label' => $label, 'count' => $perKategori[$key]->jumlah, 'color' => $color, 'bg' => $bg];
        }
    }
@endphp
<div x-data="txHistory(@js($records->map(fn($r) => [
    'id' => $r->id,
    'slug' => $r->slug,
    'tanggal' => $r->tanggal->format('d/m/Y'),
    'jam' => $r->created_at->format('H:i'),
    'kategori' => $r->kategori,
    'kat_label' => ($katLabels[$r->kategori] ?? ['?'])[0],
    'kat_color' => ($katLabels[$r->kategori] ?? ['?','text-gray-600','bg-gray-50'])[1],
    'kat_bg' => ($katLabels[$r->kategori] ?? ['?','text-gray-600','bg-gray-50'])[2],
    'keterangan' => $r->keterangan ?? '-',
    'modal' => $r->modal,
    'pendapatan' => $r->pendapatan,
    'keuntungan' => $r->keuntungan,
    'metode' => $r->metode_bayar ? ucfirst(str_replace('_', ' ', $r->metode_bayar)) : '-',
    'edit_url' => route('bloxfruit.profit.edit', $r),
    'delete_url' => route('bloxfruit.profit.destroy', $r),
])))" class="glass-card rounded-2xl overflow-hidden">
    <div class="px-5 py-3 border-b border-gray-100/50 dark:border-slate-700">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-semibold text-gray-900 dark:text-white">Riwayat Transaksi <span class="text-xs font-normal text-gray-400" x-text="'(' + filtered.length + ')'"></span></h3>
            @if($trashedCount > 0)
            <a href="{{ route('bloxfruit.profit.trash') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-red-500 hover:text-red-700">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Sampah ({{ $trashedCount }})
            </a>
            @endif
        </div>
        {{-- Kategori filter tabs --}}
        <div class="flex flex-wrap gap-1.5">
            <button @click="kat = ''; page = 1" class="rounded-full px-3 py-1 text-[11px] font-semibold transition-colors" :class="kat === '' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-slate-700 dark:text-gray-400'">Semua ({{ $records->count() }})</button>
            @foreach($katTabs as $key => $tab)
            <button @click="kat = '{{ $key }}'; page = 1" class="rounded-full px-3 py-1 text-[11px] font-semibold transition-colors" :class="kat === '{{ $key }}' ? '{{ $tab['color'] }} {{ $tab['bg'] }} ring-1 ring-current' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-slate-700 dark:text-gray-400'">{{ $tab['label'] }} ({{ $tab['count'] }})</button>
            @endforeach
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
            <thead class="bg-gray-50 dark:bg-slate-800">
                <tr>
                    <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase text-gray-500">Tanggal</th>
                    <th x-show="kat === ''" class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase text-gray-500">Kategori</th>
                    <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase text-gray-500">Keterangan</th>
                    <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase text-gray-500">Modal</th>
                    <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase text-gray-500">Pendapatan</th>
                    <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase text-gray-500">Untung</th>
                    <th class="px-3 py-2.5 text-center text-[11px] font-semibold uppercase text-gray-500">Bayar</th>
                    <th class="px-3 py-2.5 text-center text-[11px] font-semibold uppercase text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                <template x-for="rec in paged" :key="rec.id">
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-800/50">
                        <td class="px-3 py-2.5">
                            <p class="text-sm text-gray-700 dark:text-gray-300" x-text="rec.tanggal"></p>
                            <p class="text-[10px] text-gray-400" x-text="rec.jam"></p>
                        </td>
                        <td x-show="kat === ''" class="px-3 py-2.5">
                            <span class="rounded-md px-2 py-0.5 text-[10px] font-bold" :class="rec.kat_color + ' ' + rec.kat_bg" x-text="rec.kat_label"></span>
                        </td>
                        <td class="px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 max-w-xs truncate" x-text="rec.keterangan"></td>
                        <td class="px-3 py-2.5 text-sm text-right text-red-600" x-text="fmt(rec.modal)"></td>
                        <td class="px-3 py-2.5 text-sm text-right text-blue-600" x-text="fmt(rec.pendapatan)"></td>
                        <td class="px-3 py-2.5 text-sm text-right font-bold" :class="rec.keuntungan >= 0 ? 'text-emerald-600' : 'text-red-600'" x-text="fmt(rec.keuntungan)"></td>
                        <td class="px-3 py-2.5 text-center text-[11px] text-gray-500" x-text="rec.metode"></td>
                        <td class="px-3 py-2.5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a :href="rec.edit_url" class="text-[11px] text-gray-400 hover:text-indigo-600">Edit</a>
                                <form method="POST" :action="rec.delete_url" onsubmit="return confirm('Hapus?')">
                                    @csrf @method('DELETE')
                                    <button class="text-[11px] text-gray-400 hover:text-red-500">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        <template x-if="filtered.length === 0">
            <p class="px-4 py-8 text-center text-sm text-gray-400">Belum ada transaksi</p>
        </template>
    </div>
    {{-- Pagination --}}
    <template x-if="totalPages > 1">
        <div class="px-5 py-3 border-t border-gray-100 dark:border-slate-700 flex items-center justify-between">
            <p class="text-[11px] text-gray-400" x-text="'Hal ' + page + ' dari ' + totalPages"></p>
            <div class="flex gap-1">
                <button @click="page = Math.max(1, page-1)" :disabled="page <= 1" class="rounded-lg px-3 py-1.5 text-xs font-medium bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 disabled:opacity-40">&laquo; Prev</button>
                <button @click="page = Math.min(totalPages, page+1)" :disabled="page >= totalPages" class="rounded-lg px-3 py-1.5 text-xs font-medium bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 disabled:opacity-40">Next &raquo;</button>
            </div>
        </div>
    </template>
</div>

<script>
function txHistory(records) {
    return {
        all: records,
        kat: '',
        page: 1,
        perPage: 20,
        get filtered() { return this.kat ? this.all.filter(r => r.kategori === this.kat) : this.all; },
        get totalPages() { return Math.ceil(this.filtered.length / this.perPage); },
        get paged() { return this.filtered.slice((this.page - 1) * this.perPage, this.page * this.perPage); },
        fmt(n) { return new Intl.NumberFormat('id-ID').format(n); }
    }
}
function walletForm() {
    return {
        vals: {
            dana: {{ $wallet->dana ?? 0 }},
            gopay: {{ $wallet->gopay ?? 0 }},
            shopeepay: {{ $wallet->shopeepay ?? 0 }},
            seabank: {{ $wallet->seabank ?? 0 }},
            bank_kalsel: {{ $wallet->bank_kalsel ?? 0 }},
            bri: {{ $wallet->bri ?? 0 }},
            qris: {{ $wallet->qris ?? 0 }},
            cash: {{ $wallet->cash ?? 0 }},
        },
        fmt(n) { return new Intl.NumberFormat('id-ID').format(n || 0); },
        parse(s) { return parseInt(String(s).replace(/\D/g, '') || '0', 10); }
    }
}
</script>
</div>{{-- end x-data spoiler --}}
@endsection
