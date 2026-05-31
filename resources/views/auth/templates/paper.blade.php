{{-- O. Paper / Stationery — vintage notebook texture, sketchy borders --}}
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    @include('auth.templates._head', [
        'themeColor' => '#fefae0',
        'extraFonts' => 'https://fonts.googleapis.com/css2?family=Caveat:wght@500;700&family=Lora:ital,wght@0,400;0,600;1,400&display=swap',
    ])
    <style>
        body.tpl-paper {
            font-family: 'Lora', Georgia, serif;
            background: #fefae0;
            color: #283618;
            background-image:
                /* Subtle horizontal rules like notebook lines */
                repeating-linear-gradient(0deg, transparent 0, transparent 27px, rgba(40, 54, 24, 0.08) 27px, rgba(40, 54, 24, 0.08) 28px),
                /* Tiny noise paper grain */
                radial-gradient(rgba(40, 54, 24, 0.04) 1px, transparent 1px);
            background-size: 100% 28px, 4px 4px;
        }
        .tpl-paper .handwriting { font-family: 'Caveat', cursive; }
        .tpl-paper .paper-card {
            background: #fffdf6;
            border: 1px solid rgba(40, 54, 24, 0.18);
            box-shadow:
                0 1px 3px rgba(40, 54, 24, 0.08),
                0 12px 32px -12px rgba(40, 54, 24, 0.18);
            position: relative;
        }
        /* Tape strip top */
        .tpl-paper .tape {
            position: absolute; top: -10px; left: 50%;
            transform: translateX(-50%) rotate(-2deg);
            width: 80px; height: 22px;
            background: rgba(188, 108, 37, 0.4);
            border: 1px dashed rgba(188, 108, 37, 0.5);
        }
        .tpl-paper .paper-input {
            background: transparent; border: none;
            border-bottom: 1.5px dashed rgba(40, 54, 24, 0.4);
            color: #283618;
            border-radius: 0;
            transition: border-color 0.15s ease;
        }
        .tpl-paper .paper-input:focus {
            outline: none;
            border-bottom-color: #bc6c25;
            border-bottom-style: solid;
        }
        .tpl-paper .paper-input::placeholder { color: rgba(40, 54, 24, 0.3); font-style: italic; }
        .tpl-paper .paper-btn {
            background: #283618; color: #fefae0;
            border: 2px solid #283618;
            transition: all 0.15s ease;
            position: relative;
        }
        .tpl-paper .paper-btn::before {
            content: ''; position: absolute; inset: 0;
            transform: translate(3px, 3px); z-index: -1;
            background: rgba(188, 108, 37, 0.4);
            border: 2px dashed rgba(188, 108, 37, 0.6);
        }
        @media (hover: hover) and (pointer: fine) {
            .tpl-paper .paper-btn:hover {
                background: #bc6c25; border-color: #bc6c25;
            }
        }
        .tpl-paper .stamp {
            display: inline-block;
            border: 2px solid #bc6c25;
            color: #bc6c25;
            padding: 2px 8px;
            font-family: 'Lora', serif;
            font-weight: 700;
            font-size: 10px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            transform: rotate(-3deg);
            opacity: 0.8;
        }
        @media (prefers-reduced-motion: reduce) {
            .tpl-paper .paper-btn { transition: none; }
        }
    </style>
</head>
<body class="tpl-paper auth-page antialiased flex items-center justify-center p-4 sm:p-6">
    <main class="w-full max-w-[360px] sm:max-w-[420px] pt-10 pb-8 relative">
        <div class="paper-card rounded-sm p-6 sm:p-8 lg:p-10 mt-2">
            <div class="tape" aria-hidden="true"></div>

            <div class="text-center mb-6">
                <span class="stamp">Confidential — {{ date('Y') }}</span>
            </div>

            <div class="flex flex-col items-center mb-6">
                <div class="h-12 w-12 sm:h-14 sm:w-14 border-2 border-stone-800 flex items-center justify-center mb-3" style="background: #bc6c25;">
                    <x-brand-logo size="h-6 w-6 sm:h-7 sm:w-7" extraClass="text-amber-50"/>
                </div>
                <h1 class="handwriting text-3xl sm:text-4xl text-stone-800 leading-none">{{ setting('store.brand_name', 'LDC Store') }}</h1>
                <p class="text-[12px] sm:text-[13px] text-stone-700 italic mt-2">— Catatan kecil untuk masuk —</p>
            </div>

            @include('auth.templates._errors', [
                'errorClass' => 'mb-4 px-3.5 py-2.5 text-[13px] text-rose-800 bg-rose-100/60 border-l-4 border-rose-700 italic'
            ])

            <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="login-username" class="handwriting block text-lg text-stone-800 mb-1">Nama:</label>
                    <input type="text" name="username" id="login-username" autocomplete="username" value="{{ old('username') }}" required autofocus
                        class="auth-input-light paper-input handwriting w-full px-1 py-1 text-xl"
                        placeholder="tulis di sini...">
                </div>
                <div>
                    <label for="login-password" class="handwriting block text-lg text-stone-800 mb-1">Sandi:</label>
                    <div x-data="{ show: false }" class="relative">
                        <input :type="show ? 'text' : 'password'" name="password" id="login-password" autocomplete="current-password" required
                            class="auth-input-light paper-input handwriting w-full px-1 py-1 text-xl pr-14"
                            placeholder="rahasia">
                        <button type="button" @click="show = !show"
                            :aria-label="show ? 'Sembunyikan sandi' : 'Tampilkan sandi'"
                            class="handwriting absolute right-0 bottom-1 text-sm text-stone-700 hover:text-amber-700">
                            <span x-text="show ? '(tutup)' : '(lihat)'"></span>
                        </button>
                    </div>
                </div>
                <div class="pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-stone-700 text-amber-700 focus:ring-amber-700">
                        <span class="handwriting text-base text-stone-800">— ingat aku ya</span>
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

                <button type="submit" class="paper-btn auth-submit-btn handwriting w-full px-4 py-3 text-xl tracking-wide mt-3">
                    Masuk &mdash;&gt;
                </button>
            </form>
        </div>

        <p class="handwriting text-center text-base text-stone-700 mt-6 italic">
            &copy; {{ date('Y') }} — {{ setting('store.brand_name', 'LDC Store') }}
        </p>
    </main>
</body>
</html>
