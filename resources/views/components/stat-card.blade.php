@props([
    'label' => '',
    'value' => '',
    'sub' => null,
    'icon' => null, // SVG path d= attribute
    'tone' => 'neutral', // neutral | success | warning | danger | info | accent
    'trend' => null, // 'up' | 'down' | 'flat' | null
    'trendLabel' => null, // optional text inside the trend pill (e.g. "+12%")
])

@php
    $valueTones = [
        'neutral' => 'text-[var(--text)]',
        'success' => 'text-[var(--success)]',
        'warning' => 'text-[var(--warning)]',
        'danger'  => 'text-[var(--danger)]',
        'info'    => 'text-[var(--info)]',
        'accent'  => 'text-[var(--accent)]',
    ];
    $valueClass = $valueTones[$tone] ?? $valueTones['neutral'];

    $iconRingClass = match ($tone) {
        'success' => 'icon-ring icon-ring-success',
        'warning' => 'icon-ring icon-ring-warning',
        'danger'  => 'icon-ring icon-ring-danger',
        'info'    => 'icon-ring icon-ring-info',
        'accent'  => 'icon-ring icon-ring-accent',
        default   => 'icon-ring',
    };

    $trendPill = $trend ? match ($trend) {
        'up'   => 'trend-pill trend-up',
        'down' => 'trend-pill trend-down',
        'flat' => 'trend-pill trend-flat',
        default => null,
    } : null;
@endphp

<div {{ $attributes->merge(['class' => 'stat-card hover-lift']) }}>
    <div class="flex items-start justify-between gap-3 mb-3">
        @if($icon)
        <span class="{{ $iconRingClass }}">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $icon }}"/></svg>
        </span>
        @else
        <div></div>
        @endif

        @if($trendPill && $trendLabel)
        <span class="{{ $trendPill }}">
            @if($trend === 'up')
                <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 17l9.2-9.2M17 17V7H7"/></svg>
            @elseif($trend === 'down')
                <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 7l-9.2 9.2M7 7v10h10"/></svg>
            @endif
            {{ $trendLabel }}
        </span>
        @endif
    </div>

    @if($label)
    <p class="text-[11px] font-semibold uppercase tracking-wider text-[var(--text-muted)] mb-1">{{ $label }}</p>
    @endif

    <p class="text-2xl num font-bold leading-tight {{ $valueClass }}">{{ $value }}</p>

    @if($sub)
    <p class="text-[11px] text-[var(--text-subtle)] mt-1.5">{{ $sub }}</p>
    @endif
    {{ $slot }}
</div>
