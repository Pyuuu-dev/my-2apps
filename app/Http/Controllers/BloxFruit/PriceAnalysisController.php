<?php

namespace App\Http\Controllers\BloxFruit;

use App\Http\Controllers\Controller;
use App\Models\BloxFruit\BloxFruit;
use App\Models\BloxFruit\FruitSkin;
use App\Models\BloxFruit\Gamepass;
use App\Models\BloxFruit\PermanentFruitPrice;
use Illuminate\Http\Request;

/**
 * Analisa Harga — bantuan menentukan harga jual yang masih untung lumayan
 * tapi tetap kompetitif mengikuti market.
 *
 * Logika rekomendasi harga:
 * - cost = harga_beli (modal)
 * - Saran minimum  = cost × (1 + min_margin/100)   → batas bawah aman
 * - Saran ideal    = cost × (1 + ideal_margin/100) → margin sehat
 * - Saran maksimum = harga_jual saat ini, atau ideal × 1.15 → ceiling kompetitif
 *
 * Default margin disesuaikan per kategori karena karakteristik pasar berbeda.
 */
class PriceAnalysisController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'fruit');
        $minMargin = (int) $request->get('min_margin', 15);
        $idealMargin = (int) $request->get('ideal_margin', 30);

        // Validasi range margin
        $minMargin = max(0, min(200, $minMargin));
        $idealMargin = max($minMargin, min(300, $idealMargin));

        $rows = collect();
        $title = '';

        switch ($tab) {
            case 'skin':
                $rows = $this->analyzeSkins($minMargin, $idealMargin);
                $title = 'Skin';
                break;
            case 'gamepass':
                $rows = $this->analyzeGamepasses($minMargin, $idealMargin);
                $title = 'Gamepass';
                break;
            case 'permanent':
                $rows = $this->analyzePermanents($minMargin, $idealMargin);
                $title = 'Permanent Fruit';
                break;
            case 'fruit':
            default:
                $rows = $this->analyzeFruits($minMargin, $idealMargin);
                $title = 'Fruit';
                $tab = 'fruit';
                break;
        }

        // Aggregate stats
        $stats = [
            'total_items' => $rows->count(),
            'total_stok' => (int) $rows->sum('stok'),
            'total_modal' => (int) $rows->sum(fn($r) => $r['stok'] * $r['harga_beli']),
            'total_potensi_revenue' => (int) $rows->sum(fn($r) => $r['stok'] * $r['harga_jual']),
            'item_margin_tipis' => $rows->where('status', 'tipis')->count(),
            'item_margin_sehat' => $rows->where('status', 'sehat')->count(),
            'item_belum_set' => $rows->where('status', 'belum')->count(),
        ];
        $stats['total_potensi_profit'] = $stats['total_potensi_revenue'] - $stats['total_modal'];
        $stats['margin_overall'] = $stats['total_potensi_revenue'] > 0
            ? round((($stats['total_potensi_revenue'] - $stats['total_modal']) / $stats['total_potensi_revenue']) * 100, 1)
            : 0;

        return view('bloxfruit.price-analysis.index', compact(
            'tab', 'title', 'rows', 'stats', 'minMargin', 'idealMargin'
        ));
    }

    /**
     * Hitung suggested prices.
     */
    protected function suggest(int $cost, int $currentSell, int $minMargin, int $idealMargin): array
    {
        $minSuggest    = $cost > 0 ? (int) round($cost * (1 + $minMargin / 100)) : $currentSell;
        $idealSuggest  = $cost > 0 ? (int) round($cost * (1 + $idealMargin / 100)) : $currentSell;
        $marketCeiling = max($idealSuggest, (int) round($currentSell * 1.05));

        // Margin saat ini
        $currentMargin = $cost > 0 && $currentSell > 0
            ? round((($currentSell - $cost) / $cost) * 100, 1)
            : 0;

        // Status:
        // - belum   : harga belum di-set (cost atau sell = 0)
        // - tipis   : current margin < minMargin
        // - sehat   : minMargin <= current margin < idealMargin
        // - optimal : current margin >= idealMargin
        if ($cost <= 0 || $currentSell <= 0) {
            $status = 'belum';
        } elseif ($currentMargin < $minMargin) {
            $status = 'tipis';
        } elseif ($currentMargin < $idealMargin) {
            $status = 'sehat';
        } else {
            $status = 'optimal';
        }

        return [
            'min_suggest' => $minSuggest,
            'ideal_suggest' => $idealSuggest,
            'market_ceiling' => $marketCeiling,
            'current_margin' => $currentMargin,
            'profit_per_unit' => $currentSell - $cost,
            'status' => $status,
        ];
    }

    protected function analyzeFruits(int $min, int $ideal)
    {
        return BloxFruit::with(['fruitStocks' => fn($q) => $q->where('jumlah', '>', 0)])
            ->orderByRarity()->orderBy('nama')->get()
            ->map(function ($f) use ($min, $ideal) {
                $stok = (int) $f->fruitStocks->sum('jumlah');
                $cost = (int) ($f->harga_beli ?? 0);
                $sell = (int) ($f->harga_jual ?? 0);
                $sug = $this->suggest($cost, $sell, $min, $ideal);
                return [
                    'id' => $f->id,
                    'nama' => $f->nama,
                    'meta' => $f->rarity,
                    'meta_class' => match($f->rarity) {
                        'Mythical' => 'text-[var(--danger)]',
                        'Legendary' => 'text-[var(--warning)]',
                        'Rare' => 'text-[var(--info)]',
                        'Uncommon' => 'text-[var(--success)]',
                        default => 'text-[var(--text-subtle)]',
                    },
                    'stok' => $stok,
                    'harga_beli' => $cost,
                    'harga_jual' => $sell,
                    'edit_url' => route('bloxfruit.fruits.edit', $f),
                ] + $sug;
            });
    }

    protected function analyzeSkins(int $min, int $ideal)
    {
        return FruitSkin::with(['fruit', 'stocks' => fn($q) => $q->where('jumlah', '>', 0)])
            ->where('aktif', true)
            ->get()
            ->sortBy(fn($s) => $s->fruit->nama ?? '')
            ->map(function ($s) use ($min, $ideal) {
                $stok = (int) $s->stocks->sum('jumlah');
                $cost = (int) ($s->harga_beli ?? 0);
                $sell = (int) ($s->harga_jual ?? 0);
                $sug = $this->suggest($cost, $sell, $min, $ideal);
                return [
                    'id' => $s->id,
                    'nama' => $s->nama_skin,
                    'meta' => $s->fruit->nama ?? '-',
                    'meta_class' => 'text-[var(--text-subtle)]',
                    'stok' => $stok,
                    'harga_beli' => $cost,
                    'harga_jual' => $sell,
                    'edit_url' => route('bloxfruit.skins.edit', $s),
                ] + $sug;
            })->values();
    }

    protected function analyzeGamepasses(int $min, int $ideal)
    {
        return Gamepass::with(['stocks' => fn($q) => $q->where('jumlah', '>', 0)])
            ->where('aktif', true)
            ->orderBy('nama')->get()
            ->map(function ($g) use ($min, $ideal) {
                $stok = (int) $g->stocks->sum('jumlah');
                $cost = (int) ($g->harga_beli ?? 0);
                $sell = (int) ($g->harga_jual ?? 0);
                $sug = $this->suggest($cost, $sell, $min, $ideal);
                return [
                    'id' => $g->id,
                    'nama' => $g->nama,
                    'meta' => format_angka($g->harga_robux) . ' R$',
                    'meta_class' => 'text-[var(--text-subtle)]',
                    'stok' => $stok,
                    'harga_beli' => $cost,
                    'harga_jual' => $sell,
                    'edit_url' => route('bloxfruit.gamepasses.edit', $g),
                ] + $sug;
            });
    }

    protected function analyzePermanents(int $min, int $ideal)
    {
        return PermanentFruitPrice::with(['stocks' => fn($q) => $q->where('jumlah', '>', 0)])
            ->where('aktif', true)
            ->orderBy('nama')->get()
            ->map(function ($p) use ($min, $ideal) {
                $stok = (int) $p->stocks->sum('jumlah');
                $cost = (int) ($p->harga_beli ?? 0);
                $sell = (int) ($p->harga_jual ?? 0);
                $sug = $this->suggest($cost, $sell, $min, $ideal);
                return [
                    'id' => $p->id,
                    'nama' => $p->nama,
                    'meta' => format_angka($p->harga_robux) . ' R$',
                    'meta_class' => 'text-[var(--text-subtle)]',
                    'stok' => $stok,
                    'harga_beli' => $cost,
                    'harga_jual' => $sell,
                    'edit_url' => route('bloxfruit.permanents.edit', $p),
                ] + $sug;
            });
    }
}
