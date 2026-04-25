{{--
    Date Navigator with active date indicators
    @param string $tanggal - currently selected date (Y-m-d)
    @param array $tanggalAktif - array of dates that have data (Y-m-d)
    @param string $route - route name to navigate to
    @param string $accent - color accent: 'emerald' (default) or 'blue'
--}}
@php
    $isBlue = ($accent ?? 'emerald') === 'blue';
    $today = now()->format('Y-m-d');
    $selected = $tanggal ?? $today;
    $selectedCarbon = \Carbon\Carbon::parse($selected);

    $days = collect();
    for ($i = -15; $i <= 7; $i++) {
        $d = $selectedCarbon->copy()->addDays($i);
        $days->push([
            'date' => $d->format('Y-m-d'),
            'day' => $d->translatedFormat('D'),
            'num' => $d->format('d'),
            'isToday' => $d->format('Y-m-d') === $today,
            'isSelected' => $d->format('Y-m-d') === $selected,
            'hasData' => in_array($d->format('Y-m-d'), $tanggalAktif ?? []),
        ]);
    }
@endphp

<div x-data="dateNav()" class="mb-5">
    <div class="flex items-center justify-between mb-2">
        <div class="flex items-center gap-2">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ $selectedCarbon->translatedFormat('F Y') }}</h3>
            @if($selected !== $today)
                <a href="{{ route($route) }}" class="text-[11px] font-medium hover:underline {{ $isBlue ? 'text-blue-600' : 'text-emerald-600' }}">Hari ini</a>
            @endif
        </div>
        <div class="flex items-center gap-2">
            <span class="flex items-center gap-1 text-[10px] text-gray-400">
                <span class="inline-block h-1.5 w-1.5 rounded-full {{ $isBlue ? 'bg-blue-500' : 'bg-emerald-500' }}"></span> ada data
            </span>
            <input type="date" value="{{ $selected }}"
                class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-slate-700 dark:text-gray-200 text-xs shadow-sm w-[130px] {{ $isBlue ? 'focus:border-blue-500 focus:ring-blue-500' : 'focus:border-emerald-500 focus:ring-emerald-500' }}"
                onchange="window.location.href='{{ route($route) }}?tanggal='+this.value">
        </div>
    </div>

    <div class="relative">
        <div x-ref="strip" class="flex gap-1 overflow-x-auto pb-2 scroll-smooth hide-scrollbar">
            @foreach($days as $d)
            @php
                if ($d['isSelected']) {
                    $cls = $isBlue
                        ? 'bg-gradient-to-b from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/30'
                        : 'bg-gradient-to-b from-emerald-500 to-emerald-600 text-white shadow-md shadow-emerald-500/30';
                } elseif ($d['isToday']) {
                    $cls = $isBlue
                        ? 'bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 text-blue-700 dark:text-blue-400'
                        : 'bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700 text-emerald-700 dark:text-emerald-400';
                } else {
                    $cls = $isBlue
                        ? 'bg-white dark:bg-slate-800 border border-gray-100 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:border-blue-200 dark:hover:border-blue-600 hover:bg-blue-50/50'
                        : 'bg-white dark:bg-slate-800 border border-gray-100 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:border-emerald-200 dark:hover:border-emerald-600 hover:bg-emerald-50/50';
                }
                $dotCls = $d['isSelected'] ? 'bg-white' : ($isBlue ? 'bg-blue-500' : 'bg-emerald-500');
            @endphp
            <a href="{{ route($route, ['tanggal' => $d['date']]) }}"
                class="shrink-0 w-12 rounded-xl py-1.5 text-center transition-all relative {{ $cls }}"
                data-date="{{ $d['date'] }}" {!! $d['isSelected'] ? 'x-ref="selected"' : '' !!}>
                <p class="text-[9px] font-medium {{ $d['isSelected'] ? 'text-white/70' : 'text-gray-400 dark:text-gray-500' }}">{{ $d['day'] }}</p>
                <p class="text-sm font-extrabold leading-tight">{{ $d['num'] }}</p>
                @if($d['hasData'])
                    <span class="absolute bottom-1 left-1/2 -translate-x-1/2 h-1.5 w-1.5 rounded-full {{ $dotCls }}"></span>
                @endif
            </a>
            @endforeach
        </div>
    </div>
</div>

<style>
.hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
.hide-scrollbar::-webkit-scrollbar { display: none; }
</style>

<script>
function dateNav() {
    return {
        init() {
            this.$nextTick(() => {
                const sel = this.$refs.selected;
                if (sel) {
                    const strip = this.$refs.strip;
                    const offset = sel.offsetLeft - strip.offsetWidth / 2 + sel.offsetWidth / 2;
                    strip.scrollLeft = offset;
                }
            });
        }
    }
}
</script>
