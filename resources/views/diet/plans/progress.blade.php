@extends('layouts.app')
@section('title', 'Progress Bulanan')

@section('content')
{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h2 class="text-lg font-bold text-gray-900">Progress Bulanan</h2>
        <p class="text-sm text-gray-500">{{ $plan->nama }} &middot; Mulai {{ $plan->tanggal_mulai->translatedFormat('d M Y') }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('diet.plans.monthly.create', $plan) }}" class="btn-success inline-flex items-center gap-1.5 text-sm">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Progress
        </a>
        <a href="{{ route('diet.dashboard') }}" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Kembali</a>
    </div>
</div>

{{-- Overview --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
    <div class="stat-card" style="--accent: linear-gradient(90deg, #6366f1, #8b5cf6)">
        <p class="text-[11px] text-gray-500">Berat Awal</p>
        <p class="text-xl font-extrabold text-indigo-600">{{ $plan->berat_awal }} kg</p>
    </div>
    <div class="stat-card" style="--accent: linear-gradient(90deg, #10b981, #059669)">
        <p class="text-[11px] text-gray-500">Berat Sekarang</p>
        <p class="text-xl font-extrabold text-emerald-600">{{ $plan->berat_sekarang ?? $plan->berat_awal }} kg</p>
    </div>
    <div class="stat-card" style="--accent: linear-gradient(90deg, #3b82f6, #6366f1)">
        <p class="text-[11px] text-gray-500">Berat Target</p>
        <p class="text-xl font-extrabold text-blue-600">{{ $plan->berat_target }} kg</p>
    </div>
    <div class="stat-card" style="--accent: linear-gradient(90deg, #f59e0b, #f97316)">
        <p class="text-[11px] text-gray-500">Total Turun</p>
        <p class="text-xl font-extrabold text-amber-600">{{ number_format($plan->berat_awal - ($plan->berat_sekarang ?? $plan->berat_awal), 1) }} kg</p>
    </div>
</div>

{{-- Progress Bar --}}
@php
    $totalTarget = $plan->berat_awal - $plan->berat_target;
    $totalTurun = $plan->berat_awal - ($plan->berat_sekarang ?? $plan->berat_awal);
    $persen = $totalTarget > 0 ? min(100, round(($totalTurun / $totalTarget) * 100)) : 0;
@endphp
<div class="stat-card mb-6" style="--accent: linear-gradient(90deg, #8b5cf6, #6366f1)">
    <div class="flex items-center justify-between mb-2">
        <p class="text-sm font-semibold text-gray-700">Progress Keseluruhan</p>
        <p class="text-sm font-bold text-purple-600">{{ $persen }}%</p>
    </div>
    <div class="h-3 w-full rounded-full bg-gray-100 overflow-hidden">
        <div class="h-3 rounded-full" style="width: {{ $persen }}%; background: linear-gradient(90deg, #8b5cf6, #6366f1);"></div>
    </div>
    <div class="flex justify-between mt-1.5 text-[11px] text-gray-400">
        <span>{{ $plan->berat_awal }} kg</span>
        <span>Sisa {{ number_format(max(0, ($plan->berat_sekarang ?? $plan->berat_awal) - $plan->berat_target), 1) }} kg lagi</span>
        <span>{{ $plan->berat_target }} kg</span>
    </div>
</div>

{{-- Bulan Ini (Live) --}}
<div class="glass-card rounded-2xl p-5 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-gray-900">Bulan Ini - {{ now()->translatedFormat('F Y') }}</h3>
        <span class="text-[11px] text-gray-400">Hari ke-{{ $liveStats['hari_lewat'] }} dari {{ $liveStats['hari_di_bulan'] }}</span>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="rounded-xl bg-orange-50 border border-orange-100 p-3 text-center">
            <p class="text-[11px] text-gray-500">Avg Kalori Masuk</p>
            <p class="text-lg font-extrabold text-orange-600">{{ number_format($liveStats['avg_kalori_masuk']) }}</p>
            <p class="text-[11px] text-gray-400">kkal/hari</p>
        </div>
        <div class="rounded-xl bg-red-50 border border-red-100 p-3 text-center">
            <p class="text-[11px] text-gray-500">Avg Kalori Bakar</p>
            <p class="text-lg font-extrabold text-red-600">{{ number_format($liveStats['avg_kalori_keluar']) }}</p>
            <p class="text-[11px] text-gray-400">kkal/hari</p>
        </div>
        <div class="rounded-xl bg-blue-50 border border-blue-100 p-3 text-center">
            <p class="text-[11px] text-gray-500">Hari Olahraga</p>
            <p class="text-lg font-extrabold text-blue-600">{{ $liveStats['hari_olahraga'] }}</p>
            <p class="text-[11px] text-gray-400">hari</p>
        </div>
        <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-3 text-center">
            <p class="text-[11px] text-gray-500">Konsistensi</p>
            <p class="text-lg font-extrabold text-emerald-600">{{ $liveStats['konsistensi'] }}%</p>
            <p class="text-[11px] text-gray-400">{{ $liveStats['hari_catat'] }}/{{ $liveStats['hari_lewat'] }} hari</p>
        </div>
    </div>
</div>

{{-- Riwayat Bulanan --}}
<div class="flex items-center justify-between mb-4">
    <h3 class="font-semibold text-gray-900">Riwayat Progress</h3>
    <span class="text-xs text-gray-400">{{ $logs->count() }} bulan tercatat</span>
</div>

@if($logs->isEmpty())
<div class="rounded-2xl bg-gray-50 border border-gray-200 p-8 text-center">
    <div class="inline-flex items-center justify-center h-12 w-12 rounded-2xl bg-gray-100 mb-3">
        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
    </div>
    <p class="text-sm font-medium text-gray-600 mb-1">Belum ada catatan progress</p>
    <p class="text-xs text-gray-400 mb-4">Tambah progress bulanan untuk melihat perkembangan berat badan dari waktu ke waktu.</p>
    <a href="{{ route('diet.plans.monthly.create', $plan) }}" class="btn-success inline-flex items-center gap-1.5 text-sm">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Progress Pertama
    </a>
</div>
@else

{{-- Grafik Berat Badan --}}
<div class="glass-card rounded-2xl p-5 mb-6">
    <h4 class="font-semibold text-gray-900 mb-4">Grafik Berat Badan</h4>
    <div class="relative" style="height: 280px;">
        <canvas id="weightChart"></canvas>
    </div>
</div>

{{-- List Riwayat --}}
<div class="space-y-3">
    @foreach($logs as $log)
    @php
        $bulanLabel = \Carbon\Carbon::parse($log->bulan . '-01')->translatedFormat('F Y');
        $isPositive = $log->berat_turun >= 0;
    @endphp
    <div class="glass-card rounded-2xl overflow-hidden" x-data="{ showDetail: false }">
        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-3 cursor-pointer hover:bg-gray-50/50" @click="showDetail = !showDetail">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl flex items-center justify-center {{ $isPositive ? 'bg-emerald-100' : 'bg-red-100' }}">
                    @if($isPositive)
                    <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    @else
                    <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                    @endif
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">{{ $bulanLabel }}</h4>
                    <p class="text-[11px] text-gray-400">{{ $log->berat_awal_bulan }} kg &rarr; {{ $log->berat_akhir_bulan }} kg</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-bold {{ $isPositive ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                    {{ $isPositive ? '-' : '+' }}{{ abs($log->berat_turun) }} kg
                </span>
                <svg class="h-4 w-4 text-gray-400 transition-transform" :class="showDetail && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
        </div>

        {{-- Detail (collapsible) --}}
        <div x-show="showDetail" x-collapse x-cloak>
            <div class="px-5 py-3 border-t border-gray-100/50">
                <div class="grid grid-cols-3 sm:grid-cols-6 gap-3 text-center mb-3">
                    <div>
                        <p class="text-[11px] text-gray-400">Berat Awal</p>
                        <p class="text-sm font-bold text-gray-700">{{ $log->berat_awal_bulan }} kg</p>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400">Berat Akhir</p>
                        <p class="text-sm font-bold {{ $isPositive ? 'text-emerald-600' : 'text-red-600' }}">{{ $log->berat_akhir_bulan }} kg</p>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400">Avg Kalori</p>
                        <p class="text-sm font-bold text-orange-600">{{ number_format($log->avg_kalori_masuk) }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400">Avg Bakar</p>
                        <p class="text-sm font-bold text-red-600">{{ number_format($log->avg_kalori_keluar) }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400">Olahraga</p>
                        <p class="text-sm font-bold text-blue-600">{{ $log->total_hari_olahraga }} hari</p>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400">Konsistensi</p>
                        <p class="text-sm font-bold text-emerald-600">{{ $log->konsistensi_persen }}%</p>
                    </div>
                </div>

                {{-- Aktivitas Stats --}}
                @if($log->avg_langkah > 0 || $log->avg_tidur > 0 || $log->avg_air_minum > 0)
                <div class="grid grid-cols-4 gap-3 text-center mt-3 pt-3 border-t border-gray-100 dark:border-slate-700">
                    <div>
                        <p class="text-[11px] text-gray-400">Avg Langkah</p>
                        <p class="text-sm font-bold text-blue-600">{{ number_format($log->avg_langkah) }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400">Avg Tidur</p>
                        <p class="text-sm font-bold text-indigo-600">{{ $log->avg_tidur }} jam</p>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400">Avg Air</p>
                        <p class="text-sm font-bold text-cyan-600">{{ number_format($log->avg_air_minum) }}ml</p>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400">Hari Catat</p>
                        <p class="text-sm font-bold text-purple-600">{{ $log->total_hari_aktivitas }}</p>
                    </div>
                </div>
                @endif

                @if($log->catatan)
                <div class="rounded-lg bg-gray-50 p-3 mb-3">
                    <p class="text-xs text-gray-500 mb-1 font-medium">Catatan:</p>
                    <p class="text-sm text-gray-700">{{ $log->catatan }}</p>
                </div>
                @endif

                {{-- Aksi Edit & Hapus --}}
                <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                    <a href="{{ route('diet.plans.monthly.edit', [$plan, $log]) }}" class="inline-flex items-center gap-1 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-100">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </a>
                    <form method="POST" action="{{ route('diet.plans.monthly.destroy', [$plan, $log]) }}" onsubmit="return confirm('Hapus progress {{ $bulanLabel }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-red-50 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-100">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('weightChart');

    const labels = @json($logs->reverse()->map(fn($log) => \Carbon\Carbon::parse($log->bulan . '-01')->translatedFormat('M Y'))->values());
    const weights = @json($logs->reverse()->pluck('berat_akhir_bulan')->values());
    const startWeight = {{ $plan->berat_awal }};
    const targetWeight = {{ $plan->berat_target }};

    // Tambah titik awal (berat awal program)
    labels.unshift('Awal');
    weights.unshift(startWeight);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Berat Badan',
                    data: weights,
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    tension: 0.3,
                    fill: true,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointBackgroundColor: 'rgb(16, 185, 129)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                },
                {
                    label: 'Target',
                    data: Array(labels.length).fill(targetWeight),
                    borderColor: 'rgb(239, 68, 68)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    pointRadius: 0,
                    fill: false,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: { size: 12, weight: '600' }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: { size: 13, weight: 'bold' },
                    bodyFont: { size: 12 },
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + ' kg';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    ticks: {
                        callback: function(value) { return value + ' kg'; },
                        font: { size: 11 }
                    },
                    grid: { color: 'rgba(0, 0, 0, 0.05)' }
                },
                x: {
                    ticks: { font: { size: 11 } },
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
@endif
@endsection
