<?php

namespace App\Http\Controllers\DietTracker;

use App\Http\Controllers\Controller;
use App\Models\DietTracker\FoodDatabase;
use Illuminate\Http\Request;

class FoodDbController extends Controller
{
    public function index(Request $request)
    {
        $query = FoodDatabase::query();

        if ($search = $request->get('q')) {
            $query->where('nama', 'like', "%{$search}%");
        }
        if ($kategori = $request->get('kategori')) {
            $query->where('kategori', $kategori);
        }

        $foods = $query->orderBy('nama')->paginate(25);
        $kategoris = FoodDatabase::distinct()->pluck('kategori')->filter()->sort();

        return view('diet.food-db.index', compact('foods', 'kategoris'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:100',
            'kalori' => 'required|integer|min:0',
            'protein' => 'required|numeric|min:0',
            'karbohidrat' => 'required|numeric|min:0',
            'lemak' => 'required|numeric|min:0',
            'satuan_porsi' => 'required|string|max:100',
            'berat_gram' => 'nullable|integer|min:0',
        ]);

        FoodDatabase::create($validated);
        return back()->with('sukses', "Makanan '{$validated['nama']}' ditambahkan.");
    }

    public function update(Request $request, FoodDatabase $food)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:100',
            'kalori' => 'required|integer|min:0',
            'protein' => 'required|numeric|min:0',
            'karbohidrat' => 'required|numeric|min:0',
            'lemak' => 'required|numeric|min:0',
            'satuan_porsi' => 'required|string|max:100',
            'berat_gram' => 'nullable|integer|min:0',
        ]);

        $food->update($validated);
        return back()->with('sukses', "Makanan '{$validated['nama']}' diupdate.");
    }

    public function destroy(FoodDatabase $food)
    {
        $nama = $food->nama;
        $food->delete();
        return back()->with('sukses', "Makanan '{$nama}' dihapus.");
    }
}
