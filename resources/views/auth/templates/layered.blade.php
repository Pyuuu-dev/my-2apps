{{-- L. 3D Layered — stacked cards dengan transforms, hover lift --}}
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    @include('auth.templates._head', ['themeColor' => '#fafafa'])
    <style>
        body.tpl-layered {
            font-family: 'Inter', sans-serif;
            background: #fafafa;
            background-image:
                radial-gradient(circle at 0% 0%, rgba(99, 102, 241, 0.08) 0%, transparent 35%),
                radial-gradient(circle at 100% 100%, rgba(236, 72, 153, 0.08) 0%, transparent 35%);
        }
        .tpl-layered .stack-wrap { position: relative; perspective: 1200px; }
        .tpl-layered .layer {
            position: absolute; inset: 0; border-radius: 24px;
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.4s ease;
            pointer-events: none;
        }
        .tpl-layered .layer-back-2 {
            transform: rotate(-6deg) translate(-12px, 16px);
            background: linear-gradient(135deg, #ec4899, #f472b6);
            opacity: 0.4; filter: blur(0.5px);
            box-shadow: 0 12px 30px -8px rgba(236, 72, 153, 0.4);
        }
        .tpl-layered .layer-back-1 {
            transform: rotate(3deg) translate(8px, 8px);
            background: linear-gradient(135deg, #6366f1, #818cf8);
            opacity: 0.5;
            box-shadow: 0 16px 36px -8px rgba(99, 102, 241, 0.4);
        }
        @media (hover: hover) and (pointer: fine) {
            .tpl-layered .stack-wrap:hover .layer-back-2 { transform: rotate(-8deg) translate(-16px, 22px); }
            .tpl-layered .stack-wrap:hover .layer-back-1 { transform: rotate(5deg) translate(12px, 12px); }
        }
        .tpl-layered .main-card {
            position: relative; background: white; border-radius: 24px;
            box-shadow:
                0 1px 2px rgba(0,0,0,0.04),
                0 8px 24px -4px rgba(99, 102, 241, 0.15),
                0 24px 48px -12px rgba(236, 72, 153, 0.18);
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.4s ease;
        }
        @media (hover: hover) and (pointer: fine) {
            .tpl-layered .stack-wrap:hover .main-card {
                transform: translateY(-4px);
                box-shadow:
                    0 1px 2px rgba(0,0,0,0.04),
                    0 16px 36px -4px rgba(99, 102, 241, 0.25),
                    0 36px 60px -12px rgba(236, 72, 153, 0.25);
            }
        }
        .tpl-layered .grad-text {
            background: linear-gradient(135deg, #6366f1, #ec4899);
            -webkit-background-clip: text; background-clip: text; color: transparent;
        }
        .tpl-layered .grad-btn {
            background: linear-gradient(135deg, #6366f1 0%, #ec4899 100%);
            color: white;
            box-shadow: 0 8px 20px -4px rgba(99, 102, 241, 0.5);
            transition: all 0.2s ease;
        }
        @media (hover: hover) and (pointer: fine) {
            .tpl-layered .grad-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 12px 28px -4px rgba(236, 72, 153, 0.55);
            }
        }
        @media (prefers-reduced-motion: reduce) {
            .tpl-layered .layer, .tpl-layered .main-card, .tpl-layered .grad-btn { transition: none; }
            .tpl-layered .stack-wrap:hover .layer-back-1,
            .tpl-layered .stack-wrap:hover .layer-back-2,
            .tpl-layered .stack-wrap:hover .main-card { transform: none; }
        }
        @media (max-width: 639px) {
            .tpl-layered .layer-back-2 { transform: rotate(-3deg) translate(-6px, 10px); }
            .tpl-layered .layer-back-1 { transform: rotate(2deg) translate(4px, 4px); }
        }
    </style>
</head>
<body class="tpl-layered auth-page antialiased flex items-center justify-center p-4 sm:p-6 text-slate-900">
    <main class="w-full max-w-[340px] sm:max-w-[400px] py-8">
        <div class="stack-wrap">
            <div class="layer layer-back-2" aria-hidden="true"></div>
            <div class="layer layer-back-1" aria-hidden="true"></div>
            <div class="main-card p-6 sm:p-8">
                <div class="flex flex-col items-center mb-6">
                    <div class="h-14 w-14 sm:h-16 sm:w-16 rounded-2xl flex items-center justify-center mb-3 shadow-lg"
                         style="background: linear-gradient(135deg, #6366f1 0%, #ec4899 100%); box-shadow: 0 8px 16px -4px rgba(99,102,241,0.4);">
                        <x-brand-logo size="h-7 w-7 sm:h-8 sm:w-8" extraClass="text-white"/>
                    </div>
                    <h1 class="text-[22px] sm:text-2xl font-extrabold tracking-tight text-center grad-text">{{ setting('store.brand_name', 'LDC Store') }}</h1>
                    <p class="text-[12px] sm:text-[13px] text-slate-600 mt-1 text-center">Login untuk lanjut</p>
                </div>

                @include('auth.templates._errors', [
                    'errorClass' => 'mb-4 rounded-xl bg-rose-50 border border-rose-200 px-3.5 py-2.5 text-[13px] text-rose-700'
                ])

                <form method="POST" action="{{ route('login.post') }}" class="space-y-3.5">
                    @csrf
                    <div>
                        <label for="login-username" class="block text-[12px] font-semibold text-slate-700 mb-1.5">Username</label>
                        <input type="text" name="username" id="login-username" autocomplete="username" value="{{ old('username') }}" required autofocus
                            class="auth-input-light w-full rounded-xl bg-slate-50 border border-slate-200 text-slate-900 px-3.5 py-2.5 text-sm placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white focus:outline-none transition-all"
                            placeholder="admin">
                    </div>
                    <div>
                        <label for="login-password" class="block text-[12px] font-semibold text-slate-700 mb-1.5">Password</label>
                        @include('auth.templates._password_input', [
                            'inputClass' => 'auth-input-light w-full rounded-xl bg-slate-50 border border-slate-200 text-slate-900 px-3.5 py-2.5 text-sm placeholder-slate-400 focus:border-pink-500 focus:ring-2 focus:ring-pink-500/20 focus:bg-white focus:outline-none transition-all',
                            'toggleClass' => 'text-slate-400 hover:text-slate-600',
                        ])
                    </div>
                    <div class="pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
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

                    <button type="submit" class="grad-btn auth-submit-btn w-full rounded-xl px-4 py-3 text-sm font-bold mt-2">
                        Masuk →
                    </button>
                </form>
            </div>
        </div>

        <p class="text-center text-[11px] text-slate-500 mt-10">
            &copy; {{ date('Y') }} {{ setting('store.brand_name', 'LDC Store') }}
        </p>
    </main>
</body>
</html>
