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

        // Stats
        $stats = [
            'joki_selesai' => JokiOrder::where('status', 'selesai')->count(),
            'akun_terjual' => AccountStock::where('status', 'terjual')->count(),
            'total_services' => JokiService::where('aktif', true)->count(),
        ];

        return view('bloxfruit.landing', compact(
            'servicesByKategori', 'kategoriLabels', 'fruitsByRarity', 'skins', 'gamepasses', 'permanents', 'stats'
        ));
    }
}
