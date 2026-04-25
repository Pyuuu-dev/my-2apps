@extends('layouts.app')
@section('title', isset($exercise) ? 'Edit Olahraga' : 'Tambah Olahraga')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ isset($exercise) ? route('diet.exercises.update', $exercise) : route('diet.exercises.store') }}" class="space-y-6 rounded-xl bg-white p-6 shadow-sm border border-gray-100">
        @csrf
        @if(isset($exercise)) @method('PUT') @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal *</label>
                <input type="date" name="tanggal" value="{{ old('tanggal', isset($exercise) ? $exercise->tanggal->format('Y-m-d') : date('Y-m-d')) }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Olahraga *</label>
                <input type="text" name="jenis_olahraga" value="{{ old('jenis_olahraga', $exercise->jenis_olahraga ?? '') }}" required placeholder="Contoh: Lari, Renang, Push Up" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Durasi (menit) *</label>
                <input type="number" name="durasi_menit" value="{{ old('durasi_menit', $exercise->durasi_menit ?? '') }}" required min="1" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kalori Terbakar</label>
                <input type="number" name="kalori_terbakar" value="{{ old('kalori_terbakar', $exercise->kalori_terbakar ?? 0) }}" min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Intensitas *</label>
                <select name="intensitas" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    @foreach(['ringan' => 'Ringan', 'sedang' => 'Sedang', 'berat' => 'Berat'] as $k => $v)
                    <option value="{{ $k }}" {{ old('intensitas', $exercise->intensitas ?? 'sedang') == $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
            <textarea name="catatan" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('catatan', $exercise->catatan ?? '') }}</textarea>
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-lg bg-emerald-600 px-6 py-2 text-sm font-medium text-white hover:bg-emerald-700">{{ isset($exercise) ? 'Perbarui' : 'Simpan' }}</button>
            <a href="{{ route('diet.exercises.index') }}" class="rounded-lg bg-gray-100 px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Batal</a>
        </div>
    </form>
</div>
@endsection
