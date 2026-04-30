<?php

namespace App\Http\Controllers\BloxFruit;

use App\Http\Controllers\Controller;
use App\Models\BloxFruit\ProfitRecord;
use App\Models\BloxFruit\WalletBalance;
use App\Models\BloxFruit\BloxFruit;
use App\Models\BloxFruit\FruitSkin;
use App\Models\BloxFruit\Gamepass;
use App\Models\BloxFruit\PermanentFruitPrice;
use App\Models\BloxFruit\FruitStock;
use App\Models\BloxFruit\SkinStock;
use App\Models\BloxFruit\GamepassStock;
use App\Models\BloxFruit\PermanentFruitStock;
use App\Models\BloxFruit\AccountStock;
use App\Models\BloxFruit\JokiOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProfitController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', now()->format('Y-m'));
        $startDate = Carbon::parse($bulan . '-01')->startOfMonth();
        $endDate = Carbon::parse($bulan . '-01')->endOfMonth();

        // Profit records bulan ini (grouped by kategori)
        $filterKategori = $request->get('kat');
        $recordsQuery = ProfitRecord::whereBetween('tanggal', [$startDate, $endDate]);
        if ($filterKategori) {
            $recordsQuery->where('kategori', $filterKategori);
        }
        $records = $recordsQuery->orderByDesc('tanggal')->orderByDesc('id')->paginate(20)->withQueryString();

        // All records for summary (no pagination)
        $allRecords = ProfitRecord::whereBetween('tanggal', [$startDate, $endDate])->get();

        // Summary per kategori
        $perKategori = ProfitRecord::whereBetween('tanggal', [$startDate, $endDate])
            ->selectRaw('kategori, SUM(modal) as total_modal, SUM(pendapatan) as total_pendapatan, SUM(keuntungan) as total_keuntungan, COUNT(*) as jumlah')
            ->groupBy('kategori')->get()->keyBy('kategori');

        // Summary per metode bayar
        $perMetode = ProfitRecord::whereBetween('tanggal', [$startDate, $endDate])
            ->whereNotNull('metode_bayar')->where('metode_bayar', '!=', '')
            ->selectRaw('metode_bayar, SUM(pendapatan) as total')
            ->groupBy('metode_bayar')->get()->keyBy('metode_bayar');

        // Total bulan ini
        $totalBulan = [
            'modal' => $allRecords->sum('modal'),
            'pendapatan' => $allRecords->sum('pendapatan'),
            'keuntungan' => $allRecords->sum('keuntungan'),
            'transaksi' => $allRecords->count(),
        ];

        // Wallet balance terbaru
        $wallet = WalletBalance::orderByDesc('tanggal')->orderByDesc('id')->first();

        // Daftar bulan yang ada data
        $bulanList = ProfitRecord::selectRaw("strftime('%Y-%m', tanggal) as bulan")
            ->groupBy('bulan')->orderByDesc('bulan')->pluck('bulan')->toArray();
        if (!in_array($bulan, $bulanList)) {
            array_unshift($bulanList, $bulan);
        }

        // Total nilai stok (harga jual x jumlah)
        $nilaiStok = $this->hitungNilaiStok();

        // Ringkasan Joki bulan ini (dari joki_orders)
        $jokiBulanIni = [
            'selesai' => JokiOrder::where('status', 'selesai')
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('tanggal_selesai', [$startDate, $endDate])
                      ->orWhere(function ($q2) use ($startDate, $endDate) {
                          $q2->whereNull('tanggal_selesai')->whereBetween('updated_at', [$startDate, $endDate]);
                      });
                })->get(),
            'proses' => JokiOrder::where('status', 'proses')->get(),
            'antrian' => JokiOrder::where('status', 'antrian')->get(),
            'total_selesai' => JokiOrder::where('status', 'selesai')
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('tanggal_selesai', [$startDate, $endDate])
                      ->orWhere(function ($q2) use ($startDate, $endDate) {
                          $q2->whereNull('tanggal_selesai')->whereBetween('updated_at', [$startDate, $endDate]);
                      });
                })->sum('harga'),
            'total_proses' => JokiOrder::where('status', 'proses')->sum('harga'),
            'total_antrian' => JokiOrder::where('status', 'antrian')->sum('harga'),
        ];

        // Pendapatan per bulan (6 bulan terakhir)
        $pendapatanPerBulan = ProfitRecord::selectRaw("strftime('%Y-%m', tanggal) as bulan_key, SUM(pendapatan) as total_pendapatan, SUM(keuntungan) as total_keuntungan, SUM(modal) as total_modal, COUNT(*) as jumlah")
            ->groupBy('bulan_key')
            ->orderByDesc('bulan_key')
            ->limit(6)
            ->get();

        $trashedCount = ProfitRecord::onlyTrashed()->count();

        return view('bloxfruit.profit.index', compact(
            'records', 'perKategori', 'perMetode', 'totalBulan', 'wallet', 'bulan', 'bulanList', 'nilaiStok', 'jokiBulanIni', 'pendapatanPerBulan', 'trashedCount', 'filterKategori'
        ));
    }

    public function create()
    {
        return view('bloxfruit.profit.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'kategori' => 'required|in:fruit,skin,gamepass,permanent,joki,akun,lainnya',
            'keterangan' => 'nullable|string|max:255',
            'modal' => 'nullable|integer|min:0',
            'pendapatan' => 'nullable|integer|min:0',
            'metode_bayar' => 'nullable|in:dana,gopay,shopeepay,seabank,bank_kalsel,bri,qris,cash',
        ]);

        $validated['keuntungan'] = ($validated['pendapatan'] ?? 0) - ($validated['modal'] ?? 0);

        ProfitRecord::create($validated);
        return redirect()->route('bloxfruit.profit.index')->with('sukses', 'Transaksi berhasil dicatat!');
    }

    public function edit(ProfitRecord $profit)
    {
        return view('bloxfruit.profit.form', compact('profit'));
    }

    public function update(Request $request, ProfitRecord $profit)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'kategori' => 'required|in:fruit,skin,gamepass,permanent,joki,akun,lainnya',
            'keterangan' => 'nullable|string|max:255',
            'modal' => 'nullable|integer|min:0',
            'pendapatan' => 'nullable|integer|min:0',
            'metode_bayar' => 'nullable|in:dana,gopay,shopeepay,seabank,bank_kalsel,bri,qris,cash',
        ]);

        $validated['keuntungan'] = ($validated['pendapatan'] ?? 0) - ($validated['modal'] ?? 0);

        $profit->update($validated);
        return redirect()->route('bloxfruit.profit.index')->with('sukses', 'Transaksi berhasil diperbarui!');
    }

    public function destroy(ProfitRecord $profit)
    {
        $profit->delete();
        return redirect()->route('bloxfruit.profit.index')->with('sukses', 'Transaksi dipindahkan ke sampah!');
    }

    /**
     * Tampilkan transaksi yang sudah dihapus (trash)
     */
    public function trashed()
    {
        $trashedRecords = ProfitRecord::onlyTrashed()->orderByDesc('deleted_at')->get();
        $totalTrashed = $trashedRecords->count();
        return view('bloxfruit.profit.trash', compact('trashedRecords', 'totalTrashed'));
    }

    /**
     * Restore transaksi yang sudah dihapus
     */
    public function restore(string $slug)
    {
        $profit = ProfitRecord::onlyTrashed()->where('slug', $slug)->firstOrFail();
        $profit->restore();
        return redirect()->route('bloxfruit.profit.trash')->with('sukses', 'Transaksi berhasil dikembalikan!');
    }

    /**
     * Restore semua transaksi yang sudah dihapus
     */
    public function restoreAll()
    {
        $count = ProfitRecord::onlyTrashed()->count();
        ProfitRecord::onlyTrashed()->restore();
        return redirect()->route('bloxfruit.profit.trash')->with('sukses', $count . ' transaksi berhasil dikembalikan!');
    }

    /**
     * Hapus permanen transaksi
     */
    public function forceDelete(string $slug)
    {
        $profit = ProfitRecord::onlyTrashed()->where('slug', $slug)->firstOrFail();
        $profit->forceDelete();
        return redirect()->route('bloxfruit.profit.trash')->with('sukses', 'Transaksi dihapus permanen!');
    }

    /**
     * Hitung total nilai semua stok berdasarkan harga jual
     */
    private function hitungNilaiStok(): array
    {
        // Fruit: join blox_fruits untuk harga_jual
        $fruit = DB::table('fruit_stocks')
            ->join('blox_fruits', 'blox_fruits.id', '=', 'fruit_stocks.blox_fruit_id')
            ->selectRaw('SUM(fruit_stocks.jumlah) as qty, SUM(fruit_stocks.jumlah * blox_fruits.harga_jual) as nilai')
            ->first();

        // Skin: join fruit_skins untuk harga_jual
        $skin = DB::table('skin_stocks')
            ->join('fruit_skins', 'fruit_skins.id', '=', 'skin_stocks.fruit_skin_id')
            ->selectRaw('SUM(skin_stocks.jumlah) as qty, SUM(skin_stocks.jumlah * fruit_skins.harga_jual) as nilai')
            ->first();

        // Gamepass: join gamepasses untuk harga_jual
        $gamepass = DB::table('gamepass_stocks')
            ->join('gamepasses', 'gamepasses.id', '=', 'gamepass_stocks.gamepass_id')
            ->selectRaw('SUM(gamepass_stocks.jumlah) as qty, SUM(gamepass_stocks.jumlah * gamepasses.harga_jual) as nilai')
            ->first();

        // Permanent: join permanent_fruit_prices untuk harga_jual
        $permanent = DB::table('permanent_fruit_stocks')
            ->join('permanent_fruit_prices', 'permanent_fruit_prices.id', '=', 'permanent_fruit_stocks.permanent_fruit_price_id')
            ->where('permanent_fruit_stocks.jumlah', '>', 0)
            ->selectRaw('SUM(permanent_fruit_stocks.jumlah) as qty, SUM(permanent_fruit_stocks.jumlah * permanent_fruit_prices.harga_jual) as nilai')
            ->first();

        // Akun jual tersedia
        $akun = AccountStock::where('status', 'tersedia')->selectRaw('COUNT(*) as qty, SUM(harga_beli) as nilai')->first();

        // Joki aktif
        $joki = JokiOrder::whereIn('status', ['antrian', 'proses'])->selectRaw('COUNT(*) as qty, SUM(harga) as nilai')->first();

        $items = [
            'fruit' => ['label' => 'Fruit', 'qty' => (int) ($fruit->qty ?? 0), 'nilai' => (int) ($fruit->nilai ?? 0), 'color' => 'indigo'],
            'skin' => ['label' => 'Skin', 'qty' => (int) ($skin->qty ?? 0), 'nilai' => (int) ($skin->nilai ?? 0), 'color' => 'pink'],
            'gamepass' => ['label' => 'Gamepass', 'qty' => (int) ($gamepass->qty ?? 0), 'nilai' => (int) ($gamepass->nilai ?? 0), 'color' => 'blue'],
            'permanent' => ['label' => 'Permanent', 'qty' => (int) ($permanent->qty ?? 0), 'nilai' => (int) ($permanent->nilai ?? 0), 'color' => 'amber'],
            'akun' => ['label' => 'Akun Jual', 'qty' => (int) ($akun->qty ?? 0), 'nilai' => (int) ($akun->nilai ?? 0), 'color' => 'teal'],
            'joki' => ['label' => 'Joki Aktif', 'qty' => (int) ($joki->qty ?? 0), 'nilai' => (int) ($joki->nilai ?? 0), 'color' => 'orange'],
        ];

        $totalNilai = collect($items)->sum('nilai');

        return ['items' => $items, 'total' => $totalNilai];
    }

    public function updateWallet(Request $request)
    {
        $walletFields = ['dana', 'gopay', 'shopeepay', 'seabank', 'bank_kalsel', 'bri', 'qris', 'cash'];

        $data = ['tanggal' => now()->toDateString(), 'catatan' => $request->input('catatan')];
        foreach ($walletFields as $field) {
            $data[$field] = (int) ($request->input($field, 0) ?: 0);
        }

        WalletBalance::create($data);
        return redirect()->route('bloxfruit.profit.index')->with('sukses', 'Saldo wallet berhasil diupdate!');
    }
}
