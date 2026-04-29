@extends('layouts.app')
@section('title', 'Keuangan')

@section('content')
{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <form method="GET" class="flex gap-2 items-center">
        <select name="bulan" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            @foreach($bulanList as $b)
            <option value="{{ $b }}" {{ $bulan === $b ? 'selected' : '' }}>{{ \Carbon\Carbon::parse($b . '-01')->translatedFormat('F Y') }}</option>
            @endforeach
        </select>
    </form>
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
    <div class="rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 p-5 text-white text-center shadow-lg group cursor-default" x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false" @click="show = !show">
        <p class="text-xs text-emerald-100">Total Aset (Stok + Saldo)</p>
        <p class="text-2xl font-extrabold transition-all" :class="show ? '' : 'blur-md select-none'">Rp {{ number_format($totalAset) }}</p>
        <p class="text-[10px] mt-1 transition-all" :class="show ? 'text-emerald-200' : 'blur-sm text-emerald-200 select-none'">Stok {{ number_format($totalStok) }} + Saldo {{ number_format($totalWallet) }}</p>
        <p class="text-[9px] text-emerald-300/50 mt-1" x-show="!show">tap untuk lihat</p>
    </div>
    <div class="rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 p-5 text-white text-center shadow-lg" x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false" @click="show = !show">
        <p class="text-xs text-blue-200">Total Saldo E-Wallet</p>
        <p class="text-2xl font-extrabold transition-all" :class="show ? '' : 'blur-md select-none'">Rp {{ number_format($totalWallet) }}</p>
        <p class="text-[10px] mt-1 transition-all" :class="show ? 'text-blue-300' : 'blur-sm text-blue-300 select-none'">
            @if($wallet)
            @foreach(['dana'=>'Dana','gopay'=>'GoPay','shopeepay'=>'SPay','seabank'=>'Sea','bank_kalsel'=>'Kalsel','bri'=>'BRI','qris'=>'QRIS','cash'=>'Cash'] as $wk => $wl)
            @if(($wallet->$wk ?? 0) > 0){{ $wl }} {{ number_format($wallet->$wk) }} &middot; @endif
            @endforeach
            @endif
        </p>
        <p class="text-[9px] text-blue-300/50 mt-1" x-show="!show">tap untuk lihat</p>
    </div>
    <div class="rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 p-5 text-white text-center shadow-lg" x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false" @click="show = !show">
        <p class="text-xs text-amber-100">Total Nilai Stok</p>
        <p class="text-2xl font-extrabold transition-all" :class="show ? '' : 'blur-md select-none'">Rp {{ number_format($totalStok) }}</p>
        <p class="text-[10px] mt-1 transition-all" :class="show ? 'text-amber-200' : 'blur-sm text-amber-200 select-none'">
            @foreach($nilaiStok['items'] as $si)
            @if($si['nilai'] > 0){{ $si['label'] }} {{ number_format($si['nilai']) }} &middot; @endif
            @endforeach
        </p>
        <p class="text-[9px] text-amber-300/50 mt-1" x-show="!show">tap untuk lihat</p>
    </div>
</div>

{{-- ============ STATS BULAN INI ============ --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
    <div class="stat-card cursor-default" style="--accent: linear-gradient(90deg, #ef4444, #dc2626)" x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false" @click="show = !show">
        <p class="text-[11px] text-gray-500">Total Modal</p>
        <p class="text-xl font-extrabold text-red-600 transition-all" :class="show ? '' : 'blur-md select-none'">{{ number_format($totalBulan['modal']) }}</p>
    </div>
    <div class="stat-card cursor-default" style="--accent: linear-gradient(90deg, #3b82f6, #6366f1)" x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false" @click="show = !show">
        <p class="text-[11px] text-gray-500">Total Pendapatan</p>
        <p class="text-xl font-extrabold text-blue-600 transition-all" :class="show ? '' : 'blur-md select-none'">{{ number_format($totalBulan['pendapatan']) }}</p>
    </div>
    <div class="stat-card cursor-default" style="--accent: linear-gradient(90deg, #10b981, #059669)" x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false" @click="show = !show">
        <p class="text-[11px] text-gray-500">Total Keuntungan</p>
        <p class="text-xl font-extrabold {{ $totalBulan['keuntungan'] >= 0 ? 'text-emerald-600' : 'text-red-600' }} transition-all" :class="show ? '' : 'blur-md select-none'">{{ number_format($totalBulan['keuntungan']) }}</p>
    </div>
    <div class="stat-card cursor-default" style="--accent: linear-gradient(90deg, #8b5cf6, #6366f1)" x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false" @click="show = !show">
        <p class="text-[11px] text-gray-500">Transaksi</p>
        <p class="text-xl font-extrabold text-purple-600 transition-all" :class="show ? '' : 'blur-md select-none'">{{ $totalBulan['transaksi'] }}</p>
    </div>
</div>

{{-- ============ RINGKASAN JOKI ============ --}}
<div class="glass-card rounded-2xl p-5 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-gray-900">Pendapatan Joki</h3>
        <a href="{{ route('bloxfruit.joki.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">Lihat Semua &rarr;</a>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
        <div class="rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 p-4 text-white text-center" x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false" @click="show = !show">
            <p class="text-[10px] text-green-100">Selesai Bulan Ini</p>
            <p class="text-xl font-extrabold transition-all" :class="show ? '' : 'blur-md select-none'">Rp {{ number_format($jokiBulanIni['total_selesai']) }}</p>
            <p class="text-[10px] text-green-200">{{ $jokiBulanIni['selesai']->count() }} order</p>
        </div>
        <div class="rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 p-4 text-white text-center" x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false" @click="show = !show">
            <p class="text-[10px] text-blue-100">Sedang Proses</p>
            <p class="text-xl font-extrabold transition-all" :class="show ? '' : 'blur-md select-none'">Rp {{ number_format($jokiBulanIni['total_proses']) }}</p>
            <p class="text-[10px] text-blue-200">{{ $jokiBulanIni['proses']->count() }} order</p>
        </div>
        <div class="rounded-xl bg-gradient-to-br from-yellow-500 to-amber-600 p-4 text-white text-center" x-data="{ show: false }" @mouseenter="show = true" @mouseleave="show = false" @click="show = !show">
            <p class="text-[10px] text-yellow-100">Antrian</p>
            <p class="text-xl font-extrabold transition-all" :class="show ? '' : 'blur-md select-none'">Rp {{ number_format($jokiBulanIni['total_antrian']) }}</p>
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

{{-- ============ RIWAYAT TRANSAKSI ============ --}}
<div class="glass-card rounded-2xl overflow-hidden">
    <div class="px-5 py-3 border-b border-gray-100/50">
        <h3 class="font-semibold text-gray-900">Riwayat Transaksi</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase text-gray-500">Tanggal</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase text-gray-500">Kategori</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase text-gray-500">Keterangan</th>
                    <th class="px-4 py-2.5 text-right text-[11px] font-semibold uppercase text-gray-500">Modal</th>
                    <th class="px-4 py-2.5 text-right text-[11px] font-semibold uppercase text-gray-500">Pendapatan</th>
                    <th class="px-4 py-2.5 text-right text-[11px] font-semibold uppercase text-gray-500">Untung</th>
                    <th class="px-4 py-2.5 text-center text-[11px] font-semibold uppercase text-gray-500">Bayar</th>
                    <th class="px-4 py-2.5 text-center text-[11px] font-semibold uppercase text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($records as $rec)
                @php $katInfo = $katLabels[$rec->kategori] ?? ['?', 'text-gray-600', 'bg-gray-50']; @endphp
                <tr class="hover:bg-gray-50/50">
                    <td class="px-4 py-2.5 text-sm text-gray-600">{{ $rec->tanggal->format('d/m') }}</td>
                    <td class="px-4 py-2.5"><span class="rounded-md px-2 py-0.5 text-[10px] font-bold {{ $katInfo[1] }} {{ $katInfo[2] }}">{{ $katInfo[0] }}</span></td>
                    <td class="px-4 py-2.5 text-sm text-gray-700 max-w-xs truncate">{{ $rec->keterangan ?? '-' }}</td>
                    <td class="px-4 py-2.5 text-sm text-right text-red-600">{{ number_format($rec->modal) }}</td>
                    <td class="px-4 py-2.5 text-sm text-right text-blue-600">{{ number_format($rec->pendapatan) }}</td>
                    <td class="px-4 py-2.5 text-sm text-right font-bold {{ $rec->keuntungan >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ number_format($rec->keuntungan) }}</td>
                    <td class="px-4 py-2.5 text-center text-[11px] text-gray-500">{{ $rec->metode_bayar ? ucfirst(str_replace('_', ' ', $rec->metode_bayar)) : '-' }}</td>
                    <td class="px-4 py-2.5 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('bloxfruit.profit.edit', $rec) }}" class="text-[11px] text-gray-400 hover:text-indigo-600">Edit</a>
                            <form method="POST" action="{{ route('bloxfruit.profit.destroy', $rec) }}" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="text-[11px] text-gray-400 hover:text-red-500">Hapus</button></form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-4 py-8 text-center text-sm text-gray-400">Belum ada transaksi bulan ini</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
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
@endsection
