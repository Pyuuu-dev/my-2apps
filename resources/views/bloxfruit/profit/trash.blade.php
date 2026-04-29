@extends('layouts.app')
@section('title', 'Transaksi Terhapus')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <a href="{{ route('bloxfruit.profit.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">&larr; Kembali ke Keuangan</a>
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mt-1">Transaksi Terhapus</h2>
        <p class="text-sm text-gray-500">{{ $totalTrashed }} transaksi di sampah</p>
    </div>
    @if($totalTrashed > 0)
    <form method="POST" action="{{ route('bloxfruit.profit.restoreAll') }}" onsubmit="return confirm('Kembalikan semua {{ $totalTrashed }} transaksi?')">
        @csrf
        <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Restore Semua
        </button>
    </form>
    @endif
</div>

@php
    $katLabels = ['fruit' => ['Fruit', 'text-indigo-600', 'bg-indigo-50'], 'skin' => ['Skin', 'text-pink-600', 'bg-pink-50'], 'gamepass' => ['Gamepass', 'text-blue-600', 'bg-blue-50'], 'permanent' => ['Permanent', 'text-amber-600', 'bg-amber-50'], 'joki' => ['Joki', 'text-orange-600', 'bg-orange-50'], 'akun' => ['Akun', 'text-teal-600', 'bg-teal-50'], 'lainnya' => ['Lainnya', 'text-gray-600', 'bg-gray-50']];
@endphp

<div class="glass-card rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 dark:bg-slate-800">
                <tr>
                    <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase text-gray-500">Tanggal</th>
                    <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase text-gray-500">Kategori</th>
                    <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase text-gray-500">Keterangan</th>
                    <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase text-gray-500">Modal</th>
                    <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase text-gray-500">Pendapatan</th>
                    <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase text-gray-500">Untung</th>
                    <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase text-gray-500">Dihapus</th>
                    <th class="px-3 py-2.5 text-center text-[11px] font-semibold uppercase text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($trashedRecords as $rec)
                @php $katInfo = $katLabels[$rec->kategori] ?? ['?', 'text-gray-600', 'bg-gray-50']; @endphp
                <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-800/50">
                    <td class="px-3 py-2.5">
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $rec->tanggal->format('d/m/Y') }}</p>
                        <p class="text-[10px] text-gray-400">{{ $rec->created_at->format('H:i') }}</p>
                    </td>
                    <td class="px-3 py-2.5"><span class="rounded-md px-2 py-0.5 text-[10px] font-bold {{ $katInfo[1] }} {{ $katInfo[2] }}">{{ $katInfo[0] }}</span></td>
                    <td class="px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 max-w-xs truncate">{{ $rec->keterangan ?? '-' }}</td>
                    <td class="px-3 py-2.5 text-sm text-right text-red-600">{{ number_format($rec->modal) }}</td>
                    <td class="px-3 py-2.5 text-sm text-right text-blue-600">{{ number_format($rec->pendapatan) }}</td>
                    <td class="px-3 py-2.5 text-sm text-right font-bold {{ $rec->keuntungan >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ number_format($rec->keuntungan) }}</td>
                    <td class="px-3 py-2.5 text-[11px] text-gray-400">{{ $rec->deleted_at->diffForHumans() }}</td>
                    <td class="px-3 py-2.5 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <form method="POST" action="{{ route('bloxfruit.profit.restore', $rec->slug) }}">
                                @csrf @method('PATCH')
                                <button class="text-[11px] font-medium text-emerald-600 hover:text-emerald-800">Restore</button>
                            </form>
                            <form method="POST" action="{{ route('bloxfruit.profit.forceDelete', $rec->slug) }}" onsubmit="return confirm('Hapus permanen? Data tidak bisa dikembalikan!')">
                                @csrf @method('DELETE')
                                <button class="text-[11px] text-red-500 hover:text-red-700">Hapus Permanen</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-4 py-12 text-center text-sm text-gray-400">Sampah kosong</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
