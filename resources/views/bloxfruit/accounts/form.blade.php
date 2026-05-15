@extends('layouts.app')
@section('title', isset($account) ? 'Edit Akun' : 'Tambah Akun')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ isset($account) ? route('bloxfruit.accounts.update', $account) : route('bloxfruit.accounts.store') }}"
        x-data="{ showPass: false }">
        @csrf
        @if(isset($account)) @method('PUT') @endif

        <x-form-card class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-form-label required>Username Roblox</x-form-label>
                    <x-form-input name="username_roblox" :value="$account->username_roblox ?? ''" required />
                </div>
                <div>
                    <x-form-label>Password Roblox</x-form-label>
                    <div class="relative">
                        <input
                            :type="showPass ? 'text' : 'password'"
                            name="password_roblox"
                            value="{{ old('password_roblox', $account->password_roblox ?? '') }}"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-gray-100 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pr-9">
                        <button type="button" @click="showPass = !showPass" class="absolute right-2 top-1/2 -translate-y-1/2 p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <svg x-show="!showPass" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="showPass" x-cloak class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <div>
                <x-form-label>Sword / Gun</x-form-label>
                <x-form-input name="sword_gun" :value="$account->sword_gun ?? ''" placeholder="CDK, GH, SG, Shark Anchor" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-form-label>Fruit</x-form-label>
                    <x-form-input name="fruit" :value="$account->fruit ?? ''" placeholder="Yeti, Buddha" />
                </div>
                <div>
                    <x-form-label>Race</x-form-label>
                    <x-form-input name="race" :value="$account->race ?? ''" placeholder="v3 Human" />
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                <div>
                    <x-form-label>Belly</x-form-label>
                    <x-form-input name="belly" :value="$account->belly ?? ''" placeholder="17m" />
                </div>
                <div>
                    <x-form-label>Fragment</x-form-label>
                    <x-form-input name="fragment" :value="$account->fragment ?? ''" placeholder="34.9k" />
                </div>
                <div>
                    <x-form-label>Level</x-form-label>
                    <x-form-input name="level" :value="$account->level ?? ''" placeholder="2800" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <x-form-label>Harga Beli (Rp)</x-form-label>
                    <x-form-input type="number" name="harga_beli" :value="$account->harga_beli ?? 0" min="0" />
                </div>
                <div>
                    <x-form-label>Harga Jual (Rp)</x-form-label>
                    <x-form-input type="number" name="harga_jual" :value="$account->harga_jual ?? 0" min="0" />
                </div>
                <div>
                    <x-form-label required>Status</x-form-label>
                    <x-form-select name="status" required :value="$account->status ?? 'tersedia'"
                        :options="['tersedia' => 'Tersedia', 'terjual' => 'Terjual', 'pending' => 'Pending']" />
                </div>
            </div>

            <div>
                <x-form-label>Keterangan</x-form-label>
                <x-form-textarea name="keterangan" rows="2" :value="$account->keterangan ?? ''" />
            </div>

            <div class="flex items-center gap-2 pt-1">
                <x-btn type="submit" variant="primary" size="lg">{{ isset($account) ? 'Perbarui' : 'Simpan' }}</x-btn>
                <x-btn :href="route('bloxfruit.accounts.index')" variant="secondary" size="lg">Batal</x-btn>
            </div>
        </x-form-card>
    </form>
</div>
@endsection
