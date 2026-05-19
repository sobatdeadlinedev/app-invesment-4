<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\TradingSignal;
use App\Models\SignalParticipant;
use Illuminate\Http\Request;

class InvestController extends Controller
{
    /**
     * Show all-in-one coin signals page
     */
    public function coinSignals(Request $request)
    {
        $user = auth()->user();
        $coin = strtoupper($request->query('coin', 'BTCUSDT'));

        // ========================================
        // Data untuk POPUP - All Coins
        // ========================================
        $allCoins = TradingSignal::getAvailableCoins();

        // Count open signals per coin yang user bisa akses
        $signalCounts = [];
        foreach (array_keys($allCoins) as $coinSymbol) {
            $signalCounts[$coinSymbol] = TradingSignal::forCoin($coinSymbol)
                ->open()
                ->accessibleBy($user->id)
                ->count();
        }

        // ========================================
        // Data untuk CURRENT COIN
        // ========================================
        $coinInfo = $allCoins[$coin] ?? $allCoins['BTCUSDT'];

        // ========================================
        // TAB 1: Trading Signals untuk coin ini
        // ========================================
        $openSignals = TradingSignal::forCoin($coin)
            ->open()
            ->accessibleBy($user->id)
            ->with('creator')
            ->withCount('participants')
            ->latest()
            ->get();

        // Get signal IDs yang sudah di-join user
        $joinedSignalIds = SignalParticipant::where('user_id', $user->id)
            ->pluck('signal_id')
            ->toArray();

        // Calculate bet amount preview untuk setiap signal
        $openSignals->each(function ($signal) use ($user) {
            $signal->betAmountPreview = $signal->calculateUserBetAmount($user);
        });

        // Find first unjoined signal with sufficient balance for auto-popup
        $unjoinedOpenSignal = $openSignals->first(function ($s) use ($user, $joinedSignalIds) {
            if (in_array($s->id, $joinedSignalIds)) return false;
            if ($s->bet_type === 'percentage') return $user->canJoinSignal();
            return $user->getAvailableTradeBalance() >= $s->betAmountPreview;
        });

        $tab = $request->query('tab', 'signals');

        // ========================================
        // TAB 2: Historical Orders untuk coin ini
        // ========================================
        // Sesudah
$historyForThisCoin = SignalParticipant::where('user_id', $user->id)
    ->whereHas('signal', function ($q) use ($coin) {
        $q->where('coin', $coin);
    })
    ->with('signal')
    ->orderBy('joined_at', 'desc')  // ← ganti latest() dengan ini
    ->paginate(10);

        // Calculate statistics untuk history tab
        $totalJoinedThisCoin = SignalParticipant::where('user_id', $user->id)
            ->whereHas('signal', function ($q) use ($coin) {
                $q->where('coin', $coin);
            })
            ->count();

        $totalSettledThisCoin = SignalParticipant::where('user_id', $user->id)
            ->whereHas('signal', function ($q) use ($coin) {
                $q->where('coin', $coin);
            })
            ->settled()
            ->count();

        $totalProfitLossThisCoin = SignalParticipant::where('user_id', $user->id)
            ->whereHas('signal', function ($q) use ($coin) {
                $q->where('coin', $coin);
            })
            ->settled()
            ->sum('profit_loss');

        $totalFeesThisCoin = SignalParticipant::where('user_id', $user->id)
            ->whereHas('signal', function ($q) use ($coin) {
                $q->where('coin', $coin);
            })
            ->settled()
            ->sum('fee_amount');

        $totalWinsThisCoin = SignalParticipant::where('user_id', $user->id)
            ->whereHas('signal', function ($q) use ($coin) {
                $q->where('coin', $coin);
            })
            ->settled()
            ->where('profit_loss', '>', 0)
            ->count();

        $winRateThisCoin = $totalSettledThisCoin > 0
            ? ($totalWinsThisCoin / $totalSettledThisCoin) * 100
            : 0;

        return view('member.pages.invest.coin-signals', compact(
            'coin', 'coinInfo',
            'allCoins', 'signalCounts',
            'openSignals', 'joinedSignalIds',
            'unjoinedOpenSignal', 'tab',
            'historyForThisCoin',
            'totalJoinedThisCoin', 'totalSettledThisCoin',
            'totalProfitLossThisCoin', 'totalFeesThisCoin',
            'totalWinsThisCoin', 'winRateThisCoin'
        ));
    }
}
