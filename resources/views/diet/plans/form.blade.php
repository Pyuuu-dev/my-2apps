@extends('layouts.app')
@section('title', isset($plan) ? 'Edit Program Diet' : 'Buat Program Diet')

@section('content')
<div class="max-w-xl mx-auto">
    @if(!isset($plan))
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center h-12 w-12 rounded-2xl bg-emerald-100 mb-3">
            <svg class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        </div>
        <h2 class="text-lg font-bold text-gray-900">Mulai Program Diet</h2>
        <p class="text-sm text-gray-500 mt-1">Isi data dasar kamu, sistem akan menghitung semuanya secara otomatis.</p>
    </div>
    @endif

    <form method="POST" action="{{ isset($plan) ? route('diet.plans.update', $plan) : route('diet.plans.store') }}" class="space-y-5 rounded-2xl bg-white p-6 shadow-sm border border-gray-100">
        @csrf
        @if(isset($plan)) @method('PUT') @endif

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Program</label>
            <input type="text" name="nama" value="{{ old('nama', $plan->nama ?? 'Diet ' . now()->translatedFormat('F Y')) }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                <select name="gender" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    <option value="pria" {{ old('gender', $plan->gender ?? 'pria') == 'pria' ? 'selected' : '' }}>Pria</option>
                    <option value="wanita" {{ old('gender', $plan->gender ?? '') == 'wanita' ? 'selected' : '' }}>Wanita</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Umur</label>
                <input type="number" name="umur" value="{{ old('umur', $plan->umur ?? '') }}" required min="10" max="100" placeholder="tahun" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tinggi Badan</label>
                <input type="number" name="tinggi_cm" value="{{ old('tinggi_cm', $plan->tinggi_cm ?? '') }}" required min="100" max="250" step="0.1" placeholder="cm" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Berat Badan</label>
                <input type="number" name="berat_awal" value="{{ old('berat_awal', $plan->berat_awal ?? '') }}" required min="20" max="300" step="0.1" placeholder="kg" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Seberapa aktif kamu sehari-hari?</label>
            <select name="level_aktivitas" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                @foreach(\App\Services\DietHelperService::labelAktivitas() as $key => $label)
                <option value="{{ $key }}" {{ old('level_aktivitas', $plan->level_aktivitas ?? 'sedang') == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        @if(isset($plan))
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                @foreach(['aktif','selesai','berhenti'] as $s)
                <option value="{{ $s }}" {{ old('status', $plan->status) == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        @endif

        @if(isset($plan) && $plan->bmr > 0)
        <div class="rounded-xl bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-100 p-4">
            <p class="text-xs font-semibold text-emerald-700 mb-3">Hasil Kalkulasi Otomatis</p>
            <div class="grid grid-cols-3 gap-3 text-center mb-3">
                <div class="rounded-lg bg-white p-2">
                    <p class="text-[11px] text-gray-500">BMR</p>
                    <p class="text-base font-extrabold text-emerald-700">{{ number_format($plan->bmr) }}</p>
                    <p class="text-[11px] text-gray-400">kkal/hari</p>
                </div>
                <div class="rounded-lg bg-white p-2">
                    <p class="text-[11px] text-gray-500">TDEE</p>
                    <p class="text-base font-extrabold text-blue-700">{{ number_format($plan->tdee) }}</p>
                    <p class="text-[11px] text-gray-400">kkal/hari</p>
                </div>
                <div class="rounded-lg bg-white p-2">
                    <p class="text-[11px] text-gray-500">Target</p>
                    <p class="text-base font-extrabold text-orange-700">{{ number_format($plan->kalori_harian_target) }}</p>
                    <p class="text-[11px] text-gray-400">kkal/hari</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2 text-center">
                <div class="rounded-lg bg-white p-2">
                    <p class="text-[11px] text-gray-500">Berat Target</p>
                    <p class="text-sm font-bold text-gray-900">{{ $plan->berat_target }} kg</p>
                </div>
                <div class="rounded-lg bg-white p-2">
                    <p class="text-[11px] text-gray-500">Estimasi Selesai</p>
                    <p class="text-sm font-bold text-gray-900">{{ $plan->tanggal_selesai?->translatedFormat('d M Y') ?? '-' }}</p>
                </div>
            </div>
        </div>
        @endif

        <div class="rounded-xl bg-blue-50 border border-blue-100 p-3">
            <p class="text-xs text-blue-700">Sistem akan otomatis menghitung: BMI, berat ideal, target berat badan, target kalori harian, distribusi makro (protein/karbo/lemak), target air minum, target langkah kaki, jadwal olahraga, dan estimasi waktu pencapaian.</p>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="btn-success flex-1">{{ isset($plan) ? 'Perbarui & Hitung Ulang' : 'Mulai Program Diet' }}</button>
            <a href="{{ isset($plan) ? route('diet.plans.index') : route('diet.dashboard') }}" class="rounded-lg bg-gray-100 px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Batal</a>
        </div>
    </form>
</div>
@endsection
