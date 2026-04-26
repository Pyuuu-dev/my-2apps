<?php

namespace App\Http\Controllers\BloxFruit;

use App\Http\Controllers\Controller;
use App\Models\BloxFruit\AccountStock;
use App\Models\BloxFruit\ProfitRecord;
use Illuminate\Http\Request;

class AccountStockController extends Controller
{
    public function index(Request $request)
    {
        $query = AccountStock::query();

        if ($request->filled('cari')) {
            $s = '%' . $request->cari . '%';
            $query->where(function ($q) use ($s) {
                $q->where('username_roblox', 'like', $s)
                  ->orWhere('sword_gun', 'like', $s)
                  ->orWhere('fruit', 'like', $s)
                  ->orWhere('race', 'like', $s)
                  ->orWhere('judul', 'like', $s);
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $accounts = $query->orderByDesc('id')->paginate(20)->withQueryString();

        // Stats
        $stats = [
            'total' => AccountStock::count(),
            'tersedia' => AccountStock::where('status', 'tersedia')->count(),
            'terjual' => AccountStock::where('status', 'terjual')->count(),
            'pending' => AccountStock::where('status', 'pending')->count(),
            'total_modal' => AccountStock::sum('harga_beli'),
            'total_jual' => AccountStock::where('status', 'terjual')->sum('harga_jual'),
        ];

        return view('bloxfruit.accounts.index', compact('accounts', 'stats'));
    }

    public function create()
    {
        return view('bloxfruit.accounts.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username_roblox' => 'required|string|max:255',
            'password_roblox' => 'nullable|string|max:255',
            'sword_gun' => 'nullable|string|max:500',
            'fruit' => 'nullable|string|max:500',
            'belly' => 'nullable|string|max:100',
            'fragment' => 'nullable|string|max:100',
            'race' => 'nullable|string|max:100',
            'level' => 'nullable|string|max:100',
            'harga_beli' => 'nullable|integer|min:0',
            'harga_jual' => 'nullable|integer|min:0',
            'status' => 'required|in:tersedia,terjual,pending',
            'keterangan' => 'nullable|string',
        ]);

        AccountStock::create($validated);
        return redirect()->route('bloxfruit.accounts.index')->with('sukses', 'Akun berhasil ditambahkan!');
    }

    public function edit(AccountStock $account)
    {
        return view('bloxfruit.accounts.form', compact('account'));
    }

    public function update(Request $request, AccountStock $account)
    {
        $validated = $request->validate([
            'username_roblox' => 'required|string|max:255',
            'password_roblox' => 'nullable|string|max:255',
            'sword_gun' => 'nullable|string|max:500',
            'fruit' => 'nullable|string|max:500',
            'belly' => 'nullable|string|max:100',
            'fragment' => 'nullable|string|max:100',
            'race' => 'nullable|string|max:100',
            'level' => 'nullable|string|max:100',
            'harga_beli' => 'nullable|integer|min:0',
            'harga_jual' => 'nullable|integer|min:0',
            'status' => 'required|in:tersedia,terjual,pending',
            'keterangan' => 'nullable|string',
        ]);

        $oldStatus = $account->status;
        $account->update($validated);

        // Auto-record profit saat status berubah ke terjual
        if ($oldStatus !== 'terjual' && $validated['status'] === 'terjual' && ($account->harga_jual > 0)) {
            ProfitRecord::create([
                'tanggal' => now()->toDateString(),
                'kategori' => 'akun',
                'keterangan' => 'Jual akun ' . $account->username_roblox,
                'modal' => $account->harga_beli ?? 0,
                'pendapatan' => $account->harga_jual ?? 0,
                'keuntungan' => ($account->harga_jual ?? 0) - ($account->harga_beli ?? 0),
            ]);
        }

        // Hapus profit record saat status berubah dari terjual ke status lain
        if ($oldStatus === 'terjual' && $validated['status'] !== 'terjual') {
            ProfitRecord::where('kategori', 'akun')
                ->where('keterangan', 'Jual akun ' . $account->username_roblox)
                ->delete();
        }

        return redirect()->route('bloxfruit.accounts.index')->with('sukses', 'Akun berhasil diperbarui!');
    }

    public function destroy(AccountStock $account)
    {
        $account->delete();
        return redirect()->route('bloxfruit.accounts.index')->with('sukses', 'Akun berhasil dihapus!');
    }
}
