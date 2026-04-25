@extends('layouts.app')
@section('title', isset($account) ? 'Edit Akun' : 'Tambah Akun')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ isset($account) ? route('bloxfruit.accounts.update', $account) : route('bloxfruit.accounts.store') }}" class="space-y-6 rounded-xl bg-white p-6 shadow-sm border border-gray-100">
        @csrf
        @if(isset($account)) @method('PUT') @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Akun *</label>
                <input type="text" name="judul" value="{{ old('judul', $account->judul ?? '') }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Level</label>
                <input type="text" name="level" value="{{ old('level', $account->level ?? '') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga (IDR)</label>
                <input type="number" name="harga" value="{{ old('harga', $account->harga ?? 0) }}" min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                <select name="status" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    @foreach(['tersedia','terjual','pending'] as $s)
                    <option value="{{ $s }}" {{ old('status', $account->status ?? 'tersedia') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Daftar Buah</label>
            <textarea name="daftar_buah" rows="2" placeholder="Contoh: Leopard, Dragon, Venom" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('daftar_buah', $account->daftar_buah ?? '') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Daftar Gamepass</label>
            <textarea name="daftar_gamepass" rows="2" placeholder="Contoh: 2x Mastery, Fruit Storage" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('daftar_gamepass', $account->daftar_gamepass ?? '') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
            <textarea name="keterangan" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('keterangan', $account->keterangan ?? '') }}</textarea>
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-lg bg-indigo-600 px-6 py-2 text-sm font-medium text-white hover:bg-indigo-700">{{ isset($account) ? 'Perbarui' : 'Simpan' }}</button>
            <a href="{{ route('bloxfruit.accounts.index') }}" class="rounded-lg bg-gray-100 px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Batal</a>
        </div>
    </form>
</div>
@endsection
