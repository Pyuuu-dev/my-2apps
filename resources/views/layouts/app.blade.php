<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#0f172a">
    <title>@yield('title', 'Dashboard') - MyApp</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
    x-init="if(darkMode) document.documentElement.classList.add('dark')"
    x-cloak>
    <div class="min-h-full md:flex">
        {{-- Mobile backdrop --}}
        <div x-show="sidebarOpen"
            x-transition:enter="transition-opacity duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm md:hidden"
            @click="closeSidebar()"></div>

        {{-- ============ SIDEBAR ============ --}}
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-72 transition-transform duration-250 ease-out md:translate-x-0 md:static md:shrink-0 md:w-64 sidebar-bg flex flex-col">

            {{-- Logo --}}
            <div class="flex h-16 items-center justify-between px-5 border-b border-white/10 shrink-0">
                <a href="{{ route('home') }}" class="flex items-center gap-3" @click="closeSidebar()">
                    <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <span class="text-lg font-extrabold text-white tracking-tight">MyApp</span>
                        <p class="text-[10px] text-gray-500 -mt-0.5">Management Tools</p>
                    </div>
                </a>
                <button @click="closeSidebar()" class="rounded-lg p-1.5 text-gray-500 hover:text-white hover:bg-white/10 md:hidden">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Nav - scrollable --}}
            <nav class="flex-1 min-h-0 overflow-y-auto overscroll-contain p-4 space-y-1 sidebar-scroll">
                {{-- Beranda --}}
                <a href="{{ route('home') }}" @click="closeSidebar()" class="sidebar-link {{ request()->routeIs('home') ? 'sidebar-link-active' : 'text-gray-400' }}">
                    <div class="sidebar-icon bg-gradient-to-br from-gray-600 to-gray-700">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4"/></svg>
                    </div>
                    Beranda
                </a>

                {{-- ====== BLOX FRUIT ====== --}}
                <div class="pt-4">
                    <div class="flex items-center gap-2 px-3 pb-2">
                        <div class="h-1 w-4 rounded-full bg-indigo-500/50"></div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-indigo-400/80">Blox Fruit</p>
                        <div class="flex-1 h-px bg-white/5"></div>
                    </div>

                    {{-- BF: Utama --}}
                    @php
                        $bfMain = [
                            ['route' => 'bloxfruit.dashboard', 'match' => 'bloxfruit.dashboard', 'label' => 'Dashboard', 'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z', 'gradient' => 'from-indigo-500 to-indigo-600'],
                            ['route' => 'bloxfruit.search', 'match' => 'bloxfruit.search', 'label' => 'Cari Stok', 'icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z', 'gradient' => 'from-violet-500 to-violet-600'],
                        ];
                    @endphp
                    @foreach($bfMain as $link)
                    <a href="{{ route($link['route']) }}" @click="closeSidebar()" class="sidebar-link {{ request()->routeIs($link['match']) ? 'sidebar-link-active' : 'text-gray-400' }}">
                        <div class="sidebar-icon bg-gradient-to-br {{ $link['gradient'] }}">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}"/></svg>
                        </div>
                        {{ $link['label'] }}
                    </a>
                    @endforeach

                    {{-- BF: Operasional --}}
                    <p class="px-3 pt-3 pb-1 text-[9px] font-semibold uppercase tracking-wider text-gray-600">Operasional</p>
                    @php
                        $bfOps = [
                            ['route' => 'bloxfruit.storage.index', 'match' => 'bloxfruit.storage.*', 'label' => 'Akun Storage', 'icon' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4', 'gradient' => 'from-cyan-500 to-cyan-600'],
                            ['route' => 'bloxfruit.accounts.index', 'match' => 'bloxfruit.accounts.*', 'label' => 'Akun Jual', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'gradient' => 'from-sky-500 to-sky-600'],
                            ['route' => 'bloxfruit.joki.index', 'match' => 'bloxfruit.joki.*', 'label' => 'List Joki', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01', 'gradient' => 'from-amber-500 to-amber-600'],
                            ['route' => 'bloxfruit.profit.index', 'match' => 'bloxfruit.profit.*', 'label' => 'Keuangan', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'gradient' => 'from-emerald-500 to-emerald-600'],
                        ];
                    @endphp
                    @foreach($bfOps as $link)
                    <a href="{{ route($link['route']) }}" @click="closeSidebar()" class="sidebar-link {{ request()->routeIs($link['match']) ? 'sidebar-link-active' : 'text-gray-400' }}">
                        <div class="sidebar-icon bg-gradient-to-br {{ $link['gradient'] }}">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}"/></svg>
                        </div>
                        {{ $link['label'] }}
                    </a>
                    @endforeach

                    {{-- BF: Data Master --}}
                    <p class="px-3 pt-3 pb-1 text-[9px] font-semibold uppercase tracking-wider text-gray-600">Data Master</p>
                    @php
                        $bfMaster = [
                            ['route' => 'bloxfruit.fruits.index', 'match' => 'bloxfruit.fruits.*', 'label' => 'Daftar Buah', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'gradient' => 'from-purple-500 to-purple-600'],
                            ['route' => 'bloxfruit.skins.index', 'match' => 'bloxfruit.skins.*', 'label' => 'Skin Buah', 'icon' => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01', 'gradient' => 'from-pink-500 to-pink-600'],
                            ['route' => 'bloxfruit.gamepasses.index', 'match' => 'bloxfruit.gamepasses.*', 'label' => 'Gamepass', 'icon' => 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z', 'gradient' => 'from-blue-500 to-blue-600'],
                            ['route' => 'bloxfruit.permanents.index', 'match' => 'bloxfruit.permanents.*', 'label' => 'Permanent Fruit', 'icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z', 'gradient' => 'from-amber-500 to-amber-600'],
                            ['route' => 'bloxfruit.joki-services.index', 'match' => 'bloxfruit.joki-services.*', 'label' => 'Jenis Joki', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'gradient' => 'from-red-500 to-red-600'],
                        ];
                    @endphp
                    @foreach($bfMaster as $link)
                    <a href="{{ route($link['route']) }}" @click="closeSidebar()" class="sidebar-link {{ request()->routeIs($link['match']) ? 'sidebar-link-active' : 'text-gray-400' }}">
                        <div class="sidebar-icon bg-gradient-to-br {{ $link['gradient'] }}">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}"/></svg>
                        </div>
                        {{ $link['label'] }}
                    </a>
                    @endforeach
                </div>

                {{-- ====== DIET TRACKER ====== --}}
                <div class="pt-4">
                    <div class="flex items-center gap-2 px-3 pb-2">
                        <div class="h-1 w-4 rounded-full bg-emerald-500/50"></div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-emerald-400/80">Diet Tracker</p>
                        <div class="flex-1 h-px bg-white/5"></div>
                    </div>

                    {{-- DT: Utama --}}
                    @php
                        $dtMain = [
                            ['route' => 'diet.dashboard', 'match' => 'diet.dashboard', 'label' => 'Dashboard', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'gradient' => 'from-emerald-500 to-emerald-600'],
                        ];
                    @endphp
                    @foreach($dtMain as $link)
                    <a href="{{ route($link['route']) }}" @click="closeSidebar()" class="sidebar-link {{ request()->routeIs($link['match']) ? 'sidebar-link-active-green' : 'text-gray-400' }}">
                        <div class="sidebar-icon bg-gradient-to-br {{ $link['gradient'] }}">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}"/></svg>
                        </div>
                        {{ $link['label'] }}
                    </a>
                    @endforeach

                    {{-- DT: Tracking --}}
                    <p class="px-3 pt-3 pb-1 text-[9px] font-semibold uppercase tracking-wider text-gray-600">Tracking</p>
                    @php
                        $dtTrack = [
                            ['route' => 'diet.meals.index', 'match' => 'diet.meals.*', 'label' => 'Jadwal Makan', 'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z', 'gradient' => 'from-orange-500 to-orange-600'],
                            ['route' => 'diet.exercises.index', 'match' => 'diet.exercises.*', 'label' => 'Olahraga', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'gradient' => 'from-red-500 to-red-600'],
                            ['route' => 'diet.activities.index', 'match' => 'diet.activities.*', 'label' => 'Aktivitas Harian', 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', 'gradient' => 'from-teal-500 to-teal-600'],
                        ];
                    @endphp
                    @foreach($dtTrack as $link)
                    <a href="{{ route($link['route']) }}" @click="closeSidebar()" class="sidebar-link {{ request()->routeIs($link['match']) ? 'sidebar-link-active-green' : 'text-gray-400' }}">
                        <div class="sidebar-icon bg-gradient-to-br {{ $link['gradient'] }}">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}"/></svg>
                        </div>
                        {{ $link['label'] }}
                    </a>
                    @endforeach

                    {{-- DT: Pengaturan --}}
                    <p class="px-3 pt-3 pb-1 text-[9px] font-semibold uppercase tracking-wider text-gray-600">Pengaturan</p>
                    @php
                        $dtSettings = [
                            ['route' => 'diet.reminders.index', 'match' => 'diet.reminders.*', 'label' => 'Pengingat', 'icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9', 'gradient' => 'from-yellow-500 to-yellow-600'],
                            ['route' => 'settings', 'match' => 'settings*', 'label' => 'Akun', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', 'gradient' => 'from-gray-500 to-gray-600'],
                        ];
                    @endphp
                    @foreach($dtSettings as $link)
                    <a href="{{ route($link['route']) }}" @click="closeSidebar()" class="sidebar-link {{ request()->routeIs($link['match']) ? 'sidebar-link-active-green' : 'text-gray-400' }}">
                        <div class="sidebar-icon bg-gradient-to-br {{ $link['gradient'] }}">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}"/></svg>
                        </div>
                        {{ $link['label'] }}
                    </a>
                    @endforeach

                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <button type="submit" class="sidebar-link text-red-400 hover:bg-red-500/10 w-full text-left">
                            <div class="sidebar-icon bg-gradient-to-br from-red-500 to-red-600">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            </div>
                            Logout
                        </button>
                    </form>
                </div>
            </nav>

            {{-- Footer --}}
            <div class="shrink-0 px-4 py-3 border-t border-white/5">
                <div class="flex items-center justify-between">
                    <p class="text-[10px] text-gray-600">{{ now()->format('H:i') }} SGT</p>
                    <button @click="toggleDark()" class="rounded-lg p-1.5 transition-colors" :class="darkMode ? 'text-yellow-400 hover:bg-white/10' : 'text-gray-500 hover:bg-white/10'">
                        <svg x-show="!darkMode" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        <svg x-show="darkMode" x-cloak class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </button>
                </div>
            </div>
        </aside>

        {{-- ============ MAIN ============ --}}
        <div class="flex-1 min-w-0">
            {{-- Topbar --}}
            <header class="topbar sticky top-0 z-30 flex h-14 items-center gap-3 px-4 sm:px-6 transition-colors duration-200">
                <button @click="openSidebar()" class="rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 dark:hover:bg-slate-700 hover:text-gray-700 dark:hover:text-gray-200 md:hidden">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-base font-semibold text-gray-800 dark:text-gray-100 truncate">@yield('title', 'Dashboard')</h1>
                <div class="ml-auto flex items-center gap-1.5">
                    <div x-data="{ time: '{{ now()->format('H:i:s') }}' }" x-init="setInterval(() => { const now = new Date(); time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false }); }, 1000)" class="text-xs text-gray-400 hidden sm:block mr-2">
                        <span>{{ now()->translatedFormat('l, d F Y') }}</span>
                        <span class="mx-1.5 text-gray-300">•</span>
                        <span x-text="time" class="font-mono"></span>
                    </div>

                    {{-- Backup Dropdown --}}
                    @php $backupConfigured = !empty(config('services.telegram_backup.bot_token')) && !empty(config('services.telegram_backup.chat_id')); @endphp
                    <div x-data="{ openBackup: false, showSetup: false }" class="relative">
                        <button @click="openBackup = !openBackup" class="rounded-lg p-1.5 transition-colors" :class="darkMode ? 'text-gray-400 hover:bg-slate-700 hover:text-gray-200' : 'text-gray-400 hover:bg-gray-100 hover:text-gray-600'">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        </button>
                        <div x-show="openBackup" @click.away="openBackup = false" x-transition
                            class="absolute right-0 mt-2 w-72 rounded-xl shadow-lg border z-50"
                            :class="darkMode ? 'bg-slate-800 border-slate-700' : 'bg-white border-gray-200'">
                            <div class="p-2">
                                <p class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider" :class="darkMode ? 'text-gray-500' : 'text-gray-400'">Backup Database</p>
                                <a href="{{ route('backup.download') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-colors" :class="darkMode ? 'text-gray-300 hover:bg-slate-700' : 'text-gray-700 hover:bg-gray-50'">
                                    <svg class="h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Download Backup
                                </a>
                                @if($backupConfigured)
                                <form method="POST" action="{{ route('backup.telegram') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-colors" :class="darkMode ? 'text-gray-300 hover:bg-slate-700' : 'text-gray-700 hover:bg-gray-50'">
                                        <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                        Kirim ke Telegram
                                    </button>
                                </form>
                                @endif
                            </div>

                            {{-- Setup Bot Backup --}}
                            <div class="border-t px-3 py-2" :class="darkMode ? 'border-slate-700' : 'border-gray-100'">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1.5">
                                        <div class="h-2 w-2 rounded-full {{ $backupConfigured ? 'bg-emerald-500' : 'bg-gray-400' }}"></div>
                                        <p class="text-[10px] font-semibold" :class="darkMode ? 'text-gray-400' : 'text-gray-500'">Bot Backup {{ $backupConfigured ? '(Aktif)' : '(Belum Setup)' }}</p>
                                    </div>
                                    <button @click="showSetup = !showSetup" class="text-[10px] font-medium text-indigo-500 hover:text-indigo-400">{{ $backupConfigured ? 'Edit' : 'Setup' }}</button>
                                </div>

                                <div x-show="showSetup" x-collapse x-cloak class="mt-2">
                                    <form method="POST" action="{{ route('backup.config') }}" class="space-y-2">
                                        @csrf
                                        <input type="text" name="backup_bot_token" value="{{ config('services.telegram_backup.bot_token') }}" placeholder="Bot Token" class="w-full rounded-lg text-xs px-3 py-1.5" :class="darkMode ? 'bg-slate-900 border-slate-600 text-gray-200' : 'border-gray-300'" required>
                                        <input type="text" name="backup_chat_id" value="{{ config('services.telegram_backup.chat_id') }}" placeholder="Chat ID" class="w-full rounded-lg text-xs px-3 py-1.5" :class="darkMode ? 'bg-slate-900 border-slate-600 text-gray-200' : 'border-gray-300'" required>
                                        <div class="flex gap-1.5">
                                            <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-1 text-[10px] font-semibold text-white hover:bg-emerald-700">Simpan</button>
                                            @if($backupConfigured)
                                            </form>
                                            <form method="POST" action="{{ route('backup.test') }}">
                                                @csrf
                                                <button type="submit" class="rounded-lg bg-blue-600 px-3 py-1 text-[10px] font-semibold text-white hover:bg-blue-700">Test</button>
                                            </form>
                                            @else
                                            </form>
                                            @endif
                                        </div>
                                </div>

                                <p class="text-[10px] mt-1.5" :class="darkMode ? 'text-gray-600' : 'text-gray-400'">Auto backup 4x/hari (02:00, 08:00, 14:00, 20:00)</p>
                            </div>
                        </div>
                    </div>

                    {{-- Dark Mode Toggle --}}
                    <button @click="toggleDark()" class="rounded-lg p-1.5 transition-colors hidden md:block" :class="darkMode ? 'text-yellow-400 hover:bg-slate-700' : 'text-gray-400 hover:bg-gray-100'">
                        <svg x-show="!darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        <svg x-show="darkMode" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </button>
                </div>
            </header>

            {{-- Content --}}
            <main class="p-4 sm:p-6 max-w-7xl dark:text-gray-200">
                @if(session('sukses'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2500)"
                     x-transition:leave="transition-opacity duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="mb-4 flex items-center gap-2 rounded-xl px-4 py-3 text-sm text-emerald-700 toast-success">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('sukses') }}
                </div>
                @endif
                @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                     x-transition:leave="transition-opacity duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="mb-4 flex items-center gap-2 rounded-xl px-4 py-3 text-sm text-red-700 toast-error">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('error') }}
                </div>
                @endif
                @if($errors->any())
                <div class="mb-4 rounded-xl px-4 py-3 text-sm text-red-700 toast-error">
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
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
