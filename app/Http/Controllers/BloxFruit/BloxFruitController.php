<?php

namespace App\Http\Controllers\BloxFruit;

use App\Http\Controllers\Controller;
use App\Models\BloxFruit\BloxFruit;
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

        $fruits = $query->orderByDesc('harga_jual')->orderBy('nama')->paginate(50)->withQueryString();
        return view('bloxfruit.fruits.index', compact('fruits'));
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
