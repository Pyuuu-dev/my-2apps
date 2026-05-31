{{-- T. Holographic Foil — iridescent rainbow shifting gradient --}}
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    @include('auth.templates._head', ['themeColor' => '#dbeafe'])
    <style>
        body.tpl-holo {
            font-family: 'Inter', sans-serif;
            color: #1f2937;
            background: #f0f4ff;
            background-image:
                conic-gradient(from 0deg at 50% 50%,
                    #fce7f3 0deg,
                    #ddd6fe 60deg,
                    #a5f3fc 120deg,
                    #d1fae5 180deg,
                    #fef9c3 240deg,
                    #fed7aa 300deg,
                    #fce7f3 360deg);
            background-attachment: fixed;
        }
        .tpl-holo::before {
            content: ''; position: fixed; inset: 0; z-index: -1;
            background:
                radial-gradient(at 20% 30%, rgba(236, 72, 153, 0.3) 0px, transparent 40%),
                radial-gradient(at 80% 70%, rgba(34, 211, 238, 0.3) 0px, transparent 40%),
                radial-gradient(at 50% 50%, rgba(250, 204, 21, 0.18) 0px, transparent 40%);
        }
        .tpl-holo .holo-card {
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow:
                0 25px 60px -15px rgba(168, 85, 247, 0.3),
                0 10px 30px -10px rgba(34, 211, 238, 0.25),
                inset 0 1px 0 rgba(255, 255, 255, 1);
        }
        @supports ((backdrop-filter: blur(20px)) or (-webkit-backdrop-filter: blur(20px))) {
            .tpl-holo .holo-card {
                background: rgba(255, 255, 255, 0.55);
                backdrop-filter: blur(28px) saturate(200%);
                -webkit-backdrop-filter: blur(28px) saturate(200%);
            }
        }
        .tpl-holo .holo-input {
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(168, 85, 247, 0.18);
            color: #1f2937;
            transition: all 0.15s ease;
        }
        .tpl-holo .holo-input:focus {
            outline: none;
            background: #ffffff;
            border-color: #a855f7;
            box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.2);
        }
        .tpl-holo .holo-input::placeholder { color: rgba(31, 41, 55, 0.4); }
        .tpl-holo .holo-text {
            background: linear-gradient(120deg, #ec4899 0%, #a855f7 30%, #06b6d4 60%, #10b981 100%);
            background-size: 300% 300%;
            -webkit-background-clip: text; background-clip: text;
            color: transparent;
            animation: holo-shift 6s ease-in-out infinite;
            will-change: background-position;
        }
        .tpl-holo .holo-btn {
            background: linear-gradient(120deg, #ec4899 0%, #a855f7 25%, #06b6d4 50%, #10b981 75%, #ec4899 100%);
            background-size: 300% 100%;
            color: white;
            box-shadow: 0 12px 30px -8px rgba(168, 85, 247, 0.5);
            transition: all 0.2s ease;
            animation: holo-btn-shift 5s linear infinite;
            will-change: background-position;
        }
        @keyframes holo-shift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        @keyframes holo-btn-shift {
            0% { background-position: 0% 50%; }
            100% { background-position: 200% 50%; }
        }
        @media (hover: hover) and (pointer: fine) {
            .tpl-holo .holo-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 16px 36px -8px rgba(168, 85, 247, 0.6);
            }
        }
        @media (prefers-reduced-motion: reduce) {
            .tpl-holo .holo-text, .tpl-holo .holo-btn { animation: none; }
            .tpl-holo .holo-btn { transition: none; }
            .tpl-holo .holo-btn:hover { transform: none; }
        }
        @media (max-width: 414px) {
            .tpl-holo .holo-text { animation-duration: 12s; }
            .tpl-holo .holo-btn { animation-duration: 10s; }
        }
    </style>
</head>
<body class="tpl-holo auth-page antialiased flex items-center justify-center p-4 sm:p-6">
    <main class="w-full max-w-[340px] sm:max-w-[400px] py-6">
        <div class="holo-card rounded-3xl p-6 sm:p-8">
            <div class="flex flex-col items-center mb-6">
                <div class="h-14 w-14 sm:h-16 sm:w-16 rounded-2xl flex items-center justify-center mb-3 shadow-lg"
                     style="background: linear-gradient(120deg, #ec4899, #a855f7, #06b6d4); box-shadow: 0 12px 24px -6px rgba(168, 85, 247, 0.5);">
                    <x-brand-logo size="h-7 w-7 sm:h-8 sm:w-8" extraClass="text-white"/>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-center holo-text">{{ setting('store.brand_name', 'LDC Store') }}</h1>
                <p class="text-[12px] sm:text-[13px] text-slate-700 mt-1 text-center font-medium">Selamat datang kembali</p>
            </div>

            @include('auth.templates._errors', [
                'errorClass' => 'mb-4 rounded-2xl px-3.5 py-2.5 text-[13px] text-rose-700 bg-rose-100/70 border border-rose-300/40'
            ])

            <form method="POST" action="{{ route('login.post') }}" class="space-y-3.5">
                @csrf
                <div>
                    <label for="login-username" class="block text-[12px] font-semibold text-slate-700 mb-1.5">Username</label>
                    <input type="text" name="username" id="login-username" autocomplete="username" value="{{ old('username') }}" required autofocus
                        class="auth-input-light holo-input w-full rounded-xl px-3.5 py-2.5 text-sm"
                        placeholder="admin">
                </div>
                <div>
                    <label for="login-password" class="block text-[12px] font-semibold text-slate-700 mb-1.5">Password</label>
                    @include('auth.templates._password_input', [
                        'inputClass' => 'auth-input-light holo-input w-full rounded-xl px-3.5 py-2.5 text-sm',
                        'toggleClass' => 'text-slate-400 hover:text-purple-600',
                    ])
                </div>
                <div class="pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-purple-300 text-purple-600 focus:ring-purple-500">
                        <span class="text-[13px] text-slate-700">Ingat saya</span>
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

                <button type="submit" class="holo-btn auth-submit-btn w-full rounded-xl px-4 py-3 text-sm font-bold mt-2">
                    ✨ Masuk
                </button>
            </form>
        </div>

        <p class="text-center text-[11px] text-slate-700 font-medium mt-5">
            &copy; {{ date('Y') }} {{ setting('store.brand_name', 'LDC Store') }}
        </p>
    </main>
</body>
</html>
