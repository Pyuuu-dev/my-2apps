@extends('layouts.app')
@section('title', 'Jenis Joki')

@section('content')
<div x-data="jokiPromoPage(@js($jokiForCopy))">
    <x-page-header subtitle="Kelola daftar jenis joki dan harga">
        <x-slot:actions>
            @if(!empty($jokiForCopy))
            <button type="button" @click="showCopy = true" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                Copy Teks Promo
            </button>
            @endif
            <x-btn :href="route('bloxfruit.joki-services.create')" variant="primary" icon="M12 4v16m8-8H4">
                Tambah Jenis
            </x-btn>
        </x-slot:actions>
    </x-page-header>

    @foreach($kategoriLabels as $katKey => $kat)
    @if(isset($services[$katKey]))
    <div class="mb-6">
        <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3">{{ $kat['icon'] }} {{ $kat['label'] }}</h3>
        <div class="table-container overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                <thead class="bg-gray-50 dark:bg-slate-900/50">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase text-gray-500 dark:text-gray-400">Nama</th>
                        <th class="px-4 py-2.5 text-right text-[11px] font-semibold uppercase text-gray-500 dark:text-gray-400">Harga</th>
                        <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase text-gray-500 dark:text-gray-400">Keterangan</th>
                        <th class="px-4 py-2.5 text-center text-[11px] font-semibold uppercase text-gray-500 dark:text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    @foreach($services[$katKey] as $svc)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/30">
                        <td class="px-4 py-2.5 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $svc->nama }}</td>
                        <td class="px-4 py-2.5 text-sm text-right font-semibold text-indigo-600 dark:text-indigo-400">{{ format_angka($svc->harga) }}</td>
                        <td class="px-4 py-2.5 text-sm text-gray-500 dark:text-gray-400">{{ $svc->keterangan ?? '-' }}</td>
                        <td class="px-4 py-2.5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('bloxfruit.joki-services.edit', $svc) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 text-sm font-medium">Edit</a>
                                <x-confirm-form :action="route('bloxfruit.joki-services.destroy', $svc)" method="DELETE" :message="'Hapus ' . $svc->nama . '?'">
                                    <button class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 text-sm font-medium">Hapus</button>
                                </x-confirm-form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
    @endforeach

    @include('bloxfruit.partials.copy-joki-modal')
</div>

@include('bloxfruit.partials.copy-joki-script')
@endsection
