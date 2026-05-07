@extends('layouts.app')
@section('title', ($profile->nama ?? 'User') . ' - Detail')

@section('content')
<div class="space-y-5" x-data="{ editMode: false, sendMsg: false, resetModal: false, deleteModal: false }">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('diet.users.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div class="h-12 w-12 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-bold text-lg">
                {{ strtoupper(substr($profile->nama ?? '?', 0, 1)) }}
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-800 dark:text-white">{{ $profile->nama ?? 'Unknown' }}</h2>
                <p class="text-xs text-gray-500">@{{ $profile->username ?? '-' }} | ID: {{ $profile->telegram_chat_id }} | {{ $profile->aktif ? '🟢 Aktif' : '🔴 Nonaktif' }}</p>
            </div>
        </div>
        <div class="flex gap-1.5">
            <button @click="editMode = !editMode" class="px-2.5 py-1.5 text-[11px] font-medium rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700">
                <span x-text="editMode ? 'Tutup' : '✏️ Edit'"></span>
            </button>
            <button @click="sendMsg = true" class="px-2.5 py-1.5 text-[11px] font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700">💬 Kirim Pesan</button>
            <button @click="resetModal = true" class="px-2.5 py-1.5 text-[11px] font-medium rounded-lg bg-amber-600 text-white hover:bg-amber-700">🔄 Reset</button>
            <button @click="deleteModal = true" class="px-2.5 py-1.5 text-[11px] font-medium rounded-lg bg-red-600 text-white hover:bg-red-700">🗑</button>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-3 sm:grid-cols-6 gap-2">
        @php $statItems = [
            ['🍽', $counts['food_logs'], 'Food'],
            ['⚖️', $counts['weight_logs'], 'Berat'],
            ['💧', $counts['water_logs'], 'Air'],
            ['🏃', $counts['exercise_logs'], 'Sport'],
            ['🏆', count($badges), 'Badge'],
            ['🔥', $streak->current_streak ?? 0, 'Streak'],
        ]; @endphp
        @foreach($statItems as [$icon, $val, $label])
        <div class="rounded-xl p-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-center">
            <div class="text-xs text-gray-400">{{ $icon }}</div>
            <div class="text-lg font-bold text-gray-800 dark:text-white">{{ $val }}</div>
            <div class="text-[9px] text-gray-500">{{ $label }}</div>
        </div>
        @endforeach
    </div>

    {{-- Edit Profile Form --}}
    <div x-show="editMode" x-collapse x-cloak>
        <form method="POST" action="{{ route('diet.users.update', $profile) }}" class="rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 p-4 space-y-4">
            @csrf @method('PUT')
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Edit Profil</h3>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div>
                    <label class="text-[10px] text-gray-500 block mb-1">Nama</label>
                    <input type="text" name="nama" value="{{ $profile->nama }}" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 px-2.5 py-1.5 text-sm dark:text-gray-200">
                </div>
                <div>
                    <label class="text-[10px] text-gray-500 block mb-1">Gender</label>
                    <select name="gender" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 px-2.5 py-1.5 text-sm dark:text-gray-200">
                        <option value="pria" {{ $profile->gender === 'pria' ? 'selected' : '' }}>Pria</option>
                        <option value="wanita" {{ $profile->gender === 'wanita' ? 'selected' : '' }}>Wanita</option>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] text-gray-500 block mb-1">Umur</label>
                    <input type="number" name="umur" value="{{ $profile->umur }}" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 px-2.5 py-1.5 text-sm dark:text-gray-200">
                </div>
                <div>
                    <label class="text-[10px] text-gray-500 block mb-1">Tinggi (cm)</label>
                    <input type="number" step="0.1" name="tinggi_cm" value="{{ $profile->tinggi_cm }}" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 px-2.5 py-1.5 text-sm dark:text-gray-200">
                </div>
                <div>
                    <label class="text-[10px] text-gray-500 block mb-1">Berat (kg)</label>
                    <input type="number" step="0.1" name="berat_kg" value="{{ $profile->berat_kg }}" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 px-2.5 py-1.5 text-sm dark:text-gray-200">
                </div>
                <div>
                    <label class="text-[10px] text-gray-500 block mb-1">Target (kg)</label>
                    <input type="number" step="0.1" name="berat_target" value="{{ $profile->berat_target }}" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 px-2.5 py-1.5 text-sm dark:text-gray-200">
                </div>
                <div>
                    <label class="text-[10px] text-gray-500 block mb-1">Aktivitas</label>
                    <select name="level_aktivitas" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 px-2.5 py-1.5 text-sm dark:text-gray-200">
                        @foreach(['sedentary','light','moderate','active','very_active'] as $lv)
                        <option value="{{ $lv }}" {{ $profile->level_aktivitas === $lv ? 'selected' : '' }}>{{ ucfirst($lv) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] text-gray-500 block mb-1">Goal</label>
                    <select name="goal" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 px-2.5 py-1.5 text-sm dark:text-gray-200">
                        @foreach(['cutting','bulking','maintenance','diet'] as $g)
                        <option value="{{ $g }}" {{ $profile->goal === $g ? 'selected' : '' }}>{{ ucfirst($g) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                <div>
                    <label class="text-[10px] text-gray-500 block mb-1">Kalori Target</label>
                    <input type="number" name="kalori_target" value="{{ $profile->kalori_target }}" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 px-2.5 py-1.5 text-sm dark:text-gray-200">
                </div>
                <div>
                    <label class="text-[10px] text-gray-500 block mb-1">Protein (g)</label>
                    <input type="number" name="protein_target" value="{{ $profile->protein_target }}" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 px-2.5 py-1.5 text-sm dark:text-gray-200">
                </div>
                <div>
                    <label class="text-[10px] text-gray-500 block mb-1">Karbo (g)</label>
                    <input type="number" name="karbo_target" value="{{ $profile->karbo_target }}" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 px-2.5 py-1.5 text-sm dark:text-gray-200">
                </div>
                <div>
                    <label class="text-[10px] text-gray-500 block mb-1">Lemak (g)</label>
                    <input type="number" name="lemak_target" value="{{ $profile->lemak_target }}" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 px-2.5 py-1.5 text-sm dark:text-gray-200">
                </div>
                <div>
                    <label class="text-[10px] text-gray-500 block mb-1">Air (ml)</label>
                    <input type="number" name="air_target_ml" value="{{ $profile->air_target_ml }}" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 px-2.5 py-1.5 text-sm dark:text-gray-200">
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div>
                    <label class="text-[10px] text-gray-500 block mb-1">Max AI/hari</label>
                    <input type="number" name="max_ai_requests" value="{{ $profile->max_ai_requests ?? 50 }}" class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 px-2.5 py-1.5 text-sm dark:text-gray-200">
                </div>
                <div class="flex items-end gap-3 pb-1">
                    <label class="flex items-center gap-1.5 text-sm text-gray-700 dark:text-gray-300">
                        <input type="checkbox" name="proactive_nudge" value="1" {{ $profile->proactive_nudge ? 'checked' : '' }} class="rounded border-gray-300 dark:border-slate-600">
                        <span class="text-[11px]">Nudge</span>
                    </label>
                    <label class="flex items-center gap-1.5 text-sm text-gray-700 dark:text-gray-300">
                        <input type="checkbox" name="aktif" value="1" {{ $profile->aktif ? 'checked' : '' }} class="rounded border-gray-300 dark:border-slate-600">
                        <span class="text-[11px]">Aktif</span>
                    </label>
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-1.5 text-xs font-medium rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">Simpan</button>
                <form method="POST" action="{{ route('diet.users.recalculate', $profile) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-1.5 text-xs font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">🔄 Recalculate BMR/TDEE</button>
                </form>
            </div>
        </form>
    </div>

    {{-- Today Stats --}}
    <div class="rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 p-4">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">Hari Ini ({{ now('Asia/Singapore')->translatedFormat('l, d M Y') }})</h3>
        @php
            $tKal = $todayFood->sum('kalori');
            $tP = round($todayFood->sum('protein'), 1);
            $tK = round($todayFood->sum('karbohidrat'), 1);
            $tL = round($todayFood->sum('lemak'), 1);
            $exMin = $todayExercise->sum('durasi_menit');
            $exCal = $todayExercise->sum('kalori_terbakar');
            $tarK = $profile->kalori_target ?: 2000;
            $pctK = min(100, round(($tKal / $tarK) * 100));
            $tarA = $profile->getAirTarget();
            $pctA = min(100, round(($todayWater / $tarA) * 100));
        @endphp
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs text-gray-500">🔥 Kalori</span>
                    <span class="text-xs font-medium {{ $pctK > 100 ? 'text-red-500' : 'text-emerald-600' }}">{{ $pctK }}%</span>
                </div>
                <div class="text-lg font-bold text-gray-800 dark:text-white">{{ $tKal }} <span class="text-xs font-normal text-gray-400">/ {{ $tarK }}</span></div>
                <div class="h-1.5 rounded-full bg-gray-100 dark:bg-slate-700 mt-1 overflow-hidden">
                    <div class="h-full rounded-full {{ $pctK > 100 ? 'bg-red-500' : 'bg-emerald-500' }}" style="width: {{ min(100, $pctK) }}%"></div>
                </div>
            </div>
            <div>
                <div class="text-xs text-gray-500 mb-1">🥩 Macro</div>
                <div class="text-sm font-medium text-gray-800 dark:text-white">P: {{ $tP }}g | K: {{ $tK }}g | L: {{ $tL }}g</div>
                <div class="text-[10px] text-gray-400 mt-1">Target: P:{{ $profile->protein_target ?? '-' }} K:{{ $profile->karbo_target ?? '-' }} L:{{ $profile->lemak_target ?? '-' }}</div>
            </div>
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs text-gray-500">💧 Air</span>
                    <span class="text-xs font-medium text-blue-600">{{ $pctA }}%</span>
                </div>
                <div class="text-lg font-bold text-blue-600">{{ $todayWater }} <span class="text-xs font-normal text-gray-400">/ {{ $tarA }}ml</span></div>
                <div class="h-1.5 rounded-full bg-gray-100 dark:bg-slate-700 mt-1 overflow-hidden">
                    <div class="h-full rounded-full bg-blue-500" style="width: {{ $pctA }}%"></div>
                </div>
            </div>
        </div>
        @if($exMin > 0)
        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-slate-700 text-sm text-gray-600 dark:text-gray-400">
            🏃 Olahraga: {{ $exMin }} menit ({{ $exCal }} kkal terbakar)
        </div>
        @endif
    </div>

    <div class="grid md:grid-cols-2 gap-5">
        {{-- Recent Food --}}
        <div class="rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-slate-700">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">🍽 Food Logs (7 hari terakhir)</h3>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-slate-700 max-h-72 overflow-y-auto">
                @forelse($recentFood as $log)
                <div class="px-4 py-2">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-sm text-gray-800 dark:text-gray-200">{{ $log->nama_makanan }}</span>
                            <span class="text-[10px] text-gray-400 ml-1">{{ $log->waktu_makan }} | {{ $log->sumber }}</span>
                        </div>
                        <span class="text-xs font-medium text-orange-600">{{ $log->kalori }}</span>
                    </div>
                    <div class="text-[10px] text-gray-400">{{ $log->tanggal->format('d/m') }} | P:{{ $log->protein }}g K:{{ $log->karbohidrat }}g L:{{ $log->lemak }}g</div>
                </div>
                @empty
                <div class="px-4 py-6 text-center text-sm text-gray-400">Belum ada.</div>
                @endforelse
            </div>
        </div>

        {{-- Favorites --}}
        <div class="rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-slate-700">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">⭐ Favorit (Top 10)</h3>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($favorites as $fav)
                <div class="px-4 py-2 flex justify-between items-center">
                    <div>
                        <span class="text-sm text-gray-800 dark:text-gray-200">{{ $fav->nama_makanan }}</span>
                        <span class="text-[10px] text-gray-400 ml-1">{{ $fav->kalori }} kkal</span>
                    </div>
                    <span class="text-[10px] text-gray-500">x{{ $fav->use_count }}</span>
                </div>
                @empty
                <div class="px-4 py-6 text-center text-sm text-gray-400">Belum ada.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-5">
        {{-- Badges --}}
        <div class="rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-slate-700">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">🏆 Badges ({{ $badges->count() }})</h3>
            </div>
            <div class="p-4">
                @if($badges->isEmpty())
                <p class="text-sm text-gray-400 text-center">Belum ada.</p>
                @else
                <div class="flex flex-wrap gap-2">
                    @foreach($badges as $badge)
                    <div class="px-2.5 py-1.5 rounded-lg bg-gray-50 dark:bg-slate-700 text-xs" title="{{ $badge->deskripsi }}">
                        {{ $badge->badge_icon }} {{ $badge->badge_name }}
                        <span class="text-[9px] text-gray-400">{{ $badge->earned_at->format('d/m') }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- Reminders & Sleep --}}
        <div class="rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-slate-700">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">⏰ Reminders ({{ $reminders->count() }}) & 😴 Tidur</h3>
            </div>
            <div class="p-4 space-y-2">
                @foreach($reminders as $r)
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-700 dark:text-gray-300">{{ $r->aktif ? '✅' : '❌' }} {{ $r->judul }}</span>
                    <span class="text-gray-400">{{ substr($r->waktu, 0, 5) }}</span>
                </div>
                @endforeach
                @if($reminders->isEmpty())
                <p class="text-xs text-gray-400">Tidak ada reminder.</p>
                @endif

                @if($sleepLogs->count() > 0)
                <div class="pt-2 mt-2 border-t border-gray-100 dark:border-slate-700">
                    <p class="text-[10px] text-gray-500 mb-1">Tidur terakhir:</p>
                    @foreach($sleepLogs->take(3) as $sl)
                    <div class="text-xs text-gray-600 dark:text-gray-400">{{ $sl->tanggal->format('d/m') }}: {{ $sl->jam_tidur }} → {{ $sl->jam_bangun }} ({{ $sl->durasi_jam }}j)</div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Weight Chart --}}
    @if($weightHistory->count() > 1)
    <div class="rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 p-4">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">⚖️ Berat Badan</h3>
        <p class="text-[10px] text-gray-400 mb-3">{{ $weightHistory->first()->berat_kg }}kg → {{ $weightHistory->last()->berat_kg }}kg ({{ $weightHistory->first()->tanggal->format('d/m') }} - {{ $weightHistory->last()->tanggal->format('d/m') }})</p>
        <div class="flex items-end gap-1 h-28">
            @php $ws = $weightHistory->pluck('berat_kg'); $mn = $ws->min()-1; $mx = $ws->max()+1; $rg = $mx-$mn?:1; @endphp
            @foreach($weightHistory->take(30) as $w)
            @php $h = (($w->berat_kg - $mn) / $rg) * 100; @endphp
            <div class="flex-1 flex flex-col items-center justify-end" title="{{ $w->tanggal->format('d/m') }}: {{ $w->berat_kg }}kg">
                <div class="w-full max-w-[10px] rounded-t bg-emerald-500" style="height: {{ $h }}%"></div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Kalori Chart --}}
    @if($summaries->count() > 0)
    <div class="rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 p-4">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">📊 Kalori 14 Hari</h3>
        <div class="flex items-end gap-1 h-28">
            @php $mxK = max($summaries->max('total_kalori'), $tarK); @endphp
            @foreach($summaries as $s)
            @php $h = ($s->total_kalori / $mxK) * 100; $c = $s->total_kalori > $tarK ? 'bg-red-500' : ($s->pct_target >= 80 ? 'bg-emerald-500' : 'bg-amber-400'); @endphp
            <div class="flex-1 flex flex-col items-center justify-end" title="{{ $s->tanggal->format('d/m') }}: {{ $s->total_kalori }} kkal">
                <div class="w-full max-w-[14px] rounded-t {{ $c }}" style="height: {{ $h }}%"></div>
            </div>
            @endforeach
        </div>
        <div class="flex justify-between mt-1 text-[9px] text-gray-400">
            <span>{{ $summaries->first()->tanggal->format('d/m') }}</span>
            <span class="text-emerald-500">Target: {{ $tarK }}</span>
            <span>{{ $summaries->last()->tanggal->format('d/m') }}</span>
        </div>
    </div>
    @endif

    {{-- Profile Detail --}}
    <div class="rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 p-4">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">Detail Profil</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-y-2 gap-x-4 text-xs">
            @php $details = [
                'Gender' => ucfirst($profile->gender ?? '-'),
                'Umur' => ($profile->umur ?? '-') . ' thn',
                'Tinggi' => ($profile->tinggi_cm ?? '-') . ' cm',
                'Berat' => ($profile->berat_kg ?? '-') . ' kg',
                'Target' => ($profile->berat_target ?? '-') . ' kg',
                'BMI' => ($profile->bmi ?? '-') . ' (' . ($profile->bmi ? ($profile->bmi < 18.5 ? 'Under' : ($profile->bmi < 25 ? 'Normal' : ($profile->bmi < 30 ? 'Over' : 'Obese'))) : '-') . ')',
                'BMR' => round($profile->bmr ?? 0) . ' kkal',
                'TDEE' => round($profile->tdee ?? 0) . ' kkal',
                'Body Fat' => ($profile->body_fat_pct ?? '-') . '%',
                'Aktivitas' => ucfirst($profile->level_aktivitas ?? '-'),
                'Goal' => ucfirst($profile->goal ?? '-'),
                'AI Today' => ($profile->ai_requests_today ?? 0) . '/' . ($profile->max_ai_requests ?? 50),
                'Dibuat' => $profile->created_at->format('d/m/Y H:i'),
                'Update' => $profile->updated_at->diffForHumans(),
            ]; @endphp
            @foreach($details as $label => $value)
            <div><span class="text-gray-500">{{ $label }}:</span> <span class="font-medium text-gray-800 dark:text-gray-200">{{ $value }}</span></div>
            @endforeach
        </div>
    </div>

    {{-- MODALS --}}

    {{-- Send Message Modal --}}
    <div x-show="sendMsg" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50" @click="sendMsg = false"></div>
        <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-md p-5 z-10">
            <h3 class="text-sm font-bold text-gray-800 dark:text-white mb-3">💬 Kirim Pesan ke {{ $profile->nama }}</h3>
            <form method="POST" action="{{ route('diet.users.send-message', $profile) }}">
                @csrf
                <textarea name="message" rows="4" placeholder="Tulis pesan (HTML supported)..." required class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 px-3 py-2 text-sm dark:text-gray-200"></textarea>
                <div class="flex gap-2 mt-3">
                    <button type="submit" class="flex-1 rounded-lg bg-blue-600 py-2 text-sm font-medium text-white hover:bg-blue-700">Kirim</button>
                    <button type="button" @click="sendMsg = false" class="px-4 rounded-lg border border-gray-300 dark:border-slate-600 py-2 text-sm text-gray-600 dark:text-gray-400">Batal</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Reset Data Modal --}}
    <div x-show="resetModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50" @click="resetModal = false"></div>
        <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-sm p-5 z-10">
            <h3 class="text-sm font-bold text-gray-800 dark:text-white mb-3">🔄 Reset Data {{ $profile->nama }}</h3>
            <form method="POST" action="{{ route('diet.users.reset-data', $profile) }}">
                @csrf
                <div class="space-y-2">
                    @foreach(['food' => '🍽 Food Logs', 'weight' => '⚖️ Weight Logs', 'water' => '💧 Water Logs', 'exercise' => '🏃 Exercise Logs', 'badges' => '🏆 Badges & Streak', 'reminders' => '⏰ Reminders', 'favorites' => '⭐ Favorites', 'all' => '⚠️ SEMUA DATA'] as $type => $label)
                    <label class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer">
                        <input type="radio" name="type" value="{{ $type }}" {{ $type === 'food' ? 'checked' : '' }} class="text-red-600">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
                <div class="flex gap-2 mt-4">
                    <button type="submit" class="flex-1 rounded-lg bg-amber-600 py-2 text-sm font-medium text-white hover:bg-amber-700" onclick="return confirm('Yakin reset data ini?')">Reset</button>
                    <button type="button" @click="resetModal = false" class="px-4 rounded-lg border border-gray-300 dark:border-slate-600 py-2 text-sm text-gray-600 dark:text-gray-400">Batal</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete User Modal --}}
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50" @click="deleteModal = false"></div>
        <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-sm p-5 z-10">
            <h3 class="text-sm font-bold text-red-600 mb-2">🗑 Hapus User</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Hapus <b>{{ $profile->nama }}</b> dan semua datanya? Aksi ini tidak bisa dibatalkan.</p>
            <form method="POST" action="{{ route('diet.users.destroy', $profile) }}">
                @csrf @method('DELETE')
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 rounded-lg bg-red-600 py-2 text-sm font-medium text-white hover:bg-red-700">Hapus Permanen</button>
                    <button type="button" @click="deleteModal = false" class="px-4 rounded-lg border border-gray-300 dark:border-slate-600 py-2 text-sm text-gray-600 dark:text-gray-400">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
