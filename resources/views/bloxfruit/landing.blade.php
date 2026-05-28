<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    @php
        $brand = setting('store.brand_name', 'LDC Store');
        $brandColor = setting('store.brand_color', '#020617');
        $branding = app(\App\Services\BrandingService::class);
        $ogImageUrl = $branding->getOgImageUrl();
        $titleFull = $brand . ' - Blox Fruit Joki & Akun Murah';
        $descShort = "Jasa joki Blox Fruit terpercaya, permanent fruit & gamepass murah. {$stats['joki_selesai']}+ joki selesai. Proses cepat & aman.";
        $descLong  = "Jasa joki terpercaya, permanent fruit & gamepass dengan harga terjangkau. {$stats['joki_selesai']}+ joki selesai, {$stats['akun_terjual']}+ akun terjual, {$stats['item_terjual']}+ item terjual. Proses cepat & aman!";
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="{{ $brandColor }}">
    <meta name="description" content="{{ $descShort }}">

    {{-- Favicons (dynamic from BrandingService with fallback to defaults) --}}
    <link rel="icon" type="image/svg+xml" href="{{ $branding->getFaviconUrl('svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ $branding->getFaviconUrl('png32') }}">
    <link rel="shortcut icon" href="{{ $branding->getFaviconUrl('ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ $branding->getFaviconUrl('apple') }}">
    <link rel="manifest" href="{{ url('/site.webmanifest') }}">

    {{-- SEO basics --}}
    <link rel="canonical" href="{{ url('/') }}">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <meta name="author" content="{{ $brand }}">
    <meta name="keywords" content="blox fruit, joki blox fruit, jual akun blox fruit, permanent fruit, gamepass blox fruit, skin blox fruit, joki murah, {{ strtolower($brand) }}">
    <meta name="format-detection" content="telephone=no">

    {{-- iOS PWA / Mobile App --}}
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ $brand }}">
    <meta name="application-name" content="{{ $brand }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="{{ $titleFull }}">
    <meta property="og:description" content="{{ $descLong }}">
    <meta property="og:image" content="{{ $ogImageUrl }}">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $titleFull }}">
    <meta property="og:site_name" content="{{ $brand }}">
    <meta property="og:locale" content="id_ID">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $titleFull }}">
    <meta name="twitter:description" content="{{ $descShort }}">
    <meta name="twitter:image" content="{{ $ogImageUrl }}">
    <meta name="twitter:image:alt" content="{{ $titleFull }}">

    <title>{{ $titleFull }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; background: #020617; }
        .card-hover { transition: transform 0.2s, box-shadow 0.2s; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(0,0,0,0.3); }
        [x-cloak] { display: none !important; }
    </style>

    {{-- Schema.org JSON-LD untuk Google rich snippet --}}
    @php
        // Build sameAs array (filter empty social URLs at render time)
        $sameAs = array_values(array_filter([
            setting('store.tiktok_url') ?: null,
            setting('store.instagram_url') ?: null,
            setting('store.wa_channel_url') ?: null,
        ]));
        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'Store',
            'name' => $brand,
            'description' => 'Jasa joki Blox Fruit terpercaya, permanent fruit & gamepass dengan harga terjangkau',
            'url' => url('/'),
            'image' => $ogImageUrl,
            'telephone' => '+' . preg_replace('/\D/', '', setting('store.wa_number', '6282353085502')),
            'priceRange' => 'Rp 1.000 - Rp 380.000',
        ];
        if (!empty($sameAs)) {
            $jsonLd['sameAs'] = $sameAs;
        }
    @endphp
    <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
</head>
@php
    // Sanitize WhatsApp number defensively at render time so even legacy/invalid
    // values can never break the wa.me link or be abused via the URL path.
    $waNumber = preg_replace('/\D/', '', (string) setting('store.wa_number', '6282353085502')) ?: '6282353085502';
    $waChannelUrl = (string) setting('store.wa_channel_url', '');
    $tiktokUrl = (string) setting('store.tiktok_url', '');
    $instagramUrl = (string) setting('store.instagram_url', '');
