@extends('layouts.app')
@section('title', isset($profit) ? 'Edit Transaksi' : 'Catat Transaksi')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ isset($profit) ? route('bloxfruit.profit.update', $profit) : route('bloxfruit.profit.store') }}"
        x-data="{
            modal: {{ old('modal', $profit->modal ?? 0) }},
            pendapatan: {{ old('pendapatan', $profit->pendapatan ?? 0) }},
            get untung() { return this.pendapatan - this.modal; }
        }">
        @csrf
        @if(isset($profit)) @method('PUT') @endif

        <x-form-card class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-form-label required>Tanggal</x-form-label>
                    <x-form-input type="date" name="tanggal" :value="isset($profit) ? $profit->tanggal->format('Y-m-d') : date('Y-m-d')" required />
                </div>
                <div>
                    <x-form-label required>Kategori</x-form-label>
                    <x-form-select name="kategori" required :value="$profit->kategori ?? ''"
                        :options="['fruit' => 'Fruit', 'skin' => 'Skin', 'gamepass' => 'Gamepass', 'permanent' => 'Permanent', 'joki' => 'Joki', 'akun' => 'Akun Jual', 'lainnya' => 'Lainnya']" />
                </div>
                <div class="sm:col-span-2">
                    <x-form-label>Keterangan <span class="text-xs text-gray-400 dark:text-gray-500 font-normal">- opsional</span></x-form-label>
                    <x-form-input name="keterangan" :value="$profit->keterangan ?? ''" placeholder="Contoh: Jual Perm Dragon ke @buyer123" />
                </div>
                <div>
                    <x-form-label>Modal (Rp) <span class="text-xs text-gray-400 dark:text-gray-500 font-normal">- opsional</span></x-form-label>
                    <input type="number" name="modal" x-model.number="modal" value="{{ old('modal', $profit->modal ?? 0) }}" min="0" placeholder="0" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-gray-100 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <x-form-label>Pendapatan (Rp) <span class="text-xs text-gray-400 dark:text-gray-500 font-normal">- opsional</span></x-form-label>
                    <input type="number" name="pendapatan" x-model.number="pendapatan" value="{{ old('pendapatan', $profit->pendapatan ?? 0) }}" min="0" placeholder="0" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-gray-100 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            {{-- Preview Keuntungan --}}
            <div class="rounded-xl p-3 text-center" :class="untung >= 0 ? 'bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800' : 'bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800'">
                <p class="text-[11px] text-gray-500 dark:text-gray-400">Keuntungan</p>
                <p class="text-2xl font-extrabold" :class="untung >= 0 ? 'text-emerald-600' : 'text-red-600'" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(untung)"></p>
            </div>

            <div>
                <x-form-label>Metode Bayar <span class="text-xs text-gray-400 dark:text-gray-500 font-normal">- opsional</span></x-form-label>
                <x-form-select name="metode_bayar" placeholder="-- Pilih --" :value="$profit->metode_bayar ?? ''"
                    :options="['dana' => 'Dana', 'gopay' => 'GoPay', 'shopeepay' => 'ShopeePay', 'seabank' => 'SeaBank', 'bank_kalsel' => 'Bank Kalsel', 'bri' => 'BRI', 'qris' => 'QRIS', 'cash' => 'Cash']" />
            </div>

            <div class="flex items-center gap-2 pt-1">
                <x-btn type="submit" variant="primary" size="lg">{{ isset($profit) ? 'Perbarui' : 'Simpan' }}</x-btn>
                <x-btn :href="route('bloxfruit.profit.index')" variant="secondary" size="lg">Batal</x-btn>
            </div>
        </x-form-card>
    </form>
</div>
@endsection
