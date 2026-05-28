@props(['size' => 'h-4 w-4', 'extraClass' => 'text-white'])
@php
    $logoUrl = setting('store.logo_url');
    $logoSvg = setting('store.logo_svg');
@endphp
@if(!empty($logoUrl))
    {{-- External image URL (PNG/JPG/SVG via http) --}}
    <img src="{{ $logoUrl }}" alt="Logo"
         {{ $attributes->class([$size, 'object-contain']) }}
         loading="lazy" decoding="async">
@elseif(!empty($logoSvg))
    {{-- Inline custom SVG (already sanitized at save time) --}}
    <span {{ $attributes->class([$size, $extraClass]) }} aria-hidden="true">
        {!! $logoSvg !!}
    </span>
@else
    {{-- Default lightning bolt --}}
    <svg {{ $attributes->class([$size, $extraClass]) }} fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
    </svg>
@endif
