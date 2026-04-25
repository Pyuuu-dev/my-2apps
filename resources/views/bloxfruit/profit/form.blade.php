@extends('layouts.app')
@section('title', isset($profit) ? 'Edit Transaksi' : 'Catat Transaksi')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ isset($profit) ? route('bloxfruit.profit.update', $profit) : route('bloxfruit.profit.store') }}" class="space-y-5 rounded-xl bg-white dark:bg-slate-800 p-6 shadow-sm border border-gray-100 dark:border-slate-700"
        x-data="{
            modal: {{ old('modal', $profit->modal ?? 0) }},
            pendapatan: {{ old('pendapatan', $profit->pendapatan ?? 0) }},
            get untung() { return this.pendapatan - this.modal; }
        }">
        @csrf
        @if(isset($profit)) @method('PUT') @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal *</label>
                <input type="date" name="tanggal" value="{{ old('tanggal', isset($profit) ? $profit->tanggal->format('Y-m-d') : date('Y-m-d')) }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori *</label>
                <select name="kategori" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    @foreach(['fruit' => 'Fruit', 'skin' => 'Skin', 'gamepass' => 'Gamepass', 'permanent' => 'Permanent', 'joki' => 'Joki', 'lainnya' => 'Lainnya'] as $k => $v)
                    <option value="{{ $k }}" {{ old('kategori', $profit->kategori ?? '') == $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan <span class="text-xs text-gray-400">- opsional</span></label>
                <input type="text" name="keterangan" value="{{ old('keterangan', $profit->keterangan ?? '') }}" placeholder="Contoh: Jual Perm Dragon ke @buyer123" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Modal (Rp) <span class="text-xs text-gray-400">- opsional</span></label>
                <input type="number" name="modal" x-model.number="modal" value="{{ old('modal', $profit->modal ?? 0) }}" min="0" placeholder="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pendapatan (Rp) <span class="text-xs text-gray-400">- opsional</span></label>
                <input type="number" name="pendapatan" x-model.number="pendapatan" value="{{ old('pendapatan', $profit->pendapatan ?? 0) }}" min="0" placeholder="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
        </div>

        {{-- Preview Keuntungan --}}
        <div class="rounded-xl p-3 text-center" :class="untung >= 0 ? 'bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800' : 'bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800'">
            <p class="text-[11px] text-gray-500">Keuntungan</p>
            <p class="text-2xl font-extrabold" :class="untung >= 0 ? 'text-emerald-600' : 'text-red-600'" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(untung)"></p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Metode Bayar <span class="text-xs text-gray-400">- opsional</span></label>
            <select name="metode_bayar" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                <option value="">-- Pilih --</option>
                @foreach(['dana' => 'Dana', 'gopay' => 'GoPay', 'shopeepay' => 'ShopeePay', 'seabank' => 'SeaBank', 'bank_kalsel' => 'Bank Kalsel', 'bri' => 'BRI', 'qris' => 'QRIS', 'cash' => 'Cash'] as $k => $v)
                <option value="{{ $k }}" {{ old('metode_bayar', $profit->metode_bayar ?? '') == $k ? 'selected' : '' }}>{{ $v }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-lg bg-indigo-600 px-6 py-2 text-sm font-medium text-white hover:bg-indigo-700">{{ isset($profit) ? 'Perbarui' : 'Simpan' }}</button>
            <a href="{{ route('bloxfruit.profit.index') }}" class="rounded-lg bg-gray-100 px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Batal</a>
        </div>
    </form>
</div>
@endsection
