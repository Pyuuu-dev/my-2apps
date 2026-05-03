@extends('layouts.app')
@section('title', isset($storage) ? 'Edit Akun Storage' : 'Tambah Akun Storage')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ isset($storage) ? route('bloxfruit.storage.update', $storage) : route('bloxfruit.storage.store') }}" class="space-y-6 rounded-xl bg-white dark:bg-slate-800 p-6 shadow-sm border border-gray-100 dark:border-slate-700">
        @csrf
        @if(isset($storage)) @method('PUT') @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Akun *</label>
                <input type="text" name="nama_akun" value="{{ old('nama_akun', $storage->nama_akun ?? '') }}" required placeholder="Contoh: Storage 1" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Username Roblox</label>
                <input type="text" name="username" value="{{ old('username', $storage->username ?? '') }}" placeholder="Username Roblox" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kapasitas Storage <span class="text-xs text-gray-400 font-normal">per item</span></label>
                <input type="number" name="kapasitas_storage" value="{{ old('kapasitas_storage', $storage->kapasitas_storage ?? 1) }}" min="1" max="99" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                <p class="text-[10px] text-gray-400 mt-1">Max jumlah per fruit/skin yang bisa disimpan</p>
            </div>
        </div>
        @if(isset($storage))
        <div>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="aktif" value="1" {{ old('aktif', $storage->aktif) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-sm text-gray-700">Akun Aktif</span>
            </label>
        </div>
        @endif
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
            <textarea name="catatan" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('catatan', $storage->catatan ?? '') }}</textarea>
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-lg bg-indigo-600 px-6 py-2 text-sm font-medium text-white hover:bg-indigo-700">{{ isset($storage) ? 'Perbarui' : 'Simpan' }}</button>
            <a href="{{ route('bloxfruit.storage.index') }}" class="rounded-lg bg-gray-100 px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Batal</a>
        </div>
    </form>
</div>
@endsection
