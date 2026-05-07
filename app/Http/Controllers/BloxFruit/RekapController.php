<?php

namespace App\Http\Controllers\BloxFruit;

use App\Http\Controllers\Controller;
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

        // Joki stats
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

        // Akun terjual bulan ini
        $akunTerjual = ProfitRecord::where('kategori', 'akun')
            ->whereBetween('tanggal', [$start, $end])->count();

        // Fruit terjual bulan ini
        $fruitTerjual = ProfitRecord::where('kategori', 'fruit')
            ->whereBetween('tanggal', [$start, $end])->count();

        // Total transaksi
        $totalTransaksi = ProfitRecord::whereBetween('tanggal', [$start, $end])->count();

        // Kategori labels
        $kategoriLabels = [
            'level' => '⚔️ Joki Level',
            'belly_fragment' => '💰 Belly & Fragment',
            'mastery' => '🔥 Mastery',
            'fighting_style' => '🥋 Fighting Style',
            'sword' => '🗡️ Get Sword',
            'gun' => '🔫 Get Gun',
            'race' => '🧬 Race',
            'boss_raid' => '👹 Boss Raid',
            'haki' => '✨ Haki',
            'instinct' => '👁️ Instinct',
            'awaken' => '🍎 Awaken',
            'material' => '📦 Material',
            'lainnya' => '📝 Lainnya',
        ];

        // Bulan list
        $bulanList = ProfitRecord::selectRaw("strftime('%Y-%m', tanggal) as bulan")
            ->groupBy('bulan')->orderByDesc('bulan')->pluck('bulan')->toArray();
        if (!in_array($bulan, $bulanList)) array_unshift($bulanList, $bulan);

        return view('bloxfruit.rekap.index', compact(
            'bulan', 'bulanLabel', 'bulanList',
            'jokiSelesai', 'jokiByKategori', 'jokiByCustomer',
            'akunTerjual', 'fruitTerjual', 'totalTransaksi', 'kategoriLabels'
        ));
    }
}
