@extends('layouts.app')
@section('title', 'Aktivitas Harian')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <p class="text-sm text-gray-500">Catat aktivitas harian kamu</p>
    <a href="{{ route('diet.activities.create') }}" class="btn-success inline-flex items-center gap-1.5 text-sm">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Catat Aktivitas
    </a>
</div>

<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
    @foreach($targetHarian as $t)
    <div class="stat-card" style="--accent: linear-gradient(90deg, #10b981, #059669)">
        <p class="text-[11px] text-gray-500">{{ $t['label'] }}</p>
        <p class="text-xl font-extrabold text-emerald-600">{{ $t['target'] }}</p>
        <p class="text-[11px] text-gray-400">{{ $t['satuan'] }} / hari</p>
        <p class="text-[11px] text-gray-400 mt-1">{{ $t['tips'] }}</p>
    </div>
    @endforeach
</div>

<div class="glass-card rounded-2xl p-4 mb-6">
    <div class="flex items-center justify-between mb-2">
        <p class="text-sm font-semibold text-gray-700">Konsistensi Program</p>
        <p class="text-sm font-bold text-emerald-600">{{ $konsistensi['konsistensi_persen'] }}%</p>
    </div>
    <div class="h-2.5 w-full rounded-full bg-gray-100 overflow-hidden">
        <div class="h-2.5 rounded-full transition-all" style="width: {{ $konsistensi['konsistensi_persen'] }}%; background: linear-gradient(90deg, #10b981, #059669);"></div>
    </div>
    <p class="text-[11px] text-gray-400 mt-1.5">{{ $konsistensi['total_hari_aktif'] }} dari {{ $konsistensi['total_hari_program'] }} hari aktif mencatat</p>
</div>

<div class="table-container overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase text-gray-500">Tanggal</th>
                <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase text-gray-500">Langkah</th>
                <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase text-gray-500">Jarak</th>
                <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase text-gray-500">Kalori</th>
                <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase text-gray-500">Berat</th>
                <th class="px-3 py-2.5 text-center text-[11px] font-semibold uppercase text-gray-500">Tidur</th>
                <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase text-gray-500">Air</th>
                <th class="px-3 py-2.5 text-center text-[11px] font-semibold uppercase text-gray-500">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($activities as $act)
            <tr class="hover:bg-gray-50/50">
                <td class="px-4 py-2.5 text-sm font-medium text-gray-900">{{ $act->tanggal->translatedFormat('d/m') }}</td>
                <td class="px-3 py-2.5 text-sm text-right text-gray-600">{{ number_format($act->langkah_kaki) }}</td>
                <td class="px-3 py-2.5 text-sm text-right text-gray-600">{{ $act->jarak_km }} km</td>
                <td class="px-3 py-2.5 text-sm text-right font-medium text-red-600">{{ number_format($act->kalori_terbakar) }}</td>
                <td class="px-3 py-2.5 text-sm text-right text-gray-600">{{ $act->berat_badan ?? '-' }} kg</td>
                <td class="px-3 py-2.5 text-sm text-center text-gray-600">{{ $act->jam_tidur }} jam</td>
                <td class="px-3 py-2.5 text-sm text-right text-gray-600">{{ number_format($act->air_minum_ml) }} ml</td>
                <td class="px-3 py-2.5 text-center">
                    <div class="flex items-center justify-center gap-1.5">
                        <a href="{{ route('diet.activities.edit', $act) }}" class="text-[11px] text-gray-400 hover:text-emerald-600">Edit</a>
                        <form method="POST" action="{{ route('diet.activities.destroy', $act) }}" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="text-[11px] text-gray-400 hover:text-red-500">Hapus</button></form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="px-4 py-6 text-center text-sm text-gray-300">Belum ada data aktivitas</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $activities->links() }}</div>
@endsection
