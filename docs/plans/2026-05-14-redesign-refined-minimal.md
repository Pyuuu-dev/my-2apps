# Redesign Refined Minimal — MyApp

**Tanggal:** 2026-05-14
**Brand:** MyApp (admin) + LDC Store (landing publik)
**Aesthetic:** Refined Minimal
**Status:** Approved, eksekusi bertahap

---

## A. Prinsip Dasar

1. Konten dulu, dekorasi belakangan — gradient/blur/shadow hanya jika berfungsi
2. Hierarki via tipografi & spacing, bukan warna atau border
3. Single accent primary — semantic color hanya untuk meaning
4. Density airy konsisten — 8pt grid
5. Minim animasi — hanya transitions yang membantu interaksi

---

## B. Decisions Locked

| Aspek | Pilihan |
|---|---|
| Aesthetic | Refined minimal |
| Font | Inter saja, weight 400/500/600/700 |
| Sidebar | Context-aware (light/dark), icon mono |
| Background | Flat off-white `#fafaf9` / pure dark `#0a0a0a` |
| Card | Border-only flat, no shadow default |
| Accent | Single indigo + semantic only |
| Brand | MyApp + LDC Store dipertahankan |

---

## C. Design Tokens

### Tipografi
```
Family: Inter
Weights: 400, 500, 600, 700 (drop 800)

Display:   28/32 700 -0.02em
Title:     20/28 600
Subtitle:  16/24 600
Body:      14/20 400
Small:     13/18 400
Caption:   12/16 500 0.02em
Micro:     11/14 600 uppercase wider
```

### Color (CSS vars)

**Light:**
```
--bg            #fafaf9
--surface       #ffffff
--surface-2     #f5f5f4
--border        #e7e5e4
--border-hover  #d6d3d1
--text          #0a0a0a
--text-muted    #525252
--text-subtle   #a3a3a3
--accent        #4f46e5
--accent-hover  #4338ca
--success       #059669
--warning       #d97706
--danger        #dc2626
--info          #0284c7
```

**Dark:**
```
--bg            #0a0a0a
--surface       #141414
--surface-2     #1c1c1c
--border        #262626
--border-hover  #404040
--text          #fafafa
--text-muted    #a3a3a3
--text-subtle   #737373
--accent        #6366f1
--accent-hover  #818cf8
--success       #10b981
--warning       #f59e0b
--danger        #ef4444
--info          #0ea5e9
```

### Spacing (8pt)
`4 / 8 / 12 / 16 / 20 / 24 / 32 / 40 / 48 / 64`

### Radius
- input/btn: 8px
- card: 12px
- modal: 16px

### Shadow
- default: none
- elevated: `0 4px 12px -2px rgba(0,0,0,0.08), 0 2px 4px -1px rgba(0,0,0,0.04)`

### Motion
- Hapus `pageIn`, hapus `transform: scale(0.97)`
- Transition: 150ms cubic-bezier(0.4, 0, 0.2, 1)

---

## D. Element Redesign

### Sidebar
- Light: bg `--surface-2` border kanan
- Dark: bg `#0a0a0a` border kanan
- Icon mono SVG, tidak ada wrapper square gradient
- Active: bg subtle accent + left-border 2px accent
- Section heading: micro caption uppercase tracking

### Topbar
- Flat solid, no backdrop-blur
- Title 16/24 600
- Right tools pakai `<x-btn variant="ghost" size="sm">`

### Button (`<x-btn>`)
Variants: primary, secondary, ghost, danger, success
Sizes: sm (28px), md (36px), lg (40px)
Tidak ada shadow glow, tidak ada translateY hover

### Form
- bg `--surface` (light) / `--surface-2` (dark)
- border `--border`, focus accent ring 2px
- height 36/40px
- label caption style uppercase

### Card
- Single variant: bg `--surface`, border `--border`, radius 12px
- No shadow default
- No gradient strip

### Stat-card
Layout:
```
[icon mono 16] [trend pill optional]
LABEL caption
VALUE 24/600
SUBTITLE small subtle
```

### Badge
Pill flat: success/warning/danger/info/neutral
Rarity tetap colored tapi flat (no gradient)

### Empty state
Icon mono dalam circle bg `--surface-2`, title + message + optional CTA

### Modal
Backdrop rgba(0,0,0,0.4) no blur, panel shadow elevated, radius 16px

### Table
Header bg `--surface-2`, row hover `--surface-2`, padding 12/16px

### Toast
Border-left 3px semantic, slide-in dari kanan

---

## E. Roadmap Eksekusi

### Fase R1 — Tokens & Components
- Update `app.css` (CSS vars, hapus gradient body, hapus pageIn, hapus glass-card blur, refined utilities)
- Update Inter import (drop weight 800)
- Update Blade components: btn, form-*, stat-card, modal, empty-state, page-header

### Fase R2 — Layout & Sidebar
- Sidebar context-aware light/dark
- Icon mono
- Topbar flat
- Active state left-border

### Fase R3 — Dashboard Pages
- `dashboard/index.blade.php`
- `bloxfruit/dashboard.blade.php`

### Fase R4 — Master Data Konsolidasi
- `<x-stock-grid-page>` Blade component
- Migrate fruits/skins/gamepasses/permanents

### Fase R5 — Halaman Besar
- `bloxfruit/profit/index.blade.php` (434 baris)
- `bloxfruit/storage/show.blade.php` extract bulk form

### Fase R6 — Polish Detail
- Empty state seluruh app
- Toast position
- Tab component baru
- Filter chip component

### Fase R7 — Auth & Landing
- Login refined minimal
- Landing /store polish (minor)

---

## F. Aturan Kerja
1. Data lama jangan dihilangkan (welcome.blade, scripts root)
2. CSS class lama (`glass-card`, `btn-primary`, `stat-card`, `sidebar-bg`) tetap berfungsi sebagai bridge selama migrasi
3. Tidak ada DROP migration
4. Performa tetap ringan: hapus backdrop-blur, hapus banyak shadow, hapus pageIn animation

---

## G. Estimasi
| Fase | Estimasi |
|---|---|
| R1 | 4-5 jam |
| R2 | 3-4 jam |
| R3 | 4-5 jam |
| R4 | 4-5 jam |
| R5 | 6-8 jam |
| R6 | 3-4 jam |
| R7 | 2-3 jam |
| **Total** | **26-34 jam** |
