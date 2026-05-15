@props([
    'icon' => null,
    'title' => null,
    'message' => 'Belum ada data',
    'compact' => false,
])

<div {{ $attributes->merge(['class' => $compact ? 'py-6 px-4 text-center' : 'py-12 px-4 text-center']) }}>
    @if($icon)
    <div class="mx-auto mb-3 relative flex h-14 w-14 items-center justify-center rounded-full empty-state-icon-bg">
        <span class="absolute inset-0 rounded-full ring-1 ring-[var(--border)]"></span>
        <span class="absolute -inset-1.5 rounded-full ring-1 ring-[var(--border)] opacity-40"></span>
        <svg class="relative h-5 w-5 text-[var(--text-muted)]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $icon }}"/></svg>
    </div>
    @endif
    @if($title)
    <p class="text-sm font-semibold text-[var(--text)]">{{ $title }}</p>
    @endif
    <p class="text-xs text-[var(--text-subtle)] mt-1">{{ $message }}</p>
    {{ $slot }}
</div>
