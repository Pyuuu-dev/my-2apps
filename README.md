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

- Single accent color (indigo) + semantic colors (success/warning/danger/info)
- CSS variables untuk theming light/dark mode
- 11 reusable Blade components dengan API konsisten
- Font Inter (weight 400/500/600/700)
- Border-only flat card, no heavy shadow
- Sidebar context-aware (light di light mode, dark di dark mode)

Detail design tokens & komponen di `docs/plans/2026-05-14-redesign-refined-minimal.md`.

## Settings Dinamis

Brand name, kontak (WA, IG, TikTok, Discord), template marketing copy, dan konfigurasi app dapat di-edit lewat halaman `/settings/store` tanpa perlu deploy ulang. Nilai disimpan di tabel `settings` dengan caching otomatis.

> Logo, favicon, dan warna theme browser bersifat fixed default (di-commit ke `public/`). Untuk ganti branding visual, replace file di `public/` lalu redeploy.

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
