@extends('layouts.app')
@section('title', isset($fruit) ? 'Edit Buah' : 'Tambah Buah')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ isset($fruit) ? route('bloxfruit.fruits.update', $fruit) : route('bloxfruit.fruits.store') }}">
        @csrf
        @if(isset($fruit)) @method('PUT') @endif

        <x-form-card class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-form-label required>Nama Buah</x-form-label>
                    <x-form-input name="nama" :value="$fruit->nama ?? ''" required />
                </div>
                <div>
                    <x-form-label required>Tipe</x-form-label>
                    <x-form-select name="tipe" required :value="$fruit->tipe ?? ''"
                        :options="collect(['Natural','Elemental','Beast'])->mapWithKeys(fn($t) => [$t => $t])->all()" />
                </div>
                <div>
                    <x-form-label required>Rarity</x-form-label>
                    <x-form-select name="rarity" required :value="$fruit->rarity ?? ''"
                        :options="collect(['Common','Uncommon','Rare','Legendary','Mythical'])->mapWithKeys(fn($r) => [$r => $r])->all()" />
                </div>
                <div>
                    <x-form-label>Harga Beli</x-form-label>
                    <x-form-input type="number" name="harga_beli" :value="$fruit->harga_beli ?? 0" min="0" />
                </div>
                <div class="sm:col-span-2">
                    <x-form-label>Harga Jual</x-form-label>
                    <x-form-input type="number" name="harga_jual" :value="$fruit->harga_jual ?? 0" min="0" />
                </div>
            </div>
            <div>
                <x-form-label>Keterangan</x-form-label>
                <x-form-textarea name="keterangan" rows="3" :value="$fruit->keterangan ?? ''" />
            </div>
            <p class="text-xs text-gray-400 dark:text-gray-500">* Stok dikelola melalui menu Akun Storage</p>
            <div class="flex items-center gap-2 pt-1">
                <x-btn type="submit" variant="primary" size="lg">{{ isset($fruit) ? 'Perbarui' : 'Simpan' }}</x-btn>
                <x-btn :href="route('bloxfruit.fruits.index')" variant="secondary" size="lg">Batal</x-btn>
            </div>
        </x-form-card>
    </form>
</div>
@endsection
