@extends('layouts.app')
@section('title', isset($fruit) ? 'Edit Buah' : 'Tambah Buah')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ isset($fruit) ? route('bloxfruit.fruits.update', $fruit) : route('bloxfruit.fruits.store') }}" class="space-y-6 rounded-xl bg-white p-6 shadow-sm border border-gray-100">
        @csrf
        @if(isset($fruit)) @method('PUT') @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Buah *</label>
                <input type="text" name="nama" value="{{ old('nama', $fruit->nama ?? '') }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe *</label>
                <select name="tipe" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    @foreach(['Natural','Elemental','Beast'] as $t)
                    <option value="{{ $t }}" {{ old('tipe', $fruit->tipe ?? '') == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rarity *</label>
                <select name="rarity" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    @foreach(['Common','Uncommon','Rare','Legendary','Mythical'] as $r)
                    <option value="{{ $r }}" {{ old('rarity', $fruit->rarity ?? '') == $r ? 'selected' : '' }}>{{ $r }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Beli</label>
                <input type="number" name="harga_beli" value="{{ old('harga_beli', $fruit->harga_beli ?? 0) }}" min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual</label>
                <input type="number" name="harga_jual" value="{{ old('harga_jual', $fruit->harga_jual ?? 0) }}" min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
            <textarea name="keterangan" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('keterangan', $fruit->keterangan ?? '') }}</textarea>
        </div>
        <p class="text-xs text-gray-400">* Stok dikelola melalui menu Akun Storage</p>
        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-lg bg-indigo-600 px-6 py-2 text-sm font-medium text-white hover:bg-indigo-700">{{ isset($fruit) ? 'Perbarui' : 'Simpan' }}</button>
            <a href="{{ route('bloxfruit.fruits.index') }}" class="rounded-lg bg-gray-100 px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Batal</a>
        </div>
    </form>
</div>
@endsection
