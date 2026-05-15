@extends('layouts.app')
@section('title', 'Stok Akun')

@section('content')
{{-- Stats --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
    <x-stat-card label="Total Akun" :value="$stats['total']" :sub="$stats['tersedia'] . ' tersedia · ' . $stats['terjual'] . ' terjual'" valueClass="text-indigo-600 dark:text-indigo-400" accent="linear-gradient(90deg, #6366f1, #8b5cf6)" />
    <x-stat-card label="Tersedia" :value="$stats['tersedia']" valueClass="text-emerald-600 dark:text-emerald-400" accent="linear-gradient(90deg, #10b981, #059669)" />
    <x-stat-card label="Total Modal" :value="format_rupiah($stats['total_modal'])" valueClass="text-amber-600 dark:text-amber-400" accent="linear-gradient(90deg, #f59e0b, #d97706)" />
    <x-stat-card label="Total Penjualan" :value="format_rupiah($stats['total_jual'])" valueClass="text-blue-600 dark:text-blue-400" accent="linear-gradient(90deg, #3b82f6, #2563eb)" />
</div>

{{-- Filter --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
    <form method="GET" class="flex flex-wrap gap-2">
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari username, sword, fruit..." class="rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-gray-100 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-52">
        <select name="status" class="rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-gray-100 text-sm shadow-sm" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            @foreach(['tersedia','terjual','pending'] as $s)
            <option value="{{ $s }}" @selected(request('status') == $s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <x-btn type="submit" variant="secondary">Filter</x-btn>
    </form>
    <x-btn :href="route('bloxfruit.accounts.create')" variant="primary" icon="M12 4v16m8-8H4" class="shrink-0">
        Tambah Akun
    </x-btn>
</div>

{{-- Table --}}
<div class="table-container overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
        <thead class="bg-gray-50 dark:bg-slate-900/50">
            <tr>
                <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase text-gray-500 dark:text-gray-400">Username</th>
                <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase text-gray-500 dark:text-gray-400">Password</th>
                <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase text-gray-500 dark:text-gray-400">Sword / Gun</th>
                <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase text-gray-500 dark:text-gray-400">Fruit</th>
                <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase text-gray-500 dark:text-gray-400">Belly</th>
                <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase text-gray-500 dark:text-gray-400">Fragment</th>
                <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase text-gray-500 dark:text-gray-400">Race</th>
                <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase text-gray-500 dark:text-gray-400">Level</th>
                <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase text-gray-500 dark:text-gray-400">Harga Beli</th>
                <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase text-gray-500 dark:text-gray-400">Harga Jual</th>
                <th class="px-3 py-2.5 text-center text-[11px] font-semibold uppercase text-gray-500 dark:text-gray-400">Status</th>
                <th class="px-3 py-2.5 text-center text-[11px] font-semibold uppercase text-gray-500 dark:text-gray-400">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
            @forelse($accounts as $akun)
            <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/30 {{ $akun->status === 'terjual' ? 'opacity-60' : '' }}">
                <td class="px-3 py-2.5 text-sm font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">{{ $akun->username_roblox }}</td>
                <td class="px-3 py-2.5 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap" x-data="{ show: false }">
                    <button type="button" x-show="!show" @click="show = true" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 inline-flex items-center gap-1">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        ••••••
                    </button>
                    <span x-show="show" x-cloak @click="show = false" class="cursor-pointer font-mono text-xs">{{ $akun->password_roblox }}</span>
                </td>
                <td class="px-3 py-2.5 text-xs text-gray-600 dark:text-gray-400 max-w-[180px]">{{ $akun->sword_gun ?: '-' }}</td>
                <td class="px-3 py-2.5 text-xs text-gray-600 dark:text-gray-400">{{ $akun->fruit ?: '-' }}</td>
                <td class="px-3 py-2.5 text-xs text-right text-gray-600 dark:text-gray-400">{{ $akun->belly ?: '-' }}</td>
                <td class="px-3 py-2.5 text-xs text-right text-gray-600 dark:text-gray-400">{{ $akun->fragment ?: '-' }}</td>
                <td class="px-3 py-2.5 text-xs text-gray-600 dark:text-gray-400">{{ $akun->race ?: '-' }}</td>
                <td class="px-3 py-2.5 text-sm text-right font-medium text-gray-700 dark:text-gray-300">{{ $akun->level ?: '-' }}</td>
                <td class="px-3 py-2.5 text-sm text-right text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $akun->harga_beli ? format_rupiah($akun->harga_beli) : '-' }}</td>
                <td class="px-3 py-2.5 text-sm text-right font-semibold whitespace-nowrap {{ $akun->harga_jual ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400 dark:text-gray-500' }}">{{ $akun->harga_jual ? format_rupiah($akun->harga_jual) : '-' }}</td>
                <td class="px-3 py-2.5 text-center">
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold
                        {{ $akun->status === 'tersedia' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : ($akun->status === 'terjual' ? 'bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400') }}">
                        {{ ucfirst($akun->status) }}
                    </span>
                </td>
                <td class="px-3 py-2.5 text-center whitespace-nowrap">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('bloxfruit.accounts.edit', $akun) }}" class="text-[11px] font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">Edit</a>
                        <x-confirm-form :action="route('bloxfruit.accounts.destroy', $akun)" method="DELETE" message="Yakin hapus akun {{ $akun->username_roblox }}?">
                            <button class="text-[11px] font-medium text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">Hapus</button>
                        </x-confirm-form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="12" class="px-4 py-12 text-center">
                <x-empty-state compact icon="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" message="Belum ada stok akun" />
            </td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $accounts->links() }}</div>
@endsection
