@extends('layouts.app')
@section('title', ($isEdit ?? false) ? 'Edit Progress Bulanan' : 'Tambah Progress Bulanan')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center h-12 w-12 rounded-2xl bg-emerald-100 mb-3">
            <svg class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
        <h2 class="text-lg font-bold text-gray-900">{{ ($isEdit ?? false) ? 'Edit Progress Bulanan' : 'Tambah Progress Bulanan' }}</h2>
        <p class="text-sm text-gray-500 mt-1">Catat berat badan di akhir bulan untuk pantau perkembangan</p>
    </div>

    {{-- Info Progress --}}
    <div class="rounded-2xl bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 p-5 mb-6">
        <div class="grid grid-cols-3 gap-3">
            <div class="text-center">
                <p class="text-[11px] text-blue-600 mb-1">Berat Awal Bulan</p>
                <p class="text-xl font-extrabold text-blue-900">{{ $beratAwalBulan }}</p>
                <p class="text-[11px] text-blue-600">kg</p>
            </div>
            <div class="text-center">
                <p class="text-[11px] text-emerald-600 mb-1">Target Program</p>
                <p class="text-xl font-extrabold text-emerald-900">{{ $plan->berat_target }}</p>
                <p class="text-[11px] text-emerald-600">kg</p>
            </div>
            <div class="text-center">
                <p class="text-[11px] text-amber-600 mb-1">Sisa</p>
                <p class="text-xl font-extrabold text-amber-900">{{ number_format(max(0, $beratAwalBulan - $plan->berat_target), 1) }}</p>
                <p class="text-[11px] text-amber-600">kg lagi</p>
            </div>
        </div>
    </div>

    <form method="POST"
        action="{{ ($isEdit ?? false) ? route('diet.plans.monthly.update', [$plan, $log]) : route('diet.plans.monthly.store', $plan) }}"
        class="space-y-5 rounded-2xl bg-white p-6 shadow-sm border border-gray-100"
        x-data="monthlyForm()">
        @csrf
        @if($isEdit ?? false)
            @method('PUT')
        @endif

        {{-- Pilih Bulan --}}
        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">Bulan *</label>
            @if($isEdit ?? false)
                <input type="hidden" name="bulan" value="{{ $bulan }}">
                <div class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-700">
                    {{ \Carbon\Carbon::parse($bulan . '-01')->translatedFormat('F Y') }}
                </div>
            @else
                <select name="bulan" x-model="selectedBulan" @change="updateBeratAwal()"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm font-semibold">
                    @foreach($daftarBulan as $key => $label)
                        @if(!in_array($key, $bulanSudahAda))
                        <option value="{{ $key }}" {{ $key === $bulan ? 'selected' : '' }}>{{ $label }}</option>
                        @else
                        <option value="{{ $key }}" disabled class="text-gray-400">{{ $label }} (sudah ada)</option>
                        @endif
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Pilih bulan yang ingin dicatat progressnya</p>
            @endif
        </div>

        {{-- Berat Badan --}}
        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">Berat Badan Akhir Bulan (kg) *</label>
            <input type="number" name="berat_akhir_bulan"
                value="{{ old('berat_akhir_bulan', $existing->berat_akhir_bulan ?? '') }}"
                required step="0.1" min="20" max="300"
                placeholder="Contoh: 82.5"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-2xl font-bold text-center py-3"
                x-model="beratAkhir" @input="hitungPerubahan()"
                autofocus>

            {{-- Preview perubahan --}}
            <div class="mt-2 text-center" x-show="beratAkhir > 0" x-cloak>
                <span x-show="perubahan > 0" class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-sm font-bold text-emerald-700">
                    Turun <span x-text="perubahan"></span> kg
                </span>
                <span x-show="perubahan < 0" class="inline-flex items-center gap-1 rounded-full bg-red-100 px-3 py-1 text-sm font-bold text-red-700">
                    Naik <span x-text="Math.abs(perubahan)"></span> kg
                </span>
                <span x-show="perubahan == 0" class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-3 py-1 text-sm font-bold text-gray-600">
                    Tetap
                </span>
            </div>
        </div>

        {{-- Catatan --}}
        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-2">Catatan (opsional)</label>
            <textarea name="catatan" rows="3"
                placeholder="Contoh: Konsisten olahraga 3x seminggu. Cheat day 2x tapi tetap terkontrol."
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('catatan', $existing->catatan ?? '') }}</textarea>
        </div>

        {{-- Info Otomatis --}}
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4">
            <div class="flex items-start gap-2">
                <svg class="h-5 w-5 text-emerald-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="text-sm text-emerald-800">
                    <p class="font-semibold mb-1">Dihitung Otomatis:</p>
                    <ul class="space-y-0.5 text-xs text-emerald-700">
                        <li>Perubahan berat, rata-rata kalori, hari olahraga, konsistensi, dan target kalori bulan depan</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="btn-success flex-1 py-3">
                <span class="font-bold">{{ ($isEdit ?? false) ? 'Perbarui' : 'Simpan Progress' }}</span>
            </button>
            <a href="{{ route('diet.plans.progress', $plan) }}" class="rounded-lg bg-gray-100 px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-200">Batal</a>
        </div>
    </form>
</div>

<script>
function monthlyForm() {
    return {
        beratAwal: {{ $beratAwalBulan }},
        beratAkhir: {{ old('berat_akhir_bulan', $existing->berat_akhir_bulan ?? 0) }},
        perubahan: 0,
        selectedBulan: '{{ $bulan }}',
        init() {
            this.hitungPerubahan();
        },
        hitungPerubahan() {
            if (this.beratAkhir > 0) {
                this.perubahan = Math.round((this.beratAwal - this.beratAkhir) * 10) / 10;
            }
        },
        updateBeratAwal() {
            // Reload halaman dengan bulan baru untuk update berat awal
            window.location.href = '{{ route("diet.plans.monthly.create", $plan) }}?bulan=' + this.selectedBulan;
        }
    }
}
</script>
@endsection
