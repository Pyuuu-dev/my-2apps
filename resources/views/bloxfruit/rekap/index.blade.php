@extends('layouts.app')
@section('title', 'Rekap Bulanan')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-extrabold text-gray-900 dark:text-white">Rekap Bulanan</h2>
            <p class="text-sm text-gray-500">Ringkasan performa LDC Store</p>
        </div>
        <form method="GET">
            <select name="bulan" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @foreach($bulanList as $b)
                <option value="{{ $b }}" {{ $bulan === $b ? 'selected' : '' }}>{{ \Carbon\Carbon::parse($b . '-01')->translatedFormat('F Y') }}</option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Hero Card --}}
    <div class="rounded-3xl bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 p-8 text-white text-center mb-8 shadow-xl relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-full opacity-10">
            <div class="absolute top-10 left-10 h-40 w-40 rounded-full bg-white blur-3xl"></div>
            <div class="absolute bottom-10 right-10 h-32 w-32 rounded-full bg-white blur-3xl"></div>
        </div>
        <div class="relative">
            <p class="text-sm text-white/70 uppercase tracking-widest mb-2">LDC Store</p>
            <h1 class="text-3xl font-black mb-1">{{ $bulanLabel }}</h1>
            <p class="text-white/60 text-sm">Monthly Performance Report</p>

            <div class="grid grid-cols-3 gap-4 mt-8">
                <div class="rounded-2xl bg-white/10 backdrop-blur-sm p-4">
                    <p class="text-3xl font-black">{{ $jokiSelesai->count() }}</p>
                    <p class="text-xs text-white/70 mt-1">Joki Selesai</p>
                </div>
                <div class="rounded-2xl bg-white/10 backdrop-blur-sm p-4">
                    <p class="text-3xl font-black">{{ $akunTerjual }}</p>
                    <p class="text-xs text-white/70 mt-1">Akun Terjual</p>
                </div>
                <div class="rounded-2xl bg-white/10 backdrop-blur-sm p-4">
                    <p class="text-3xl font-black">{{ $totalTransaksi }}</p>
                    <p class="text-xs text-white/70 mt-1">Total Transaksi</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Joki Breakdown --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- By Kategori --}}
        <div class="glass-card rounded-2xl p-6">
            <h3 class="font-bold text-gray-900 dark:text-white mb-4">Joki per Kategori</h3>
            @if($jokiByKategori->count() > 0)
            <div class="space-y-3">
                @foreach($jokiByKategori as $kat => $count)
                @php
                    $maxCount = $jokiByKategori->first();
                    $pct = round(($count / max(1, $maxCount)) * 100);
                @endphp
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $kategoriLabels[$kat] ?? ucfirst($kat) }}</span>
                        <span class="text-xs font-bold text-indigo-600">{{ $count }}</span>
                    </div>
                    <div class="h-2.5 w-full rounded-full bg-gray-100 dark:bg-slate-700 overflow-hidden">
                        <div class="h-2.5 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-sm text-gray-400 text-center py-4">Belum ada data joki bulan ini</p>
            @endif
        </div>

        {{-- Top Customers --}}
        <div class="glass-card rounded-2xl p-6">
            <h3 class="font-bold text-gray-900 dark:text-white mb-4">Top Customer</h3>
            @if($jokiByCustomer->count() > 0)
            <div class="space-y-3">
                @php $rank = 1; @endphp
                @foreach($jokiByCustomer as $name => $count)
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-black {{ $rank === 1 ? 'bg-amber-100 text-amber-700' : ($rank === 2 ? 'bg-gray-100 text-gray-600' : ($rank === 3 ? 'bg-orange-100 text-orange-700' : 'bg-gray-50 text-gray-500')) }}">
                        {{ $rank }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $name ?: '-' }}</p>
                        <p class="text-[10px] text-gray-400">{{ $count }} order</p>
                    </div>
                    <div class="flex gap-0.5">
                        @for($i = 0; $i < min(5, $count); $i++)
                        <div class="h-2 w-2 rounded-full bg-indigo-500"></div>
                        @endfor
                        @if($count > 5)
                        <span class="text-[9px] text-gray-400 ml-1">+{{ $count - 5 }}</span>
                        @endif
                    </div>
                </div>
                @php $rank++; @endphp
                @endforeach
            </div>
            @else
            <p class="text-sm text-gray-400 text-center py-4">Belum ada data</p>
            @endif
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-8">
        <div class="glass-card rounded-xl p-4 text-center">
            <p class="text-3xl font-black text-indigo-600">{{ $jokiSelesai->count() }}</p>
            <p class="text-[10px] text-gray-500 mt-1">Joki Selesai</p>
        </div>
        <div class="glass-card rounded-xl p-4 text-center">
            <p class="text-3xl font-black text-emerald-600">{{ $akunTerjual }}</p>
            <p class="text-[10px] text-gray-500 mt-1">Akun Terjual</p>
        </div>
        <div class="glass-card rounded-xl p-4 text-center">
            <p class="text-3xl font-black text-orange-600">{{ $fruitTerjual }}</p>
            <p class="text-[10px] text-gray-500 mt-1">Fruit Terjual</p>
        </div>
        <div class="glass-card rounded-xl p-4 text-center">
            <p class="text-3xl font-black text-purple-600">{{ $jokiByKategori->count() }}</p>
            <p class="text-[10px] text-gray-500 mt-1">Kategori Aktif</p>
        </div>
    </div>

    {{-- Achievement Style --}}
    @if($jokiSelesai->count() > 0)
    <div class="glass-card rounded-2xl p-6 text-center">
        <h3 class="font-bold text-gray-900 dark:text-white mb-4">Pencapaian Bulan Ini</h3>
        <div class="flex flex-wrap justify-center gap-3">
            @if($jokiSelesai->count() >= 100)
            <div class="rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 px-4 py-2 text-white text-xs font-bold shadow-lg">100+ Joki Selesai</div>
            @elseif($jokiSelesai->count() >= 50)
            <div class="rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 px-4 py-2 text-white text-xs font-bold shadow-lg">50+ Joki Selesai</div>
            @elseif($jokiSelesai->count() >= 20)
            <div class="rounded-xl bg-gradient-to-br from-blue-500 to-indigo-500 px-4 py-2 text-white text-xs font-bold shadow-lg">20+ Joki Selesai</div>
            @endif

            @if($akunTerjual >= 10)
            <div class="rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 px-4 py-2 text-white text-xs font-bold shadow-lg">10+ Akun Terjual</div>
            @endif

            @if($totalTransaksi >= 100)
            <div class="rounded-xl bg-gradient-to-br from-red-500 to-rose-500 px-4 py-2 text-white text-xs font-bold shadow-lg">100+ Transaksi</div>
            @elseif($totalTransaksi >= 50)
            <div class="rounded-xl bg-gradient-to-br from-cyan-500 to-blue-500 px-4 py-2 text-white text-xs font-bold shadow-lg">50+ Transaksi</div>
            @endif

            @if($jokiByCustomer->count() > 0 && $jokiByCustomer->first() >= 5)
            <div class="rounded-xl bg-gradient-to-br from-pink-500 to-fuchsia-500 px-4 py-2 text-white text-xs font-bold shadow-lg">Loyal Customer ({{ $jokiByCustomer->first() }}x order)</div>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
