<?php

namespace App\Http\Controllers\BloxFruit;

use App\Http\Controllers\Controller;
use App\Models\BloxFruit\FruitStock;
use App\Models\BloxFruit\SkinStock;
use App\Models\BloxFruit\GamepassStock;
use App\Models\BloxFruit\PermanentFruitStock;
use App\Models\BloxFruit\ProfitRecord;
use Illuminate\Http\Request;

class QuickSellController extends Controller
{
    public function sell(Request $request)
    {
        $validated = $request->validate([
            'tipe' => 'required|in:fruit,skin,gamepass,permanent',
            'stock_id' => 'required|integer',
            'jumlah' => 'required|integer|min:1',
            'harga_jual' => 'required|integer|min:0',
            'harga_modal' => 'nullable|integer|min:0',
            'metode_bayar' => 'nullable|in:dana,gopay,shopeepay,seabank,bank_kalsel,bri,qris,cash',
        ]);

        $tipe = $validated['tipe'];
        $jumlahJual = $validated['jumlah'];
        $nama = '';
        $tab = 'buah';

        // Kurangi stok
        switch ($tipe) {
            case 'fruit':
                $stock = FruitStock::findOrFail($validated['stock_id']);
                if ($stock->jumlah < $jumlahJual) {
                    return redirect()->back()->with('error', 'Stok tidak cukup!');
                }
                $stock->jumlah -= $jumlahJual;
                $stock->jumlah > 0 ? $stock->save() : $stock->delete();
                $nama = $stock->fruit->nama ?? 'Fruit';
                $tab = 'buah';
                break;

            case 'skin':
                $stock = SkinStock::findOrFail($validated['stock_id']);
                if ($stock->jumlah < $jumlahJual) {
                    return redirect()->back()->with('error', 'Stok tidak cukup!');
                }
                $stock->jumlah -= $jumlahJual;
                $stock->jumlah > 0 ? $stock->save() : $stock->delete();
                $nama = $stock->skin->nama_skin ?? 'Skin';
                $tab = 'skin';
                break;

            case 'gamepass':
                $stock = GamepassStock::findOrFail($validated['stock_id']);
                if ($stock->jumlah < $jumlahJual) {
                    return redirect()->back()->with('error', 'Stok tidak cukup!');
                }
                $stock->jumlah -= $jumlahJual;
                $stock->jumlah > 0 ? $stock->save() : $stock->delete();
                $nama = $stock->gamepass->nama ?? 'Gamepass';
                $tab = 'gamepass';
                break;

            case 'permanent':
                $stock = PermanentFruitStock::findOrFail($validated['stock_id']);
                if ($stock->jumlah < $jumlahJual) {
                    return redirect()->back()->with('error', 'Stok tidak cukup!');
                }
                $stock->jumlah -= $jumlahJual;
                $stock->jumlah > 0 ? $stock->save() : $stock->delete();
                $nama = $stock->permanentPrice->nama ?? 'Permanent';
                $tab = 'permanent';
                break;
        }

        // Catat transaksi otomatis
        $modal = ($validated['harga_modal'] ?? 0) * $jumlahJual;
        $pendapatan = $validated['harga_jual'] * $jumlahJual;

        ProfitRecord::create([
            'tanggal' => now()->toDateString(),
            'kategori' => $tipe,
            'keterangan' => "Jual {$jumlahJual}x {$nama}",
            'modal' => $modal,
            'pendapatan' => $pendapatan,
            'keuntungan' => $pendapatan - $modal,
            'metode_bayar' => $validated['metode_bayar'],
        ]);

        $untung = number_format($pendapatan - $modal);
        return redirect()->back()->with('sukses', "Terjual! {$jumlahJual}x {$nama} - Untung Rp {$untung}");
    }
}
