<?php

namespace App\Http\Controllers\BloxFruit;

use App\Http\Controllers\Controller;
use App\Models\BloxFruit\JokiService;
use Illuminate\Http\Request;

class JokiServiceController extends Controller
{
    public function index()
    {
        $services = JokiService::orderByRaw("CASE kategori
            WHEN 'level' THEN 1 WHEN 'belly_fragment' THEN 2 WHEN 'mastery' THEN 3
            WHEN 'fighting_style' THEN 4 WHEN 'sword' THEN 5 WHEN 'gun' THEN 6
            WHEN 'race' THEN 7 WHEN 'boss_raid' THEN 8 WHEN 'haki' THEN 9
            WHEN 'instinct' THEN 10 WHEN 'awaken' THEN 11 WHEN 'material' THEN 12
            ELSE 99 END")->orderBy('harga')->get()->groupBy('kategori');

        $kategoriLabels = [
            'level' => ['label' => 'Joki Level', 'icon' => '⚔️'],
            'belly_fragment' => ['label' => 'Belly & Fragment', 'icon' => '💰'],
            'mastery' => ['label' => 'Mastery', 'icon' => '🔥'],
            'fighting_style' => ['label' => 'Fighting Style V2', 'icon' => '🥋'],
            'sword' => ['label' => 'Get Sword', 'icon' => '🗡️'],
            'gun' => ['label' => 'Get Gun', 'icon' => '🔫'],
            'race' => ['label' => 'Up & Get Race', 'icon' => '🧬'],
            'boss_raid' => ['label' => 'Boss Raid', 'icon' => '👹'],
            'haki' => ['label' => 'Haki Legendary', 'icon' => '✨'],
            'instinct' => ['label' => 'Instinct', 'icon' => '👁️'],
            'awaken' => ['label' => 'Awaken Fruit', 'icon' => '🍎'],
            'material' => ['label' => 'Material', 'icon' => '📦'],
            'lainnya' => ['label' => 'Lainnya', 'icon' => '📝'],
        ];

        return view('bloxfruit.joki-services.index', compact('services', 'kategoriLabels'));
    }

    public function create()
    {
        $kategoriOptions = [
            'level' => 'Joki Level', 'belly_fragment' => 'Belly & Fragment', 'mastery' => 'Mastery',
            'fighting_style' => 'Fighting Style V2', 'sword' => 'Get Sword', 'gun' => 'Get Gun',
            'race' => 'Up & Get Race', 'boss_raid' => 'Boss Raid', 'haki' => 'Haki Legendary',
            'instinct' => 'Instinct', 'awaken' => 'Awaken Fruit',             'material' => 'Material',
            'lainnya' => 'Lainnya',
        ];
        return view('bloxfruit.joki-services.form', compact('kategoriOptions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori' => 'required|string|max:50',
            'nama' => 'required|string|max:255',
            'harga' => 'required|integer|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        JokiService::create($validated);
        return redirect()->route('bloxfruit.joki-services.index')->with('sukses', 'Jenis joki "' . $validated['nama'] . '" berhasil ditambahkan!');
    }

    public function edit(JokiService $joki_service)
    {
        $kategoriOptions = [
            'level' => 'Joki Level', 'belly_fragment' => 'Belly & Fragment', 'mastery' => 'Mastery',
            'fighting_style' => 'Fighting Style V2', 'sword' => 'Get Sword', 'gun' => 'Get Gun',
            'race' => 'Up & Get Race', 'boss_raid' => 'Boss Raid', 'haki' => 'Haki Legendary',
            'instinct' => 'Instinct', 'awaken' => 'Awaken Fruit',             'material' => 'Material',
            'lainnya' => 'Lainnya',
        ];
        $service = $joki_service;
        return view('bloxfruit.joki-services.form', compact('service', 'kategoriOptions'));
    }

    public function update(Request $request, JokiService $joki_service)
    {
        $validated = $request->validate([
            'kategori' => 'required|string|max:50',
            'nama' => 'required|string|max:255',
            'harga' => 'required|integer|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $joki_service->update($validated);
        return redirect()->route('bloxfruit.joki-services.index')->with('sukses', 'Jenis joki berhasil diperbarui!');
    }

    public function destroy(JokiService $joki_service)
    {
        $nama = $joki_service->nama;
        $joki_service->delete();
        return redirect()->route('bloxfruit.joki-services.index')->with('sukses', 'Jenis joki "' . $nama . '" berhasil dihapus!');
    }
}
