{{-- H. Cyberpunk Neon — black bg, neon pink/cyan glow, scanlines --}}
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    @include('auth.templates._head', [
        'themeColor' => '#0a0014',
        'extraFonts' => 'https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700;900&family=Share+Tech+Mono&display=swap',
    ])
    <style>
        body.tpl-cyber {
            font-family: 'Inter', sans-serif;
            background: #0a0014;
            background-image:
                radial-gradient(circle at 20% 30%, rgba(255, 0, 110, 0.18) 0%, transparent 45%),
                radial-gradient(circle at 80% 70%, rgba(0, 255, 255, 0.14) 0%, transparent 45%),
                linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 100% 100%, 100% 100%, 32px 32px, 32px 32px;
            color: #e2e8f0;
        }
        .tpl-cyber .display { font-family: 'Orbitron', 'Inter', sans-serif; letter-spacing: 0.02em; }
        .tpl-cyber .mono { font-family: 'Share Tech Mono', 'Courier New', monospace; }
        .tpl-cyber .neon-card {
            background: rgba(10, 0, 20, 0.85);
            border: 1px solid #ff006e;
            box-shadow:
                0 0 24px rgba(255, 0, 110, 0.4),
                0 0 60px rgba(255, 0, 110, 0.2),
                inset 0 0 24px rgba(255, 0, 110, 0.08);
        }
        .tpl-cyber .neon-card::before {
            content: ''; position: absolute; inset: 0; pointer-events: none;
            background: repeating-linear-gradient(
                0deg,
                transparent 0,
                transparent 2px,
                rgba(255, 255, 255, 0.025) 2px,
                rgba(255, 255, 255, 0.025) 3px
            );
            mix-blend-mode: overlay;
        }
        .tpl-cyber .neon-input {
            background: rgba(0, 255, 255, 0.04);
            border: 1px solid rgba(0, 255, 255, 0.25);
            color: #e2e8f0;
            transition: all 0.15s ease;
        }
        .tpl-cyber .neon-input:focus {
            outline: none;
            border-color: #00ffff;
            background: rgba(0, 255, 255, 0.08);
            box-shadow: 0 0 12px rgba(0, 255, 255, 0.4), inset 0 0 8px rgba(0, 255, 255, 0.1);
        }
        .tpl-cyber .neon-input::placeholder { color: rgba(0, 255, 255, 0.45); }
        .tpl-cyber .neon-btn {
            background: linear-gradient(135deg, #ff006e 0%, #d6005a 100%);
            color: white;
            border: 1px solid #ff006e;
            box-shadow: 0 0 18px rgba(255, 0, 110, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.2);
            clip-path: polygon(8px 0, 100% 0, calc(100% - 8px) 100%, 0 100%);
            transition: all 0.15s ease;
        }
        .tpl-cyber .neon-btn:hover {
            box-shadow: 0 0 28px rgba(255, 0, 110, 0.75), inset 0 1px 0 rgba(255, 255, 255, 0.3);
        }
        .tpl-cyber .glitch {
            position: relative; color: #00ffff;
            text-shadow: 0 0 8px rgba(0, 255, 255, 0.6);
        }
        .tpl-cyber .accent-line {
            background: linear-gradient(90deg, transparent, #ff006e 30%, #00ffff 70%, transparent);
            height: 1px;
        }
        @media (prefers-reduced-motion: reduce) {
            .tpl-cyber .neon-card::before { display: none; }
            .tpl-cyber .neon-btn { transition: none; }
        }
    </style>
</head>
<body class="tpl-cyber auth-page antialiased flex items-center justify-center p-4 sm:p-6">
    <main class="w-full max-w-[340px] sm:max-w-md py-6 relative">
        <div class="neon-card relative rounded-md p-6 sm:p-7">
            <div class="flex items-center gap-3 mb-1">
                <div class="h-9 w-9 flex items-center justify-center"
                     style="background: linear-gradient(135deg, #ff006e, #00ffff); clip-path: polygon(20% 0, 100% 0, 80% 100%, 0 100%);">
                    <x-brand-logo size="h-4 w-4" extraClass="text-white"/>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="mono text-[10px] text-cyan-300 tracking-widest uppercase">// {{ now()->format('Hi') }} :: secure</p>
                    <p class="display text-base font-bold text-white truncate">{{ setting('store.brand_name', 'LDC Store') }}</p>
                </div>
            </div>

            <div class="accent-line my-4" aria-hidden="true"></div>

            <h1 class="display text-2xl sm:text-3xl font-black uppercase tracking-tight">
                <span class="glitch">Access</span><br>
                <span class="text-white">Granted?</span>
            </h1>
            <p class="mono text-[11px] text-pink-300 mt-2 uppercase">&gt; identification required</p>

            <div class="mt-5">
                @include('auth.templates._errors', [
                    'errorClass' => 'mb-4 mono text-[12px] text-pink-200 bg-pink-500/10 border border-pink-500/40 px-3 py-2'
                ])

                <form method="POST" action="{{ route('login.post') }}" class="space-y-3.5">
                    @csrf
                    <div>
                        <label for="login-username" class="mono block text-[10px] uppercase tracking-widest text-cyan-300 mb-1.5">[ user_id ]</label>
                        <input type="text" name="username" id="login-username" autocomplete="username" value="{{ old('username') }}" required autofocus
                            class="auth-input-dark neon-input mono w-full px-3 py-2.5 text-sm rounded-sm"
                            placeholder="admin">
                    </div>
                    <div>
                        <label for="login-password" class="mono block text-[10px] uppercase tracking-widest text-cyan-300 mb-1.5">[ access_key ]</label>
                        <div x-data="{ show: false }" class="relative">
                            <input :type="show ? 'text' : 'password'" name="password" id="login-password" autocomplete="current-password" required
                                class="auth-input-dark neon-input mono w-full px-3 py-2.5 text-sm rounded-sm pr-12"
                                placeholder="••••••••">
                            <button type="button" @click="show = !show"
                                :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'"
                                class="absolute right-2 top-1/2 -translate-y-1/2 mono text-[10px] uppercase text-cyan-300 hover:text-cyan-200 px-2 py-1 border border-cyan-500/40 hover:border-cyan-300">
                                <span x-text="show ? 'hide' : 'show'"></span>
                            </button>
                        </div>
                    </div>
                    <div class="pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="rounded-sm bg-cyan-500/10 border-cyan-500/40 text-pink-500 focus:ring-pink-500">
                            <span class="mono text-[12px] text-cyan-200 uppercase tracking-wider">Persist session</span>
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

                    <button type="submit" class="neon-btn auth-submit-btn display w-full px-5 py-2.5 text-sm font-bold uppercase tracking-widest mt-2">
                        Authenticate &raquo;
                    </button>
                </form>
            </div>
        </div>

        <p class="mono text-center text-[10px] text-cyan-400/60 mt-5 uppercase tracking-widest">
            &copy; {{ date('Y') }} :: {{ setting('store.brand_name', 'LDC Store') }} :: encrypted
        </p>
    </main>
</body>
</html>
