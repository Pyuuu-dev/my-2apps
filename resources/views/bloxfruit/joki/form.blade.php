@extends('layouts.app')
@section('title', isset($joki) ? 'Edit Joki' : 'Tambah Joki')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ isset($joki) ? route('bloxfruit.joki.update', $joki) : route('bloxfruit.joki.store') }}"
        class="space-y-5 rounded-xl bg-white dark:bg-slate-800 p-6 shadow-sm border border-gray-100 dark:border-slate-700"
        x-data="jokiForm()">
        @csrf
        @if(isset($joki)) @method('PUT') @endif

        {{-- Info Pelanggan --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pelanggan *</label>
                <input type="text" name="nama_pelanggan" value="{{ old('nama_pelanggan', $joki->nama_pelanggan ?? '') }}" required placeholder="Nama / username" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kontak <span class="text-xs text-gray-400">- opsional</span></label>
                <input type="text" name="kontak" value="{{ old('kontak', $joki->kontak ?? '') }}" placeholder="WA / Discord" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
        </div>

        {{-- Akun Roblox --}}
        <div class="rounded-xl border border-gray-200 dark:border-slate-700 p-4">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Akun Roblox Pelanggan</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username Roblox</label>
                    <input type="text" name="username_roblox" value="{{ old('username_roblox', $joki->username_roblox ?? '') }}" placeholder="Username Roblox" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Roblox</label>
                    <div class="relative" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'" name="password_roblox" value="{{ old('password_roblox', $joki->password_roblox ?? '') }}" placeholder="Password" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm pr-10">
                        <button type="button" @click="show = !show" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg x-show="!show" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="show" x-cloak class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pilih Jenis Joki --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Joki *</label>
            <div class="space-y-2 max-h-64 overflow-y-auto rounded-lg border border-gray-200 dark:border-slate-700 p-3">
                @foreach($kategoriLabels as $katKey => $kat)
                @if(isset($servicesByKategori[$katKey]))
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1">{{ $kat['icon'] }} {{ $kat['label'] }}</p>
                    @foreach($servicesByKategori[$katKey] as $svc)
                    @php $radioVal = $katKey . ':' . $svc->nama; @endphp
                    <label class="flex items-center justify-between rounded-lg px-3 py-2 cursor-pointer hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors" :class="selected === '{{ $radioVal }}' && 'bg-indigo-50 dark:bg-indigo-950/30'">
                        <div class="flex items-center gap-2">
                            <input type="radio" name="jenis_joki" value="{{ $radioVal }}" x-model="selected" @change="onSelect('{{ $radioVal }}', {{ $svc->harga }}, {{ $katKey === 'lainnya' ? 'true' : 'false' }})" class="text-indigo-600 focus:ring-indigo-500" {{ old('jenis_joki', $joki->jenis_joki ?? '') === $radioVal ? 'checked' : '' }}>
                            <span class="text-sm text-gray-800 dark:text-gray-200">{{ $svc->nama }}</span>
                            @if($svc->keterangan)
                            <span class="text-[10px] text-gray-400">({{ $svc->keterangan }})</span>
                            @endif
                        </div>
                        @if($svc->harga > 0)
                        <span class="text-sm font-bold text-indigo-600">{{ number_format($svc->harga) }}</span>
                        @endif
                    </label>
                    @endforeach
                </div>
                @endif
                @endforeach
            </div>

            {{-- Input custom jika pilih Lainnya --}}
            <div x-show="isLainnya" x-collapse x-cloak class="mt-2">
                <input type="text" x-model="customJenis" @input="updateJenisLainnya()" placeholder="Ketik jenis joki custom..." class="w-full rounded-lg border-amber-300 bg-amber-50 dark:bg-amber-950/20 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-sm">
                <input type="hidden" name="jenis_joki_custom" :value="customJenis">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp)</label>
                <input type="number" name="harga" x-model.number="harga" min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                <p class="text-[10px] text-gray-400 mt-1">Auto dari jenis, bisa diedit</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                <select name="status" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    @foreach(['antrian' => 'Antrian', 'proses' => 'Proses', 'selesai' => 'Selesai', 'batal' => 'Batal'] as $k => $v)
                    <option value="{{ $k }}" {{ old('status', $joki->status ?? 'antrian') == $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', isset($joki) && $joki->tanggal_mulai ? $joki->tanggal_mulai->format('Y-m-d') : date('Y-m-d')) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Detail Pesanan <span class="text-xs text-gray-400">- opsional</span></label>
            <textarea name="detail_pesanan" rows="2" placeholder="Detail tambahan..." class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('detail_pesanan', $joki->detail_pesanan ?? '') }}</textarea>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-lg bg-indigo-600 px-6 py-2 text-sm font-medium text-white hover:bg-indigo-700">{{ isset($joki) ? 'Perbarui' : 'Simpan' }}</button>
            <a href="{{ route('bloxfruit.joki.index') }}" class="rounded-lg bg-gray-100 px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Batal</a>
        </div>
    </form>
</div>

<script>
function jokiForm() {
    return {
        selected: '{{ old('jenis_joki', $joki->jenis_joki ?? '') }}',
        harga: {{ old('harga', $joki->harga ?? 0) }},
        isLainnya: {{ old('jenis_joki', $joki->jenis_joki ?? '') === 'lainnya:Lainnya (Custom)' ? 'true' : 'false' }},
        customJenis: '',
        onSelect(val, price, lainnya) {
            this.harga = price;
            this.isLainnya = lainnya;
            if (!lainnya) this.customJenis = '';
        },
        updateJenisLainnya() {
            if (this.customJenis) {
                this.selected = 'lainnya:' + this.customJenis;
                // Update hidden radio
                const radios = document.querySelectorAll('input[name="jenis_joki"]');
                radios.forEach(r => { if (r.value.startsWith('lainnya:')) r.value = 'lainnya:' + this.customJenis; });
            }
        }
    }
}
</script>
@endsection
