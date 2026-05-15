# MyApp — BloxFruit Management & Diet Tracker

Laravel admin panel untuk dua modul bisnis terpisah:

- **BloxFruit (LDC Store)** — manajemen toko jasa joki & jualan akun Blox Fruits Roblox
- **DietTracker** — admin panel + Telegram bot untuk monitoring diet & kesehatan

## Tech Stack

- Laravel 12 (PHP 8.2+)
- Tailwind CSS v4
- Alpine.js
- Vite
- SQLite / MySQL

## Modul

### BloxFruit
- Master data: Fruit, Skin, Gamepass, Permanent Fruit, Joki Service
- Storage account & stock management dengan kapasitas per item
- Joki order workflow (antrian → proses → selesai)
- Profit tracking + 8-channel wallet (Dana / GoPay / ShopeePay / SeaBank / Bank Kalsel / BRI / QRIS / Cash)
- Analisa harga otomatis dengan saran min / ideal / market ceiling
- Rekap bulanan dengan compare month-over-month
- Cari stok & cari slot kosong (multi-tipe: fruit/skin/gamepass/permanent)
- Public landing page (`/`) dengan search realtime
- Stock alert banner untuk item perlu restock

### DietTracker
- User profile management
- Food logs + AI calorie estimation
- Telegram bot integration (webhook)
- Statistics global + per user
- Broadcast & send-message ke user aktif
- Food database CRUD

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
- `/diet/*` — modul DietTracker (butuh auth)
- `/settings/store` — pengaturan brand, kontak, marketing copy
- `/webhook/telegram-diet` — endpoint webhook bot Telegram

## Struktur Project

```
app/
  Http/Controllers/
    BloxFruit/      — controllers untuk modul BloxFruit
    DietTracker/    — controllers untuk modul DietTracker
    HomeController  — admin dashboard
    StoreSettingsController — UI settings store
  Services/         — service layer (StockAlertService, dll)
  Helpers/          — helper functions auto-loaded (format, settings)
  Models/
    BloxFruit/      — model BloxFruit
    DietTracker/    — model DietTracker
    Setting         — settings table generic key-value
config/
  stock.php         — threshold stock alert per kategori
resources/
  views/
    components/     — Blade components reusable (btn, stat-card, modal, dll)
    layouts/        — layout utama
    bloxfruit/      — view bloxfruit
    diet/           — view diet
docs/
  plans/            — design & implementation plans
```

## Design System

Aplikasi pakai design system **refined minimal**:

- Single accent color (indigo) + semantic colors (success/warning/danger/info)
- CSS variables untuk theming light/dark mode
- 11 reusable Blade components dengan API konsisten
- Font Inter (weight 400/500/600/700)
- Border-only flat card, no heavy shadow
- Sidebar context-aware (light di light mode, dark di dark mode)

Detail design tokens & komponen di `docs/plans/2026-05-14-redesign-refined-minimal.md`.

## Settings Dinamis

Brand name, kontak (WA, IG, TikTok, Discord), template marketing copy, dan konfigurasi app dapat di-edit lewat halaman `/settings/store` tanpa perlu deploy ulang. Nilai disimpan di tabel `settings` dengan caching otomatis.

## Performance

- Cache stats home (TTL 60s)
- Cache stock alert (TTL 5 menit)
- Cache settings forever (auto-invalidate saat update)
- Database indexes pada kolom yang sering di-filter (status, tanggal, kategori)
- Eager loading agresif untuk hindari N+1
- CSS bundle target < 120 KB

## Lisensi

MIT (Laravel default)
