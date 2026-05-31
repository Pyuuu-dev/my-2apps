{{-- S. Hand-drawn Sketch — wobbly border, marker font, casual --}}
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    @include('auth.templates._head', [
        'themeColor' => '#fffdf7',
        'extraFonts' => 'https://fonts.googleapis.com/css2?family=Architects+Daughter&family=Patrick+Hand&family=Inter:wght@400;500;600&display=swap',
    ])
    <style>
        body.tpl-sketch {
            font-family: 'Patrick Hand', 'Inter', sans-serif;
            background: #fffdf7;
            color: #1f2937;
            background-image:
                radial-gradient(rgba(31, 41, 55, 0.04) 1px, transparent 1px);
            background-size: 22px 22px;
        }
        .tpl-sketch .marker { font-family: 'Architects Daughter', 'Patrick Hand', cursive; }
        .tpl-sketch .sketch-card {
            background: #ffffff;
            border: 2.5px solid #1f2937;
            border-radius: 22px 30px 24px 32px / 28px 22px 32px 26px;
            box-shadow: 4px 4px 0 0 #f59e0b, 8px 8px 0 0 #1f2937;
            position: relative;
        }
        .tpl-sketch .sketch-card::before {
            content: ''; position: absolute; inset: -3px;
            border: 1px dashed rgba(31, 41, 55, 0.25);
            border-radius: inherit;
            pointer-events: none;
        }
        .tpl-sketch .sketch-input {
            background: #fefce8;
            border: 2px solid #1f2937;
            border-radius: 14px 18px 12px 16px / 14px 12px 18px 14px;
            color: #1f2937;
            transition: all 0.15s ease;
        }
        .tpl-sketch .sketch-input:focus {
            outline: none;
            background: #fffbeb;
            border-color: #f59e0b;
            box-shadow: 3px 3px 0 0 #f59e0b;
            transform: rotate(-0.3deg);
        }
        .tpl-sketch .sketch-input::placeholder { color: rgba(31, 41, 55, 0.35); font-style: italic; }
        .tpl-sketch .sketch-btn {
            background: #f59e0b;
            color: #1f2937;
            border: 2.5px solid #1f2937;
            border-radius: 18px 24px 16px 22px / 16px 22px 24px 18px;
            box-shadow: 4px 4px 0 0 #1f2937;
            transition: all 0.15s ease;
        }
        @media (hover: hover) and (pointer: fine) {
            .tpl-sketch .sketch-btn:hover {
                transform: translate(-1px, -1px) rotate(-0.5deg);
                box-shadow: 5px 5px 0 0 #1f2937;
            }
        }
        .tpl-sketch .sketch-btn:active {
            transform: translate(2px, 2px);
            box-shadow: 1px 1px 0 0 #1f2937;
        }
        .tpl-sketch .squiggle {
            color: #f59e0b; font-weight: 700;
        }
        .tpl-sketch .sketch-checkbox {
            appearance: none; -webkit-appearance: none;
            width: 1.2rem; height: 1.2rem;
            border: 2px solid #1f2937;
            border-radius: 6px 4px 5px 7px;
            background: #fffdf7;
            position: relative; cursor: pointer;
        }
        .tpl-sketch .sketch-checkbox:checked { background: #f59e0b; }
        .tpl-sketch .sketch-checkbox:checked::after {
            content: '✓'; position: absolute; inset: -2px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.95rem; font-weight: 700; color: #1f2937;
            font-family: 'Architects Daughter', sans-serif;
        }
        @media (prefers-reduced-motion: reduce) {
            .tpl-sketch .sketch-input, .tpl-sketch .sketch-btn { transition: none; }
            .tpl-sketch .sketch-input:focus { transform: none; }
            .tpl-sketch .sketch-btn:hover { transform: none; }
        }
    </style>
</head>
<body class="tpl-sketch auth-page antialiased flex items-center justify-center p-4 sm:p-6">
    <main class="w-full max-w-[360px] sm:max-w-[420px] py-6">
        <div class="sketch-card p-6 sm:p-8">
            <div class="flex flex-col items-center mb-5">
                <div class="h-14 w-14 sm:h-16 sm:w-16 flex items-center justify-center mb-3 border-[2.5px] border-stone-800"
                     style="background: #f59e0b; border-radius: 16px 22px 18px 24px / 22px 18px 24px 16px; box-shadow: 3px 3px 0 0 #1f2937;">
                    <x-brand-logo size="h-7 w-7 sm:h-8 sm:w-8" extraClass="text-stone-800"/>
                </div>
                <h1 class="marker text-[26px] sm:text-[30px] text-stone-900 leading-none">{{ setting('store.brand_name', 'LDC Store') }}</h1>
                <p class="text-[15px] sm:text-base text-stone-700 mt-1.5"><span class="squiggle">~</span> hai! login dulu yuk <span class="squiggle">~</span></p>
            </div>

            @include('auth.templates._errors', [
                'errorClass' => 'mb-4 px-3.5 py-2.5 text-[14px] text-rose-700 bg-rose-100 border-2 border-rose-700'
            ])

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="login-username" class="marker block text-base text-stone-800 mb-1.5">→ username:</label>
                    <input type="text" name="username" id="login-username" autocomplete="username" value="{{ old('username') }}" required autofocus
                        class="auth-input-light sketch-input w-full px-3.5 py-2.5 text-[15px]"
                        placeholder="tulis nama disini ✏️">
                </div>
                <div>
                    <label for="login-password" class="marker block text-base text-stone-800 mb-1.5">→ password:</label>
                    @include('auth.templates._password_input', [
                        'inputClass' => 'auth-input-light sketch-input w-full px-3.5 py-2.5 text-[15px]',
                        'toggleClass' => 'text-stone-500 hover:text-amber-700',
                    ])
                </div>
                <div class="pt-1">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="remember" class="sketch-checkbox">
                        <span class="text-[15px] text-stone-700">— ingat ya, jangan lupa</span>
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

                <button type="submit" class="sketch-btn auth-submit-btn marker w-full px-4 py-3 text-lg mt-3">
                    ✦ Masuk! ✦
                </button>
            </form>
        </div>

        <p class="text-center text-base text-stone-700 mt-5 marker">
            © {{ date('Y') }} — {{ setting('store.brand_name', 'LDC Store') }}
        </p>
    </main>
</body>
</html>
