@extends('layouts.app')
@section('title', isset($category) ? 'Edit Kategori Joki' : 'Tambah Kategori Joki')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ isset($category) ? route('bloxfruit.joki-categories.update', $category) : route('bloxfruit.joki-categories.store') }}">
        @csrf
        @if(isset($category)) @method('PUT') @endif

        <x-form-card class="space-y-5">
            @if(isset($category) && ($isProtected ?? false))
            <div class="rounded-lg border border-amber-200 bg-amber-50 dark:bg-amber-900/20 dark:border-amber-800 px-3 py-2 text-xs text-amber-700 dark:text-amber-300">
                Kategori <span class="font-mono font-semibold">{{ $category->key }}</span> adalah kategori sistem.
                Key tidak bisa diubah dan kategori tidak bisa dinonaktifkan.
            </div>
            @endif

            @if(isset($category) && !($isProtected ?? false) && ($servicesCount ?? 0) > 0)
            <div class="rounded-lg border border-blue-200 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-800 px-3 py-2 text-xs text-blue-700 dark:text-blue-300">
                Kategori ini dipakai oleh <strong>{{ $servicesCount }}</strong> jenis joki.
                Kalau label diubah, key akan ikut di-rename otomatis dan jenis joki yang terkait tetap konsisten.
            </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <x-form-label required>Label Kategori</x-form-label>
                    <x-form-input name="label" :value="$category->label ?? ''" required placeholder="Contoh: Joki Level" />
                    <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">Nama tampilan kategori. Key internal akan di-generate otomatis dari label ini.</p>
                </div>
                <div>
                    <x-form-label>Icon</x-form-label>
                    <x-form-input name="icon" :value="$category->icon ?? ''" placeholder="⚔️" maxlength="10" />
                    <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">Emoji 1 karakter (opsional).</p>
                </div>
                <div>
                    <x-form-label required>Urutan</x-form-label>
                    <x-form-input type="number" name="urutan" :value="$category->urutan ?? ($nextUrutan ?? 99)" min="0" max="9999" required />
                    <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">Makin kecil makin atas.</p>
                </div>
                <div class="sm:col-span-2 flex items-end">
                    @if(isset($category) && ($isProtected ?? false))
                    <input type="hidden" name="aktif" value="1">
                    @else
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <input type="hidden" name="aktif" value="0">
                        <input type="checkbox" name="aktif" value="1"
                            @checked(old('aktif', isset($category) ? $category->aktif : true))
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        Aktif (tampil di form & landing page)
                    </label>
                    @endif
                </div>
            </div>

            @if(isset($category))
            <div class="border-t border-gray-100 dark:border-slate-700 pt-3">
                <p class="text-[11px] text-gray-500 dark:text-gray-400">
                    Key saat ini: <span class="font-mono px-1.5 py-0.5 rounded bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-gray-300">{{ $category->key }}</span>
                </p>
            </div>
            @endif

            <div class="flex items-center gap-2 pt-1">
                <x-btn type="submit" variant="primary" size="lg">{{ isset($category) ? 'Perbarui' : 'Simpan' }}</x-btn>
                <x-btn :href="route('bloxfruit.joki-categories.index')" variant="secondary" size="lg">Batal</x-btn>
            </div>
        </x-form-card>
    </form>
</div>
@endsection
