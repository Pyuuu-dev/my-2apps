@extends('layouts.app')
@section('title', 'Pengaturan Akun')

@section('content')
<div class="max-w-2xl space-y-6">
    {{-- Profil --}}
    <x-form-card>
        <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Profil</h3>
        <form method="POST" action="{{ route('settings.profile') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-form-label required>Nama</x-form-label>
                    <x-form-input name="name" :value="auth()->user()->name" required />
                </div>
                <div>
                    <x-form-label required>Username</x-form-label>
                    <x-form-input name="username" :value="auth()->user()->username" required />
                </div>
            </div>
            <x-btn type="submit" variant="primary" size="lg">Simpan Profil</x-btn>
        </form>
    </x-form-card>

    {{-- Ganti Password --}}
    <x-form-card>
        <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Ganti Password</h3>
        <form method="POST" action="{{ route('settings.password') }}" class="space-y-4"
            onsubmit="return confirm('Yakin mau ganti password?')">
            @csrf
            <div>
                <x-form-label required>Password Lama</x-form-label>
                <x-form-input type="password" name="current_password" required />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-form-label required>Password Baru</x-form-label>
                    <x-form-input type="password" name="password" required minlength="6" />
                </div>
                <div>
                    <x-form-label required>Konfirmasi Password</x-form-label>
                    <x-form-input type="password" name="password_confirmation" required />
                </div>
            </div>
            <x-btn type="submit" variant="danger" size="lg">Ganti Password</x-btn>
        </form>
    </x-form-card>

    {{-- Backup Database --}}
    <x-form-card x-data="{ loading: false }">
        <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Backup Database</h3>
        <div class="space-y-3">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Backup otomatis berjalan 4x sehari (02:00, 08:00, 14:00, 20:00) dan dikirim ke Telegram bot backup.
            </p>
            <form method="POST" action="{{ route('settings.backup') }}" @submit="loading = true">
                @csrf
                <button
                    type="submit"
                    :disabled="loading"
                    class="inline-flex items-center justify-center gap-1.5 px-5 py-2.5 text-sm font-semibold rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm shadow-emerald-500/20 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                    <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                    </svg>
                    <svg x-show="loading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="loading ? 'Memproses...' : 'Backup Manual Sekarang'"></span>
                </button>
            </form>
        </div>
    </x-form-card>
</div>
@endsection
