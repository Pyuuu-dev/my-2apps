{{-- K. Nature Organic — warm sunset gradient, organic shapes, earthy --}}
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    @include('auth.templates._head', [
        'themeColor' => '#fed7aa',
        'extraFonts' => 'https://fonts.googleapis.com/css2?family=Fraunces:wght@500;700;900&family=Inter:wght@400;500;600&display=swap',
    ])
    <style>
        body.tpl-nature {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #fef3c7 0%, #fed7aa 35%, #fda4af 75%, #c4b5fd 100%);
            color: #44403c;
            position: relative;
            overflow-x: hidden;
        }
        .tpl-nature .blob {
            position: absolute; pointer-events: none; opacity: 0.55;
            filter: blur(40px);
        }
        .tpl-nature .serif { font-family: 'Fraunces', 'Georgia', serif; }
        .tpl-nature .nature-card {
            background: rgba(255, 251, 245, 0.85);
            border-radius: 32px;
            box-shadow: 0 20px 50px -12px rgba(120, 80, 60, 0.2);
        }
        @supports ((backdrop-filter: blur(8px)) or (-webkit-backdrop-filter: blur(8px))) {
            .tpl-nature .nature-card {
                background: rgba(255, 251, 245, 0.7);
                backdrop-filter: blur(8px) saturate(140%);
                -webkit-backdrop-filter: blur(8px) saturate(140%);
            }
        }
        .tpl-nature .nature-input {
            background: rgba(254, 243, 199, 0.4);
            border: 1px solid rgba(194, 65, 12, 0.15);
            color: #44403c;
            transition: all 0.15s ease;
        }
        .tpl-nature .nature-input:focus {
            outline: none;
            background: rgba(254, 243, 199, 0.7);
            border-color: #84cc16;
            box-shadow: 0 0 0 3px rgba(132, 204, 22, 0.15);
        }
        .tpl-nature .nature-input::placeholder { color: rgba(68, 64, 60, 0.5); }
        .tpl-nature .nature-btn {
            background: linear-gradient(135deg, #84cc16 0%, #65a30d 100%);
            color: white;
            box-shadow: 0 8px 20px -4px rgba(132, 204, 22, 0.45);
            transition: all 0.15s ease;
        }
        @media (hover: hover) and (pointer: fine) {
            .tpl-nature .nature-btn:hover {
                box-shadow: 0 12px 28px -4px rgba(132, 204, 22, 0.6);
                transform: translateY(-1px);
            }
        }
        @media (prefers-reduced-motion: reduce) {
            .tpl-nature .nature-btn { transition: none; }
            .tpl-nature .nature-btn:hover { transform: none; }
        }
        @media (max-width: 380px) {
            .tpl-nature > svg.absolute { display: none; }
        }
    </style>
</head>
<body class="tpl-nature auth-page antialiased">
    <div class="blob" style="top: -120px; left: -80px; width: 320px; height: 320px; border-radius: 60% 40% 50% 50% / 50% 60% 40% 50%; background: #fde68a;" aria-hidden="true"></div>
    <div class="blob" style="bottom: -120px; right: -100px; width: 380px; height: 380px; border-radius: 40% 60% 65% 35% / 50% 40% 60% 50%; background: #fda4af;" aria-hidden="true"></div>
    <div class="blob hidden sm:block" style="top: 30%; right: -60px; width: 200px; height: 200px; border-radius: 50% 50% 40% 60% / 60% 50% 50% 40%; background: #bef264; opacity: 0.4;" aria-hidden="true"></div>

    <svg class="absolute top-6 right-6 sm:top-10 sm:right-10 h-12 w-12 sm:h-16 sm:w-16 text-lime-700/30 pointer-events-none" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
        <path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z"/>
    </svg>

    <div class="relative min-h-screen flex items-center justify-center p-4 sm:p-6">
        <main class="w-full max-w-[360px] sm:max-w-[420px] py-6">
            <div class="nature-card p-6 sm:p-8 lg:p-10">
                <div class="flex flex-col items-center mb-6 sm:mb-7">
                    <div class="h-14 w-14 sm:h-16 sm:w-16 rounded-2xl flex items-center justify-center mb-4 shadow-lg"
                         style="background: linear-gradient(135deg, #84cc16 0%, #c2410c 100%);">
                        <x-brand-logo size="h-7 w-7 sm:h-8 sm:w-8" extraClass="text-white"/>
                    </div>
                    <h1 class="serif text-[24px] sm:text-[28px] font-bold text-stone-800 text-center leading-tight">{{ setting('store.brand_name', 'LDC Store') }}</h1>
                    <p class="text-[12px] sm:text-[13px] text-stone-600 mt-1.5 text-center italic serif">— Tumbuh perlahan, bersama —</p>
                </div>

                @include('auth.templates._errors', [
                    'errorClass' => 'mb-4 rounded-2xl px-3.5 py-2.5 text-[13px] text-rose-700 bg-rose-100/60 border border-rose-200/60'
                ])

                <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="login-username" class="block text-[12px] font-semibold text-stone-700 mb-1.5">Username</label>
                        <input type="text" name="username" id="login-username" autocomplete="username" value="{{ old('username') }}" required autofocus
                            class="auth-input-light nature-input w-full rounded-2xl px-4 py-2.5 text-sm"
                            placeholder="admin">
                    </div>
                    <div>
                        <label for="login-password" class="block text-[12px] font-semibold text-stone-700 mb-1.5">Password</label>
                        @include('auth.templates._password_input', [
                            'inputClass' => 'auth-input-light nature-input w-full rounded-2xl px-4 py-2.5 text-sm',
                            'toggleClass' => 'text-stone-400 hover:text-stone-600',
                        ])
                    </div>
                    <div class="pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="rounded border-stone-300 text-lime-600 focus:ring-lime-500">
                            <span class="text-[13px] text-stone-600">Ingat saya</span>
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

                    <button type="submit" class="nature-btn auth-submit-btn w-full rounded-2xl px-4 py-3 text-sm font-bold mt-2">
                        Masuk
                    </button>
                </form>
            </div>

            <p class="text-center text-[11px] text-stone-600 mt-5 italic serif">
                &copy; {{ date('Y') }} — {{ setting('store.brand_name', 'LDC Store') }}
            </p>
        </main>
    </div>
</body>
</html>
