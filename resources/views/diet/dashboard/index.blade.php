@extends('layouts.app')
@section('title', 'Diet Tracker - Dashboard')

@section('content')
@if(!$planAktif)
<div class="rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-200 p-8 text-center">
    <div class="inline-flex items-center justify-center h-14 w-14 rounded-2xl bg-emerald-100 mb-4">
        <svg class="h-7 w-7 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
    </div>
    <h3 class="text-lg font-bold text-gray-900 mb-1">Belum ada program diet aktif</h3>
    <p class="text-sm text-gray-500 mb-4">Buat program diet untuk mulai tracking dan mendapat rekomendasi.</p>
    <a href="{{ route('diet.plans.create') }}" class="btn-success inline-flex items-center gap-2">Buat Program Diet</a>
</div>
@else

{{-- Reminder Update Bulanan (jika sudah akhir bulan) --}}
@php
    $hariIni = now()->day;
    $hariDiBulan = now()->daysInMonth;
    $sudahAkhirBulan = $hariIni >= ($hariDiBulan - 5); // 5 hari terakhir
    $bulanIniSudahUpdate = $bulanIni['sudah_update'] ?? false;
@endphp
@if($sudahAkhirBulan && !$bulanIniSudahUpdate)
<div class="rounded-xl bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 p-4 mb-6 flex items-start gap-3">
    <div class="shrink-0">
        <div class="h-10 w-10 rounded-full bg-amber-100 flex items-center justify-center">
            <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
    </div>
    <div class="flex-1">
        <h4 class="text-sm font-bold text-amber-900">Waktunya Update Bulanan!</h4>
        <p class="text-sm text-amber-700 mt-0.5">Bulan {{ now()->translatedFormat('F') }} sudah hampir selesai. Timbang berat badan dan catat progress kamu bulan ini.</p>
        <a href="{{ route('diet.plans.monthly.create', $planAktif) }}" class="inline-flex items-center gap-1 mt-2 text-sm font-semibold text-amber-800 hover:text-amber-900">
            Update Sekarang
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
</div>
@endif

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-6">
    <div>
        <h2 class="text-lg font-bold text-gray-900">{{ $planAktif->nama }}</h2>
        <p class="text-sm text-gray-500">Hari ke-{{ $analisis['hari_ke'] }} &middot; {{ now()->translatedFormat('d F Y') }}</p>
    </div>
    <div class="flex items-center gap-2">
        @if($analisis['status'] === 'on_track')
        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">On Track</span>
        @elseif($analisis['status'] === 'over')
        <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">Kalori Berlebih</span>
        @else
        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">Kalori Kurang</span>
        @endif
        <a href="{{ route('diet.plans.edit', $planAktif) }}" class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-200">Edit Program</a>
    </div>
</div>

