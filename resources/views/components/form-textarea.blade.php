@props([
    'name' => null,
    'rows' => 3,
    'value' => null,
    'placeholder' => null,
    'required' => false,
])

<textarea
    @if($name) name="{{ $name }}" id="{{ $attributes->get('id', $name) }}" @endif
    rows="{{ $rows }}"
    @if($placeholder) placeholder="{{ $placeholder }}" @endif
    @if($required) required @endif
    {{ $attributes->merge(['class' => 'w-full px-3 py-2 rounded-lg bg-[var(--surface)] border border-[var(--border)] text-[var(--text)] text-sm placeholder:text-[var(--text-subtle)] focus:border-[var(--accent)] focus:ring-0 focus:outline-none transition-colors']) }}>{{ old($name, $value) }}</textarea>
