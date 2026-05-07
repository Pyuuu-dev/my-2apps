@extends('layouts.app')
@section('title', 'Database Makanan')

@section('content')
<div class="space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Database Makanan</h2>
            <p class="text-sm text-gray-500">{{ $foods->total() }} makanan tersimpan</p>
        </div>
        <button x-data x-on:click="$dispatch('open-add-modal')" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">+ Tambah Makanan</button>
    </div>

    {{-- Search & Filter --}}
    <form method="GET" class="flex gap-2">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari makanan..." class="flex-1 rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-800 px-3 py-1.5 text-sm dark:text-gray-200">
        <select name="kategori" class="rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-800 px-3 py-1.5 text-sm dark:text-gray-200">
            <option value="">Semua Kategori</option>
            @foreach($kategoris as $k)
            <option value="{{ $k }}" {{ request('kategori') === $k ? 'selected' : '' }}>{{ ucfirst($k) }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">Cari</button>
    </form>

    {{-- Table --}}
    <div class="rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-gray-50 dark:bg-slate-700/50">
                    <tr>
                        <th class="px-3 py-2.5 text-left text-gray-500">Nama</th>
                        <th class="px-3 py-2.5 text-left text-gray-500">Kategori</th>
                        <th class="px-3 py-2.5 text-center text-gray-500">Kalori</th>
                        <th class="px-3 py-2.5 text-center text-gray-500">P</th>
                        <th class="px-3 py-2.5 text-center text-gray-500">K</th>
                        <th class="px-3 py-2.5 text-center text-gray-500">L</th>
                        <th class="px-3 py-2.5 text-left text-gray-500">Porsi</th>
                        <th class="px-3 py-2.5 text-center text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    @foreach($foods as $food)
                    <tr>
                        <td class="px-3 py-2 font-medium text-gray-800 dark:text-gray-200">{{ $food->nama }}</td>
                        <td class="px-3 py-2"><span class="px-1.5 py-0.5 rounded bg-gray-100 dark:bg-slate-700 text-[10px]">{{ $food->kategori ?? '-' }}</span></td>
                        <td class="px-3 py-2 text-center font-medium text-orange-600">{{ $food->kalori }}</td>
                        <td class="px-3 py-2 text-center text-gray-600 dark:text-gray-400">{{ $food->protein }}</td>
                        <td class="px-3 py-2 text-center text-gray-600 dark:text-gray-400">{{ $food->karbohidrat }}</td>
                        <td class="px-3 py-2 text-center text-gray-600 dark:text-gray-400">{{ $food->lemak }}</td>
                        <td class="px-3 py-2 text-gray-500">{{ $food->satuan_porsi }}</td>
                        <td class="px-3 py-2 text-center">
                            <form method="POST" action="{{ route('diet.food-db.destroy', $food) }}" onsubmit="return confirm('Hapus?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-[10px]">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($foods->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 dark:border-slate-700">{{ $foods->withQueryString()->links() }}</div>
        @endif
    </div>

    {{-- Add Modal --}}
    <div x-data="{ open: false }" x-on:open-add-modal.window="open = true" x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50" x-on:click="open = false"></div>
        <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-md p-5 z-10">
            <h3 class="text-sm font-bold text-gray-800 dark:text-white mb-4">Tambah Makanan</h3>
            <form method="POST" action="{{ route('diet.food-db.store') }}" class="space-y-3">
                @csrf
                <input type="text" name="nama" placeholder="Nama makanan" required class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 px-3 py-2 text-sm dark:text-gray-200">
                <input type="text" name="kategori" placeholder="Kategori (nasi, lauk, dll)" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 px-3 py-2 text-sm dark:text-gray-200">
                <div class="grid grid-cols-2 gap-2">
                    <input type="number" name="kalori" placeholder="Kalori" required class="rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 px-3 py-2 text-sm dark:text-gray-200">
                    <input type="number" name="protein" placeholder="Protein (g)" required step="0.1" class="rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 px-3 py-2 text-sm dark:text-gray-200">
                    <input type="number" name="karbohidrat" placeholder="Karbo (g)" required step="0.1" class="rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 px-3 py-2 text-sm dark:text-gray-200">
                    <input type="number" name="lemak" placeholder="Lemak (g)" required step="0.1" class="rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 px-3 py-2 text-sm dark:text-gray-200">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <input type="text" name="satuan_porsi" placeholder="Satuan (1 porsi)" required class="rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 px-3 py-2 text-sm dark:text-gray-200">
                    <input type="number" name="berat_gram" placeholder="Berat (gram)" class="rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 px-3 py-2 text-sm dark:text-gray-200">
                </div>
                <div class="flex gap-2 pt-2">
                    <button type="submit" class="flex-1 rounded-lg bg-emerald-600 py-2 text-sm font-medium text-white hover:bg-emerald-700">Simpan</button>
                    <button type="button" x-on:click="open = false" class="px-4 rounded-lg border border-gray-300 dark:border-slate-600 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
