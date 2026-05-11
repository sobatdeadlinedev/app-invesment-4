<?php

namespace App\Http\Controllers\Member;

use App\Models\User;
use App\Models\Transaction;
use App\Models\FuturesTrade;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function index()
    {
        $user = User::current();

        $balanceBreakdown  = Transaction::getUserBalanceBreakdown(auth()->id());
        $todayPnl          = $user->today_pnl;
        $openFuturesAmount = (float) FuturesTrade::where('user_id', $user->id)->open()->sum('amount');

        return view('member.pages.profile.index', compact(
            'user', 'balanceBreakdown', 'todayPnl', 'openFuturesAmount'
        ));
    }
}
