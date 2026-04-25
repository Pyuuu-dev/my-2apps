@extends('layouts.app')
@section('title', 'Jenis Joki')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <p class="text-sm text-gray-500">Kelola daftar jenis joki dan harga</p>
    <a href="{{ route('bloxfruit.joki-services.create') }}" class="btn-primary inline-flex items-center gap-2 text-sm">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Jenis
    </a>
</div>

@foreach($kategoriLabels as $katKey => $kat)
@if(isset($services[$katKey]))
<div class="mb-6">
    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3">{{ $kat['icon'] }} {{ $kat['label'] }}</h3>
    <div class="table-container overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase text-gray-500">Nama</th>
                    <th class="px-4 py-2.5 text-right text-[11px] font-semibold uppercase text-gray-500">Harga</th>
                    <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase text-gray-500">Keterangan</th>
                    <th class="px-4 py-2.5 text-center text-[11px] font-semibold uppercase text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($services[$katKey] as $svc)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-4 py-2.5 text-sm font-medium text-gray-900">{{ $svc->nama }}</td>
                    <td class="px-4 py-2.5 text-sm text-right font-semibold text-indigo-600">{{ number_format($svc->harga) }}</td>
                    <td class="px-4 py-2.5 text-sm text-gray-500">{{ $svc->keterangan ?? '-' }}</td>
                    <td class="px-4 py-2.5 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('bloxfruit.joki-services.edit', $svc) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">Edit</a>
                            <form method="POST" action="{{ route('bloxfruit.joki-services.destroy', $svc) }}" onsubmit="return confirm('Hapus {{ $svc->nama }}?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:text-red-800 text-sm">Hapus</button>
                            </form>
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
@endsection
