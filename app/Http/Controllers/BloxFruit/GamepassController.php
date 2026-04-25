<?php

namespace App\Http\Controllers\BloxFruit;

use App\Http\Controllers\Controller;
use App\Models\BloxFruit\Gamepass;
use Illuminate\Http\Request;

class GamepassController extends Controller
{
    public function index(Request $request)
    {
        $query = Gamepass::withSum('stocks as total_stok', 'jumlah');

        if ($request->filled('cari')) {
            $query->where('nama', 'like', '%' . $request->cari . '%');
        }

        $gamepasses = $query->orderByDesc('harga_jual')->orderBy('nama')->paginate(15)->withQueryString();
        return view('bloxfruit.gamepasses.index', compact('gamepasses'));
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
