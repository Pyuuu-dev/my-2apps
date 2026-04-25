<?php

namespace App\Http\Controllers\BloxFruit;

use App\Http\Controllers\Controller;
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
        return view('bloxfruit.permanents.index', compact('permanents'));
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
