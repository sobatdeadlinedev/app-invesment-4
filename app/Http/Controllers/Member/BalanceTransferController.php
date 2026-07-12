<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BalanceTransferController extends Controller
{
    /**
     * Show balance transfer page
     */
    public function index()
    {
        $user = auth()->user();

        return view('member.pages.balance.transfer', [
            'user' => $user,
            'exchangeBalance' => $user->exchange_balance,
            'tradeBalance' => $user->trade_balance,
            'lockedBalance' => $user->locked_balance,
            'availableTradeBalance' => $user->getAvailableTradeBalance(),
            'targetVolume' => $user->target_volume,
            'achievedVolume' => $user->achieved_volume,
            'remainingVolume' => $user->getRemainingVolume(),
            'volumePercentage' => $user->getVolumeCompletionPercentage(),
            'needsPenalty' => $user->needsPenalty(),
        ]);
    }

    /**
     * Transfer from Exchange to Trade
     */
    public function exchangeToTrade(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10',
        ], [
            'amount.required' => 'Jumlah transfer harus diisi',
            'amount.min' => 'Minimal transfer adalah 10 USDT',
        ]);

        try {
            $user = auth()->user();
            $amount = $request->amount;

            // Check exchange balance
            if ($user->exchange_balance < $amount) {
                return redirect()
                    ->back()
                    ->with('error', 'Saldo Exchange tidak mencukupi. Saldo Anda: ' . number_format($user->exchange_balance, 2) . ' USDT');
            }

            DB::beginTransaction();

            // Deduct from exchange
            $user->deductExchangeBalance($amount);

            // Add to trade
            $user->addTradeBalance($amount);

            // Add target volume (1:1 dengan amount)
            $user->addTargetVolume($amount);

            DB::commit();

            return redirect()
                ->route('member.balance.transfer')
                ->with('success', 'Successfully transferred ' . number_format($amount, 2) . ' USDT from Exchange to Trade Balance. Your trading volume target has been increased by ' . number_format($amount, 2) . ' USDT.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Exchange to Trade transfer failed: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Transfer failed: ' . $e->getMessage());
        }
    }

    /**
     * Transfer from Trade to Exchange (with penalty check)
     */
    public function tradeToExchange(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10',
        ], [
            'amount.required' => 'Jumlah transfer harus diisi',
            'amount.min' => 'Minimal transfer adalah 10 USDT',
        ]);

        try {
            $user = auth()->user();
            $amount = $request->amount;

            // Check available trade balance (excluding locked)
            $availableBalance = $user->getAvailableTradeBalance();

            if ($availableBalance < $amount) {
                return redirect()
                    ->back()
                    ->with('error', 'Saldo Trade tersedia tidak mencukupi. Saldo tersedia: ' . number_format($availableBalance, 2) . ' USDT (Locked: ' . number_format($user->locked_balance, 2) . ' USDT)');
            }

            DB::beginTransaction();

            // Calculate penalty if volume not completed
            $penalty = 0;
            $netAmount = $amount;

            if ($user->needsPenalty()) {
                $penalty = $user->calculatePenalty($amount);
                $netAmount = $amount - $penalty;

                Log::info('Penalty applied', [
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'penalty' => $penalty,
                    'net_amount' => $netAmount,
                    'target_volume' => $user->target_volume,
                    'achieved_volume' => $user->achieved_volume,
                ]);
            }

            // Deduct from trade
            $user->deductTradeBalance($amount);

            // Add to exchange (after penalty)
            $user->addExchangeBalance($netAmount);

            DB::commit();

            $message = 'Successfully transferred ' . number_format($amount, 2) . ' USDT from Trade to Exchange Balance.';

            if ($penalty > 0) {
                $message .= ' Penalty applied: ' . number_format($penalty, 2) . ' USDT (30%) because trading volume is not yet completed. Net amount received: ' . number_format($netAmount, 2) . ' USDT.';
            }

            return redirect()
                ->route('member.balance.transfer')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Trade to Exchange transfer failed: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Transfer failed: ' . $e->getMessage());
        }
    }
}