{{-- G. Neumorphism Soft — soft UI shadows, tactile, friendly --}}
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    @include('auth.templates._head', ['themeColor' => '#e0e5ec'])
    <style>
        body.tpl-neu {
            font-family: 'Inter', sans-serif;
            background: #e0e5ec;
        }
        .tpl-neu .neu-card {
            background: #e0e5ec;
            border-radius: 24px;
            box-shadow:
                12px 12px 24px rgba(163, 177, 198, 0.5),
                -12px -12px 24px rgba(255, 255, 255, 0.85);
        }
        .tpl-neu .neu-input {
            background: #e0e5ec; border: none; border-radius: 12px;
            box-shadow:
                inset 4px 4px 8px rgba(163, 177, 198, 0.4),
                inset -4px -4px 8px rgba(255, 255, 255, 0.8);
            color: #475569;
            transition: box-shadow 0.2s ease;
        }
        .tpl-neu .neu-input:focus {
            outline: none;
            box-shadow:
                inset 5px 5px 10px rgba(99, 102, 241, 0.18),
                inset -5px -5px 10px rgba(255, 255, 255, 0.9);
        }
        .tpl-neu .neu-input::placeholder { color: #94a3b8; }
        .tpl-neu .neu-btn {
            background: linear-gradient(145deg, #ebeff5, #c5cad1);
            border-radius: 12px;
            box-shadow:
                6px 6px 12px rgba(163, 177, 198, 0.5),
                -6px -6px 12px rgba(255, 255, 255, 0.9);
            color: #1e293b;
            transition: all 0.15s ease;
        }
        .tpl-neu .neu-btn:hover { transform: translateY(-1px); }
        .tpl-neu .neu-btn:active {
            box-shadow:
                inset 5px 5px 10px rgba(163, 177, 198, 0.5),
                inset -5px -5px 10px rgba(255, 255, 255, 0.9);
            transform: translateY(0);
        }
        .tpl-neu .neu-logo {
            background: #e0e5ec; border-radius: 16px;
            box-shadow:
                inset 4px 4px 8px rgba(163, 177, 198, 0.4),
                inset -4px -4px 8px rgba(255, 255, 255, 0.85);
        }
        .tpl-neu .neu-checkbox {
            appearance: none; -webkit-appearance: none;
            width: 1.1rem; height: 1.1rem; border-radius: 6px;
            background: #e0e5ec;
            box-shadow:
                inset 2px 2px 4px rgba(163, 177, 198, 0.5),
                inset -2px -2px 4px rgba(255, 255, 255, 0.85);
            position: relative; cursor: pointer;
        }
        .tpl-neu .neu-checkbox:checked {
            background: linear-gradient(145deg, #6366f1, #4f46e5);
            box-shadow:
                inset 1px 1px 2px rgba(0, 0, 0, 0.2),
                2px 2px 4px rgba(99, 102, 241, 0.3);
        }
        .tpl-neu .neu-checkbox:checked::after {
            content: '✓'; position: absolute; inset: 0;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.75rem; font-weight: 700; color: white;
        }
        @media (prefers-reduced-motion: reduce) {
            .tpl-neu .neu-btn { transition: none; }
            .tpl-neu .neu-btn:hover { transform: none; }
        }
    </style>
</head>
<body class="tpl-neu auth-page antialiased flex items-center justify-center p-4 sm:p-6 text-slate-700">
    <main class="w-full max-w-[340px] sm:max-w-[400px] py-6">
        <div class="neu-card p-6 sm:p-8 lg:p-10">
            <div class="flex flex-col items-center mb-6 sm:mb-7">
                <div class="neu-logo h-14 w-14 sm:h-16 sm:w-16 flex items-center justify-center mb-4">
                    <x-brand-logo size="h-7 w-7 sm:h-8 sm:w-8" extraClass="text-indigo-500"/>
                </div>
                <h1 class="text-[20px] sm:text-[22px] font-bold text-slate-700 text-center">{{ setting('store.brand_name', 'LDC Store') }}</h1>
                <p class="text-[12px] sm:text-[13px] text-slate-500 mt-1 text-center">Selamat datang kembali</p>
            </div>

            @include('auth.templates._errors', [
                'errorClass' => 'mb-4 rounded-xl px-3.5 py-2.5 text-[13px] text-rose-700 bg-rose-50/80'
            ])

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="login-username" class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 mb-2 px-1">Username</label>
                    <input type="text" name="username" id="login-username" autocomplete="username" value="{{ old('username') }}" required autofocus
                        class="auth-input-light neu-input w-full px-4 py-3 text-sm font-medium"
                        placeholder="admin">
                </div>
                <div>
                    <label for="login-password" class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 mb-2 px-1">Password</label>
                    @include('auth.templates._password_input', [
                        'inputClass' => 'auth-input-light neu-input w-full px-4 py-3 text-sm font-medium',
                        'toggleClass' => 'text-slate-400 hover:text-slate-600',
                    ])
                </div>
                <div class="pt-1">
                    <label class="flex items-center gap-2.5 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="neu-checkbox">
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

                <button type="submit" class="neu-btn auth-submit-btn w-full px-4 py-3 text-sm font-bold mt-2">
                    Masuk
                </button>
            </form>
        </div>

        <p class="text-center text-[11px] text-slate-500 mt-6">
            &copy; {{ date('Y') }} {{ setting('store.brand_name', 'LDC Store') }}
        </p>
    </main>
</body>
</html>
