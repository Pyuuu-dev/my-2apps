@extends('layouts.app')
@section('title', 'Diet Users')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between" x-data="{ broadcastModal: false }">
        <div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Users</h2>
            <p class="text-sm text-gray-500">{{ $users->count() }} pengguna terdaftar</p>
        </div>
        <button @click="broadcastModal = true" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700">📢 Broadcast</button>

        {{-- Broadcast Modal --}}
        <div x-show="broadcastModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50" @click="broadcastModal = false"></div>
            <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-md p-5 z-10">
                <h3 class="text-sm font-bold text-gray-800 dark:text-white mb-3">📢 Broadcast ke Semua User Aktif</h3>
                <form method="POST" action="{{ route('diet.users.broadcast') }}">
                    @csrf
                    <textarea name="message" rows="4" placeholder="Tulis pesan broadcast (HTML supported)..." required class="w-full rounded-lg border border-gray-300 dark:border-slate-600 dark:bg-slate-700 px-3 py-2 text-sm dark:text-gray-200"></textarea>
                    <div class="flex gap-2 mt-3">
                        <button type="submit" class="flex-1 rounded-lg bg-blue-600 py-2 text-sm font-medium text-white hover:bg-blue-700">Kirim ke {{ $users->where('aktif', true)->count() }} user</button>
                        <button type="button" @click="broadcastModal = false" class="px-4 rounded-lg border border-gray-300 dark:border-slate-600 py-2 text-sm text-gray-600 dark:text-gray-400">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="grid gap-3">
        @forelse($users as $user)
        <a href="{{ route('diet.users.show', $user) }}" class="block rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 p-4 hover:border-emerald-300 dark:hover:border-emerald-600 transition-colors">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-bold text-sm">
                        {{ strtoupper(substr($user->nama ?? '?', 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-semibold text-gray-800 dark:text-white text-sm">
                            {{ $user->nama ?? 'Unknown' }}
                            @if($user->username) <span class="text-gray-400 font-normal">@{{ $user->username }}</span> @endif
                        </div>
                        <div class="text-[11px] text-gray-500 flex items-center gap-2 mt-0.5">
                            <span>{{ ucfirst($user->goal ?? '-') }}</span>
                            <span class="text-gray-300">|</span>
                            <span>BMI: {{ $user->bmi ?? '-' }}</span>
                            <span class="text-gray-300">|</span>
                            <span>{{ $user->berat_kg ?? '-' }}kg</span>
                            @if($user->berat_target) <span class="text-gray-300">→</span> <span>{{ $user->berat_target }}kg</span> @endif
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="flex items-center gap-3 text-[11px] text-gray-500">
                        <span title="Food logs">🍽 {{ $user->food_logs_count }}</span>
                        <span title="Exercise">🏃 {{ $user->exercise_logs_count }}</span>
                        <span title="Badges">🏆 {{ $user->badges_count }}</span>
                    </div>
                    <div class="text-[10px] text-gray-400 mt-1">
                        {{ $user->updated_at->diffForHumans() }}
                    </div>
                </div>
            </div>
            @if($user->kalori_target)
            @php
                $todayKal = $user->foodLogs()->whereDate('tanggal', now('Asia/Singapore')->toDateString())->sum('kalori');
                $pct = min(100, round(($todayKal / $user->kalori_target) * 100));
            @endphp
            <div class="mt-3 flex items-center gap-2">
                <div class="flex-1 h-1.5 rounded-full bg-gray-100 dark:bg-slate-700 overflow-hidden">
                    <div class="h-full rounded-full {{ $pct > 100 ? 'bg-red-500' : ($pct >= 80 ? 'bg-emerald-500' : 'bg-amber-500') }}" style="width: {{ min(100, $pct) }}%"></div>
                </div>
                <span class="text-[10px] text-gray-500 w-20 text-right">{{ $todayKal }}/{{ $user->kalori_target }}</span>
            </div>
            @endif
        </a>
        @empty
        <div class="text-center py-12 text-gray-400">
            <p class="text-lg">Belum ada user</p>
            <p class="text-sm mt-1">User akan muncul setelah menggunakan bot Telegram</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
