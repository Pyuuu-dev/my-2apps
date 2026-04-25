@extends('layouts.app')
@section('title', 'Pengaturan Akun')

@section('content')
<div class="max-w-2xl">
    {{-- Profil --}}
    <div class="glass-card rounded-2xl p-6 mb-6">
        <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Profil</h3>
        <form method="POST" action="{{ route('settings.profile') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                    <input type="text" name="name" value="{{ auth()->user()->name }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" name="username" value="{{ auth()->user()->username }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
            </div>
            <button type="submit" class="rounded-lg bg-indigo-600 px-6 py-2 text-sm font-medium text-white hover:bg-indigo-700">Simpan Profil</button>
        </form>
    </div>

    {{-- Ganti Password --}}
    <div class="glass-card rounded-2xl p-6">
        <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Ganti Password</h3>
        <form method="POST" action="{{ route('settings.password') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Lama</label>
                <input type="password" name="current_password" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                    <input type="password" name="password" required minlength="6" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
            </div>
            <button type="submit" class="rounded-lg bg-red-600 px-6 py-2 text-sm font-medium text-white hover:bg-red-700">Ganti Password</button>
        </form>
    </div>
</div>
@endsection
