@extends('layouts.app')
@section('title', 'Program Diet')

@section('content')
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">Kelola program diet kamu</p>
    <a href="{{ route('diet.plans.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Buat Program
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @forelse($plans as $plan)
    <div class="rounded-xl bg-white p-5 shadow-sm border border-gray-100 {{ $plan->status === 'aktif' ? 'ring-2 ring-emerald-500' : '' }}">
        <div class="flex items-start justify-between mb-3">
            <div>
                <h3 class="font-semibold text-gray-900">{{ $plan->nama }}</h3>
                <p class="text-xs text-gray-500">{{ $plan->tanggal_mulai->format('d/m/Y') }} - {{ $plan->tanggal_selesai?->format('d/m/Y') ?? 'Belum ditentukan' }}</p>
            </div>
            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $plan->status === 'aktif' ? 'bg-green-100 text-green-700' : ($plan->status === 'selesai' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700') }}">
                {{ ucfirst($plan->status) }}
            </span>
        </div>
        <div class="grid grid-cols-3 gap-2 text-center mb-4">
            <div class="rounded-lg bg-gray-50 p-2">
                <p class="text-xs text-gray-500">Awal</p>
                <p class="text-sm font-bold text-gray-900">{{ $plan->berat_awal }} kg</p>
            </div>
            <div class="rounded-lg bg-gray-50 p-2">
                <p class="text-xs text-gray-500">Sekarang</p>
                <p class="text-sm font-bold text-emerald-600">{{ $plan->berat_sekarang ?? '-' }} kg</p>
            </div>
            <div class="rounded-lg bg-gray-50 p-2">
                <p class="text-xs text-gray-500">Target</p>
                <p class="text-sm font-bold text-blue-600">{{ $plan->berat_target }} kg</p>
            </div>
        </div>
        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
            <div class="flex items-center gap-2">
                <p class="text-xs text-gray-500">{{ number_format($plan->kalori_harian_target) }} kkal/hari</p>
                @if($plan->monthly_logs_count > 0)
                <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-medium text-blue-600">{{ $plan->monthly_logs_count }} bulan</span>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('diet.plans.progress', $plan) }}" class="rounded-md bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 hover:bg-emerald-100">Progress</a>
                <a href="{{ route('diet.plans.edit', $plan) }}" class="rounded-md bg-gray-50 px-2.5 py-1 text-[11px] font-medium text-gray-500 hover:bg-gray-100">Edit</a>
                <form method="POST" action="{{ route('diet.plans.destroy', $plan) }}" onsubmit="return confirm('Yakin hapus?')">
                    @csrf @method('DELETE')
                    <button class="rounded-md bg-gray-50 px-2.5 py-1 text-[11px] font-medium text-red-500 hover:bg-red-50">Hapus</button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full py-8 text-center text-sm text-gray-400">Belum ada program diet</div>
    @endforelse
</div>
<div class="mt-4">{{ $plans->links() }}</div>
@endsection