@endphp
<body class="bg-[#020617] text-gray-100 antialiased"
    x-data="storeSearch()"
    x-init="init()">

    {{-- ============ NAVBAR ============ --}}
    <nav class="fixed top-0 inset-x-0 z-50 backdrop-blur-xl bg-[#020617]/90 border-b border-slate-800">
        <div class="max-w-6xl mx-auto px-4 h-14 flex items-center justify-between">
            <a href="#" class="flex items-center gap-2.5">
                <div class="h-8 w-8 rounded-lg bg-indigo-600 flex items-center justify-center">
                    <x-brand-logo size="h-4 w-4" extraClass="text-white"/>
                </div>
                <span class="font-bold text-sm text-white">{{ setting('store.brand_name', 'LDC Store') }}</span>
            </a>
            <div class="flex items-center gap-4 text-xs">
                <a href="#joki" class="text-slate-400 hover:text-white transition-colors hidden sm:block">Joki</a>
                <a href="#fruit" class="text-slate-400 hover:text-white transition-colors hidden sm:block">Fruit</a>
                <a href="#skin" class="text-slate-400 hover:text-white transition-colors hidden sm:block">Skin</a>
                <a href="#gamepass" class="text-slate-400 hover:text-white transition-colors hidden sm:block">Gamepass</a>
                <a href="#permanent" class="text-slate-400 hover:text-white transition-colors hidden sm:block">Permanent</a>
                <a href="#kontak" class="rounded-lg bg-indigo-600 px-4 py-2 font-bold text-white hover:bg-indigo-500 transition-colors">Hubungi</a>
            </div>
        </div>
    </nav>

    {{-- ============ HERO ============ --}}
    <section class="pt-28 pb-12 px-4 relative overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute top-16 left-1/4 h-72 w-72 rounded-full bg-indigo-600/8 blur-[100px]"></div>
            <div class="absolute bottom-0 right-1/4 h-56 w-56 rounded-full bg-purple-600/8 blur-[100px]"></div>
        </div>
        <div class="max-w-3xl mx-auto text-center relative">
            <div class="inline-flex items-center gap-2 rounded-full bg-slate-800 border border-slate-700 px-4 py-1.5 mb-6">
                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-medium text-slate-300">{{ $stats['joki_selesai'] }}+ Joki Selesai &middot; {{ $stats['akun_terjual'] }}+ Akun Terjual &middot; {{ $stats['item_terjual'] }}+ Item Terjual</span>
            </div>
            <h1 class="text-4xl sm:text-5xl font-bold leading-tight mb-4 text-white tracking-tight">
                {{ explode(' ', setting('store.brand_name', 'LDC Store'))[0] ?? 'LDC' }} <span class="text-indigo-400">{{ explode(' ', setting('store.brand_name', 'LDC Store'))[1] ?? 'Store' }}</span>
            </h1>
            <p class="text-slate-400 text-base sm:text-lg max-w-xl mx-auto mb-8 leading-relaxed">Jasa joki terpercaya dan permanent fruit dengan harga terjangkau. Proses cepat &amp; aman.</p>

            {{-- Stats --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 max-w-lg mx-auto">
                <div class="rounded-xl bg-slate-800/80 border border-slate-700/50 p-3">
                    <p class="text-2xl font-bold text-white">{{ $stats['joki_selesai'] }}+</p>
                    <p class="text-[10px] text-slate-500 mt-0.5">Joki Selesai</p>
                </div>
                <div class="rounded-xl bg-slate-800/80 border border-slate-700/50 p-3">
                    <p class="text-2xl font-bold text-white">{{ $stats['akun_terjual'] }}+</p>
                    <p class="text-[10px] text-slate-500 mt-0.5">Akun Terjual</p>
                </div>
                <div class="rounded-xl bg-slate-800/80 border border-slate-700/50 p-3">
                    <p class="text-2xl font-bold text-white">{{ $stats['item_terjual'] }}+</p>
                    <p class="text-[10px] text-slate-500 mt-0.5">Item Terjual</p>
                </div>
                <div class="rounded-xl bg-slate-800/80 border border-slate-700/50 p-3">
                    <p class="text-2xl font-bold text-white">{{ $stats['total_services'] }}</p>
                    <p class="text-[10px] text-slate-500 mt-0.5">Layanan</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ KONTAK / CTA (DIPINDAH KE ATAS) ============ --}}
    <section id="kontak" class="px-4 pb-6">
        <div class="max-w-3xl mx-auto">
            <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 sm:p-7 text-center">
                <h2 class="text-xl sm:text-2xl font-bold text-white mb-1 tracking-tight">Tertarik? Hubungi Kami!</h2>
                <p class="text-slate-400 text-xs sm:text-sm mb-5">Chat untuk order joki, beli akun, atau tanya harga.</p>
                <div class="flex flex-wrap justify-center gap-2.5">
                    <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white hover:bg-emerald-500 transition-colors">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        WhatsApp
                    </a>
                    @if($tiktokUrl)
                    <a href="{{ $tiktokUrl }}" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 rounded-xl bg-slate-800 border border-slate-700 px-4 py-2.5 text-xs font-bold text-white hover:bg-slate-700 transition-colors">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1v-3.5a6.37 6.37 0 00-.79-.05A6.34 6.34 0 003.15 15.2a6.34 6.34 0 0010.86 4.46V13a8.28 8.28 0 005.58 2.17v-3.44a4.85 4.85 0 01-3.77-1.27V6.69h3.77z"/></svg>
                        TikTok
                    </a>
                    @endif
                    @if($instagramUrl)
                    <a href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 rounded-xl bg-slate-800 border border-slate-700 px-4 py-2.5 text-xs font-bold text-white hover:bg-slate-700 transition-colors">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        Instagram
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ============ SALURAN WA (PROMOSI STOK REAL-TIME) ============ --}}
    @if($waChannelUrl)
    <section id="saluran" class="px-4 pb-6">
        <div class="max-w-3xl mx-auto">
            <a href="{{ $waChannelUrl }}" target="_blank" rel="noopener noreferrer"
               class="block group rounded-2xl bg-slate-900 border border-slate-800 hover:border-emerald-600/50 p-5 sm:p-6 card-hover transition-colors">
                <div class="flex items-center gap-4">
                    {{-- Icon container (flat emerald, sama style dengan 'Kenapa Pilih Kami?') --}}
                    <div class="h-11 w-11 rounded-xl bg-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>

                    {{-- Text content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-0.5">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400">Live Update</span>
                        </div>
                        <p class="text-sm font-bold text-white">Update Stok Real-Time di Saluran WhatsApp</p>
                        <p class="text-xs text-slate-400 mt-0.5 hidden sm:block">Ikuti untuk stok terbaru &amp; restock fruit langka</p>
                    </div>

                    {{-- CTA arrow (desktop) --}}
                    <div class="shrink-0 hidden sm:flex items-center gap-1.5 text-xs font-semibold text-emerald-400 group-hover:text-emerald-300 transition-colors">
                        Ikuti
                        <svg class="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </div>

                    {{-- Mobile chevron only --}}
                    <div class="shrink-0 sm:hidden text-emerald-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </a>
        </div>
    </section>
    @endif

    {{-- ============ SEARCH BAR (Sticky) ============ --}}
    <div class="sticky top-14 z-40 backdrop-blur-xl bg-[#020617]/95 border-b border-slate-800/50 px-4 py-3">
        <div class="max-w-3xl mx-auto">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input
                    id="landing-search"
                    type="text"
                    x-model="q"
                    placeholder="Cari fruit, skin, gamepass, permanent, atau joki..."
                    class="w-full h-11 pl-10 pr-24 rounded-xl bg-slate-900 border border-slate-700 text-white placeholder:text-slate-500 text-sm focus:border-indigo-500 focus:ring-0 focus:outline-none transition-colors">
                <div class="absolute right-2 top-1/2 -translate-y-1/2 flex items-center gap-1">
                    <span x-show="q" x-cloak class="text-[10px] text-slate-500 px-2" x-text="visibleCount + ' hasil'"></span>
                    <button x-show="q" x-cloak @click="clear()" class="rounded-md p-1.5 text-slate-500 hover:text-white hover:bg-slate-800 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Empty State --}}
    <div id="search-empty-state" style="display: none;" class="max-w-2xl mx-auto px-4 py-12 text-center">
        <div class="inline-flex h-14 w-14 rounded-full bg-slate-800 border border-slate-700 items-center justify-center mb-3">
            <svg class="h-6 w-6 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        <p class="text-base font-semibold text-white">Tidak ditemukan</p>
        <p class="text-sm text-slate-500 mt-1">Coba kata kunci lain atau hubungi WhatsApp untuk request custom.</p>
        <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 px-4 py-2 text-sm font-bold text-white mt-4 transition-colors">
            Chat WhatsApp
        </a>
    </div>

    {{-- ============ DAFTAR HARGA JOKI ============ --}}
    <section id="joki" class="py-12 px-4 border-t border-slate-800/50" data-search-section="joki">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-10">
                <p class="text-xs font-bold uppercase tracking-widest text-indigo-400 mb-2">Layanan</p>
                <h2 class="text-3xl font-bold text-white tracking-tight">Daftar Harga Joki</h2>
                <p class="text-sm text-slate-500 mt-2">Harga terjangkau, proses cepat &amp; aman</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($kategoriLabels as $katKey => $kat)
                @if(isset($servicesByKategori[$katKey]))
                <div class="rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden card-hover">
                    <div class="px-5 py-3 bg-slate-800/50 border-b border-slate-800">
                        <p class="text-sm font-bold text-white">{{ $kat['icon'] }} {{ $kat['label'] }}</p>
                    </div>
                    <div class="divide-y divide-slate-800/50">
                        @foreach($servicesByKategori[$katKey] as $svc)
                        <div data-search="{{ strtolower($svc->nama . ' ' . $kat['label'] . ' joki') }}" data-search-match="1" data-section="joki" class="px-5 py-2.5 flex items-center justify-between">
                            <span class="text-xs text-slate-400">{{ $svc->nama }}</span>
                            <span class="text-xs font-bold {{ $svc->harga > 0 ? 'text-indigo-400' : 'text-slate-600' }}">{{ $svc->harga > 0 ? 'Rp ' . number_format($svc->harga, 0, ',', '.') : 'Custom' }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ HARGA FRUIT ============ --}}
    <section id="fruit" class="py-12 px-4 border-t border-slate-800/50" data-search-section="fruit">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-10">
                <p class="text-xs font-bold uppercase tracking-widest text-purple-400 mb-2">Fruit</p>
                <h2 class="text-3xl font-bold text-white tracking-tight">Harga Fruit</h2>
                <p class="text-sm text-slate-500 mt-2">Harga jual fruit per rarity</p>
            </div>

            @php
                $rarityColors = [
                    'Mythical' => ['text-fuchsia-400', 'border-fuchsia-800/50', 'bg-fuchsia-500/5'],
                    'Legendary' => ['text-amber-400', 'border-amber-800/50', 'bg-amber-500/5'],
                    'Rare' => ['text-blue-400', 'border-blue-800/50', 'bg-blue-500/5'],
                    'Uncommon' => ['text-emerald-400', 'border-emerald-800/50', 'bg-emerald-500/5'],
                    'Common' => ['text-slate-400', 'border-slate-700', 'bg-slate-800/50'],
                ];
            @endphp

            <div class="space-y-6">
                @foreach($fruitsByRarity as $rarity => $fruits)
                @php $rc = $rarityColors[$rarity] ?? ['text-slate-400', 'border-slate-700', 'bg-slate-800/50']; @endphp
                <div data-rarity-group="{{ $rarity }}">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider {{ $rc[0] }}">{{ $rarity }}</span>
                        <span class="text-[10px] text-slate-600">{{ $fruits->count() }} buah</span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2">
                        @foreach($fruits as $fruit)
                        <div data-search="{{ strtolower($fruit->nama . ' ' . $rarity . ' fruit buah') }}" data-search-match="1" data-section="fruit" class="rounded-xl bg-slate-900 border {{ $rc[1] }} p-3 text-center card-hover">
                            <p class="text-sm font-bold text-white mb-1">{{ $fruit->nama }}</p>
                            @if($fruit->harga_jual > 0)
                            <p class="text-xs font-bold text-emerald-400">Rp {{ number_format($fruit->harga_jual / 1000, 1) }}k</p>
                            @else
                            <p class="text-xs text-slate-600">Hubungi</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ HARGA SKIN ============ --}}
    <section id="skin" class="py-12 px-4 border-t border-slate-800/50" data-search-section="skin">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-10">
                <p class="text-xs font-bold uppercase tracking-widest text-pink-400 mb-2">Skin</p>
                <h2 class="text-3xl font-bold text-white tracking-tight">Harga Skin</h2>
                <p class="text-sm text-slate-500 mt-2">{{ $skins->count() }} skin tersedia</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($skins as $skin)
                <div data-search="{{ strtolower($skin->nama_skin . ' skin ' . ($skin->fruit->nama ?? '')) }}" data-search-match="1" data-section="skin" class="rounded-xl bg-slate-900 border border-slate-800 p-4 flex items-center justify-between card-hover">
                    <p class="text-sm font-bold text-white">{{ $skin->nama_skin }}</p>
                    @if($skin->harga_jual > 0)
                    <p class="text-sm font-bold text-emerald-400 shrink-0">Rp {{ number_format($skin->harga_jual / 1000) }}k</p>
                    @else
                    <p class="text-xs text-slate-600 shrink-0">Hubungi</p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ HARGA GAMEPASS ============ --}}
    <section id="gamepass" class="py-12 px-4 border-t border-slate-800/50" data-search-section="gamepass">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-10">
                <p class="text-xs font-bold uppercase tracking-widest text-cyan-400 mb-2">Gamepass</p>
                <h2 class="text-3xl font-bold text-white tracking-tight">Harga Gamepass</h2>
                <p class="text-sm text-slate-500 mt-2">{{ $gamepasses->count() }} gamepass tersedia</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 max-w-4xl mx-auto">
                @foreach($gamepasses as $gp)
                <div data-search="{{ strtolower($gp->nama . ' gamepass') }}" data-search-match="1" data-section="gamepass" class="rounded-xl bg-slate-900 border border-slate-800 p-4 card-hover flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-white">{{ $gp->nama }}</p>
                        <p class="text-[10px] text-slate-600">{{ number_format($gp->harga_robux) }} Robux</p>
                    </div>
                    @if($gp->harga_jual > 0)
                    <p class="text-sm font-bold text-emerald-400 shrink-0">Rp {{ number_format($gp->harga_jual / 1000) }}k</p>
                    @else
                    <p class="text-xs text-slate-600 shrink-0">Hubungi</p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ PERMANENT FRUIT ============ --}}
    <section id="permanent" class="py-12 px-4 border-t border-slate-800/50" data-search-section="permanent">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-10">
                <p class="text-xs font-bold uppercase tracking-widest text-amber-400 mb-2">Permanent</p>
                <h2 class="text-3xl font-bold text-white tracking-tight">Harga Permanent Fruit</h2>
                <p class="text-sm text-slate-500 mt-2">{{ $permanents->count() }} buah tersedia</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                @foreach($permanents as $perm)
                <div data-search="{{ strtolower('perm ' . $perm->nama . ' permanent fruit') }}" data-search-match="1" data-section="permanent" class="rounded-xl bg-slate-900 border border-slate-800 p-3 card-hover text-center">
                    <p class="text-sm font-bold text-white mb-1">{{ $perm->nama }}</p>
                    <p class="text-[10px] text-slate-500 mb-1">{{ number_format($perm->harga_robux) }} Robux</p>
                    @if($perm->harga_jual > 0)
                    <p class="text-xs font-bold text-emerald-400">Rp {{ number_format($perm->harga_jual / 1000) }}k</p>
                    @else
                    <p class="text-xs text-slate-600">Hubungi</p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ KENAPA PILIH KAMI ============ --}}
    <section class="py-16 px-4 border-t border-slate-800/50">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-bold text-white tracking-tight">Kenapa Pilih Kami?</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 text-center card-hover">
                    <div class="h-12 w-12 rounded-xl bg-indigo-600 flex items-center justify-center mx-auto mb-4">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="font-bold text-white mb-1">Aman &amp; Terpercaya</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">{{ $stats['joki_selesai'] }}+ joki selesai tanpa masalah. Data akun dijaga kerahasiaannya.</p>
                </div>
                <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 text-center card-hover">
                    <div class="h-12 w-12 rounded-xl bg-emerald-600 flex items-center justify-center mx-auto mb-4">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="font-bold text-white mb-1">Proses Cepat</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">Pengerjaan joki cepat dan tepat waktu. Akun langsung siap pakai setelah pembayaran.</p>
                </div>
                <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 text-center card-hover">
                    <div class="h-12 w-12 rounded-xl bg-amber-600 flex items-center justify-center mx-auto mb-4">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-bold text-white mb-1">Harga Terjangkau</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">Harga bersaing dengan kualitas terbaik. Banyak pilihan layanan sesuai budget.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ FOOTER ============ --}}
    <footer class="border-t border-slate-800 py-6 px-4">
        <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-slate-600">&copy; {{ date('Y') }} {{ setting('store.brand_name', 'LDC Store') }} - Blox Fruit Joki &amp; Akun</p>
            <div class="flex items-center gap-4">
                @if($tiktokUrl)
                <a href="{{ $tiktokUrl }}" target="_blank" rel="noopener noreferrer" class="text-slate-600 hover:text-white transition-colors">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1v-3.5a6.37 6.37 0 00-.79-.05A6.34 6.34 0 003.15 15.2a6.34 6.34 0 0010.86 4.46V13a8.28 8.28 0 005.58 2.17v-3.44a4.85 4.85 0 01-3.77-1.27V6.69h3.77z"/></svg>
                </a>
                @endif
                @if($instagramUrl)
                <a href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer" class="text-slate-600 hover:text-white transition-colors">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                </a>
                @endif
                @if($waChannelUrl)
                <a href="{{ $waChannelUrl }}" target="_blank" rel="noopener noreferrer" class="text-slate-600 hover:text-emerald-400 transition-colors" title="Saluran WhatsApp">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </a>
                @endif
                <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener noreferrer" class="text-slate-600 hover:text-emerald-400 transition-colors">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </a>
            </div>
        </div>
    </footer>

    <script>
    function storeSearch() {
        return {
            q: '',
            visibleCount: 0,

            init() {
                this.$watch('q', () => this.applyFilter());
                this.applyFilter();
            },

            clear() {
                this.q = '';
                document.querySelector('#landing-search')?.focus();
            },

            applyFilter() {
                const norm = this.q.trim().toLowerCase();

                // 1. Toggle individual cards
                document.querySelectorAll('[data-search]').forEach(el => {
                    const text = (el.getAttribute('data-search') || '').toLowerCase();
                    const matched = !norm || text.includes(norm);
                    el.style.display = matched ? '' : 'none';
                    if (matched) el.setAttribute('data-search-match', '1');
                    else el.removeAttribute('data-search-match');
                });

                // 2. Hide entire section when no card matches inside
                document.querySelectorAll('[data-search-section]').forEach(sec => {
                    const name = sec.getAttribute('data-search-section');
                    const total = sec.querySelectorAll(`[data-search][data-section="${name}"]`).length;
                    const visible = sec.querySelectorAll(`[data-search-match][data-section="${name}"]`).length;
                    sec.style.display = (norm && total > 0 && visible === 0) ? 'none' : '';
                });

                // 3. Hide rarity-group inside fruit section if all hidden
                document.querySelectorAll('[data-rarity-group]').forEach(g => {
                    const total = g.querySelectorAll('[data-search]').length;
                    const visible = g.querySelectorAll('[data-search-match]').length;
                    g.style.display = (norm && total > 0 && visible === 0) ? 'none' : '';
                });

                // 4. Empty state + counter
                const totalVisible = document.querySelectorAll('[data-search-match]').length;
                this.visibleCount = totalVisible;
                const empty = document.querySelector('#search-empty-state');
                if (empty) {
                    empty.style.display = (norm && totalVisible === 0) ? '' : 'none';
                }
            }
        };
    }
    </script>

</body>
</html>
