@extends('layouts.app')
@section('title', 'Blox Fruit - Dashboard')

@section('content')
{{-- Welcome Banner --}}
<div class="glass-card rounded-2xl p-6 mb-6 overflow-hidden relative">
    <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-indigo-500/20 to-purple-500/20 rounded-full blur-3xl -mr-32 -mt-32"></div>
    <div class="relative z-10">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-1">Dashboard Blox Fruit</h2>
                <p class="text-sm text-gray-500">Kelola stok dan penjualan akun game Anda</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('bloxfruit.search') }}" class="btn-primary inline-flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Cari Stok
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Main Stats Grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
    $mainCards = [
        ['label' => 'Master Buah', 'value' => $stats['total_buah'], 'sub' => 'Stok: ' . number_format($stats['total_stok_buah']), 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'gradient' => 'from-indigo-500 to-purple-600', 'bg' => 'bg-indigo-50', 'text' => 'text-indigo-600'],
        ['label' => 'Master Skin', 'value' => $stats['total_skin_master'], 'sub' => 'Stok: ' . number_format($stats['total_skin']), 'icon' => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01', 'gradient' => 'from-pink-500 to-rose-600', 'bg' => 'bg-pink-50', 'text' => 'text-pink-600'],
        ['label' => 'Master Gamepass', 'value' => $stats['total_gamepass'], 'sub' => 'Stok: ' . number_format($stats['total_stok_gamepass']), 'icon' => 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z', 'gradient' => 'from-blue-500 to-cyan-600', 'bg' => 'bg-blue-50', 'text' => 'text-blue-600'],
        ['label' => 'Master Permanent', 'value' => $stats['total_permanent_master'], 'sub' => 'Stok: ' . number_format($stats['total_permanent']), 'icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z', 'gradient' => 'from-amber-500 to-orange-600', 'bg' => 'bg-amber-50', 'text' => 'text-amber-600'],
    ];
    @endphp
    @foreach($mainCards as $card)
    <div class="group glass-card rounded-2xl p-5 hover:shadow-xl transition-all duration-300 hover:-translate-y-1 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br {{ $card['gradient'] }} opacity-10 rounded-full blur-2xl -mr-16 -mt-16 group-hover:opacity-20 transition-opacity"></div>
        <div class="relative z-10">
            <div class="flex items-start justify-between mb-3">
                <div class="p-3 rounded-xl bg-gradient-to-br {{ $card['gradient'] }} shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/></svg>
                </div>
            </div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">{{ $card['label'] }}</p>
            <p class="text-3xl font-extrabold {{ $card['text'] }} mb-1">{{ number_format($card['value']) }}</p>
            <p class="text-xs text-gray-400">{{ $card['sub'] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- Keuangan Ringkas --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
    <div class="glass-card rounded-xl p-4 border-l-4 border-emerald-500">
        <p class="text-[10px] font-semibold text-gray-500 uppercase">Pendapatan Bulan Ini</p>
        <p class="text-xl font-extrabold text-emerald-600">Rp {{ number_format($keuanganBulanIni['pendapatan']) }}</p>
        <p class="text-[10px] text-gray-400">{{ $keuanganBulanIni['transaksi'] }} transaksi</p>
    </div>
    <div class="glass-card rounded-xl p-4 border-l-4 border-blue-500">
        <p class="text-[10px] font-semibold text-gray-500 uppercase">Keuntungan Bulan Ini</p>
        <p class="text-xl font-extrabold text-blue-600">Rp {{ number_format($keuanganBulanIni['keuntungan']) }}</p>
    </div>
    <div class="glass-card rounded-xl p-4 border-l-4 border-amber-500">
        <p class="text-[10px] font-semibold text-gray-500 uppercase">Nilai Stok</p>
        <p class="text-xl font-extrabold text-amber-600">Rp {{ number_format($nilaiStokTotal) }}</p>
        <p class="text-[10px] text-gray-400">Fruit + Skin</p>
    </div>
    <div class="glass-card rounded-xl p-4 border-l-4 border-purple-500">
        <p class="text-[10px] font-semibold text-gray-500 uppercase">Saldo Wallet</p>
        <p class="text-xl font-extrabold text-purple-600">Rp {{ number_format($saldoWallet) }}</p>
    </div>
</div>

{{-- Secondary Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
    $secondaryCards = [
        ['label' => 'Akun Storage', 'value' => $stats['total_akun_storage'], 'sub' => 'Aktif', 'icon' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4', 'gradient' => 'from-emerald-500 to-teal-600', 'text' => 'text-emerald-600'],
        ['label' => 'Akun Tersedia', 'value' => $stats['akun_tersedia'], 'sub' => 'Siap Jual', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'gradient' => 'from-teal-500 to-cyan-600', 'text' => 'text-teal-600'],
        ['label' => 'Akun Terjual', 'value' => $stats['akun_terjual'], 'sub' => 'Total', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'gradient' => 'from-gray-500 to-slate-600', 'text' => 'text-gray-600'],
        ['label' => 'Total Joki', 'value' => $stats['joki_antrian'] + $stats['joki_proses'] + $stats['joki_selesai'], 'sub' => $stats['joki_proses'] . ' sedang proses', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01', 'gradient' => 'from-red-500 to-rose-600', 'text' => 'text-red-600'],
    ];
    @endphp
    @foreach($secondaryCards as $card)
    <div class="glass-card rounded-xl p-4 hover:shadow-lg transition-all duration-300 border-l-4 border-transparent hover:border-current {{ $card['text'] }}">
        <div class="flex items-center gap-3">
            <div class="p-2.5 rounded-lg bg-gradient-to-br {{ $card['gradient'] }} shadow-md">
                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider">{{ $card['label'] }}</p>
                <p class="text-2xl font-bold {{ $card['text'] }}">{{ number_format($card['value']) }}</p>
                <p class="text-[11px] text-gray-400 truncate">{{ $card['sub'] }}</p>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Joki Status Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="glass-card rounded-xl p-5 border-l-4 border-yellow-500 hover:shadow-lg transition-all duration-300">
        <div class="flex items-center justify-between mb-2">
            <div class="p-2 rounded-lg bg-gradient-to-br from-yellow-400 to-amber-500 shadow-md">
                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="text-xs font-semibold text-yellow-600 bg-yellow-100 px-2.5 py-1 rounded-full">Antrian</span>
        </div>
        <p class="text-3xl font-extrabold text-yellow-600 mb-1">{{ $stats['joki_antrian'] }}</p>
        <p class="text-xs text-gray-500">Order menunggu diproses</p>
    </div>

    <div class="glass-card rounded-xl p-5 border-l-4 border-orange-500 hover:shadow-lg transition-all duration-300">
        <div class="flex items-center justify-between mb-2">
            <div class="p-2 rounded-lg bg-gradient-to-br from-orange-500 to-red-500 shadow-md">
                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <span class="text-xs font-semibold text-orange-600 bg-orange-100 px-2.5 py-1 rounded-full">Proses</span>
        </div>
        <p class="text-3xl font-extrabold text-orange-600 mb-1">{{ $stats['joki_proses'] }}</p>
        <p class="text-xs text-gray-500">Sedang dikerjakan</p>
    </div>

    <div class="glass-card rounded-xl p-5 border-l-4 border-green-500 hover:shadow-lg transition-all duration-300">
        <div class="flex items-center justify-between mb-2">
            <div class="p-2 rounded-lg bg-gradient-to-br from-emerald-500 to-green-600 shadow-md">
                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="text-xs font-semibold text-green-600 bg-green-100 px-2.5 py-1 rounded-full">Selesai</span>
        </div>
        <p class="text-3xl font-extrabold text-green-600 mb-1">{{ $stats['joki_selesai'] }}</p>
        <p class="text-xs text-gray-500">Order telah selesai</p>
    </div>
</div>

{{-- Recent Activity --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Joki Terbaru --}}
    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-100/50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 shadow-md">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                </div>
                <h3 class="font-bold text-gray-900">Joki Terbaru</h3>
            </div>
            <a href="{{ route('bloxfruit.joki.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">Lihat Semua &rarr;</a>
        </div>
        <div class="divide-y divide-gray-50 max-h-96 overflow-y-auto">
            @forelse($jokiTerbaru as $joki)
            <div class="px-6 py-4 hover:bg-gray-50/50 transition-colors">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 text-sm truncate">{{ $joki->nama_pelanggan }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs text-gray-500">{{ ucfirst($joki->jenis_joki) }}</span>
                            <span class="text-gray-300">•</span>
                            <span class="text-xs text-gray-400">{{ $joki->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold whitespace-nowrap {{ $joki->status === 'selesai' ? 'badge-uncommon' : ($joki->status === 'proses' ? 'badge-rare' : ($joki->status === 'batal' ? 'badge-mythical' : 'badge-legendary')) }}">
                        {{ ucfirst($joki->status) }}
                    </span>
                </div>
            </div>
            @empty
            <div class="px-6 py-12 text-center">
                <svg class="h-12 w-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                <p class="text-sm text-gray-500 font-medium">Belum ada order joki</p>
                <p class="text-xs text-gray-400 mt-1">Order joki akan muncul di sini</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Akun Jual Terbaru --}}
    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-emerald-50 to-teal-50 border-b border-gray-100/50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 shadow-md">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <h3 class="font-bold text-gray-900">Akun Jual Terbaru</h3>
            </div>
            <a href="{{ route('bloxfruit.accounts.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-800 transition-colors">Lihat Semua &rarr;</a>
        </div>
        <div class="divide-y divide-gray-50 max-h-96 overflow-y-auto">
            @forelse($akunTerbaru as $akun)
            <div class="px-6 py-4 hover:bg-gray-50/50 transition-colors">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 text-sm truncate">{{ $akun->judul }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs text-gray-500">Level {{ $akun->level ?? '-' }}</span>
                            <span class="text-gray-300">•</span>
                            <span class="text-xs text-gray-400">{{ $akun->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-900 whitespace-nowrap">Rp {{ number_format($akun->harga, 0, ',', '.') }}</p>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold mt-1 {{ $akun->status === 'tersedia' ? 'badge-uncommon' : ($akun->status === 'terjual' ? 'badge-common' : 'badge-legendary') }}">
                            {{ ucfirst($akun->status) }}
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div class="px-6 py-12 text-center">
                <svg class="h-12 w-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <p class="text-sm text-gray-500 font-medium">Belum ada stok akun</p>
                <p class="text-xs text-gray-400 mt-1">Akun yang ditambahkan akan muncul di sini</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
