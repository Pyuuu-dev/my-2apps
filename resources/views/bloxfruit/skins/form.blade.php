@extends('layouts.app')
@section('title', isset($skin) ? 'Edit Skin' : 'Tambah Skin')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ isset($skin) ? route('bloxfruit.skins.update', $skin) : route('bloxfruit.skins.store') }}" class="space-y-6 rounded-xl bg-white p-6 shadow-sm border border-gray-100">
        @csrf
        @if(isset($skin)) @method('PUT') @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Buah *</label>
                <select name="blox_fruit_id" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">Pilih Buah</option>
                    @foreach($fruits as $f)
                    <option value="{{ $f->id }}" {{ old('blox_fruit_id', $skin->blox_fruit_id ?? '') == $f->id ? 'selected' : '' }}>{{ $f->nama }} ({{ $f->rarity }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Skin *</label>
                <input type="text" name="nama_skin" value="{{ old('nama_skin', $skin->nama_skin ?? '') }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Beli</label>
                <input type="number" name="harga_beli" value="{{ old('harga_beli', $skin->harga_beli ?? 0) }}" min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual</label>
                <input type="number" name="harga_jual" value="{{ old('harga_jual', $skin->harga_jual ?? 0) }}" min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
            <textarea name="keterangan" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('keterangan', $skin->keterangan ?? '') }}</textarea>
        </div>
        <p class="text-xs text-gray-400">* Stok dikelola melalui menu Akun Storage</p>
        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-lg bg-indigo-600 px-6 py-2 text-sm font-medium text-white hover:bg-indigo-700">{{ isset($skin) ? 'Perbarui' : 'Simpan' }}</button>
            <a href="{{ route('bloxfruit.skins.index') }}" class="rounded-lg bg-gray-100 px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Batal</a>
        </div>
    </form>
</div>
@endsection
