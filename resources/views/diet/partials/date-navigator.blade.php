{{--
    Date Navigator with calendar popup & active date indicators
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
@endphp

<div x-data="calendarNav('{{ $selected }}', @js($tanggalAktif ?? []), '{{ route($route) }}')" class="mb-5">
    {{-- Header row --}}
    <div class="flex items-center justify-between mb-2">
        <div class="flex items-center gap-2">
            {{-- Calendar toggle button --}}
            <button @click="open = !open" class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-sm font-bold transition-colors {{ $isBlue ? 'text-blue-700 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20' : 'text-emerald-700 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20' }}">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span x-text="monthYearLabel"></span>
                <svg class="h-3 w-3 transition-transform" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            @if($selected !== $today)
                <a href="{{ route($route) }}" class="text-[11px] font-medium hover:underline {{ $isBlue ? 'text-blue-600' : 'text-emerald-600' }}">Hari ini</a>
            @endif
        </div>
        <span class="flex items-center gap-1 text-[10px] text-gray-400">
            <span class="inline-block h-1.5 w-1.5 rounded-full {{ $isBlue ? 'bg-blue-500' : 'bg-emerald-500' }}"></span> ada data
        </span>
    </div>

    {{-- Calendar popup --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click.outside="open = false" class="mb-3 glass-card rounded-2xl p-4 shadow-lg border border-gray-100 dark:border-gray-700" x-cloak>
        {{-- Month navigation --}}
        <div class="flex items-center justify-between mb-3">
            <button @click="prevMonth()" class="rounded-lg p-1.5 hover:bg-gray-100 dark:hover:bg-slate-700 text-gray-500">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <span class="text-sm font-bold text-gray-800 dark:text-gray-200" x-text="calMonthYear"></span>
            <button @click="nextMonth()" class="rounded-lg p-1.5 hover:bg-gray-100 dark:hover:bg-slate-700 text-gray-500">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>

        {{-- Day headers --}}
        <div class="grid grid-cols-7 mb-1">
            <template x-for="d in ['Sen','Sel','Rab','Kam','Jum','Sab','Min']">
                <div class="text-center text-[10px] font-semibold text-gray-400 py-1" x-text="d"></div>
            </template>
        </div>

        {{-- Calendar grid --}}
        <div class="grid grid-cols-7 gap-0.5">
            <template x-for="cell in calDays" :key="cell.key">
                <template x-if="cell.empty">
                    <div class="h-9"></div>
                </template>
            </template>
            <template x-for="cell in calDays" :key="cell.key">
                <template x-if="!cell.empty">
                    <a :href="baseUrl + '?tanggal=' + cell.date"
                        class="relative h-9 flex items-center justify-center rounded-lg text-xs font-medium transition-all"
                        :class="{
                            '{{ $isBlue ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-500/30' : 'bg-emerald-600 text-white font-bold shadow-md shadow-emerald-500/30' }}': cell.isSelected,
                            '{{ $isBlue ? 'ring-2 ring-blue-400 text-blue-700 dark:text-blue-400 font-bold' : 'ring-2 ring-emerald-400 text-emerald-700 dark:text-emerald-400 font-bold' }}': cell.isToday && !cell.isSelected,
                            'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700': !cell.isSelected && !cell.isToday,
                        }">
                        <span x-text="cell.num"></span>
                        <template x-if="cell.hasData && !cell.isSelected">
                            <span class="absolute bottom-0.5 left-1/2 -translate-x-1/2 h-1 w-1 rounded-full {{ $isBlue ? 'bg-blue-500' : 'bg-emerald-500' }}"></span>
                        </template>
                        <template x-if="cell.hasData && cell.isSelected">
                            <span class="absolute bottom-0.5 left-1/2 -translate-x-1/2 h-1 w-1 rounded-full bg-white"></span>
                        </template>
                    </a>
                </template>
            </template>
        </div>
    </div>

    {{-- Date strip (always visible) --}}
    @php
        $days = collect();
        for ($i = -10; $i <= 5; $i++) {
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
    <div class="relative">
        <div x-ref="strip" class="flex gap-1 overflow-x-auto pb-1 scroll-smooth hide-scrollbar">
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
                class="shrink-0 w-11 rounded-xl py-1.5 text-center transition-all relative {{ $cls }}"
                {!! $d['isSelected'] ? 'x-ref="selected"' : '' !!}>
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
function calendarNav(selected, activeDates, baseUrl) {
    const sel = new Date(selected + 'T00:00:00');
    const todayStr = new Date().toISOString().slice(0, 10);
    const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

    return {
        open: false,
        baseUrl: baseUrl,
        calMonth: sel.getMonth(),
        calYear: sel.getFullYear(),
        selected: selected,
        activeDates: new Set(activeDates),

        get monthYearLabel() {
            const d = new Date(this.selected + 'T00:00:00');
            return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
        },
        get calMonthYear() {
            return months[this.calMonth] + ' ' + this.calYear;
        },
        get calDays() {
            const first = new Date(this.calYear, this.calMonth, 1);
            const lastDay = new Date(this.calYear, this.calMonth + 1, 0).getDate();
            let startDay = first.getDay() - 1;
            if (startDay < 0) startDay = 6;

            const cells = [];
            for (let i = 0; i < startDay; i++) {
                cells.push({ key: 'e' + i, empty: true });
            }
            for (let d = 1; d <= lastDay; d++) {
                const mm = String(this.calMonth + 1).padStart(2, '0');
                const dd = String(d).padStart(2, '0');
                const dateStr = this.calYear + '-' + mm + '-' + dd;
                cells.push({
                    key: dateStr,
                    empty: false,
                    date: dateStr,
                    num: d,
                    isToday: dateStr === todayStr,
                    isSelected: dateStr === this.selected,
                    hasData: this.activeDates.has(dateStr),
                });
            }
            return cells;
        },
        prevMonth() {
            if (this.calMonth === 0) { this.calMonth = 11; this.calYear--; }
            else { this.calMonth--; }
        },
        nextMonth() {
            if (this.calMonth === 11) { this.calMonth = 0; this.calYear++; }
            else { this.calMonth++; }
        },
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
