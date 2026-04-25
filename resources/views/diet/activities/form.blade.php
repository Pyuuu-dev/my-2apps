@extends('layouts.app')
@section('title', isset($activity) ? 'Edit Aktivitas' : 'Catat Aktivitas')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ isset($activity) ? route('diet.activities.update', $activity) : route('diet.activities.store') }}" class="space-y-6 rounded-xl bg-white p-6 shadow-sm border border-gray-100">
        @csrf
        @if(isset($activity)) @method('PUT') @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal *</label>
                <input type="date" name="tanggal" value="{{ old('tanggal', isset($activity) ? $activity->tanggal->format('Y-m-d') : date('Y-m-d')) }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Langkah Kaki <span class="text-xs text-gray-400 font-normal">- opsional</span></label>
                <input type="number" name="langkah_kaki" value="{{ old('langkah_kaki', $activity->langkah_kaki ?? '') }}" min="0" placeholder="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jarak (km) <span class="text-xs text-gray-400 font-normal">- opsional</span></label>
                <input type="number" name="jarak_km" value="{{ old('jarak_km', $activity->jarak_km ?? '') }}" min="0" step="0.01" placeholder="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kalori Terbakar <span class="text-xs text-gray-400 font-normal">- opsional</span></label>
                <input type="number" name="kalori_terbakar" value="{{ old('kalori_terbakar', $activity->kalori_terbakar ?? '') }}" min="0" placeholder="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Berat Badan (kg) <span class="text-xs text-gray-400 font-normal">- opsional</span></label>
                <input type="number" name="berat_badan" value="{{ old('berat_badan', $activity->berat_badan ?? '') }}" min="1" step="0.1" placeholder="Kosongkan jika tidak timbang" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jam Tidur <span class="text-xs text-gray-400 font-normal">- opsional</span></label>
                <input type="number" name="jam_tidur" value="{{ old('jam_tidur', $activity->jam_tidur ?? '') }}" min="0" max="24" placeholder="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Air Minum (ml) <span class="text-xs text-gray-400 font-normal">- opsional</span></label>
                <input type="number" name="air_minum_ml" value="{{ old('air_minum_ml', $activity->air_minum_ml ?? '') }}" min="0" placeholder="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
            <textarea name="catatan" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('catatan', $activity->catatan ?? '') }}</textarea>
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-lg bg-emerald-600 px-6 py-2 text-sm font-medium text-white hover:bg-emerald-700">{{ isset($activity) ? 'Perbarui' : 'Simpan' }}</button>
            <a href="{{ route('diet.activities.index') }}" class="rounded-lg bg-gray-100 px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Batal</a>
        </div>
    </form>
</div>
@endsection
