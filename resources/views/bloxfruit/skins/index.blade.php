@extends('layouts.app')
@section('title', 'Skin Buah')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-2">
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari skin..." class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <select name="fruit" class="rounded-lg border-gray-300 text-sm shadow-sm">
            <option value="">Semua Buah</option>
            @foreach($fruits as $f)
            <option value="{{ $f->id }}" {{ request('fruit') == $f->id ? 'selected' : '' }}>{{ $f->nama }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Filter</button>
    </form>
    <a href="{{ route('bloxfruit.skins.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Skin
    </a>
</div>

<div class="table-container overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Nama Skin</th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Buah</th>
                <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">Harga Beli</th>
                <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">Harga Jual</th>
                <th class="px-4 py-3 text-center text-xs font-medium uppercase text-gray-500">Total Stok</th>
                <th class="px-4 py-3 text-center text-xs font-medium uppercase text-gray-500">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($skins as $skin)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $skin->nama_skin }}</td>
                <td class="px-4 py-3 text-sm text-gray-600">
                    {{ $skin->fruit->nama ?? '-' }}
                    <span class="inline-flex rounded-full px-1.5 py-0.5 text-[11px] font-medium {{ $skin->fruit->rarity === 'Mythical' ? 'bg-red-100 text-red-700' : ($skin->fruit->rarity === 'Legendary' ? 'bg-yellow-100 text-yellow-700' : ($skin->fruit->rarity === 'Rare' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600')) }}">{{ $skin->fruit->rarity ?? '' }}</span>
                </td>
                <td class="px-4 py-3 text-sm text-right text-gray-600">{{ number_format($skin->harga_beli, 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900">{{ number_format($skin->harga_jual, 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-sm text-center">
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ ($skin->total_stok ?? 0) > 0 ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ $skin->total_stok ?? 0 }}</span>
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('bloxfruit.skins.edit', $skin) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">Edit</a>
                        <form method="POST" action="{{ route('bloxfruit.skins.destroy', $skin) }}" onsubmit="return confirm('Yakin hapus?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:text-red-800 text-sm">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400">Belum ada data skin</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $skins->links() }}</div>
@endsection
