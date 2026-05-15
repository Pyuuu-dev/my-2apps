@extends('layouts.app')
@section('title', isset($gamepass) ? 'Edit Gamepass' : 'Tambah Gamepass')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ isset($gamepass) ? route('bloxfruit.gamepasses.update', $gamepass) : route('bloxfruit.gamepasses.store') }}">
        @csrf
        @if(isset($gamepass)) @method('PUT') @endif

        <x-form-card class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <x-form-label required>Nama Gamepass</x-form-label>
                    <x-form-input name="nama" :value="$gamepass->nama ?? ''" required />
                </div>
                <div class="sm:col-span-2">
                    <x-form-label>Harga Robux (R$)</x-form-label>
                    <x-form-input type="number" name="harga_robux" :value="$gamepass->harga_robux ?? 0" min="0" />
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">Harga dalam Robux di game</p>
                </div>
                <div>
                    <x-form-label>Harga Beli (IDR)</x-form-label>
                    <x-form-input type="number" name="harga_beli" :value="$gamepass->harga_beli ?? 0" min="0" />
                </div>
                <div>
                    <x-form-label>Harga Jual (IDR)</x-form-label>
                    <x-form-input type="number" name="harga_jual" :value="$gamepass->harga_jual ?? 0" min="0" />
                </div>
            </div>
            <div>
                <x-form-label>Deskripsi</x-form-label>
                <x-form-textarea name="deskripsi" rows="3" :value="$gamepass->deskripsi ?? ''" />
            </div>
            <p class="text-xs text-gray-400 dark:text-gray-500">* Stok dikelola melalui menu Akun Storage</p>
            <div class="flex items-center gap-2 pt-1">
                <x-btn type="submit" variant="primary" size="lg">{{ isset($gamepass) ? 'Perbarui' : 'Simpan' }}</x-btn>
                <x-btn :href="route('bloxfruit.gamepasses.index')" variant="secondary" size="lg">Batal</x-btn>
            </div>
        </x-form-card>
    </form>
</div>
@endsection
