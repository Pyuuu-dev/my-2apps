@extends('layouts.app')
@section('title', isset($storage) ? 'Edit Akun Storage' : 'Tambah Akun Storage')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ isset($storage) ? route('bloxfruit.storage.update', $storage) : route('bloxfruit.storage.store') }}">
        @csrf
        @if(isset($storage)) @method('PUT') @endif

        <x-form-card class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-form-label required>Nama Akun</x-form-label>
                    <x-form-input name="nama_akun" :value="$storage->nama_akun ?? ''" required placeholder="Contoh: Storage 1" />
                </div>
                <div>
                    <x-form-label>Username Roblox</x-form-label>
                    <x-form-input name="username" :value="$storage->username ?? ''" placeholder="Username Roblox" />
                </div>
                <div>
                    <x-form-label>Kapasitas Storage <span class="text-xs text-gray-400 dark:text-gray-500 font-normal">per item</span></x-form-label>
                    <x-form-input type="number" name="kapasitas_storage" :value="$storage->kapasitas_storage ?? 1" min="1" max="99" />
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">Max jumlah per fruit/skin yang bisa disimpan</p>
                </div>
            </div>
            @if(isset($storage))
            <div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="aktif" value="1" {{ old('aktif', $storage->aktif) ? 'checked' : '' }} class="rounded border-gray-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Akun Aktif</span>
                </label>
            </div>
            @endif
            <div>
                <x-form-label>Catatan</x-form-label>
                <x-form-textarea name="catatan" rows="3" :value="$storage->catatan ?? ''" />
            </div>
            <div class="flex items-center gap-2 pt-1">
                <x-btn type="submit" variant="primary" size="lg">{{ isset($storage) ? 'Perbarui' : 'Simpan' }}</x-btn>
                <x-btn :href="route('bloxfruit.storage.index')" variant="secondary" size="lg">Batal</x-btn>
            </div>
        </x-form-card>
    </form>
</div>
@endsection
