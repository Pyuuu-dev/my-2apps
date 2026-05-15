@php
    $alert = app(\App\Services\StockAlertService::class)->getAlerts();
@endphp

@if($alert['has_alerts'])
<div x-data="{ open: false }" class="card border-[var(--warning)]/30 bg-[var(--warning-soft)] mb-5 overflow-hidden">
    <button type="button" @click="open = !open" class="w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-[var(--warning-soft)] transition-colors">
        <span class="icon-ring icon-ring-warning shrink-0">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </span>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-[var(--text)]">
                <span class="num">{{ $alert['total'] }}</span> item perlu restock
            </p>
            <p class="text-[11px] text-[var(--text-muted)] mt-0.5">
                @php
                    $parts = [];
                    if ($alert['counts']['fruit'] > 0) $parts[] = $alert['counts']['fruit'] . ' fruit';
                    if ($alert['counts']['skin'] > 0) $parts[] = $alert['counts']['skin'] . ' skin';
                    if ($alert['counts']['gamepass'] > 0) $parts[] = $alert['counts']['gamepass'] . ' gamepass';
                    if ($alert['counts']['permanent'] > 0) $parts[] = $alert['counts']['permanent'] . ' permanent';
                @endphp
                {{ implode(' · ', $parts) }}
            </p>
        </div>
        <svg class="h-4 w-4 text-[var(--text-muted)] transition-transform shrink-0" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>

    <div x-show="open" x-collapse x-cloak class="border-t border-[var(--warning)]/20 bg-[var(--surface)]">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 p-4">
            @foreach(['fruit' => 'Fruit', 'skin' => 'Skin', 'gamepass' => 'Gamepass', 'permanent' => 'Permanent'] as $key => $label)
                @if(count($alert['by_kategori'][$key]) > 0)
                <div class="py-2">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-[var(--text-subtle)] mb-2">{{ $label }} <span class="num">({{ count($alert['by_kategori'][$key]) }})</span></p>
                    <div class="space-y-1">
                        @foreach($alert['by_kategori'][$key] as $item)
                        <a href="{{ $item['edit_url'] }}" class="flex items-center gap-2 text-xs py-1.5 px-2 rounded-md hover:bg-[var(--surface-2)] transition-colors group">
                            <span class="dot dot-warning shrink-0"></span>
                            <span class="font-medium text-[var(--text)] flex-1 truncate group-hover:text-[var(--accent)]">{{ $item['nama'] }}</span>
                            @if($item['meta'] !== '-')
                            <span class="text-[10px] text-[var(--text-subtle)] shrink-0">{{ $item['meta'] }}</span>
                            @endif
                            <span class="num text-[var(--danger)] font-bold shrink-0">{{ $item['stok'] }}<span class="text-[var(--text-subtle)] font-normal">/{{ $item['threshold'] }}</span></span>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
@endif
