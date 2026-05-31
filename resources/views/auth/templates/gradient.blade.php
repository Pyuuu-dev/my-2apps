{{-- P. Bold Mesh Gradient — psychedelic multi-color gradient, playful --}}
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    @include('auth.templates._head', ['themeColor' => '#3a86ff'])
    <style>
        body.tpl-gradient {
            font-family: 'Inter', sans-serif;
            color: #ffffff;
            background: #2d1b69;
            background-image:
                radial-gradient(at 5% 10%, hsla(335, 100%, 50%, 0.85) 0px, transparent 50%),
                radial-gradient(at 95% 20%, hsla(20, 100%, 55%, 0.85) 0px, transparent 50%),
                radial-gradient(at 50% 60%, hsla(280, 100%, 60%, 0.7) 0px, transparent 50%),
                radial-gradient(at 90% 90%, hsla(214, 100%, 60%, 0.85) 0px, transparent 50%),
                radial-gradient(at 10% 90%, hsla(170, 100%, 50%, 0.6) 0px, transparent 50%);
        }
        .tpl-gradient .glass-card {
            background: rgba(15, 10, 40, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.55), inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }
        @supports ((backdrop-filter: blur(20px)) or (-webkit-backdrop-filter: blur(20px))) {
            .tpl-gradient .glass-card {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(28px) saturate(160%);
                -webkit-backdrop-filter: blur(28px) saturate(160%);
            }
        }
        .tpl-gradient .grad-input {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: #ffffff;
            transition: all 0.15s ease;
        }
        .tpl-gradient .grad-input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.16);
            border-color: rgba(255, 255, 255, 0.6);
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.18);
        }
        .tpl-gradient .grad-input::placeholder { color: rgba(255, 255, 255, 0.45); }
        .tpl-gradient .grad-btn {
            background: linear-gradient(135deg, #ff006e, #fb5607, #ffbe0b);
            background-size: 200% 200%;
            color: white;
            box-shadow: 0 12px 30px -8px rgba(255, 0, 110, 0.55);
            transition: all 0.2s ease;
            animation: grad-shift 6s ease-in-out infinite;
        }
        @keyframes grad-shift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        @media (hover: hover) and (pointer: fine) {
            .tpl-gradient .grad-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 16px 36px -8px rgba(251, 86, 7, 0.6);
            }
        }
        @media (prefers-reduced-motion: reduce) {
            .tpl-gradient .grad-btn { transition: none; animation: none; }
            .tpl-gradient .grad-btn:hover { transform: none; }
        }
    </style>
</head>
<body class="tpl-gradient auth-page antialiased flex items-center justify-center p-4 sm:p-6">
    <main class="w-full max-w-[340px] sm:max-w-sm py-6">
        <div class="glass-card rounded-2xl p-6 sm:p-8">
            <div class="flex flex-col items-center mb-6">
                <div class="h-14 w-14 sm:h-16 sm:w-16 rounded-2xl flex items-center justify-center mb-3"
                     style="background: linear-gradient(135deg, #ff006e, #fb5607, #ffbe0b); box-shadow: 0 8px 24px -4px rgba(255, 0, 110, 0.5);">
                    <x-brand-logo size="h-7 w-7 sm:h-8 sm:w-8" extraClass="text-white"/>
                </div>
                <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-center">{{ setting('store.brand_name', 'LDC Store') }}</h1>
                <p class="text-[12px] sm:text-[13px] text-white/70 mt-1 text-center">Selamat datang kembali</p>
            </div>

            @include('auth.templates._errors', [
                'errorClass' => 'mb-4 rounded-xl px-3.5 py-2.5 text-[13px] text-red-100 bg-red-500/30 border border-red-300/40'
            ])

            <form method="POST" action="{{ route('login.post') }}" class="space-y-3.5">
                @csrf
                <div>
                    <label for="login-username" class="block text-[12px] font-semibold text-white/80 mb-1.5">Username</label>
                    <input type="text" name="username" id="login-username" autocomplete="username" value="{{ old('username') }}" required autofocus
                        class="auth-input-dark grad-input w-full rounded-xl px-3.5 py-2.5 text-sm"
                        placeholder="admin">
                </div>
                <div>
                    <label for="login-password" class="block text-[12px] font-semibold text-white/80 mb-1.5">Password</label>
                    @include('auth.templates._password_input', [
                        'inputClass' => 'auth-input-dark grad-input w-full rounded-xl px-3.5 py-2.5 text-sm',
                        'toggleClass' => 'text-white/60 hover:text-white',
                    ])
                </div>
                <div class="pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-white/30 bg-white/10 text-pink-500 focus:ring-pink-400">
                        <span class="text-[13px] text-white/80">Ingat saya</span>
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

                <button type="submit" class="grad-btn auth-submit-btn w-full rounded-xl px-4 py-3 text-sm font-bold mt-2">
                    Masuk
                </button>
            </form>
        </div>

        <p class="text-center text-[11px] text-white/60 mt-5">
            &copy; {{ date('Y') }} {{ setting('store.brand_name', 'LDC Store') }}
        </p>
    </main>
</body>
</html>
