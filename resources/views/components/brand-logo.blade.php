@props(['size' => 'h-4 w-4', 'extraClass' => 'text-white'])
{{-- Default lightning bolt logo (custom logo feature removed) --}}
<svg {{ $attributes->class([$size, $extraClass]) }} fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
</svg>
