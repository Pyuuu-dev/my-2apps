@extends('layouts.app')
@section('title', 'Stok Akun')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-2">
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari akun..." class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <select name="status" class="rounded-lg border-gray-300 text-sm shadow-sm">
            <option value="">Semua Status</option>
            @foreach(['tersedia','terjual','pending'] as $s)
            <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Filter</button>
    </form>
    <a href="{{ route('bloxfruit.accounts.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Akun
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($accounts as $akun)
    <div class="rounded-xl bg-white p-4 shadow-sm border border-gray-100">
        <div class="flex items-start justify-between mb-3">
            <div>
                <h3 class="font-semibold text-gray-900">{{ $akun->judul }}</h3>
                <p class="text-sm text-gray-500">Level {{ $akun->level ?? '-' }}</p>
            </div>
            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium
                {{ $akun->status === 'tersedia' ? 'bg-green-100 text-green-700' : ($akun->status === 'terjual' ? 'bg-gray-100 text-gray-700' : 'bg-yellow-100 text-yellow-700') }}">
                {{ ucfirst($akun->status) }}
            </span>
        </div>
        @if($akun->daftar_buah)
        <p class="text-xs text-gray-500 mb-1"><span class="font-medium">Buah:</span> {{ $akun->daftar_buah }}</p>
        @endif
        @if($akun->daftar_gamepass)
        <p class="text-xs text-gray-500 mb-3"><span class="font-medium">Gamepass:</span> {{ $akun->daftar_gamepass }}</p>
        @endif
        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
            <p class="text-lg font-bold text-indigo-600">Rp {{ number_format($akun->harga, 0, ',', '.') }}</p>
            <div class="flex gap-2">
                <a href="{{ route('bloxfruit.accounts.edit', $akun) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">Edit</a>
                <form method="POST" action="{{ route('bloxfruit.accounts.destroy', $akun) }}" onsubmit="return confirm('Yakin hapus?')">
                    @csrf @method('DELETE')
                    <button class="text-red-600 hover:text-red-800 text-sm">Hapus</button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full py-8 text-center text-sm text-gray-400">Belum ada stok akun</div>
    @endforelse
</div>
<div class="mt-4">{{ $accounts->links() }}</div>
@endsection
