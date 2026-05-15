<?php

namespace App\Http\Controllers\BloxFruit;

use App\Http\Controllers\Controller;
use App\Models\BloxFruit\BloxFruit;
use App\Models\BloxFruit\FruitSkin;
use App\Models\BloxFruit\Gamepass;
use App\Models\BloxFruit\PermanentFruitPrice;
use App\Models\BloxFruit\StorageAccount;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $allFruits = BloxFruit::orderByRarity()->orderBy('nama')->get();
        $allSkins = FruitSkin::with('fruit')->get()->sortBy(fn($s) => $s->fruit->nama ?? '');
        $allGamepasses = Gamepass::orderBy('nama')->get();
        $allPermanents = PermanentFruitPrice::where('aktif', true)->orderByDesc('harga_jual')->get();

        $results = null;
        $emptyResults = null;
        $searchedItems = [];
        $mode = $request->get('mode', 'semua'); // semua = punya semua, sebagian = punya minimal 1

        $fruitIds = array_filter($request->get('fruits', []), fn($v) => $v);
        $skinIds = array_filter($request->get('skins', []), fn($v) => $v);
        $gpIds = array_filter($request->get('gamepasses', []), fn($v) => $v);
        $permIds = array_filter($request->get('permanents', []), fn($v) => $v);

        $hasSearch = count($fruitIds) || count($skinIds) || count($gpIds) || count($permIds);

        if ($hasSearch) {
            // Kumpulkan label item yang dicari
            if ($fruitIds) {
                foreach (BloxFruit::whereIn('id', $fruitIds)->get() as $f) {
                    $searchedItems[] = ['tipe' => 'Buah', 'nama' => $f->nama, 'rarity' => $f->rarity];
                }
            }
            if ($skinIds) {
                foreach (FruitSkin::with('fruit')->whereIn('id', $skinIds)->get() as $s) {
                    $searchedItems[] = ['tipe' => 'Skin', 'nama' => $s->nama_skin, 'rarity' => $s->fruit->rarity ?? '-'];
                }
            }
            if ($gpIds) {
                foreach (Gamepass::whereIn('id', $gpIds)->get() as $g) {
                    $searchedItems[] = ['tipe' => 'Gamepass', 'nama' => $g->nama, 'rarity' => '-'];
                }
            }
            if ($permIds) {
                foreach (PermanentFruitPrice::whereIn('id', $permIds)->get() as $p) {
                    $searchedItems[] = ['tipe' => 'Permanent', 'nama' => 'Perm ' . $p->nama, 'rarity' => '-'];
                }
            }

            $totalCriteria = count($fruitIds) + count($skinIds) + count($gpIds) + count($permIds);

            $query = StorageAccount::where('aktif', true);

            $query->withCount([
                'fruitStocks as matched_fruits' => function ($q) use ($fruitIds) {
                    if ($fruitIds) $q->whereIn('blox_fruit_id', $fruitIds)->where('jumlah', '>', 0);
                    else $q->whereRaw('0 = 1');
                },
                'skinStocks as matched_skins' => function ($q) use ($skinIds) {
                    if ($skinIds) $q->whereIn('fruit_skin_id', $skinIds)->where('jumlah', '>', 0);
                    else $q->whereRaw('0 = 1');
                },
                'gamepassStocks as matched_gp' => function ($q) use ($gpIds) {
                    if ($gpIds) $q->whereIn('gamepass_id', $gpIds)->where('jumlah', '>', 0);
                    else $q->whereRaw('0 = 1');
                },
                'permanentStocks as matched_perm' => function ($q) use ($permIds) {
                    if ($permIds) $q->whereIn('permanent_fruit_price_id', $permIds)->where('jumlah', '>', 0);
                    else $q->whereRaw('0 = 1');
                },
            ]);

            $query->with([
                'fruitStocks' => fn($q) => $fruitIds ? $q->whereIn('blox_fruit_id', $fruitIds)->where('jumlah', '>', 0)->with('fruit') : $q->whereRaw('0 = 1'),
                'skinStocks' => fn($q) => $skinIds ? $q->whereIn('fruit_skin_id', $skinIds)->where('jumlah', '>', 0)->with('skin') : $q->whereRaw('0 = 1'),
                'gamepassStocks' => fn($q) => $gpIds ? $q->whereIn('gamepass_id', $gpIds)->where('jumlah', '>', 0)->with('gamepass') : $q->whereRaw('0 = 1'),
                'permanentStocks' => fn($q) => $permIds ? $q->whereIn('permanent_fruit_price_id', $permIds)->where('jumlah', '>', 0)->with('permanentPrice') : $q->whereRaw('0 = 1'),
            ]);

            $allAccounts = $query->get();

            $allAccounts->each(function ($acc) {
                $acc->total_matched = $acc->matched_fruits + $acc->matched_skins + $acc->matched_gp + $acc->matched_perm;
            });

            if ($mode === 'semua') {
                $results = $allAccounts->filter(fn($a) => $a->total_matched >= $totalCriteria)->sortByDesc('total_matched')->values();
            } else {
                $results = $allAccounts->filter(fn($a) => $a->total_matched > 0)->sortByDesc('total_matched')->values();
            }
        }

        // ============================================================
        // CARI SLOT KOSONG — multi-tipe (fruits/skins/gamepasses/permanents)
        // ============================================================
        $emptyFruitIds = array_filter($request->get('empty_fruits', []), fn($v) => $v);
        $emptySkinIds = array_filter($request->get('empty_skins', []), fn($v) => $v);
        $emptyGpIds = array_filter($request->get('empty_gamepasses', []), fn($v) => $v);
        $emptyPermIds = array_filter($request->get('empty_permanents', []), fn($v) => $v);

        $hasEmptySearch = count($emptyFruitIds) || count($emptySkinIds) || count($emptyGpIds) || count($emptyPermIds);

        $searchedEmptyItems = [
            'fruits' => collect(),
            'skins' => collect(),
            'gamepasses' => collect(),
            'permanents' => collect(),
        ];

        if ($hasEmptySearch) {
            if ($emptyFruitIds) $searchedEmptyItems['fruits'] = BloxFruit::whereIn('id', $emptyFruitIds)->get();
            if ($emptySkinIds) $searchedEmptyItems['skins'] = FruitSkin::whereIn('id', $emptySkinIds)->get();
            if ($emptyGpIds) $searchedEmptyItems['gamepasses'] = Gamepass::whereIn('id', $emptyGpIds)->get();
            if ($emptyPermIds) $searchedEmptyItems['permanents'] = PermanentFruitPrice::whereIn('id', $emptyPermIds)->get();

            $accounts = StorageAccount::where('aktif', true)
                ->with([
                    'fruitStocks' => fn($q) => $emptyFruitIds ? $q->whereIn('blox_fruit_id', $emptyFruitIds) : $q->whereRaw('0=1'),
                    'skinStocks' => fn($q) => $emptySkinIds ? $q->whereIn('fruit_skin_id', $emptySkinIds) : $q->whereRaw('0=1'),
                    'gamepassStocks' => fn($q) => $emptyGpIds ? $q->whereIn('gamepass_id', $emptyGpIds) : $q->whereRaw('0=1'),
                    'permanentStocks' => fn($q) => $emptyPermIds ? $q->whereIn('permanent_fruit_price_id', $emptyPermIds) : $q->whereRaw('0=1'),
                ])->get();

            $emptyResults = $accounts->map(function ($acc) use (
                $emptyFruitIds, $emptySkinIds, $emptyGpIds, $emptyPermIds
            ) {
                $capacity = $acc->kapasitas_storage;
                $details = ['fruits' => [], 'skins' => [], 'gamepasses' => [], 'permanents' => []];
                $totalAvailable = 0;

                foreach ($emptyFruitIds as $id) {
                    $stock = $acc->fruitStocks->firstWhere('blox_fruit_id', $id);
                    $current = $stock ? $stock->jumlah : 0;
                    $available = max(0, $capacity - $current);
                    $details['fruits'][$id] = ['current' => $current, 'available' => $available];
                    $totalAvailable += $available;
                }
                foreach ($emptySkinIds as $id) {
                    $stock = $acc->skinStocks->firstWhere('fruit_skin_id', $id);
                    $current = $stock ? $stock->jumlah : 0;
                    $available = max(0, $capacity - $current);
                    $details['skins'][$id] = ['current' => $current, 'available' => $available];
                    $totalAvailable += $available;
                }
                foreach ($emptyGpIds as $id) {
                    $stock = $acc->gamepassStocks->firstWhere('gamepass_id', $id);
                    $current = $stock ? $stock->jumlah : 0;
                    $available = max(0, $capacity - $current);
                    $details['gamepasses'][$id] = ['current' => $current, 'available' => $available];
                    $totalAvailable += $available;
                }
                foreach ($emptyPermIds as $id) {
                    $stock = $acc->permanentStocks->firstWhere('permanent_fruit_price_id', $id);
                    $current = $stock ? $stock->jumlah : 0;
                    $available = max(0, $capacity - $current);
                    $details['permanents'][$id] = ['current' => $current, 'available' => $available];
                    $totalAvailable += $available;
                }

                return [
                    'akun' => $acc,
                    'capacity' => $capacity,
                    'details' => $details,
                    'total_available' => $totalAvailable,
                ];
            })->filter(fn($r) => $r['total_available'] > 0)
              ->sortByDesc('total_available')
              ->values();
        }

        return view('bloxfruit.search.index', compact(
            'allFruits', 'allSkins', 'allGamepasses', 'allPermanents',
            'results', 'emptyResults', 'searchedEmptyItems', 'searchedItems', 'hasSearch', 'hasEmptySearch', 'mode',
            'fruitIds', 'skinIds', 'gpIds', 'permIds',
            'emptyFruitIds', 'emptySkinIds', 'emptyGpIds', 'emptyPermIds'
        ));
    }
}
