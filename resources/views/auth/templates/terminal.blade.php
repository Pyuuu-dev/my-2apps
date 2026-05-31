{{-- I. Vintage Terminal — CRT green-on-black, monospace, blinking cursor --}}
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    @include('auth.templates._head', [
        'themeColor' => '#000000',
        'extraFonts' => 'https://fonts.googleapis.com/css2?family=VT323&family=IBM+Plex+Mono:wght@400;500;700&display=swap',
    ])
    <style>
        body.tpl-term {
            font-family: 'IBM Plex Mono', 'Courier New', monospace;
            background: #000000;
            color: #33ff33;
            text-shadow: 0 0 4px rgba(51, 255, 51, 0.5);
        }
        .tpl-term .crt-screen {
            background: radial-gradient(ellipse at center, #0a1a0a 0%, #000000 100%);
            border: 1px solid #33ff33;
            box-shadow: 0 0 24px rgba(51, 255, 51, 0.3), inset 0 0 60px rgba(51, 255, 51, 0.05);
            position: relative;
        }
        .tpl-term .crt-screen::before {
            content: ''; position: absolute; inset: 0; pointer-events: none;
            background: repeating-linear-gradient(
                0deg,
                rgba(0, 0, 0, 0) 0,
                rgba(0, 0, 0, 0) 2px,
                rgba(51, 255, 51, 0.04) 3px,
                rgba(51, 255, 51, 0.04) 4px
            );
        }
        .tpl-term .crt-screen::after {
            content: ''; position: absolute; inset: 0; pointer-events: none;
            background: radial-gradient(ellipse at center, transparent 30%, rgba(0, 0, 0, 0.4) 100%);
        }
        .tpl-term .vt323 { font-family: 'VT323', 'IBM Plex Mono', monospace; }
        .tpl-term .term-input {
            background: transparent; border: none;
            border-bottom: 1px solid rgba(51, 255, 51, 0.4);
            color: #33ff33;
            text-shadow: 0 0 4px rgba(51, 255, 51, 0.5);
            caret-color: #33ff33;
        }
        .tpl-term .term-input:focus {
            outline: none;
            border-bottom-color: #33ff33;
            box-shadow: 0 1px 0 0 #33ff33;
        }
        .tpl-term .term-input::placeholder { color: rgba(51, 255, 51, 0.35); }
        .tpl-term .term-btn {
            background: rgba(51, 255, 51, 0.1);
            border: 1px solid #33ff33;
            color: #33ff33;
            text-shadow: 0 0 6px rgba(51, 255, 51, 0.7);
            transition: background 0.1s ease;
        }
        .tpl-term .term-btn:hover { background: rgba(51, 255, 51, 0.2); }
        .tpl-term .term-btn:active { background: rgba(51, 255, 51, 0.3); }
        .tpl-term .blink { display: inline-block; animation: term-blink 1.06s steps(2, start) infinite; }
        @keyframes term-blink { to { visibility: hidden; } }
        .tpl-term .term-checkbox {
            appearance: none; -webkit-appearance: none;
            width: 1rem; height: 1rem;
            border: 1px solid #33ff33; background: transparent;
            position: relative; cursor: pointer;
        }
        .tpl-term .term-checkbox:checked::after {
            content: '✕'; position: absolute; inset: -2px;
            display: flex; align-items: center; justify-content: center;
            color: #33ff33; font-size: 0.85rem; font-weight: 700;
            text-shadow: 0 0 6px rgba(51, 255, 51, 0.7);
        }
        @media (prefers-reduced-motion: reduce) {
            .tpl-term .blink { animation: none; opacity: 0.7; }
            .tpl-term .crt-screen::before { display: none; }
        }
    </style>
</head>
<body class="tpl-term auth-page antialiased flex items-center justify-center p-3 sm:p-6">
    <main class="w-full max-w-[360px] sm:max-w-lg py-4">
        <div class="crt-screen rounded-md p-5 sm:p-7 relative">
            <div class="relative z-10">
                <p class="text-[11px] sm:text-xs tracking-wider mb-1">
                    <span class="opacity-70">~/{{ strtolower(str_replace(' ', '-', setting('store.brand_name', 'ldc'))) }}/auth</span>
                    <span class="opacity-50">$</span>
                </p>
                <p class="vt323 text-2xl sm:text-3xl leading-none mb-1">SYSTEM LOGIN <span class="opacity-70">v1.0</span></p>
                <p class="text-[11px] opacity-70 mb-4">[ SECURE TERMINAL // {{ now()->format('Y-m-d H:i') }} ]</p>

                <pre class="text-[10px] leading-tight opacity-60 mb-4 hidden sm:block">+----------------------------------------+
| AUTHENTICATION REQUIRED                |
| ENTER CREDENTIALS TO PROCEED           |
+----------------------------------------+</pre>

                @include('auth.templates._errors', [
                    'errorClass' => 'mb-4 text-[12px] px-2.5 py-2 border border-red-500/60 bg-red-500/10 text-red-300'
                ])

                <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="login-username" class="block text-[12px] mb-1">
                            <span class="opacity-80">&gt; user_id</span>
                            <span class="blink" aria-hidden="true">_</span>
                        </label>
                        <input type="text" name="username" id="login-username" autocomplete="username" value="{{ old('username') }}" required autofocus
                            class="auth-input-dark term-input w-full px-1 py-1.5 text-sm"
                            placeholder="admin">
                    </div>
                    <div>
                        <label for="login-password" class="block text-[12px] mb-1">
                            <span class="opacity-80">&gt; passkey</span>
                        </label>
                        <div x-data="{ show: false }" class="relative flex items-center gap-2">
                            <input :type="show ? 'text' : 'password'" name="password" id="login-password" autocomplete="current-password" required
                                class="auth-input-dark term-input flex-1 px-1 py-1.5 text-sm"
                                placeholder="••••••••">
                            <button type="button" @click="show = !show"
                                :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'"
                                class="text-[10px] uppercase opacity-70 hover:opacity-100 px-2 py-1 border border-current shrink-0">
                                <span x-text="show ? '[hide]' : '[show]'"></span>
                            </button>
                        </div>
                    </div>
                    <div class="pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="term-checkbox">
                            <span class="text-[12px] opacity-80">--keep-session</span>
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

                    <button type="submit" class="term-btn auth-submit-btn w-full px-4 py-3 text-sm font-bold uppercase tracking-widest mt-2">
                        [ EXECUTE LOGIN ]
                    </button>
                </form>

                <p class="text-[10px] opacity-60 mt-5">
                    &gt; {{ setting('store.brand_name', 'LDC') }} :: copyleft {{ date('Y') }} :: all rights reversed
                </p>
            </div>
        </div>
    </main>
</body>
</html>
