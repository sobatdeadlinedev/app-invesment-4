@extends('member.layouts.app')
@section('content')
<div class="scrollable-content">
<div class="cs-wrap">

    {{-- ═══ HEADER ═══ --}}
    <div class="cs-header">
        <div class="cs-coin-info">
            <div class="cs-coin-ico" style="background:linear-gradient(135deg,{{ $coinInfo['color'] }}33,{{ $coinInfo['color'] }}1a);border:2px solid {{ $coinInfo['color'] }}66;">
                <i class="bi {{ $coinInfo['icon'] }}" style="color:{{ $coinInfo['color'] }};"></i>
            </div>
            <div>
                <div class="cs-coin-name">{{ $coinInfo['symbol'] }}</div>
                <div class="cs-coin-sub">{{ $coinInfo['name'] }}</div>
            </div>
        </div>
        <div class="cs-header-right">
            @if(count($openSignals) > 0)
                <div class="cs-live-badge">
                    <span class="cs-live-dot"></span> {{ count($openSignals) }} {{ __('app.open_signals') }}
                </div>
            @endif
            <button onclick="openCoinPopup()" class="cs-switch-btn">
                <i class="bi bi-arrow-left-right"></i>
            </button>
        </div>
    </div>

    {{-- ═══ COIN SELECTOR POPUP ═══ --}}
    <div id="coinPopupOverlay" class="cs-popup-overlay" onclick="closeCoinPopup()" style="display:none;">
        <div class="cs-popup-modal" onclick="event.stopPropagation()">
            <div class="cs-popup-head">
                <h6>{{ __('app.select_coin') }}</h6>
                <button class="cs-popup-close" onclick="closeCoinPopup()"><i class="bi bi-x"></i></button>
            </div>
            <div class="cs-popup-body">
                @foreach($allCoins as $symbol => $info)
                    <a href="{{ route('member.invest.coin', ['coin' => strtolower($symbol)]) }}"
                       class="cs-coin-row {{ $symbol === $coin ? 'active' : '' }}">
                        <div class="cs-coin-row-ico" style="background:linear-gradient(135deg,{{ $info['color'] }},{{ $info['color'] }}cc);">
                            <i class="bi {{ $info['icon'] }}"></i>
                        </div>
                        <div class="cs-coin-row-info">
                            <div class="cs-coin-row-sym">{{ $info['symbol'] }}</div>
                            <div class="cs-coin-row-name">{{ $info['name'] }}</div>
                        </div>
                        @if($signalCounts[$symbol] > 0)
                            <span class="cs-coin-row-badge">{{ $signalCounts[$symbol] }} {{ __('app.signals') }}</span>
                        @else
                            <small class="cs-coin-row-none">{{ __('app.no_signals') }}</small>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ═══ ALERTS ═══ --}}
    @if(!auth()->user()->canJoinSignal())
        <div class="cs-notice">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <p>{!! __('app.minimum_balance_required') !!} {!! __('app.please_transfer_funds', ['url' => route('member.balance.transfer')]) !!}</p>
        </div>
    @endif
    @if(session('success'))
        <div class="cs-alert cs-alert-ok" id="cs-alert">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()"><i class="bi bi-x"></i></button>
        </div>
    @endif
    @if(session('error'))
        <div class="cs-alert cs-alert-err" id="cs-alert">
            <i class="bi bi-exclamation-circle-fill"></i>
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()"><i class="bi bi-x"></i></button>
        </div>
    @endif

    {{-- ═══ CHART ═══ --}}
    <div class="cs-chart-wrap">
        <iframe src="https://www.tradingview.com/widgetembed/?symbol={{ $coinInfo['tradingview_symbol'] }}&interval=60&theme=dark&style=1&locale=en&toolbar_bg=131d2e&enable_publishing=false&hidesidetoolbar=1&allow_symbol_change=0&show_popup_button=0&details=0&calendar=0&studies=%5B%5D"
            style="width:100%;height:280px;border:none;display:block;" frameborder="0" allowtransparency="true" scrolling="no">
        </iframe>
    </div>

    {{-- ═══ TABS ═══ --}}
    <div class="cs-tabs">
        <button class="cs-tab-btn active" data-tab="signals">
            <i class="bi bi-broadcast"></i> {{ __('app.trading_signals') }}
        </button>
        <button class="cs-tab-btn" data-tab="history">
            <i class="bi bi-clock-history"></i> {{ __('app.historical_orders') }}
        </button>
    </div>

    {{-- ═══ TAB: SIGNALS ═══ --}}
    <div class="cs-tab-content" id="tab-signals">

        @forelse($openSignals as $signal)
            @php
                $hasJoined = in_array($signal->id, $joinedSignalIds);
                $betAmountPreview = $signal->betAmountPreview ?? 0;
                $hasSufficientBalance = $signal->bet_type == 'percentage'
                    ? auth()->user()->canJoinSignal()
                    : auth()->user()->getAvailableTradeBalance() >= $betAmountPreview;
            @endphp

            <div class="cs-signal-card">
                <div class="cs-sc-top">
                    <div class="cs-sc-live"><span class="cs-live-dot"></span> LIVE</div>
                    <span class="cs-sc-open-badge">{{ __('app.open') }}</span>
                </div>
                <div class="cs-sc-title">{{ $signal->title }}</div>
                @if($signal->description)
                    <div class="cs-sc-desc">{{ $signal->description }}</div>
                @endif

                <div class="cs-sc-stats">
                    <div class="cs-sc-stat">
                        <div class="cs-sc-stat-lbl">{{ __('app.your_balance') }}</div>
                        <div class="cs-sc-stat-val">{{ number_format(auth()->user()->trade_balance, 2) }} <span class="cs-usdt">USDT</span></div>
                    </div>
                    <div class="cs-sc-stat-sep"></div>
                    <div class="cs-sc-stat">
                        <div class="cs-sc-stat-lbl">{{ __('app.your_bet') }}</div>
                        <div class="cs-sc-stat-val gold">{{ number_format($betAmountPreview, 2) }} <span class="cs-usdt">USDT</span></div>
                    </div>
                </div>

                <div class="cs-sc-action">
                    @if($hasJoined)
                        <div class="cs-sc-joined">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ __('app.you_have_joined') }}
                        </div>
                    @elseif($hasSufficientBalance)
                        <form action="{{ route('member.signals.join', $signal->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="cs-sc-join-btn"
                                onclick="return confirm('{{ __('app.join_signal_confirmation', ['bet_amount' => number_format($betAmountPreview, 2)]) }}')">
                                <i class="bi bi-check-circle me-2"></i>{{ __('app.join_this_signal') }}
                            </button>
                        </form>
                    @else
                        <div class="cs-sc-insuf">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ __('app.insufficient_balance_message') }}
                            <a href="{{ route('member.balance.transfer') }}" class="cs-insuf-link">{{ __('app.transfer_now') }}</a>
                        </div>
                    @endif
                </div>
            </div>

        @empty
            <div class="cs-empty">
                <i class="bi bi-broadcast-pin cs-empty-ico"></i>
                <div class="cs-empty-h">{{ __('app.no_open_signals_for') }} {{ $coinInfo['name'] }}</div>
                <div class="cs-empty-sub">{{ __('app.check_back_later') }}</div>
            </div>
        @endforelse
    </div>

    {{-- ═══ TAB: HISTORY ═══ --}}
    <div class="cs-tab-content" id="tab-history" style="display:none;">

        @if($totalJoinedThisCoin > 0)
            <div class="cs-hist-stats">
                <div class="cs-hs-chip">
                    <div class="cs-hs-val">{{ $totalJoinedThisCoin }}</div>
                    <div class="cs-hs-lbl">{{ __('app.total') }}</div>
                </div>
                <div class="cs-hs-div"></div>
                <div class="cs-hs-chip">
                    <div class="cs-hs-val {{ $winRateThisCoin >= 50 ? 'green' : 'red' }}">{{ number_format($winRateThisCoin, 1) }}%</div>
                    <div class="cs-hs-lbl">{{ __('app.win_rate') }}</div>
                </div>
                <div class="cs-hs-div"></div>
                <div class="cs-hs-chip">
                    <div class="cs-hs-val {{ $totalProfitLossThisCoin >= 0 ? 'green' : 'red' }}">
                        {{ $totalProfitLossThisCoin >= 0 ? '+' : '' }}{{ number_format($totalProfitLossThisCoin, 0) }}
                    </div>
                    <div class="cs-hs-lbl">P&L</div>
                </div>
                <div class="cs-hs-div"></div>
                <div class="cs-hs-chip">
                    <div class="cs-hs-val amber">{{ number_format($totalFeesThisCoin, 0) }}</div>
                    <div class="cs-hs-lbl">{{ __('app.fees') }}</div>
                </div>
            </div>
        @endif

        @forelse($historyForThisCoin as $participant)
            @php
                $signal     = $participant->signal;
                $isPending  = $signal->status !== 'settled' || $signal->result === null;
                $isWin      = $signal->result === 'win';
                $isSettled  = $participant->status === 'settled';
                $netResult  = ($participant->profit_loss ?? 0) - ($participant->fee_amount ?? 0);
                $adminChoice = strtolower($signal->admin_choice ?? '');
                $dirLabel   = $isPending ? __('app.pending_status')
                    : ($adminChoice === 'call' ? __('app.call') . ' ↑'
                    : ($adminChoice === 'put'  ? __('app.put')  . ' ↓' : __('app.na')));
            @endphp

            <div class="cs-hist-card {{ $isPending ? 'pending' : ($isWin ? 'win' : 'loss') }}">
                <div class="cs-hc-head">
                    <div class="cs-hc-title">{{ $signal->title }}</div>
                    <span class="cs-hc-badge {{ $isPending ? 'pending' : ($isWin ? 'win' : 'loss') }}">
                        @if($isPending) {{ __('app.pending_status') }}
                        @elseif($isWin)  WIN ✓
                        @else            LOSS ✗
                        @endif
                    </span>
                </div>
                <div class="cs-hc-row">
                    <span class="cs-hc-lbl">{{ __('app.order_quantity') }}</span>
                    <span class="cs-hc-val">{{ number_format($participant->bet_amount, 2) }} USDT</span>
                </div>
                <div class="cs-hc-row">
                    <span class="cs-hc-lbl">{{ __('app.net_profit_loss') }}</span>
                    <span class="cs-hc-val {{ $isPending ? '' : ($netResult >= 0 ? 'green' : 'red') }}">
                        {{ $isPending ? '~' : ($isSettled ? ($netResult >= 0 ? '+' : '') . number_format($netResult, 2) . ' USDT' : '-') }}
                    </span>
                </div>
                <div class="cs-hc-row">
                    <span class="cs-hc-lbl">{{ __('app.time_period') }}</span>
                    <span class="cs-hc-val">
                        {{ $signal->opened_at ? $signal->opened_at->format('H:i') : '-' }} –
                        {{ $signal->closed_at  ? $signal->closed_at->format('H:i')  : '~' }}
                    </span>
                </div>
                <div class="cs-hc-row">
                    <span class="cs-hc-lbl">{{ __('app.direction') }}</span>
                    <span class="cs-hc-val {{ $adminChoice === 'call' ? 'green' : ($adminChoice === 'put' ? 'red' : '') }}">{{ $dirLabel }}</span>
                </div>
                @if($isSettled && ($signal->rate_of_return ?? 0) > 0)
                <div class="cs-hc-row">
                    <span class="cs-hc-lbl">{{ __('app.rate_of_return') }}</span>
                    <span class="cs-hc-val">{{ number_format($signal->rate_of_return, 2) }}%</span>
                </div>
                @endif
            </div>

        @empty
            <div class="cs-empty">
                <i class="bi bi-clock-history cs-empty-ico"></i>
                <div class="cs-empty-h">{{ __('app.no_historical_orders_for') }} {{ $coinInfo['name'] }}</div>
                <div class="cs-empty-sub">{{ __('app.join_signals_to_start') }}</div>
            </div>
        @endforelse

        @if($historyForThisCoin->hasPages())
            <div class="cs-pagination">{{ $historyForThisCoin->links() }}</div>
        @endif
    </div>

    <div style="height:24px;"></div>

