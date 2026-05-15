@props([
    'name' => 'modal',
    'title' => null,
    'maxWidth' => 'max-w-lg',
])

<div
    x-data="{ open: false }"
    x-on:open-modal-{{ $name }}.window="open = true; document.body.style.overflow = 'hidden'"
    x-on:close-modal-{{ $name }}.window="open = false; document.body.style.overflow = ''"
    x-on:keydown.escape.window="if(open){ open = false; document.body.style.overflow = '' }"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4">

    {{-- Backdrop --}}
    <div
        x-show="open"
        x-transition:enter="transition-opacity duration-150"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-100"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        @click="open = false; document.body.style.overflow = ''"
        class="fixed inset-0 bg-black/40 dark:bg-black/60"></div>

    {{-- Panel --}}
    <div
        x-show="open"
        x-transition:enter="transition duration-150 ease-out"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition duration-100 ease-in"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @click.stop
        {{ $attributes->merge(['class' => "relative w-full {$maxWidth} rounded-2xl bg-[var(--surface)] border border-[var(--border)] shadow-[var(--elev-3)] overflow-hidden"]) }}>

        @if($title)
        <div class="px-5 py-3.5 border-b border-[var(--border)] flex items-center justify-between">
            <h3 class="font-semibold text-[var(--text)] text-sm">{{ $title }}</h3>
            <button @click="open = false; document.body.style.overflow = ''" class="rounded-md p-1 text-[var(--text-subtle)] hover:text-[var(--text)] hover:bg-[var(--surface-2)] transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        @endif

        {{ $slot }}

        @isset($footer)
        <div class="px-5 py-3 border-t border-[var(--border)] bg-[var(--surface-2)] flex items-center justify-end gap-2">
            {{ $footer }}
        </div>
        @endisset
    </div>
</div>
