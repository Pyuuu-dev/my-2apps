{{-- F. Brutalist Bold — solid color block, thick borders, chunky typography --}}
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    @include('auth.templates._head', [
        'themeColor' => '#facc15',
        'extraFonts' => 'https://fonts.googleapis.com/css2?family=Archivo+Black&family=Space+Mono:wght@400;700&display=swap',
    ])
    <style>
        body.tpl-brutalist {
            font-family: 'Space Mono', 'Courier New', monospace;
            background: #facc15;
            background-image:
                linear-gradient(rgba(0,0,0,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,0,0,0.04) 1px, transparent 1px);
            background-size: 24px 24px;
        }
        .tpl-brutalist h1, .tpl-brutalist .display { font-family: 'Archivo Black', 'Inter', sans-serif; letter-spacing: -0.02em; }
        .tpl-brutalist .brut-card {
            background: #ffffff;
            border: 4px solid #000000;
            box-shadow: 8px 8px 0 0 #000000;
        }
        .tpl-brutalist .brut-input {
            background: #fef9c3;
            border: 3px solid #000000;
            box-shadow: 4px 4px 0 0 #000000;
            transition: transform 0.1s ease, box-shadow 0.1s ease;
        }
        .tpl-brutalist .brut-input:focus {
            outline: none;
            transform: translate(-2px, -2px);
            box-shadow: 6px 6px 0 0 #000000;
            background: #ffffff;
        }
        .tpl-brutalist .brut-btn {
            background: #ec4899;
            border: 4px solid #000000;
            box-shadow: 6px 6px 0 0 #000000;
            transition: transform 0.1s ease, box-shadow 0.1s ease;
        }
        @media (hover: hover) and (pointer: fine) {
            .tpl-brutalist .brut-btn:hover {
                transform: translate(-2px, -2px);
                box-shadow: 8px 8px 0 0 #000000;
            }
        }
        .tpl-brutalist .brut-btn:active {
            transform: translate(2px, 2px);
            box-shadow: 2px 2px 0 0 #000000;
        }
        .tpl-brutalist .brut-checkbox {
            appearance: none;
            -webkit-appearance: none;
            width: 1.15rem; height: 1.15rem;
            border: 3px solid #000000;
            background: #ffffff;
            cursor: pointer;
            position: relative;
        }
        .tpl-brutalist .brut-checkbox:checked { background: #ec4899; }
        .tpl-brutalist .brut-checkbox:checked::after {
            content: '✕'; position: absolute; inset: 0;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.85rem; font-weight: 700; color: #ffffff;
        }
        /* Mobile: kurangi shadow offset supaya tidak overflow horizontal */
        @media (max-width: 639px) {
            .tpl-brutalist .brut-card { box-shadow: 5px 5px 0 0 #000000; }
            .tpl-brutalist .brut-input { box-shadow: 3px 3px 0 0 #000000; }
            .tpl-brutalist .brut-input:focus { box-shadow: 4px 4px 0 0 #000000; transform: translate(-1px, -1px); }
            .tpl-brutalist .brut-btn { box-shadow: 4px 4px 0 0 #000000; }
        }
        @media (hover: hover) and (pointer: fine) and (max-width: 639px) {
            .tpl-brutalist .brut-btn:hover { box-shadow: 5px 5px 0 0 #000000; transform: translate(-1px, -1px); }
        }
        @media (prefers-reduced-motion: reduce) {
            .tpl-brutalist .brut-input, .tpl-brutalist .brut-btn { transition: none; }
        }
    </style>
</head>
<body class="tpl-brutalist auth-page antialiased flex items-center justify-center p-5 sm:p-6 text-black">
    <main class="w-full max-w-[340px] sm:max-w-[400px] md:max-w-[420px] py-4">

        {{-- Top brand strip --}}
        <div class="brut-card mb-5 px-4 py-3 sm:px-5 flex items-center gap-3">
            <div class="h-9 w-9 bg-black flex items-center justify-center shrink-0">
                <x-brand-logo size="h-5 w-5" extraClass="text-yellow-300"/>
            </div>
            <div class="flex-1 min-w-0">
                <p class="display text-base leading-none truncate">{{ setting('store.brand_name', 'LDC Store') }}</p>
                <p class="text-[10px] uppercase tracking-widest font-bold mt-0.5">// LOGIN PORTAL</p>
            </div>
            <span class="text-[10px] font-bold uppercase bg-black text-yellow-300 px-2 py-1">v1</span>
        </div>

        <div class="brut-card p-5 sm:p-6 md:p-7">
            <h1 class="display text-2xl sm:text-3xl md:text-4xl mb-1">MASUK.</h1>
            <p class="text-xs uppercase tracking-widest font-bold mb-5">// authorized personnel only</p>

            @include('auth.templates._errors', [
                'errorClass' => 'mb-4 px-3 py-2.5 text-sm font-bold uppercase tracking-wide bg-red-400 border-[3px] border-black',
            ])

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="login-username" class="block text-[11px] font-bold uppercase tracking-widest mb-1.5">[ username ]</label>
                    <input type="text" name="username" id="login-username" autocomplete="username" value="{{ old('username') }}" required autofocus
                        class="brut-input w-full px-3 py-2.5 text-sm font-bold text-black placeholder-black/50"
                        placeholder="admin">
                </div>
                <div>
                    <label for="login-password" class="block text-[11px] font-bold uppercase tracking-widest mb-1.5">[ password ]</label>
                    <div x-data="{ show: false }" class="relative">
                        <input :type="show ? 'text' : 'password'" name="password" id="login-password" autocomplete="current-password" required
                            class="brut-input w-full px-3 py-2.5 text-sm font-bold text-black placeholder-black/50 pr-12"
                            placeholder="••••••••">
                        <button type="button" @click="show = !show"
                            :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'"
                            class="absolute right-2 top-1/2 -translate-y-1/2 h-8 w-8 bg-black text-yellow-300 flex items-center justify-center font-bold text-[10px] uppercase">
                            <span x-text="show ? 'HIDE' : 'SHOW'"></span>
                        </button>
                    </div>
                </div>
                <div class="pt-1">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="remember" class="brut-checkbox">
                        <span class="text-xs font-bold uppercase tracking-wider">Remember me</span>
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

                <button type="submit" class="brut-btn auth-submit-btn w-full px-4 py-3 text-base font-black uppercase tracking-wider text-white mt-2">
                    →&nbsp; Submit
                </button>
            </form>
        </div>

        <p class="text-center text-[10px] font-bold uppercase tracking-widest mt-5">
            &copy; {{ date('Y') }} {{ setting('store.brand_name', 'LDC Store') }} // ALL CAPS, ALL BUSINESS
        </p>
    </main>
</body>
</html>
