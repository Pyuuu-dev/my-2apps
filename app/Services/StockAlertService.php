<?php

namespace App\Services;

use App\Models\BloxFruit\BloxFruit;
use App\Models\BloxFruit\FruitSkin;
use App\Models\BloxFruit\Gamepass;
use App\Models\BloxFruit\PermanentFruitPrice;
use Illuminate\Support\Facades\Cache;

/**
 * Stock Alert — agregat item dengan stok di bawah threshold.
 *
 * Hasil di-cache untuk hindari query repetitive saat layout render.
 * Cache di-invalidate manual via clearCache() atau model events (kalau
 * ditambahkan di stock model nanti).
 */
class StockAlertService
{
    public const CACHE_KEY = 'stock_alert:v1';

    public function getAlerts(): array
    {
        return Cache::remember(
            self::CACHE_KEY,
            (int) config('stock.cache_ttl', 300),
            fn () => $this->compute()
        );
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    protected function compute(): array
    {
        $thresholds = config('stock.thresholds', []);
        $alerts = [
            'fruit' => [],
            'skin' => [],
            'gamepass' => [],
            'permanent' => [],
        ];

        // Fruits — threshold per rarity
        $fruits = BloxFruit::with(['fruitStocks' => fn ($q) => $q->where('jumlah', '>', 0)])
            ->get();
        foreach ($fruits as $f) {
            $stok = (int) $f->fruitStocks->sum('jumlah');
            $threshold = $thresholds['fruit'][$f->rarity] ?? 3;
            if ($stok < $threshold) {
                $alerts['fruit'][] = [
                    'id' => $f->id,
                    'nama' => $f->nama,
                    'meta' => $f->rarity,
                    'stok' => $stok,
                    'threshold' => $threshold,
                    'edit_url' => route('bloxfruit.fruits.edit', $f),
                ];
            }
        }

        // Skins
        $skinThreshold = (int) ($thresholds['skin'] ?? 2);
        $skins = FruitSkin::with(['fruit', 'stocks' => fn ($q) => $q->where('jumlah', '>', 0)])
            ->where('aktif', true)->get();
        foreach ($skins as $s) {
            $stok = (int) $s->stocks->sum('jumlah');
            if ($stok < $skinThreshold) {
                $alerts['skin'][] = [
                    'id' => $s->id,
                    'nama' => $s->nama_skin,
                    'meta' => $s->fruit->nama ?? '-',
                    'stok' => $stok,
                    'threshold' => $skinThreshold,
                    'edit_url' => route('bloxfruit.skins.edit', $s),
                ];
            }
        }

        // Gamepasses
        $gpThreshold = (int) ($thresholds['gamepass'] ?? 3);
        $gps = Gamepass::with(['stocks' => fn ($q) => $q->where('jumlah', '>', 0)])
            ->where('aktif', true)->get();
        foreach ($gps as $g) {
            $stok = (int) $g->stocks->sum('jumlah');
            if ($stok < $gpThreshold) {
                $alerts['gamepass'][] = [
                    'id' => $g->id,
                    'nama' => $g->nama,
                    'meta' => '-',
                    'stok' => $stok,
                    'threshold' => $gpThreshold,
                    'edit_url' => route('bloxfruit.gamepasses.edit', $g),
                ];
            }
        }

        // Permanents
        $permThreshold = (int) ($thresholds['permanent'] ?? 1);
        $perms = PermanentFruitPrice::with(['stocks' => fn ($q) => $q->where('jumlah', '>', 0)])
            ->where('aktif', true)->get();
        foreach ($perms as $p) {
            $stok = (int) $p->stocks->sum('jumlah');
            if ($stok < $permThreshold) {
                $alerts['permanent'][] = [
                    'id' => $p->id,
                    'nama' => $p->nama,
                    'meta' => '-',
                    'stok' => $stok,
                    'threshold' => $permThreshold,
                    'edit_url' => route('bloxfruit.permanents.edit', $p),
                ];
            }
        }

        $total = collect($alerts)->sum(fn ($a) => count($a));

        return [
            'total' => $total,
            'by_kategori' => $alerts,
            'counts' => [
                'fruit' => count($alerts['fruit']),
                'skin' => count($alerts['skin']),
                'gamepass' => count($alerts['gamepass']),
                'permanent' => count($alerts['permanent']),
            ],
            'has_alerts' => $total > 0,
        ];
    }
}