{{-- Toggle Puasa --}}
<div x-data="{ showPuasaForm: false }" class="mb-6">
    @if($puasaHariIni)
    {{-- Mode Puasa Aktif --}}
    <div class="rounded-2xl bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-emerald-100 flex items-center justify-center text-lg">🌙</div>
                <div>
                    <p class="text-sm font-bold text-emerald-900">Mode Puasa Aktif - {{ $puasaHariIni->label_tipe }}</p>
                    <p class="text-[11px] text-emerald-700">Sahur {{ $puasaHariIni->waktu_sahur }} &middot; Berbuka {{ $puasaHariIni->waktu_berbuka }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @if(!$puasaHariIni->completed)
                <form method="POST" action="{{ route('diet.fasting.complete', $puasaHariIni) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="completed" value="1">
                    <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">Selesai Puasa</button>
                </form>
                @else
                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-200 px-2.5 py-1 text-[11px] font-bold text-emerald-800">
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Alhamdulillah
                </span>
                @endif
                <form method="POST" action="{{ route('diet.fasting.toggle') }}">
                    @csrf
                    <button type="submit" class="rounded-lg bg-red-100 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-200">Batalkan</button>
                </form>
            </div>
        </div>
    </div>
    @else
    {{-- Toggle Puasa --}}
    <div class="rounded-2xl bg-white border border-gray-200 p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-gray-100 flex items-center justify-center text-lg">🌙</div>
                <div>
                    <p class="text-sm font-bold text-gray-900">Hari ini puasa?</p>
                    <p class="text-[11px] text-gray-500">Aktifkan untuk menyesuaikan jadwal makan & minum</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                {{-- Quick toggle --}}
                <form method="POST" action="{{ route('diet.fasting.toggle') }}">
                    @csrf
                    <button type="submit" class="rounded-lg bg-emerald-100 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-200">Aktifkan</button>
                </form>
                <button @click="showPuasaForm = !showPuasaForm" class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-200">Atur Detail</button>
            </div>
        </div>

        {{-- Form Detail Puasa --}}
        <div x-show="showPuasaForm" x-collapse x-cloak class="mt-4 pt-4 border-t border-gray-100">
            <form method="POST" action="{{ route('diet.fasting.store') }}" class="space-y-3">
                @csrf
                <input type="hidden" name="tanggal" value="{{ now()->toDateString() }}">
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 mb-1">Tipe Puasa</label>
                        <select name="tipe" class="w-full rounded-lg border-gray-300 text-xs focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="sunnah">Sunnah</option>
                            <option value="ramadhan">Ramadhan</option>
                            <option value="senin_kamis">Senin-Kamis</option>
                            <option value="ayyamul_bidh">Ayyamul Bidh</option>
                            <option value="daud">Puasa Daud</option>
                            <option value="syawal">Syawal</option>
                            <option value="arafah">Arafah</option>
                            <option value="asyura">Asyura</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 mb-1">Waktu Sahur</label>
                        <input type="time" name="waktu_sahur" value="04:00" class="w-full rounded-lg border-gray-300 text-xs focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 mb-1">Waktu Berbuka</label>
                        <input type="time" name="waktu_berbuka" value="18:15" class="w-full rounded-lg border-gray-300 text-xs focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                </div>
                <button type="submit" class="btn-success text-xs px-4 py-2">Aktifkan Puasa</button>
            </form>
        </div>
    </div>
    @endif
</div>

{{-- Tips Puasa --}}
@if($puasaHariIni && count($tipsPuasa) > 0)
<div class="mb-6 space-y-2">
    @foreach(array_slice($tipsPuasa, 0, 3) as $tip)
    <div class="flex items-start gap-2.5 rounded-xl p-3 text-sm {{ $tip['type'] === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : ($tip['type'] === 'warning' ? 'bg-amber-50 text-amber-700 border border-amber-100' : 'bg-blue-50 text-blue-700 border border-blue-100') }}">
        @if($tip['type'] === 'success')
        <span class="shrink-0 mt-0.5">🌙</span>
        @elseif($tip['type'] === 'warning')
        <span class="shrink-0 mt-0.5">⚠️</span>
        @else
        <span class="shrink-0 mt-0.5">💡</span>
        @endif
        <span>{{ $tip['text'] }}</span>
    </div>
    @endforeach
</div>
@endif

{{-- Stats --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
    <div class="stat-card" style="--accent: linear-gradient(90deg, #f97316, #ef4444)">
        <p class="text-xs text-gray-500">Kalori Masuk</p>
        <p class="text-2xl font-extrabold text-orange-600">{{ number_format($analisis['kalori_masuk']) }}</p>
        <p class="text-[11px] text-gray-400">/ {{ number_format($planAktif->kalori_harian_target) }} target</p>
    </div>
    <div class="stat-card" style="--accent: linear-gradient(90deg, #ef4444, #dc2626)">
        <p class="text-xs text-gray-500">Kalori Terbakar</p>
        <p class="text-2xl font-extrabold text-red-600">{{ number_format($analisis['kalori_keluar']) }}</p>
    </div>
    <div class="stat-card" style="--accent: linear-gradient(90deg, #10b981, #059669)">
        <p class="text-xs text-gray-500">Sisa Budget</p>
        <p class="text-2xl font-extrabold {{ $analisis['sisa_kalori'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ number_format($analisis['sisa_kalori']) }}</p>
        <p class="text-[11px] text-gray-400">kkal</p>
    </div>
    <div class="stat-card" style="--accent: linear-gradient(90deg, #3b82f6, #6366f1)">
        <p class="text-xs text-gray-500">Berat Badan</p>
        <p class="text-2xl font-extrabold text-blue-600">{{ $analisis['berat_sekarang'] }}</p>
        <p class="text-[11px] text-gray-400">target {{ $planAktif->berat_target }} kg</p>
    </div>
</div>

{{-- Minum Air Hari Ini --}}
@if($targetAir > 0)
@php
    $persenMinum = min(100, round(($totalMinum / max(1, $targetAir)) * 100));
    $gelasCount = floor($totalMinum / 250);
    $targetGelas = ceil($targetAir / 250);
@endphp
<div class="glass-card rounded-2xl p-4 mb-6">
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
            <div class="h-8 w-8 rounded-lg bg-blue-100 flex items-center justify-center">
                <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-900">Minum Air</p>
                <p class="text-[11px] text-gray-400">{{ number_format($totalMinum) }} / {{ number_format($targetAir) }}ml</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if($totalMinum >= $targetAir)
            <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-bold text-emerald-700">Tercapai!</span>
            @else
            <span class="text-xs font-bold {{ $persenMinum >= 50 ? 'text-blue-600' : 'text-gray-400' }}">{{ $persenMinum }}%</span>
            @endif
        </div>
    </div>
    {{-- Progress bar --}}
    <div class="h-2.5 w-full rounded-full bg-gray-100 overflow-hidden mb-3">
        <div class="h-2.5 rounded-full transition-all" style="width: {{ $persenMinum }}%; background: {{ $totalMinum >= $targetAir ? 'linear-gradient(90deg, #10b981, #059669)' : 'linear-gradient(90deg, #3b82f6, #6366f1)' }};"></div>
    </div>
    {{-- Quick add buttons --}}
    <div class="flex gap-2">
        @foreach([250, 500] as $ml)
        <form method="POST" action="{{ route('diet.water.store') }}">
            @csrf
            <input type="hidden" name="jumlah_ml" value="{{ $ml }}">
            <input type="hidden" name="tanggal" value="{{ now()->toDateString() }}">
            <button type="submit" class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100 transition-colors">
                +{{ $ml }}ml
            </button>
        </form>
        @endforeach
        <a href="{{ route('diet.meals.index') }}" class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-200">Detail</a>
    </div>
</div>
@endif

{{-- Smart Info --}}
@if($smartPlan)
<div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-2 mb-6">
    <div class="rounded-xl bg-white border border-gray-100 p-3 text-center">
        <p class="text-[11px] text-gray-400">BMI</p>
        <p class="text-lg font-extrabold {{ $smartPlan['bmi']['kategori'] === 'Normal' ? 'text-emerald-600' : ($smartPlan['bmi']['kategori'] === 'Kurus' ? 'text-amber-600' : 'text-red-600') }}">{{ $smartPlan['bmi']['bmi'] }}</p>
        <p class="text-[11px] font-medium {{ $smartPlan['bmi']['kategori'] === 'Normal' ? 'text-emerald-500' : ($smartPlan['bmi']['kategori'] === 'Kurus' ? 'text-amber-500' : 'text-red-500') }}">{{ $smartPlan['bmi']['kategori'] }}</p>
    </div>
    <div class="rounded-xl bg-white border border-gray-100 p-3 text-center">
        <p class="text-[11px] text-gray-400">Berat Ideal</p>
        <p class="text-lg font-extrabold text-blue-600">{{ $smartPlan['berat_ideal'] }}</p>
        <p class="text-[11px] text-gray-400">kg</p>
    </div>
    <div class="rounded-xl bg-white border border-gray-100 p-3 text-center">
        <p class="text-[11px] text-gray-400">Protein</p>
        <p class="text-lg font-extrabold text-purple-600">{{ $smartPlan['makro']['protein'] }}g</p>
        <p class="text-[11px] text-gray-400">/hari</p>
    </div>
    <div class="rounded-xl bg-white border border-gray-100 p-3 text-center">
        <p class="text-[11px] text-gray-400">Air Minum</p>
        <p class="text-lg font-extrabold text-cyan-600">{{ number_format($smartPlan['target_harian']['air_ml'] / 1000, 1) }}L</p>
        <p class="text-[11px] text-gray-400">/hari</p>
    </div>
    <div class="rounded-xl bg-white border border-gray-100 p-3 text-center">
        <p class="text-[11px] text-gray-400">Langkah</p>
        <p class="text-lg font-extrabold text-teal-600">{{ number_format($smartPlan['target_harian']['langkah'] / 1000, 1) }}k</p>
        <p class="text-[11px] text-gray-400">/hari</p>
    </div>
    <div class="rounded-xl bg-white border border-gray-100 p-3 text-center">
        <p class="text-[11px] text-gray-400">Olahraga</p>
        <p class="text-lg font-extrabold text-orange-600">{{ $smartPlan['target_harian']['olahraga_per_minggu'] }}x</p>
        <p class="text-[11px] text-gray-400">/minggu</p>
    </div>
</div>
@endif

{{-- Progress Bar --}}
<div class="stat-card mb-6" style="--accent: linear-gradient(90deg, #8b5cf6, #6366f1)">
    <div class="flex items-center justify-between mb-2">
        <p class="text-sm font-semibold text-gray-700">Progress Berat Badan</p>
        <p class="text-sm font-bold text-purple-600">{{ $analisis['progress_persen'] }}%</p>
    </div>
    <div class="h-3 w-full rounded-full bg-gray-100 overflow-hidden">
        <div class="h-3 rounded-full transition-all" style="width: {{ $analisis['progress_persen'] }}%; background: linear-gradient(90deg, #8b5cf6, #6366f1);"></div>
    </div>
    <div class="flex justify-between mt-1.5 text-[11px] text-gray-400">
        <span>{{ $planAktif->berat_awal }} kg</span>
        <span>Turun {{ number_format($analisis['berat_turun'], 1) }} kg</span>
        <span>{{ $planAktif->berat_target }} kg</span>
    </div>
</div>

{{-- Progress Bulan Ini --}}
@if($bulanIni)
<div class="glass-card rounded-2xl p-5 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
        <div>
            <h3 class="font-semibold text-gray-900">Progress {{ $bulanIni['label'] }}</h3>
            <p class="text-[11px] text-gray-400">Hari ke-{{ $bulanIni['hari_lewat'] }} dari {{ $bulanIni['hari_di_bulan'] }}</p>
        </div>
        <div class="flex gap-2">
            @if(!$bulanIni['sudah_update'])
            <a href="{{ route('diet.plans.monthly.create', $planAktif) }}" class="btn-success text-xs px-3 py-1.5">Update Bulanan</a>
            @else
            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-semibold text-emerald-700">
                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Sudah update
            </span>
            @endif
            <a href="{{ route('diet.plans.progress', $planAktif) }}" class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-200">Lihat Semua</a>
        </div>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="rounded-xl bg-orange-50 border border-orange-100 p-2.5 text-center">
            <p class="text-[11px] text-gray-500">Avg Kalori/hari</p>
            <p class="text-base font-extrabold text-orange-600">{{ number_format($bulanIni['avg_kalori_masuk']) }}</p>
        </div>
        <div class="rounded-xl bg-red-50 border border-red-100 p-2.5 text-center">
            <p class="text-[11px] text-gray-500">Avg Bakar/hari</p>
            <p class="text-base font-extrabold text-red-600">{{ number_format($bulanIni['avg_kalori_keluar']) }}</p>
        </div>
        <div class="rounded-xl bg-blue-50 border border-blue-100 p-2.5 text-center">
            <p class="text-[11px] text-gray-500">Olahraga</p>
            <p class="text-base font-extrabold text-blue-600">{{ $bulanIni['hari_olahraga'] }} hari</p>
        </div>
        <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-2.5 text-center">
            <p class="text-[11px] text-gray-500">Konsistensi</p>
            <p class="text-base font-extrabold text-emerald-600">{{ $bulanIni['konsistensi'] }}%</p>
        </div>
    </div>
</div>
@endif

{{-- Timeline Bulanan --}}
@if($allLogs->count() > 0)
<div class="glass-card rounded-2xl p-5 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-gray-900">Riwayat Bulanan</h3>
        <a href="{{ route('diet.plans.progress', $planAktif) }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-800">Detail &rarr;</a>
    </div>
    <div class="overflow-x-auto">
        <div class="flex gap-3 min-w-max">
            @foreach($allLogs as $log)
            @php
                $lbl = \Carbon\Carbon::parse($log->bulan . '-01')->translatedFormat('M Y');
                $pos = $log->berat_turun >= 0;
            @endphp
            <div class="rounded-xl border p-3 min-w-[130px] text-center {{ $pos ? 'bg-emerald-50 border-emerald-200' : 'bg-red-50 border-red-200' }}">
                <p class="text-[11px] font-semibold text-gray-600">{{ $lbl }}</p>
                <p class="text-lg font-extrabold {{ $pos ? 'text-emerald-600' : 'text-red-600' }}">{{ $pos ? '-' : '+' }}{{ abs($log->berat_turun) }} kg</p>
                <p class="text-[11px] text-gray-400">{{ $log->berat_akhir_bulan }} kg</p>
            </div>
            @endforeach
        </div>
    </div>
</div>
@elseif($lastMonthLog === null && $analisis['hari_ke'] > 1)
<div class="rounded-xl bg-amber-50 border border-amber-200 p-4 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <p class="text-sm font-medium text-amber-800">Belum ada catatan progress bulanan</p>
        <p class="text-[11px] text-amber-600">Update bulanan untuk melihat perkembangan dari bulan ke bulan.</p>
    </div>
    <a href="{{ route('diet.plans.monthly.create', $planAktif) }}" class="btn-success text-xs px-3 py-1.5 shrink-0">Update Sekarang</a>
</div>
@endif

{{-- Tips & Analisis --}}
@if(count($analisis['tips']) > 0)
<div class="mb-6 space-y-2">
    @foreach($analisis['tips'] as $tip)
    <div class="flex items-start gap-2.5 rounded-xl p-3 text-sm {{ $tip['type'] === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : ($tip['type'] === 'warning' ? 'bg-amber-50 text-amber-700 border border-amber-100' : 'bg-blue-50 text-blue-700 border border-blue-100') }}">
        @if($tip['type'] === 'success')
        <svg class="h-4 w-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        @elseif($tip['type'] === 'warning')
        <svg class="h-4 w-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
        @else
        <svg class="h-4 w-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        @endif
        <span>{{ $tip['text'] }}</span>
    </div>
    @endforeach
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    {{-- Makanan Hari Ini --}}
    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="px-5 py-3 flex items-center justify-between border-b border-gray-100/50">
            <h3 class="font-semibold text-gray-900">Makanan Hari Ini</h3>
            <a href="{{ route('diet.meals.create') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-800">+ Tambah</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($makanHariIni as $makan)
            <div class="px-5 py-2.5 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $makan->nama_makanan }}</p>
                    <p class="text-[11px] text-gray-400">{{ ucfirst(str_replace('_', ' ', $makan->waktu_makan)) }}</p>
                </div>
                <span class="text-sm font-bold text-orange-600">{{ $makan->kalori }} kkal</span>
            </div>
            @empty
            <p class="px-5 py-6 text-sm text-gray-400 text-center">Belum ada catatan makan</p>
            @endforelse
        </div>
    </div>

    {{-- Olahraga Hari Ini --}}
    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="px-5 py-3 flex items-center justify-between border-b border-gray-100/50">
            <h3 class="font-semibold text-gray-900">Olahraga Hari Ini</h3>
            <a href="{{ route('diet.exercises.create') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-800">+ Tambah</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($olahragaHariIni as $or)
            <div class="px-5 py-2.5 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $or->jenis_olahraga }}</p>
                    <p class="text-[11px] text-gray-400">{{ $or->durasi_menit }} menit</p>
                </div>
                <span class="text-sm font-bold text-red-600">-{{ $or->kalori_terbakar }} kkal</span>
            </div>
            @empty
            <p class="px-5 py-6 text-sm text-gray-400 text-center">Belum ada olahraga</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Aktivitas Hari Ini --}}
<div class="glass-card rounded-2xl p-5 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-gray-900">Aktivitas Hari Ini</h3>
        @if(!$aktivitasHariIni)
        <a href="{{ route('diet.activities.create') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-800">+ Catat</a>
        @else
        <a href="{{ route('diet.activities.edit', $aktivitasHariIni) }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-800">Edit</a>
        @endif
    </div>
    @if($aktivitasHariIni)
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="rounded-xl bg-blue-50 dark:bg-blue-950/30 border border-blue-100 dark:border-blue-900/50 p-2.5 text-center">
            <p class="text-[11px] text-gray-500">Langkah</p>
            <p class="text-base font-extrabold text-blue-600">{{ number_format($aktivitasHariIni->langkah_kaki ?? 0) }}</p>
            <p class="text-[10px] text-gray-400">/ {{ number_format($smartPlan['target_harian']['langkah']) }}</p>
        </div>
        <div class="rounded-xl bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900/50 p-2.5 text-center">
            <p class="text-[11px] text-gray-500">Tidur</p>
            <p class="text-base font-extrabold text-indigo-600">{{ $aktivitasHariIni->jam_tidur ?? 0 }} jam</p>
            <p class="text-[10px] text-gray-400">/ {{ $smartPlan['target_harian']['tidur_jam'] }} jam</p>
        </div>
        <div class="rounded-xl bg-cyan-50 dark:bg-cyan-950/30 border border-cyan-100 dark:border-cyan-900/50 p-2.5 text-center">
            <p class="text-[11px] text-gray-500">Air Minum</p>
            <p class="text-base font-extrabold text-cyan-600">{{ number_format($aktivitasHariIni->air_minum_ml ?? 0) }}</p>
            <p class="text-[10px] text-gray-400">ml</p>
        </div>
        <div class="rounded-xl bg-purple-50 dark:bg-purple-950/30 border border-purple-100 dark:border-purple-900/50 p-2.5 text-center">
            <p class="text-[11px] text-gray-500">Berat</p>
            <p class="text-base font-extrabold text-purple-600">{{ $aktivitasHariIni->berat_badan ?? '-' }}</p>
            <p class="text-[10px] text-gray-400">kg</p>
        </div>
    </div>
    @else
    <div class="rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 p-4 text-center">
        <p class="text-sm text-gray-400">Belum ada catatan aktivitas hari ini</p>
        <a href="{{ route('diet.activities.create') }}" class="inline-flex items-center gap-1 mt-2 text-xs font-semibold text-emerald-600 hover:text-emerald-800">
            Catat Sekarang
            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
    @endif
</div>

{{-- Rekomendasi Menu --}}
<div class="glass-card rounded-2xl p-5 mb-6">
    <h3 class="font-semibold text-gray-900 mb-4">Rekomendasi Menu Hari Ini <span class="text-xs font-normal text-gray-400">({{ number_format($planAktif->kalori_harian_target) }} kkal target)</span></h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
        @foreach($rekomendasiMenu as $waktu => $data)
        <div class="rounded-xl bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-100 p-3">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-bold text-emerald-700 uppercase">{{ str_replace('_', ' ', $waktu) }}</p>
                <span class="text-[11px] text-emerald-600">~{{ $data['target_kalori'] }} kkal</span>
            </div>
            @forelse($data['menu'] as $food)
            <p class="text-sm text-gray-700">{{ $food->nama }} <span class="text-[11px] text-gray-400">({{ $food->kalori }})</span></p>
            @empty
            <p class="text-xs text-gray-400">-</p>
            @endforelse
        </div>
        @endforeach
    </div>
</div>

{{-- Jadwal Puasa Lengkap (jika puasa aktif) --}}
@if($puasaHariIni && $configPuasa)
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    {{-- Jadwal Minum Puasa --}}
    @if(!empty($configPuasa['jadwal_minum']))
    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100/50 bg-blue-50/50">
            <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                <span>💧</span> Jadwal Minum
            </h3>
            <p class="text-[11px] text-gray-500">Pola minum saat puasa {{ $puasaHariIni->label_tipe }}</p>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($configPuasa['jadwal_minum'] as $minum)
            <div class="px-5 py-2.5 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-blue-600 w-12">{{ $minum['waktu'] }}</span>
                    <p class="text-sm text-gray-700">{{ $minum['label'] }}</p>
                </div>
                <span class="text-xs font-bold text-blue-600">{{ $minum['ml'] }}ml</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Jadwal Olahraga Puasa --}}
    @if(!empty($configPuasa['jadwal_olahraga']))
    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100/50 bg-red-50/50">
            <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                <span>🏃</span> Olahraga Puasa
            </h3>
            <p class="text-[11px] text-gray-500">{{ $configPuasa['boleh_olahraga_berat'] ? 'Boleh olahraga berat' : 'Olahraga ringan saja' }}</p>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($configPuasa['jadwal_olahraga'] as $or)
            <div class="px-5 py-3">
                <div class="flex items-center justify-between mb-1">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-red-600 w-12">{{ $or['waktu'] }}</span>
                        <p class="text-sm font-semibold text-gray-900">{{ $or['jenis'] }}</p>
                    </div>
                    <span class="text-xs font-bold text-red-600">{{ $or['durasi'] }}'</span>
                </div>
                <p class="text-[11px] text-gray-500 ml-14">{{ $or['catatan'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Jadwal Aktivitas Puasa --}}
    @if(!empty($configPuasa['jadwal_aktivitas']))
    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100/50 bg-emerald-50/50">
            <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                <span>📋</span> Aktivitas Hari Ini
            </h3>
            <p class="text-[11px] text-gray-500">Jadwal harian puasa {{ $puasaHariIni->label_tipe }}</p>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($configPuasa['jadwal_aktivitas'] as $akt)
            @php
                $jamNow = now()->format('H:i');
                $sudahLewat = $akt['waktu'] <= $jamNow;
                $sedangBerjalan = $akt['waktu'] <= $jamNow && (($configPuasa['jadwal_aktivitas'][array_search($akt, $configPuasa['jadwal_aktivitas']) + 1]['waktu'] ?? '23:59') > $jamNow);
            @endphp
            <div class="px-5 py-2.5 flex items-center gap-3 {{ $sedangBerjalan ? 'bg-emerald-50' : '' }}">
                <div class="flex items-center justify-center h-5 w-5 shrink-0">
                    @if($sedangBerjalan)
                    <div class="h-3 w-3 rounded-full bg-emerald-500 animate-pulse"></div>
                    @elseif($sudahLewat)
                    <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    @else
                    <div class="h-2.5 w-2.5 rounded-full border-2 border-gray-300"></div>
                    @endif
                </div>
                <span class="text-xs font-bold text-gray-500 w-12">{{ $akt['waktu'] }}</span>
                <p class="text-sm {{ $sedangBerjalan ? 'font-bold text-emerald-800' : ($sudahLewat ? 'text-gray-400 line-through' : 'text-gray-700') }}">{{ $akt['aktivitas'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endif

{{-- Rekomendasi Olahraga --}}
@if(!$puasaHariIni)
<div class="glass-card rounded-2xl p-5">
    <h3 class="font-semibold text-gray-900 mb-4">Rekomendasi Olahraga</h3>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-2">
        @foreach($rekomendasiOlahraga as $ex)
        <div class="rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 p-3 text-center">
            <p class="text-sm font-semibold text-gray-900 mb-1">{{ $ex->nama }}</p>
            <p class="text-lg font-extrabold text-blue-600">{{ $ex->durasi_rekomendasi }}'</p>
            <p class="text-[11px] text-gray-400">~{{ $ex->kalori_estimasi }} kkal</p>
        </div>
        @endforeach
    </div>
</div>
@endif

@endif
@endsection
