<?php

namespace App\Http\Controllers\BloxFruit;

use App\Http\Controllers\Controller;
use App\Models\BloxFruit\FruitSkin;
use App\Models\BloxFruit\BloxFruit;
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
        return view('bloxfruit.skins.index', compact('skins', 'fruits'));
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
