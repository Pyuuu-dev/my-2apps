{{-- A. Modern Gradient — gradient gelap full-screen + glassmorphism card --}}
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    @include('auth.templates._head', ['themeColor' => '#0f172a'])
    <style>
        body.tpl-modern { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="tpl-modern auth-page antialiased flex items-center justify-center p-4 sm:p-6" style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%);">
    <main class="w-full max-w-[340px] sm:max-w-sm py-6">
        {{-- Logo --}}
        <div class="text-center mb-6 sm:mb-8">
            <div class="inline-flex items-center justify-center h-14 w-14 sm:h-16 sm:w-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 mb-4 shadow-lg shadow-indigo-500/30">
                <x-brand-logo size="h-8 w-8 sm:h-9 sm:w-9" extraClass="text-white"/>
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-white">{{ setting('store.brand_name', 'LDC Store') }}</h1>
            <p class="text-sm text-gray-400 mt-1">Masuk untuk melanjutkan</p>
        </div>

        @include('auth.templates._errors')

        <form method="POST" action="{{ route('login.post') }}" class="space-y-3.5 sm:space-y-4">
            @csrf
            <div>
                <label for="login-username" class="block text-sm font-medium text-gray-300 mb-1.5">Username</label>
                <input type="text" name="username" id="login-username" autocomplete="username" value="{{ old('username') }}" required autofocus
                    class="auth-input-dark w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-3 text-sm placeholder-gray-500 focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white/10"
                    placeholder="Masukkan username">
            </div>
            <div>
                <label for="login-password" class="block text-sm font-medium text-gray-300 mb-1.5">Password</label>
                @include('auth.templates._password_input', [
                    'inputClass' => 'auth-input-dark w-full rounded-xl bg-white/5 border border-white/10 text-white px-4 py-3 text-sm placeholder-gray-500 focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white/10',
                    'toggleClass' => 'text-gray-500 hover:text-gray-300',
                    'iconClass' => 'h-5 w-5',
                    'placeholder' => 'Masukkan password',
                ])
            </div>
            <div class="flex items-center">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-white/20 bg-white/5 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-gray-400">Ingat saya</span>
                </label>
            </div>

            @if(config('services.turnstile.site_key'))
            <div class="flex justify-center">
                <div class="cf-turnstile"
                     data-sitekey="{{ config('services.turnstile.site_key') }}"
                     data-theme="dark"
                     data-language="id"></div>
            </div>
            @endif

            <button type="submit" class="auth-submit-btn w-full rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-3 text-sm font-bold text-white hover:from-indigo-500 hover:to-purple-500 shadow-lg shadow-indigo-500/25 transition-all">
                Masuk
            </button>
        </form>
    </main>
</body>
</html>
