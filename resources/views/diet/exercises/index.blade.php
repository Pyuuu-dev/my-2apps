@extends('layouts.app')
@section('title', 'Olahraga')

@section('content')
{{-- Date Navigator --}}
@include('diet.partials.date-navigator', ['tanggal' => $tanggal, 'tanggalAktif' => $tanggalAktif, 'route' => 'diet.exercises.index', 'accent' => 'blue'])

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <span class="text-sm text-gray-500">{{ $totalDurasi }} menit | {{ number_format($totalKalori) }} kkal</span>
    <a href="{{ route('diet.exercises.create') }}" class="btn-success inline-flex items-center gap-1.5 text-sm">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah
    </a>
</div>

{{-- Banner Puasa --}}
@if($puasaHariIni)
<div class="rounded-xl bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 p-3 mb-4 flex items-center gap-3">
    <span class="text-lg">🌙</span>
    <div>
        <p class="text-sm font-bold text-amber-900">Mode Puasa - {{ $puasaHariIni->label_tipe }}</p>
        <p class="text-[11px] text-amber-700">Olahraga ringan saja. Hindari intensitas berat saat puasa. Waktu terbaik: sebelum berbuka atau setelah makan.</p>
    </div>
</div>
@endif

{{-- Stats --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
    <div class="stat-card" style="--accent: linear-gradient(90deg, #f59e0b, #f97316)">
        <p class="text-[11px] text-gray-500">Streak Olahraga</p>
        <p class="text-xl font-extrabold text-amber-600">{{ $konsistensi['streak_olahraga'] }} hari</p>
    </div>
    <div class="stat-card" style="--accent: linear-gradient(90deg, #3b82f6, #6366f1)">
        <p class="text-[11px] text-gray-500">Minggu Ini</p>
        <p class="text-xl font-extrabold text-blue-600">{{ $konsistensi['olahraga_minggu_ini'] }}x</p>
    </div>
    <div class="stat-card" style="--accent: linear-gradient(90deg, #ef4444, #dc2626)">
        <p class="text-[11px] text-gray-500">Kalori Hari Ini</p>
        <p class="text-xl font-extrabold text-red-600">{{ number_format($totalKalori) }} kkal</p>
    </div>
    <div class="stat-card" style="--accent: linear-gradient(90deg, #10b981, #059669)">
        <p class="text-[11px] text-gray-500">Konsistensi</p>
        <p class="text-xl font-extrabold text-emerald-600">{{ $konsistensi['konsistensi_persen'] }}%</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    {{-- Catatan Hari Ini --}}
    <div class="md:col-span-2">
        <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3">Catatan - {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</h3>
        <div class="table-container overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 dark:bg-slate-800">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase text-gray-500">Jenis</th>
                        <th class="px-3 py-2.5 text-center text-[11px] font-semibold uppercase text-gray-500">Durasi</th>
                        <th class="px-3 py-2.5 text-center text-[11px] font-semibold uppercase text-gray-500">Intensitas</th>
                        <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase text-gray-500">Kalori</th>
                        <th class="px-3 py-2.5 text-center text-[11px] font-semibold uppercase text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($exercises as $ex)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-2.5 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $ex->jenis_olahraga }}</td>
                        <td class="px-3 py-2.5 text-sm text-center text-gray-600">{{ $ex->durasi_menit }}'</td>
                        <td class="px-3 py-2.5 text-center">
                            <span class="inline-flex rounded-full px-2 py-0.5 text-[11px] font-medium {{ $ex->intensitas === 'berat' ? 'badge-mythical' : ($ex->intensitas === 'sedang' ? 'badge-legendary' : 'badge-uncommon') }}">{{ ucfirst($ex->intensitas) }}</span>
                        </td>
                        <td class="px-3 py-2.5 text-sm text-right font-bold text-red-600">{{ $ex->kalori_terbakar }}</td>
                        <td class="px-3 py-2.5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('diet.exercises.edit', $ex) }}" class="text-[11px] text-gray-400 hover:text-emerald-600">Edit</a>
                                <form method="POST" action="{{ route('diet.exercises.destroy', $ex) }}" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="text-[11px] text-gray-400 hover:text-red-500">Hapus</button></form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-sm text-gray-300">Belum ada olahraga hari ini</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Sidebar: Jadwal Mingguan / Puasa --}}
    <div>
        @if($puasaHariIni && $configPuasa && !empty($configPuasa['jadwal_olahraga']))
        <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3">Olahraga Saat Puasa</h3>
        <div class="space-y-2 mb-6">
            @foreach($configPuasa['jadwal_olahraga'] as $or)
            <div class="rounded-xl bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-100 p-3">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-bold text-amber-700">{{ $or['waktu'] }} - {{ $or['jenis'] }}</span>
                    <span class="text-[11px] font-semibold text-amber-600">{{ $or['durasi'] }}'</span>
                </div>
                <p class="text-[11px] text-gray-600">{{ $or['catatan'] }}</p>
            </div>
            @endforeach
        </div>
        @endif

        <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3">Jadwal Mingguan</h3>
        <div class="space-y-2">
            @foreach($jadwalMingguan as $j)
            @php $isToday = $j['hari'] === now()->translatedFormat('l'); @endphp
            <div class="rounded-xl p-3 border {{ $j['aktif'] ? ($isToday ? 'bg-blue-50 border-blue-200' : 'bg-gradient-to-r from-blue-50 to-indigo-50 border-blue-100') : 'bg-gray-50 border-gray-100' }}">
                <div class="flex items-center justify-between mb-0.5">
                    <span class="text-xs font-bold {{ $j['aktif'] ? 'text-blue-700' : 'text-gray-400' }}">
                        {{ $j['hari'] }} @if($isToday) <span class="text-[11px] font-normal">(Hari ini)</span> @endif
                    </span>
                    @if($j['aktif'])
                    <span class="text-[11px] font-semibold text-blue-600">{{ $j['durasi'] }}'</span>
                    @endif
                </div>
                <p class="text-[11px] {{ $j['aktif'] ? 'text-gray-600' : 'text-gray-400' }}">{{ $j['jenis'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Rekomendasi Exercise --}}
<div class="mb-6">
    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Rekomendasi Exercise & Panduan</h3>

    @php
        $katLabels = [
            'kardio' => ['label' => 'Kardio', 'icon' => '🏃', 'color' => 'red', 'bg' => 'from-red-50 to-rose-50 border-red-200'],
            'kekuatan' => ['label' => 'Kekuatan', 'icon' => '💪', 'color' => 'blue', 'bg' => 'from-blue-50 to-indigo-50 border-blue-200'],
            'fleksibilitas' => ['label' => 'Fleksibilitas', 'icon' => '🧘', 'color' => 'purple', 'bg' => 'from-purple-50 to-violet-50 border-purple-200'],
            'hiit' => ['label' => 'HIIT', 'icon' => '🔥', 'color' => 'orange', 'bg' => 'from-orange-50 to-amber-50 border-orange-200'],
        ];
        $levelColors = ['pemula' => 'bg-emerald-100 text-emerald-700', 'menengah' => 'bg-amber-100 text-amber-700', 'lanjutan' => 'bg-red-100 text-red-700'];
    @endphp

    <div class="space-y-4">
        @foreach($katLabels as $katKey => $kat)
        @if($exercisesByKategori->has($katKey))
        <div x-data="{ open: false }">
            <div class="flex items-center justify-between rounded-t-xl bg-gradient-to-r {{ $kat['bg'] }} border px-4 py-3 cursor-pointer" @click="open = !open">
                <div class="flex items-center gap-2">
                    <span class="text-lg">{{ $kat['icon'] }}</span>
                    <div>
                        <p class="text-sm font-bold text-gray-900">{{ $kat['label'] }}</p>
                        <p class="text-[10px] text-gray-500">{{ $exercisesByKategori[$katKey]->count() }} exercise</p>
                    </div>
                </div>
                <svg class="h-4 w-4 text-gray-400 transition-transform" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
            <div x-show="open" x-collapse x-cloak class="border border-t-0 rounded-b-xl {{ $kat['bg'] }} overflow-hidden">
                @foreach($exercisesByKategori[$katKey] as $exDb)
                <div x-data="exerciseTimer('{{ $exDb->nama }}', {{ $exDb->kalori_per_menit }}, '{{ $exDb->intensitas }}', {{ $exDb->durasi_rekomendasi }})" class="border-b border-white/50 last:border-b-0">
                    {{-- Header --}}
                    <div class="flex items-center justify-between px-4 py-3 cursor-pointer hover:bg-white/30" @click="detail = !detail">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="text-sm font-bold text-gray-900">{{ $exDb->nama }}</p>
                                    <span class="rounded-full px-1.5 py-0.5 text-[10px] font-semibold {{ $levelColors[$exDb->level] ?? 'bg-gray-100 text-gray-600' }}">{{ ucfirst($exDb->level) }}</span>
                                    {{-- Timer badge saat aktif --}}
                                    <template x-if="running || paused">
                                        <span class="rounded-full px-2 py-0.5 text-[10px] font-bold animate-pulse" :class="running ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700'" x-text="timerDisplay"></span>
                                    </template>
                                </div>
                                <p class="text-[11px] text-gray-500">{{ $exDb->otot_target }} &middot; {{ $exDb->durasi_rekomendasi }}' &middot; ~{{ $exDb->kalori_per_menit * $exDb->durasi_rekomendasi }} kkal</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0" @click.stop>
                            {{-- Tombol Timer --}}
                            <template x-if="!running && !paused && !finished">
                                <button @click="start()" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-[11px] font-bold text-white hover:bg-emerald-700">Mulai</button>
                            </template>
                            <template x-if="running">
                                <button @click="pause()" class="rounded-lg bg-amber-500 px-3 py-1.5 text-[11px] font-bold text-white hover:bg-amber-600">Pause</button>
                            </template>
                            <template x-if="paused">
                                <div class="flex gap-1">
                                    <button @click="resume()" class="rounded-lg bg-emerald-600 px-2.5 py-1.5 text-[11px] font-bold text-white hover:bg-emerald-700">Lanjut</button>
                                    <button @click="stop()" class="rounded-lg bg-red-500 px-2.5 py-1.5 text-[11px] font-bold text-white hover:bg-red-600">Stop</button>
                                </div>
                            </template>
                            <template x-if="finished">
                                <div class="flex gap-1">
                                    <button @click="save()" class="rounded-lg bg-emerald-600 px-2.5 py-1.5 text-[11px] font-bold text-white hover:bg-emerald-700">Simpan</button>
                                    <button @click="reset()" class="rounded-lg bg-gray-200 px-2.5 py-1.5 text-[11px] font-bold text-gray-600 hover:bg-gray-300">Reset</button>
                                </div>
                            </template>
                            <svg class="h-4 w-4 text-gray-400 transition-transform" :class="detail && 'rotate-180'" @click="detail = !detail" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>

                    {{-- Timer Display (saat aktif) --}}
                    <div x-show="running || paused || finished" x-collapse x-cloak class="px-4 pb-3">
                        <div class="rounded-xl p-4 text-center" :class="running ? 'bg-red-50 border border-red-200' : (finished ? 'bg-emerald-50 border border-emerald-200' : 'bg-amber-50 border border-amber-200')">
                            {{-- Animasi besar saat timer aktif --}}
                            <div class="flex justify-center mb-2">
                                <div class="h-20 w-16" :class="running ? 'text-red-500' : (finished ? 'text-emerald-500' : 'text-amber-500')">
                                    @include('diet.exercises.partials.stick-animation', ['exercise' => $exDb->nama, 'playing' => true])
                                </div>
                            </div>
                            {{-- Timer besar --}}
                            <p class="text-4xl font-extrabold font-mono tracking-wider" :class="running ? 'text-red-600' : (finished ? 'text-emerald-600' : 'text-amber-600')" x-text="timerDisplay"></p>
                            <p class="text-xs text-gray-500 mt-1" x-text="running ? 'Sedang berjalan...' : (finished ? 'Selesai!' : 'Dijeda')"></p>

                            {{-- Stats real-time --}}
                            <div class="flex justify-center gap-6 mt-3">
                                <div>
                                    <p class="text-lg font-extrabold text-red-600" x-text="kaloriTerbakar"></p>
                                    <p class="text-[10px] text-gray-400">kkal terbakar</p>
                                </div>
                                <div>
                                    <p class="text-lg font-extrabold text-blue-600" x-text="menitBerjalan"></p>
                                    <p class="text-[10px] text-gray-400">menit</p>
                                </div>
                            </div>

                            {{-- Tombol kontrol besar --}}
                            <div class="flex justify-center gap-2 mt-3">
                                <template x-if="running">
                                    <button @click="pause()" class="rounded-xl bg-amber-500 px-6 py-2 text-sm font-bold text-white hover:bg-amber-600">Pause</button>
                                </template>
                                <template x-if="paused">
                                    <button @click="resume()" class="rounded-xl bg-emerald-600 px-6 py-2 text-sm font-bold text-white hover:bg-emerald-700">Lanjut</button>
                                </template>
                                <template x-if="running || paused">
                                    <button @click="stop()" class="rounded-xl bg-red-600 px-6 py-2 text-sm font-bold text-white hover:bg-red-700">Selesai</button>
                                </template>
                                <template x-if="finished">
                                    <form method="POST" action="{{ route('diet.exercises.store') }}">
                                        @csrf
                                        <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                                        <input type="hidden" name="jenis_olahraga" :value="nama">
                                        <input type="hidden" name="durasi_menit" :value="menitBerjalan">
                                        <input type="hidden" name="kalori_terbakar" :value="kaloriTerbakar">
                                        <input type="hidden" name="intensitas" :value="intensitas">
                                        <button type="submit" class="rounded-xl bg-emerald-600 px-6 py-2 text-sm font-bold text-white hover:bg-emerald-700">Simpan Hasil</button>
                                    </form>
                                </template>
                                <template x-if="finished">
                                    <button @click="reset()" class="rounded-xl bg-gray-200 px-6 py-2 text-sm font-bold text-gray-600 hover:bg-gray-300">Ulangi</button>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Detail Instruksi --}}
                    <div x-show="detail" x-collapse x-cloak class="px-4 pb-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="space-y-2">
                                <div class="flex flex-wrap gap-1.5">
                                    <span class="rounded-md bg-white/80 border border-gray-200 px-2 py-0.5 text-[10px] font-semibold text-gray-600">{{ ucfirst($exDb->intensitas) }}</span>
                                    <span class="rounded-md bg-white/80 border border-gray-200 px-2 py-0.5 text-[10px] font-semibold text-gray-600">{{ $exDb->set_rep }}</span>
                                    <span class="rounded-md bg-white/80 border border-gray-200 px-2 py-0.5 text-[10px] font-semibold text-gray-600">{{ str_replace('_', ' ', ucfirst($exDb->peralatan)) }}</span>
                                    <span class="rounded-md bg-white/80 border border-gray-200 px-2 py-0.5 text-[10px] font-semibold text-red-600">~{{ $exDb->kalori_per_menit }} kkal/menit</span>
                                </div>
                                <div class="rounded-lg bg-emerald-50 border border-emerald-100 p-2.5">
                                    <p class="text-[11px] font-bold text-emerald-800 mb-1">Manfaat:</p>
                                    <p class="text-[11px] text-emerald-700 leading-relaxed">{{ $exDb->manfaat }}</p>
                                </div>
                            </div>
                            <div class="rounded-lg bg-blue-50 border border-blue-100 p-2.5">
                                <p class="text-[11px] font-bold text-blue-800 mb-1">Cara Melakukan:</p>
                                <div class="text-[11px] text-blue-700 leading-relaxed whitespace-pre-line">{{ $exDb->instruksi }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @endforeach
    </div>
</div>
<script>
function exerciseTimer(nama, kaloriPerMenit, intensitas, durasiRekomendasi) {
    return {
        nama: nama,
        kaloriPerMenit: kaloriPerMenit,
        intensitas: intensitas,
        durasiRekomendasi: durasiRekomendasi,
        detail: false,
        running: false,
        paused: false,
        finished: false,
        seconds: 0,
        interval: null,
        timerDisplay: '00:00',
        kaloriTerbakar: 0,
        menitBerjalan: 0,

        start() {
            this.running = true;
            this.paused = false;
            this.finished = false;
            this.seconds = 0;
            this.tick();
            this.interval = setInterval(() => this.tick(), 1000);
        },

        tick() {
            this.seconds++;
            const m = Math.floor(this.seconds / 60);
            const s = this.seconds % 60;
            this.timerDisplay = String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
            this.menitBerjalan = Math.max(1, Math.round(this.seconds / 60));
            this.kaloriTerbakar = Math.round((this.seconds / 60) * this.kaloriPerMenit);
        },

        pause() {
            this.running = false;
            this.paused = true;
            clearInterval(this.interval);
        },

        resume() {
            this.running = true;
            this.paused = false;
            this.interval = setInterval(() => this.tick(), 1000);
        },

        stop() {
            this.running = false;
            this.paused = false;
            this.finished = true;
            clearInterval(this.interval);
            this.menitBerjalan = Math.max(1, Math.round(this.seconds / 60));
            this.kaloriTerbakar = Math.round((this.seconds / 60) * this.kaloriPerMenit);
        },

        save() {
            // Submit form via closest form element
            this.$el.querySelector('form[action*="exercises"]')?.submit();
        },

        reset() {
            this.running = false;
            this.paused = false;
            this.finished = false;
            this.seconds = 0;
            this.timerDisplay = '00:00';
            this.kaloriTerbakar = 0;
            this.menitBerjalan = 0;
            clearInterval(this.interval);
        },

        destroy() {
            clearInterval(this.interval);
        }
    }
}
</script>
@endsection
