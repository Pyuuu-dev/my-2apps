<?php

namespace App\Http\Controllers\BloxFruit;

use App\Http\Controllers\Controller;
use App\Models\BloxFruit\JokiService;
use App\Models\BloxFruit\JokiOrder;
use App\Models\BloxFruit\AccountStock;
use App\Models\BloxFruit\BloxFruit;
use App\Models\BloxFruit\FruitSkin;
use App\Models\BloxFruit\Gamepass;
use App\Models\BloxFruit\PermanentFruitPrice;
use App\Models\BloxFruit\ProfitRecord;
use App\Services\BrandingService;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class LandingController extends Controller
{
    public function index()
    {
        // Joki services grouped by kategori
        $servicesByKategori = JokiService::where('aktif', true)->orderBy('harga')->get()->groupBy('kategori');

        $kategoriLabels = [
            'level' => ['label' => 'Joki Level', 'icon' => '⚔️'],
            'belly_fragment' => ['label' => 'Belly & Fragment', 'icon' => '💰'],
            'mastery' => ['label' => 'Mastery', 'icon' => '🔥'],
            'fighting_style' => ['label' => 'Fighting Style V2', 'icon' => '🥋'],
            'sword' => ['label' => 'Get Sword', 'icon' => '🗡️'],
            'gun' => ['label' => 'Get Gun', 'icon' => '🔫'],
            'race' => ['label' => 'Up & Get Race', 'icon' => '🧬'],
            'boss_raid' => ['label' => 'Boss Raid', 'icon' => '👹'],
            'haki' => ['label' => 'Haki Legendary', 'icon' => '✨'],
            'instinct' => ['label' => 'Instinct', 'icon' => '👁️'],
            'awaken' => ['label' => 'Awaken Fruit', 'icon' => '🍎'],
            'material' => ['label' => 'Material', 'icon' => '📦'],
            'lainnya' => ['label' => 'Lainnya', 'icon' => '📝'],
        ];

        // Fruits grouped by rarity
        $fruitsByRarity = BloxFruit::where('aktif', true)
            ->orderByRaw("CASE rarity WHEN 'Mythical' THEN 5 WHEN 'Legendary' THEN 4 WHEN 'Rare' THEN 3 WHEN 'Uncommon' THEN 2 WHEN 'Common' THEN 1 END DESC")
            ->orderByDesc('harga_jual')
            ->get()
            ->groupBy('rarity');

        // Skins
        $skins = FruitSkin::where('aktif', true)
            ->orderByDesc('harga_jual')->get();

        // Gamepasses
        $gamepasses = Gamepass::where('aktif', true)
            ->orderByDesc('harga_jual')->get();

        // Permanent fruit prices
        $permanents = PermanentFruitPrice::where('aktif', true)
            ->orderBy('harga_jual')->get();

        // Penjualan kategori (dari profit_records: 1 row = 1 transaksi)
        $kategoriTerjual = ProfitRecord::query()
            ->whereIn('kategori', ['fruit', 'skin', 'gamepass', 'permanent'])
            ->selectRaw('kategori, COUNT(*) as total')
            ->groupBy('kategori')
            ->pluck('total', 'kategori');

        // Stats
        $stats = [
            'joki_selesai'      => JokiOrder::where('status', 'selesai')->count(),
            'akun_terjual'      => AccountStock::where('status', 'terjual')->count(),
            'total_services'    => JokiService::where('aktif', true)->count(),
            'fruit_terjual'     => (int) ($kategoriTerjual['fruit']     ?? 0),
            'skin_terjual'      => (int) ($kategoriTerjual['skin']      ?? 0),
            'gamepass_terjual'  => (int) ($kategoriTerjual['gamepass']  ?? 0),
            'permanent_terjual' => (int) ($kategoriTerjual['permanent'] ?? 0),
            'item_terjual'      => (int) $kategoriTerjual->sum(),
        ];

        return view('bloxfruit.landing', compact(
            'servicesByKategori', 'kategoriLabels', 'fruitsByRarity', 'skins', 'gamepasses', 'permanents', 'stats'
        ));
    }

    /**
     * Dynamic PWA manifest. Reads brand_name & icons from settings each request
     * but is cheap (helper-cached). Throttled at route level.
     */
    public function manifest(BrandingService $branding): SymfonyResponse
    {
        return response()
            ->json($branding->buildManifest(), 200, [
                'Content-Type' => 'application/manifest+json; charset=utf-8',
                'Cache-Control' => 'public, max-age=300',
            ]);
    }
}
