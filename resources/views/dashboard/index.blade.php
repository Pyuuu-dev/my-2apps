@extends('layouts.app')
@section('title', 'Beranda')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- ============ Hero Header ============ --}}
    <div class="card p-6 sm:p-7 accent-glow card-hairline reveal">
        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[var(--accent)] mb-2 section-bar">{{ now()->translatedFormat('l') }}</p>
        <h1 class="text-2xl sm:text-[28px] font-bold tracking-tight text-[var(--text)]">
            Selamat {{ salam_waktu() }}, {{ auth()->user()->name }}
        </h1>
        <p class="text-sm text-[var(--text-muted)] mt-1.5">
            {{ format_tanggal(now(), 'd F Y') }}
            <span class="text-[var(--text-subtle)] mx-1.5">·</span>
            <span class="num">{{ now()->format('H:i') }}</span> {{ setting('app.timezone_label', 'SGT') }}
        </p>
    </div>

    {{-- ============ Keuangan Bulan Ini ============ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="reveal reveal-1">
        <x-stat-card
            label="Pendapatan"
            :value="format_rupiah($keuangan['pendapatan'])"
            :sub="$keuangan['transaksi'] . ' transaksi · ' . format_bulan(now())"
            tone="info"
            icon="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
        </div>
        <div class="reveal reveal-2">
        <x-stat-card
            label="Keuntungan"
            :value="format_rupiah($keuangan['keuntungan'])"
            sub="Bulan ini"
            :tone="$keuangan['keuntungan'] >= 0 ? 'success' : 'danger'"
            :trend="$keuangan['keuntungan'] >= 0 ? 'up' : 'down'"
            icon="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
        </div>
        <div class="reveal reveal-3">
        <x-stat-card
            label="Saldo Wallet"
            :value="format_rupiah($keuangan['saldo_wallet'])"
            sub="E-wallet aggregate"
            tone="accent"
            icon="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
        </div>
        <div class="reveal reveal-4">
        <x-stat-card
            label="Joki Aktif"
            :value="$bfStats['joki_aktif']"
            :sub="$bfStats['joki_proses'] . ' proses · ' . $bfStats['joki_antrian'] . ' antrian'"
            tone="warning"
            icon="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </div>
    </div>

    {{-- ============ Module Cards ============ --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Blox Fruit --}}
        <a href="{{ route('bloxfruit.dashboard') }}" class="card p-5 hover-lift accent-glow card-hairline group block">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-2.5">
                    <span class="icon-ring icon-ring-accent">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </span>
                    <div>
                        <h3 class="text-base font-semibold text-[var(--text)]">Blox Fruit</h3>
                        <p class="text-xs text-[var(--text-muted)]">Manajemen stok &amp; penjualan</p>
                    </div>
                </div>
                <svg class="h-4 w-4 text-[var(--text-subtle)] group-hover:text-[var(--accent)] group-hover:translate-x-1 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </div>
            <div class="grid grid-cols-4 gap-3 pt-3 border-t border-[var(--border)]">
                <div>
                    <p class="text-lg font-bold num">{{ $bfStats['total_buah'] }}</p>
                    <p class="text-[10px] uppercase tracking-wider text-[var(--text-subtle)] mt-0.5">Buah</p>
                </div>
                <div>
                    <p class="text-lg font-bold num">{{ $bfStats['total_skin'] }}</p>
                    <p class="text-[10px] uppercase tracking-wider text-[var(--text-subtle)] mt-0.5">Skin</p>
                </div>
                <div>
                    <p class="text-lg font-bold num">{{ $bfStats['total_akun_storage'] }}</p>
                    <p class="text-[10px] uppercase tracking-wider text-[var(--text-subtle)] mt-0.5">Storage</p>
                </div>
                <div>
                    <p class="text-lg font-bold num">{{ $bfStats['total_joki'] }}</p>
                    <p class="text-[10px] uppercase tracking-wider text-[var(--text-subtle)] mt-0.5">Joki</p>
                </div>
            </div>
        </a>

        {{-- Diet Tracker --}}
        <a href="{{ route('diet.dashboard') }}" class="card p-5 hover-lift accent-glow accent-glow-success card-hairline group block">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-2.5">
                    <span class="icon-ring icon-ring-success">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </span>
                    <div>
                        <h3 class="text-base font-semibold text-[var(--text)]">Diet Tracker</h3>
                        <p class="text-xs text-[var(--text-muted)]">Monitoring diet &amp; kesehatan</p>
                    </div>
                </div>
                <svg class="h-4 w-4 text-[var(--text-subtle)] group-hover:text-[var(--success)] group-hover:translate-x-1 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </div>
            @if($dtStats)
            <div class="grid grid-cols-4 gap-3 pt-3 border-t border-[var(--border)]">
                <div>
                    <p class="text-lg font-bold num">{{ format_angka($dtStats['kalori_masuk']) }}</p>
                    <p class="text-[10px] uppercase tracking-wider text-[var(--text-subtle)] mt-0.5">Kalori</p>
                </div>
                <div>
                    <p class="text-lg font-bold num">{{ format_angka($dtStats['total_minum']) }}</p>
                    <p class="text-[10px] uppercase tracking-wider text-[var(--text-subtle)] mt-0.5">ml Air</p>
                </div>
                <div>
                    <p class="text-lg font-bold num">{{ $dtStats['berat_sekarang'] ?? '-' }}</p>
                    <p class="text-[10px] uppercase tracking-wider text-[var(--text-subtle)] mt-0.5">Kg</p>
                </div>
                <div>
                    <p class="text-lg font-bold num">{{ $dtStats['bmi'] ?? '-' }}</p>
                    <p class="text-[10px] uppercase tracking-wider text-[var(--text-subtle)] mt-0.5">BMI</p>
                </div>
            </div>
            @else
            <div class="pt-3 border-t border-[var(--border)]">
                <p class="text-sm text-[var(--text-muted)]">Belum ada program diet. Klik untuk mulai.</p>
            </div>
            @endif
        </a>
    </div>

    {{-- ============ Joki Aktif + Transaksi Terakhir ============ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Joki Aktif --}}
        <div class="card overflow-hidden">
            <div class="px-5 py-3.5 border-b border-[var(--border)] flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="dot dot-warning dot-pulse"></span>
                    <h3 class="text-sm font-semibold text-[var(--text)]">Joki Aktif</h3>
                </div>
                <a href="{{ route('bloxfruit.joki.index') }}" class="text-xs link-soft">Lihat semua</a>
            </div>
            <div class="divide-y divide-[var(--border)]">
                @forelse($jokiAktif as $joki)
                @php
                    $parts = explode(':', $joki->jenis_joki, 2);
                    $jenisNama = $parts[1] ?? $joki->jenis_joki;
                @endphp
                <div class="px-5 py-3 flex items-center justify-between hover:bg-[var(--surface-2)] transition-colors">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="dot {{ $joki->status === 'proses' ? 'dot-info dot-pulse' : 'dot-warning' }}"></span>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-[var(--text)] truncate">{{ $joki->nama_pelanggan }}</p>
                            <p class="text-[11px] text-[var(--text-subtle)] truncate">{{ $jenisNama }}</p>
                        </div>
                    </div>
                    <div class="text-right shrink-0 ml-3">
                        <p class="text-sm font-semibold num text-[var(--text)]">{{ format_angka($joki->harga) }}</p>
                        <p class="text-[10px] uppercase tracking-wider {{ $joki->status === 'proses' ? 'text-[var(--info)]' : 'text-[var(--warning)]' }}">{{ $joki->status }}</p>
                    </div>
                </div>
                @empty
                <x-empty-state compact icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" message="Tidak ada joki aktif" />
                @endforelse
            </div>
        </div>

        {{-- Transaksi Terakhir --}}
        <div class="card overflow-hidden">
            <div class="px-5 py-3.5 border-b border-[var(--border)] flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="dot dot-accent"></span>
                    <h3 class="text-sm font-semibold text-[var(--text)]">Transaksi Terakhir</h3>
                </div>
                <a href="{{ route('bloxfruit.profit.index') }}" class="text-xs link-soft">Lihat semua</a>
            </div>
            <div class="divide-y divide-[var(--border)]">
                @forelse($transaksiTerakhir as $trx)
                <div class="px-5 py-3 flex items-center justify-between hover:bg-[var(--surface-2)] transition-colors">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="chip">{{ ucfirst($trx->kategori) }}</span>
                        <div class="min-w-0">
                            <p class="text-sm text-[var(--text)] truncate">{{ $trx->keterangan ?? '-' }}</p>
                            <p class="text-[11px] text-[var(--text-subtle)]">{{ $trx->tanggal->format('d M') }}</p>
                        </div>
                    </div>
                    <span class="text-sm font-semibold num shrink-0 ml-3 {{ $trx->keuntungan >= 0 ? 'text-[var(--success)]' : 'text-[var(--danger)]' }}">{{ ($trx->keuntungan >= 0 ? '+' : '') . format_angka($trx->keuntungan) }}</span>
                </div>
                @empty
                <x-empty-state compact icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" message="Belum ada transaksi" />
                @endforelse
            </div>
        </div>
    </div>

    {{-- ============ Akun Jual + Diet Hari Ini ============ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="card p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-[var(--text)] flex items-center gap-2">
                    <span class="dot dot-accent"></span> Akun Jual
                </h3>
                <a href="{{ route('bloxfruit.accounts.index') }}" class="text-xs link-soft">Lihat semua</a>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div class="text-center py-3 rounded-lg bg-[var(--surface-2)] border border-[var(--border)]">
                    <p class="text-2xl font-bold num text-[var(--text)]">{{ $akunJual['total'] }}</p>
                    <p class="text-[10px] uppercase tracking-wider text-[var(--text-subtle)] mt-0.5">Total</p>
                </div>
                <div class="text-center py-3 rounded-lg bg-[var(--success-soft)]">
                    <p class="text-2xl font-bold num text-[var(--success)]">{{ $akunJual['tersedia'] }}</p>
                    <p class="text-[10px] uppercase tracking-wider text-[var(--text-subtle)] mt-0.5">Tersedia</p>
                </div>
                <div class="text-center py-3 rounded-lg bg-[var(--surface-2)] border border-[var(--border)]">
                    <p class="text-2xl font-bold num text-[var(--text-subtle)]">{{ $akunJual['terjual'] }}</p>
                    <p class="text-[10px] uppercase tracking-wider text-[var(--text-subtle)] mt-0.5">Terjual</p>
                </div>
            </div>
        </div>

        @if($dtStats)
        <div class="card p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-[var(--text)] flex items-center gap-2">
                    <span class="dot dot-success"></span> Diet Hari Ini
                </h3>
                <a href="{{ route('diet.dashboard') }}" class="text-xs link-soft">Detail</a>
            </div>
            <div class="grid grid-cols-2 gap-4">
                @php
                    $kPct = min(100, round(($dtStats['kalori_masuk'] / max(1, $dtStats['target_kalori'])) * 100));
                    $aPct = min(100, round(($dtStats['total_minum'] / max(1, $dtStats['target_air'])) * 100));
                @endphp
                <div>
                    <p class="text-[11px] uppercase tracking-wider text-[var(--text-subtle)] mb-1">Kalori</p>
                    <p class="text-base font-semibold num">{{ format_angka($dtStats['kalori_masuk']) }} <span class="text-xs font-normal text-[var(--text-subtle)]">/ {{ format_angka($dtStats['target_kalori']) }}</span></p>
                    <div class="progress mt-2">
                        <div class="progress-bar progress-bar-warning" style="width: {{ $kPct }}%"></div>
                    </div>
                </div>
                <div>
                    <p class="text-[11px] uppercase tracking-wider text-[var(--text-subtle)] mb-1">Air Minum</p>
                    <p class="text-base font-semibold num">{{ format_angka($dtStats['total_minum']) }}<span class="text-xs font-normal text-[var(--text-subtle)]">ml / {{ format_angka($dtStats['target_air']) }}</span></p>
                    <div class="progress mt-2">
                        <div class="progress-bar progress-bar-info" style="width: {{ $aPct }}%"></div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- ============ Aksi Cepat ============ --}}
    <div class="card p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-[var(--text)] section-bar">Aksi Cepat</h3>
        </div>
        <div class="grid grid-cols-3 sm:grid-cols-6 gap-2">
            @php
                $quickActions = [
                    ['url' => route('bloxfruit.joki.create'), 'label' => 'Joki Baru', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                    ['url' => route('bloxfruit.profit.create'), 'label' => 'Transaksi', 'icon' => 'M12 6v6m0 0v6m0-6h6m-6 0H6'],
                    ['url' => route('bloxfruit.search'), 'label' => 'Cari Stok', 'icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'],
                    ['url' => route('diet.dashboard'), 'label' => 'Diet', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2'],
                    ['url' => route('bloxfruit.accounts.index'), 'label' => 'Akun Jual', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                    ['url' => route('bloxfruit.rekap'), 'label' => 'Rekap', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                ];
            @endphp
            @foreach($quickActions as $a)
            <a href="{{ $a['url'] }}" class="tile">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $a['icon'] }}"/></svg>
                <span class="text-[11px] font-semibold">{{ $a['label'] }}</span>
            </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
