@extends('layouts.app')
@section('title', 'Diet Tracker')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <x-page-header eyebrow="Modul" title="Diet Tracker" subtitle="Monitoring bot &amp; aktivitas pengguna">
        <x-slot:actions>
            <form method="POST" action="{{ route('diet.webhook.setup') }}" class="inline">
                @csrf
                <x-btn type="submit" variant="success" icon="M13.828 10.172a4 4 0 015.656 0l3 3a4 4 0 11-5.656 5.656l-1.102-1.101m-.758-4.899a4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.1-1.1">
                    Setup Webhook
                </x-btn>
            </form>
            <x-btn :href="route('diet.ai-logs')" variant="primary" icon="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z">
                AI Logs
            </x-btn>
        </x-slot:actions>
    </x-page-header>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="reveal reveal-1">
        <x-stat-card
            label="Total Users"
            :value="$stats['total_users']"
            :sub="$stats['active_users'] . ' aktif'"
            tone="success"
            icon="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
        </div>
        <div class="reveal reveal-2">
        <x-stat-card
            label="Food Logs Hari Ini"
            :value="$stats['total_food_today']"
            :sub="format_angka($stats['total_food_logs']) . ' total'"
            tone="info"
            icon="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </div>
        <div class="reveal reveal-3">
        <x-stat-card
            label="AI Request Hari Ini"
            :value="$stats['ai_requests_today']"
            :sub="format_angka($stats['total_ai_requests']) . ' total'"
            tone="accent"
            icon="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
        </div>
        <div class="reveal reveal-4">
        <x-stat-card
            label="AI Success Rate"
            :value="$stats['ai_success_rate'] . '%'"
            :sub="$stats['avg_response_time'] . 'ms avg'"
            tone="warning"
            :trend="$stats['ai_success_rate'] >= 90 ? 'up' : ($stats['ai_success_rate'] >= 70 ? 'flat' : 'down')"
            icon="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2" />
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-4">
        {{-- User Profiles --}}
        <div class="card overflow-hidden">
            <div class="px-5 py-3.5 border-b border-[var(--border)] flex items-center justify-between">
                <h3 class="text-sm font-semibold text-[var(--text)] flex items-center gap-2">
                    <span class="dot dot-success"></span> User Profiles
                </h3>
                <a href="{{ route('diet.users.index') }}" class="text-xs link-soft">Lihat semua</a>
            </div>
            <div class="divide-y divide-[var(--border)]">
                @forelse($profiles as $profile)
                <div class="px-5 py-3 hover:bg-[var(--surface-2)] transition-colors">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-[var(--accent-soft)] text-[var(--accent)] text-xs font-bold shrink-0">
                                {{ strtoupper(substr($profile->nama ?? '?', 0, 1)) }}
                            </span>
                            <div class="min-w-0">
                                <div class="text-sm font-medium text-[var(--text)] truncate">
                                    {{ $profile->nama ?? 'Unknown' }}
                                </div>
                                <div class="text-[11px] text-[var(--text-subtle)] mt-0.5">
                                    {{ ucfirst($profile->goal ?? '-') }}
                                    <span class="mx-1">·</span>
                                    BMI {{ $profile->bmi ?? '-' }}
                                </div>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-xs font-semibold text-[var(--success)] num">{{ $profile->food_logs_count }} logs</p>
                            <p class="text-[10px] text-[var(--text-subtle)]">{{ $profile->badges_count }} badges</p>
                        </div>
                    </div>
                    @if($profile->kalori_target)
                    @php
                        $todayKalori = (int) ($profile->today_kalori ?? 0);
                        $pct = min(100, round(($todayKalori / max(1, $profile->kalori_target)) * 100));
                    @endphp
                    <div class="mt-2.5 flex items-center gap-2.5">
                        <div class="progress flex-1">
                            <div class="progress-bar {{ $pct > 100 ? 'progress-bar-danger' : 'progress-bar-success' }}" style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="text-[10px] text-[var(--text-subtle)] num shrink-0">{{ $todayKalori }}/{{ $profile->kalori_target }}</span>
                    </div>
                    @endif
                </div>
                @empty
                <x-empty-state icon="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" title="Belum ada user" message="User akan muncul setelah pakai bot Telegram." />
                @endforelse
            </div>
        </div>

        {{-- Recent Food Logs --}}
        <div class="card overflow-hidden">
            <div class="px-5 py-3.5 border-b border-[var(--border)]">
                <h3 class="text-sm font-semibold text-[var(--text)] flex items-center gap-2">
                    <span class="dot dot-warning"></span> Food Logs Terbaru
                </h3>
            </div>
            <div class="divide-y divide-[var(--border)] max-h-[26rem] overflow-y-auto">
                @forelse($recentFoodLogs as $log)
                <div class="px-5 py-2.5 flex items-center justify-between gap-3 hover:bg-[var(--surface-2)] transition-colors">
                    <div class="min-w-0">
                        <p class="text-sm text-[var(--text)] truncate">{{ $log->nama_makanan }}</p>
                        <p class="text-[11px] text-[var(--text-subtle)] mt-0.5">
                            {{ $log->profile->nama ?? '?' }}
                            <span class="mx-1">·</span>
                            {{ $log->waktu_makan }}
                            <span class="mx-1">·</span>
                            {{ $log->sumber }}
                        </p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-xs font-semibold text-[var(--warning)] num">{{ $log->kalori }} kkal</p>
                        <p class="text-[10px] text-[var(--text-subtle)]">{{ format_relatif($log->created_at) }}</p>
                    </div>
                </div>
                @empty
                <x-empty-state icon="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17" message="Belum ada food logs" />
                @endforelse
            </div>
        </div>
    </div>

    {{-- AI Logs --}}
    <div class="card overflow-hidden">
        <div class="px-5 py-3.5 border-b border-[var(--border)] flex items-center justify-between">
            <h3 class="text-sm font-semibold text-[var(--text)] flex items-center gap-2">
                <span class="dot dot-accent"></span> AI Request Logs
            </h3>
            <a href="{{ route('diet.ai-logs') }}" class="text-xs link-soft">Lihat semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-[var(--surface-2)]">
                    <tr>
                        <th class="px-3 py-2 text-left text-[10px] uppercase tracking-wider text-[var(--text-subtle)]">Waktu</th>
                        <th class="px-3 py-2 text-left text-[10px] uppercase tracking-wider text-[var(--text-subtle)]">User</th>
                        <th class="px-3 py-2 text-left text-[10px] uppercase tracking-wider text-[var(--text-subtle)]">Tipe</th>
                        <th class="px-3 py-2 text-left text-[10px] uppercase tracking-wider text-[var(--text-subtle)]">Model</th>
                        <th class="px-3 py-2 text-center text-[10px] uppercase tracking-wider text-[var(--text-subtle)]">Status</th>
                        <th class="px-3 py-2 text-right text-[10px] uppercase tracking-wider text-[var(--text-subtle)]">Response</th>
                        <th class="px-3 py-2 text-right text-[10px] uppercase tracking-wider text-[var(--text-subtle)]">Tokens</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    @forelse($recentAiLogs as $log)
                    <tr class="hover:bg-[var(--surface-2)]">
                        <td class="px-3 py-2 text-[var(--text-muted)] whitespace-nowrap num">{{ $log->created_at->format('d/m H:i') }}</td>
                        <td class="px-3 py-2 text-[var(--text-muted)]">{{ $log->profile->nama ?? '-' }}</td>
                        <td class="px-3 py-2">
                            @php
                                $tone = $log->tipe === 'vision'
                                    ? 'bg-[var(--accent-soft)] text-[var(--accent)]'
                                    : 'bg-[var(--info-soft)] text-[var(--info)]';
                            @endphp
                            <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $tone }}">{{ $log->tipe }}</span>
                        </td>
                        <td class="px-3 py-2 text-[var(--text-subtle)]">{{ $log->model_used }}</td>
                        <td class="px-3 py-2 text-center">
                            @if($log->success)
                                <span class="inline-flex h-4 w-4 items-center justify-center rounded-full bg-[var(--success-soft)] text-[var(--success)] text-[10px]">✓</span>
                            @else
                                <span class="inline-flex h-4 w-4 items-center justify-center rounded-full bg-[var(--danger-soft)] text-[var(--danger)] text-[10px]" title="{{ $log->error_message }}">✗</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-right text-[var(--text-subtle)] num">{{ $log->response_time_ms }}ms</td>
                        <td class="px-3 py-2 text-right text-[var(--text-subtle)] num">{{ $log->tokens_used ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-10">
                        <x-empty-state icon="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z" message="Belum ada AI logs" />
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Weight Chart --}}
    @if(count($weightProgress) > 1)
    <div class="card p-5">
        <h3 class="text-sm font-semibold text-[var(--text)] mb-4 flex items-center gap-2 section-bar">Progress Berat Badan</h3>
        <div x-data="weightChart()" x-init="init()" class="h-48">
            <canvas x-ref="chart"></canvas>
        </div>
    </div>

    <script>
    function weightChart() {
        return {
            init() {
                const ctx = this.$refs.chart.getContext('2d');
                const data = @json($weightProgress->map(fn($w) => ['date' => $w->tanggal->format('d/m'), 'weight' => $w->berat_kg]));
                if (data.length < 2) return;

                const css = getComputedStyle(document.documentElement);
                const colorAccent = css.getPropertyValue('--accent').trim() || '#4f46e5';
                const colorText = css.getPropertyValue('--text-subtle').trim() || '#a3a3a3';
                const colorBorder = css.getPropertyValue('--border').trim() || '#e7e5e4';

                const width = ctx.canvas.parentElement.clientWidth;
                const height = 180;
                const dpr = window.devicePixelRatio || 1;
                ctx.canvas.width = width * dpr;
                ctx.canvas.height = height * dpr;
                ctx.canvas.style.width = width + 'px';
                ctx.canvas.style.height = height + 'px';
                ctx.scale(dpr, dpr);

                const weights = data.map(d => d.weight);
                const minW = Math.min(...weights) - 1;
                const maxW = Math.max(...weights) + 1;
                const range = maxW - minW || 1;

                const padding = { top: 20, right: 20, bottom: 30, left: 40 };
                const chartW = width - padding.left - padding.right;
                const chartH = height - padding.top - padding.bottom;

                // Grid
                ctx.strokeStyle = colorBorder;
                ctx.lineWidth = 1;
                ctx.fillStyle = colorText;
                ctx.font = '10px Inter';
                ctx.textAlign = 'right';
                for (let i = 0; i <= 4; i++) {
                    const y = padding.top + (chartH / 4) * i;
                    ctx.beginPath();
                    ctx.moveTo(padding.left, y);
                    ctx.lineTo(width - padding.right, y);
                    ctx.stroke();
                    const val = (maxW - (range / 4) * i).toFixed(1);
                    ctx.fillText(val, padding.left - 6, y + 3);
                }

                // Area fill (subtle)
                const grad = ctx.createLinearGradient(0, padding.top, 0, padding.top + chartH);
                grad.addColorStop(0, colorAccent + '40');
                grad.addColorStop(1, colorAccent + '00');
                ctx.fillStyle = grad;
                ctx.beginPath();
                data.forEach((d, i) => {
                    const x = padding.left + (chartW / (data.length - 1)) * i;
                    const y = padding.top + chartH - ((d.weight - minW) / range) * chartH;
                    if (i === 0) ctx.moveTo(x, y);
                    else ctx.lineTo(x, y);
                });
                ctx.lineTo(padding.left + chartW, padding.top + chartH);
                ctx.lineTo(padding.left, padding.top + chartH);
                ctx.closePath();
                ctx.fill();

                // Line
                ctx.strokeStyle = colorAccent;
                ctx.lineWidth = 2;
                ctx.beginPath();
                data.forEach((d, i) => {
                    const x = padding.left + (chartW / (data.length - 1)) * i;
                    const y = padding.top + chartH - ((d.weight - minW) / range) * chartH;
                    if (i === 0) ctx.moveTo(x, y);
                    else ctx.lineTo(x, y);
                });
                ctx.stroke();

                // Points
                ctx.fillStyle = colorAccent;
                data.forEach((d, i) => {
                    const x = padding.left + (chartW / (data.length - 1)) * i;
                    const y = padding.top + chartH - ((d.weight - minW) / range) * chartH;
                    ctx.beginPath();
                    ctx.arc(x, y, 3, 0, Math.PI * 2);
                    ctx.fill();
                });

                // X labels
                ctx.fillStyle = colorText;
                ctx.font = '9px Inter';
                ctx.textAlign = 'center';
                const step = Math.max(1, Math.floor(data.length / 6));
                data.forEach((d, i) => {
                    if (i % step === 0 || i === data.length - 1) {
                        const x = padding.left + (chartW / (data.length - 1)) * i;
                        ctx.fillText(d.date, x, height - 6);
                    }
                });
            }
        }
    }
    </script>
    @endif
</div>
@endsection
