# Login Templates

Multi-template login page system. User pilih template di `/settings/theme` (section "Halaman Login"), value disimpan di setting `theme.login_template`.

## Arsitektur

```
resources/views/auth/
├── login.blade.php              # Switcher entry point
└── templates/
    ├── _head.blade.php          # Shared <head> partial (favicon, meta, fonts)
    ├── _errors.blade.php        # Shared error display partial
    ├── _password_input.blade.php # Shared password toggle (icon-based)
    └── <template>.blade.php     # 20 template files
```

`login.blade.php` membaca whitelist dari `ThemeSettingsController::ENUMS['theme.login_template']` dan include partial template yang dipilih.

## Single Source of Truth

`app/Http/Controllers/ThemeSettingsController.php` punya 3 const yang harus konsisten:

- `ENUMS['theme.login_template']` — array of valid keys (validation di update)
- `LOGIN_TEMPLATES` — metadata per template (label, description, palette, group)
- `LOGIN_TEMPLATE_GROUPS` — group label & order untuk picker UI

## Cara Nambah Template Baru

1. Bikin file `resources/views/auth/templates/<key>.blade.php`
2. Update `LOGIN_TEMPLATES` const tambah entry baru dengan:
   - `label` (string)
   - `description` (string, max 1-2 kalimat)
   - `palette` (array 3 hex colors untuk preview)
   - `group` (key dari `LOGIN_TEMPLATE_GROUPS`)
3. Update `ENUMS['theme.login_template']` tambah key baru di array
4. Update `resources/views/settings/theme.blade.php` picker section:
   - Tambah `@elseif($key === '<key>')` di block thumbnail background
   - Tambah `@elseif($key === '<key>')` di block mock card
5. Run `php artisan view:clear`

## Required Elements per Template

Setiap template wajib punya:

- `<!DOCTYPE html>` + `<html lang="id">`
- `<head>` include `@include('auth.templates._head', ['themeColor' => '#xxx'])`
- `<body class="<tpl-key> auth-page antialiased ...">` (auth-page utility memberikan min-h-dvh + safe-area)
- `<main>` wrapper untuk content (a11y landmark)
- Form `POST {{ route('login.post') }}` dengan `@csrf`
- Input `username` dengan id, autocomplete="username", autofocus, label dengan for=
- Input `password` dengan id, autocomplete="current-password", label dengan for=
- Toggle password visibility (gunakan partial `_password_input` kalau cocok dengan icon SVG, atau custom kalau text-based)
- Checkbox `remember`
- Cloudflare Turnstile widget (kondisional `@if(config('services.turnstile.site_key'))`)
- Submit button dengan class `auth-submit-btn` (memberikan `min-height: 44px` iOS HIG)
- Error display `@include('auth.templates._errors', ['errorClass' => '...'])`

## Best Practices

### Responsive
- Pakai class `auth-page` (defined di `resources/css/app.css`) untuk dapat `min-height: 100dvh` + `padding-bottom: env(safe-area-inset-bottom)`
- Container `max-w-[340px] sm:max-w-md` (atau scale lain) — jangan langsung `max-w-md` di mobile
- Padding progressive: `p-6 sm:p-8 lg:p-10`
- Heading scaling: `text-xl sm:text-2xl`
- Touch target: pakai class `auth-submit-btn`

### Accessibility
- Label `for=` matching input `id=`
- Input `autocomplete` attribute (`username`, `current-password`)
- Toggle button `:aria-label` dinamis
- Decorative element `aria-hidden="true"`
- Color contrast WCAG AA (4.5:1 normal text)
- Focus visible (jangan hapus outline tanpa replacement ring)

### Performance
- Backdrop-filter wrap di `@supports` dengan solid bg fallback
- Conic-gradient wrap di `@supports` dengan linear fallback
- Animasi heavy GPU di `@media (max-width: 639px)` di-throttle atau disable
- `prefers-reduced-motion: reduce` matikan animation

### Browser Support
- Tested di Chrome, Firefox, Safari (iOS + macOS), Edge
- Fallback untuk: `backdrop-filter`, `conic-gradient`, `mask-image`, `clip-path`

## Preview Mode

Authenticated admin bisa preview template tanpa simpan via `?_preview=<key>`:

- Logic di `AuthController::showLogin` — bypass redirect dashboard kalau ada `_preview` valid
- Logic di `login.blade.php` switcher — pakai preview value kalau user authenticated
- UI: link "Preview di tab baru" per card di settings picker

## Verification

Smoke test render semua template:

```bash
php artisan tinker --execute="
foreach (\App\Http\Controllers\ThemeSettingsController::ENUMS['theme.login_template'] as \$t) {
    \App\Models\Setting::updateOrCreate(['key'=>'theme.login_template'], ['value'=>\$t,'group'=>'theme_login','label'=>'x','type'=>'text']);
    \Illuminate\Support\Facades\Cache::forget('settings.all');
    try {
        \$html = view('auth.login')->render();
        echo str_pad(\$t, 14) . ' size=' . strlen(\$html) . ' OK' . PHP_EOL;
    } catch (\Throwable \$e) {
        echo str_pad(\$t, 14) . ' ERROR: ' . \$e->getMessage() . PHP_EOL;
    }
}
"
```

Lint check:

```bash
for f in resources/views/auth/templates/*.blade.php; do php -l "\$f"; done
```

## Troubleshooting

**Template tidak muncul atau fallback ke modern**
- Cek setting value di DB: `select * from settings where key = 'theme.login_template'`
- Cek key ada di `ENUMS['theme.login_template']`
- Cek file template ada di `resources/views/auth/templates/<key>.blade.php`
- Run `php artisan view:clear`

**Preview link tidak jalan saat sudah login**
- Cek `AuthController::showLogin` punya logic bypass `?_preview=<key>`
- Cek query string di-pass ke `login.blade.php` switcher

**Picker tidak menampilkan thumbnail baru**
- Cek `settings/theme.blade.php` picker punya `@elseif($key === '<new>')` block untuk background dan mock card
- Run `php artisan view:clear`

## Daftar Template (20 total)

### Professional
- `modern` — Modern Gradient
- `split` — Split Screen
- `minimal` — Minimal Clean
- `corporate` — Corporate Premium
- `editorial` — Magazine Editorial

### Playful & Bold
- `brutalist` — Brutalist Bold
- `manga` — Anime Manga
- `layered` — 3D Layered
- `sketch` — Hand-drawn Sketch

### Themed / Vibes
- `cyberpunk` — Cyberpunk Neon
- `terminal` — Vintage Terminal
- `arcade` — Retro Arcade 80s
- `nature` — Nature Organic
- `paper` — Paper / Stationery

### Atmospheric
- `image` — Card on Image
- `glass` — Floating Glass
- `glasslight` — Glassmorphism Light
- `holographic` — Holographic Foil
- `gradient` — Bold Mesh Gradient

### Soft & Tactile
- `neumorphism` — Neumorphism Soft
