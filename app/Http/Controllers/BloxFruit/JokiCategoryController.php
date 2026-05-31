<?php

namespace App\Http\Controllers\BloxFruit;

use App\Http\Controllers\Controller;
use App\Models\BloxFruit\JokiCategory;
use App\Models\BloxFruit\JokiService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class JokiCategoryController extends Controller
{
    /**
     * Key kategori yang tidak boleh dihapus / di-rename karena dipakai
     * sebagai fallback custom (lihat JokiOrderController::store()).
     */
    private const PROTECTED_KEYS = ['lainnya'];

    public function index()
    {
        $categories = JokiCategory::orderBy('urutan')->orderBy('id')->get();

        // Hitung jumlah jenis joki per kategori (untuk indikator di tabel).
        $counts = JokiService::query()
            ->selectRaw('kategori, COUNT(*) as total')
            ->groupBy('kategori')
            ->pluck('total', 'kategori');

        return view('bloxfruit.joki-categories.index', compact('categories', 'counts'));
    }

    public function create()
    {
        $nextUrutan = (int) (JokiCategory::max('urutan') ?? 0) + 1;
        return view('bloxfruit.joki-categories.form', compact('nextUrutan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label'  => 'required|string|max:100',
            'icon'   => 'nullable|string|max:10',
            'urutan' => 'nullable|integer|min:0|max:9999',
            'aktif'  => 'nullable|boolean',
        ]);

        $key = $this->generateUniqueKey($validated['label']);

        JokiCategory::create([
            'key'    => $key,
            'label'  => $validated['label'],
            'icon'   => $validated['icon'] ?? null,
            'urutan' => $validated['urutan'] ?? ((int) (JokiCategory::max('urutan') ?? 0) + 1),
            'aktif'  => $request->boolean('aktif', true),
        ]);

        return redirect()->route('bloxfruit.joki-categories.index')
            ->with('sukses', 'Kategori "' . $validated['label'] . '" berhasil ditambahkan!');
    }

    public function edit(JokiCategory $joki_category)
    {
        $category = $joki_category;
        $isProtected = in_array($category->key, self::PROTECTED_KEYS, true);
        $servicesCount = JokiService::where('kategori', $category->key)->count();

        return view('bloxfruit.joki-categories.form', compact('category', 'isProtected', 'servicesCount'));
    }

    public function update(Request $request, JokiCategory $joki_category)
    {
        $validated = $request->validate([
            'label'  => 'required|string|max:100',
            'icon'   => 'nullable|string|max:10',
            'urutan' => 'nullable|integer|min:0|max:9999',
            'aktif'  => 'nullable|boolean',
        ]);

        $isProtected = in_array($joki_category->key, self::PROTECTED_KEYS, true);

        $data = [
            'label'  => $validated['label'],
            'icon'   => $validated['icon'] ?? null,
            'urutan' => $validated['urutan'] ?? $joki_category->urutan,
            'aktif'  => $isProtected ? true : $request->boolean('aktif', true),
        ];

        // Kategori protected (lainnya) tidak boleh ganti key.
        // Kategori biasa: kalau label berubah, regenerate key dan update kolom
        // joki_services.kategori yang lama agar tetap konsisten.
        if (!$isProtected) {
            $newKey = $this->generateUniqueKey($validated['label'], $joki_category->id);
            if ($newKey !== $joki_category->key) {
                $oldKey = $joki_category->key;
                $data['key'] = $newKey;

                // Cascade rename ke joki_services supaya relasi tetap konsisten.
                JokiService::where('kategori', $oldKey)->update(['kategori' => $newKey]);
            }
        }

        $joki_category->update($data);

        return redirect()->route('bloxfruit.joki-categories.index')
            ->with('sukses', 'Kategori "' . $joki_category->label . '" berhasil diperbarui!');
    }

    public function destroy(JokiCategory $joki_category)
    {
        if (in_array($joki_category->key, self::PROTECTED_KEYS, true)) {
            return redirect()->route('bloxfruit.joki-categories.index')
                ->with('error', 'Kategori "' . $joki_category->label . '" tidak bisa dihapus (dipakai sebagai fallback sistem).');
        }

        $usedCount = JokiService::where('kategori', $joki_category->key)->count();
        if ($usedCount > 0) {
            return redirect()->route('bloxfruit.joki-categories.index')
                ->with('error', 'Kategori "' . $joki_category->label . '" masih dipakai oleh ' . $usedCount . ' jenis joki. Hapus / pindahkan jenis nya dulu.');
        }

        $nama = $joki_category->label;
        $joki_category->delete();

        return redirect()->route('bloxfruit.joki-categories.index')
            ->with('sukses', 'Kategori "' . $nama . '" berhasil dihapus!');
    }

    /**
     * Generate key unik dari label (snake_case ASCII).
     * Contoh: "Joki Level"  -> "joki_level"
     *         "Boss Raid"   -> "boss_raid"
     *         duplikat      -> "boss_raid_2"
     */
    private function generateUniqueKey(string $label, ?int $ignoreId = null): string
    {
        $base = Str::snake(Str::ascii($label));
        $base = preg_replace('/[^a-z0-9_]+/', '_', $base);
        $base = trim($base, '_');
        if ($base === '') {
            $base = 'kategori';
        }

        $key = $base;
        $i = 2;
        while (
            JokiCategory::where('key', $key)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $key = $base . '_' . $i++;
        }

        return $key;
    }
}
