<?php

namespace App\Http\Controllers\BloxFruit;

use App\Http\Controllers\Controller;
use App\Models\BloxFruit\BloxFruit;
use App\Models\BloxFruit\FruitSkin;
use App\Models\BloxFruit\Gamepass;
use App\Models\BloxFruit\PermanentFruitPrice;
use Illuminate\Http\Request;

class PermanentFruitPriceController extends Controller
{
    public function index(Request $request)
    {
        $query = PermanentFruitPrice::query();

        if ($request->filled('cari')) {
            $query->where('nama', 'like', '%' . $request->cari . '%');
        }

        $permanents = $query->withSum('stocks as total_stok', 'jumlah')
            ->orderByDesc('harga_jual')->paginate(50)->withQueryString();
        $totalStok = PermanentFruitPrice::withSum('stocks as total_stok', 'jumlah')->get()->sum('total_stok');

        $fruitsForCopy = BloxFruit::withSum('fruitStocks as stok', 'jumlah')->where('aktif', true)->orderByDesc('harga_jual')->get()->map(fn($f) => ['nama' => $f->nama, 'harga_jual' => $f->harga_jual, 'stok' => (int)($f->stok ?? 0)]);
        $skinsForCopy = FruitSkin::withSum('stocks as stok', 'jumlah')->where('aktif', true)->orderByDesc('harga_jual')->get()->map(fn($s) => ['nama' => $s->nama_skin, 'harga_jual' => $s->harga_jual, 'stok' => (int)($s->stok ?? 0)]);
        $gamepassesForCopy = Gamepass::where('aktif', true)->orderByDesc('harga_jual')->get()->map(fn($g) => ['nama' => $g->nama, 'harga_jual' => $g->harga_jual]);
        $permanentsForCopy = PermanentFruitPrice::where('aktif', true)->orderByDesc('harga_jual')->get()->map(fn($p) => ['nama' => $p->nama, 'harga_jual' => $p->harga_jual]);

        return view('bloxfruit.permanents.index', compact('permanents', 'totalStok', 'fruitsForCopy', 'skinsForCopy', 'gamepassesForCopy', 'permanentsForCopy'));
    }

    public function create()
    {
        return view('bloxfruit.permanents.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'harga_robux' => 'nullable|integer|min:0',
            'harga_beli' => 'nullable|integer|min:0',
            'harga_jual' => 'nullable|integer|min:0',
        ]);

        PermanentFruitPrice::create($validated);
        return redirect()->route('bloxfruit.permanents.index')->with('sukses', 'Permanent fruit berhasil ditambahkan!');
    }

    public function edit(PermanentFruitPrice $permanent)
    {
        return view('bloxfruit.permanents.form', compact('permanent'));
    }

    public function update(Request $request, PermanentFruitPrice $permanent)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'harga_robux' => 'nullable|integer|min:0',
            'harga_beli' => 'nullable|integer|min:0',
            'harga_jual' => 'nullable|integer|min:0',
        ]);

        $permanent->update($validated);
        return redirect()->route('bloxfruit.permanents.index')->with('sukses', 'Permanent fruit berhasil diperbarui!');
    }

    public function destroy(PermanentFruitPrice $permanent)
    {
        $permanent->delete();
        return redirect()->route('bloxfruit.permanents.index')->with('sukses', 'Permanent fruit berhasil dihapus!');
    }
}
