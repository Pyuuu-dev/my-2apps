{{-- R. Retro Arcade 80s — synthwave grid horizon, neon sun, retro --}}
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    @include('auth.templates._head', [
        'themeColor' => '#2d1b4e',
        'extraFonts' => 'https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Audiowide&family=Inter:wght@400;500;600;700&display=swap',
    ])
    <style>
        body.tpl-arcade {
            font-family: 'Inter', sans-serif;
            background: #1a0b2e;
            color: #ffffff;
            position: relative;
            overflow-x: hidden;
        }
        .tpl-arcade .arcade-bg {
            position: fixed; inset: 0; z-index: -1; overflow: hidden;
            background:
                linear-gradient(180deg, #1a0b2e 0%, #2d1b4e 50%, #391952 75%, #2d1b4e 100%);
        }
        /* Sun */
        .tpl-arcade .arcade-sun {
            position: absolute; left: 50%; bottom: 35%;
            transform: translateX(-50%);
            width: 220px; height: 220px;
            border-radius: 50%;
            background: linear-gradient(180deg, #fbbf24 0%, #ff006e 60%, #d6005a 100%);
            box-shadow: 0 0 80px rgba(251, 191, 36, 0.5), 0 0 200px rgba(255, 0, 110, 0.4);
        }
        .tpl-arcade .arcade-sun::after {
            content: ''; position: absolute; inset: 0;
            background: repeating-linear-gradient(
                0deg,
                rgba(45, 27, 78, 0) 0px,
                rgba(45, 27, 78, 0) 14px,
                #1a0b2e 15px,
                #1a0b2e 19px
            );
            mask-image: linear-gradient(180deg, transparent 50%, black 50%);
            -webkit-mask-image: linear-gradient(180deg, transparent 50%, black 50%);
        }
        /* Grid horizon */
        .tpl-arcade .arcade-grid {
            position: absolute; left: -10%; right: -10%; bottom: 0; height: 45%;
            background:
                linear-gradient(rgba(255, 0, 110, 0.6), rgba(255, 0, 110, 0.6)) bottom/100% 1px no-repeat,
                repeating-linear-gradient(0deg, rgba(255, 0, 110, 0.5) 0 1px, transparent 1px 32px),
                repeating-linear-gradient(90deg, rgba(0, 255, 255, 0.4) 0 1px, transparent 1px 56px);
            transform: perspective(400px) rotateX(60deg);
            transform-origin: bottom;
            mask-image: linear-gradient(180deg, transparent 0%, black 30%);
            -webkit-mask-image: linear-gradient(180deg, transparent 0%, black 30%);
        }
        .tpl-arcade .display { font-family: 'Press Start 2P', cursive; letter-spacing: 0; }
        .tpl-arcade .audiowide { font-family: 'Audiowide', cursive; letter-spacing: 0.04em; }
        .tpl-arcade .arcade-card {
            background: rgba(26, 11, 46, 0.85);
            border: 2px solid #ff006e;
            box-shadow:
                0 0 30px rgba(255, 0, 110, 0.5),
                0 0 80px rgba(0, 255, 255, 0.15),
                inset 0 0 30px rgba(255, 0, 110, 0.1);
        }
        .tpl-arcade .arcade-input {
            background: rgba(0, 255, 255, 0.05);
            border: 1px solid rgba(0, 255, 255, 0.3);
            color: #fbbf24;
            transition: all 0.15s ease;
        }
        .tpl-arcade .arcade-input:focus {
            outline: none;
            border-color: #fbbf24;
            background: rgba(251, 191, 36, 0.05);
            box-shadow: 0 0 12px rgba(251, 191, 36, 0.4);
        }
        .tpl-arcade .arcade-input::placeholder { color: rgba(0, 255, 255, 0.4); }
        .tpl-arcade .arcade-btn {
            background: linear-gradient(180deg, #ff006e 0%, #c10058 100%);
            color: #fbbf24;
            border: 2px solid #fbbf24;
            text-shadow: 0 0 8px rgba(251, 191, 36, 0.6);
            box-shadow:
                0 0 18px rgba(255, 0, 110, 0.6),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
            transition: all 0.15s ease;
        }
        @media (hover: hover) and (pointer: fine) {
            .tpl-arcade .arcade-btn:hover {
                transform: translateY(-2px);
                box-shadow:
                    0 0 28px rgba(255, 0, 110, 0.8),
                    inset 0 1px 0 rgba(255, 255, 255, 0.3);
            }
        }
        @media (prefers-reduced-motion: reduce) {
            .tpl-arcade .arcade-btn { transition: none; }
            .tpl-arcade .arcade-btn:hover { transform: none; }
        }
        @media (max-width: 639px) {
            .tpl-arcade .arcade-sun { width: 160px; height: 160px; bottom: 50%; }
        }
        @media (max-width: 380px) {
            .tpl-arcade .arcade-sun { width: 130px; height: 130px; bottom: 55%; }
        }
        @media (max-height: 670px) and (orientation: portrait) {
            .tpl-arcade .arcade-sun { display: none; }
            .tpl-arcade .arcade-grid { opacity: 0.5; }
        }
        .tpl-arcade .arcade-sun,
        .tpl-arcade .arcade-grid {
            will-change: transform;
        }
    </style>
</head>
<body class="tpl-arcade auth-page antialiased flex items-center justify-center p-4 sm:p-6">
    <div class="arcade-bg" aria-hidden="true">
        <div class="arcade-sun"></div>
        <div class="arcade-grid"></div>
    </div>

    <main class="w-full max-w-[340px] sm:max-w-md py-6 relative">
        <div class="arcade-card rounded-md p-5 sm:p-7">
            <div class="text-center mb-4">
                <p class="display text-[8px] sm:text-[10px] text-cyan-300 uppercase mb-3" style="text-shadow: 0 0 8px rgba(0, 255, 255, 0.7);">// INSERT COIN</p>
                <h1 class="audiowide text-2xl sm:text-3xl text-pink-400" style="text-shadow: 0 0 10px rgba(255, 0, 110, 0.7);">{{ setting('store.brand_name', 'LDC STORE') }}</h1>
                <p class="display text-[8px] sm:text-[10px] text-amber-300 uppercase mt-3" style="text-shadow: 0 0 6px rgba(251, 191, 36, 0.5);">— Player 1 ready —</p>
            </div>

            @include('auth.templates._errors', [
                'errorClass' => 'mb-4 px-3 py-2 text-[12px] text-pink-200 bg-pink-500/15 border border-pink-500/40 uppercase tracking-wider'
            ])

            <form method="POST" action="{{ route('login.post') }}" class="space-y-3.5">
                @csrf
                <div>
                    <label for="login-username" class="display block text-[8px] uppercase text-cyan-300 mb-1.5" style="text-shadow: 0 0 4px rgba(0, 255, 255, 0.6);">[ Player Name ]</label>
                    <input type="text" name="username" id="login-username" autocomplete="username" value="{{ old('username') }}" required autofocus
                        class="auth-input-dark arcade-input audiowide w-full rounded-sm px-3 py-2.5 text-sm"
                        placeholder="ENTER NAME">
                </div>
                <div>
                    <label for="login-password" class="display block text-[8px] uppercase text-cyan-300 mb-1.5" style="text-shadow: 0 0 4px rgba(0, 255, 255, 0.6);">[ Pass Code ]</label>
                    @include('auth.templates._password_input', [
                        'inputClass' => 'auth-input-dark arcade-input audiowide w-full rounded-sm px-3 py-2.5 text-sm',
                        'toggleClass' => 'text-cyan-300 hover:text-amber-300',
                    ])
                </div>
                <div class="pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded-none border-cyan-400/40 bg-cyan-500/10 text-pink-500 focus:ring-pink-500">
                        <span class="display text-[8px] text-cyan-200 uppercase">[ continue session ]</span>
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

                <button type="submit" class="arcade-btn auth-submit-btn audiowide w-full rounded-sm px-4 py-3 text-base uppercase tracking-widest mt-3">
                    ▶ Start Game
                </button>
            </form>

            <p class="display text-center text-[7px] text-cyan-400/60 uppercase mt-5 tracking-widest">
                © {{ date('Y') }} :: GAME OVER ? RETRY
            </p>
        </div>
    </main>
</body>
</html>
