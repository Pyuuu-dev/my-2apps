<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#fafaf9">
    <title>@yield('title', 'Dashboard') - {{ setting('store.app_name', 'MyApp') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full antialiased"
    x-data="{
        sidebarOpen: false,
        darkMode: localStorage.getItem('darkMode') === 'true',
        toggleDark() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('darkMode', this.darkMode);
            document.documentElement.classList.toggle('dark', this.darkMode);
            document.querySelector('meta[name=theme-color]')?.setAttribute('content', this.darkMode ? '#0a0a0a' : '#fafaf9');
        },
        openSidebar() {
            this.sidebarOpen = true;
            document.body.style.overflow = 'hidden';
        },
        closeSidebar() {
            this.sidebarOpen = false;
            document.body.style.overflow = '';
        }
    }"
    x-init="if(darkMode) { document.documentElement.classList.add('dark'); document.querySelector('meta[name=theme-color]')?.setAttribute('content', '#0a0a0a') }"
    x-cloak>
    <div class="min-h-full md:flex">
        {{-- Mobile backdrop --}}
        <div x-show="sidebarOpen"
            x-transition:enter="transition-opacity duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-40 bg-black/40 md:hidden"
            @click="closeSidebar()"></div>

        {{-- ============ SIDEBAR ============ --}}
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-72 transition-transform duration-200 ease-out md:translate-x-0 md:static md:shrink-0 md:w-60 sidebar-bg flex flex-col">

            {{-- Logo --}}
            <div class="flex h-14 items-center justify-between px-4 border-b border-[var(--border)] shrink-0">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5" @click="closeSidebar()">
                    <div class="h-8 w-8 rounded-lg bg-[var(--accent)] flex items-center justify-center">
                        <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <span class="text-sm font-bold text-[var(--text)] tracking-tight">{{ setting('store.app_name', 'MyApp') }}</span>
                        <p class="text-[10px] text-[var(--text-subtle)] -mt-0.5">{{ setting('store.tagline', 'Management Tools') }}</p>
                    </div>
                </a>
                <button @click="closeSidebar()" class="rounded-md p-1.5 text-[var(--text-subtle)] hover:text-[var(--text)] hover:bg-[var(--surface)] md:hidden">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 min-h-0 overflow-y-auto overscroll-contain p-3 sidebar-scroll space-y-0.5">
                {{-- Beranda --}}
                <a href="{{ route('dashboard') }}" @click="closeSidebar()" class="sidebar-link {{ request()->routeIs('dashboard') ? 'sidebar-link-active' : '' }}">
                    <span class="sidebar-icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4"/></svg>
                    </span>
                    Beranda
                </a>

                {{-- ====== BLOX FRUIT ====== --}}
                <div class="pt-5 pb-1.5 px-3">
                    <p class="sidebar-section-label">Blox Fruit</p>
                </div>

                @php
                    $bfMain = [
                        ['route' => 'bloxfruit.dashboard', 'match' => 'bloxfruit.dashboard', 'label' => 'Dashboard', 'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
                        ['route' => 'bloxfruit.search', 'match' => 'bloxfruit.search', 'label' => 'Cari Stok', 'icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'],
                    ];
                @endphp
                @foreach($bfMain as $link)
                <a href="{{ route($link['route']) }}" @click="closeSidebar()" class="sidebar-link {{ request()->routeIs($link['match']) ? 'sidebar-link-active' : '' }}">
                    <span class="sidebar-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $link['icon'] }}"/></svg></span>
                    {{ $link['label'] }}
                </a>
                @endforeach

                <div class="pt-3 pb-1 px-3">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-[var(--text-subtle)]">Operasional</p>
                </div>
                @php
                    $bfOps = [
                        ['route' => 'bloxfruit.storage.index', 'match' => 'bloxfruit.storage.*', 'label' => 'Akun Storage', 'icon' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4'],
                        ['route' => 'bloxfruit.accounts.index', 'match' => 'bloxfruit.accounts.*', 'label' => 'Akun Jual', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                        ['route' => 'bloxfruit.joki.index', 'match' => 'bloxfruit.joki.*', 'label' => 'List Joki', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
                        ['route' => 'bloxfruit.profit.index', 'match' => 'bloxfruit.profit.*', 'label' => 'Keuangan', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['route' => 'bloxfruit.price-analysis', 'match' => 'bloxfruit.price-analysis', 'label' => 'Analisa Harga', 'icon' => 'M3 3v18h18M7 14l4-4 4 4 5-5'],
                        ['route' => 'bloxfruit.rekap', 'match' => 'bloxfruit.rekap', 'label' => 'Rekap Bulanan', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                    ];
                @endphp
                @foreach($bfOps as $link)
                <a href="{{ route($link['route']) }}" @click="closeSidebar()" class="sidebar-link {{ request()->routeIs($link['match']) ? 'sidebar-link-active' : '' }}">
                    <span class="sidebar-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $link['icon'] }}"/></svg></span>
                    {{ $link['label'] }}
                </a>
                @endforeach

                <div class="pt-3 pb-1 px-3">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-[var(--text-subtle)]">Data Master</p>
                </div>
                @php
                    $bfMaster = [
                        ['route' => 'bloxfruit.fruits.index', 'match' => 'bloxfruit.fruits.*', 'label' => 'Daftar Buah', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                        ['route' => 'bloxfruit.skins.index', 'match' => 'bloxfruit.skins.*', 'label' => 'Skin Buah', 'icon' => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01'],
                        ['route' => 'bloxfruit.gamepasses.index', 'match' => 'bloxfruit.gamepasses.*', 'label' => 'Gamepass', 'icon' => 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z'],
                        ['route' => 'bloxfruit.permanents.index', 'match' => 'bloxfruit.permanents.*', 'label' => 'Permanent Fruit', 'icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'],
                        ['route' => 'bloxfruit.joki-services.index', 'match' => 'bloxfruit.joki-services.*', 'label' => 'Jenis Joki', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                    ];
                @endphp
                @foreach($bfMaster as $link)
                <a href="{{ route($link['route']) }}" @click="closeSidebar()" class="sidebar-link {{ request()->routeIs($link['match']) ? 'sidebar-link-active' : '' }}">
                    <span class="sidebar-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $link['icon'] }}"/></svg></span>
                    {{ $link['label'] }}
                </a>
                @endforeach

                <div class="pt-3 pb-1 px-3">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-[var(--text-subtle)]">Pengaturan</p>
                </div>
                <a href="{{ route('settings') }}" @click="closeSidebar()" class="sidebar-link {{ request()->routeIs('settings') ? 'sidebar-link-active' : '' }}">
                    <span class="sidebar-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg></span>
                    Akun
                </a>
                <a href="{{ route('settings.store.edit') }}" @click="closeSidebar()" class="sidebar-link {{ request()->routeIs('settings.store.*') ? 'sidebar-link-active' : '' }}">
                    <span class="sidebar-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg></span>
                    Store &amp; Marketing
                </a>

                <form method="POST" action="{{ route('logout') }}" class="mt-1">
                    @csrf
                    <button type="submit" class="sidebar-link w-full text-left text-[var(--danger)] hover:bg-[var(--danger-soft)] hover:text-[var(--danger)]">
                        <span class="sidebar-icon" style="color: var(--danger)">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </span>
                        Logout
                    </button>
                </form>
            </nav>

            {{-- Footer --}}
            <div class="shrink-0 px-3 py-2.5 border-t border-[var(--border)]">
                <div class="flex items-center justify-between">
                    <p class="text-[10px] text-[var(--text-subtle)] tabular-nums">{{ now()->format('H:i') }} {{ setting('app.timezone_label', 'SGT') }}</p>
                    <button @click="toggleDark()" class="rounded-md p-1 text-[var(--text-subtle)] hover:text-[var(--text)] hover:bg-[var(--surface)] transition-colors">
                        <svg x-show="!darkMode" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        <svg x-show="darkMode" x-cloak class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </button>
                </div>
            </div>
        </aside>

        {{-- ============ MAIN ============ --}}
        <div class="flex-1 min-w-0">
            {{-- Topbar --}}
            <header class="topbar sticky top-0 z-30 flex h-12 items-center gap-2 px-4 sm:px-6">
                <button @click="openSidebar()" class="rounded-md p-1.5 text-[var(--text-muted)] hover:bg-[var(--surface-2)] hover:text-[var(--text)] md:hidden transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-sm font-semibold text-[var(--text)] truncate">@yield('title', 'Dashboard')</h1>
                <div class="ml-auto flex items-center gap-1">
                    <div x-data="{ time: '{{ now()->format('H:i') }}' }"
                         x-init="setInterval(() => { const now = new Date(); time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false }); }, 30000)"
                         class="text-xs text-[var(--text-subtle)] hidden sm:flex items-center gap-2 mr-2 tabular-nums">
                        <span>{{ now()->translatedFormat('D, d M Y') }}</span>
                        <span class="text-[var(--border-hover)]">·</span>
                        <span x-text="time"></span>
                    </div>

                    {{-- Backup Dropdown --}}
                    @php $backupConfigured = !empty(config('services.telegram_backup.bot_token')) && !empty(config('services.telegram_backup.chat_id')); @endphp
                    <div x-data="{ openBackup: false, showSetup: false }" class="relative">
                        <button @click="openBackup = !openBackup" class="rounded-md p-1.5 text-[var(--text-muted)] hover:bg-[var(--surface-2)] hover:text-[var(--text)] transition-colors" title="Backup Database">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        </button>
                        <div x-show="openBackup" @click.away="openBackup = false" x-transition x-cloak
                            class="absolute right-0 mt-1.5 w-72 rounded-lg shadow-[var(--elev-2)] z-50 bg-[var(--surface)] border border-[var(--border)]">
                            <div class="p-1.5">
                                <p class="px-2 py-1.5 text-[10px] font-semibold uppercase tracking-wider text-[var(--text-subtle)]">Backup Database</p>
                                <a href="{{ route('backup.download') }}" class="flex items-center gap-2 px-2 py-2 rounded-md text-sm text-[var(--text-muted)] hover:bg-[var(--surface-2)] hover:text-[var(--text)] transition-colors">
                                    <svg class="h-4 w-4 text-[var(--info)]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Download Backup
                                </a>
                                @if($backupConfigured)
                                <form method="POST" action="{{ route('backup.telegram') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2 px-2 py-2 rounded-md text-sm text-[var(--text-muted)] hover:bg-[var(--surface-2)] hover:text-[var(--text)] transition-colors">
                                        <svg class="h-4 w-4 text-[var(--success)]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                        Kirim ke Telegram
                                    </button>
                                </form>
                                @endif
                            </div>

                            <div class="border-t border-[var(--border)] px-3 py-2.5">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1.5">
                                        <span class="h-1.5 w-1.5 rounded-full" style="background: {{ $backupConfigured ? 'var(--success)' : 'var(--text-subtle)' }}"></span>
                                        <p class="text-[10px] font-semibold text-[var(--text-muted)]">Bot Backup {{ $backupConfigured ? '(Aktif)' : '(Belum Setup)' }}</p>
                                    </div>
                                    <button @click="showSetup = !showSetup" class="text-[10px] font-semibold text-[var(--accent)] hover:underline">{{ $backupConfigured ? 'Edit' : 'Setup' }}</button>
                                </div>

                                <div x-show="showSetup" x-collapse x-cloak class="mt-2.5 space-y-1.5">
                                    <form method="POST" action="{{ route('backup.config') }}" class="space-y-1.5">
                                        @csrf
                                        <input type="text" name="backup_bot_token" value="{{ config('services.telegram_backup.bot_token') }}" placeholder="Bot Token" class="w-full h-8 rounded-md text-xs px-2 bg-[var(--surface-2)] border border-[var(--border)] text-[var(--text)]" required>
                                        <input type="text" name="backup_chat_id" value="{{ config('services.telegram_backup.chat_id') }}" placeholder="Chat ID" class="w-full h-8 rounded-md text-xs px-2 bg-[var(--surface-2)] border border-[var(--border)] text-[var(--text)]" required>
                                        <button type="submit" class="rounded-md bg-[var(--success)] px-2.5 h-7 text-[10px] font-semibold text-white hover:opacity-90 transition-opacity">Simpan</button>
                                    </form>
                                    @if($backupConfigured)
                                    <form method="POST" action="{{ route('backup.test') }}" class="inline-block">
                                        @csrf
                                        <button type="submit" class="rounded-md bg-[var(--info)] px-2.5 h-7 text-[10px] font-semibold text-white hover:opacity-90 transition-opacity">Test</button>
                                    </form>
                                    @endif
                                </div>

                                <p class="text-[10px] text-[var(--text-subtle)] mt-2">Auto backup 4x/hari (02:00, 08:00, 14:00, 20:00)</p>
                            </div>
                        </div>
                    </div>

                    {{-- Dark Mode Toggle --}}
                    <button @click="toggleDark()" class="rounded-md p-1.5 text-[var(--text-muted)] hover:bg-[var(--surface-2)] hover:text-[var(--text)] hidden md:block transition-colors" title="Toggle dark mode">
                        <svg x-show="!darkMode" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        <svg x-show="darkMode" x-cloak class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </button>
                </div>
            </header>

            {{-- Content --}}
            <main class="p-4 sm:p-6 max-w-7xl">
                @if(session('sukses'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2500)"
                     x-transition:leave="transition-opacity duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="mb-4 flex items-center gap-2 px-3 py-2.5 text-sm toast-success">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('sukses') }}
                </div>
                @endif
                @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                     x-transition:leave="transition-opacity duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="mb-4 flex items-center gap-2 px-3 py-2.5 text-sm toast-error">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('error') }}
                </div>
                @endif
                @if($errors->any())
                <div class="mb-4 px-3 py-2.5 text-sm toast-error">
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
                @endif

                {{-- Stock Alert (cached, only renders if there are alerts) --}}
                @if(request()->routeIs('home', 'bloxfruit.*'))
                <x-stock-alert />
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const prefetched = new Set();
            document.querySelectorAll('a[href]').forEach(link => {
                link.addEventListener('mouseenter', () => {
                    const href = link.getAttribute('href');
                    if (href && href.startsWith('/') && !prefetched.has(href)) {
                        prefetched.add(href);
                        const l = document.createElement('link');
                        l.rel = 'prefetch'; l.href = href;
                        document.head.appendChild(l);
                    }
                }, { once: true });
            });
        });
    </script>
</body>
</html>
