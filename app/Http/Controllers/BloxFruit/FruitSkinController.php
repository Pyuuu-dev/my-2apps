<?php

namespace App\Http\Controllers\BloxFruit;

use App\Http\Controllers\Controller;
use App\Models\BloxFruit\FruitSkin;
use App\Models\BloxFruit\BloxFruit;
use App\Models\BloxFruit\Gamepass;
use App\Models\BloxFruit\PermanentFruitPrice;
use Illuminate\Http\Request;

class FruitSkinController extends Controller
{
    public function index(Request $request)
    {
        $query = FruitSkin::with('fruit')->withSum('stocks as total_stok', 'jumlah');

        if ($request->filled('cari')) {
            $query->where('nama_skin', 'like', '%' . $request->cari . '%');
        }
        if ($request->filled('fruit')) {
            $query->where('blox_fruit_id', $request->fruit);
        }

        $skins = $query->orderByDesc('fruit_skins.harga_jual')
            ->orderBy('fruit_skins.nama_skin')
            ->paginate(30)->withQueryString();

        $fruits = BloxFruit::orderByDesc('harga_jual')->orderBy('nama')->get();
        $totalStok = FruitSkin::withSum('stocks as total_stok', 'jumlah')->get()->sum('total_stok');

        $fruitsForCopy = BloxFruit::withSum('fruitStocks as stok', 'jumlah')->where('aktif', true)->orderByDesc('harga_jual')->get()->map(fn($f) => ['nama' => $f->nama, 'harga_jual' => $f->harga_jual, 'stok' => (int)($f->stok ?? 0)]);
        $skinsForCopy = FruitSkin::withSum('stocks as stok', 'jumlah')->where('aktif', true)->orderByDesc('harga_jual')->get()->map(fn($s) => ['nama' => $s->nama_skin, 'harga_jual' => $s->harga_jual, 'stok' => (int)($s->stok ?? 0)]);
        $gamepassesForCopy = Gamepass::where('aktif', true)->orderByDesc('harga_jual')->get()->map(fn($g) => ['nama' => $g->nama, 'harga_jual' => $g->harga_jual]);
        $permanentsForCopy = PermanentFruitPrice::where('aktif', true)->orderByDesc('harga_jual')->get()->map(fn($p) => ['nama' => $p->nama, 'harga_jual' => $p->harga_jual]);

        return view('bloxfruit.skins.index', compact('skins', 'fruits', 'totalStok', 'fruitsForCopy', 'skinsForCopy', 'gamepassesForCopy', 'permanentsForCopy'));
    }

    public function create()
    {
        $fruits = BloxFruit::orderByDesc('harga_jual')->orderBy('nama')->get();
        return view('bloxfruit.skins.form', compact('fruits'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'blox_fruit_id' => 'required|exists:blox_fruits,id',
            'nama_skin' => 'required|string|max:255',
            'harga_beli' => 'nullable|integer|min:0',
            'harga_jual' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        FruitSkin::create($validated);
        return redirect()->route('bloxfruit.skins.index')->with('sukses', 'Skin berhasil ditambahkan!');
    }

    public function edit(FruitSkin $skin)
    {
        $fruits = BloxFruit::orderByDesc('harga_jual')->orderBy('nama')->get();
        return view('bloxfruit.skins.form', compact('skin', 'fruits'));
    }

    public function update(Request $request, FruitSkin $skin)
    {
        $validated = $request->validate([
            'blox_fruit_id' => 'required|exists:blox_fruits,id',
            'nama_skin' => 'required|string|max:255',
            'harga_beli' => 'nullable|integer|min:0',
            'harga_jual' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $skin->update($validated);
        return redirect()->route('bloxfruit.skins.index')->with('sukses', 'Skin berhasil diperbarui!');
    }

    public function destroy(FruitSkin $skin)
    {
        $skin->delete();
        return redirect()->route('bloxfruit.skins.index')->with('sukses', 'Skin berhasil dihapus!');
    }
}