</div>
</div>

{{-- ═══ SIGNAL CONFIRM POPUP (Bottom Sheet) ═══ --}}
@if($unjoinedOpenSignal)
<div class="cs-sp-overlay" id="cs-sp-overlay" onclick="csSpClose()"></div>
<div class="cs-sp-sheet" id="cs-sp-sheet">
    <div class="cs-sp-handle"></div>
    <div class="cs-sp-bell-wrap">
        <div class="cs-sp-bell"><i class="bi bi-bell-fill"></i></div>
    </div>
    <div class="cs-sp-head-title">{{ __('app.new_signal_available') }}</div>
    <div class="cs-sp-coin">{{ $coinInfo['symbol'] }} · {{ $coinInfo['name'] }}</div>

    <div class="cs-sp-signal-name">{{ $unjoinedOpenSignal->title }}</div>

    <div class="cs-sp-meta">
        <div class="cs-sp-meta-item">
            <div class="cs-sp-meta-lbl">{{ __('app.your_bet') }}</div>
            <div class="cs-sp-meta-val gold">{{ number_format($unjoinedOpenSignal->betAmountPreview, 2) }} <span style="font-size:10px;">USDT</span></div>
        </div>
        <div class="cs-sp-meta-sep"></div>
        <div class="cs-sp-meta-item">
            <div class="cs-sp-meta-lbl">{{ __('app.your_balance') }}</div>
            <div class="cs-sp-meta-val">{{ number_format(auth()->user()->trade_balance, 2) }} <span style="font-size:10px;">USDT</span></div>
        </div>
    </div>

    <form action="{{ route('member.signals.join', $unjoinedOpenSignal->id) }}" method="POST" id="cs-sp-form">
        @csrf
        <button type="submit" class="cs-sp-confirm">
            <i class="bi bi-check-circle-fill me-2"></i>{{ __('app.confirm_follow_signal') }}
        </button>
    </form>
    <button type="button" class="cs-sp-later" onclick="csSpClose()">{{ __('app.later') }}</button>
