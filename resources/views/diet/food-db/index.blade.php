@extends('layouts.app')
@section('title', 'Database Makanan')

@section('content')
<div class="space-y-4"
    x-data="{
        editing: null,
        openEdit(food) {
            this.editing = food;
            this.$nextTick(() => document.body.style.overflow = 'hidden');
        },
        closeEdit() {
            this.editing = null;
            document.body.style.overflow = '';
        }
    }">

    <x-page-header title="Database Makanan" :subtitle="$foods->total() . ' makanan tersimpan'">
        <x-slot:actions>
            <x-btn x-on:click="$dispatch('open-add-modal')" variant="success" icon="M12 4v16m8-8H4">
                Tambah Makanan
            </x-btn>
        </x-slot:actions>
    </x-page-header>

    {{-- Search & Filter --}}
    <form method="GET" class="flex gap-2">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari makanan..." class="flex-1 rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-800 px-3 py-1.5 text-sm dark:text-gray-200">
        <select name="kategori" class="rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-800 px-3 py-1.5 text-sm dark:text-gray-200">
            <option value="">Semua Kategori</option>
            @foreach($kategoris as $k)
            <option value="{{ $k }}" @selected(request('kategori') === $k)>{{ ucfirst($k) }}</option>
            @endforeach
        </select>
        <x-btn type="submit" variant="primary" size="sm">Cari</x-btn>
    </form>

    {{-- Table --}}
    <div class="rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-gray-50 dark:bg-slate-700/50">
                    <tr>
                        <th class="px-3 py-2.5 text-left text-gray-500 dark:text-gray-400">Nama</th>
                        <th class="px-3 py-2.5 text-left text-gray-500 dark:text-gray-400">Kategori</th>
                        <th class="px-3 py-2.5 text-center text-gray-500 dark:text-gray-400">Kalori</th>
                        <th class="px-3 py-2.5 text-center text-gray-500 dark:text-gray-400">P</th>
                        <th class="px-3 py-2.5 text-center text-gray-500 dark:text-gray-400">K</th>
                        <th class="px-3 py-2.5 text-center text-gray-500 dark:text-gray-400">L</th>
                        <th class="px-3 py-2.5 text-left text-gray-500 dark:text-gray-400">Porsi</th>
                        <th class="px-3 py-2.5 text-center text-gray-500 dark:text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    @forelse($foods as $food)
                    @php
                        $editData = [
                            'id' => $food->id,
                            'nama' => $food->nama,
                            'kategori' => $food->kategori,
                            'kalori' => $food->kalori,
                            'protein' => $food->protein,
                            'karbohidrat' => $food->karbohidrat,
                            'lemak' => $food->lemak,
                            'satuan_porsi' => $food->satuan_porsi,
                            'berat_gram' => $food->berat_gram,
                            'url' => route('diet.food-db.update', $food),
                        ];
                    @endphp
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/30">
                        <td class="px-3 py-2 font-medium text-gray-800 dark:text-gray-200">{{ $food->nama }}</td>
                        <td class="px-3 py-2"><span class="px-1.5 py-0.5 rounded bg-gray-100 dark:bg-slate-700 text-[10px] dark:text-gray-300">{{ $food->kategori ?? '-' }}</span></td>
                        <td class="px-3 py-2 text-center font-medium text-orange-600 dark:text-orange-400">{{ $food->kalori }}</td>
                        <td class="px-3 py-2 text-center text-gray-600 dark:text-gray-400">{{ $food->protein }}</td>
                        <td class="px-3 py-2 text-center text-gray-600 dark:text-gray-400">{{ $food->karbohidrat }}</td>
                        <td class="px-3 py-2 text-center text-gray-600 dark:text-gray-400">{{ $food->lemak }}</td>
                        <td class="px-3 py-2 text-gray-500 dark:text-gray-400">{{ $food->satuan_porsi }}</td>
                        <td class="px-3 py-2 text-center whitespace-nowrap">
                            <div class="inline-flex items-center gap-2">
                                <button type="button"
                                    @click="openEdit({{ Js::from($editData) }})"
                                    class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 text-[10px] font-medium">Edit</button>
                                <x-confirm-form :action="route('diet.food-db.destroy', $food)" method="DELETE" :message="'Hapus ' . $food->nama . '?'">
                                    <button type="submit" class="text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 text-[10px] font-medium">Hapus</button>
                                </x-confirm-form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="py-10">
                        <x-empty-state icon="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" message="Belum ada makanan" />
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($foods->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 dark:border-slate-700">{{ $foods->withQueryString()->links() }}</div>
        @endif
    </div>

    {{-- Add Modal --}}
    <div x-data="{ open: false }"
        x-on:open-add-modal.window="open = true; document.body.style.overflow = 'hidden'"
        x-on:keydown.escape.window="if(open){ open = false; document.body.style.overflow = '' }"
        x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" x-on:click="open = false; document.body.style.overflow = ''"></div>
        <div x-show="open"
            x-transition:enter="transition duration-150 ease-out" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-md p-5 z-10 border border-gray-100 dark:border-slate-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-800 dark:text-white">Tambah Makanan</h3>
                <button type="button" @click="open = false; document.body.style.overflow = ''" class="rounded-lg p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('diet.food-db.store') }}" class="space-y-3">
                @csrf
                <x-form-input name="nama" placeholder="Nama makanan" required />
                <x-form-input name="kategori" placeholder="Kategori (nasi, lauk, dll)" />
                <div class="grid grid-cols-2 gap-2">
                    <x-form-input type="number" name="kalori" placeholder="Kalori" required />
                    <x-form-input type="number" name="protein" placeholder="Protein (g)" required step="0.1" />
                    <x-form-input type="number" name="karbohidrat" placeholder="Karbo (g)" required step="0.1" />
                    <x-form-input type="number" name="lemak" placeholder="Lemak (g)" required step="0.1" />
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <x-form-input name="satuan_porsi" placeholder="Satuan (1 porsi)" required />
                    <x-form-input type="number" name="berat_gram" placeholder="Berat (gram)" />
                </div>
                <div class="flex gap-2 pt-2">
                    <x-btn type="submit" variant="success" class="flex-1">Simpan</x-btn>
                    <x-btn type="button" x-on:click="open = false; document.body.style.overflow = ''" variant="secondary">Batal</x-btn>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div x-show="editing" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="closeEdit()"></div>
        <div x-show="editing"
            x-transition:enter="transition duration-150 ease-out" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-md p-5 z-10 border border-gray-100 dark:border-slate-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-800 dark:text-white">Edit Makanan <span class="text-xs font-normal text-gray-400" x-text="editing ? '— ' + editing.nama : ''"></span></h3>
                <button type="button" @click="closeEdit()" class="rounded-lg p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form :action="editing ? editing.url : ''" method="POST" class="space-y-3" x-show="editing">
                @csrf
                @method('PUT')
                <input type="text" name="nama" :value="editing?.nama ?? ''" placeholder="Nama makanan" required class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-gray-100 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <input type="text" name="kategori" :value="editing?.kategori ?? ''" placeholder="Kategori (nasi, lauk, dll)" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-gray-100 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <div class="grid grid-cols-2 gap-2">
                    <input type="number" name="kalori" :value="editing?.kalori ?? 0" placeholder="Kalori" required class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-gray-100 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <input type="number" name="protein" :value="editing?.protein ?? 0" placeholder="Protein (g)" required step="0.1" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-gray-100 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <input type="number" name="karbohidrat" :value="editing?.karbohidrat ?? 0" placeholder="Karbo (g)" required step="0.1" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-gray-100 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <input type="number" name="lemak" :value="editing?.lemak ?? 0" placeholder="Lemak (g)" required step="0.1" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-gray-100 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <input type="text" name="satuan_porsi" :value="editing?.satuan_porsi ?? ''" placeholder="Satuan (1 porsi)" required class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-gray-100 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <input type="number" name="berat_gram" :value="editing?.berat_gram ?? ''" placeholder="Berat (gram)" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-gray-100 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="flex gap-2 pt-2">
                    <x-btn type="submit" variant="primary" class="flex-1">Update</x-btn>
                    <x-btn type="button" @click="closeEdit()" variant="secondary">Batal</x-btn>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
