{{-- E. Floating Glass — animated gradient bg + glassmorphism card (with @supports fallback) --}}
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    @include('auth.templates._head', ['themeColor' => '#0f172a'])
    <style>
        body.tpl-glass { font-family: 'Inter', sans-serif; }
        .tpl-glass-bg {
            position: fixed; inset: 0; z-index: -1; overflow: hidden;
            background: #0a0a14;
        }
        .tpl-glass-bg .blob {
            position: absolute; border-radius: 9999px;
            filter: blur(80px); opacity: 0.6; mix-blend-mode: screen;
            animation: glassFloat 18s ease-in-out infinite;
            will-change: transform;
        }
        .tpl-glass-bg .blob-1 { background: #7c3aed; width: 40vw; height: 40vw; top: -10vw; left: -10vw; animation-delay: 0s; }
        .tpl-glass-bg .blob-2 { background: #06b6d4; width: 35vw; height: 35vw; bottom: -10vw; right: -5vw; animation-delay: -6s; }
        .tpl-glass-bg .blob-3 { background: #ec4899; width: 30vw; height: 30vw; top: 30vh; right: 20vw; animation-delay: -12s; }
        @keyframes glassFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(8vw, 6vh) scale(1.1); }
            66% { transform: translate(-6vw, -4vh) scale(0.95); }
        }
        /* Card: solid fallback, glassy upgrade dengan @supports */
        .tpl-glass .glass-card {
            background: rgba(20, 16, 30, 0.92);
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }
        @supports ((backdrop-filter: blur(20px)) or (-webkit-backdrop-filter: blur(20px))) {
            .tpl-glass .glass-card {
                background: rgba(255, 255, 255, 0.06);
                backdrop-filter: blur(24px) saturate(180%);
                -webkit-backdrop-filter: blur(24px) saturate(180%);
            }
        }
        .tpl-glass input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .tpl-glass input:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(124, 58, 237, 0.6);
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.15);
        }
        /* Mobile: kurangi blob & blur radius supaya GPU lebih ringan */
        @media (max-width: 639px) {
            .tpl-glass-bg .blob { filter: blur(50px); }
            .tpl-glass-bg .blob-3 { display: none; }
        }
        @media (max-width: 414px) {
            .tpl-glass-bg .blob-2 { display: none; }
            .tpl-glass-bg .blob-1 { filter: blur(40px); width: 70vw; height: 70vw; }
        }
        @media (prefers-reduced-motion: reduce) {
            .tpl-glass-bg .blob { animation: none; opacity: 0.35; filter: blur(40px); }
        }
    </style>
</head>
<body class="tpl-glass auth-page antialiased flex items-center justify-center p-4 sm:p-6 text-white">
    <div class="tpl-glass-bg" aria-hidden="true">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
    </div>

    <main class="w-full max-w-[340px] sm:max-w-sm py-6">
        <div class="glass-card rounded-2xl p-6 sm:p-8">
            <div class="flex flex-col items-center mb-6 sm:mb-7">
                <div class="relative">
                    <div class="absolute inset-0 rounded-2xl blur-xl"
                         style="background: linear-gradient(135deg, #7c3aed 0%, #06b6d4 100%); opacity: 0.6;"></div>
                    <div class="relative inline-flex items-center justify-center h-13 w-13 sm:h-14 sm:w-14 rounded-2xl"
                         style="background: linear-gradient(135deg, #7c3aed 0%, #06b6d4 100%); height: 3.25rem; width: 3.25rem;">
                        <x-brand-logo size="h-6 w-6 sm:h-7 sm:w-7" extraClass="text-white"/>
                    </div>
                </div>
                <h1 class="text-lg sm:text-xl font-extrabold text-white mt-4 tracking-tight">{{ setting('store.brand_name', 'LDC Store') }}</h1>
                <p class="text-[12px] sm:text-[13px] text-white/70 mt-1">Authentication required</p>
            </div>

            @include('auth.templates._errors', [
                'errorClass' => 'mb-4 rounded-lg backdrop-blur-md px-3.5 py-2.5 text-sm text-red-200 border border-red-400/30'
            ])

            <form method="POST" action="{{ route('login.post') }}" class="space-y-3 sm:space-y-3.5">
                @csrf
                <div>
                    <label for="login-username" class="block text-[11px] font-semibold uppercase tracking-wider text-white/70 mb-1.5">Username</label>
                    <input type="text" name="username" id="login-username" autocomplete="username" value="{{ old('username') }}" required autofocus
                        class="auth-input-dark w-full rounded-lg text-white px-3.5 py-2.5 text-sm placeholder-white/40 focus:outline-none transition-all"
                        placeholder="admin">
                </div>
                <div>
                    <label for="login-password" class="block text-[11px] font-semibold uppercase tracking-wider text-white/70 mb-1.5">Password</label>
                    @include('auth.templates._password_input', [
                        'inputClass' => 'auth-input-dark w-full rounded-lg text-white px-3.5 py-2.5 text-sm placeholder-white/40 focus:outline-none transition-all',
                        'toggleClass' => 'text-white/50 hover:text-white/80',
                    ])
                </div>
                <div class="flex items-center pt-0.5">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-white/20 bg-white/5 text-violet-600 focus:ring-violet-500">
                        <span class="text-sm text-white/70">Ingat saya</span>
                    </label>
                </div>

                @if(config('services.turnstile.site_key'))
                <div class="flex justify-center pt-1">
                    <div class="cf-turnstile"
                         data-sitekey="{{ config('services.turnstile.site_key') }}"
                         data-theme="dark"
                         data-language="id"></div>
                </div>
                @endif

                <button type="submit" class="auth-submit-btn w-full rounded-lg px-4 py-2.5 text-sm font-bold text-white transition-all mt-1"
                    style="background: linear-gradient(135deg, #7c3aed 0%, #06b6d4 100%); box-shadow: 0 8px 20px rgba(124, 58, 237, 0.4);">
                    Authenticate
                </button>
            </form>
        </div>

        <p class="text-center text-[10px] text-white/40 mt-5 tracking-wider uppercase">
            Secured by {{ setting('store.brand_name', 'LDC Store') }}
        </p>
    </main>
</body>
</html>
