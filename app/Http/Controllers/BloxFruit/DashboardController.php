<?php

namespace App\Http\Controllers\BloxFruit;

use App\Http\Controllers\Controller;
use App\Models\BloxFruit\BloxFruit;
use App\Models\BloxFruit\FruitStock;
use App\Models\BloxFruit\SkinStock;
use App\Models\BloxFruit\FruitSkin;
use App\Models\BloxFruit\Gamepass;
use App\Models\BloxFruit\GamepassStock;
use App\Models\BloxFruit\PermanentFruitStock;
use App\Models\BloxFruit\PermanentFruitPrice;
use App\Models\BloxFruit\StorageAccount;
use App\Models\BloxFruit\AccountStock;
use App\Models\BloxFruit\JokiOrder;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_buah' => BloxFruit::count(),
            'total_stok_buah' => FruitStock::sum('jumlah'),
            'total_skin_master' => FruitSkin::count(),
            'total_skin' => SkinStock::sum('jumlah'),
            'total_gamepass' => Gamepass::count(),
            'total_stok_gamepass' => GamepassStock::sum('jumlah'),
            'total_permanent_master' => PermanentFruitPrice::count(),
            'total_permanent' => PermanentFruitStock::sum('jumlah'),
            'total_akun_storage' => StorageAccount::where('aktif', true)->count(),
            'akun_tersedia' => AccountStock::where('status', 'tersedia')->count(),
            'akun_terjual' => AccountStock::where('status', 'terjual')->count(),
            'joki_antrian' => JokiOrder::where('status', 'antrian')->count(),
            'joki_proses' => JokiOrder::where('status', 'proses')->count(),
            'joki_selesai' => JokiOrder::where('status', 'selesai')->count(),
        ];

        $jokiTerbaru = JokiOrder::latest()->take(5)->get();
        $akunTerbaru = AccountStock::latest()->take(5)->get();

        return view('bloxfruit.dashboard', compact('stats', 'jokiTerbaru', 'akunTerbaru'));
    }
}
