# Audit & Redesign Plan — LDC Store (BloxFruit)

**Tanggal:** 2026-05-14
**Update:** 2026-05-26 — modul DietTracker dihapus dari project; section terkait diet di-prune.
**Scope:** Blade files BloxFruit, layout, 14 BloxFruit controllers
**Brand:** MyApp (admin) + LDC Store (landing publik)
**Status:** Approved, eksekusi bertahap

---

## A. Konteks Aplikasi

Aplikasi Laravel single-module (sejak 2026-05-26):

**BloxFruit** — manajemen toko jasa joki & jual akun Roblox (LDC Store)
- Dashboard, Search, Landing publik
- Master data: Fruits, Skins, Gamepasses, Permanent Fruits, Joki Services
- Operasional: Storage Account, Account Stock, Joki Order
- Keuangan: Profit, Wallet, Trash, Rekap, Quick Sell

**Stack:** Laravel + Tailwind v4 + Alpine.js + Vite. Tanpa Livewire/Inertia.

---

## B. Temuan Audit (Ringkas)

### Pattern Berulang (kandidat refactor)
- Stat-card 4-grid muncul di 5+ halaman
- Header section flex-col→sm:flex-row di hampir semua index
- Modal Alpine pattern di 7+ tempat
- Master data index (fruits/skins/gp/permanents) ~80% identik
- Native `confirm()` dipakai di 15+ tempat

### Inkonsistensi
- Dark mode partial: ~60% lengkap, ~40% tidak (form labels & beberapa card)
- Tombol "+ Tambah" ukuran beragam
- Format tanggal acak (5+ varian)

### Hardcoded yang seharusnya konfigurabel
- Marketing copy LDC + nomor WA + link sosmed di `copy-stock-script.blade.php`
- Status workflow joki, kategori transaksi, metode bayar di banyak blade
- Achievement threshold, timezone label

### Performance Issue
- N+1 query di `dashboard/index.blade.php`
- Closure dashboard di `routes/web.php` execute 8+ query per render

### Security
- `password_roblox` SUDAH encrypted di DB (cast di model, baris 22) ✓
- `accounts/form.blade.php:17` input pakai `type="text"` — bocor visual
- Backup config form tag-balancing rapuh

### Feature Gap
- `rekap` tidak menampilkan revenue/profit di hero

### Dead Code
- `welcome.blade.php` — default Laravel, tidak terpakai (route `/` overridden)

---

## C. Roadmap Eksekusi

### Fase 1 — Foundation (Low risk, high value)

**1.0 Design Tokens & Visual Direction**
- Generate token via `ui-design-system/scripts/design_token_generator.py`
- Visual direction: **Refined minimal + sharp accent**
  - Display font: tetap Inter (sudah loaded), tambah font-weight variation
  - Palette: slate-950 sidebar (sekarang) + indigo primary + emerald success + amber warning
  - Spacing scale konsisten: 4/6/8 (sm/md/lg)
- Output: token tetap di `resources/css/app.css` (extend yang ada)

**1.1 Blade Component Library** (`resources/views/components/`)
- `<x-page-header :title :subtitle>` + slot `actions`
- `<x-stat-card :label :value :sub :accent>`
- `<x-form-card>` wrapper dark-aware
- `<x-form-label>`, `<x-form-input>`, `<x-form-select>`, `<x-form-textarea>`
- `<x-btn variant size>`
- `<x-modal name :title>` Alpine event-based
- `<x-empty-state :icon :message>`
- `<x-confirm-form>` ganti native `confirm()`

**1.2 Helper functions**
- `app/Helpers/format.php`: `format_tanggal()`, `format_rupiah()`, `format_bulan()`
- Auto-load via composer.json

**1.3 Settings table** (baru)
- Migration `create_settings_table` (key-value)
- Model `App\Models\Setting`
- Seeder default: `store.wa_number`, `store.tiktok_url`, `store.instagram_url`, `store.copy_header_template`, `store.brand_name`
- Helper `setting('key', 'default')`

