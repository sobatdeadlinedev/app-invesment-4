<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\TradingSignal;
use App\Models\SignalParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SignalController extends Controller
{
    /**
     * Join a trading signal
     */
    public function join($id)
    {
        $signal = TradingSignal::findOrFail($id);
        $user = auth()->user();

        // Check if user has access to this signal
        if (!$signal->isUserAllowed($user->id)) {
            return redirect()
                ->back()
                ->with('error', 'You do not have access to this signal.');
        }

        // Validasi signal status
        if ($signal->status !== 'open') {
            return redirect()
                ->back()
                ->with('error', 'This signal is no longer available for joining.');
        }

        // Check if user already joined
        $alreadyJoined = SignalParticipant::where('signal_id', $signal->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyJoined) {
            return redirect()
                ->back()
                ->with('error', 'You have already joined this signal.');
        }

        // Calculate bet amount based on signal configuration
        $betAmount = $signal->calculateUserBetAmount($user);

        // Validate minimum balance
        if ($signal->bet_type === 'percentage') {
            // For percentage, check minimum 100 USDT available trade balance
            if (!$user->canJoinSignal()) {
                return redirect()
                    ->back()
                    ->with('error', 'Minimum available Trade Balance to join signal is 200 USDT. Your available balance: ' . number_format($user->getAvailableTradeBalance(), 2) . ' USDT.');
            }
        } else {
            // For fixed amount, check if user has sufficient balance
            if ($user->getAvailableTradeBalance() < $betAmount) {
                return redirect()
                    ->back()
                    ->with('error', 'Insufficient available Trade Balance. Required: ' . number_format($betAmount, 2) . ' USDT. Your available balance: ' . number_format($user->getAvailableTradeBalance(), 2) . ' USDT.');
            }
        }

        try {
            DB::beginTransaction();

            // Lock the bet amount
            $user->lockBalance($betAmount);

            // Create participant record
            $participant = SignalParticipant::create([
                'signal_id' => $signal->id,
                'user_id' => $user->id,
                'bet_amount' => $betAmount,
                'status' => 'joined',
                'joined_at' => now(),
            ]);

            DB::commit();

            Log::info('User joined signal', [
                'user_id' => $user->id,
                'signal_id' => $signal->id,
                'bet_type' => $signal->bet_type,
                'bet_value' => $signal->bet_value,
                'calculated_bet_amount' => $betAmount,
                'locked_balance' => $user->locked_balance,
            ]);

            return redirect()
                ->route('member.invest.coin', ['coin' => strtolower($signal->coin), 'tab' => 'history'])
                ->with('success', __('app.signal_joined_success', ['title' => $signal->title, 'amount' => number_format($betAmount, 2)]));
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Join signal failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'signal_id' => $signal->id,
            ]);

            return redirect()
                ->back()
                ->with('error', 'Failed to join signal: ' . $e->getMessage());
        }
    }

    // ❌ HAPUS method history() - sudah tidak dipakai
}
