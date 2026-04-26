<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#020617">
    <title>LDC Store - Blox Fruit Joki & Akun Murah</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; background: #020617; }
        .card-hover { transition: transform 0.2s, box-shadow 0.2s; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(0,0,0,0.3); }
    </style>
</head>
<body class="bg-[#020617] text-gray-100 antialiased">

    {{-- ============ NAVBAR ============ --}}
    <nav class="fixed top-0 inset-x-0 z-50 backdrop-blur-xl bg-[#020617]/90 border-b border-slate-800">
        <div class="max-w-6xl mx-auto px-4 h-14 flex items-center justify-between">
            <a href="#" class="flex items-center gap-2.5">
                <div class="h-8 w-8 rounded-lg bg-indigo-600 flex items-center justify-center">
                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <span class="font-extrabold text-sm text-white">LDC Store</span>
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
    <section class="pt-28 pb-20 px-4 relative overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute top-16 left-1/4 h-72 w-72 rounded-full bg-indigo-600/8 blur-[100px]"></div>
            <div class="absolute bottom-0 right-1/4 h-56 w-56 rounded-full bg-purple-600/8 blur-[100px]"></div>
        </div>
        <div class="max-w-3xl mx-auto text-center relative">
            <div class="inline-flex items-center gap-2 rounded-full bg-slate-800 border border-slate-700 px-4 py-1.5 mb-6">
                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-medium text-slate-300">{{ $stats['joki_selesai'] }}+ Joki Selesai &middot; {{ $stats['akun_terjual'] }}+ Akun Terjual</span>
            </div>
            <h1 class="text-4xl sm:text-5xl font-black leading-tight mb-4 text-white">
                LDC <span class="text-indigo-400">Store</span>
            </h1>
            <p class="text-slate-400 text-base sm:text-lg max-w-xl mx-auto mb-8 leading-relaxed">Jasa joki terpercaya dan permanent fruit dengan harga terjangkau. Proses cepat & aman.</p>
            <div class="flex flex-wrap justify-center gap-3">
                <a href="#joki" class="rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white hover:bg-indigo-500 transition-colors">Lihat Harga Joki</a>
                <a href="#permanent" class="rounded-xl bg-slate-800 border border-slate-700 px-6 py-3 text-sm font-bold text-slate-200 hover:bg-slate-700 transition-colors">Permanent Fruit</a>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-3 gap-3 mt-14 max-w-sm mx-auto">
                <div class="rounded-xl bg-slate-800/80 border border-slate-700/50 p-3">
                    <p class="text-2xl font-black text-white">{{ $stats['joki_selesai'] }}+</p>
                    <p class="text-[10px] text-slate-500 mt-0.5">Joki Selesai</p>
                </div>
                <div class="rounded-xl bg-slate-800/80 border border-slate-700/50 p-3">
                    <p class="text-2xl font-black text-white">{{ $stats['akun_terjual'] }}+</p>
                    <p class="text-[10px] text-slate-500 mt-0.5">Akun Terjual</p>
                </div>
                <div class="rounded-xl bg-slate-800/80 border border-slate-700/50 p-3">
                    <p class="text-2xl font-black text-white">{{ $stats['total_services'] }}</p>
                    <p class="text-[10px] text-slate-500 mt-0.5">Layanan</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ DAFTAR HARGA JOKI ============ --}}
    <section id="joki" class="py-16 px-4 border-t border-slate-800/50">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-10">
                <p class="text-xs font-bold uppercase tracking-widest text-indigo-400 mb-2">Layanan</p>
                <h2 class="text-3xl font-black text-white">Daftar Harga Joki</h2>
                <p class="text-sm text-slate-500 mt-2">Harga terjangkau, proses cepat & aman</p>
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
                        <div class="px-5 py-2.5 flex items-center justify-between">
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
    <section id="fruit" class="py-16 px-4 border-t border-slate-800/50">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-10">
                <p class="text-xs font-bold uppercase tracking-widest text-purple-400 mb-2">Fruit</p>
                <h2 class="text-3xl font-black text-white">Harga Fruit</h2>
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
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider {{ $rc[0] }}">{{ $rarity }}</span>
                        <span class="text-[10px] text-slate-600">{{ $fruits->count() }} buah</span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2">
                        @foreach($fruits as $fruit)
                        <div class="rounded-xl bg-slate-900 border {{ $rc[1] }} p-3 text-center card-hover">
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
    <section id="skin" class="py-16 px-4 border-t border-slate-800/50">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-10">
                <p class="text-xs font-bold uppercase tracking-widest text-pink-400 mb-2">Skin</p>
                <h2 class="text-3xl font-black text-white">Harga Skin</h2>
                <p class="text-sm text-slate-500 mt-2">{{ $skins->count() }} skin tersedia</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($skins as $skin)
                <div class="rounded-xl bg-slate-900 border border-slate-800 p-4 flex items-center justify-between card-hover">
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
    <section id="gamepass" class="py-16 px-4 border-t border-slate-800/50">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-10">
                <p class="text-xs font-bold uppercase tracking-widest text-cyan-400 mb-2">Gamepass</p>
                <h2 class="text-3xl font-black text-white">Harga Gamepass</h2>
                <p class="text-sm text-slate-500 mt-2">{{ $gamepasses->count() }} gamepass tersedia</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 max-w-4xl mx-auto">
                @foreach($gamepasses as $gp)
                <div class="rounded-xl bg-slate-900 border border-slate-800 p-4 card-hover flex items-center justify-between">
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
    <section id="permanent" class="py-16 px-4 border-t border-slate-800/50">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-10">
                <p class="text-xs font-bold uppercase tracking-widest text-amber-400 mb-2">Permanent</p>
                <h2 class="text-3xl font-black text-white">Harga Permanent Fruit</h2>
                <p class="text-sm text-slate-500 mt-2">{{ $permanents->count() }} buah tersedia</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                @foreach($permanents as $perm)
                <div class="rounded-xl bg-slate-900 border border-slate-800 p-3 card-hover text-center">
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
                <h2 class="text-3xl font-black text-white">Kenapa Pilih Kami?</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 text-center card-hover">
                    <div class="h-12 w-12 rounded-xl bg-indigo-600 flex items-center justify-center mx-auto mb-4">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="font-bold text-white mb-1">Aman & Terpercaya</h3>
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

    {{-- ============ KONTAK / CTA ============ --}}
    <section id="kontak" class="py-16 px-4 border-t border-slate-800/50">
        <div class="max-w-2xl mx-auto text-center">
            <div class="rounded-2xl bg-slate-900 border border-slate-800 p-10">
                <h2 class="text-2xl font-black text-white mb-2">Tertarik? Hubungi Kami!</h2>
                <p class="text-slate-400 text-sm mb-8">Chat langsung untuk order joki, beli akun, atau tanya-tanya.</p>
                <div class="flex flex-wrap justify-center gap-3">
                    <a href="https://wa.me/6282353085502" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white hover:bg-emerald-500 transition-colors">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        WhatsApp
                    </a>
                    <a href="https://www.tiktok.com/@ldc_storee" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-slate-800 border border-slate-700 px-5 py-3 text-sm font-bold text-white hover:bg-slate-700 transition-colors">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1v-3.5a6.37 6.37 0 00-.79-.05A6.34 6.34 0 003.15 15.2a6.34 6.34 0 0010.86 4.46V13a8.28 8.28 0 005.58 2.17v-3.44a4.85 4.85 0 01-3.77-1.27V6.69h3.77z"/></svg>
                        TikTok
                    </a>
                    <a href="https://www.instagram.com/ldcstoree/" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-slate-800 border border-slate-700 px-5 py-3 text-sm font-bold text-white hover:bg-slate-700 transition-colors">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        Instagram
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ FOOTER ============ --}}
    <footer class="border-t border-slate-800 py-6 px-4">
        <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-slate-600">&copy; {{ date('Y') }} LDC Store - Blox Fruit Joki & Akun</p>
            <div class="flex items-center gap-4">
                <a href="https://www.tiktok.com/@ldc_storee" target="_blank" class="text-slate-600 hover:text-white transition-colors">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1v-3.5a6.37 6.37 0 00-.79-.05A6.34 6.34 0 003.15 15.2a6.34 6.34 0 0010.86 4.46V13a8.28 8.28 0 005.58 2.17v-3.44a4.85 4.85 0 01-3.77-1.27V6.69h3.77z"/></svg>
                </a>
                <a href="https://www.instagram.com/ldcstoree/" target="_blank" class="text-slate-600 hover:text-white transition-colors">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                </a>
                <a href="https://wa.me/6282353085502" target="_blank" class="text-slate-600 hover:text-emerald-400 transition-colors">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </a>
            </div>
        </div>
    </footer>

</body>
</html>
