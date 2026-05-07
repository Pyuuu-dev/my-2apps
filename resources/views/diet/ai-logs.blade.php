@extends('layouts.app')
@section('title', 'AI Request Logs')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">🤖 AI Request Logs</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Log semua request ke AI model</p>
        </div>
        <a href="{{ route('diet.dashboard') }}" class="text-xs text-indigo-500 hover:text-indigo-400">← Kembali</a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <div class="rounded-xl p-3 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700">
            <div class="text-lg font-bold text-gray-700 dark:text-gray-200">{{ $totalLogs }}</div>
            <div class="text-[10px] text-gray-500">Total Requests</div>
        </div>
        <div class="rounded-xl p-3 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700">
            <div class="text-lg font-bold text-emerald-600">{{ $successCount }}</div>
            <div class="text-[10px] text-gray-500">Success</div>
        </div>
        <div class="rounded-xl p-3 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700">
            <div class="text-lg font-bold text-red-500">{{ $failCount }}</div>
            <div class="text-[10px] text-gray-500">Failed</div>
        </div>
        <div class="rounded-xl p-3 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700">
            <div class="text-lg font-bold text-blue-600">{{ $avgResponseTime }}ms</div>
            <div class="text-[10px] text-gray-500">Avg Response</div>
        </div>
        <div class="rounded-xl p-3 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700">
            <div class="text-lg font-bold text-purple-600">{{ $totalTokens }}</div>
            <div class="text-[10px] text-gray-500">Total Tokens</div>
        </div>
    </div>

    {{-- Logs Table --}}
    <div class="rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-gray-50 dark:bg-slate-700/50">
                    <tr>
                        <th class="px-3 py-2.5 text-left text-gray-500 dark:text-gray-400">Waktu</th>
                        <th class="px-3 py-2.5 text-left text-gray-500 dark:text-gray-400">User</th>
                        <th class="px-3 py-2.5 text-left text-gray-500 dark:text-gray-400">Tipe</th>
                        <th class="px-3 py-2.5 text-left text-gray-500 dark:text-gray-400">Model</th>
                        <th class="px-3 py-2.5 text-left text-gray-500 dark:text-gray-400">Status</th>
                        <th class="px-3 py-2.5 text-left text-gray-500 dark:text-gray-400">Response Time</th>
                        <th class="px-3 py-2.5 text-left text-gray-500 dark:text-gray-400">Tokens</th>
                        <th class="px-3 py-2.5 text-left text-gray-500 dark:text-gray-400">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    @forelse($logs as $log)
                    <tr x-data="{ expanded: false }">
                        <td class="px-3 py-2 text-gray-600 dark:text-gray-300 whitespace-nowrap">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-3 py-2 text-gray-600 dark:text-gray-300">
                            {{ $log->profile->nama ?? '-' }}
                        </td>
                        <td class="px-3 py-2">
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-medium
                                @if($log->tipe === 'vision') bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400
                                @elseif($log->tipe === 'recommendation') bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400
                                @else bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                                @endif">
                                {{ $log->tipe }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-gray-500 font-mono">{{ $log->model_used }}</td>
                        <td class="px-3 py-2">
                            @if($log->success)
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Success</span>
                            @else
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Failed</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-gray-500">{{ $log->response_time_ms }}ms</td>
                        <td class="px-3 py-2 text-gray-500">{{ $log->tokens_used ?? '-' }}</td>
                        <td class="px-3 py-2">
                            <button @click="expanded = !expanded" class="text-indigo-500 hover:text-indigo-400 text-[10px]">
                                <span x-text="expanded ? 'Tutup' : 'Detail'"></span>
                            </button>
                        </td>
                    </tr>
                    <tr x-show="expanded" x-collapse x-cloak>
                        <td colspan="8" class="px-3 py-3 bg-gray-50 dark:bg-slate-900/50">
                            @if($log->error_message)
                            <div class="mb-2">
                                <span class="text-[10px] font-semibold text-red-500">Error:</span>
                                <pre class="text-[10px] text-red-400 mt-1 whitespace-pre-wrap">{{ $log->error_message }}</pre>
                            </div>
                            @endif
                            @if($log->prompt)
                            <div class="mb-2">
                                <span class="text-[10px] font-semibold text-gray-500">Prompt:</span>
                                <pre class="text-[10px] text-gray-600 dark:text-gray-400 mt-1 whitespace-pre-wrap max-h-32 overflow-y-auto">{{ \Illuminate\Support\Str::limit($log->prompt, 500) }}</pre>
                            </div>
                            @endif
                            @if($log->response)
                            <div>
                                <span class="text-[10px] font-semibold text-gray-500">Response:</span>
                                <pre class="text-[10px] text-gray-600 dark:text-gray-400 mt-1 whitespace-pre-wrap max-h-32 overflow-y-auto">{{ \Illuminate\Support\Str::limit($log->response, 500) }}</pre>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-3 py-8 text-center text-gray-400">Belum ada AI logs.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 dark:border-slate-700">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
