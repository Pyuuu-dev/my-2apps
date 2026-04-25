@extends('layouts.app')
@section('title', 'Beranda')

@section('content')
<div class="max-w-5xl mx-auto">
    {{-- Header --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center h-16 w-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 mb-4 shadow-lg shadow-indigo-500/20">
            <svg class="h-9 w-9 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        </div>
        <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white">Selamat {{ now()->hour < 12 ? 'Pagi' : (now()->hour < 15 ? 'Siang' : (now()->hour < 18 ? 'Sore' : 'Malam')) }}!</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ now()->translatedFormat('l, d F Y') }} &middot; {{ now()->format('H:i') }} WIB</p>
    </div>

    {{-- App Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        {{-- Blox Fruit --}}
        <a href="{{ route('bloxfruit.dashboard') }}" class="group relative overflow-hidden rounded-2xl p-6 text-white shadow-xl transition-all duration-300 hover:shadow-2xl hover:scale-[1.02]" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm shadow-lg">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <svg class="h-5 w-5 transition-transform group-hover:translate-x-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </div>
                <h3 class="text-xl font-bold mb-1">Blox Fruit</h3>
                <p class="text-indigo-100 text-xs mb-4">Manajemen stok & penjualan</p>
                <div class="grid grid-cols-4 gap-2">
                    <div class="rounded-lg bg-white/15 p-2 text-center">
                        <p class="text-lg font-extrabold">{{ $bfStats['total_buah'] }}</p>
                        <p class="text-[10px] text-indigo-200">Buah</p>
                    </div>
                    <div class="rounded-lg bg-white/15 p-2 text-center">
                        <p class="text-lg font-extrabold">{{ $bfStats['total_skin'] }}</p>
                        <p class="text-[10px] text-indigo-200">Skin</p>
                    </div>
                    <div class="rounded-lg bg-white/15 p-2 text-center">
                        <p class="text-lg font-extrabold">{{ $bfStats['total_akun'] }}</p>
                        <p class="text-[10px] text-indigo-200">Akun</p>
                    </div>
                    <div class="rounded-lg bg-white/15 p-2 text-center">
                        <p class="text-lg font-extrabold">{{ $bfStats['joki_aktif'] }}</p>
                        <p class="text-[10px] text-indigo-200">Joki</p>
                    </div>
                </div>
            </div>
        </a>

        {{-- Diet Tracker --}}
        <a href="{{ route('diet.dashboard') }}" class="group relative overflow-hidden rounded-2xl p-6 text-white shadow-xl transition-all duration-300 hover:shadow-2xl hover:scale-[1.02]" style="background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm shadow-lg">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <svg class="h-5 w-5 transition-transform group-hover:translate-x-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </div>
                <h3 class="text-xl font-bold mb-1">Diet Tracker</h3>
                <p class="text-emerald-100 text-xs mb-4">Monitoring diet & kesehatan</p>
                @if($dtStats)
                <div class="grid grid-cols-4 gap-2">
                    <div class="rounded-lg bg-white/15 p-2 text-center">
                        <p class="text-lg font-extrabold">{{ number_format($dtStats['kalori_masuk']) }}</p>
                        <p class="text-[10px] text-emerald-200">Kalori</p>
                    </div>
                    <div class="rounded-lg bg-white/15 p-2 text-center">
                        <p class="text-lg font-extrabold">{{ number_format($dtStats['total_minum']) }}</p>
                        <p class="text-[10px] text-emerald-200">ml Air</p>
                    </div>
                    <div class="rounded-lg bg-white/15 p-2 text-center">
                        <p class="text-lg font-extrabold">{{ $dtStats['berat_sekarang'] }}</p>
                        <p class="text-[10px] text-emerald-200">kg</p>
                    </div>
                    <div class="rounded-lg bg-white/15 p-2 text-center">
                        <p class="text-lg font-extrabold">{{ $dtStats['bmi']['bmi'] }}</p>
                        <p class="text-[10px] text-emerald-200">BMI</p>
                    </div>
                </div>
                @else
                <div class="rounded-lg bg-white/15 p-3 text-center">
                    <p class="text-sm text-emerald-100">Belum ada program diet. Klik untuk mulai!</p>
                </div>
                @endif
            </div>
        </a>
    </div>

    {{-- Quick Stats Diet (jika ada) --}}
    @if($dtStats)
    <div class="glass-card rounded-2xl p-5 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-900 dark:text-white">Ringkasan Hari Ini</h3>
            <a href="{{ route('diet.dashboard') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-800">Detail &rarr;</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            {{-- Kalori --}}
            <div class="rounded-xl bg-orange-50 dark:bg-orange-950/30 border border-orange-100 dark:border-orange-900/50 p-3">
                <p class="text-[11px] text-gray-500 dark:text-gray-400 mb-1">Kalori Masuk</p>
                <p class="text-lg font-extrabold text-orange-600">{{ number_format($dtStats['kalori_masuk']) }}</p>
                <div class="h-1.5 w-full rounded-full bg-orange-100 dark:bg-orange-900/50 mt-2 overflow-hidden">
                    <div class="h-1.5 rounded-full bg-orange-500" style="width: {{ min(100, round(($dtStats['kalori_masuk'] / max(1, $dtStats['target_kalori'])) * 100)) }}%"></div>
                </div>
                <p class="text-[10px] text-gray-400 mt-1">/ {{ number_format($dtStats['target_kalori']) }} target</p>
            </div>
            {{-- Minum --}}
            <div class="rounded-xl bg-blue-50 dark:bg-blue-950/30 border border-blue-100 dark:border-blue-900/50 p-3">
                <p class="text-[11px] text-gray-500 dark:text-gray-400 mb-1">Minum Air</p>
                <p class="text-lg font-extrabold text-blue-600">{{ number_format($dtStats['total_minum']) }}<span class="text-xs font-normal">ml</span></p>
                <div class="h-1.5 w-full rounded-full bg-blue-100 dark:bg-blue-900/50 mt-2 overflow-hidden">
                    <div class="h-1.5 rounded-full bg-blue-500" style="width: {{ min(100, round(($dtStats['total_minum'] / max(1, $dtStats['target_air'])) * 100)) }}%"></div>
                </div>
                <p class="text-[10px] text-gray-400 mt-1">/ {{ number_format($dtStats['target_air']) }}ml target</p>
            </div>
            {{-- Berat --}}
            <div class="rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-900/50 p-3">
                <p class="text-[11px] text-gray-500 dark:text-gray-400 mb-1">Berat Badan</p>
                <p class="text-lg font-extrabold text-emerald-600">{{ $dtStats['berat_sekarang'] }} <span class="text-xs font-normal">kg</span></p>
                <p class="text-[10px] text-gray-400 mt-2">Target: {{ $dtStats['berat_target'] }} kg</p>
                <p class="text-[10px] text-emerald-600 font-semibold">Sisa {{ number_format(max(0, $dtStats['berat_sekarang'] - $dtStats['berat_target']), 1) }} kg</p>
            </div>
            {{-- BMI --}}
            <div class="rounded-xl bg-purple-50 dark:bg-purple-950/30 border border-purple-100 dark:border-purple-900/50 p-3">
                <p class="text-[11px] text-gray-500 dark:text-gray-400 mb-1">BMI</p>
                <p class="text-lg font-extrabold {{ $dtStats['bmi']['kategori'] === 'Normal' ? 'text-emerald-600' : ($dtStats['bmi']['kategori'] === 'Kurus' ? 'text-amber-600' : 'text-red-600') }}">{{ $dtStats['bmi']['bmi'] }}</p>
                <p class="text-[10px] mt-2 font-semibold {{ $dtStats['bmi']['kategori'] === 'Normal' ? 'text-emerald-600' : ($dtStats['bmi']['kategori'] === 'Kurus' ? 'text-amber-600' : 'text-red-600') }}">{{ $dtStats['bmi']['kategori'] }}</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Quick Actions --}}
    <div class="glass-card rounded-2xl p-5">
        <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <a href="{{ route('diet.meals.create') }}" class="rounded-xl border border-gray-200 dark:border-slate-700 p-3 text-center hover:border-emerald-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 transition-colors">
                <span class="text-2xl">🍽</span>
                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mt-1">Catat Makan</p>
            </a>
            <a href="{{ route('diet.exercises.create') }}" class="rounded-xl border border-gray-200 dark:border-slate-700 p-3 text-center hover:border-blue-300 hover:bg-blue-50 dark:hover:bg-blue-950/30 transition-colors">
                <span class="text-2xl">🏃</span>
                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mt-1">Catat Olahraga</p>
            </a>
            <a href="{{ route('bloxfruit.search') }}" class="rounded-xl border border-gray-200 dark:border-slate-700 p-3 text-center hover:border-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-950/30 transition-colors">
                <span class="text-2xl">🔍</span>
                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mt-1">Cari Stok</p>
            </a>
            <a href="{{ route('bloxfruit.joki.index') }}" class="rounded-xl border border-gray-200 dark:border-slate-700 p-3 text-center hover:border-purple-300 hover:bg-purple-50 dark:hover:bg-purple-950/30 transition-colors">
                <span class="text-2xl">📋</span>
                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mt-1">List Joki</p>
            </a>
        </div>
    </div>
</div>
@endsection
