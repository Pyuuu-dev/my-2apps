@extends('layouts.app')
@section('title', 'Jadwal Makan')

@section('content')
{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <form method="GET" class="flex gap-2 items-center">
        <input type="date" name="tanggal" value="{{ $tanggal }}" class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
    </form>
    <div class="flex items-center gap-3">
        <span class="text-sm text-gray-500">{{ number_format($totalKalori) }} / {{ number_format($planAktif->kalori_harian_target) }} kkal</span>
        <a href="{{ route('diet.meals.create') }}" class="btn-success inline-flex items-center gap-1.5 text-sm">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah
        </a>
    </div>
</div>

{{-- Banner Puasa --}}
@if($puasaHariIni)
<div class="rounded-xl bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 p-3 mb-4 flex items-center gap-3">
    <span class="text-lg">🌙</span>
    <div class="flex-1">
        <p class="text-sm font-bold text-emerald-900">Mode Puasa - {{ $puasaHariIni->label_tipe }}</p>
        <p class="text-[11px] text-emerald-700">Sahur {{ $puasaHariIni->waktu_sahur }} &middot; Berbuka {{ $puasaHariIni->waktu_berbuka }} &middot; Jadwal makan disesuaikan</p>
    </div>
</div>
@endif

{{-- Konsistensi --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
    <div class="stat-card" style="--accent: linear-gradient(90deg, #f59e0b, #f97316)">
        <p class="text-[11px] text-gray-500">Streak Catat Makan</p>
        <p class="text-xl font-extrabold text-amber-600">{{ $konsistensi['streak_makan'] }} hari</p>
    </div>
    <div class="stat-card" style="--accent: linear-gradient(90deg, #10b981, #059669)">
        <p class="text-[11px] text-gray-500">Konsistensi</p>
        <p class="text-xl font-extrabold text-emerald-600">{{ $konsistensi['konsistensi_persen'] }}%</p>
    </div>
    <div class="stat-card" style="--accent: linear-gradient(90deg, #3b82f6, #6366f1)">
        <p class="text-[11px] text-gray-500">Hari Aktif</p>
        <p class="text-xl font-extrabold text-blue-600">{{ $konsistensi['total_hari_aktif'] }} / {{ $konsistensi['total_hari_program'] }}</p>
    </div>
    <div class="stat-card" style="--accent: linear-gradient(90deg, #f97316, #ef4444)">
        <p class="text-[11px] text-gray-500">Kalori Hari Ini</p>
        <p class="text-xl font-extrabold {{ $totalKalori > $planAktif->kalori_harian_target ? 'text-red-600' : 'text-orange-600' }}">{{ number_format($totalKalori) }}</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    {{-- Catatan Makan Hari Ini --}}
    <div class="md:col-span-2">
        <h3 class="text-sm font-bold text-gray-900 mb-3">Catatan Makan - {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</h3>
        @php
        $grouped = $meals->groupBy('waktu_makan');
        $labels = ['sarapan' => 'Sarapan', 'makan_siang' => 'Makan Siang', 'makan_malam' => 'Makan Malam', 'snack' => 'Snack'];
        @endphp

        @foreach($labels as $key => $label)
        <div class="mb-4">
            <p class="text-xs font-semibold text-gray-500 uppercase mb-1.5">{{ $label }}</p>
            <div class="glass-card rounded-xl divide-y divide-gray-50 overflow-hidden">
                @forelse($grouped->get($key, collect()) as $meal)
                <div class="px-4 py-2.5 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $meal->nama_makanan }}</p>
                        <p class="text-[11px] text-gray-400">P:{{ $meal->protein }}g K:{{ $meal->karbohidrat }}g L:{{ $meal->lemak }}g</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold text-orange-600">{{ $meal->kalori }}</span>
                        <a href="{{ route('diet.meals.edit', $meal) }}" class="text-[11px] text-gray-400 hover:text-emerald-600">Edit</a>
                        <form method="POST" action="{{ route('diet.meals.destroy', $meal) }}" onsubmit="return confirm('Hapus?')">
                            @csrf @method('DELETE')
                            <button class="text-[11px] text-gray-400 hover:text-red-500">Hapus</button>
                        </form>
                    </div>
                </div>
                @empty
                <p class="px-4 py-3 text-sm text-gray-300">Belum ada catatan</p>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">
        {{-- Tracker Minum Air --}}
        <div x-data="waterTracker()">
            <h3 class="text-sm font-bold text-gray-900 mb-3">Minum Air</h3>
            <div class="glass-card rounded-xl p-4">
                {{-- Progress Circle --}}
                <div class="flex items-center gap-4 mb-3">
                    <div class="relative h-16 w-16 shrink-0">
                        <svg class="h-16 w-16 -rotate-90" viewBox="0 0 64 64">
                            <circle cx="32" cy="32" r="28" fill="none" stroke="#e5e7eb" stroke-width="5"/>
                            <circle cx="32" cy="32" r="28" fill="none" stroke="{{ $totalMinum >= $targetAir ? '#10b981' : '#3b82f6' }}" stroke-width="5"
                                stroke-dasharray="{{ min(100, round(($totalMinum / max(1, $targetAir)) * 100)) * 1.759 }} 175.9"
                                stroke-linecap="round"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-xs font-extrabold {{ $totalMinum >= $targetAir ? 'text-emerald-600' : 'text-blue-600' }}">{{ round(($totalMinum / max(1, $targetAir)) * 100) }}%</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-lg font-extrabold text-blue-600">{{ number_format($totalMinum) }}<span class="text-xs font-normal text-gray-400">ml</span></p>
                        <p class="text-[11px] text-gray-400">Target: {{ number_format($targetAir) }}ml</p>
                        @if($totalMinum >= $targetAir)
                        <p class="text-[11px] font-semibold text-emerald-600">Target tercapai!</p>
                        @else
                        <p class="text-[11px] text-gray-400">Sisa: {{ number_format($targetAir - $totalMinum) }}ml</p>
                        @endif
                    </div>
                </div>

                {{-- Tombol Tambah Cepat --}}
                <div class="grid grid-cols-4 gap-1.5 mb-3">
                    @foreach([150, 250, 350, 500] as $ml)
                    <form method="POST" action="{{ route('diet.water.store') }}">
                        @csrf
                        <input type="hidden" name="jumlah_ml" value="{{ $ml }}">
                        <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                        <button type="submit" class="w-full rounded-lg border border-blue-200 bg-blue-50 px-1 py-2 text-center hover:bg-blue-100 transition-colors">
                            <p class="text-xs font-bold text-blue-700">+{{ $ml }}ml</p>
                            <p class="text-[10px] text-blue-500">{{ $ml < 250 ? 'Cangkir' : ($ml == 250 ? '1 Gelas' : ($ml == 350 ? 'Botol Kecil' : 'Botol')) }}</p>
                        </button>
                    </form>
                    @endforeach
                </div>

                {{-- Custom Input --}}
                <form method="POST" action="{{ route('diet.water.store') }}" class="flex gap-1.5 mb-3">
                    @csrf
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                    <input type="number" name="jumlah_ml" placeholder="ml" min="50" max="2000" required
                        class="flex-1 rounded-lg border-gray-300 text-sm text-center focus:border-blue-500 focus:ring-blue-500">
                    <button type="submit" class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">Tambah</button>
                </form>

                {{-- Riwayat Minum Hari Ini --}}
                @if($waterLogs->count() > 0)
                <div class="border-t border-gray-100 pt-2">
                    <div class="flex items-center justify-between mb-1.5">
                        <p class="text-[11px] font-semibold text-gray-500">Hari ini ({{ $waterLogs->count() }}x minum)</p>
                        <form method="POST" action="{{ route('diet.water.reset') }}" onsubmit="return confirm('Reset semua catatan minum hari ini?')">
                            @csrf
                            <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                            <button type="submit" class="text-[10px] text-red-400 hover:text-red-600">Reset</button>
                        </form>
                    </div>
                    <div class="space-y-1 max-h-32 overflow-y-auto custom-scrollbar">
                        @foreach($waterLogs->reverse() as $wl)
                        <div class="flex items-center justify-between rounded-md bg-gray-50 px-2 py-1">
                            <div class="flex items-center gap-1.5">
                                <svg class="h-3 w-3 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                <span class="text-[11px] text-gray-600">{{ $wl->created_at->format('H:i') }}</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-[11px] font-semibold text-blue-600">{{ $wl->jumlah_ml }}ml</span>
                                <form method="POST" action="{{ route('diet.water.destroy', $wl) }}" onsubmit="return confirm('Hapus?')">
                                    @csrf @method('DELETE')
                                    <button class="text-[10px] text-gray-300 hover:text-red-500">&times;</button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Quick Add --}}
        <div x-data="{ open: false, waktu: 'sarapan' }">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-bold text-gray-900">Tambah Cepat</h3>
                <button @click="open = !open" class="text-[11px] text-emerald-600 font-medium" x-text="open ? 'Tutup' : 'Buka'"></button>
            </div>
            <div x-show="open" x-transition>
                <div class="mb-3">
                    <select x-model="waktu" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="sarapan">Sarapan</option>
                        <option value="makan_siang">Makan Siang</option>
                        <option value="makan_malam">Makan Malam</option>
                        <option value="snack">Snack</option>
                    </select>
                </div>
                @php $katLabels = ['sarapan'=>'Sarapan','makan_utama'=>'Makan Utama','snack'=>'Snack & Buah','minuman'=>'Minuman']; @endphp
                @foreach($katLabels as $kat => $katLabel)
                @if($foodsByKategori->has($kat))
                <p class="text-[11px] font-bold text-gray-500 uppercase mt-3 mb-1.5">{{ $katLabel }}</p>
                <div class="space-y-1">
                    @foreach($foodsByKategori[$kat] as $food)
                    <form method="POST" action="{{ route('diet.meals.quick') }}" class="flex items-center justify-between rounded-lg bg-white border border-gray-100 px-3 py-1.5 hover:border-emerald-200 hover:bg-emerald-50/50 transition-colors">
                        @csrf
                        <input type="hidden" name="food_id" value="{{ $food->id }}">
                        <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                        <input type="hidden" :name="'waktu_makan'" :value="waktu">
                        <input type="hidden" name="porsi" value="1">
                        <div>
                            <p class="text-sm text-gray-800">{{ $food->nama }}</p>
                            <p class="text-[11px] text-gray-400">{{ $food->kalori }} kkal &middot; {{ $food->satuan_porsi }}</p>
                        </div>
                        <button type="submit" class="shrink-0 rounded-md bg-emerald-100 px-2 py-1 text-[11px] font-semibold text-emerald-700 hover:bg-emerald-200">+ Tambah</button>
                    </form>
                    @endforeach
                </div>
                @endif
                @endforeach
            </div>
        </div>

        {{-- Jadwal Ideal --}}
        <div>
            <h3 class="text-sm font-bold text-gray-900 mb-3">Jadwal Ideal</h3>
            <div class="space-y-2">
                @foreach($jadwalIdeal as $j)
                <div class="rounded-xl bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-100 p-3">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-bold text-emerald-700">{{ $j['waktu'] }} - {{ $j['label'] }}</span>
                        <span class="text-[11px] font-semibold text-emerald-600">~{{ $j['kalori'] }} kkal</span>
                    </div>
                    <p class="text-[11px] text-gray-500 leading-relaxed">{{ $j['tips'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
<script>
function waterTracker() {
    return {};
}
</script>
@endsection
