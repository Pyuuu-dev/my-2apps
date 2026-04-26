@extends('layouts.app')
@section('title', isset($account) ? 'Edit Akun' : 'Tambah Akun')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ isset($account) ? route('bloxfruit.accounts.update', $account) : route('bloxfruit.accounts.store') }}" class="space-y-5 glass-card rounded-2xl p-6">
        @csrf
        @if(isset($account)) @method('PUT') @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Username Roblox *</label>
                <input type="text" name="username_roblox" value="{{ old('username_roblox', $account->username_roblox ?? '') }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Roblox</label>
                <input type="text" name="password_roblox" value="{{ old('password_roblox', $account->password_roblox ?? '') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sword / Gun</label>
            <input type="text" name="sword_gun" value="{{ old('sword_gun', $account->sword_gun ?? '') }}" placeholder="CDK, GH, SG, Shark Anchor" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fruit</label>
                <input type="text" name="fruit" value="{{ old('fruit', $account->fruit ?? '') }}" placeholder="Yeti, Buddha" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Race</label>
                <input type="text" name="race" value="{{ old('race', $account->race ?? '') }}" placeholder="v3 Human" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Belly</label>
                <input type="text" name="belly" value="{{ old('belly', $account->belly ?? '') }}" placeholder="17m" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fragment</label>
                <input type="text" name="fragment" value="{{ old('fragment', $account->fragment ?? '') }}" placeholder="34.9k" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Level</label>
                <input type="text" name="level" value="{{ old('level', $account->level ?? '') }}" placeholder="2800" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Beli (Rp)</label>
                <input type="number" name="harga_beli" value="{{ old('harga_beli', $account->harga_beli ?? 0) }}" min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual (Rp)</label>
                <input type="number" name="harga_jual" value="{{ old('harga_jual', $account->harga_jual ?? 0) }}" min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                <select name="status" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    @foreach(['tersedia' => 'Tersedia', 'terjual' => 'Terjual', 'pending' => 'Pending'] as $val => $label)
                    <option value="{{ $val }}" {{ old('status', $account->status ?? 'tersedia') == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
            <textarea name="keterangan" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('keterangan', $account->keterangan ?? '') }}</textarea>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-lg bg-indigo-600 px-6 py-2 text-sm font-medium text-white hover:bg-indigo-700">{{ isset($account) ? 'Perbarui' : 'Simpan' }}</button>
            <a href="{{ route('bloxfruit.accounts.index') }}" class="rounded-lg bg-gray-100 px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Batal</a>
        </div>
    </form>
</div>
@endsection
