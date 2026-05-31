{{-- D. Card on Image — full-screen image bg, semi-transparent overlay card --}}
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    @include('auth.templates._head', ['themeColor' => '#1e293b'])
    <style>
        body.tpl-image { font-family: 'Inter', sans-serif; }
        .tpl-image-bg {
            position: fixed; inset: 0; z-index: -1;
            background:
                radial-gradient(ellipse at top left, rgba(220, 38, 38, 0.35) 0%, transparent 55%),
                radial-gradient(ellipse at bottom right, rgba(250, 204, 21, 0.25) 0%, transparent 50%),
                radial-gradient(ellipse at center, rgba(15, 23, 42, 0) 0%, rgba(2, 6, 23, 0.85) 100%),
                linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }
        .tpl-image-bg::before {
            content: ''; position: absolute; inset: 0;
            background-image:
                radial-gradient(circle at 15% 50%, rgba(255,255,255,0.06) 0%, transparent 25%),
                radial-gradient(circle at 85% 30%, rgba(255,255,255,0.04) 0%, transparent 25%);
        }
        @media (min-width: 640px) {
            .tpl-image-bg::after {
                content: ''; position: absolute; inset: 0;
                background-image:
                    linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(255,255,255,0.025) 1px, transparent 1px);
                background-size: 48px 48px;
                mask-image: radial-gradient(ellipse at center, rgba(0,0,0,0.6), transparent 70%);
                -webkit-mask-image: radial-gradient(ellipse at center, rgba(0,0,0,0.6), transparent 70%);
            }
        }
        .tpl-image .glow-ring {
            background: linear-gradient(90deg, #dc2626, #facc15, #ef4444, #dc2626);
            filter: blur(8px); opacity: 0.6;
        }
        @supports (background: conic-gradient(from 0deg, red, blue)) {
            .tpl-image .glow-ring {
                background: conic-gradient(from 0deg, #dc2626, #facc15, #ef4444, #dc2626);
            }
        }
        .tpl-image-card { background: rgba(2, 6, 23, 0.92); }
        @supports ((backdrop-filter: blur(20px)) or (-webkit-backdrop-filter: blur(20px))) {
            .tpl-image-card {
                background: linear-gradient(180deg, rgba(15, 23, 42, 0.7) 0%, rgba(2, 6, 23, 0.85) 100%);
                backdrop-filter: blur(20px) saturate(150%);
                -webkit-backdrop-filter: blur(20px) saturate(150%);
            }
        }
        @media (prefers-reduced-motion: reduce) {
            .tpl-image .glow-ring { animation: none; filter: blur(4px); opacity: 0.4; }
        }
    </style>
</head>
<body class="tpl-image auth-page antialiased flex items-center justify-center p-3 sm:p-4 md:p-6 text-white">
    <div class="tpl-image-bg" aria-hidden="true"></div>

    <main class="w-full max-w-[340px] sm:max-w-md relative py-4">
        <div class="hidden sm:block absolute -inset-1 rounded-3xl glow-ring" aria-hidden="true"></div>

        <div class="tpl-image-card relative rounded-2xl sm:rounded-3xl overflow-hidden border border-white/10 shadow-xl sm:shadow-2xl">

            <div class="px-5 pt-6 pb-4 sm:px-7 sm:pt-7 sm:pb-5 border-b border-white/5">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 sm:h-11 sm:w-11 rounded-xl bg-gradient-to-br from-red-500 to-amber-500 flex items-center justify-center shadow-lg shadow-red-500/30 shrink-0">
                        <x-brand-logo size="h-5 w-5 sm:h-6 sm:w-6" extraClass="text-white"/>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm sm:text-base font-extrabold tracking-tight truncate">{{ setting('store.brand_name', 'LDC Store') }}</p>
                        <p class="text-[10px] sm:text-[11px] text-white/60 -mt-0.5 truncate">{{ setting('store.tagline', 'Management Tools') }}</p>
                    </div>
                </div>
            </div>

            <div class="px-5 py-5 sm:px-7 sm:py-6">
                <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight">Welcome back, Captain.</h1>
                <p class="text-xs sm:text-sm text-white/70 mt-1">Set sail. Login untuk lanjut ke kontrol stok.</p>

                <div class="mt-5 sm:mt-6">
                    @include('auth.templates._errors', [
                        'errorClass' => 'mb-4 rounded-lg bg-red-500/15 border border-red-500/30 px-3.5 py-2.5 text-sm text-red-200'
                    ])

                    <form method="POST" action="{{ route('login.post') }}" class="space-y-3 sm:space-y-3.5">
                        @csrf
                        <div>
                            <label for="login-username" class="block text-[11px] sm:text-[12px] font-semibold uppercase tracking-wider text-white/70 mb-1.5">Username</label>
                            <input type="text" name="username" id="login-username" autocomplete="username" value="{{ old('username') }}" required autofocus
                                class="auth-input-dark w-full rounded-lg bg-white/5 border border-white/10 text-white px-3.5 py-2.5 text-sm placeholder-white/40 focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 focus:outline-none focus:bg-white/10 transition-all"
                                placeholder="admin">
                        </div>
                        <div>
                            <label for="login-password" class="block text-[11px] sm:text-[12px] font-semibold uppercase tracking-wider text-white/70 mb-1.5">Password</label>
                            @include('auth.templates._password_input', [
                                'inputClass' => 'auth-input-dark w-full rounded-lg bg-white/5 border border-white/10 text-white px-3.5 py-2.5 text-sm placeholder-white/40 focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 focus:outline-none focus:bg-white/10 transition-all',
                                'toggleClass' => 'text-white/50 hover:text-white/80',
                            ])
                        </div>
                        <div class="flex items-center pt-0.5">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="remember" class="rounded border-white/20 bg-white/5 text-amber-500 focus:ring-amber-400">
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

                        <button type="submit" class="auth-submit-btn w-full rounded-lg bg-gradient-to-r from-red-500 via-amber-500 to-yellow-500 hover:opacity-95 active:opacity-90 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-red-500/30 transition-opacity mt-1">
                            Masuk
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <p class="text-center text-[11px] text-white/50 mt-5">
            &copy; {{ date('Y') }} {{ setting('store.brand_name', 'LDC Store') }} — Sail safe.
        </p>
    </main>
</body>
</html>
