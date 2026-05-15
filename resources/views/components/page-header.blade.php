@props([
    'title' => '',
    'subtitle' => null,
    'eyebrow' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6']) }}>
    <div class="min-w-0">
        @if($eyebrow)
        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[var(--accent)] mb-2 section-bar">{{ $eyebrow }}</p>
        @endif
        @if($title)
        <h1 class="text-2xl sm:text-[28px] leading-tight font-bold tracking-tight text-[var(--text)] truncate">{{ $title }}</h1>
        @endif
        @if($subtitle)
        <p class="text-sm text-[var(--text-muted)] mt-1">{{ $subtitle }}</p>
        @endif
        {{ $slot }}
    </div>
    @isset($actions)
    <div class="flex items-center gap-2 flex-wrap shrink-0">
        {{ $actions }}
    </div>
    @endisset
</div>
