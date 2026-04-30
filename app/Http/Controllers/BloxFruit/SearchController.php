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
        $searchedItems = [];
        $mode = $request->get('mode', 'semua'); // semua = akun punya semua item, sebagian = punya minimal 1

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

            // Query akun storage yang punya item-item tersebut
            $query = StorageAccount::where('aktif', true);

            // Hitung berapa kriteria yang match per akun
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

            // Juga load detail stok yang match
            $query->with([
                'fruitStocks' => fn($q) => $fruitIds ? $q->whereIn('blox_fruit_id', $fruitIds)->where('jumlah', '>', 0)->with('fruit') : $q->whereRaw('0 = 1'),
                'skinStocks' => fn($q) => $skinIds ? $q->whereIn('fruit_skin_id', $skinIds)->where('jumlah', '>', 0)->with('skin') : $q->whereRaw('0 = 1'),
                'gamepassStocks' => fn($q) => $gpIds ? $q->whereIn('gamepass_id', $gpIds)->where('jumlah', '>', 0)->with('gamepass') : $q->whereRaw('0 = 1'),
                'permanentStocks' => fn($q) => $permIds ? $q->whereIn('permanent_fruit_price_id', $permIds)->where('jumlah', '>', 0)->with('permanentPrice') : $q->whereRaw('0 = 1'),
            ]);

            $allAccounts = $query->get();

            // Hitung total match per akun
            $allAccounts->each(function ($acc) {
                $acc->total_matched = $acc->matched_fruits + $acc->matched_skins + $acc->matched_gp + $acc->matched_perm;
            });

            if ($mode === 'semua') {
                // Hanya akun yang punya SEMUA item
                $results = $allAccounts->filter(fn($a) => $a->total_matched >= $totalCriteria)->sortByDesc('total_matched')->values();
            } else {
                // Akun yang punya minimal 1 item, urutkan dari yang paling banyak match
                $results = $allAccounts->filter(fn($a) => $a->total_matched > 0)->sortByDesc('total_matched')->values();
            }
        }

        return view('bloxfruit.search.index', compact(
            'allFruits', 'allSkins', 'allGamepasses', 'allPermanents',
            'results', 'searchedItems', 'hasSearch', 'mode',
            'fruitIds', 'skinIds', 'gpIds', 'permIds'
        ));
    }
}
