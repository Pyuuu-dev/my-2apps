<?php

namespace App\Http\Controllers\BloxFruit;

use App\Http\Controllers\Controller;
use App\Models\BloxFruit\StorageAccount;
use App\Models\BloxFruit\BloxFruit;
use App\Models\BloxFruit\FruitSkin;
use App\Models\BloxFruit\Gamepass;
use App\Models\BloxFruit\FruitStock;
use App\Models\BloxFruit\SkinStock;
use App\Models\BloxFruit\GamepassStock;
use App\Models\BloxFruit\PermanentFruitStock;
use App\Models\BloxFruit\PermanentFruitPrice;
use Illuminate\Http\Request;

class StorageAccountController extends Controller
{
    public function index(Request $request)
    {
        $query = StorageAccount::withCount(['fruitStocks', 'skinStocks', 'gamepassStocks', 'permanentStocks']);

        if ($request->filled('cari')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_akun', 'like', '%' . $request->cari . '%')
                  ->orWhere('username', 'like', '%' . $request->cari . '%');
            });
        }

        $accounts = $query->orderBy('nama_akun')->orderBy('username')->get();

        // Group by browser (nama_akun)
        $grouped = $accounts->groupBy('nama_akun');

        return view('bloxfruit.storage.index', compact('grouped'));
    }

    public function create()
    {
        return view('bloxfruit.storage.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_akun' => 'required|string|max:255',
            'username' => 'nullable|string|max:255',
            'catatan' => 'nullable|string',
            'kapasitas_storage' => 'nullable|integer|min:1|max:99',
        ]);

        $account = StorageAccount::create($validated);
        return redirect()->route('bloxfruit.storage.show', $account)->with('sukses', 'Akun storage berhasil ditambahkan!');
    }

    public function show(StorageAccount $storage, Request $request)
    {
        $tab = $request->get('tab', 'buah');

        $allFruits = BloxFruit::orderByRarity()->orderByDesc('harga_jual')->orderBy('nama')->get();
        $allSkins = FruitSkin::with('fruit')->orderByDesc('harga_jual')->orderBy('nama_skin')->get();
        $allGamepasses = Gamepass::orderByDesc('harga_jual')->orderBy('nama')->get();
        $allPermanents = PermanentFruitPrice::orderByDesc('harga_jual')->get();

        $fruitStocks = $storage->fruitStocks()->pluck('jumlah', 'blox_fruit_id')->toArray();
        $fruitStockIds = $storage->fruitStocks()->pluck('id', 'blox_fruit_id')->toArray();
        $skinStocks = $storage->skinStocks()->pluck('jumlah', 'fruit_skin_id')->toArray();
        $skinStockIds = $storage->skinStocks()->pluck('id', 'fruit_skin_id')->toArray();
        $gamepassStocks = $storage->gamepassStocks()->pluck('jumlah', 'gamepass_id')->toArray();
        $gamepassStockIds = $storage->gamepassStocks()->pluck('id', 'gamepass_id')->toArray();
        $permanentStocks = $storage->permanentStocks->keyBy('permanent_fruit_price_id');

        return view('bloxfruit.storage.show', compact(
            'storage', 'tab',
            'allFruits', 'allSkins', 'allGamepasses', 'allPermanents',
            'fruitStocks', 'fruitStockIds', 'skinStocks', 'skinStockIds',
            'gamepassStocks', 'gamepassStockIds', 'permanentStocks'
        ));
    }

    public function edit(StorageAccount $storage)
    {
        return view('bloxfruit.storage.form', compact('storage'));
    }

    public function update(Request $request, StorageAccount $storage)
    {
        $validated = $request->validate([
            'nama_akun' => 'required|string|max:255',
            'username' => 'nullable|string|max:255',
            'catatan' => 'nullable|string',
            'aktif' => 'boolean',
            'kapasitas_storage' => 'nullable|integer|min:1|max:99',
        ]);
        $validated['aktif'] = $request->has('aktif');
        $storage->update($validated);
        return redirect()->route('bloxfruit.storage.show', $storage)->with('sukses', 'Akun berhasil diperbarui!');
    }

    public function destroy(StorageAccount $storage)
    {
        $storage->delete();
        return redirect()->route('bloxfruit.storage.index')->with('sukses', 'Akun berhasil dihapus!');
    }

    /**
     * Kosongkan semua stok dari 1 akun storage
     */
    public function clearStocks(StorageAccount $storage)
    {
        $storage->fruitStocks()->delete();
        $storage->skinStocks()->delete();
        $storage->gamepassStocks()->delete();
        $storage->permanentStocks()->delete();

        return redirect()->route('bloxfruit.storage.show', $storage)->with('sukses', 'Semua stok berhasil dikosongkan!');
    }

    /**
     * Kosongkan semua stok dari SEMUA akun storage
     */
    public function clearAllStocks()
    {
        FruitStock::truncate();
        SkinStock::truncate();
        GamepassStock::truncate();
        PermanentFruitStock::truncate();

        return redirect()->route('bloxfruit.storage.index')->with('sukses', 'Semua stok dari semua akun berhasil dikosongkan!');
    }

    // === BULK SAVE ===

    public function bulkSaveFruitStock(Request $request, StorageAccount $storage)
    {
        $items = $request->input('fruits', []);
        $count = 0;
        foreach ($items as $id => $jumlah) {
            $jumlah = (int) $jumlah;
            if ($jumlah > 0) {
                FruitStock::updateOrCreate(
                    ['storage_account_id' => $storage->id, 'blox_fruit_id' => $id],
                    ['jumlah' => $jumlah]
                );
                $count++;
            } else {
                FruitStock::where('storage_account_id', $storage->id)->where('blox_fruit_id', $id)->delete();
            }
        }
        return redirect()->route('bloxfruit.storage.show', ['storage' => $storage, 'tab' => 'buah'])
            ->with('sukses', "Stok buah disimpan! ({$count} item)");
    }

    public function bulkSaveSkinStock(Request $request, StorageAccount $storage)
    {
        $items = $request->input('skins', []);
        $count = 0;
        foreach ($items as $id => $jumlah) {
            $jumlah = (int) $jumlah;
            if ($jumlah > 0) {
                SkinStock::updateOrCreate(
                    ['storage_account_id' => $storage->id, 'fruit_skin_id' => $id],
                    ['jumlah' => $jumlah]
                );
                $count++;
            } else {
                SkinStock::where('storage_account_id', $storage->id)->where('fruit_skin_id', $id)->delete();
            }
        }
        return redirect()->route('bloxfruit.storage.show', ['storage' => $storage, 'tab' => 'skin'])
            ->with('sukses', "Stok skin disimpan! ({$count} item)");
    }

    public function bulkSaveGamepassStock(Request $request, StorageAccount $storage)
    {
        $items = $request->input('gamepasses', []);
        $count = 0;
        foreach ($items as $id => $jumlah) {
            $jumlah = (int) $jumlah;
            if ($jumlah > 0) {
                GamepassStock::updateOrCreate(
                    ['storage_account_id' => $storage->id, 'gamepass_id' => $id],
                    ['jumlah' => $jumlah]
                );
                $count++;
            } else {
                GamepassStock::where('storage_account_id', $storage->id)->where('gamepass_id', $id)->delete();
            }
        }
        return redirect()->route('bloxfruit.storage.show', ['storage' => $storage, 'tab' => 'gamepass'])
            ->with('sukses', "Stok gamepass disimpan! ({$count} item)");
    }

    public function bulkSavePermanentStock(Request $request, StorageAccount $storage)
    {
        $items = $request->input('permanents', []);
        $count = 0;
        foreach ($items as $priceId => $data) {
            $jumlah = (int) ($data['jumlah'] ?? 0);
            if ($jumlah > 0) {
                PermanentFruitStock::updateOrCreate(
                    ['storage_account_id' => $storage->id, 'permanent_fruit_price_id' => $priceId],
                    ['jumlah' => $jumlah, 'harga_robux' => (int) ($data['harga_robux'] ?? 0), 'harga_idr' => (int) ($data['harga_idr'] ?? 0)]
                );
                $count++;
            } else {
                PermanentFruitStock::where('storage_account_id', $storage->id)->where('permanent_fruit_price_id', $priceId)->delete();
            }
        }
        return redirect()->route('bloxfruit.storage.show', ['storage' => $storage, 'tab' => 'permanent'])
            ->with('sukses', "Stok permanent disimpan! ({$count} item)");
    }
}
