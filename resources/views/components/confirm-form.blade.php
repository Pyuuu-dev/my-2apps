{{--
  <x-confirm-form action="..." method="DELETE" message="Hapus item ini?">
      <x-slot:trigger>...button content...</x-slot:trigger>
  </x-confirm-form>

  Renders an inline form with onsubmit confirm. Used to replace native confirm().
--}}
@props([
    'action' => '#',
    'method' => 'POST',
    'message' => 'Yakin?',
    'class' => 'inline',
])

@php
    $methodUpper = strtoupper($method);
    $isSpoofed = !in_array($methodUpper, ['GET', 'POST']);
@endphp

<form method="{{ $isSpoofed ? 'POST' : $methodUpper }}" action="{{ $action }}" onsubmit="return confirm({{ json_encode($message) }})" class="{{ $class }}">
    @if($methodUpper !== 'GET')@csrf @endif
    @if($isSpoofed)@method($methodUpper)@endif
    {{ $slot }}
</form>
