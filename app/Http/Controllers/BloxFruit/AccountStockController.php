<?php

namespace App\Http\Controllers\BloxFruit;

use App\Http\Controllers\Controller;
use App\Models\BloxFruit\AccountStock;
use Illuminate\Http\Request;

class AccountStockController extends Controller
{
    public function index(Request $request)
    {
        $query = AccountStock::query();

        if ($request->filled('cari')) {
            $query->where('judul', 'like', '%' . $request->cari . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $accounts = $query->orderByDesc('id')->paginate(15)->withQueryString();
        return view('bloxfruit.accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('bloxfruit.accounts.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'level' => 'nullable|string|max:100',
            'daftar_buah' => 'nullable|string',
            'daftar_gamepass' => 'nullable|string',
            'harga' => 'nullable|integer|min:0',
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
            'judul' => 'required|string|max:255',
            'level' => 'nullable|string|max:100',
            'daftar_buah' => 'nullable|string',
            'daftar_gamepass' => 'nullable|string',
            'harga' => 'nullable|integer|min:0',
            'status' => 'required|in:tersedia,terjual,pending',
            'keterangan' => 'nullable|string',
        ]);

        $account->update($validated);
        return redirect()->route('bloxfruit.accounts.index')->with('sukses', 'Akun berhasil diperbarui!');
    }

    public function destroy(AccountStock $account)
    {
        $account->delete();
        return redirect()->route('bloxfruit.accounts.index')->with('sukses', 'Akun berhasil dihapus!');
    }
}
