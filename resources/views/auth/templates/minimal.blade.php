{{-- C. Minimal Clean — Apple-style, monochrome, generous whitespace --}}
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    @include('auth.templates._head', ['themeColor' => '#fafafa'])
    <style>
        body.tpl-minimal {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .tpl-minimal input::placeholder { color: #a1a1aa; }
    </style>
</head>
<body class="tpl-minimal auth-page antialiased flex items-center justify-center p-4 sm:p-6 bg-neutral-50">
    <main class="w-full max-w-[340px] sm:max-w-[380px] md:max-w-[400px] py-6">
        <div class="bg-white rounded-2xl border border-neutral-200 shadow-[0_1px_2px_rgba(0,0,0,0.04),0_8px_24px_rgba(0,0,0,0.04)] p-6 sm:p-8 lg:p-10">
            {{-- Logo --}}
            <div class="flex flex-col items-center mb-6 sm:mb-8">
                <div class="inline-flex items-center justify-center h-11 w-11 sm:h-12 sm:w-12 rounded-xl bg-neutral-900 mb-4 sm:mb-5">
                    <x-brand-logo size="h-6 w-6" extraClass="text-white"/>
                </div>
                <h1 class="text-[20px] sm:text-[22px] font-bold text-neutral-900 tracking-tight text-center">{{ setting('store.brand_name', 'LDC Store') }}</h1>
                <p class="text-[13px] text-neutral-500 mt-1 text-center">Masuk untuk melanjutkan</p>
            </div>

            @include('auth.templates._errors', [
                'errorClass' => 'mb-4 rounded-lg bg-neutral-100 border border-neutral-200 px-3.5 py-2.5 text-[13px] text-neutral-700'
            ])

            <form method="POST" action="{{ route('login.post') }}" class="space-y-3 sm:space-y-3.5">
                @csrf
                <div>
                    <label for="login-username" class="block text-[12px] font-medium text-neutral-700 mb-1.5">Username</label>
                    <input type="text" name="username" id="login-username" autocomplete="username" value="{{ old('username') }}" required autofocus
                        class="auth-input-light w-full rounded-lg bg-neutral-50 border border-neutral-200 text-neutral-900 px-3.5 py-2.5 text-[14px] focus:border-neutral-900 focus:ring-2 focus:ring-neutral-900/10 focus:bg-white focus:outline-none transition-all"
                        placeholder="admin">
                </div>
                <div>
                    <label for="login-password" class="block text-[12px] font-medium text-neutral-700 mb-1.5">Password</label>
                    @include('auth.templates._password_input', [
                        'inputClass' => 'auth-input-light w-full rounded-lg bg-neutral-50 border border-neutral-200 text-neutral-900 px-3.5 py-2.5 text-[14px] focus:border-neutral-900 focus:ring-2 focus:ring-neutral-900/10 focus:bg-white focus:outline-none transition-all',
                        'toggleClass' => 'text-neutral-400 hover:text-neutral-600',
                    ])
                </div>
                <div class="pt-1">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="rounded border-neutral-300 text-neutral-900 focus:ring-neutral-500">
                        <span class="text-[13px] text-neutral-600">Ingat saya</span>
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

                <button type="submit" class="auth-submit-btn w-full rounded-lg bg-neutral-900 hover:bg-neutral-800 active:bg-neutral-950 px-4 py-2.5 text-[14px] font-semibold text-white transition-colors mt-2">
                    Masuk
                </button>
            </form>
        </div>

        <p class="text-center text-[11px] text-neutral-400 mt-6">
            &copy; {{ date('Y') }} {{ setting('store.brand_name', 'LDC Store') }}
        </p>
    </main>
</body>
</html>
