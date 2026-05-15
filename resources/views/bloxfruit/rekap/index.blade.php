@extends('layouts.app')
@section('title', 'Rekap Bulanan')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    {{-- Header --}}
    <x-page-header eyebrow="Laporan" title="Rekap Bulanan" subtitle="Ringkasan performa LDC Store">
        <x-slot:actions>
            <form method="GET">
                <select name="bulan" onchange="this.form.submit()" class="h-9 px-3 rounded-lg bg-[var(--surface)] border border-[var(--border)] text-[var(--text)] text-sm focus:border-[var(--accent)] focus:ring-0 focus:outline-none">
                    @foreach($bulanList as $b)
                    <option value="{{ $b }}" @selected($bulan === $b)>{{ format_bulan($b) }}</option>
                    @endforeach
                </select>
            </form>
        </x-slot:actions>
    </x-page-header>

    {{-- Hero Performance Card --}}
    <div class="card p-6 sm:p-7 accent-glow card-hairline">
        <div class="flex items-baseline justify-between gap-3 flex-wrap mb-2">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[var(--accent)] section-bar">{{ $bulanLabel }}</p>
            <p class="text-[11px] text-[var(--text-subtle)]">vs {{ $prevBulanLabel }}</p>
        </div>
        <h2 class="text-2xl sm:text-[28px] font-bold tracking-tight text-[var(--text)]">Performance Report</h2>
        <p class="text-sm text-[var(--text-muted)] mt-1">Total ringkasan keuangan dan operasional</p>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 pt-6 border-t border-[var(--border)]">
            <div>
                <div class="flex items-baseline justify-between gap-2 mb-1">
                    <p class="text-[11px] uppercase tracking-wider text-[var(--text-subtle)]">Pendapatan</p>
                    @php $c = $comparison['revenue']; @endphp
                    <span class="trend-pill {{ $c['direction'] === 'up' ? 'trend-up' : ($c['direction'] === 'down' ? 'trend-down' : 'trend-flat') }}">
                        @if($c['direction'] === 'up')
                            <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 17l9.2-9.2M17 17V7H7"/></svg>
                        @elseif($c['direction'] === 'down')
                            <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 7l-9.2 9.2M7 7v10h10"/></svg>
                        @endif
                        {{ $c['label'] }}
                    </span>
                </div>
                <p class="text-2xl font-bold num text-[var(--info)]">{{ format_rupiah($totalRevenue) }}</p>
                <p class="text-[11px] text-[var(--text-subtle)] mt-1 num">{{ $totalTransaksi }} transaksi</p>
            </div>
            <div>
                <div class="flex items-baseline justify-between gap-2 mb-1">
                    <p class="text-[11px] uppercase tracking-wider text-[var(--text-subtle)]">Keuntungan</p>
                    @php $c = $comparison['profit']; @endphp
                    <span class="trend-pill {{ $c['direction'] === 'up' ? 'trend-up' : ($c['direction'] === 'down' ? 'trend-down' : 'trend-flat') }}">
                        @if($c['direction'] === 'up')
                            <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 17l9.2-9.2M17 17V7H7"/></svg>
                        @elseif($c['direction'] === 'down')
                            <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 7l-9.2 9.2M7 7v10h10"/></svg>
                        @endif
                        {{ $c['label'] }}
                    </span>
                </div>
                <p class="text-2xl font-bold num {{ $totalProfit >= 0 ? 'text-[var(--success)]' : 'text-[var(--danger)]' }}">{{ format_rupiah($totalProfit) }}</p>
                <p class="text-[11px] text-[var(--text-subtle)] mt-1 num">
                    Margin {{ $margin }}%
                    @if($marginDelta != 0)
                    <span class="{{ $marginDelta > 0 ? 'text-[var(--success)]' : 'text-[var(--danger)]' }}">
                        ({{ $marginDelta > 0 ? '+' : '' }}{{ $marginDelta }}pt)
                    </span>
                    @endif
                </p>
            </div>
            <div>
                <p class="text-[11px] uppercase tracking-wider text-[var(--text-subtle)] mb-1">Modal</p>
                <p class="text-2xl font-bold num text-[var(--text-muted)]">{{ format_rupiah($totalModal) }}</p>
            </div>
            <div>
                <div class="flex items-baseline justify-between gap-2 mb-1">
                    <p class="text-[11px] uppercase tracking-wider text-[var(--text-subtle)]">Joki Selesai</p>
                    @php $c = $comparison['joki']; @endphp
                    <span class="trend-pill {{ $c['direction'] === 'up' ? 'trend-up' : ($c['direction'] === 'down' ? 'trend-down' : 'trend-flat') }}">
                        @if($c['direction'] === 'up')
                            <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 17l9.2-9.2M17 17V7H7"/></svg>
                        @elseif($c['direction'] === 'down')
                            <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 7l-9.2 9.2M7 7v10h10"/></svg>
                        @endif
                        {{ $c['label'] }}
                    </span>
                </div>
                <p class="text-2xl font-bold num text-[var(--warning)]">{{ $jokiSelesai->count() }}</p>
                <p class="text-[11px] text-[var(--text-subtle)] mt-1 num">{{ format_rupiah($jokiRevenue) }}</p>
            </div>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <x-stat-card label="Joki Selesai" :value="$jokiSelesai->count()" :sub="'Revenue: ' . format_rupiah($jokiRevenue)" tone="accent" :trend="$comparison['joki']['direction']" :trendLabel="$comparison['joki']['label']" icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        <x-stat-card label="Akun Terjual" :value="$akunTerjual" sub="Bulan ini" tone="success" :trend="$comparison['akun']['direction']" :trendLabel="$comparison['akun']['label']" icon="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        <x-stat-card label="Fruit Terjual" :value="$fruitTerjual" sub="Transaksi fruit" tone="warning" icon="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        <x-stat-card label="Total Transaksi" :value="$totalTransaksi" sub="Semua kategori" tone="info" :trend="$comparison['transaksi']['direction']" :trendLabel="$comparison['transaksi']['label']" icon="M19 11H5m14-6H5m14 12H5" />
    </div>

    {{-- Revenue per Kategori --}}
    @if($revenuePerKategori->count() > 0)
    <div class="card overflow-hidden">
        <div class="px-5 py-3.5 border-b border-[var(--border)]">
            <h3 class="text-sm font-semibold text-[var(--text)] section-bar">Pendapatan per Kategori</h3>
        </div>
        <div class="p-5 space-y-3">
            @php
                $maxRev = $revenuePerKategori->max('pendapatan') ?: 1;
                $katProfitColor = ['fruit' => 'progress-bar', 'skin' => 'progress-bar', 'gamepass' => 'progress-bar-info', 'permanent' => 'progress-bar-warning', 'joki' => 'progress-bar-success', 'akun' => 'progress-bar', 'lainnya' => 'progress-bar'];
                $katLabelMap = ['fruit' => 'Fruit', 'skin' => 'Skin', 'gamepass' => 'Gamepass', 'permanent' => 'Permanent', 'joki' => 'Joki', 'akun' => 'Akun Jual', 'lainnya' => 'Lainnya'];
            @endphp
            @foreach($revenuePerKategori as $rk)
            @php $pct = round(($rk->pendapatan / $maxRev) * 100); @endphp
            <div>
                <div class="flex items-center justify-between mb-1.5 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="font-semibold text-[var(--text)]">{{ $katLabelMap[$rk->kategori] ?? ucfirst($rk->kategori) }}</span>
                        <span class="chip num">{{ $rk->jumlah }}x</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="num text-[var(--text-muted)]">{{ format_rupiah($rk->pendapatan) }}</span>
                        <span class="num font-semibold {{ $rk->keuntungan >= 0 ? 'text-[var(--success)]' : 'text-[var(--danger)]' }}">{{ ($rk->keuntungan >= 0 ? '+' : '') . format_angka($rk->keuntungan) }}</span>
                    </div>
                </div>
                <div class="progress">
                    <div class="progress-bar {{ $katProfitColor[$rk->kategori] ?? 'progress-bar' }}" style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Joki Breakdown + Top Customer --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="card overflow-hidden">
            <div class="px-5 py-3.5 border-b border-[var(--border)]">
                <h3 class="text-sm font-semibold text-[var(--text)]">Joki per Kategori</h3>
            </div>
            <div class="p-5">
                @if($jokiByKategori->count() > 0)
                @php $maxKat = $jokiByKategori->first(); @endphp
                <div class="space-y-3">
                    @foreach($jokiByKategori as $kat => $count)
                    @php $pct = round(($count / max(1, $maxKat)) * 100); @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1 text-xs">
                            <span class="font-medium text-[var(--text-muted)]">{{ $kategoriLabels[$kat] ?? ucfirst($kat) }}</span>
                            <span class="font-bold text-[var(--accent)] num">{{ $count }}</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <x-empty-state compact icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" message="Belum ada data joki bulan ini" />
                @endif
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="px-5 py-3.5 border-b border-[var(--border)]">
                <h3 class="text-sm font-semibold text-[var(--text)]">Top Customer</h3>
            </div>
            <div class="p-5">
                @if($jokiByCustomer->count() > 0)
                <div class="space-y-2.5">
                    @foreach($jokiByCustomer as $name => $count)
                    @php $rank = $loop->index + 1; @endphp
                    @php
                        $rankClass = match($rank) {
                            1 => 'bg-[var(--warning-soft)] text-[var(--warning)]',
                            2 => 'bg-[var(--surface-2)] text-[var(--text-muted)]',
                            3 => 'bg-[var(--accent-soft)] text-[var(--accent)]',
                            default => 'bg-[var(--surface-2)] text-[var(--text-subtle)]',
                        };
                    @endphp
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-bold num {{ $rankClass }}">
                            {{ $rank }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-[var(--text)] truncate">{{ $name ?: '-' }}</p>
                            <p class="text-[11px] text-[var(--text-subtle)] num">{{ $count }} order</p>
                        </div>
                        <div class="flex gap-0.5 shrink-0">
                            @for($i = 0; $i < min(5, $count); $i++)
                            <span class="dot dot-accent"></span>
                            @endfor
                            @if($count > 5)
                            <span class="text-[10px] text-[var(--text-subtle)] ml-1 num">+{{ $count - 5 }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <x-empty-state compact icon="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1z" message="Belum ada data customer" />
                @endif
            </div>
        </div>
    </div>

    {{-- Achievement --}}
    @if($jokiSelesai->count() > 0 || $totalTransaksi > 0)
    @php
        $achievements = [];
        if ($jokiSelesai->count() >= 100) $achievements[] = ['label' => '100+ Joki Selesai', 'tone' => 'warning'];
        elseif ($jokiSelesai->count() >= 50) $achievements[] = ['label' => '50+ Joki Selesai', 'tone' => 'accent'];
        elseif ($jokiSelesai->count() >= 20) $achievements[] = ['label' => '20+ Joki Selesai', 'tone' => 'info'];

        if ($akunTerjual >= 10) $achievements[] = ['label' => '10+ Akun Terjual', 'tone' => 'success'];
        if ($totalTransaksi >= 100) $achievements[] = ['label' => '100+ Transaksi', 'tone' => 'danger'];
        elseif ($totalTransaksi >= 50) $achievements[] = ['label' => '50+ Transaksi', 'tone' => 'info'];

        if ($jokiByCustomer->count() > 0 && $jokiByCustomer->first() >= 5) {
            $achievements[] = ['label' => 'Loyal Customer (' . $jokiByCustomer->first() . 'x)', 'tone' => 'accent'];
        }
        if ($margin >= 30) $achievements[] = ['label' => 'High Margin ' . $margin . '%', 'tone' => 'success'];
    @endphp

    @if(count($achievements) > 0)
    <div class="card p-5">
        <h3 class="text-sm font-semibold text-[var(--text)] mb-4 section-bar">Pencapaian Bulan Ini</h3>
        <div class="flex flex-wrap gap-2">
            @foreach($achievements as $a)
            @php
                $tone = match($a['tone']) {
                    'success' => 'bg-[var(--success-soft)] text-[var(--success)]',
                    'warning' => 'bg-[var(--warning-soft)] text-[var(--warning)]',
                    'danger'  => 'bg-[var(--danger-soft)] text-[var(--danger)]',
                    'info'    => 'bg-[var(--info-soft)] text-[var(--info)]',
                    default   => 'bg-[var(--accent-soft)] text-[var(--accent)]',
                };
            @endphp
            <span class="rounded-full px-3 py-1.5 text-xs font-semibold {{ $tone }}">{{ $a['label'] }}</span>
            @endforeach
        </div>
    </div>
    @endif
    @endif
</div>
@endsection
