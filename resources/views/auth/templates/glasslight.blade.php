{{-- N. Glassmorphism Light — pastel gradient + frosted white glass card --}}
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    @include('auth.templates._head', ['themeColor' => '#fce7f3'])
    <style>
        body.tpl-glasslight {
            font-family: 'Inter', sans-serif;
            background: #fce7f3;
            position: relative;
            overflow-x: hidden;
            color: #1f2937;
        }
        .tpl-glasslight-bg {
            position: fixed; inset: 0; z-index: -1; overflow: hidden;
            background: linear-gradient(135deg, #fce7f3 0%, #dbeafe 50%, #fef3c7 100%);
        }
        .tpl-glasslight-bg .blob {
            position: absolute; border-radius: 9999px;
            filter: blur(70px); opacity: 0.55;
            animation: gl-float 22s ease-in-out infinite;
            will-change: transform;
        }
        .tpl-glasslight-bg .blob-1 { background: #f9a8d4; width: 36vw; height: 36vw; top: -8vw; left: -6vw; animation-delay: 0s; }
        .tpl-glasslight-bg .blob-2 { background: #93c5fd; width: 32vw; height: 32vw; bottom: -8vw; right: -4vw; animation-delay: -8s; }
        .tpl-glasslight-bg .blob-3 { background: #fcd34d; width: 26vw; height: 26vw; top: 35vh; right: 25vw; animation-delay: -14s; }
        @keyframes gl-float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(6vw, 4vh) scale(1.08); }
            66% { transform: translate(-4vw, -3vh) scale(0.95); }
        }
        .tpl-glasslight .gl-card {
            background: rgba(255, 255, 255, 0.92);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.7);
            box-shadow: 0 20px 50px -10px rgba(31, 41, 55, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.9);
        }
        @supports ((backdrop-filter: blur(20px)) or (-webkit-backdrop-filter: blur(20px))) {
            .tpl-glasslight .gl-card {
                background: rgba(255, 255, 255, 0.55);
                backdrop-filter: blur(24px) saturate(180%);
                -webkit-backdrop-filter: blur(24px) saturate(180%);
            }
        }
        .tpl-glasslight .gl-input {
            background: rgba(255, 255, 255, 0.55);
            border: 1px solid rgba(31, 41, 55, 0.08);
            color: #1f2937;
            transition: all 0.15s ease;
        }
        .tpl-glasslight .gl-input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.95);
            border-color: #ec4899;
            box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.15);
        }
        .tpl-glasslight .gl-input::placeholder { color: rgba(31, 41, 55, 0.4); }
        .tpl-glasslight .gl-btn {
            background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%);
            color: white;
            box-shadow: 0 8px 22px -4px rgba(236, 72, 153, 0.45);
            transition: all 0.15s ease;
        }
        @media (hover: hover) and (pointer: fine) {
            .tpl-glasslight .gl-btn:hover {
                transform: translateY(-1px);
                box-shadow: 0 12px 30px -4px rgba(139, 92, 246, 0.5);
            }
        }
        @media (max-width: 639px) {
            .tpl-glasslight-bg .blob { filter: blur(50px); }
            .tpl-glasslight-bg .blob-3 { display: none; }
        }
        @media (max-width: 414px) {
            .tpl-glasslight-bg .blob-2 { display: none; }
            .tpl-glasslight-bg .blob-1 { filter: blur(35px); }
        }
        @media (prefers-reduced-motion: reduce) {
            .tpl-glasslight-bg .blob { animation: none; }
            .tpl-glasslight .gl-btn { transition: none; }
            .tpl-glasslight .gl-btn:hover { transform: none; }
        }
    </style>
</head>
<body class="tpl-glasslight auth-page antialiased flex items-center justify-center p-4 sm:p-6">
    <div class="tpl-glasslight-bg" aria-hidden="true">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
    </div>

    <main class="w-full max-w-[340px] sm:max-w-[400px] py-6">
        <div class="gl-card p-6 sm:p-8">
            <div class="flex flex-col items-center mb-6 sm:mb-7">
                <div class="relative">
                    <div class="absolute inset-0 rounded-2xl blur-xl opacity-50"
                         style="background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%);"></div>
                    <div class="relative inline-flex items-center justify-center h-14 w-14 sm:h-16 sm:w-16 rounded-2xl"
                         style="background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%);">
                        <x-brand-logo size="h-7 w-7 sm:h-8 sm:w-8" extraClass="text-white"/>
                    </div>
                </div>
                <h1 class="text-[20px] sm:text-[22px] font-extrabold text-slate-900 mt-4 tracking-tight text-center">{{ setting('store.brand_name', 'LDC Store') }}</h1>
                <p class="text-[13px] text-slate-600 mt-1 text-center">Selamat datang kembali</p>
            </div>

            @include('auth.templates._errors', [
                'errorClass' => 'mb-4 rounded-xl px-3.5 py-2.5 text-[13px] text-rose-700 bg-rose-100/70 border border-rose-200/60'
            ])

            <form method="POST" action="{{ route('login.post') }}" class="space-y-3.5">
                @csrf
                <div>
                    <label for="login-username" class="block text-[12px] font-semibold text-slate-700 mb-1.5">Username</label>
                    <input type="text" name="username" id="login-username" autocomplete="username" value="{{ old('username') }}" required autofocus
                        class="auth-input-light gl-input w-full rounded-xl px-3.5 py-2.5 text-sm"
                        placeholder="admin">
                </div>
                <div>
                    <label for="login-password" class="block text-[12px] font-semibold text-slate-700 mb-1.5">Password</label>
                    @include('auth.templates._password_input', [
                        'inputClass' => 'auth-input-light gl-input w-full rounded-xl px-3.5 py-2.5 text-sm',
                        'toggleClass' => 'text-slate-400 hover:text-slate-600',
                    ])
                </div>
                <div class="pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-pink-500 focus:ring-pink-500">
                        <span class="text-[13px] text-slate-600">Ingat saya</span>
                    </label>
                </div>

                @if(config('services.turnstile.site_key'))
                <div class="flex justify-center pt-1">
                    <div class="cf-turnstile"
                         data-sitekey="{{ config('services.turnstile.site_key') }}"
                         data-theme="light"
                         data-language="id"></div>
                </div>
                @endif

                <button type="submit" class="gl-btn auth-submit-btn w-full rounded-xl px-4 py-3 text-sm font-bold mt-2">
                    Masuk
                </button>
            </form>
        </div>

        <p class="text-center text-[11px] text-slate-600 mt-5">
            &copy; {{ date('Y') }} {{ setting('store.brand_name', 'LDC Store') }}
        </p>
    </main>
</body>
</html>
