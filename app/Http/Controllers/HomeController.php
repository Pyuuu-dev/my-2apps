<?php

namespace App\Http\Controllers;

use App\Models\BloxFruit\AccountStock;
use App\Models\BloxFruit\BloxFruit;
use App\Models\BloxFruit\FruitSkin;
use App\Models\BloxFruit\JokiOrder;
use App\Models\BloxFruit\ProfitRecord;
use App\Models\BloxFruit\StorageAccount;
use App\Models\BloxFruit\WalletBalance;
use App\Models\DietTracker\FoodLog;
use App\Models\DietTracker\UserProfile;
use App\Models\DietTracker\WaterLog;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    /**
     * Halaman beranda — gabungan stats BloxFruit + DietTracker.
     *
     * Menggantikan closure di routes/web.php yang sebelumnya menjalankan 8+ query
     * mentah setiap render. Sekarang di-cache 60 detik untuk stats yang relatif statis.
     */
    public function index()
    {
        $today = now()->toDateString();

        // === Stats yang aman di-cache (refresh tiap 60 detik) ===
        $cached = Cache::remember('home:stats:v1', 60, function () use ($today) {
            $bulanIni = [now()->startOfMonth(), now()->endOfMonth()];

            // BloxFruit counts — single optimized aggregation
            $jokiCounts = JokiOrder::selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status IN ('antrian','proses') THEN 1 ELSE 0 END) as aktif,
                SUM(CASE WHEN status = 'proses' THEN 1 ELSE 0 END) as proses,
                SUM(CASE WHEN status = 'antrian' THEN 1 ELSE 0 END) as antrian
            ")->first();

            $akunCounts = AccountStock::selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'tersedia' THEN 1 ELSE 0 END) as tersedia,
                SUM(CASE WHEN status = 'terjual' THEN 1 ELSE 0 END) as terjual
            ")->first();

            $bfStats = [
                'total_buah' => BloxFruit::count(),
                'total_skin' => FruitSkin::count(),
                'total_akun_storage' => StorageAccount::count(),
                'total_joki' => (int) $jokiCounts->total,
                'joki_aktif' => (int) $jokiCounts->aktif,
                'joki_proses' => (int) $jokiCounts->proses,
                'joki_antrian' => (int) $jokiCounts->antrian,
            ];

            // Keuangan bulan ini — single query aggregate
            $profit = ProfitRecord::whereBetween('tanggal', $bulanIni)
                ->selectRaw('SUM(pendapatan) as pendapatan, SUM(keuntungan) as keuntungan, COUNT(*) as transaksi')
                ->first();

            $keuangan = [
                'pendapatan' => (int) ($profit->pendapatan ?? 0),
                'keuntungan' => (int) ($profit->keuntungan ?? 0),
                'transaksi' => (int) ($profit->transaksi ?? 0),
            ];

            $akunJual = [
                'total' => (int) $akunCounts->total,
                'tersedia' => (int) $akunCounts->tersedia,
                'terjual' => (int) $akunCounts->terjual,
            ];

            return compact('bfStats', 'keuangan', 'akunJual');
        });

        $bfStats = $cached['bfStats'];
        $keuangan = $cached['keuangan'];
        $akunJual = $cached['akunJual'];

        // Wallet — tidak di-cache karena harus selalu fresh
        $wallet = WalletBalance::orderByDesc('tanggal')->orderByDesc('id')->first();
        $keuangan['saldo_wallet'] = $wallet->total ?? 0;

        // Joki aktif (proses + antrian) - 5 terbaru
        $jokiAktif = JokiOrder::whereIn('status', ['proses', 'antrian'])
            ->orderByRaw("CASE status WHEN 'proses' THEN 1 WHEN 'antrian' THEN 2 END")
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        // Transaksi terakhir - 5 terbaru
        $transaksiTerakhir = ProfitRecord::orderByDesc('tanggal')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        // === Diet Tracker (single profile assumption — pakai first profile) ===
        $profile = UserProfile::first();
        $dtStats = null;

        if ($profile && $profile->kalori_target) {
            $kaloriMasuk = FoodLog::where('profile_id', $profile->id)
                ->whereDate('tanggal', $today)
                ->sum('kalori');

            $totalMinum = WaterLog::where('profile_id', $profile->id)
                ->whereDate('tanggal', $today)
                ->sum('jumlah_ml');

            $dtStats = [
                'profile' => $profile,
                'kalori_masuk' => $kaloriMasuk,
                'target_kalori' => $profile->kalori_target,
                'total_minum' => $totalMinum,
                'target_air' => $profile->air_target_ml ?? 2500,
                'berat_sekarang' => $profile->berat_kg,
                'berat_target' => $profile->berat_target,
                'bmi' => $profile->bmi,
            ];
        }

        return view('dashboard.index', compact(
            'bfStats', 'keuangan', 'akunJual', 'jokiAktif', 'transaksiTerakhir', 'dtStats'
        ));
    }
}
