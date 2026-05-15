@extends('layouts.app')
@section('title', isset($permanent) ? 'Edit Permanent Fruit' : 'Tambah Permanent Fruit')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ isset($permanent) ? route('bloxfruit.permanents.update', $permanent) : route('bloxfruit.permanents.store') }}">
        @csrf
        @if(isset($permanent)) @method('PUT') @endif

        <x-form-card class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <x-form-label required>Nama</x-form-label>
                    <x-form-input name="nama" :value="$permanent->nama ?? ''" placeholder="Contoh: Perm Dragon" required />
                </div>
                <div class="sm:col-span-2">
                    <x-form-label>Harga Robux (R$)</x-form-label>
                    <x-form-input type="number" name="harga_robux" :value="$permanent->harga_robux ?? 0" min="0" />
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">Harga permanent di game</p>
                </div>
                <div>
                    <x-form-label>Harga Beli (IDR)</x-form-label>
                    <x-form-input type="number" name="harga_beli" :value="$permanent->harga_beli ?? 0" min="0" />
                </div>
                <div>
                    <x-form-label>Harga Jual (IDR)</x-form-label>
                    <x-form-input type="number" name="harga_jual" :value="$permanent->harga_jual ?? 0" min="0" />
                </div>
            </div>
            <div class="flex items-center gap-2 pt-1">
                <x-btn type="submit" variant="primary" size="lg">{{ isset($permanent) ? 'Perbarui' : 'Simpan' }}</x-btn>
                <x-btn :href="route('bloxfruit.permanents.index')" variant="secondary" size="lg">Batal</x-btn>
            </div>
        </x-form-card>
    </form>
</div>
@endsection
