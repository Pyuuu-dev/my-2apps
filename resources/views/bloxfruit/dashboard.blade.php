@extends('layouts.app')
@section('title', 'Blox Fruit - Dashboard')

@section('content')
{{-- Stats Grid --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    @php
    $cards = [
        ['label' => 'Master Buah', 'value' => $stats['total_buah'], 'sub' => 'Stok: ' . number_format($stats['total_stok_buah']), 'accent' => 'linear-gradient(90deg, #6366f1, #8b5cf6)', 'color' => 'text-indigo-600'],
        ['label' => 'Master Skin', 'value' => $stats['total_skin_master'], 'sub' => 'Stok: ' . number_format($stats['total_skin']), 'accent' => 'linear-gradient(90deg, #ec4899, #f43f5e)', 'color' => 'text-pink-600'],
        ['label' => 'Master Gamepass', 'value' => $stats['total_gamepass'], 'sub' => 'Stok: ' . number_format($stats['total_stok_gamepass']), 'accent' => 'linear-gradient(90deg, #3b82f6, #6366f1)', 'color' => 'text-blue-600'],
        ['label' => 'Master Permanent', 'value' => $stats['total_permanent_master'], 'sub' => 'Stok: ' . number_format($stats['total_permanent']), 'accent' => 'linear-gradient(90deg, #f59e0b, #f97316)', 'color' => 'text-amber-600'],
        ['label' => 'Akun Storage', 'value' => $stats['total_akun_storage'], 'sub' => 'Aktif', 'accent' => 'linear-gradient(90deg, #10b981, #059669)', 'color' => 'text-emerald-600'],
        ['label' => 'Akun Jual', 'value' => $stats['akun_tersedia'], 'sub' => 'Tersedia', 'accent' => 'linear-gradient(90deg, #14b8a6, #10b981)', 'color' => 'text-teal-600'],
        ['label' => 'Akun Terjual', 'value' => $stats['akun_terjual'], 'sub' => 'Total', 'accent' => 'linear-gradient(90deg, #64748b, #475569)', 'color' => 'text-gray-600'],
        ['label' => 'Total Joki', 'value' => $stats['joki_antrian'] + $stats['joki_proses'] + $stats['joki_selesai'], 'sub' => $stats['joki_proses'] . ' proses', 'accent' => 'linear-gradient(90deg, #ef4444, #dc2626)', 'color' => 'text-red-600'],
    ];
    @endphp
    @foreach($cards as $card)
    <div class="stat-card" style="--accent: {{ $card['accent'] }}">
        <p class="text-xs font-medium text-gray-500 mb-1">{{ $card['label'] }}</p>
        <p class="text-2xl font-extrabold {{ $card['color'] }}">{{ number_format($card['value']) }}</p>
        <p class="text-[11px] text-gray-400">{{ $card['sub'] }}</p>
    </div>
    @endforeach
</div>

{{-- Joki Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="stat-card" style="--accent: linear-gradient(90deg, #eab308, #f59e0b)">
        <p class="text-xs font-medium text-gray-500 mb-1">Joki Antrian</p>
        <p class="text-2xl font-extrabold text-yellow-600">{{ $stats['joki_antrian'] }}</p>
    </div>
    <div class="stat-card" style="--accent: linear-gradient(90deg, #f97316, #ef4444)">
        <p class="text-xs font-medium text-gray-500 mb-1">Joki Proses</p>
        <p class="text-2xl font-extrabold text-orange-600">{{ $stats['joki_proses'] }}</p>
    </div>
    <div class="stat-card" style="--accent: linear-gradient(90deg, #10b981, #059669)">
        <p class="text-xs font-medium text-gray-500 mb-1">Joki Selesai</p>
        <p class="text-2xl font-extrabold text-green-600">{{ $stats['joki_selesai'] }}</p>
    </div>
</div>

{{-- Recent --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="px-5 py-4 flex items-center justify-between border-b border-gray-100/50">
            <h3 class="font-semibold text-gray-900">Joki Terbaru</h3>
            <a href="{{ route('bloxfruit.joki.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">Lihat Semua &rarr;</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($jokiTerbaru as $joki)
            <div class="px-5 py-3 flex items-center justify-between">
                <div>
                    <p class="font-medium text-gray-900 text-sm">{{ $joki->nama_pelanggan }}</p>
                    <p class="text-xs text-gray-400">{{ ucfirst($joki->jenis_joki) }}</p>
                </div>
                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $joki->status === 'selesai' ? 'badge-uncommon' : ($joki->status === 'proses' ? 'badge-rare' : ($joki->status === 'batal' ? 'badge-mythical' : 'badge-legendary')) }}">{{ ucfirst($joki->status) }}</span>
            </div>
            @empty
            <p class="px-5 py-8 text-sm text-gray-400 text-center">Belum ada order joki</p>
            @endforelse
        </div>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="px-5 py-4 flex items-center justify-between border-b border-gray-100/50">
            <h3 class="font-semibold text-gray-900">Akun Jual Terbaru</h3>
            <a href="{{ route('bloxfruit.accounts.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">Lihat Semua &rarr;</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($akunTerbaru as $akun)
            <div class="px-5 py-3 flex items-center justify-between">
                <div>
                    <p class="font-medium text-gray-900 text-sm">{{ $akun->judul }}</p>
                    <p class="text-xs text-gray-400">Level {{ $akun->level ?? '-' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-gray-900">Rp {{ number_format($akun->harga, 0, ',', '.') }}</p>
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $akun->status === 'tersedia' ? 'badge-uncommon' : ($akun->status === 'terjual' ? 'badge-common' : 'badge-legendary') }}">{{ ucfirst($akun->status) }}</span>
                </div>
            </div>
            @empty
            <p class="px-5 py-8 text-sm text-gray-400 text-center">Belum ada stok akun</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
