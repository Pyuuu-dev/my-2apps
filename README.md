# LDC Store Management — BloxFruit

Laravel admin panel untuk **LDC Store** — manajemen toko jasa joki & jualan akun Blox Fruits Roblox.

## Tech Stack

- Laravel 12 (PHP 8.2+)
- Tailwind CSS v4
- Alpine.js
- Vite
- SQLite / MySQL

## Fitur

- Master data: Fruit, Skin, Gamepass, Permanent Fruit, Joki Service
- Storage account & stock management dengan kapasitas per item
- Joki order workflow (antrian → proses → selesai)
- Profit tracking + 8-channel wallet (Dana / GoPay / ShopeePay / SeaBank / Bank Kalsel / BRI / QRIS / Cash)
- Analisa harga otomatis dengan saran min / ideal / market ceiling
- Rekap bulanan dengan compare month-over-month
- Cari stok & cari slot kosong (multi-tipe: fruit/skin/gamepass/permanent)
- Public landing page (`/`) dengan search realtime
- Stock alert banner untuk item perlu restock
- 20 design template halaman login dengan preview live admin-only

## Setup Development

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

## Routing Penting

- `/` — landing page publik (LDC Store)
- `/login` — form login admin
- `/dashboard` — admin dashboard (butuh auth)
- `/bloxfruit/*` — modul BloxFruit (butuh auth)
- `/settings/store` — pengaturan brand, kontak, marketing copy
- `/settings/theme` — customizer tema (warna, font, layout, density)

## Struktur Project

```
app/
  Http/Controllers/
    BloxFruit/      — controllers untuk modul BloxFruit
    HomeController  — admin dashboard
    StoreSettingsController — UI settings store
    BackupController — backup database via Telegram
  Services/         — service layer (StockAlertService, TelegramService, dll)
  Helpers/          — helper functions auto-loaded (format, settings)
  Models/
    BloxFruit/      — model BloxFruit
    Setting         — settings table generic key-value
config/
  stock.php         — threshold stock alert per kategori
resources/
  views/
    components/     — Blade components reusable (btn, stat-card, modal, dll)
    layouts/        — layout utama
    bloxfruit/      — view bloxfruit
docs/
  plans/            — design & implementation plans
```

## Design System

Aplikasi pakai design system **refined minimal**:

- Single accent color (indigo default, dapat diganti via `/settings/theme`) + semantic colors (success/warning/danger/info)
- CSS variables untuk theming light/dark mode dengan injector dinamis dari database
- Auto-derived `accent-hover` (8% darken di light, 10% lighten di dark)
- 11 reusable Blade components dengan API konsisten
- Font family configurable (Inter default, Manrope, Plus Jakarta, DM Sans, System)
- Border-only flat card, no heavy shadow
- Sidebar context-aware (light di light mode, dark di dark mode) dengan 3 varian (subtle/solid/accent-tint)
- Sidebar sticky dengan auto-scroll-to-active

Detail design tokens & komponen di `docs/plans/2026-05-14-redesign-refined-minimal.md`.

## Settings Dinamis

Brand name, kontak (WA, IG, TikTok, Discord), template marketing copy, dan konfigurasi app dapat di-edit lewat halaman `/settings/store` tanpa perlu deploy ulang. Nilai disimpan di tabel `settings` dengan caching otomatis.

> Logo, favicon, dan warna theme browser bersifat fixed default (di-commit ke `public/`). Untuk ganti branding visual, replace file di `public/` lalu redeploy.

## Theme & UI Customizer

Halaman `/settings/theme` menyediakan customizer untuk:

- **6 preset warna** siap pakai: Indigo (default), Emerald, Rose, Amber, Slate, Ocean
- **Custom override** per token warna (accent, bg, surface, text, semantic) untuk light & dark mode
- **Mode default**: Light / Dark / Ikut Sistem
- **Layout**: border-radius (sm/md/lg), density (compact/normal/longgar), sidebar variant (subtle/solid/accent-tint)
- **Font family**: Inter, Manrope, Plus Jakarta, DM Sans, atau System
- **Reduce motion** toggle untuk matikan animasi
- **Live preview** dengan kontras checker WCAG AA, toggle mobile/desktop, dan render token yang sama dengan dashboard sungguhan
- **Save preset custom** (max 10), apply dari dropdown topbar
- **Export / Import** tema sebagai JSON file untuk backup atau share antar instance
- **20 template halaman login** dengan preview live admin-only (lihat `docs/login-templates.md`)

Quick-toggle preset juga tersedia di topbar (ikon palette di samping toggle dark mode).

Token disimpan di tabel `settings` group `theme*` dengan auto-cache. Browser tab `theme-color` ikut menyesuaikan warna background per mode (light/dark) untuk integrasi PWA.

## Login Templates

20 design template untuk halaman `/login`, terbagi dalam 5 vibe group:

| Group | Templates |
|---|---|
| Professional | modern, split, minimal, corporate, editorial |
| Playful & Bold | brutalist, manga, layered, sketch |
| Themed / Vibes | cyberpunk, terminal, arcade, nature, paper |
| Atmospheric | image, glass, glasslight, holographic, gradient |
| Soft & Tactile | neumorphism |

Pilih dari `/settings/theme` (section "Halaman Login"). Admin authenticated bisa preview tab baru via tombol "Preview" pada setiap card tanpa perlu simpan dulu.

Best practices yang sudah diterapkan:
- Responsive: `min-h-dvh`, `safe-area-inset-bottom`, breakpoint progressive
- Touch target: 44px iOS HIG, 48px Android Material via `@media (pointer: coarse)`
- Android: `-webkit-tap-highlight-color: transparent`, `:hover` wrapped di `@media (hover: hover) and (pointer: fine)` untuk cegah hover stuck
- Browser fallback: `@supports` untuk `backdrop-filter` & `conic-gradient`
- Performance: `will-change` di animated elements, blob hide di low-end Android (≤414px)
- A11y: `id`/`for`/`aria-label`/`autocomplete`/`aria-hidden`, kontras WCAG AA
- Autofill consistency: `auth-input-dark`/`auth-input-light` utility cegah Chrome yellow override

Cara nambah template baru, troubleshooting, dan checklist verification ada di `docs/login-templates.md`.

## Performance

- Cache stats home (TTL 60s)
- Cache stock alert (TTL 5 menit)
- Cache settings forever (auto-invalidate saat update)
- Database indexes pada kolom yang sering di-filter (status, tanggal, kategori)
- Eager loading agresif untuk hindari N+1
- CSS bundle target < 120 KB

## Safety & Backup

- Backup database otomatis 4× sehari (02:00, 08:00, 14:00, 20:00) via Telegram bot
- Manual backup tersedia di topbar dropdown
- Aksi destruktif (kosongkan semua stok) dilindungi modal konfirmasi dengan input ketik manual
- Soft delete + halaman trash untuk transaksi profit
- Selective restore dari backup file tersedia via tinker (lihat `docs/plans/`)

## Lisensi

MIT (Laravel default)
