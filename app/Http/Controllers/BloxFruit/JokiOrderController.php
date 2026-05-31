<?php

namespace App\Http\Controllers\BloxFruit;

use App\Http\Controllers\Controller;
use App\Models\BloxFruit\JokiCategory;
use App\Models\BloxFruit\JokiOrder;
use App\Models\BloxFruit\JokiService;
use App\Models\BloxFruit\ProfitRecord;
use Illuminate\Http\Request;

class JokiOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = JokiOrder::query();

        if ($request->filled('cari')) {
            $query->where('nama_pelanggan', 'like', '%' . $request->cari . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('kategori')) {
            $query->where('jenis_joki', 'like', $request->kategori . ':%');
        }

        $orders = $query->orderByRaw("CASE status WHEN 'proses' THEN 1 WHEN 'antrian' THEN 2 WHEN 'selesai' THEN 3 WHEN 'batal' THEN 4 END")
            ->orderByDesc('id')->paginate(20)->withQueryString();

        // Joki services grouped by kategori
        $servicesByKategori = JokiService::where('aktif', true)->orderBy('harga')->get()->groupBy('kategori');

        $kategoriLabels = $this->getKategoriLabels();

        return view('bloxfruit.joki.index', compact('orders', 'servicesByKategori', 'kategoriLabels'));
    }

    public function create()
    {
        $servicesByKategori = JokiService::where('aktif', true)->orderBy('harga')->get()->groupBy('kategori');
        $kategoriLabels = $this->getKategoriLabels();
        return view('bloxfruit.joki.form', compact('servicesByKategori', 'kategoriLabels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'kontak' => 'nullable|string|max:255',
            'username_roblox' => 'nullable|string|max:255',
            'password_roblox' => 'nullable|string|max:255',
            'jenis_joki' => 'required|string|max:255',
            'detail_pesanan' => 'nullable|string',
            'harga' => 'nullable|integer|min:0',
            'status' => 'required|in:antrian,proses,selesai,batal',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'catatan' => 'nullable|string',
        ]);

        // Handle custom jenis dari input "Lainnya"
        if ($request->filled('jenis_joki_custom') && str_starts_with($validated['jenis_joki'], 'lainnya:')) {
            $validated['jenis_joki'] = 'lainnya:' . $request->input('jenis_joki_custom');
        }

        if (empty($validated['tanggal_mulai'])) {
            $validated['tanggal_mulai'] = now()->toDateString();
        }

        $order = JokiOrder::create($validated);

        // Otomatis catat ke keuangan jika langsung selesai
        if ($validated['status'] === 'selesai') {
            $parts = explode(':', $order->jenis_joki, 2);
            $jenisNama = $parts[1] ?? $order->jenis_joki;

            ProfitRecord::create([
                'tanggal' => $order->tanggal_selesai ?? now()->toDateString(),
                'kategori' => 'joki',
                'keterangan' => 'Joki ' . $order->nama_pelanggan . ' - ' . $jenisNama,
                'modal' => 0,
                'pendapatan' => $order->harga,
                'keuntungan' => $order->harga,
                'joki_order_id' => $order->id,
            ]);

            if (!$order->tanggal_selesai) {
                $order->update(['tanggal_selesai' => now()->toDateString()]);
            }
        }

        return redirect()->route('bloxfruit.joki.index')->with('sukses', 'Order joki berhasil ditambahkan!');
    }

    public function edit(JokiOrder $joki)
    {
        $servicesByKategori = JokiService::where('aktif', true)->orderBy('harga')->get()->groupBy('kategori');
        $kategoriLabels = $this->getKategoriLabels();
        return view('bloxfruit.joki.form', compact('joki', 'servicesByKategori', 'kategoriLabels'));
    }

    public function update(Request $request, JokiOrder $joki)
    {
        $validated = $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'kontak' => 'nullable|string|max:255',
            'username_roblox' => 'nullable|string|max:255',
            'password_roblox' => 'nullable|string|max:255',
            'jenis_joki' => 'required|string|max:255',
            'detail_pesanan' => 'nullable|string',
            'harga' => 'nullable|integer|min:0',
            'status' => 'required|in:antrian,proses,selesai,batal',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'catatan' => 'nullable|string',
        ]);

        $statusLama = $joki->getOriginal('status');
        $joki->update($validated);

        // Otomatis catat ke keuangan saat status berubah ke "selesai"
        if ($validated['status'] === 'selesai' && $statusLama !== 'selesai') {
            $parts = explode(':', $joki->jenis_joki, 2);
            $jenisNama = $parts[1] ?? $joki->jenis_joki;

            if (!ProfitRecord::where('joki_order_id', $joki->id)->exists()) {
                ProfitRecord::create([
                    'tanggal' => $joki->tanggal_selesai ?? now()->toDateString(),
                    'kategori' => 'joki',
                    'keterangan' => 'Joki ' . $joki->nama_pelanggan . ' - ' . $jenisNama,
                    'modal' => 0,
                    'pendapatan' => $joki->harga,
                    'keuntungan' => $joki->harga,
                    'joki_order_id' => $joki->id,
                ]);
            }

            if (!$joki->tanggal_selesai) {
                $joki->update(['tanggal_selesai' => now()->toDateString()]);
            }
        }

        // Hapus profit record saat status berubah dari selesai ke status lain
        if ($statusLama === 'selesai' && $validated['status'] !== 'selesai') {
            ProfitRecord::where('joki_order_id', $joki->id)->forceDelete();
        }

        return redirect()->route('bloxfruit.joki.index')->with('sukses', 'Order joki berhasil diperbarui!');
    }

    public function toggleStatus(Request $request, JokiOrder $joki)
    {
        $request->validate([
            'status' => 'required|in:antrian,proses,selesai,batal',
        ]);

        $statusLama = $joki->status;
        $statusBaru = $request->status;
        $joki->update(['status' => $statusBaru]);

        // Auto-record profit saat status berubah ke selesai
        if ($statusBaru === 'selesai' && $statusLama !== 'selesai') {
            $parts = explode(':', $joki->jenis_joki, 2);
            $jenisNama = $parts[1] ?? $joki->jenis_joki;

            if (!ProfitRecord::where('joki_order_id', $joki->id)->exists()) {
                ProfitRecord::create([
                    'tanggal' => now()->toDateString(),
                    'kategori' => 'joki',
                    'keterangan' => 'Joki ' . $joki->nama_pelanggan . ' - ' . $jenisNama,
                    'modal' => 0,
                    'pendapatan' => $joki->harga,
                    'keuntungan' => $joki->harga,
                    'joki_order_id' => $joki->id,
                ]);
            }

            if (!$joki->tanggal_selesai) {
                $joki->update(['tanggal_selesai' => now()->toDateString()]);
            }
        }

        // Hapus profit record saat status berubah dari selesai ke status lain
        if ($statusLama === 'selesai' && $statusBaru !== 'selesai') {
            ProfitRecord::where('joki_order_id', $joki->id)->forceDelete();
        }

        return redirect()->route('bloxfruit.joki.index', request()->only('cari', 'kategori'))
            ->with('sukses', 'Status ' . $joki->nama_pelanggan . ' diubah ke ' . ucfirst($statusBaru));
    }

    public function destroy(JokiOrder $joki)
    {
        $joki->delete();
        return redirect()->route('bloxfruit.joki.index')->with('sukses', 'Order joki berhasil dihapus!');
    }

    private function getKategoriLabels(): array
    {
        // Default colors per key. Untuk kategori baru yang belum ada di map ini
        // dipakai 'gray' supaya tetap valid. Color tidak disimpan di DB karena
        // dipilih untuk tetap minimal di tahap ini (sesuai requirement).
        $colorMap = [
            'level' => 'indigo', 'belly_fragment' => 'amber', 'mastery' => 'red',
            'fighting_style' => 'purple', 'sword' => 'blue', 'gun' => 'gray',
            'race' => 'teal', 'boss_raid' => 'orange', 'haki' => 'yellow',
            'instinct' => 'cyan', 'awaken' => 'pink', 'material' => 'emerald',
            'lainnya' => 'gray',
        ];

        return JokiCategory::query()
            ->orderBy('urutan')->orderBy('id')
            ->get()
            ->mapWithKeys(fn($c) => [
                $c->key => [
                    'label' => $c->label,
                    'icon'  => $c->icon ?: '📝',
                    'color' => $colorMap[$c->key] ?? 'gray',
                ],
            ])
            ->toArray();
    }
}
