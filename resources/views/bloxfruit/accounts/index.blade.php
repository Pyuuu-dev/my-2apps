@extends('layouts.app')
@section('title', 'Stok Akun')

@section('content')
{{-- Stats --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
    <div class="stat-card" style="--accent: linear-gradient(90deg, #6366f1, #8b5cf6)">
        <p class="text-[11px] text-gray-500">Total Akun</p>
        <p class="text-xl font-extrabold text-indigo-600">{{ $stats['total'] }}</p>
        <p class="text-[10px] text-gray-400">{{ $stats['tersedia'] }} tersedia &middot; {{ $stats['terjual'] }} terjual</p>
    </div>
    <div class="stat-card" style="--accent: linear-gradient(90deg, #10b981, #059669)">
        <p class="text-[11px] text-gray-500">Tersedia</p>
        <p class="text-xl font-extrabold text-emerald-600">{{ $stats['tersedia'] }}</p>
    </div>
    <div class="stat-card" style="--accent: linear-gradient(90deg, #f59e0b, #d97706)">
        <p class="text-[11px] text-gray-500">Total Modal</p>
        <p class="text-xl font-extrabold text-amber-600">Rp {{ number_format($stats['total_modal'], 0, ',', '.') }}</p>
    </div>
    <div class="stat-card" style="--accent: linear-gradient(90deg, #3b82f6, #2563eb)">
        <p class="text-[11px] text-gray-500">Total Penjualan</p>
        <p class="text-xl font-extrabold text-blue-600">Rp {{ number_format($stats['total_jual'], 0, ',', '.') }}</p>
    </div>
</div>

{{-- Filter --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
    <form method="GET" class="flex flex-wrap gap-2">
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari username, sword, fruit..." class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-52">
        <select name="status" class="rounded-lg border-gray-300 text-sm shadow-sm" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            @foreach(['tersedia','terjual','pending'] as $s)
            <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Filter</button>
    </form>
    <a href="{{ route('bloxfruit.accounts.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 shrink-0">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Akun
    </a>
</div>

{{-- Table --}}
<div class="table-container overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase text-gray-500">Username</th>
                <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase text-gray-500">Password</th>
                <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase text-gray-500">Sword / Gun</th>
                <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase text-gray-500">Fruit</th>
                <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase text-gray-500">Belly</th>
                <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase text-gray-500">Fragment</th>
                <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase text-gray-500">Race</th>
                <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase text-gray-500">Level</th>
                <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase text-gray-500">Harga Beli</th>
                <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase text-gray-500">Harga Jual</th>
                <th class="px-3 py-2.5 text-center text-[11px] font-semibold uppercase text-gray-500">Status</th>
                <th class="px-3 py-2.5 text-center text-[11px] font-semibold uppercase text-gray-500">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($accounts as $akun)
            <tr class="hover:bg-gray-50/50 {{ $akun->status === 'terjual' ? 'opacity-60' : '' }}">
                <td class="px-3 py-2.5 text-sm font-medium text-gray-900 whitespace-nowrap">{{ $akun->username_roblox }}</td>
                <td class="px-3 py-2.5 text-sm text-gray-600 whitespace-nowrap" x-data="{ show: false }">
                    <span x-show="!show" class="text-gray-400 cursor-pointer" @click="show = true">*****</span>
                    <span x-show="show" x-cloak @click="show = false" class="cursor-pointer">{{ $akun->password_roblox }}</span>
                </td>
                <td class="px-3 py-2.5 text-xs text-gray-600 max-w-[180px]">{{ $akun->sword_gun ?: '-' }}</td>
                <td class="px-3 py-2.5 text-xs text-gray-600">{{ $akun->fruit ?: '-' }}</td>
                <td class="px-3 py-2.5 text-xs text-right text-gray-600">{{ $akun->belly ?: '-' }}</td>
                <td class="px-3 py-2.5 text-xs text-right text-gray-600">{{ $akun->fragment ?: '-' }}</td>
                <td class="px-3 py-2.5 text-xs text-gray-600">{{ $akun->race ?: '-' }}</td>
                <td class="px-3 py-2.5 text-sm text-right font-medium text-gray-700">{{ $akun->level ?: '-' }}</td>
                <td class="px-3 py-2.5 text-sm text-right text-gray-600 whitespace-nowrap">{{ $akun->harga_beli ? 'Rp ' . number_format($akun->harga_beli, 0, ',', '.') : '-' }}</td>
                <td class="px-3 py-2.5 text-sm text-right font-semibold whitespace-nowrap {{ $akun->harga_jual ? 'text-emerald-600' : 'text-gray-400' }}">{{ $akun->harga_jual ? 'Rp ' . number_format($akun->harga_jual, 0, ',', '.') : '-' }}</td>
                <td class="px-3 py-2.5 text-center">
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold
                        {{ $akun->status === 'tersedia' ? 'bg-emerald-100 text-emerald-700' : ($akun->status === 'terjual' ? 'bg-gray-100 text-gray-600' : 'bg-amber-100 text-amber-700') }}">
                        {{ ucfirst($akun->status) }}
                    </span>
                </td>
                <td class="px-3 py-2.5 text-center whitespace-nowrap">
                    <div class="flex items-center justify-center gap-1.5">
                        <a href="{{ route('bloxfruit.accounts.edit', $akun) }}" class="text-[11px] text-indigo-600 hover:text-indigo-800">Edit</a>
                        <form method="POST" action="{{ route('bloxfruit.accounts.destroy', $akun) }}" onsubmit="return confirm('Yakin hapus?')">
                            @csrf @method('DELETE')
                            <button class="text-[11px] text-red-500 hover:text-red-700">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="12" class="px-4 py-8 text-center text-sm text-gray-300">Belum ada stok akun</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $accounts->links() }}</div>
@endsection
