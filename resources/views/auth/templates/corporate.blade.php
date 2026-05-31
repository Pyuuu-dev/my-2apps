{{-- Q. Corporate Premium — dark navy + gold accent, formal banking/fintech --}}
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    @include('auth.templates._head', [
        'themeColor' => '#0c1e3e',
        'extraFonts' => 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;700&family=Inter:wght@400;500;600;700&display=swap',
    ])
    <style>
        body.tpl-corp {
            font-family: 'Inter', sans-serif;
            background: #0c1e3e;
            color: #e2e8f0;
            background-image:
                linear-gradient(135deg, #0c1e3e 0%, #16345e 100%),
                radial-gradient(circle at 20% 20%, rgba(212, 175, 55, 0.08) 0%, transparent 40%);
        }
        .tpl-corp .display { font-family: 'Cormorant Garamond', 'Georgia', serif; letter-spacing: -0.01em; }
        .tpl-corp .gold-line {
            background: linear-gradient(90deg, transparent, #d4af37 50%, transparent);
            height: 1px;
        }
        .tpl-corp .corp-card {
            background: rgba(8, 14, 30, 0.85);
            border: 1px solid rgba(212, 175, 55, 0.25);
            box-shadow:
                0 25px 60px -15px rgba(0, 0, 0, 0.6),
                inset 0 1px 0 rgba(212, 175, 55, 0.1);
        }
        .tpl-corp .corp-input {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(212, 175, 55, 0.2);
            color: #ffffff;
            transition: all 0.15s ease;
        }
        .tpl-corp .corp-input:focus {
            outline: none;
            border-color: #d4af37;
            background: rgba(212, 175, 55, 0.05);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.15);
        }
        .tpl-corp .corp-input::placeholder { color: rgba(255, 255, 255, 0.35); }
        .tpl-corp .corp-btn {
            background: linear-gradient(135deg, #d4af37 0%, #c19b2c 100%);
            color: #0c1e3e;
            border: 1px solid rgba(212, 175, 55, 0.6);
            box-shadow: 0 8px 22px -4px rgba(212, 175, 55, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.25);
            transition: all 0.15s ease;
        }
        @media (hover: hover) and (pointer: fine) {
            .tpl-corp .corp-btn:hover {
                transform: translateY(-1px);
                box-shadow: 0 12px 30px -4px rgba(212, 175, 55, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.3);
            }
        }
        .tpl-corp .gold-text { color: #d4af37; }
        @media (prefers-reduced-motion: reduce) {
            .tpl-corp .corp-btn { transition: none; }
            .tpl-corp .corp-btn:hover { transform: none; }
        }
    </style>
</head>
<body class="tpl-corp auth-page antialiased flex items-center justify-center p-4 sm:p-6">
    <main class="w-full max-w-[340px] sm:max-w-md py-6">
        <div class="text-center mb-5">
            <div class="inline-flex items-center gap-2.5 mb-3">
                <span class="h-px w-8 bg-amber-500/40"></span>
                <span class="text-[10px] font-bold uppercase tracking-[0.3em] gold-text">Established</span>
                <span class="h-px w-8 bg-amber-500/40"></span>
            </div>
            <p class="display text-3xl sm:text-4xl text-white leading-none">{{ setting('store.brand_name', 'LDC Store') }}</p>
            <p class="text-[10px] uppercase tracking-[0.25em] text-white/60 mt-2">— Trusted Management Suite —</p>
        </div>

        <div class="corp-card rounded-sm p-6 sm:p-8">
            <div class="flex items-center justify-center gap-2 mb-1">
                <div class="h-9 w-9 flex items-center justify-center border border-amber-500/40" style="background: rgba(212, 175, 55, 0.08);">
                    <x-brand-logo size="h-4 w-4" extraClass="gold-text"/>
                </div>
                <h1 class="display text-xl sm:text-2xl font-bold gold-text">Sign In</h1>
            </div>
            <div class="gold-line my-4"></div>

            @include('auth.templates._errors', [
                'errorClass' => 'mb-4 px-3.5 py-2.5 text-sm text-rose-200 bg-rose-900/30 border-l-2 border-rose-500'
            ])

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="login-username" class="block text-[10px] font-semibold uppercase tracking-[0.2em] text-amber-200/80 mb-1.5">Username</label>
                    <input type="text" name="username" id="login-username" autocomplete="username" value="{{ old('username') }}" required autofocus
                        class="auth-input-dark corp-input w-full rounded-sm px-3.5 py-2.5 text-sm font-medium"
                        placeholder="admin">
                </div>
                <div>
                    <label for="login-password" class="block text-[10px] font-semibold uppercase tracking-[0.2em] text-amber-200/80 mb-1.5">Password</label>
                    @include('auth.templates._password_input', [
                        'inputClass' => 'auth-input-dark corp-input w-full rounded-sm px-3.5 py-2.5 text-sm font-medium',
                        'toggleClass' => 'text-amber-200/60 hover:text-amber-200',
                    ])
                </div>
                <div class="pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded-sm border-amber-500/40 bg-white/5 text-amber-500 focus:ring-amber-500">
                        <span class="text-[12px] text-white/70 uppercase tracking-wider">Remember me</span>
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

                <button type="submit" class="corp-btn auth-submit-btn w-full rounded-sm px-4 py-3 text-sm font-bold uppercase tracking-[0.18em] mt-3">
                    Authorize Access
                </button>
            </form>

            <div class="gold-line mt-5"></div>
            <p class="text-[10px] uppercase tracking-[0.2em] text-white/40 text-center mt-3">
                Confidential / Authorized Personnel Only
            </p>
        </div>

        <p class="text-center text-[10px] text-white/40 mt-5 uppercase tracking-[0.2em]">
            &copy; {{ date('Y') }} {{ setting('store.brand_name', 'LDC Store') }} — All Rights Reserved
        </p>
    </main>
</body>
</html>
