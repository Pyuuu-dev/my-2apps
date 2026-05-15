@extends('layouts.app')
@section('title', isset($skin) ? 'Edit Skin' : 'Tambah Skin')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ isset($skin) ? route('bloxfruit.skins.update', $skin) : route('bloxfruit.skins.store') }}">
        @csrf
        @if(isset($skin)) @method('PUT') @endif

        <x-form-card class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-form-label required>Buah</x-form-label>
                    <x-form-select name="blox_fruit_id" required placeholder="Pilih Buah" :value="$skin->blox_fruit_id ?? ''"
                        :options="$fruits->mapWithKeys(fn($f) => [$f->id => $f->nama . ' (' . $f->rarity . ')'])->all()" />
                </div>
                <div>
                    <x-form-label required>Nama Skin</x-form-label>
                    <x-form-input name="nama_skin" :value="$skin->nama_skin ?? ''" required />
                </div>
                <div>
                    <x-form-label>Harga Beli</x-form-label>
                    <x-form-input type="number" name="harga_beli" :value="$skin->harga_beli ?? 0" min="0" />
                </div>
                <div>
                    <x-form-label>Harga Jual</x-form-label>
                    <x-form-input type="number" name="harga_jual" :value="$skin->harga_jual ?? 0" min="0" />
                </div>
            </div>
            <div>
                <x-form-label>Keterangan</x-form-label>
                <x-form-textarea name="keterangan" rows="3" :value="$skin->keterangan ?? ''" />
            </div>
            <p class="text-xs text-gray-400 dark:text-gray-500">* Stok dikelola melalui menu Akun Storage</p>
            <div class="flex items-center gap-2 pt-1">
                <x-btn type="submit" variant="primary" size="lg">{{ isset($skin) ? 'Perbarui' : 'Simpan' }}</x-btn>
                <x-btn :href="route('bloxfruit.skins.index')" variant="secondary" size="lg">Batal</x-btn>
            </div>
        </x-form-card>
    </form>
</div>
@endsection
