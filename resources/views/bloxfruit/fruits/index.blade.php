@extends('layouts.app')
@section('title', 'Daftar Buah')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-2">
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari buah..." class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <select name="tipe" class="rounded-lg border-gray-300 text-sm shadow-sm">
            <option value="">Semua Tipe</option>
            @foreach(['Natural','Elemental','Beast'] as $t)
            <option value="{{ $t }}" {{ request('tipe') == $t ? 'selected' : '' }}>{{ $t }}</option>
            @endforeach
        </select>
        <select name="rarity" class="rounded-lg border-gray-300 text-sm shadow-sm">
            <option value="">Semua Rarity</option>
            @foreach(['Common','Uncommon','Rare','Legendary','Mythical'] as $r)
            <option value="{{ $r }}" {{ request('rarity') == $r ? 'selected' : '' }}>{{ $r }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Filter</button>
    </form>
    <a href="{{ route('bloxfruit.fruits.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Buah
    </a>
</div>

<div class="overflow-x-auto rounded-xl bg-white shadow-sm border border-gray-100">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Nama</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Tipe</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Rarity</th>
                <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">Harga Beli</th>
                <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">Harga Jual</th>
                <th class="px-4 py-3 text-center text-xs font-medium uppercase text-gray-500">Total Stok</th>
                <th class="px-4 py-3 text-center text-xs font-medium uppercase text-gray-500">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($fruits as $fruit)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $fruit->nama }}</td>
                <td class="px-4 py-3 text-sm text-gray-600">{{ $fruit->tipe }}</td>
                <td class="px-4 py-3">
                    <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium
                        {{ $fruit->rarity === 'Mythical' ? 'bg-red-100 text-red-700' : ($fruit->rarity === 'Legendary' ? 'bg-yellow-100 text-yellow-700' : ($fruit->rarity === 'Rare' ? 'bg-blue-100 text-blue-700' : ($fruit->rarity === 'Uncommon' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'))) }}">
                        {{ $fruit->rarity }}
                    </span>
                </td>
                <td class="px-4 py-3 text-sm text-right text-gray-600">{{ number_format($fruit->harga_beli, 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-sm text-right text-gray-600">{{ number_format($fruit->harga_jual, 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-sm text-center">
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ ($fruit->total_stok ?? 0) > 0 ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ $fruit->total_stok ?? 0 }}</span>
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('bloxfruit.fruits.edit', $fruit) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">Edit</a>
                        <form method="POST" action="{{ route('bloxfruit.fruits.destroy', $fruit) }}" onsubmit="return confirm('Yakin hapus?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:text-red-800 text-sm">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-sm text-gray-400">Belum ada data buah</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $fruits->links() }}</div>
@endsection
