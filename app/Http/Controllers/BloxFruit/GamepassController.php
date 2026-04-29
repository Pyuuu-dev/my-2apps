<?php

namespace App\Http\Controllers\BloxFruit;

use App\Http\Controllers\Controller;
use App\Models\BloxFruit\BloxFruit;
use App\Models\BloxFruit\FruitSkin;
use App\Models\BloxFruit\Gamepass;
use App\Models\BloxFruit\PermanentFruitPrice;
use Illuminate\Http\Request;

class GamepassController extends Controller
{
    public function index(Request $request)
    {
        $query = Gamepass::withSum('stocks as total_stok', 'jumlah');

        if ($request->filled('cari')) {
            $query->where('nama', 'like', '%' . $request->cari . '%');
        }

        $gamepasses = $query->orderByDesc('harga_jual')->orderBy('nama')->paginate(50)->withQueryString();
        $totalStok = Gamepass::withSum('stocks as total_stok', 'jumlah')->get()->sum('total_stok');

        $fruitsForCopy = BloxFruit::withSum('fruitStocks as stok', 'jumlah')->where('aktif', true)->orderByDesc('harga_jual')->get()->map(fn($f) => ['nama' => $f->nama, 'harga_jual' => $f->harga_jual, 'stok' => (int)($f->stok ?? 0)]);
        $skinsForCopy = FruitSkin::withSum('stocks as stok', 'jumlah')->where('aktif', true)->orderByDesc('harga_jual')->get()->map(fn($s) => ['nama' => $s->nama_skin, 'harga_jual' => $s->harga_jual, 'stok' => (int)($s->stok ?? 0)]);
        $gamepassesForCopy = Gamepass::where('aktif', true)->orderByDesc('harga_jual')->get()->map(fn($g) => ['nama' => $g->nama, 'harga_jual' => $g->harga_jual]);
        $permanentsForCopy = PermanentFruitPrice::where('aktif', true)->orderByDesc('harga_jual')->get()->map(fn($p) => ['nama' => $p->nama, 'harga_jual' => $p->harga_jual]);

        return view('bloxfruit.gamepasses.index', compact('gamepasses', 'totalStok', 'fruitsForCopy', 'skinsForCopy', 'gamepassesForCopy', 'permanentsForCopy'));
    }

    public function create()
    {
        return view('bloxfruit.gamepasses.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'harga_robux' => 'nullable|integer|min:0',
            'harga_beli' => 'nullable|integer|min:0',
            'harga_jual' => 'nullable|integer|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        Gamepass::create($validated);
        return redirect()->route('bloxfruit.gamepasses.index')->with('sukses', 'Gamepass berhasil ditambahkan!');
    }

    public function edit(Gamepass $gamepass)
    {
        return view('bloxfruit.gamepasses.form', compact('gamepass'));
    }

    public function update(Request $request, Gamepass $gamepass)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'harga_robux' => 'nullable|integer|min:0',
            'harga_beli' => 'nullable|integer|min:0',
            'harga_jual' => 'nullable|integer|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        $gamepass->update($validated);
        return redirect()->route('bloxfruit.gamepasses.index')->with('sukses', 'Gamepass berhasil diperbarui!');
    }

    public function destroy(Gamepass $gamepass)
    {
        $gamepass->delete();
        return redirect()->route('bloxfruit.gamepasses.index')->with('sukses', 'Gamepass berhasil dihapus!');
    }
}
