@props(['size' => 'h-4 w-4', 'extraClass' => 'text-white'])
@php
    $logoSvg = setting('store.logo_svg');
@endphp
@if(!empty($logoSvg))
    {{-- Inline custom SVG (already sanitized at save time). Wrap in span with size classes --}}
    <span {{ $attributes->class([$size, $extraClass]) }} aria-hidden="true">
        {!! $logoSvg !!}
    </span>
@else
    {{-- Default lightning bolt --}}
    <svg {{ $attributes->class([$size, $extraClass]) }} fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
    </svg>
@endif
