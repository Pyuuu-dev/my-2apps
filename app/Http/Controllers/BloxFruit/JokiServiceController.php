<?php

namespace App\Http\Controllers\BloxFruit;

use App\Http\Controllers\Controller;
use App\Models\BloxFruit\JokiCategory;
use App\Models\BloxFruit\JokiService;
use Illuminate\Http\Request;

class JokiServiceController extends Controller
{
    public function index()
    {
        // Ambil kategori (semua, aktif maupun nonaktif) untuk referensi label & urutan.
        $categories = JokiCategory::orderBy('urutan')->orderBy('id')->get();

        $kategoriLabels = $categories->mapWithKeys(fn($c) => [
            $c->key => ['label' => $c->label, 'icon' => $c->icon ?: '📝'],
        ])->toArray();

        // Map key -> urutan untuk sorting di sisi PHP (lebih portable lintas DB
        // daripada orderByRaw CASE).
        $urutanMap = $categories->mapWithKeys(fn($c) => [$c->key => $c->urutan])->toArray();

        $services = JokiService::orderBy('harga')->get()
            ->sortBy(fn($s) => $urutanMap[$s->kategori] ?? 9999)
            ->groupBy('kategori');

        // Build data untuk fitur Copy Teks Promo (Alpine.js)
        // Hanya kategori yang punya item yang dimasukkan, urut sesuai $kategoriLabels.
        $jokiForCopy = [];
        foreach ($kategoriLabels as $katKey => $kat) {
            if (!isset($services[$katKey])) {
                continue;
            }
            $items = $services[$katKey]->map(fn($s) => [
                'nama'       => $s->nama,
                'harga'      => (int) $s->harga,
                'keterangan' => $s->keterangan,
            ])->values()->all();

            if (empty($items)) {
                continue;
            }

            $jokiForCopy[] = [
                'kategori' => $katKey,
                'label'    => $kat['label'],
                'icon'     => $kat['icon'],
                'items'    => $items,
            ];
        }

        return view('bloxfruit.joki-services.index', compact('services', 'kategoriLabels', 'jokiForCopy'));
    }

    public function create()
    {
        $kategoriOptions = JokiCategory::selectOptions(true);
        return view('bloxfruit.joki-services.form', compact('kategoriOptions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori'   => 'required|string|max:50|exists:joki_categories,key',
            'nama'       => 'required|string|max:255',
            'harga'      => 'required|integer|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        JokiService::create($validated);
        return redirect()->route('bloxfruit.joki-services.index')->with('sukses', 'Jenis joki "' . $validated['nama'] . '" berhasil ditambahkan!');
    }

    public function edit(JokiService $joki_service)
    {
        // Untuk edit, sertakan semua kategori (termasuk nonaktif) supaya kategori
        // existing yang kebetulan dinonaktifkan tetap kelihatan di dropdown.
        $kategoriOptions = JokiCategory::selectOptions(false);
        $service = $joki_service;
        return view('bloxfruit.joki-services.form', compact('service', 'kategoriOptions'));
    }

    public function update(Request $request, JokiService $joki_service)
    {
        $validated = $request->validate([
            'kategori'   => 'required|string|max:50|exists:joki_categories,key',
            'nama'       => 'required|string|max:255',
            'harga'      => 'required|integer|min:0',
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
