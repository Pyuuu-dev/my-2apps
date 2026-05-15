@extends('layouts.app')
@section('title', isset($service) ? 'Edit Jenis Joki' : 'Tambah Jenis Joki')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ isset($service) ? route('bloxfruit.joki-services.update', $service) : route('bloxfruit.joki-services.store') }}">
        @csrf
        @if(isset($service)) @method('PUT') @endif

        <x-form-card class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-form-label required>Kategori</x-form-label>
                    <x-form-select name="kategori" required :value="$service->kategori ?? ''" :options="$kategoriOptions" />
                </div>
                <div>
                    <x-form-label required>Harga (Rp)</x-form-label>
                    <x-form-input type="number" name="harga" :value="$service->harga ?? 0" min="0" required />
                </div>
                <div class="sm:col-span-2">
                    <x-form-label required>Nama Jenis Joki</x-form-label>
                    <x-form-input name="nama" :value="$service->nama ?? ''" required placeholder="Contoh: God Human" />
                </div>
                <div class="sm:col-span-2">
                    <x-form-label>Keterangan <span class="text-xs text-gray-400 dark:text-gray-500 font-normal">- opsional</span></x-form-label>
                    <x-form-input name="keterangan" :value="$service->keterangan ?? ''" placeholder="Contoh: Full, per 1, dll" />
                </div>
            </div>
            <div class="flex items-center gap-2 pt-1">
                <x-btn type="submit" variant="primary" size="lg">{{ isset($service) ? 'Perbarui' : 'Simpan' }}</x-btn>
                <x-btn :href="route('bloxfruit.joki-services.index')" variant="secondary" size="lg">Batal</x-btn>
            </div>
        </x-form-card>
    </form>
</div>
@endsection
