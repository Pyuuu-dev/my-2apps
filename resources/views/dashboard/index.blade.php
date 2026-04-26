@extends('layouts.app')
@section('title', 'Beranda')

@section('content')
<div class="max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-extrabold text-gray-900 dark:text-white">Selamat {{ now()->hour < 12 ? 'Pagi' : (now()->hour < 15 ? 'Siang' : (now()->hour < 18 ? 'Sore' : 'Malam')) }}, {{ auth()->user()->name }}!</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ now()->translatedFormat('l, d F Y') }} &middot; {{ now()->format('H:i') }} SGT</p>
        </div>
    </div>

    {{-- ============ KEUANGAN BULAN INI ============ --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="stat-card" style="--accent: linear-gradient(90deg, #3b82f6, #6366f1)">
            <p class="text-[11px] text-gray-500">Pendapatan Bulan Ini</p>
            <p class="text-xl font-extrabold text-blue-600">Rp {{ number_format($keuangan['pendapatan']) }}</p>
            <p class="text-[10px] text-gray-400">{{ $keuangan['transaksi'] }} transaksi</p>
        </div>
        <div class="stat-card" style="--accent: linear-gradient(90deg, #10b981, #059669)">
            <p class="text-[11px] text-gray-500">Keuntungan Bulan Ini</p>
            <p class="text-xl font-extrabold {{ $keuangan['keuntungan'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">Rp {{ number_format($keuangan['keuntungan']) }}</p>
        </div>
        <div class="stat-card" style="--accent: linear-gradient(90deg, #8b5cf6, #a855f7)">
            <p class="text-[11px] text-gray-500">Saldo Wallet</p>
            <p class="text-xl font-extrabold text-purple-600">Rp {{ number_format($keuangan['saldo_wallet']) }}</p>
        </div>
        <div class="stat-card" style="--accent: linear-gradient(90deg, #f59e0b, #d97706)">
            <p class="text-[11px] text-gray-500">Joki Aktif</p>
            <p class="text-xl font-extrabold text-amber-600">{{ $bfStats['joki_aktif'] }}</p>
            <p class="text-[10px] text-gray-400">{{ $bfStats['joki_proses'] }} proses &middot; {{ $bfStats['joki_antrian'] }} antrian</p>
        </div>
    </div>

    {{-- ============ APP CARDS ============ --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        {{-- Blox Fruit --}}
        <a href="{{ route('bloxfruit.dashboard') }}" class="group relative overflow-hidden rounded-2xl p-5 text-white shadow-lg transition-all hover:shadow-xl hover:scale-[1.01]" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);">
            <div class="absolute top-0 right-0 -mt-6 -mr-6 h-24 w-24 rounded-full bg-white/10 blur-2xl"></div>
            <div class="relative flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold">Blox Fruit</h3>
                    <p class="text-indigo-200 text-xs">Manajemen stok & penjualan</p>
                </div>
                <svg class="h-5 w-5 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </div>
            <div class="grid grid-cols-4 gap-2 mt-4">
                <div class="rounded-lg bg-white/15 p-2 text-center">
                    <p class="text-base font-extrabold">{{ $bfStats['total_buah'] }}</p>
                    <p class="text-[9px] text-indigo-200">Buah</p>
                </div>
                <div class="rounded-lg bg-white/15 p-2 text-center">
                    <p class="text-base font-extrabold">{{ $bfStats['total_skin'] }}</p>
                    <p class="text-[9px] text-indigo-200">Skin</p>
                </div>
                <div class="rounded-lg bg-white/15 p-2 text-center">
                    <p class="text-base font-extrabold">{{ $bfStats['total_akun_storage'] }}</p>
                    <p class="text-[9px] text-indigo-200">Storage</p>
                </div>
                <div class="rounded-lg bg-white/15 p-2 text-center">
                    <p class="text-base font-extrabold">{{ $bfStats['total_joki'] }}</p>
                    <p class="text-[9px] text-indigo-200">Joki</p>
                </div>
            </div>
        </a>

        {{-- Diet Tracker --}}
        <a href="{{ route('diet.dashboard') }}" class="group relative overflow-hidden rounded-2xl p-5 text-white shadow-lg transition-all hover:shadow-xl hover:scale-[1.01]" style="background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);">
            <div class="absolute top-0 right-0 -mt-6 -mr-6 h-24 w-24 rounded-full bg-white/10 blur-2xl"></div>
            <div class="relative flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold">Diet Tracker</h3>
                    <p class="text-emerald-200 text-xs">Monitoring diet & kesehatan</p>
                </div>
                <svg class="h-5 w-5 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </div>
            @if($dtStats)
            <div class="grid grid-cols-4 gap-2 mt-4">
                <div class="rounded-lg bg-white/15 p-2 text-center">
                    <p class="text-base font-extrabold">{{ number_format($dtStats['kalori_masuk']) }}</p>
                    <p class="text-[9px] text-emerald-200">Kalori</p>
                </div>
                <div class="rounded-lg bg-white/15 p-2 text-center">
                    <p class="text-base font-extrabold">{{ number_format($dtStats['total_minum']) }}</p>
                    <p class="text-[9px] text-emerald-200">ml Air</p>
                </div>
                <div class="rounded-lg bg-white/15 p-2 text-center">
                    <p class="text-base font-extrabold">{{ $dtStats['berat_sekarang'] }}</p>
                    <p class="text-[9px] text-emerald-200">kg</p>
                </div>
                <div class="rounded-lg bg-white/15 p-2 text-center">
                    <p class="text-base font-extrabold">{{ $dtStats['bmi']['bmi'] }}</p>
                    <p class="text-[9px] text-emerald-200">BMI</p>
                </div>
            </div>
            @else
            <div class="rounded-lg bg-white/15 p-3 text-center mt-4">
                <p class="text-sm text-emerald-100">Belum ada program diet. Klik untuk mulai!</p>
            </div>
            @endif
        </a>
    </div>

    {{-- ============ JOKI AKTIF + TRANSAKSI TERAKHIR ============ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        {{-- Joki Aktif --}}
        <div class="glass-card rounded-2xl overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between">
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Joki Aktif</h3>
                <a href="{{ route('bloxfruit.joki.index') }}" class="text-[11px] font-medium text-indigo-600 hover:text-indigo-800">Lihat Semua</a>
            </div>
            <div class="divide-y divide-gray-50 dark:divide-slate-700">
                @forelse($jokiAktif as $joki)
                @php
                    $parts = explode(':', $joki->jenis_joki, 2);
                    $jenisNama = $parts[1] ?? $joki->jenis_joki;
                @endphp
                <div class="px-5 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="h-2.5 w-2.5 rounded-full shrink-0 {{ $joki->status === 'proses' ? 'bg-blue-500 animate-pulse' : 'bg-yellow-500' }}"></div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $joki->nama_pelanggan }}</p>
                            <p class="text-[11px] text-gray-400 truncate">{{ $jenisNama }}</p>
                        </div>
                    </div>
                    <div class="text-right shrink-0 ml-3">
                        <p class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ number_format($joki->harga) }}</p>
                        <span class="text-[10px] font-semibold {{ $joki->status === 'proses' ? 'text-blue-600' : 'text-yellow-600' }}">{{ ucfirst($joki->status) }}</span>
                    </div>
                </div>
                @empty
                <p class="px-5 py-6 text-center text-sm text-gray-400">Tidak ada joki aktif</p>
                @endforelse
            </div>
        </div>

        {{-- Transaksi Terakhir --}}
        <div class="glass-card rounded-2xl overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 dark:border-slate-700 flex items-center justify-between">
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Transaksi Terakhir</h3>
                <a href="{{ route('bloxfruit.profit.index') }}" class="text-[11px] font-medium text-indigo-600 hover:text-indigo-800">Lihat Semua</a>
            </div>
            @php
                $katColors = ['fruit' => 'text-indigo-600 bg-indigo-50', 'skin' => 'text-pink-600 bg-pink-50', 'gamepass' => 'text-blue-600 bg-blue-50', 'permanent' => 'text-amber-600 bg-amber-50', 'joki' => 'text-orange-600 bg-orange-50', 'akun' => 'text-teal-600 bg-teal-50', 'lainnya' => 'text-gray-600 bg-gray-50'];
            @endphp
            <div class="divide-y divide-gray-50 dark:divide-slate-700">
                @forelse($transaksiTerakhir as $trx)
                <div class="px-5 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="rounded-md px-1.5 py-0.5 text-[9px] font-bold shrink-0 {{ $katColors[$trx->kategori] ?? 'text-gray-600 bg-gray-50' }}">{{ ucfirst($trx->kategori) }}</span>
                        <div class="min-w-0">
                            <p class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ $trx->keterangan ?? '-' }}</p>
                            <p class="text-[10px] text-gray-400">{{ $trx->tanggal->format('d/m') }}</p>
                        </div>
                    </div>
                    <span class="text-sm font-bold shrink-0 ml-3 {{ $trx->keuntungan >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ ($trx->keuntungan >= 0 ? '+' : '') . number_format($trx->keuntungan) }}</span>
                </div>
                @empty
                <p class="px-5 py-6 text-center text-sm text-gray-400">Belum ada transaksi</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ============ AKUN JUAL + DIET HARI INI ============ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        {{-- Akun Jual --}}
        <div class="glass-card rounded-2xl p-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Akun Jual</h3>
                <a href="{{ route('bloxfruit.accounts.index') }}" class="text-[11px] font-medium text-indigo-600 hover:text-indigo-800">Lihat Semua</a>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div class="rounded-xl bg-gray-50 dark:bg-slate-800 p-3 text-center">
                    <p class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ $akunJual['total'] }}</p>
                    <p class="text-[10px] text-gray-500">Total</p>
                </div>
                <div class="rounded-xl bg-emerald-50 dark:bg-emerald-950/30 p-3 text-center">
                    <p class="text-2xl font-extrabold text-emerald-600">{{ $akunJual['tersedia'] }}</p>
                    <p class="text-[10px] text-gray-500">Tersedia</p>
                </div>
                <div class="rounded-xl bg-gray-50 dark:bg-slate-800 p-3 text-center">
                    <p class="text-2xl font-extrabold text-gray-400">{{ $akunJual['terjual'] }}</p>
                    <p class="text-[10px] text-gray-500">Terjual</p>
                </div>
            </div>
        </div>

        {{-- Diet Hari Ini --}}
        @if($dtStats)
        <div class="glass-card rounded-2xl p-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Diet Hari Ini</h3>
                <a href="{{ route('diet.dashboard') }}" class="text-[11px] font-medium text-emerald-600 hover:text-emerald-800">Detail</a>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-xl bg-orange-50 dark:bg-orange-950/30 p-3">
                    <p class="text-[11px] text-gray-500 mb-1">Kalori</p>
                    <p class="text-lg font-extrabold text-orange-600">{{ number_format($dtStats['kalori_masuk']) }} <span class="text-xs font-normal text-gray-400">/ {{ number_format($dtStats['target_kalori']) }}</span></p>
                    <div class="h-1.5 w-full rounded-full bg-orange-100 dark:bg-orange-900/50 mt-2 overflow-hidden">
                        <div class="h-1.5 rounded-full bg-orange-500" style="width: {{ min(100, round(($dtStats['kalori_masuk'] / max(1, $dtStats['target_kalori'])) * 100)) }}%"></div>
                    </div>
                </div>
                <div class="rounded-xl bg-blue-50 dark:bg-blue-950/30 p-3">
                    <p class="text-[11px] text-gray-500 mb-1">Air Minum</p>
                    <p class="text-lg font-extrabold text-blue-600">{{ number_format($dtStats['total_minum']) }}<span class="text-xs font-normal text-gray-400">ml / {{ number_format($dtStats['target_air']) }}</span></p>
                    <div class="h-1.5 w-full rounded-full bg-blue-100 dark:bg-blue-900/50 mt-2 overflow-hidden">
                        <div class="h-1.5 rounded-full bg-blue-500" style="width: {{ min(100, round(($dtStats['total_minum'] / max(1, $dtStats['target_air'])) * 100)) }}%"></div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- ============ AKSI CEPAT ============ --}}
    <div class="glass-card rounded-2xl p-5">
        <h3 class="font-semibold text-gray-900 dark:text-white mb-3 text-sm">Aksi Cepat</h3>
        <div class="grid grid-cols-3 sm:grid-cols-6 gap-2">
            <a href="{{ route('bloxfruit.joki.create') }}" class="rounded-xl border border-gray-200 dark:border-slate-700 p-3 text-center hover:border-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-950/30 transition-colors">
                <svg class="h-5 w-5 mx-auto text-indigo-500 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <p class="text-[10px] font-semibold text-gray-700 dark:text-gray-300">Joki Baru</p>
            </a>
            <a href="{{ route('bloxfruit.profit.create') }}" class="rounded-xl border border-gray-200 dark:border-slate-700 p-3 text-center hover:border-emerald-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 transition-colors">
                <svg class="h-5 w-5 mx-auto text-emerald-500 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-[10px] font-semibold text-gray-700 dark:text-gray-300">Transaksi</p>
            </a>
            <a href="{{ route('bloxfruit.search') }}" class="rounded-xl border border-gray-200 dark:border-slate-700 p-3 text-center hover:border-violet-300 hover:bg-violet-50 dark:hover:bg-violet-950/30 transition-colors">
                <svg class="h-5 w-5 mx-auto text-violet-500 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <p class="text-[10px] font-semibold text-gray-700 dark:text-gray-300">Cari Stok</p>
            </a>
            <a href="{{ route('diet.meals.create') }}" class="rounded-xl border border-gray-200 dark:border-slate-700 p-3 text-center hover:border-orange-300 hover:bg-orange-50 dark:hover:bg-orange-950/30 transition-colors">
                <svg class="h-5 w-5 mx-auto text-orange-500 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                <p class="text-[10px] font-semibold text-gray-700 dark:text-gray-300">Catat Makan</p>
            </a>
            <a href="{{ route('diet.exercises.index') }}" class="rounded-xl border border-gray-200 dark:border-slate-700 p-3 text-center hover:border-blue-300 hover:bg-blue-50 dark:hover:bg-blue-950/30 transition-colors">
                <svg class="h-5 w-5 mx-auto text-blue-500 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <p class="text-[10px] font-semibold text-gray-700 dark:text-gray-300">Olahraga</p>
            </a>
            <a href="{{ route('bloxfruit.accounts.index') }}" class="rounded-xl border border-gray-200 dark:border-slate-700 p-3 text-center hover:border-teal-300 hover:bg-teal-50 dark:hover:bg-teal-950/30 transition-colors">
                <svg class="h-5 w-5 mx-auto text-teal-500 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <p class="text-[10px] font-semibold text-gray-700 dark:text-gray-300">Akun Jual</p>
            </a>
        </div>
    </div>
</div>
@endsection
