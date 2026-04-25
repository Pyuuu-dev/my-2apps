@extends('layouts.app')
@section('title', isset($permanent) ? 'Edit Permanent Fruit' : 'Tambah Permanent Fruit')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ isset($permanent) ? route('bloxfruit.permanents.update', $permanent) : route('bloxfruit.permanents.store') }}" class="space-y-6 rounded-xl bg-white dark:bg-slate-800 p-6 shadow-sm border border-gray-100 dark:border-slate-700">
        @csrf
        @if(isset($permanent)) @method('PUT') @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama *</label>
                <input type="text" name="nama" value="{{ old('nama', $permanent->nama ?? '') }}" required placeholder="Contoh: Perm Dragon" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Robux (R$)</label>
                <input type="number" name="harga_robux" value="{{ old('harga_robux', $permanent->harga_robux ?? 0) }}" min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <p class="text-xs text-gray-400 mt-6">Harga permanent di game</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Beli (IDR)</label>
                <input type="number" name="harga_beli" value="{{ old('harga_beli', $permanent->harga_beli ?? 0) }}" min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual (IDR)</label>
                <input type="number" name="harga_jual" value="{{ old('harga_jual', $permanent->harga_jual ?? 0) }}" min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-lg bg-indigo-600 px-6 py-2 text-sm font-medium text-white hover:bg-indigo-700">{{ isset($permanent) ? 'Perbarui' : 'Simpan' }}</button>
            <a href="{{ route('bloxfruit.permanents.index') }}" class="rounded-lg bg-gray-100 px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Batal</a>
        </div>
    </form>
</div>
@endsection
