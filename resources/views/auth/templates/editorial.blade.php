{{-- J. Magazine Editorial — asymmetric grid, big serif, color block --}}
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    @include('auth.templates._head', [
        'themeColor' => '#fef3c7',
        'extraFonts' => 'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@400;500;700&display=swap',
    ])
    <style>
        body.tpl-edit {
            font-family: 'DM Sans', 'Inter', sans-serif;
            background: #fef3c7;
            color: #171717;
        }
        .tpl-edit .serif { font-family: 'Playfair Display', 'Georgia', serif; }
        .tpl-edit .number {
            font-family: 'Playfair Display', serif;
            font-weight: 900; font-style: italic;
            color: #dc2626; line-height: 0.85;
        }
        .tpl-edit .edit-input {
            background: transparent; border: none;
            border-bottom: 2px solid #171717;
            color: #171717; border-radius: 0;
            padding-left: 0; padding-right: 0;
            font-size: 1rem;
            transition: border-color 0.15s ease;
        }
        .tpl-edit .edit-input:focus {
            outline: none;
            border-bottom-color: #dc2626;
        }
        .tpl-edit .edit-input::placeholder { color: rgba(23, 23, 23, 0.4); }
        .tpl-edit .edit-btn {
            background: #171717; color: #fef3c7;
            transition: background 0.15s ease;
        }
        .tpl-edit .edit-btn:hover { background: #dc2626; }
        .tpl-edit .accent-block { background: #dc2626; }
        @media (prefers-reduced-motion: reduce) {
            .tpl-edit .edit-input, .tpl-edit .edit-btn { transition: none; }
        }
        @media (max-height: 600px) {
            .tpl-edit .number { font-size: 80px !important; top: 0 !important; }
        }
        @media (max-height: 500px) {
            .tpl-edit .number { display: none; }
        }
    </style>
</head>
<body class="tpl-edit auth-page antialiased">
    <div class="min-h-screen flex flex-col lg:flex-row">

        {{-- Left: editorial display panel --}}
        <aside class="lg:w-1/2 px-6 sm:px-10 lg:px-16 pt-8 lg:pt-12 pb-4 lg:pb-12 relative overflow-hidden">
            <div class="flex items-center justify-between mb-6 lg:mb-10">
                <div class="flex items-center gap-2.5">
                    <div class="h-8 w-8 bg-black flex items-center justify-center">
                        <x-brand-logo size="h-4 w-4" extraClass="text-amber-200"/>
                    </div>
                    <span class="text-sm font-bold tracking-tight">{{ setting('store.brand_name', 'LDC Store') }}</span>
                </div>
                <span class="text-[10px] uppercase tracking-[0.2em] opacity-70">Issue 01 / {{ date('Y') }}</span>
            </div>

            <div class="relative">
                <p class="number text-[110px] sm:text-[160px] lg:text-[180px] absolute -top-4 sm:-top-6 lg:-top-10 -left-2 select-none pointer-events-none" aria-hidden="true">01</p>
                <div class="relative pt-12 sm:pt-20 lg:pt-28 pl-4 sm:pl-8 lg:pl-12">
                    <p class="text-[11px] uppercase tracking-[0.25em] font-semibold mb-3 opacity-70">— The Login</p>
                    <h1 class="serif text-3xl sm:text-5xl lg:text-6xl font-black leading-[0.95] tracking-tight mb-3 sm:mb-5">
                        Where the<br>
                        <span class="italic">numbers</span><br>
                        find their<br>
                        <span class="relative inline-block">
                            <span class="absolute inset-x-0 -bottom-1 h-3 sm:h-4 accent-block opacity-90 -z-0"></span>
                            <span class="relative">home.</span>
                        </span>
                    </h1>
                    <p class="text-sm sm:text-base max-w-md leading-relaxed opacity-80">
                        A quiet workspace for your stock, your sales, and the small details that keep the shop alive.
                    </p>
                </div>
            </div>
        </aside>

        {{-- Right: form panel --}}
        <main class="lg:w-1/2 bg-white px-6 sm:px-10 lg:px-16 py-8 lg:py-12 flex items-center">
            <div class="w-full max-w-[400px] mx-auto">
                <div class="flex items-baseline gap-3 mb-2">
                    <span class="text-[10px] uppercase tracking-[0.25em] font-bold">§ Sign In</span>
                    <span class="flex-1 h-px bg-neutral-300" aria-hidden="true"></span>
                    <span class="text-[10px] tabular-nums opacity-60">/ p.{{ random_int(12, 88) }}</span>
                </div>
                <h2 class="serif text-3xl sm:text-4xl font-black leading-tight mt-2 mb-1">Selamat datang.</h2>
                <p class="text-sm text-neutral-600 mb-6 sm:mb-8">Masuk untuk membaca halaman lainnya.</p>

                @include('auth.templates._errors', [
                    'errorClass' => 'mb-4 px-3 py-2 text-sm bg-rose-50 border-l-4 border-rose-600 text-rose-700'
                ])

                <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="login-username" class="block text-[10px] uppercase tracking-[0.2em] font-bold mb-1.5 opacity-80">— Username</label>
                        <input type="text" name="username" id="login-username" autocomplete="username" value="{{ old('username') }}" required autofocus
                            class="edit-input w-full py-2"
                            placeholder="admin">
                    </div>
                    <div>
                        <label for="login-password" class="block text-[10px] uppercase tracking-[0.2em] font-bold mb-1.5 opacity-80">— Password</label>
                        <div x-data="{ show: false }" class="relative">
                            <input :type="show ? 'text' : 'password'" name="password" id="login-password" autocomplete="current-password" required
                                class="edit-input w-full py-2 pr-12"
                                placeholder="••••••••">
                            <button type="button" @click="show = !show"
                                :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'"
                                class="absolute right-0 bottom-2 text-[10px] uppercase tracking-wider font-bold opacity-60 hover:opacity-100">
                                <span x-text="show ? 'hide' : 'show'"></span>
                            </button>
                        </div>
                    </div>
                    <div class="pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="rounded-none border-neutral-900 text-neutral-900 focus:ring-neutral-900">
                            <span class="text-[13px] text-neutral-700">Tetap masuk</span>
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

                    <button type="submit" class="edit-btn auth-submit-btn w-full px-6 py-3 text-sm font-bold uppercase tracking-[0.2em] mt-3">
                        Masuk →
                    </button>
                </form>

                <p class="text-[10px] uppercase tracking-[0.2em] opacity-60 mt-8">
                    &copy; {{ date('Y') }} — {{ setting('store.brand_name', 'LDC Store') }}
                </p>
            </div>
        </main>
    </div>
</body>
</html>
