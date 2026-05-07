@extends('layouts.app')
@section('title', 'Diet Statistik')

@section('content')
<div class="space-y-5">
    <h2 class="text-xl font-bold text-gray-800 dark:text-white">Statistik</h2>

    {{-- Overview --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="rounded-xl p-4 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700">
            <div class="text-2xl font-bold text-emerald-600">{{ number_format($totalFoodLogs) }}</div>
            <div class="text-xs text-gray-500 mt-1">Total Food Logs</div>
        </div>
        <div class="rounded-xl p-4 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700">
            <div class="text-2xl font-bold text-blue-600">{{ $totalFoodToday }}</div>
            <div class="text-xs text-gray-500 mt-1">Hari Ini</div>
        </div>
        <div class="rounded-xl p-4 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700">
            <div class="text-2xl font-bold text-purple-600">{{ number_format($totalAiRequests) }}</div>
            <div class="text-xs text-gray-500 mt-1">AI Requests</div>
        </div>
        <div class="rounded-xl p-4 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700">
            <div class="text-2xl font-bold text-amber-600">{{ $aiToday }}</div>
            <div class="text-xs text-gray-500 mt-1">AI Hari Ini</div>
        </div>
    </div>

    {{-- Kalori Chart --}}
    @if($chartData->count() > 0)
    <div class="rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 p-4">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">Rata-rata Kalori Harian (14 hari)</h3>
        <div class="flex items-end gap-1 h-40">
            @php $maxK = max($chartData->max('kalori'), 1); @endphp
            @foreach($chartData as $date => $data)
            @php $h = ($data['kalori'] / $maxK) * 100; @endphp
            <div class="flex-1 flex flex-col items-center justify-end" title="{{ $date }}: {{ $data['kalori'] }} kkal">
                <div class="text-[8px] text-gray-400 mb-0.5">{{ $data['kalori'] }}</div>
                <div class="w-full max-w-[20px] rounded-t bg-gradient-to-t from-orange-500 to-amber-400" style="height: {{ $h }}%"></div>
                <div class="text-[8px] text-gray-400 mt-1">{{ $date }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="grid md:grid-cols-2 gap-5">
        {{-- Top Foods --}}
        <div class="rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-slate-700">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Top 10 Makanan</h3>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-slate-700">
                @foreach($topFoods as $i => $food)
                <div class="px-4 py-2 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-400 w-4">{{ $i + 1 }}.</span>
                        <span class="text-sm text-gray-800 dark:text-gray-200">{{ $food->nama_makanan }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-medium text-orange-600">{{ round($food->avg_kalori) }} kkal</span>
                        <span class="text-[10px] text-gray-400 ml-1">x{{ $food->total }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- AI Model Usage --}}
        <div class="rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-slate-700">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">AI Model Usage</h3>
            </div>
            <div class="p-4 space-y-3">
                @foreach($aiByModel as $model)
                <div class="rounded-lg bg-gray-50 dark:bg-slate-700 p-3">
                    <div class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $model->model_used }}</div>
                    <div class="grid grid-cols-3 gap-2 mt-2 text-[11px]">
                        <div><span class="text-gray-500">Requests:</span> <span class="font-medium">{{ $model->total }}</span></div>
                        <div><span class="text-gray-500">Avg time:</span> <span class="font-medium">{{ round($model->avg_time) }}ms</span></div>
                        <div><span class="text-gray-500">Tokens:</span> <span class="font-medium">{{ number_format($model->total_tokens ?? 0) }}</span></div>
                    </div>
                </div>
                @endforeach
                @if($aiByModel->isEmpty())
                <p class="text-sm text-gray-400 text-center">Belum ada data AI.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Hourly Activity --}}
    @if($hourlyActivity->count() > 0)
    <div class="rounded-xl bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 p-4">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">Aktivitas per Jam</h3>
        <div class="flex items-end gap-0.5 h-24">
            @php $maxH = max($hourlyActivity->max(), 1); @endphp
            @for($i = 0; $i < 24; $i++)
            @php $val = $hourlyActivity[str_pad($i, 2, '0', STR_PAD_LEFT)] ?? 0; $h = ($val / $maxH) * 100; @endphp
            <div class="flex-1 flex flex-col items-center justify-end" title="{{ $i }}:00 - {{ $val }} logs">
                <div class="w-full rounded-t {{ $val > 0 ? 'bg-emerald-500' : 'bg-gray-200 dark:bg-slate-600' }}" style="height: {{ max(2, $h) }}%"></div>
            </div>
            @endfor
        </div>
        <div class="flex justify-between mt-1 text-[9px] text-gray-400">
            <span>00</span><span>06</span><span>12</span><span>18</span><span>23</span>
        </div>
    </div>
    @endif
</div>
@endsection
