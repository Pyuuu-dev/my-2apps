{{-- B. Split Screen — kiri: brand showcase, kanan: form putih --}}
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    @include('auth.templates._head', ['themeColor' => '#4f46e5'])
    <style>
        body.tpl-split { font-family: 'Inter', sans-serif; }
        .tpl-split .showcase-pattern {
            background-image:
                radial-gradient(circle at 20% 30%, rgba(255,255,255,0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(255,255,255,0.05) 0%, transparent 50%);
        }
        .tpl-split .grid-bg {
            background-image:
                linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px);
            background-size: 32px 32px;
        }
    </style>
</head>
<body class="tpl-split auth-page antialiased bg-white">
    <div class="min-h-screen flex">
        {{-- KIRI: Brand showcase (lg+) --}}
        <aside class="hidden lg:flex lg:w-1/2 relative overflow-y-auto text-white"
             style="background: linear-gradient(135deg, #4f46e5 0%, #6366f1 50%, #8b5cf6 100%);">
            <div class="absolute inset-0 grid-bg opacity-50"></div>
            <div class="absolute inset-0 showcase-pattern"></div>

            <div class="relative z-10 flex flex-col justify-between p-6 lg:p-8 xl:p-12 w-full min-h-screen">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-white/15 backdrop-blur-sm flex items-center justify-center ring-1 ring-white/20">
                        <x-brand-logo size="h-5 w-5" extraClass="text-white"/>
                    </div>
                    <span class="text-base font-bold">{{ setting('store.brand_name', 'LDC Store') }}</span>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-white/60 mb-3">Selamat Datang</p>
                    <h2 class="text-2xl lg:text-3xl xl:text-4xl font-extrabold leading-tight mb-4">
                        Kelola stok &amp;<br>joki dengan tenang.
                    </h2>
                    <p class="text-sm lg:text-base text-white/80 leading-relaxed max-w-md">
                        Dashboard terpusat untuk memantau penjualan, profit, dan operasional Blox Fruit kamu. Cepat, akurat, dan dapat diakses kapan saja.
                    </p>

                    <div class="mt-5 lg:mt-6 xl:mt-8 grid grid-cols-3 gap-2 lg:gap-3 xl:gap-4 max-w-md">
                        <div>
                            <p class="text-lg lg:text-xl xl:text-2xl font-extrabold tabular-nums">100%</p>
                            <p class="text-xs text-white/60 mt-0.5">Aman</p>
                        </div>
                        <div>
                            <p class="text-lg lg:text-xl xl:text-2xl font-extrabold tabular-nums">24/7</p>
                            <p class="text-xs text-white/60 mt-0.5">Akses</p>
                        </div>
                        <div>
                            <p class="text-lg lg:text-xl xl:text-2xl font-extrabold tabular-nums">∞</p>
                            <p class="text-xs text-white/60 mt-0.5">Stok</p>
                        </div>
                    </div>
                </div>

                <p class="text-xs text-white/50">&copy; {{ date('Y') }} {{ setting('store.brand_name', 'LDC Store') }} — All rights reserved.</p>
            </div>
        </aside>

        {{-- KANAN: Form --}}
        <main class="w-full lg:w-1/2 flex items-start lg:items-center justify-center p-4 sm:p-6 md:p-8 lg:p-12 py-8 lg:py-12">
            <div class="w-full max-w-[340px] sm:max-w-sm">
                {{-- Mobile/tablet-only logo --}}
                <div class="lg:hidden text-center mb-6 sm:mb-8">
                    <div class="inline-flex items-center justify-center h-14 w-14 rounded-xl bg-indigo-600 mb-3">
                        <x-brand-logo size="h-8 w-8" extraClass="text-white"/>
                    </div>
                    <h1 class="text-xl font-extrabold text-slate-900">{{ setting('store.brand_name', 'LDC Store') }}</h1>
                </div>

                <div class="mb-6 lg:mb-8">
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Masuk ke akun</h1>
                    <p class="text-sm text-slate-500 mt-1.5">Gunakan kredensial admin untuk melanjutkan.</p>
                </div>

                @include('auth.templates._errors', [
                    'errorClass' => 'mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700'
                ])

                <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="login-username" class="block text-sm font-semibold text-slate-700 mb-1.5">Username</label>
                        <input type="text" name="username" id="login-username" autocomplete="username" value="{{ old('username') }}" required autofocus
                            class="auth-input-light w-full rounded-lg bg-white border border-slate-300 text-slate-900 px-4 py-2.5 text-sm placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-colors"
                            placeholder="admin">
                    </div>
                    <div>
                        <label for="login-password" class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label>
                        @include('auth.templates._password_input', [
                            'inputClass' => 'auth-input-light w-full rounded-lg bg-white border border-slate-300 text-slate-900 px-4 py-2.5 text-sm placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-colors',
                            'toggleClass' => 'text-slate-400 hover:text-slate-600',
                            'iconClass' => 'h-5 w-5',
                        ])
                    </div>
                    <div class="flex items-center">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm text-slate-600">Ingat saya 30 hari</span>
                        </label>
                    </div>

                    @if(config('services.turnstile.site_key'))
                    <div class="flex justify-center">
                        <div class="cf-turnstile"
                             data-sitekey="{{ config('services.turnstile.site_key') }}"
                             data-theme="light"
                             data-language="id"></div>
                    </div>
                    @endif

                    <button type="submit" class="auth-submit-btn w-full rounded-lg bg-indigo-600 hover:bg-indigo-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-indigo-500/30 transition-colors">
                        Masuk ke Dashboard
                    </button>
                </form>

                <p class="text-xs text-slate-400 text-center mt-8">
                    Akses dibatasi untuk admin.
                </p>
            </div>
        </main>
    </div>
</body>
</html>
