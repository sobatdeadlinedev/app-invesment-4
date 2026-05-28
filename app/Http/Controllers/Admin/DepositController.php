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
                ->with('error', 'This deposit has already been processed.');
        }

        DB::beginTransaction();
        try {
            // Update deposit status
            $deposit->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
            ]);

            // Add to exchange balance
            $user = $deposit->user;
            $user->addExchangeBalance($deposit->total_amount);

            // Check if this is first deposit
            $previousApprovedDeposits = Transaction::where('user_id', $deposit->user_id)
                ->where('type', 'deposit')
                ->where('status', 'approved')
                ->where('id', '!=', $deposit->id)
                ->count();

            $isFirstDeposit = ($previousApprovedDeposits === 0);

            // Give 4% bonus for first deposit
            if ($isFirstDeposit) {
                $bonusAmount = $deposit->total_amount * 0.04;

                // Add bonus to exchange balance
                $user->addExchangeBalance($bonusAmount);

                // Create bonus transaction record
                Transaction::create([
                    'user_id' => $user->id,
                    'source_user_id' => null,
                    'reference' => Transaction::generateReference('DP'),
                    'amount' => $bonusAmount,
                    'total_amount' => $bonusAmount,
                    'type' => 'deposit',
                    'balance_type' => 'exchange',
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                ]);
            }

            // Process referral commissions ONLY for first deposit
            if ($isFirstDeposit) {
                $this->processReferralCommissions($deposit);
            }

            DB::commit();

            $message = $isFirstDeposit
                ? 'Deposit has been approved successfully and added to Exchange Balance. Bonus 4% has been credited!'
                : 'Deposit has been approved successfully and added to Exchange Balance.';

            return redirect()->route('admin.deposit.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('admin.deposit.index')
                ->with('error', 'Failed to approve deposit: ' . $e->getMessage());
        }
    }

    public function reject($id)
    {
        $deposit = Transaction::deposit()->findOrFail($id);

        if ($deposit->status !== 'pending') {
            return redirect()->route('admin.deposit.index')
                ->with('error', 'This deposit has already been processed.');
        }

        $deposit->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
        ]);

        return redirect()->route('admin.deposit.index')
            ->with('success', 'Deposit has been rejected.');
    }

    /**
     * NEW: Manual adjustment (add balance)
     */
    public function adjustment(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'balance_type' => 'required|in:exchange,trade',
        ]);

        DB::beginTransaction();
        try {
            $user = User::findOrFail($request->user_id);
            $amount = $request->amount;
            $balanceType = $request->balance_type;

            // Create adjustment transaction
            Transaction::create([
                'user_id' => $user->id,
                'reference' => Transaction::generateReference('ADJ'),
                'amount' => $amount,
                'total_amount' => $amount,
                'type' => 'adjustment',
                'balance_type' => $balanceType,
                'status' => 'approved',
                'approved_by' => auth()->id(),
            ]);

            // Add balance based on type
            if ($balanceType === 'trade') {
                $user->addTradeBalance($amount);
            } else {
                $user->addExchangeBalance($amount);
            }

            DB::commit();

            return redirect()->route('admin.deposit.index')
                ->with('success', "Successfully added {$amount} USDT to {$user->name}'s {$balanceType} balance.");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('admin.deposit.index')
                ->with('error', 'Failed to add balance: ' . $e->getMessage());
        }
    }

    /**
     * Process referral commissions - UPDATED to add to exchange balance
     */
    private function processReferralCommissions(Transaction $deposit)
    {
        $referralUsage = ReferralUsage::where('referred_id', $deposit->user_id)->first();

        if (!$referralUsage) {
            return;
        }

        $depositAmount = $deposit->total_amount;

        // Referrer: 6%
        $referrerCommission = $depositAmount * 0.06;
        $this->createCommissionTransaction(
            $referralUsage->referrer_id,
            $deposit->user_id,
            $referrerCommission,
            'Referral Commission - First Deposit'
        );
    }

    /**
     * Create commission transaction - UPDATED to add to exchange balance
     */
    private function createCommissionTransaction($userId, $sourceUserId, $amount, $note = '')
    {
        // Add commission to exchange balance
        $user = \App\Models\User::find($userId);
        $user->addExchangeBalance($amount);

        return Transaction::create([
            'user_id' => $userId,
            'source_user_id' => $sourceUserId,
            'reference' => Transaction::generateReference('CM'),
            'amount' => $amount,
            'total_amount' => $amount,
            'type' => 'commission',
            'balance_type' => 'exchange',
            'status' => 'approved',
            'approved_by' => auth()->id(),
        ]);
    }
}