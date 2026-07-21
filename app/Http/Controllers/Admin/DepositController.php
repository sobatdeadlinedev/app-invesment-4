<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\ReferralUsage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepositController extends Controller
{
    public function index()
    {
        // Auto-expire deposit yang sudah lewat waktu (global, semua user)
        Transaction::where('type', 'deposit')
            ->where('status', 'pending')
            ->where('expired_at', '<', now())
            ->whereNotNull('expired_at')
            ->update(['status' => 'expired']);

        // Gabungkan deposit dan adjustment
        $deposits = Transaction::with(['user'])
            ->whereIn('type', ['deposit', 'adjustment'])
            ->latest()
            ->paginate(10);

        $members = User::role('member')
            ->orderBy('name')
            ->get();

        return view('admin.pages.deposit.index', compact('deposits', 'members'));
    }

    public function show($id)
    {
        $deposit = Transaction::with(['user', 'approver'])
            ->deposit()
            ->findOrFail($id);

        return view('admin.pages.deposit.detail', compact('deposit'));
    }

    public function approve($id)
    {
        $deposit = Transaction::deposit()->findOrFail($id);

        if ($deposit->status !== 'pending') {
            return redirect()->route('admin.deposit.index')
                ->with('error', 'Deposit ini sudah diproses sebelumnya (status: ' . $deposit->status . ').');
        }

        DB::beginTransaction();
        try {
            // Update deposit status
            $deposit->update([
                'status'      => 'approved',
                'approved_by' => auth()->id(),
            ]);

            // Tambah saldo exchange
            $user = $deposit->user;
            $user->addExchangeBalance($deposit->total_amount);

            // Cek apakah ini deposit pertama yang diapprove
            $previousApprovedDeposits = Transaction::where('user_id', $deposit->user_id)
                ->where('type', 'deposit')
                ->where('status', 'approved')
                ->where('id', '!=', $deposit->id)
                ->count();

            $isFirstDeposit = ($previousApprovedDeposits === 0);

            // Proses bonus new member (5%) dan komisi referrer (5%) — hanya untuk deposit pertama dan hanya jika pakai kode referral
            $referralBonusGiven = false;
            if ($isFirstDeposit) {
                $referralBonusGiven = $this->processReferralCommissions($deposit, $user);
            }

            DB::commit();

            $message = $referralBonusGiven
                ? 'Deposit berhasil diapprove dan saldo Exchange telah ditambahkan. Bonus referral 5% telah dikreditkan ke member dan pengundang!'
                : 'Deposit berhasil diapprove dan saldo Exchange telah ditambahkan.';

            return redirect()->route('admin.deposit.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('admin.deposit.index')
                ->with('error', 'Gagal approve deposit: ' . $e->getMessage());
        }
    }

    public function reject($id)
    {
        $deposit = Transaction::deposit()->findOrFail($id);

        if ($deposit->status !== 'pending') {
            return redirect()->route('admin.deposit.index')
                ->with('error', 'Deposit ini sudah diproses sebelumnya (status: ' . $deposit->status . ').');
        }

        $deposit->update([
            'status'      => 'rejected',
            'approved_by' => auth()->id(),
        ]);

        return redirect()->route('admin.deposit.index')
            ->with('success', 'Deposit telah ditolak.');
    }

    /**
     * Manual adjustment (tambah saldo)
     */
    public function adjustment(Request $request)
    {
        $request->validate([
            'user_id'      => 'required|exists:users,id',
            'amount'       => 'required|numeric|min:0.01',
            'balance_type' => 'required|in:exchange,trade',
        ]);

        DB::beginTransaction();
        try {
            $user        = User::findOrFail($request->user_id);
            $amount      = $request->amount;
            $balanceType = $request->balance_type;

            Transaction::create([
                'user_id'      => $user->id,
                'reference'    => Transaction::generateReference('ADJ'),
                'amount'       => $amount,
                'total_amount' => $amount,
                'type'         => 'adjustment',
                'balance_type' => $balanceType,
                'status'       => 'approved',
                'approved_by'  => auth()->id(),
            ]);

            if ($balanceType === 'trade') {
                $user->addTradeBalance($amount);
            } else {
                $user->addExchangeBalance($amount);
            }

            DB::commit();

            return redirect()->route('admin.deposit.index')
                ->with('success', "Berhasil menambahkan {$amount} USDT ke saldo {$balanceType} milik {$user->name}.");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('admin.deposit.index')
                ->with('error', 'Gagal menambahkan saldo: ' . $e->getMessage());
        }
    }

    /**
     * Proses bonus referral — hanya untuk deposit pertama, hanya jika user pakai kode referral
     * New member dapat 5%, Pengundang (referrer) dapat 5%
     *
     * @return bool true jika bonus diberikan
     */
    private function processReferralCommissions(Transaction $deposit, User $user)
    {
        $referralUsage = ReferralUsage::where('referred_id', $deposit->user_id)->first();

        if (!$referralUsage) {
            return false;
        }

        $depositAmount = $deposit->total_amount;

        // Bonus untuk new member (5%)
        $newMemberBonus = $depositAmount * 0.05;
        $user->addExchangeBalance($newMemberBonus);

        Transaction::create([
            'user_id'        => $user->id,
            'source_user_id' => $referralUsage->referrer_id,
            'reference'      => Transaction::generateReference('DP'),
            'amount'         => $newMemberBonus,
            'total_amount'   => $newMemberBonus,
            'type'           => 'deposit',
            'balance_type'   => 'exchange',
            'status'         => 'approved',
            'approved_by'    => auth()->id(),
        ]);

        // Komisi untuk pengundang (5%)
        $referrerCommission = $depositAmount * 0.05;

        $this->createCommissionTransaction(
            $referralUsage->referrer_id,
            $deposit->user_id,
            $referrerCommission,
            'Referral Commission - First Deposit'
        );

        return true;
    }

    /**
     * Buat transaksi komisi dan tambah saldo exchange referrer
     */
    private function createCommissionTransaction($userId, $sourceUserId, $amount, $note = '')
    {
        $user = \App\Models\User::find($userId);
        $user->addExchangeBalance($amount);

        return Transaction::create([
            'user_id'        => $userId,
            'source_user_id' => $sourceUserId,
            'reference'      => Transaction::generateReference('CM'),
            'amount'         => $amount,
            'total_amount'   => $amount,
            'type'           => 'commission',
            'balance_type'   => 'exchange',
            'status'         => 'approved',
            'approved_by'    => auth()->id(),
        ]);
    }
}