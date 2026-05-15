@props([
    'name' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'required' => false,
])

<input
    type="{{ $type }}"
    @if($name) name="{{ $name }}" id="{{ $attributes->get('id', $name) }}" @endif
    value="{{ old($name, $value) }}"
    @if($placeholder) placeholder="{{ $placeholder }}" @endif
    @if($required) required @endif
    {{ $attributes->merge(['class' => 'w-full h-9 px-3 rounded-lg bg-[var(--surface)] border border-[var(--border)] text-[var(--text)] text-sm placeholder:text-[var(--text-subtle)] focus:border-[var(--accent)] focus:ring-0 focus:outline-none transition-colors']) }}>
