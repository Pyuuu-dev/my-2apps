<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f172a">
    <title>LDC Store - Blox Fruit Joki & Akun Murah</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .hero-bg { background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 40%, #312e81 70%, #1e1b4b 100%); }
        .glow { box-shadow: 0 0 60px rgba(99, 102, 241, 0.15); }
        .card-hover { transition: transform 0.2s, box-shadow 0.2s; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 12px 40px rgba(0,0,0,0.12); }
        .float { animation: float 3s ease-in-out infinite; }
        @keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="bg-slate-950 text-white antialiased">

    {{-- ============ NAVBAR ============ --}}
    <nav class="fixed top-0 inset-x-0 z-50 backdrop-blur-xl bg-slate-950/80 border-b border-white/5">
        <div class="max-w-6xl mx-auto px-4 h-14 flex items-center justify-between">
            <a href="#" class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <span class="font-extrabold text-sm">LDC Store</span>
            </a>
            <div class="flex items-center gap-4 text-xs">
                <a href="#joki" class="text-gray-400 hover:text-white transition-colors hidden sm:block">Joki</a>
                <a href="#akun" class="text-gray-400 hover:text-white transition-colors hidden sm:block">Akun</a>
                <a href="#permanent" class="text-gray-400 hover:text-white transition-colors hidden sm:block">Permanent</a>
                <a href="#kontak" class="rounded-lg bg-indigo-600 px-4 py-2 font-bold text-white hover:bg-indigo-500 transition-colors">Hubungi</a>
            </div>
        </div>
    </nav>

    {{-- ============ HERO ============ --}}
    <section class="hero-bg pt-28 pb-16 px-4 relative overflow-hidden">
        <div class="absolute inset-0 opacity-30">
            <div class="absolute top-20 left-10 h-64 w-64 rounded-full bg-indigo-600/20 blur-3xl"></div>
            <div class="absolute bottom-10 right-10 h-48 w-48 rounded-full bg-purple-600/20 blur-3xl"></div>
        </div>
        <div class="max-w-4xl mx-auto text-center relative">
            <div class="inline-flex items-center gap-2 rounded-full bg-indigo-500/10 border border-indigo-500/20 px-4 py-1.5 mb-6">
                <span class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>
                <span class="text-xs font-medium text-indigo-300">Trusted &middot; {{ $stats['joki_selesai'] }}+ Joki Selesai &middot; {{ $stats['akun_terjual'] }}+ Akun Terjual</span>
            </div>
            <h1 class="text-4xl sm:text-5xl font-black leading-tight mb-4">
                LDC <span class="bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">Store</span>
            </h1>
            <p class="text-gray-400 text-base sm:text-lg max-w-2xl mx-auto mb-8">Jasa joki terpercaya, akun siap pakai, dan permanent fruit dengan harga terjangkau. Proses cepat & aman.</p>
            <div class="flex flex-wrap justify-center gap-3">
                <a href="#joki" class="rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-3 text-sm font-bold shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 transition-shadow">Lihat Harga Joki</a>
                <a href="#akun" class="rounded-xl bg-white/5 border border-white/10 px-6 py-3 text-sm font-bold hover:bg-white/10 transition-colors">Stok Akun</a>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-3 gap-4 mt-12 max-w-md mx-auto">
                <div class="rounded-xl bg-white/5 border border-white/10 p-3">
                    <p class="text-2xl font-black text-indigo-400">{{ $stats['joki_selesai'] }}+</p>
                    <p class="text-[10px] text-gray-500 mt-0.5">Joki Selesai</p>
                </div>
                <div class="rounded-xl bg-white/5 border border-white/10 p-3">
                    <p class="text-2xl font-black text-purple-400">{{ $stats['akun_terjual'] }}+</p>
                    <p class="text-[10px] text-gray-500 mt-0.5">Akun Terjual</p>
                </div>
                <div class="rounded-xl bg-white/5 border border-white/10 p-3">
                    <p class="text-2xl font-black text-emerald-400">{{ $stats['total_services'] }}</p>
                    <p class="text-[10px] text-gray-500 mt-0.5">Jenis Layanan</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ DAFTAR HARGA JOKI ============ --}}
    <section id="joki" class="py-16 px-4">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-10">
                <p class="text-xs font-bold uppercase tracking-widest text-indigo-400 mb-2">Layanan</p>
                <h2 class="text-3xl font-black">Daftar Harga Joki</h2>
                <p class="text-sm text-gray-500 mt-2">Harga terjangkau, proses cepat & aman</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($kategoriLabels as $katKey => $kat)
                @if(isset($servicesByKategori[$katKey]))
                <div class="rounded-2xl bg-slate-900/80 border border-white/5 overflow-hidden card-hover">
                    <div class="px-5 py-3 bg-gradient-to-r from-indigo-600/10 to-purple-600/10 border-b border-white/5">
                        <p class="text-sm font-bold">{{ $kat['icon'] }} {{ $kat['label'] }}</p>
                    </div>
                    <div class="divide-y divide-white/5">
                        @foreach($servicesByKategori[$katKey] as $svc)
                        <div class="px-5 py-2.5 flex items-center justify-between">
                            <span class="text-xs text-gray-400">{{ $svc->nama }}</span>
                            <span class="text-xs font-bold {{ $svc->harga > 0 ? 'text-indigo-400' : 'text-gray-500' }}">{{ $svc->harga > 0 ? 'Rp ' . number_format($svc->harga, 0, ',', '.') : 'Custom' }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ STOK AKUN ============ --}}
    <section id="akun" class="py-16 px-4 bg-slate-900/50">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-10">
                <p class="text-xs font-bold uppercase tracking-widest text-emerald-400 mb-2">Ready Stock</p>
                <h2 class="text-3xl font-black">Akun Siap Pakai</h2>
                <p class="text-sm text-gray-500 mt-2">{{ $akunTersedia->count() }} akun tersedia</p>
            </div>

            @if($akunTersedia->count() > 0)
            <div class="overflow-x-auto hide-scrollbar rounded-2xl bg-slate-900/80 border border-white/5">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-white/10">
                            <th class="px-4 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-gray-500">Sword/Gun</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-gray-500">Fruit</th>
                            <th class="px-4 py-3 text-right text-[10px] font-bold uppercase tracking-wider text-gray-500">Belly</th>
                            <th class="px-4 py-3 text-right text-[10px] font-bold uppercase tracking-wider text-gray-500">Fragment</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-gray-500">Race</th>
                            <th class="px-4 py-3 text-right text-[10px] font-bold uppercase tracking-wider text-gray-500">Level</th>
                            <th class="px-4 py-3 text-right text-[10px] font-bold uppercase tracking-wider text-gray-500">Harga</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($akunTersedia as $akun)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-3 text-xs text-gray-300">{{ $akun->sword_gun ?: '-' }}</td>
                            <td class="px-4 py-3 text-xs text-gray-300">{{ $akun->fruit ?: '-' }}</td>
                            <td class="px-4 py-3 text-xs text-right text-gray-400">{{ $akun->belly ?: '-' }}</td>
                            <td class="px-4 py-3 text-xs text-right text-gray-400">{{ $akun->fragment ?: '-' }}</td>
                            <td class="px-4 py-3 text-xs text-gray-300">{{ $akun->race ?: '-' }}</td>
                            <td class="px-4 py-3 text-xs text-right text-gray-300 font-medium">{{ $akun->level ?: '-' }}</td>
                            <td class="px-4 py-3 text-right">
                                @if($akun->harga_jual > 0)
                                <span class="text-sm font-bold text-emerald-400">Rp {{ number_format($akun->harga_jual, 0, ',', '.') }}</span>
                                @else
                                <span class="text-xs text-gray-500">Hubungi</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="rounded-2xl bg-slate-900/80 border border-white/5 p-12 text-center">
                <p class="text-gray-500">Stok akun sedang habis. Hubungi kami untuk pre-order!</p>
            </div>
            @endif

            {{-- Kontak Cepat --}}
            <div class="mt-8 text-center">
                <p class="text-sm text-gray-400 mb-4">Minat? Langsung hubungi kami</p>
                <div class="flex flex-wrap justify-center gap-3">
                    <a href="https://wa.me/6285954714723" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-green-600 hover:bg-green-500 px-5 py-2.5 text-sm font-bold text-white transition-colors shadow-lg shadow-green-600/20">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        WhatsApp
                    </a>
                    <a href="https://www.tiktok.com/@ldc_storee" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 px-5 py-2.5 text-sm font-bold text-white transition-colors">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1v-3.5a6.37 6.37 0 00-.79-.05A6.34 6.34 0 003.15 15.2a6.34 6.34 0 0010.86 4.46V13a8.28 8.28 0 005.58 2.17v-3.44a4.85 4.85 0 01-3.77-1.27V6.69h3.77z"/></svg>
                        TikTok
                    </a>
                    <a href="https://www.instagram.com/ldcstoree/" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 px-5 py-2.5 text-sm font-bold text-white transition-colors">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        Instagram
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ PERMANENT FRUIT ============ --}}
    <section id="permanent" class="py-16 px-4">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-10">
                <p class="text-xs font-bold uppercase tracking-widest text-amber-400 mb-2">Permanent</p>
                <h2 class="text-3xl font-black">Harga Permanent Fruit</h2>
                <p class="text-sm text-gray-500 mt-2">{{ $permanents->count() }} buah tersedia</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                @foreach($permanents as $perm)
                <div class="rounded-xl bg-slate-900/80 border border-white/5 p-3 card-hover text-center">
                    <p class="text-sm font-bold text-white mb-1">{{ $perm->nama }}</p>
                    <p class="text-[10px] text-gray-500 mb-2">{{ number_format($perm->harga_robux) }} Robux</p>
                    <div class="flex items-center justify-center gap-3">
                        @if($perm->harga_beli > 0)
                        <div>
                            <p class="text-[9px] text-gray-600">Beli</p>
                            <p class="text-xs font-bold text-blue-400">{{ number_format($perm->harga_beli / 1000) }}k</p>
                        </div>
                        @endif
                        @if($perm->harga_jual > 0)
                        <div>
                            <p class="text-[9px] text-gray-600">Jual</p>
                            <p class="text-xs font-bold text-emerald-400">{{ number_format($perm->harga_jual / 1000) }}k</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ KENAPA PILIH KAMI ============ --}}
    <section class="py-16 px-4 bg-slate-900/50">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-black">Kenapa Pilih Kami?</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="rounded-2xl bg-slate-900/80 border border-white/5 p-6 text-center card-hover">
                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center mx-auto mb-4">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="font-bold mb-1">Aman & Terpercaya</h3>
                    <p class="text-xs text-gray-500">{{ $stats['joki_selesai'] }}+ joki selesai tanpa masalah. Data akun dijaga kerahasiaannya.</p>
                </div>
                <div class="rounded-2xl bg-slate-900/80 border border-white/5 p-6 text-center card-hover">
                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center mx-auto mb-4">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="font-bold mb-1">Proses Cepat</h3>
                    <p class="text-xs text-gray-500">Pengerjaan joki cepat dan tepat waktu. Akun langsung siap pakai setelah pembayaran.</p>
                </div>
                <div class="rounded-2xl bg-slate-900/80 border border-white/5 p-6 text-center card-hover">
                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center mx-auto mb-4">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-bold mb-1">Harga Terjangkau</h3>
                    <p class="text-xs text-gray-500">Harga bersaing dengan kualitas terbaik. Banyak pilihan layanan sesuai budget.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ KONTAK / CTA ============ --}}
    <section id="kontak" class="py-16 px-4">
        <div class="max-w-2xl mx-auto text-center">
            <div class="rounded-3xl bg-gradient-to-br from-indigo-600 to-purple-700 p-10 shadow-2xl shadow-indigo-500/20 glow">
                <h2 class="text-2xl font-black mb-2">Tertarik? Hubungi Kami!</h2>
                <p class="text-indigo-200 text-sm mb-6">Chat langsung untuk order joki, beli akun, atau tanya-tanya.</p>
                <div class="flex flex-wrap justify-center gap-3">
                    <a href="https://wa.me/6285954714723" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-bold text-gray-900 hover:bg-gray-100 transition-colors shadow-lg">
                        <svg class="h-5 w-5 text-green-600" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        WhatsApp
                    </a>
                    <a href="https://www.tiktok.com/@ldc_storee" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-5 py-3 text-sm font-bold text-white hover:bg-white/20 transition-colors">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1v-3.5a6.37 6.37 0 00-.79-.05A6.34 6.34 0 003.15 15.2a6.34 6.34 0 0010.86 4.46V13a8.28 8.28 0 005.58 2.17v-3.44a4.85 4.85 0 01-3.77-1.27V6.69h3.77z"/></svg>
                        TikTok
                    </a>
                    <a href="https://www.instagram.com/ldcstoree/" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-5 py-3 text-sm font-bold text-white hover:bg-white/20 transition-colors">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        Instagram
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ FOOTER ============ --}}
    <footer class="border-t border-white/5 py-6 px-4">
        <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-gray-600">&copy; {{ date('Y') }} LDC Store - Blox Fruit Joki & Akun</p>
            <div class="flex items-center gap-4">
                <a href="https://www.tiktok.com/@ldc_storee" target="_blank" class="text-gray-600 hover:text-white transition-colors">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1v-3.5a6.37 6.37 0 00-.79-.05A6.34 6.34 0 003.15 15.2a6.34 6.34 0 0010.86 4.46V13a8.28 8.28 0 005.58 2.17v-3.44a4.85 4.85 0 01-3.77-1.27V6.69h3.77z"/></svg>
                </a>
                <a href="https://www.instagram.com/ldcstoree/" target="_blank" class="text-gray-600 hover:text-white transition-colors">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                </a>
                <a href="https://wa.me/6285954714723" target="_blank" class="text-gray-600 hover:text-green-500 transition-colors">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </a>
            </div>
        </div>
    </footer>

</body>
</html>
