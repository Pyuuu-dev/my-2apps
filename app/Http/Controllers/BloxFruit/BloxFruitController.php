<?php

namespace App\Http\Controllers\BloxFruit;

use App\Http\Controllers\Controller;
use App\Models\BloxFruit\BloxFruit;
use App\Models\BloxFruit\FruitSkin;
use App\Models\BloxFruit\Gamepass;
use App\Models\BloxFruit\PermanentFruitPrice;
use Illuminate\Http\Request;

class BloxFruitController extends Controller
{
    public function index(Request $request)
    {
        $query = BloxFruit::withSum('fruitStocks as total_stok', 'jumlah');

        if ($request->filled('cari')) {
            $query->where('nama', 'like', '%' . $request->cari . '%');
        }
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }
        if ($request->filled('rarity')) {
            $query->where('rarity', $request->rarity);
        }

        $fruits = $query->orderByRaw("CASE rarity WHEN 'Mythical' THEN 5 WHEN 'Legendary' THEN 4 WHEN 'Rare' THEN 3 WHEN 'Uncommon' THEN 2 WHEN 'Common' THEN 1 END DESC")
            ->orderByDesc('harga_jual')->paginate(50)->withQueryString();

        $totalStok = BloxFruit::withSum('fruitStocks as total_stok', 'jumlah')->get()->sum('total_stok');

        // Data for copy text feature
        $fruitsForCopy = BloxFruit::withSum('fruitStocks as stok', 'jumlah')
            ->where('aktif', true)->orderByDesc('harga_jual')
            ->get()->map(fn($f) => ['nama' => $f->nama, 'harga_jual' => $f->harga_jual, 'stok' => (int)($f->stok ?? 0)]);

        $skinsForCopy = FruitSkin::withSum('stocks as stok', 'jumlah')
            ->where('aktif', true)->orderByDesc('harga_jual')
            ->get()->map(fn($s) => ['nama' => $s->nama_skin, 'harga_jual' => $s->harga_jual, 'stok' => (int)($s->stok ?? 0)]);

        $gamepassesForCopy = Gamepass::where('aktif', true)->orderByDesc('harga_jual')
            ->get()->map(fn($g) => ['nama' => $g->nama, 'harga_jual' => $g->harga_jual]);

        $permanentsForCopy = PermanentFruitPrice::where('aktif', true)->orderByDesc('harga_jual')
            ->get()->map(fn($p) => ['nama' => $p->nama, 'harga_jual' => $p->harga_jual]);

        return view('bloxfruit.fruits.index', compact(
            'fruits', 'totalStok', 'fruitsForCopy', 'skinsForCopy', 'gamepassesForCopy', 'permanentsForCopy'
        ));
    }

    public function create()
    {
        return view('bloxfruit.fruits.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tipe' => 'required|in:Natural,Elemental,Beast',
            'rarity' => 'required|in:Common,Uncommon,Rare,Legendary,Mythical',
            'harga_beli' => 'nullable|integer|min:0',
            'harga_jual' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        BloxFruit::create($validated);
        return redirect()->route('bloxfruit.fruits.index')->with('sukses', 'Buah berhasil ditambahkan!');
    }

    public function edit(BloxFruit $fruit)
    {
        return view('bloxfruit.fruits.form', compact('fruit'));
    }

    public function update(Request $request, BloxFruit $fruit)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tipe' => 'required|in:Natural,Elemental,Beast',
            'rarity' => 'required|in:Common,Uncommon,Rare,Legendary,Mythical',
            'harga_beli' => 'nullable|integer|min:0',
            'harga_jual' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $fruit->update($validated);
        return redirect()->route('bloxfruit.fruits.index')->with('sukses', 'Buah berhasil diperbarui!');
    }

    public function destroy(BloxFruit $fruit)
    {
        $fruit->delete();
        return redirect()->route('bloxfruit.fruits.index')->with('sukses', 'Buah berhasil dihapus!');
    }
}
