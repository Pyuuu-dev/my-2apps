{{--
    Shared <head> partial untuk semua login templates.
    Variabel input (opsional, ada default):
      - $themeColor : warna meta theme-color (default BrandingService::THEME_COLOR)
      - $extraFonts : URL Google Fonts tambahan (Inter sudah preload by default)
      - $turnstileTheme : 'dark' | 'light' (default 'dark')
--}}
@php
    $branding = app(\App\Services\BrandingService::class);
    $brand = setting('store.brand_name', 'LDC Store');
    $themeColor = $themeColor ?? \App\Services\BrandingService::THEME_COLOR;
    $extraFonts = $extraFonts ?? null;
@endphp
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="theme-color" content="{{ $themeColor }}">
<meta name="robots" content="noindex, nofollow">

{{-- Favicons --}}
<link rel="icon" type="image/svg+xml" href="{{ $branding->getFaviconUrl('svg') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ $branding->getFaviconUrl('png32') }}">
<link rel="shortcut icon" href="{{ $branding->getFaviconUrl('ico') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ $branding->getFaviconUrl('apple') }}">
<link rel="manifest" href="{{ url('/site.webmanifest') }}">

<title>Login - {{ $brand }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@if($extraFonts)
<link href="{{ $extraFonts }}" rel="stylesheet">
@endif
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
@vite(['resources/css/app.css', 'resources/js/app.js'])
