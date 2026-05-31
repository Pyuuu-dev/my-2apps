@extends('layouts.app')
@section('title', 'Kategori Joki')

@section('content')
<x-page-header subtitle="Kelola daftar kategori joki (urutan, label, icon)">
    <x-slot:actions>
        <x-btn :href="route('bloxfruit.joki-services.index')" variant="secondary" icon="M15 19l-7-7 7-7">
            Kembali ke Jenis Joki
        </x-btn>
        <x-btn :href="route('bloxfruit.joki-categories.create')" variant="primary" icon="M12 4v16m8-8H4">
            Tambah Kategori
        </x-btn>
    </x-slot:actions>
</x-page-header>

@if(session('error'))
<div class="mb-4 rounded-lg border border-red-200 bg-red-50 dark:bg-red-900/20 dark:border-red-800 px-4 py-3 text-sm text-red-700 dark:text-red-300">
    {{ session('error') }}
</div>
@endif

<div class="table-container overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
        <thead class="bg-gray-50 dark:bg-slate-900/50">
            <tr>
                <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase text-gray-500 dark:text-gray-400 w-16">Urutan</th>
                <th class="px-4 py-2.5 text-center text-[11px] font-semibold uppercase text-gray-500 dark:text-gray-400 w-16">Icon</th>
                <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase text-gray-500 dark:text-gray-400">Label</th>
                <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase text-gray-500 dark:text-gray-400">Key</th>
                <th class="px-4 py-2.5 text-center text-[11px] font-semibold uppercase text-gray-500 dark:text-gray-400">Jenis</th>
                <th class="px-4 py-2.5 text-center text-[11px] font-semibold uppercase text-gray-500 dark:text-gray-400">Status</th>
                <th class="px-4 py-2.5 text-center text-[11px] font-semibold uppercase text-gray-500 dark:text-gray-400 w-32">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
            @forelse($categories as $cat)
            @php
                $isProtected = $cat->key === 'lainnya';
                $jumlah = (int) ($counts[$cat->key] ?? 0);
            @endphp
            <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/30">
                <td class="px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 font-mono">{{ $cat->urutan }}</td>
                <td class="px-4 py-2.5 text-center text-lg">{{ $cat->icon ?: '📝' }}</td>
                <td class="px-4 py-2.5 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $cat->label }}</td>
                <td class="px-4 py-2.5 text-xs font-mono text-gray-500 dark:text-gray-400">{{ $cat->key }}</td>
                <td class="px-4 py-2.5 text-center">
                    @if($jumlah > 0)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">{{ $jumlah }}</span>
                    @else
                    <span class="text-xs text-gray-400 dark:text-gray-500">0</span>
                    @endif
                </td>
                <td class="px-4 py-2.5 text-center">
                    @if($cat->aktif)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">Aktif</span>
                    @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-slate-800 dark:text-gray-400">Nonaktif</span>
                    @endif
                </td>
                <td class="px-4 py-2.5 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('bloxfruit.joki-categories.edit', $cat) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 text-sm font-medium">Edit</a>
                        @if($isProtected)
                        <span class="text-xs text-gray-400 dark:text-gray-500" title="Kategori sistem, tidak bisa dihapus">Sistem</span>
                        @else
                        <x-confirm-form :action="route('bloxfruit.joki-categories.destroy', $cat)" method="DELETE" :message="'Hapus kategori ' . $cat->label . '?'">
                            <button class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 text-sm font-medium">Hapus</button>
                        </x-confirm-form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                    Belum ada kategori. <a href="{{ route('bloxfruit.joki-categories.create') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Tambah kategori</a> dulu.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<p class="mt-4 text-xs text-gray-500 dark:text-gray-400">
    Tip: Kolom <span class="font-mono">key</span> dipakai sebagai identifier internal (auto-generate dari label).
    Kategori <span class="font-mono">lainnya</span> tidak bisa dihapus karena dipakai sebagai fallback custom di order joki.
</p>
@endsection