**1.4 Cleanup**
- `welcome.blade.php`: tambah komentar header "Default Laravel — kept for reference, not routed"
- Python scripts root: biarkan (sesuai permintaan)

Estimasi: 6-8 jam

---

### Fase 2 — Dark Mode Konsistensi

**2.1 Label form**
- Audit semua `text-gray-700` di form blade
- Migrate ke `<x-form-label>` (auto dark-aware)

**2.2 Card form unify**
- `fruits/form.blade.php`, `skins/form.blade.php`, `accounts/form.blade.php` → tambah dark variant

**2.3 Tabel & misc**
- `accounts/index.blade.php` — full dark
- `joki-services/index.blade.php` — header table dark
- `bloxfruit/search/index.blade.php` — section atas dark

Estimasi: 3-4 jam

---

### Fase 3 — Refactor Master Data

**3.1 Master data konsolidasi**
- `<x-stock-grid-page>` Blade component
- Migrate fruits → skins → gamepasses → permanents

**3.2 Storage show stock bulk form**
- Extract `<x-stock-bulk-form>` (4 tab identik)
- Hilangkan trick `@if($loop->last)</div>`

**3.3 Copy-stock script ke Alpine.data**
- Pindahkan `stockPage()` ke `resources/js/alpine/`
- Marketing copy migrate ke settings DB

Estimasi: 6-8 jam

---

### Fase 4 — Performance

**4.1 Dashboard closure → controller**
- Buat `App\Http\Controllers\HomeController@index`
- Cache stats `Cache::remember('home:stats', 60, ...)`
- Single agregate query

**4.2 Fix N+1**
- `dashboard/index.blade.php` → `withSum()` di controller

Estimasi: 3-4 jam

---

### Fase 5 — Security

**5.1 Password Roblox display**
- `accounts/form.blade.php:17` → `type="password"` + Alpine toggle
- `accounts/index.blade.php` → masked display, reveal via dedicated endpoint
- Migration rotate **DIBATALKAN** (sudah encrypted)

**5.2 Backup config form**
- Pisah jadi 2 form clean (hilangkan tag-balancing manual)
- Pertimbangkan store ke settings DB instead of `.env`

**5.3 Rate limit login**
- `throttle:5,1` di `login.post`

Estimasi: 3-4 jam

---

### Fase 6 — Feature Gap

**6.1 Rekap revenue di hero**
- Aggregate `total_revenue`, `total_profit` di `RekapController`
- Display di hero card

Estimasi: 2-3 jam

---

### Fase 7 — Visual Identity (Optional)

- Tetap brand "MyApp"
- Spacing scale unify
- Empty state + confirm dialog redesign
- Card pattern unify
- Sesi terpisah dengan skill `brainstorming`

---

## D. Urutan & Eksekusi Prioritas

```
Fase 1 (Foundation) ─┬─ Fase 2 (Dark mode)
                     ├─ Fase 4 (Performance)   } paralel-aman
                     └─ Fase 5 (Security)
                          └─ Fase 3 (Refactor master)
                               └─ Fase 6 (Feature gap)
                                    └─ Fase 7 (Visual rebrand) opsional
```

---

## E. Aturan Kerja

1. **Data lama jangan dihilangkan** — termasuk `welcome.blade.php`, Python scripts, file `.txt`
2. **Brand tetap "MyApp"** untuk admin, "LDC Store" untuk landing publik
3. **Password Roblox sudah encrypted** di model — tidak perlu rotate

---

## F. Testing Strategy

- Manual smoke test setiap halaman setelah migration
- Cek dark mode toggle di setiap halaman ✓
- Login + logout flow
- Backup download + Telegram send
- Form CRUD di setiap modul
- Search BloxFruit dengan kombinasi item
- Quick sell + clear stock

---

## G. Open Questions (resolved)

| Q | Jawaban |
|---|---|
| `welcome.blade.php` boleh dihapus? | Tidak. Biarkan + tambah komentar |
| Tabel `settings` sudah ada? | Belum, perlu dibuat |
| Password Roblox migration rotate? | Tidak perlu, sudah encrypted |
| Visual rebrand brand baru? | Tetap MyApp |
