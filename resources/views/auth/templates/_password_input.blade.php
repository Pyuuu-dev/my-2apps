{{--
    Reusable password input dengan toggle show/hide (SVG eye icon).
    Cocok untuk template dengan icon-based toggle. Template yang pakai
    text-based toggle ("SHOW/HIDE") tetap inline custom.

    Variabel input:
      - $inputClass     : Tailwind class untuk <input> (wajib).
      - $toggleClass    : Class untuk button toggle (default neutral).
      - $iconClass      : Class untuk SVG (default 'h-4 w-4').
      - $placeholder    : Placeholder text (default '••••••••').
      - $inputId        : id attribute untuk input (default 'login-password').
--}}
@php
    $inputClass = $inputClass ?? '';
    $toggleClass = $toggleClass ?? 'text-neutral-400 hover:text-neutral-600';
    $iconClass = $iconClass ?? 'h-4 w-4';
    $placeholder = $placeholder ?? '••••••••';
    $inputId = $inputId ?? 'login-password';
@endphp
<div x-data="{ show: false }" class="relative">
    <input :type="show ? 'text' : 'password'" name="password" required
        id="{{ $inputId }}"
        autocomplete="current-password"
        class="{{ $inputClass }} pr-11"
        placeholder="{{ $placeholder }}">
    <button type="button" @click="show = !show"
        :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'"
        class="absolute right-3 top-1/2 -translate-y-1/2 p-1 {{ $toggleClass }}">
        <svg x-show="!show" aria-hidden="true" class="{{ $iconClass }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
        <svg x-show="show" x-cloak aria-hidden="true" class="{{ $iconClass }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
    </button>
</div>
