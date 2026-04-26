@extends('layouts.app')
@section('title', 'List Joki')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-2">
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari pelanggan..." class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <select name="status" class="rounded-lg border-gray-300 text-sm shadow-sm">
            <option value="">Semua Status</option>
            @foreach(['antrian','proses','selesai','batal'] as $s)
            <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Filter</button>
    </form>
    <a href="{{ route('bloxfruit.joki.create') }}" class="btn-primary inline-flex items-center gap-2 text-sm">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Joki
    </a>
</div>

{{-- Price List --}}
<div x-data="{ showList: false }" class="mb-6">
    <button @click="showList = !showList" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 mb-2" x-text="showList ? 'Tutup Daftar Harga' : 'Lihat Daftar Harga Joki'"></button>
    <div x-show="showList" x-collapse x-cloak>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($kategoriLabels as $katKey => $kat)
            @if(isset($servicesByKategori[$katKey]))
            <div class="rounded-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
                <div class="px-3 py-2 bg-gray-50 dark:bg-slate-800">
                    <p class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ $kat['icon'] }} {{ $kat['label'] }}</p>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-slate-700">
                    @foreach($servicesByKategori[$katKey] as $svc)
                    <div class="px-3 py-1.5 flex items-center justify-between">
                        <span class="text-[11px] text-gray-600 dark:text-gray-400">{{ $svc->nama }}</span>
                        <span class="text-[11px] font-bold text-indigo-600">{{ $svc->harga > 0 ? number_format($svc->harga) : 'Custom' }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            @endforeach
        </div>
    </div>
</div>

{{-- Orders --}}
<div class="space-y-3">
    @forelse($orders as $order)
    @php
        $parts = explode(':', $order->jenis_joki, 2);
        $katKey = $parts[0] ?? '';
        $jenisNama = $parts[1] ?? $order->jenis_joki;
        $katInfo = $kategoriLabels[$katKey] ?? null;
    @endphp
    <div class="glass-card rounded-xl p-4" x-data="{ detail: false }">
        <div class="flex items-center justify-between cursor-pointer" @click="detail = !detail">
            <div class="flex items-center gap-3 flex-1 min-w-0">
                {{-- Status dot --}}
                <div class="h-3 w-3 rounded-full shrink-0 {{ $order->status === 'selesai' ? 'bg-green-500' : ($order->status === 'proses' ? 'bg-blue-500 animate-pulse' : ($order->status === 'batal' ? 'bg-red-500' : 'bg-yellow-500')) }}"></div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="text-sm font-bold text-gray-900">{{ $order->nama_pelanggan }}</p>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $order->status === 'selesai' ? 'bg-green-100 text-green-700' : ($order->status === 'proses' ? 'bg-blue-100 text-blue-700' : ($order->status === 'batal' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700')) }}">{{ ucfirst($order->status) }}</span>
                    </div>
                    <p class="text-[11px] text-gray-500">
                        @if($katInfo) {{ $katInfo['icon'] }} @endif
                        {{ $jenisNama }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <span class="text-sm font-bold text-gray-900">{{ number_format($order->harga) }}</span>
                <svg class="h-4 w-4 text-gray-400 transition-transform" :class="detail && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
        </div>

        {{-- Detail --}}
        <div x-show="detail" x-collapse x-cloak class="mt-3 pt-3 border-t border-gray-100 dark:border-slate-700">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm mb-3">
                <div>
                    <p class="text-[10px] text-gray-400">Kontak</p>
                    <p class="font-medium text-gray-700">{{ $order->kontak ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400">Username Roblox</p>
                    <p class="font-medium text-gray-700">{{ $order->username_roblox ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400">Password Roblox</p>
                    <div x-data="{ showPw: false }">
                        <p class="font-medium text-gray-700" x-show="!showPw">{{ $order->password_roblox ? '••••••••' : '-' }}</p>
                        <p class="font-medium text-gray-700" x-show="showPw" x-cloak>{{ $order->password_roblox ?? '-' }}</p>
                        @if($order->password_roblox)
                        <button @click="showPw = !showPw" class="text-[10px] text-indigo-600" x-text="showPw ? 'Sembunyikan' : 'Tampilkan'"></button>
                        @endif
                    </div>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400">Tanggal</p>
                    <p class="font-medium text-gray-700">{{ $order->tanggal_mulai?->format('d/m/Y') ?? '-' }} @if($order->tanggal_selesai) → {{ $order->tanggal_selesai->format('d/m/Y') }} @endif</p>
                </div>
            </div>
            @if($order->detail_pesanan)
            <div class="rounded-lg bg-gray-50 dark:bg-slate-800 p-2.5 mb-3">
                <p class="text-[10px] text-gray-400 mb-0.5">Detail:</p>
                <p class="text-sm text-gray-700">{{ $order->detail_pesanan }}</p>
            </div>
            @endif
            {{-- Toggle Status --}}
            <div class="mb-3">
                <p class="text-[10px] text-gray-400 mb-1.5">Ubah Status</p>
                <div class="flex flex-wrap gap-1.5">
                    @php
                        $statuses = [
                            'antrian' => ['label' => 'Antrian', 'bg' => 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400', 'active' => 'ring-2 ring-yellow-500'],
                            'proses' => ['label' => 'Proses', 'bg' => 'bg-blue-100 text-blue-700 hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-400', 'active' => 'ring-2 ring-blue-500'],
                            'selesai' => ['label' => 'Selesai', 'bg' => 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400', 'active' => 'ring-2 ring-emerald-500'],
                            'batal' => ['label' => 'Batal', 'bg' => 'bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-400', 'active' => 'ring-2 ring-red-500'],
                        ];
                    @endphp
                    @foreach($statuses as $sKey => $sVal)
                        @if($order->status === $sKey)
                            <span class="rounded-lg px-3 py-1.5 text-[11px] font-bold {{ $sVal['bg'] }} {{ $sVal['active'] }} cursor-default">{{ $sVal['label'] }}</span>
                        @else
                            <form method="POST" action="{{ route('bloxfruit.joki.status', $order) }}" onsubmit="return confirm('Ubah status ke {{ $sVal['label'] }}?')">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="{{ $sKey }}">
                                <button type="submit" class="rounded-lg px-3 py-1.5 text-[11px] font-medium {{ $sVal['bg'] }} opacity-50 hover:opacity-100 transition-opacity">{{ $sVal['label'] }}</button>
                            </form>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('bloxfruit.joki.edit', $order) }}" class="rounded-lg bg-indigo-50 dark:bg-indigo-950/30 px-3 py-1.5 text-xs font-medium text-indigo-700 hover:bg-indigo-100">Edit</a>
                <form method="POST" action="{{ route('bloxfruit.joki.destroy', $order) }}" onsubmit="return confirm('Hapus order {{ $order->nama_pelanggan }}?')">
                    @csrf @method('DELETE')
                    <button class="rounded-lg bg-red-50 dark:bg-red-950/30 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-100">Hapus</button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="py-12 text-center text-sm text-gray-400">Belum ada order joki</div>
    @endforelse
</div>
<div class="mt-4">{{ $orders->links() }}</div>
@endsection
