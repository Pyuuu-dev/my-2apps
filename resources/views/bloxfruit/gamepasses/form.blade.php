@extends('layouts.app')
@section('title', isset($gamepass) ? 'Edit Gamepass' : 'Tambah Gamepass')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ isset($gamepass) ? route('bloxfruit.gamepasses.update', $gamepass) : route('bloxfruit.gamepasses.store') }}" class="space-y-6 rounded-xl bg-white dark:bg-slate-800 p-6 shadow-sm border border-gray-100 dark:border-slate-700">
        @csrf
        @if(isset($gamepass)) @method('PUT') @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Gamepass *</label>
                <input type="text" name="nama" value="{{ old('nama', $gamepass->nama ?? '') }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Robux (R$)</label>
                <input type="number" name="harga_robux" value="{{ old('harga_robux', $gamepass->harga_robux ?? 0) }}" min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">&nbsp;</label>
                <p class="text-xs text-gray-400 mt-2">Harga dalam Robux di game</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Beli (IDR)</label>
                <input type="number" name="harga_beli" value="{{ old('harga_beli', $gamepass->harga_beli ?? 0) }}" min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual (IDR)</label>
                <input type="number" name="harga_jual" value="{{ old('harga_jual', $gamepass->harga_jual ?? 0) }}" min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
            <textarea name="deskripsi" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('deskripsi', $gamepass->deskripsi ?? '') }}</textarea>
        </div>
        <p class="text-xs text-gray-400">* Stok dikelola melalui menu Akun Storage</p>
        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-lg bg-indigo-600 px-6 py-2 text-sm font-medium text-white hover:bg-indigo-700">{{ isset($gamepass) ? 'Perbarui' : 'Simpan' }}</button>
            <a href="{{ route('bloxfruit.gamepasses.index') }}" class="rounded-lg bg-gray-100 px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Batal</a>
        </div>
    </form>
</div>
@endsection
