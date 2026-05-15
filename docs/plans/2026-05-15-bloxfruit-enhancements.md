# BloxFruit Enhancements — 5 Items

**Tanggal:** 2026-05-15
**Status:** Approved, eksekusi bertahap

## Items

| # | Item | Effort |
|---|---|---|
| 1 | Stock Alert (banner peringatan stok rendah) | S (~3-4j) |
| 2 | Pencarian di Landing /store | S (~2-3j) |
| 4 | Settings Page UI (edit settings table via UI) | S (~3-4j) |
| 5 | Compare Bulan-ke-Bulan di rekap | S (~3-4j) |
| 6 | Eager Loading + Indexing (performance) | S (~3-4j) |

**Total estimasi:** ~14-19 jam
**Skip:** Item 3 (Order Tracking Public Link), Item 7 (Stock Indicator Landing)

---

## Item 1 — Stock Alert
- Service `StockAlertService` agregat low stock per kategori
- Threshold di `config/stock.php` (Mythical=1, Legendary=2, Rare=3, Uncommon=5, Common=10, dll)
- Component `<x-stock-alert>` banner di top main content
- Cache 5 menit

## Item 2 — Pencarian Landing
- Search bar Alpine `x-data` filter realtime
- Counter "X dari Y", clear button, empty state

## Item 4 — Settings Page UI
- `/settings/store` controller + view
- Form group berdasarkan kolom `group` di tabel settings
- Auto invalidate cache `settings.all`
- Migrate hardcoded marketing copy di `copy-stock-script` ke read settings

## Item 5 — Compare MoM
- RekapController query agregat untuk bulan sebelumnya
- Delta % per metrik (revenue, profit, joki, akun, transaksi)
- Pakai `<x-stat-card>` props `trend` + `trendLabel`

## Item 6 — Eager Loading + Indexing
- Migration `add_performance_indexes` (additive only, reversible)
- Index di joki_orders, profit_records, stocks, account_stocks
- Audit & fix N+1 di controller bloxfruit

## Aturan
1. Tidak ada DROP migration
2. Data lama tetap aman, semua perubahan additive
3. Backward compat: `/settings` lama tetap, `/settings/store` halaman baru terpisah
4. Performance budget CSS < 120 KB
