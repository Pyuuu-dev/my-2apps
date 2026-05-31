{{-- M. Anime Manga — screentone bg, comic borders, speed lines --}}
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    @include('auth.templates._head', [
        'themeColor' => '#ffffff',
        'extraFonts' => 'https://fonts.googleapis.com/css2?family=Bangers&family=Permanent+Marker&family=M+PLUS+Rounded+1c:wght@400;500;700;800&display=swap',
    ])
    <style>
        body.tpl-manga {
            font-family: 'M PLUS Rounded 1c', 'Inter', sans-serif;
            background: #ffffff;
            background-image:
                radial-gradient(#000 1px, transparent 1.5px),
                radial-gradient(#000 1px, transparent 1.5px);
            background-size: 18px 18px;
            background-position: 0 0, 9px 9px;
            background-color: #fff;
            color: #0a0a0a;
        }
        .tpl-manga .display { font-family: 'Bangers', 'Impact', sans-serif; letter-spacing: 0.04em; }
        .tpl-manga .marker { font-family: 'Permanent Marker', 'Impact', sans-serif; }
        .tpl-manga .panel {
            background: #ffffff;
            border: 4px solid #0a0a0a;
            box-shadow: 8px 8px 0 0 #0a0a0a;
            position: relative;
        }
        .tpl-manga .speedlines {
            position: absolute; inset: 0; pointer-events: none;
            background: repeating-linear-gradient(
                45deg,
                rgba(255, 64, 129, 0) 0,
                rgba(255, 64, 129, 0) 8px,
                rgba(255, 64, 129, 0.18) 8px,
                rgba(255, 64, 129, 0.18) 9px
            );
            mask-image: linear-gradient(135deg, black 0%, transparent 35%);
            -webkit-mask-image: linear-gradient(135deg, black 0%, transparent 35%);
        }
        .tpl-manga .balloon {
            position: relative;
            background: #fff8;
            border: 3px solid #0a0a0a;
            border-radius: 18px;
        }
        .tpl-manga .balloon::after {
            content: ''; position: absolute;
            bottom: -16px; left: 28px;
            width: 0; height: 0;
            border-left: 12px solid transparent;
            border-right: 12px solid transparent;
            border-top: 16px solid #0a0a0a;
        }
        .tpl-manga .balloon::before {
            content: ''; position: absolute;
            bottom: -10px; left: 32px;
            width: 0; height: 0;
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-top: 12px solid #fff;
            z-index: 1;
        }
        .tpl-manga .manga-input {
            background: #fff; border: 3px solid #0a0a0a; border-radius: 0;
            transition: transform 0.1s ease;
        }
        .tpl-manga .manga-input:focus {
            outline: none;
            background: #fef3c7;
            transform: translate(-1px, -1px);
            box-shadow: 3px 3px 0 0 #ff4081;
        }
        .tpl-manga .manga-btn {
            background: #ff4081; color: white;
            border: 4px solid #0a0a0a;
            box-shadow: 6px 6px 0 0 #0a0a0a;
            transition: all 0.1s ease;
        }
        @media (hover: hover) and (pointer: fine) {
            .tpl-manga .manga-btn:hover {
                transform: translate(-2px, -2px);
                box-shadow: 8px 8px 0 0 #0a0a0a;
            }
        }
        .tpl-manga .manga-btn:active {
            transform: translate(2px, 2px);
            box-shadow: 2px 2px 0 0 #0a0a0a;
        }
        .tpl-manga .manga-checkbox {
            appearance: none; -webkit-appearance: none;
            width: 1.15rem; height: 1.15rem;
            border: 3px solid #0a0a0a; background: #fff;
            position: relative; cursor: pointer;
        }
        .tpl-manga .manga-checkbox:checked { background: #ff4081; }
        .tpl-manga .manga-checkbox:checked::after {
            content: '★'; position: absolute; inset: -3px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.9rem; color: #fff;
        }
        @media (max-width: 639px) {
            .tpl-manga .panel { box-shadow: 5px 5px 0 0 #0a0a0a; }
            .tpl-manga .manga-btn { box-shadow: 4px 4px 0 0 #0a0a0a; }
        }
        @media (hover: hover) and (pointer: fine) and (max-width: 639px) {
            .tpl-manga .manga-btn:hover { box-shadow: 5px 5px 0 0 #0a0a0a; transform: translate(-1px, -1px); }
        }
        @media (prefers-reduced-motion: reduce) {
            .tpl-manga .manga-input, .tpl-manga .manga-btn { transition: none; }
        }
    </style>
</head>
<body class="tpl-manga auth-page antialiased flex items-center justify-center p-4 sm:p-6">
    <main class="w-full max-w-[360px] sm:max-w-[440px] py-6">
        <div class="panel p-5 sm:p-7">
            <div class="speedlines" aria-hidden="true"></div>

            <div class="relative z-10">
                <div class="flex items-start gap-3 mb-3">
                    <div class="h-12 w-12 sm:h-14 sm:w-14 bg-pink-500 border-[3px] border-black flex items-center justify-center shrink-0" style="box-shadow: 4px 4px 0 0 #0a0a0a;">
                        <x-brand-logo size="h-6 w-6 sm:h-7 sm:w-7" extraClass="text-white"/>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="display text-2xl sm:text-3xl text-pink-600 truncate">{{ setting('store.brand_name', 'LDC Store') }}!</p>
                        <p class="text-[10px] sm:text-[11px] uppercase tracking-widest font-bold mt-0.5">— Chapter 01: Login Arc</p>
                    </div>
                </div>

                <div class="balloon mt-5 mb-5 px-4 py-2.5">
                    <p class="marker text-[15px] sm:text-base text-black leading-tight">Yo! Login dulu yuk, biar bisa lanjut adventure! ⚡</p>
                </div>

                @include('auth.templates._errors', [
                    'errorClass' => 'mb-4 px-3 py-2 text-[13px] font-bold bg-red-300 border-[3px] border-black text-black'
                ])

                <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="login-username" class="display block text-base mb-1 text-pink-600">Username:</label>
                        <input type="text" name="username" id="login-username" autocomplete="username" value="{{ old('username') }}" required autofocus
                            class="manga-input w-full px-3 py-2.5 text-sm font-bold text-black"
                            placeholder="admin">
                    </div>
                    <div>
                        <label for="login-password" class="display block text-base mb-1 text-pink-600">Password:</label>
                        <div x-data="{ show: false }" class="relative">
                            <input :type="show ? 'text' : 'password'" name="password" id="login-password" autocomplete="current-password" required
                                class="manga-input w-full px-3 py-2.5 text-sm font-bold text-black pr-14"
                                placeholder="••••••••">
                            <button type="button" @click="show = !show"
                                :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'"
                                class="absolute right-1.5 top-1/2 -translate-y-1/2 h-8 px-2 bg-black text-pink-300 border-[2px] border-black font-bold text-[10px] uppercase">
                                <span x-text="show ? 'HIDE' : 'SHOW'"></span>
                            </button>
                        </div>
                    </div>
                    <div class="pt-1">
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" name="remember" class="manga-checkbox">
                            <span class="text-[13px] font-bold">Ingat aku!</span>
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

                    <button type="submit" class="manga-btn auth-submit-btn display w-full px-4 py-3 text-lg uppercase tracking-wider mt-2">
                        ⚡ MASUK! ⚡
                    </button>
                </form>
            </div>
        </div>

        <p class="text-center text-[11px] font-bold mt-5">
            &copy; {{ date('Y') }} {{ setting('store.brand_name', 'LDC Store') }} — To be continued...
        </p>
    </main>
</body>
</html>