</div>
@endif

@push('styles')
<style>
/* ══ Wrap ══ */
.cs-wrap { background: transparent; }

/* ══ Header ══ */
.cs-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 18px 20px; border-bottom: 1px solid var(--border-color);
}
.cs-coin-info { display: flex; align-items: center; gap: 12px; }
.cs-coin-ico {
    width: 44px; height: 44px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: 20px;
}
.cs-coin-name { color: #fff; font-size: 16px; font-weight: 700; }
.cs-coin-sub  { color: var(--text-muted); font-size: 11px; margin-top: 1px; }
.cs-header-right { display: flex; align-items: center; gap: 10px; }
.cs-live-badge {
    display: flex; align-items: center; gap: 6px;
    padding: 5px 10px; border-radius: 20px;
    background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.25);
    color: #22c55e; font-size: 11px; font-weight: 700;
}
.cs-switch-btn {
    width: 36px; height: 36px; border-radius: 50%; border: none;
    background: linear-gradient(135deg, var(--gold-color), #00b8d4);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; font-size: 16px; color: #0a0f1e;
    box-shadow: 0 2px 8px rgba(0,229,255,0.3); flex-shrink: 0;
}

/* ══ Coin Selector Popup ══ */
.cs-popup-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);
    z-index: 9998; display: flex; align-items: center; justify-content: center;
}
.cs-popup-modal {
    background: #0e1929; border: 1px solid var(--border-color);
    border-radius: 16px; width: 92%; max-width: 420px;
    max-height: 82vh; overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,0.4);
    animation: csSlideUp 0.25s ease;
}
.cs-popup-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 18px 20px; border-bottom: 1px solid var(--border-color);
}
.cs-popup-head h6 { color: #fff; font-size: 15px; font-weight: 700; margin: 0; }
.cs-popup-close {
    background: none; border: none; width: 32px; height: 32px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: var(--text-muted); font-size: 22px; cursor: pointer;
}
.cs-popup-close:hover { background: rgba(255,255,255,0.06); }
.cs-popup-body { overflow-y: auto; max-height: calc(82vh - 70px); }
.cs-popup-body::-webkit-scrollbar { width: 4px; }
.cs-popup-body::-webkit-scrollbar-thumb { background: rgba(0,229,255,0.2); border-radius: 2px; }
.cs-coin-row {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 20px; border-bottom: 1px solid var(--border-color);
    text-decoration: none; transition: background 0.15s;
}
.cs-coin-row:last-child { border-bottom: none; }
.cs-coin-row:hover { background: rgba(0,229,255,0.04); }
.cs-coin-row.active { background: rgba(0,229,255,0.08); border-left: 3px solid var(--gold-color); padding-left: 17px; }
.cs-coin-row-ico {
    width: 40px; height: 40px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: 18px; color: #fff;
}
.cs-coin-row-info { flex: 1; }
.cs-coin-row-sym  { color: #fff; font-size: 14px; font-weight: 600; }
.cs-coin-row-name { color: var(--text-muted); font-size: 11px; }
.cs-coin-row-badge {
    background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.25);
    color: #22c55e; font-size: 10px; font-weight: 600;
    padding: 3px 8px; border-radius: 10px; white-space: nowrap;
}
.cs-coin-row-none { color: var(--text-muted); font-size: 11px; }

/* ══ Alerts / Notice ══ */
.cs-notice {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 12px 20px; font-size: 12px; color: #ef4444;
    background: rgba(239,68,68,0.05); border-bottom: 1px solid rgba(239,68,68,0.2);
}
.cs-notice i { font-size: 16px; flex-shrink: 0; margin-top: 1px; }
.cs-notice p { margin: 0; line-height: 1.5; }
.cs-alert {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 20px; font-size: 13px;
    border-bottom: 1px solid;
}
.cs-alert button { background: none; border: none; color: var(--text-muted); font-size: 18px; margin-left: auto; cursor: pointer; }
.cs-alert-ok  { background: rgba(34,197,94,0.05);  border-color: rgba(34,197,94,0.2);  color: #22c55e; }
.cs-alert-err { background: rgba(239,68,68,0.05);  border-color: rgba(239,68,68,0.2);  color: #ef4444; }

/* ══ Chart ══ */
.cs-chart-wrap { border-bottom: 1px solid var(--border-color); overflow: hidden; }

/* ══ Tabs ══ */
.cs-tabs {
    display: flex; border-bottom: 1px solid var(--border-color);
}
.cs-tab-btn {
    flex: 1; padding: 15px 12px; background: transparent; border: none;
    color: var(--text-muted); font-size: 13px; font-weight: 600;
    cursor: pointer; border-bottom: 3px solid transparent;
    display: flex; align-items: center; justify-content: center; gap: 7px;
    transition: all 0.2s;
}
.cs-tab-btn.active { color: var(--gold-color); border-bottom-color: var(--gold-color); }
.cs-tab-btn i { font-size: 15px; }

/* ══ Signal Cards ══ */
.cs-signal-card {
    margin: 14px 16px 0;
    background: rgba(255,255,255,0.02);
    border: 1px solid var(--border-color);
    border-radius: 16px; padding: 18px; overflow: hidden;
    position: relative;
}
.cs-signal-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
    background: linear-gradient(90deg, var(--gold-color), #00b8d4);
}
.cs-sc-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
.cs-sc-live { display: flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 700; color: #22c55e; }
.cs-sc-open-badge {
    font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
    padding: 3px 8px; border-radius: 4px;
    background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.25); color: #22c55e;
}
.cs-sc-title { color: #fff; font-size: 16px; font-weight: 700; margin-bottom: 4px; }
.cs-sc-desc  { color: var(--text-muted); font-size: 12px; line-height: 1.5; margin-bottom: 14px; }
.cs-sc-stats { display: flex; align-items: center; margin: 14px 0; }
.cs-sc-stat  { flex: 1; }
.cs-sc-stat-lbl { color: var(--text-muted); font-size: 11px; margin-bottom: 3px; }
.cs-sc-stat-val { color: #fff; font-size: 18px; font-weight: 800; }
.cs-sc-stat-val.gold { color: var(--gold-color); }
.cs-usdt { font-size: 10px; font-weight: 600; opacity: 0.7; }
.cs-sc-stat-sep { width: 1px; height: 38px; background: var(--border-color); margin: 0 18px; }
.cs-sc-action { margin-top: 14px; }
.cs-sc-join-btn {
    width: 100%; padding: 13px; border: none; border-radius: 12px;
    background: linear-gradient(135deg, var(--gold-color), #00b8d4);
    color: #0a0f1e; font-size: 14px; font-weight: 700;
    cursor: pointer; transition: all 0.25s;
}
.cs-sc-join-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(0,229,255,0.3); }
.cs-sc-joined {
    padding: 12px 16px; border-radius: 10px;
    background: rgba(59,130,246,0.1); border: 1px solid rgba(59,130,246,0.3);
    color: #60a5fa; font-size: 13px; font-weight: 600; text-align: center;
}
.cs-sc-insuf {
    padding: 12px 16px; border-radius: 10px;
    background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.25);
    color: #ef4444; font-size: 12px;
}
.cs-insuf-link { color: #fff; text-decoration: underline; margin-left: 4px; }

/* ══ Live Dot ══ */
.cs-live-dot {
    width: 8px; height: 8px; border-radius: 50%; background: #22c55e;
    display: inline-block; flex-shrink: 0;
    animation: csPulse 1.5s infinite;
}
@keyframes csPulse {
    0%,100% { box-shadow: 0 0 0 0 rgba(34,197,94,0.4); }
    50%      { box-shadow: 0 0 0 5px rgba(34,197,94,0); }
}

/* ══ History Stats ══ */
.cs-hist-stats {
    display: flex; align-items: center;
    margin: 14px 16px 0;
    background: rgba(255,255,255,0.02); border: 1px solid var(--border-color);
    border-radius: 12px; padding: 12px 0;
}
.cs-hs-chip { flex: 1; text-align: center; }
.cs-hs-val  { font-size: 17px; font-weight: 800; color: #fff; line-height: 1; }
.cs-hs-val.green { color: #22c55e; }
.cs-hs-val.red   { color: #ef4444; }
.cs-hs-val.amber { color: #fbbf24; }
.cs-hs-lbl  { font-size: 10px; color: var(--text-muted); margin-top: 3px; text-transform: uppercase; letter-spacing: 0.4px; }
.cs-hs-div  { width: 1px; height: 28px; background: var(--border-color); }

/* ══ History Cards ══ */
.cs-hist-card {
    margin: 12px 16px 0;
    background: rgba(255,255,255,0.02); border: 1px solid var(--border-color);
    border-radius: 12px; padding: 14px 16px;
    border-left: 3px solid var(--border-color);
}
.cs-hist-card.win     { border-left-color: #22c55e; }
.cs-hist-card.loss    { border-left-color: #ef4444; }
.cs-hist-card.pending { border-left-color: #fbbf24; }
.cs-hc-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
.cs-hc-title { color: #fff; font-size: 13px; font-weight: 700; flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.cs-hc-badge {
    font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;
    padding: 3px 8px; border-radius: 4px; flex-shrink: 0; margin-left: 8px;
}
.cs-hc-badge.win     { background: rgba(34,197,94,0.12);  border: 1px solid rgba(34,197,94,0.3);  color: #22c55e; }
.cs-hc-badge.loss    { background: rgba(239,68,68,0.12);  border: 1px solid rgba(239,68,68,0.3);  color: #ef4444; }
.cs-hc-badge.pending { background: rgba(251,191,36,0.12); border: 1px solid rgba(251,191,36,0.3); color: #fbbf24; }
.cs-hc-row { display: flex; justify-content: space-between; align-items: center; padding: 4px 0; }
.cs-hc-row:not(:last-child) { border-bottom: 1px solid rgba(255,255,255,0.04); }
.cs-hc-lbl { color: var(--text-muted); font-size: 11px; }
.cs-hc-val { color: #fff; font-size: 12px; font-weight: 600; }
.cs-hc-val.green { color: #22c55e; }
.cs-hc-val.red   { color: #ef4444; }

/* ══ Empty State ══ */
.cs-empty { padding: 50px 20px; text-align: center; }
.cs-empty-ico { font-size: 44px; color: var(--border-color); display: block; margin-bottom: 14px; }
.cs-empty-h   { color: var(--text-muted); font-size: 14px; font-weight: 600; margin-bottom: 6px; }
.cs-empty-sub { color: var(--text-muted); font-size: 12px; }

/* ══ Pagination ══ */
.cs-pagination { padding: 16px; }

/* ══ Signal Confirm Bottom Sheet ══ */
.cs-sp-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.55); z-index: 2000;
}
.cs-sp-overlay.open { display: block; }
.cs-sp-sheet {
    position: fixed; bottom: 0; left: 0; right: 0;
    background: #0e1929; border-top: 1px solid var(--border-color);
    border-radius: 22px 22px 0 0;
    padding: 14px 24px 72px;
    z-index: 2001;
    transform: translateY(100%);
    transition: transform 0.35s cubic-bezier(0.4,0,0.2,1);
}
.cs-sp-sheet.open { transform: translateY(0); }
.cs-sp-handle {
    width: 36px; height: 4px; border-radius: 2px;
    background: rgba(255,255,255,0.15); margin: 0 auto 20px;
}
.cs-sp-bell-wrap { text-align: center; margin-bottom: 10px; }
.cs-sp-bell {
    width: 52px; height: 52px; border-radius: 50%;
    background: rgba(0,229,255,0.1); border: 1px solid rgba(0,229,255,0.25);
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 22px; color: var(--gold-color);
}
.cs-sp-head-title { text-align: center; color: #fff; font-size: 17px; font-weight: 800; margin-bottom: 4px; }
.cs-sp-coin { text-align: center; color: var(--text-muted); font-size: 12px; margin-bottom: 16px; }
.cs-sp-signal-name {
    text-align: center; color: var(--gold-color); font-size: 15px; font-weight: 700;
    padding: 10px 16px; border-radius: 10px;
    background: rgba(0,229,255,0.06); border: 1px solid rgba(0,229,255,0.15);
    margin-bottom: 18px;
}
.cs-sp-meta { display: flex; align-items: center; margin-bottom: 22px; }
.cs-sp-meta-item { flex: 1; text-align: center; }
.cs-sp-meta-lbl { color: var(--text-muted); font-size: 11px; margin-bottom: 4px; }
.cs-sp-meta-val { color: #fff; font-size: 20px; font-weight: 900; }
.cs-sp-meta-val.gold { color: var(--gold-color); }
.cs-sp-meta-sep { width: 1px; height: 44px; background: var(--border-color); }
.cs-sp-confirm {
    width: 100%; padding: 14px; border: none; border-radius: 14px;
    background: linear-gradient(135deg, #22c55e, #16a34a);
    color: #fff; font-size: 15px; font-weight: 700;
    cursor: pointer; margin-bottom: 10px; transition: all 0.25s;
}
.cs-sp-confirm:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(34,197,94,0.35); }
.cs-sp-later {
    width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 14px;
    background: transparent; color: var(--text-muted); font-size: 14px; font-weight: 600;
    cursor: pointer; transition: all 0.2s;
}
.cs-sp-later:hover { background: rgba(255,255,255,0.04); }

/* ══ Animations ══ */
@keyframes csSlideUp {
    from { opacity: 0; transform: translateY(20px) scale(0.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
</style>
@endpush

@push('scripts')
<script>
// Coin selector popup
function openCoinPopup() {
    document.getElementById('coinPopupOverlay').style.display = 'flex';
}
function closeCoinPopup() {
    document.getElementById('coinPopupOverlay').style.display = 'none';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeCoinPopup(); csSpClose(); } });

// Tab switcher
document.addEventListener('DOMContentLoaded', function () {
    const initialTab = '{{ $tab }}';

    function showTab(name) {
        document.querySelectorAll('.cs-tab-content').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.cs-tab-btn').forEach(btn => btn.classList.remove('active'));
        const content = document.getElementById('tab-' + name);
        const btn = document.querySelector('[data-tab="' + name + '"]');
        if (content) content.style.display = 'block';
        if (btn) btn.classList.add('active');
    }

    showTab(initialTab);

    document.querySelectorAll('.cs-tab-btn').forEach(btn => {
        btn.addEventListener('click', () => showTab(btn.dataset.tab));
    });

    // Auto-hide alerts
    setTimeout(() => {
        const a = document.getElementById('cs-alert');
        if (a) { a.style.transition = 'opacity 0.4s'; a.style.opacity = '0'; setTimeout(() => a.remove(), 400); }
    }, 5000);

    // Auto-open signal popup if unjoined signal exists
    @if($unjoinedOpenSignal)
    const dismissKey = 'sig_dismissed_{{ $unjoinedOpenSignal->id }}';
    if (!sessionStorage.getItem(dismissKey) && initialTab !== 'history') {
        setTimeout(csSpOpen, 600);
    }
    @endif
});

// Signal confirm popup
function csSpOpen() {
    document.getElementById('cs-sp-overlay')?.classList.add('open');
    document.getElementById('cs-sp-sheet')?.classList.add('open');
}
function csSpClose() {
    @if($unjoinedOpenSignal)
    sessionStorage.setItem('sig_dismissed_{{ $unjoinedOpenSignal->id }}', '1');
    @endif
    document.getElementById('cs-sp-overlay')?.classList.remove('open');
    document.getElementById('cs-sp-sheet')?.classList.remove('open');
}
</script>
@endpush
@endsection
