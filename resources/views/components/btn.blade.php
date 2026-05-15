@props([
    'variant' => 'primary', // primary, secondary, success, danger, ghost, outline
    'size' => 'md', // sm, md, lg
    'type' => 'button',
    'href' => null,
    'icon' => null,
    'iconRight' => null, // optional trailing icon path
])

@php
    $base = 'group inline-flex items-center justify-center gap-1.5 font-semibold rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed select-none';

    $sizes = [
        'sm' => 'px-2.5 h-7 text-xs',
        'md' => 'px-3.5 h-9 text-sm',
        'lg' => 'px-5 h-10 text-sm',
    ];

    $variants = [
        'primary'   => 'bg-[var(--accent)] text-white hover:bg-[var(--accent-hover)]',
        'secondary' => 'bg-transparent border border-[var(--border)] text-[var(--text)] hover:bg-[var(--surface-2)] hover:border-[var(--border-hover)]',
        'success'   => 'bg-[var(--success)] text-white hover:opacity-90',
        'danger'    => 'bg-[var(--danger)] text-white hover:opacity-90',
        'ghost'     => 'bg-transparent text-[var(--text-muted)] hover:bg-[var(--surface-2)] hover:text-[var(--text)]',
        'outline'   => 'border border-[var(--border)] text-[var(--text-muted)] hover:bg-[var(--surface-2)] hover:text-[var(--text)]',
    ];

    $classes = $base . ' ' . ($sizes[$size] ?? $sizes['md']) . ' ' . ($variants[$variant] ?? $variants['primary']);

    $iconSize = $size === 'sm' ? 'h-3.5 w-3.5' : ($size === 'lg' ? 'h-4 w-4' : 'h-4 w-4');
@endphp

@if($href)
<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)<svg class="{{ $iconSize }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>@endif
    {{ $slot }}
    @if($iconRight)<svg class="{{ $iconSize }} transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconRight }}"/></svg>@endif
</a>
@else
<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)<svg class="{{ $iconSize }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>@endif
    {{ $slot }}
    @if($iconRight)<svg class="{{ $iconSize }} transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconRight }}"/></svg>@endif
</button>
@endif
