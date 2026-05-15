@extends('layouts.app')
@section('title', 'Blox Fruit')

@section('content')
<div class="space-y-6">

    {{-- ============ Header ============ --}}
    <x-page-header eyebrow="Modul" title="Blox Fruit" subtitle="Manajemen stok dan penjualan akun game">
        <x-slot:actions>
            <x-btn :href="route('bloxfruit.search')" variant="primary" icon="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z">
                Cari Stok
            </x-btn>
        </x-slot:actions>
    </x-page-header>

    {{-- ============ Master Counts ============ --}}
    <div>
        <p class="text-[11px] font-semibold uppercase tracking-wider text-[var(--text-subtle)] mb-2 px-1 section-bar">Master Data</p>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="reveal reveal-1">
            <x-stat-card
                label="Master Buah"
                :value="format_angka($stats['total_buah'])"
                :sub="'Stok: ' . format_angka($stats['total_stok_buah'])"
                tone="accent"
                icon="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </div>
            <div class="reveal reveal-2">
            <x-stat-card
                label="Master Skin"
                :value="format_angka($stats['total_skin_master'])"
                :sub="'Stok: ' . format_angka($stats['total_skin'])"
                icon="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
            </div>
            <div class="reveal reveal-3">
            <x-stat-card
                label="Master Gamepass"
                :value="format_angka($stats['total_gamepass'])"
                :sub="'Stok: ' . format_angka($stats['total_stok_gamepass'])"
                icon="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
            </div>
            <div class="reveal reveal-4">
            <x-stat-card
                label="Master Permanent"
                :value="format_angka($stats['total_permanent_master'])"
                :sub="'Stok: ' . format_angka($stats['total_permanent'])"
                icon="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
            </div>
        </div>
    </div>

    {{-- ============ Keuangan ============ --}}
    <div>
        <p class="text-[11px] font-semibold uppercase tracking-wider text-[var(--text-subtle)] mb-2 px-1 section-bar">Keuangan</p>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <x-stat-card
                label="Pendapatan"
                :value="format_rupiah($keuanganBulanIni['pendapatan'])"
                :sub="$keuanganBulanIni['transaksi'] . ' transaksi · ' . format_bulan(now())"
                tone="info"
                icon="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />

            <x-stat-card
                label="Keuntungan"
                :value="format_rupiah($keuanganBulanIni['keuntungan'])"
                sub="Bulan ini"
                :tone="$keuanganBulanIni['keuntungan'] >= 0 ? 'success' : 'danger'"
                :trend="$keuanganBulanIni['keuntungan'] >= 0 ? 'up' : 'down'"
                icon="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />

            <x-stat-card
                label="Nilai Stok"
                :value="format_rupiah($nilaiStokTotal)"
                sub="Fruit + Skin"
                tone="warning"
                icon="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />

            <x-stat-card
                label="Saldo Wallet"
                :value="format_rupiah($saldoWallet)"
                sub="E-wallet aggregate"
                tone="accent"
                icon="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
        </div>
    </div>

    {{-- ============ Akun + Joki Status ============ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="card p-5 hover-lift">
            <h3 class="text-sm font-semibold text-[var(--text)] mb-4 flex items-center gap-2">
                <span class="dot dot-accent"></span> Akun Jual
            </h3>
            <div class="grid grid-cols-3 gap-3">
                <div class="text-center py-3 rounded-lg bg-[var(--surface-2)] border border-[var(--border)]">
                    <p class="text-2xl font-bold num">{{ format_angka($stats['total_akun_storage']) }}</p>
                    <p class="text-[10px] uppercase tracking-wider text-[var(--text-subtle)] mt-0.5">Storage</p>
                </div>
                <div class="text-center py-3 rounded-lg bg-[var(--success-soft)]">
                    <p class="text-2xl font-bold num text-[var(--success)]">{{ format_angka($stats['akun_tersedia']) }}</p>
                    <p class="text-[10px] uppercase tracking-wider text-[var(--text-subtle)] mt-0.5">Tersedia</p>
                </div>
                <div class="text-center py-3 rounded-lg bg-[var(--surface-2)] border border-[var(--border)]">
                    <p class="text-2xl font-bold num text-[var(--text-subtle)]">{{ format_angka($stats['akun_terjual']) }}</p>
                    <p class="text-[10px] uppercase tracking-wider text-[var(--text-subtle)] mt-0.5">Terjual</p>
                </div>
            </div>
        </div>

        <div class="card p-5 hover-lift">
            <h3 class="text-sm font-semibold text-[var(--text)] mb-4 flex items-center gap-2">
                <span class="dot dot-info dot-pulse"></span> Status Joki
            </h3>
            <div class="grid grid-cols-3 gap-3">
                <div class="text-center py-3 rounded-lg bg-[var(--warning-soft)]">
                    <p class="text-2xl font-bold num text-[var(--warning)]">{{ $stats['joki_antrian'] }}</p>
                    <p class="text-[10px] uppercase tracking-wider text-[var(--text-subtle)] mt-0.5">Antrian</p>
                </div>
                <div class="text-center py-3 rounded-lg bg-[var(--info-soft)]">
                    <p class="text-2xl font-bold num text-[var(--info)]">{{ $stats['joki_proses'] }}</p>
                    <p class="text-[10px] uppercase tracking-wider text-[var(--text-subtle)] mt-0.5">Proses</p>
                </div>
                <div class="text-center py-3 rounded-lg bg-[var(--success-soft)]">
                    <p class="text-2xl font-bold num text-[var(--success)]">{{ $stats['joki_selesai'] }}</p>
                    <p class="text-[10px] uppercase tracking-wider text-[var(--text-subtle)] mt-0.5">Selesai</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ Recent Activity ============ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="card overflow-hidden">
            <div class="px-5 py-3.5 border-b border-[var(--border)] flex items-center justify-between">
                <h3 class="text-sm font-semibold text-[var(--text)] flex items-center gap-2">
                    <span class="dot dot-accent"></span> Joki Terbaru
                </h3>
                <a href="{{ route('bloxfruit.joki.index') }}" class="text-xs link-soft">Lihat semua</a>
            </div>
            <div class="divide-y divide-[var(--border)] max-h-96 overflow-y-auto">
                @forelse($jokiTerbaru as $joki)
                <div class="px-5 py-3 flex items-center justify-between gap-3 hover:bg-[var(--surface-2)] transition-colors">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-[var(--text)] truncate">{{ $joki->nama_pelanggan }}</p>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="text-[11px] text-[var(--text-subtle)]">{{ ucfirst($joki->jenis_joki) }}</span>
                            <span class="text-[var(--text-subtle)]">·</span>
                            <span class="text-[11px] text-[var(--text-subtle)]">{{ format_relatif($joki->created_at) }}</span>
                        </div>
                    </div>
                    @php
                        $tone = match($joki->status) {
                            'selesai' => 'bg-[var(--success-soft)] text-[var(--success)]',
                            'proses'  => 'bg-[var(--info-soft)] text-[var(--info)]',
                            'batal'   => 'bg-[var(--danger-soft)] text-[var(--danger)]',
                            default   => 'bg-[var(--warning-soft)] text-[var(--warning)]',
                        };
                    @endphp
                    <span class="rounded-full px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider whitespace-nowrap {{ $tone }}">{{ $joki->status }}</span>
                </div>
                @empty
                <x-empty-state icon="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" message="Belum ada order joki" />
                @endforelse
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="px-5 py-3.5 border-b border-[var(--border)] flex items-center justify-between">
                <h3 class="text-sm font-semibold text-[var(--text)] flex items-center gap-2">
                    <span class="dot dot-success"></span> Akun Jual Terbaru
                </h3>
                <a href="{{ route('bloxfruit.accounts.index') }}" class="text-xs link-soft">Lihat semua</a>
            </div>
            <div class="divide-y divide-[var(--border)] max-h-96 overflow-y-auto">
                @forelse($akunTerbaru as $akun)
                <div class="px-5 py-3 flex items-center justify-between gap-3 hover:bg-[var(--surface-2)] transition-colors">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-[var(--text)] truncate">{{ $akun->judul }}</p>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="text-[11px] text-[var(--text-subtle)]">Lv {{ $akun->level ?? '-' }}</span>
                            <span class="text-[var(--text-subtle)]">·</span>
                            <span class="text-[11px] text-[var(--text-subtle)]">{{ format_relatif($akun->created_at) }}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold num text-[var(--text)] whitespace-nowrap">{{ format_rupiah($akun->harga) }}</p>
                        @php
                            $aTone = match($akun->status) {
                                'tersedia' => 'bg-[var(--success-soft)] text-[var(--success)]',
                                'terjual'  => 'bg-[var(--surface-2)] text-[var(--text-subtle)]',
                                default    => 'bg-[var(--warning-soft)] text-[var(--warning)]',
                            };
                        @endphp
                        <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider mt-1 inline-block {{ $aTone }}">{{ $akun->status }}</span>
                    </div>
                </div>
                @empty
                <x-empty-state icon="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" message="Belum ada stok akun" />
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
