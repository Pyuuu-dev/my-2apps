@props([
    'name' => null,
    'options' => [],
    'value' => null,
    'placeholder' => null,
    'required' => false,
])

<select
    @if($name) name="{{ $name }}" id="{{ $attributes->get('id', $name) }}" @endif
    @if($required) required @endif
    {{ $attributes->merge(['class' => 'w-full h-9 px-3 rounded-lg bg-[var(--surface)] border border-[var(--border)] text-[var(--text)] text-sm focus:border-[var(--accent)] focus:ring-0 focus:outline-none transition-colors']) }}>
    @if($placeholder)
    <option value="">{{ $placeholder }}</option>
    @endif
    @if(!empty($options))
        @foreach($options as $key => $label)
        <option value="{{ $key }}" @selected(old($name, $value) == $key)>{{ $label }}</option>
        @endforeach
    @else
        {{ $slot }}
    @endif
</select>
