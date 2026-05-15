@props([
    'for' => null,
    'required' => false,
])

<label
    @if($for) for="{{ $for }}" @endif
    {{ $attributes->merge(['class' => 'form-label']) }}>
    {{ $slot }}
    @if($required)<span class="text-[var(--danger)] ml-0.5">*</span>@endif
</label>
