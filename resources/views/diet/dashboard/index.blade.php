@extends('layouts.app')
@section('title', 'Diet Tracker - Admin Panel')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Diet Tracker Admin</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Monitoring bot & aktivitas pengguna</p>
        </div>
        <div class="flex gap-2">
            <form method="POST" action="{{ route('diet.webhook.setup') }}">
                @csrf
                <button type="submit" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition-colors">
                    🔗 Setup Webhook
                </button>
            </form>
            <a href="{{ route('diet.ai-logs') }}" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-colors">
                🤖 AI Logs
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="rounded-xl p-4 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700">
            <div class="text-2xl font-bold text-emerald-600">{{ $stats['total_users'] }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total Users</div>
        </div>
        <div class="rounded-xl p-4 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['total_food_today'] }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Food Logs Hari Ini</div>
        </div>
        <div class="rounded-xl p-4 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700">
            <div class="text-2xl font-bold text-purple-600">{{ $stats['ai_requests_today'] }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">AI Request Hari Ini</div>
        </div>
        <div class="rounded-xl p-4 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700">
            <div class="text-2xl font-bold text-amber-600">{{ $stats['ai_success_rate'] }}%</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">AI Success Rate</div>
        </div>
    </div>

    {{-- Secondary Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="rounded-xl p-3 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700">
            <div class="text-lg font-semibold text-gray-700 dark:text-gray-200">{{ $stats['total_food_logs'] }}</div>
            <div class="text-[10px] text-gray-500">Total Food Logs</div>
        </div>
        <div class="rounded-xl p-3 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700">
            <div class="text-lg font-semibold text-gray-700 dark:text-gray-200">{{ $stats['total_ai_requests'] }}</div>
            <div class="text-[10px] text-gray-500">Total AI Requests</div>
        </div>
        <div class="rounded-xl p-3 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700">
            <div class="text-lg font-semibold text-gray-700 dark:text-gray-200">{{ $stats['avg_response_time'] }}ms</div>
            <div class="text-[10px] text-gray-500">Avg AI Response</div>
        </div>
        <div class="rounded-xl p-3 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700">
            <div class="text-lg font-semibold text-gray-700 dark:text-gray-200">{{ $stats['active_users'] }}</div>
            <div class="text-[10px] text-gray-500">Active Users</div>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        {{-- User Profiles --}}
        <div class="rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-slate-700">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">👤 User Profiles</h3>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($profiles as $profile)
                <div class="px-4 py-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                {{ $profile->nama ?? 'Unknown' }}
                                @if($profile->username) <span class="text-gray-400">@{{ $profile->username }}</span> @endif
                            </div>
                            <div class="text-[10px] text-gray-500 mt-0.5">
                                Chat ID: {{ $profile->telegram_chat_id }} |
                                Goal: {{ ucfirst($profile->goal ?? '-') }} |
                                BMI: {{ $profile->bmi ?? '-' }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs font-medium text-emerald-600">{{ $profile->food_logs_count }} logs</div>
                            <div class="text-[10px] text-gray-400">{{ $profile->badges_count }} badges</div>
                        </div>
                    </div>
                    @if($profile->kalori_target)
                    <div class="mt-2 flex items-center gap-2">
                        <div class="flex-1 h-1.5 rounded-full bg-gray-200 dark:bg-slate-600 overflow-hidden">
                            @php
                                $todayKalori = $profile->foodLogs()->whereDate('tanggal', $today)->sum('kalori');
                                $pct = min(100, round(($todayKalori / $profile->kalori_target) * 100));
                            @endphp
                            <div class="h-full rounded-full {{ $pct > 100 ? 'bg-red-500' : 'bg-emerald-500' }}" style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="text-[10px] text-gray-500">{{ $todayKalori }}/{{ $profile->kalori_target }}</span>
                    </div>
                    @endif
                </div>
                @empty
                <div class="px-4 py-6 text-center text-sm text-gray-400">
                    Belum ada user. Bot belum digunakan.
                </div>
                @endforelse
            </div>
        </div>

        {{-- Recent Food Logs --}}
        <div class="rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-slate-700">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">🍽 Food Logs Terbaru</h3>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-slate-700 max-h-96 overflow-y-auto">
                @forelse($recentFoodLogs as $log)
                <div class="px-4 py-2.5">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-gray-800 dark:text-gray-200">{{ $log->nama_makanan }}</div>
                            <div class="text-[10px] text-gray-500">
                                {{ $log->profile->nama ?? '?' }} •
                                {{ $log->waktu_makan }} •
                                {{ $log->sumber }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs font-medium text-orange-600">{{ $log->kalori }} kkal</div>
                            <div class="text-[10px] text-gray-400">{{ $log->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-4 py-6 text-center text-sm text-gray-400">Belum ada food logs.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- AI Logs --}}
    <div class="rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">🤖 AI Request Logs (Terbaru)</h3>
            <a href="{{ route('diet.ai-logs') }}" class="text-xs text-indigo-500 hover:text-indigo-400">Lihat Semua →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-gray-50 dark:bg-slate-700/50">
                    <tr>
                        <th class="px-3 py-2 text-left text-gray-500 dark:text-gray-400">Waktu</th>
                        <th class="px-3 py-2 text-left text-gray-500 dark:text-gray-400">User</th>
                        <th class="px-3 py-2 text-left text-gray-500 dark:text-gray-400">Tipe</th>
                        <th class="px-3 py-2 text-left text-gray-500 dark:text-gray-400">Model</th>
                        <th class="px-3 py-2 text-left text-gray-500 dark:text-gray-400">Status</th>
                        <th class="px-3 py-2 text-left text-gray-500 dark:text-gray-400">Response</th>
                        <th class="px-3 py-2 text-left text-gray-500 dark:text-gray-400">Tokens</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    @forelse($recentAiLogs as $log)
                    <tr>
                        <td class="px-3 py-2 text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ $log->created_at->format('d/m H:i') }}</td>
                        <td class="px-3 py-2 text-gray-600 dark:text-gray-300">{{ $log->profile->nama ?? '-' }}</td>
                        <td class="px-3 py-2">
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-medium
                                {{ $log->tipe === 'vision' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' }}">
                                {{ $log->tipe }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-gray-500">{{ $log->model_used }}</td>
                        <td class="px-3 py-2">
                            @if($log->success)
                                <span class="text-emerald-600">✓</span>
                            @else
                                <span class="text-red-500" title="{{ $log->error_message }}">✗</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-gray-500">{{ $log->response_time_ms }}ms</td>
                        <td class="px-3 py-2 text-gray-500">{{ $log->tokens_used ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-3 py-6 text-center text-gray-400">Belum ada AI logs.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Weight Chart (if data exists) --}}
    @if(count($weightProgress) > 1)
    <div class="rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 p-4">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">⚖️ Progress Berat Badan</h3>
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

                // Simple line chart using canvas
                const width = ctx.canvas.parentElement.clientWidth;
                const height = 180;
                ctx.canvas.width = width;
                ctx.canvas.height = height;

                if (data.length < 2) return;

                const weights = data.map(d => d.weight);
                const minW = Math.min(...weights) - 1;
                const maxW = Math.max(...weights) + 1;
                const range = maxW - minW || 1;

                const padding = { top: 20, right: 20, bottom: 30, left: 40 };
                const chartW = width - padding.left - padding.right;
                const chartH = height - padding.top - padding.bottom;

                // Draw grid
                ctx.strokeStyle = document.documentElement.classList.contains('dark') ? '#334155' : '#e5e7eb';
                ctx.lineWidth = 0.5;
                for (let i = 0; i <= 4; i++) {
                    const y = padding.top + (chartH / 4) * i;
                    ctx.beginPath();
                    ctx.moveTo(padding.left, y);
                    ctx.lineTo(width - padding.right, y);
                    ctx.stroke();

                    // Labels
                    ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#94a3b8' : '#6b7280';
                    ctx.font = '10px Inter';
                    ctx.textAlign = 'right';
                    const val = (maxW - (range / 4) * i).toFixed(1);
                    ctx.fillText(val, padding.left - 5, y + 3);
                }

                // Draw line
                ctx.strokeStyle = '#10b981';
                ctx.lineWidth = 2;
                ctx.beginPath();

                data.forEach((d, i) => {
                    const x = padding.left + (chartW / (data.length - 1)) * i;
                    const y = padding.top + chartH - ((d.weight - minW) / range) * chartH;

                    if (i === 0) ctx.moveTo(x, y);
                    else ctx.lineTo(x, y);
                });
                ctx.stroke();

                // Draw points
                data.forEach((d, i) => {
                    const x = padding.left + (chartW / (data.length - 1)) * i;
                    const y = padding.top + chartH - ((d.weight - minW) / range) * chartH;

                    ctx.fillStyle = '#10b981';
                    ctx.beginPath();
                    ctx.arc(x, y, 3, 0, Math.PI * 2);
                    ctx.fill();
                });

                // X labels (show every few)
                ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#94a3b8' : '#6b7280';
                ctx.font = '9px Inter';
                ctx.textAlign = 'center';
                const step = Math.max(1, Math.floor(data.length / 6));
                data.forEach((d, i) => {
                    if (i % step === 0 || i === data.length - 1) {
                        const x = padding.left + (chartW / (data.length - 1)) * i;
                        ctx.fillText(d.date, x, height - 5);
                    }
                });
            }
        }
    }
    </script>
    @endif
</div>
@endsection
