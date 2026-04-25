@extends('layouts.app')
@section('title', isset($reminder) ? 'Edit Pengingat' : 'Tambah Pengingat')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ isset($reminder) ? route('diet.reminders.update', $reminder) : route('diet.reminders.store') }}" class="space-y-6 rounded-xl bg-white p-6 shadow-sm border border-gray-100">
        @csrf
        @if(isset($reminder)) @method('PUT') @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul *</label>
                <input type="text" name="judul" value="{{ old('judul', $reminder->judul ?? '') }}" required placeholder="Contoh: Minum Air Putih" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Waktu *</label>
                <input type="time" name="waktu" value="{{ old('waktu', isset($reminder) ? \Carbon\Carbon::parse($reminder->waktu)->format('H:i') : '') }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe *</label>
                <select name="tipe" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    @foreach(['makan' => 'Makan', 'olahraga' => 'Olahraga', 'minum' => 'Minum Air', 'timbang' => 'Timbang Badan', 'tidur' => 'Tidur'] as $k => $v)
                    <option value="{{ $k }}" {{ old('tipe', $reminder->tipe ?? '') == $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Hari Aktif *</label>
                <select name="hari_aktif" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    @foreach(['setiap_hari' => 'Setiap Hari', 'senin_jumat' => 'Senin - Jumat', 'weekend' => 'Weekend', 'custom' => 'Custom'] as $k => $v)
                    <option value="{{ $k }}" {{ old('hari_aktif', $reminder->hari_aktif ?? 'setiap_hari') == $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Pesan</label>
            <textarea name="pesan" rows="2" placeholder="Pesan pengingat..." class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">{{ old('pesan', $reminder->pesan ?? '') }}</textarea>
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-lg bg-emerald-600 px-6 py-2 text-sm font-medium text-white hover:bg-emerald-700">{{ isset($reminder) ? 'Perbarui' : 'Simpan' }}</button>
            <a href="{{ route('diet.reminders.index') }}" class="rounded-lg bg-gray-100 px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Batal</a>
        </div>
    </form>
</div>
@endsection
