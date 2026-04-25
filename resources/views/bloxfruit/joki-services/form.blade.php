@extends('layouts.app')
@section('title', isset($service) ? 'Edit Jenis Joki' : 'Tambah Jenis Joki')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ isset($service) ? route('bloxfruit.joki-services.update', $service) : route('bloxfruit.joki-services.store') }}" class="space-y-5 rounded-xl bg-white dark:bg-slate-800 p-6 shadow-sm border border-gray-100 dark:border-slate-700">
        @csrf
        @if(isset($service)) @method('PUT') @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori *</label>
                <select name="kategori" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    @foreach($kategoriOptions as $k => $v)
                    <option value="{{ $k }}" {{ old('kategori', $service->kategori ?? '') == $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp) *</label>
                <input type="number" name="harga" value="{{ old('harga', $service->harga ?? 0) }}" required min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Jenis Joki *</label>
                <input type="text" name="nama" value="{{ old('nama', $service->nama ?? '') }}" required placeholder="Contoh: God Human" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan <span class="text-xs text-gray-400">- opsional</span></label>
                <input type="text" name="keterangan" value="{{ old('keterangan', $service->keterangan ?? '') }}" placeholder="Contoh: Full, per 1, dll" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-lg bg-indigo-600 px-6 py-2 text-sm font-medium text-white hover:bg-indigo-700">{{ isset($service) ? 'Perbarui' : 'Simpan' }}</button>
            <a href="{{ route('bloxfruit.joki-services.index') }}" class="rounded-lg bg-gray-100 px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Batal</a>
        </div>
    </form>
</div>
@endsection
