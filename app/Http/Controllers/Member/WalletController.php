<?php

namespace App\Http\Controllers\Member;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WalletController extends Controller
{
    public function index()
    {
        $user    = User::current();
        $wallets = $user->wallets()->get();
        return view('member.pages.wallet.index', compact('wallets'));
    }

    public function store(Request $request)
    {
        $user = User::current();

        // Validasi max 3 wallet
        if ($user->wallets()->count() >= 3) {
            return back()->with('error', 'Maksimal 3 wallet per user');
        }

        $request->validate([
            'type' => 'required|in:trc20,bep20',
            'account_number' => 'required|string|max:255',
        ]);

        $user->wallets()->create([
            'type' => $request->type,
            'account_number' => $request->account_number,
        ]);

        return back()->with('success', 'Wallet berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $user = User::current();
        
        // Cari wallet milik user ini
        $wallet = $user->wallets()->find($id);

        if (!$wallet) {
            return back()->with('error', 'Wallet tidak ditemukan atau bukan milik Anda');
        }

        $request->validate([
            'type' => 'required|in:trc20,bep20',
            'account_number' => 'required|string|max:255',
        ]);

        $wallet->update([
            'type' => $request->type,
            'account_number' => $request->account_number,
        ]);

        return back()->with('success', 'Wallet berhasil diupdate');
    }

    public function destroy($id)
    {
        $user = User::current();
        
        // Cari wallet milik user ini
        $wallet = $user->wallets()->find($id);

        if (!$wallet) {
            return back()->with('error', 'Wallet tidak ditemukan atau bukan milik Anda');
        }

        $wallet->delete();

        return back()->with('success', 'Wallet berhasil dihapus');
    }
}