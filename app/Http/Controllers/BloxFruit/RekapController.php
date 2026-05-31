<?php

namespace App\Http\Controllers\BloxFruit;

use App\Http\Controllers\Controller;
use App\Models\BloxFruit\JokiCategory;
use App\Models\BloxFruit\JokiOrder;
use App\Models\BloxFruit\AccountStock;
use App\Models\BloxFruit\ProfitRecord;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RekapController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', now()->format('Y-m'));
        $start = Carbon::parse($bulan . '-01')->startOfMonth();
        $end = Carbon::parse($bulan . '-01')->endOfMonth();
        $bulanLabel = $start->translatedFormat('F Y');

        // Bulan sebelumnya untuk perbandingan
        $prevStart = $start->copy()->subMonth()->startOfMonth();
        $prevEnd = $start->copy()->subMonth()->endOfMonth();
        $prevBulanLabel = $prevStart->translatedFormat('F Y');

        // ====== Joki bulan ini ======
        $jokiSelesai = JokiOrder::where('status', 'selesai')
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('tanggal_selesai', [$start, $end])
                  ->orWhere(function ($q2) use ($start, $end) {
                      $q2->whereNull('tanggal_selesai')->whereBetween('updated_at', [$start, $end]);
                  });
            })->get();

        $jokiByKategori = $jokiSelesai->groupBy(function ($j) {
            $parts = explode(':', $j->jenis_joki, 2);
            return $parts[0] ?? 'lainnya';
        })->map->count()->sortDesc();

        $jokiByCustomer = $jokiSelesai->groupBy('nama_pelanggan')->map->count()->sortDesc()->take(5);
        $jokiRevenue = (int) $jokiSelesai->sum('harga');

        // ====== Joki bulan lalu (comparison) ======
        $jokiSelesaiPrev = JokiOrder::where('status', 'selesai')
            ->where(function ($q) use ($prevStart, $prevEnd) {
                $q->whereBetween('tanggal_selesai', [$prevStart, $prevEnd])
                  ->orWhere(function ($q2) use ($prevStart, $prevEnd) {
                      $q2->whereNull('tanggal_selesai')->whereBetween('updated_at', [$prevStart, $prevEnd]);
                  });
            })->count();

        // ====== Akun terjual ======
        $akunTerjual = ProfitRecord::where('kategori', 'akun')
            ->whereBetween('tanggal', [$start, $end])->count();
        $akunTerjualPrev = ProfitRecord::where('kategori', 'akun')
            ->whereBetween('tanggal', [$prevStart, $prevEnd])->count();

        $fruitTerjual = ProfitRecord::where('kategori', 'fruit')
            ->whereBetween('tanggal', [$start, $end])->count();

        // ====== Aggregate finansial bulan ini ======
        $aggregateBulan = ProfitRecord::whereBetween('tanggal', [$start, $end])
            ->selectRaw('COUNT(*) as total, SUM(modal) as modal, SUM(pendapatan) as pendapatan, SUM(keuntungan) as keuntungan')
            ->first();

        $totalTransaksi = (int) ($aggregateBulan->total ?? 0);
        $totalRevenue = (int) ($aggregateBulan->pendapatan ?? 0);
        $totalProfit = (int) ($aggregateBulan->keuntungan ?? 0);
        $totalModal = (int) ($aggregateBulan->modal ?? 0);

        // ====== Aggregate finansial bulan lalu ======
        $aggregatePrev = ProfitRecord::whereBetween('tanggal', [$prevStart, $prevEnd])
            ->selectRaw('COUNT(*) as total, SUM(modal) as modal, SUM(pendapatan) as pendapatan, SUM(keuntungan) as keuntungan')
            ->first();

        $prevTransaksi = (int) ($aggregatePrev->total ?? 0);
        $prevRevenue = (int) ($aggregatePrev->pendapatan ?? 0);
        $prevProfit = (int) ($aggregatePrev->keuntungan ?? 0);

        // ====== Hitung delta ======
        $comparison = [
            'revenue' => $this->compareDelta($totalRevenue, $prevRevenue),
            'profit' => $this->compareDelta($totalProfit, $prevProfit),
            'transaksi' => $this->compareDelta($totalTransaksi, $prevTransaksi),
            'joki' => $this->compareDelta($jokiSelesai->count(), $jokiSelesaiPrev),
            'akun' => $this->compareDelta($akunTerjual, $akunTerjualPrev),
        ];

        // ====== Per kategori ======
        $revenuePerKategori = ProfitRecord::whereBetween('tanggal', [$start, $end])
            ->selectRaw('kategori, COUNT(*) as jumlah, SUM(pendapatan) as pendapatan, SUM(keuntungan) as keuntungan')
            ->groupBy('kategori')->orderByDesc('pendapatan')->get();

        $margin = $totalRevenue > 0 ? round(($totalProfit / $totalRevenue) * 100, 1) : 0;
        $marginPrev = $prevRevenue > 0 ? round(($prevProfit / $prevRevenue) * 100, 1) : 0;
        $marginDelta = round($margin - $marginPrev, 1);

        $kategoriLabels = JokiCategory::flatLabels(false);

        $bulanList = ProfitRecord::selectRaw("strftime('%Y-%m', tanggal) as bulan")
            ->groupBy('bulan')->orderByDesc('bulan')->pluck('bulan')->toArray();
        if (!in_array($bulan, $bulanList)) array_unshift($bulanList, $bulan);

        return view('bloxfruit.rekap.index', compact(
            'bulan', 'bulanLabel', 'bulanList', 'prevBulanLabel',
            'jokiSelesai', 'jokiByKategori', 'jokiByCustomer', 'jokiRevenue',
            'akunTerjual', 'fruitTerjual', 'totalTransaksi', 'kategoriLabels',
            'totalRevenue', 'totalProfit', 'totalModal', 'margin', 'marginPrev', 'marginDelta',
            'revenuePerKategori', 'comparison'
        ));
    }

    /**
     * Hitung delta absolut + persentase + arah trend.
     */
    protected function compareDelta(int|float $current, int|float $previous): array
    {
        $diff = $current - $previous;

        if ($previous == 0) {
            // Tidak bisa hitung %; kalau current > 0 anggap up, kalau 0 anggap flat
            $pct = $current > 0 ? null : 0;
            $direction = $current > 0 ? 'up' : 'flat';
        } else {
            $pct = round(($diff / abs($previous)) * 100, 1);
            if (abs($pct) < 5) {
                $direction = 'flat';
            } else {
                $direction = $diff >= 0 ? 'up' : 'down';
            }
        }

        return [
            'current' => $current,
            'previous' => $previous,
            'diff' => $diff,
            'pct' => $pct, // null kalau prev = 0 dan current > 0
            'direction' => $direction,
            'label' => $this->formatDeltaLabel($pct, $direction),
        ];
    }

    protected function formatDeltaLabel(?float $pct, string $direction): string
    {
        if ($pct === null) return 'Baru';
        if ($direction === 'flat') return '±' . number_format(abs($pct), 1, ',', '.') . '%';
        $sign = $pct >= 0 ? '+' : '';
        return $sign . number_format($pct, 1, ',', '.') . '%';
    }
}
