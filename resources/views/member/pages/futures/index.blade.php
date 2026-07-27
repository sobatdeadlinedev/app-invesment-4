@extends('member.layouts.app')

@section('content')
{{-- Fix scroll: override parent container yang mungkin block scroll --}}
<style>
/* Hide scrollbar everywhere, keep scroll functional */
html, body {
    overflow-x: hidden !important;
    scrollbar-width: none !important;
    -ms-overflow-style: none !important;
}
html::-webkit-scrollbar, body::-webkit-scrollbar { display: none !important; width: 0 !important; }
.scrollable-content, .main-content, .page-content, .content-wrapper,
[class*="content"], [class*="wrapper"], [class*="main"] {
    overflow: visible !important;
    height: auto !important;
    max-height: none !important;
    scrollbar-width: none !important;
}
.scrollable-content::-webkit-scrollbar,
.main-content::-webkit-scrollbar { display: none !important; }
</style>

<div class="ex-root" id="futuresPage">

    {{-- ═══ TOP BAR ═══ --}}
    <div class="ex-topbar">
        <button onclick="openCoinPopup()" class="ex-pair-btn">
            <div class="ex-pair-icon" style="background:{{ $coinInfo['color'] }}22;border:1px solid {{ $coinInfo['color'] }}55;">
                <i class="{{ $coinInfo['icon'] }}" style="color:{{ $coinInfo['color'] }};font-size:13px;"></i>
            </div>
            <div class="ex-pair-meta">
                <div class="ex-pair-top">
                    <span class="ex-pair-sym">{{ $coinInfo['symbol'] }}</span>
                    <span class="ex-perp-tag">PERP</span>
                    <i class="bi bi-chevron-down" style="color:#848e9c;font-size:11px;"></i>
                </div>
                <span class="ex-pair-nm">{{ $coinInfo['name'] }}</span>
            </div>
        </button>
        <div class="ex-topbar-right">
            <div class="ex-price-block">
                <div class="ex-price-val" id="currentPrice">
                    {{ $currentPrice ? number_format($currentPrice, $currentPrice < 1 ? 6 : ($currentPrice < 100 ? 4 : 2), '.', ',') : '—' }}
                </div>
                <div class="ex-live-tag"><span class="ex-live-dot"></span>LIVE</div>
            </div>
        </div>
    </div>

    {{-- ═══ COIN POPUP ═══ --}}
    <div id="coinPopupOverlay" class="ex-mask" onclick="closeCoinPopup()" style="display:none;">
        <div class="ex-sheet" onclick="event.stopPropagation()">
            <div class="ex-sheet-notch"></div>
            <div class="ex-sheet-hd">
                <span class="ex-sheet-title">Select Market</span>
                <button class="ex-sheet-x" onclick="closeCoinPopup()"><i class="bi bi-x-lg"></i></button>
            </div>
            <div class="ex-sheet-search">
                <i class="bi bi-search" style="color:#474d57;font-size:12px;"></i>
                <input type="text" placeholder="Search..." class="ex-search-inp" id="pairSearch" oninput="filterPairs(this.value)">
            </div>
            <div class="ex-sheet-body" id="pairList">
                @php
                    $categories = [
                        'crypto' => ['label' => __('app.cryptocurrency'), 'icon' => 'bi-currency-bitcoin'],
                        'forex'  => ['label' => __('app.forex'), 'icon' => 'bi-currency-exchange'],
                        'metals' => ['label' => __('app.precious_metals'), 'icon' => 'bi-gem'],
                    ];
                @endphp
                @foreach ($categories as $cat => $catInfo)
                    @php $catCoins = array_filter($coins, fn($c) => $c['cat'] === $cat); @endphp
                    @if (count($catCoins))
                    <div class="ex-sheet-cat">
                        <i class="bi {{ $catInfo['icon'] }}"></i> {{ $catInfo['label'] }}
                    </div>
                    @foreach ($catCoins as $sym => $info)
                    <a href="{{ route('member.futures.index', ['coin' => $sym]) }}"
                       class="ex-mkt-row {{ $coin === $sym ? 'is-active' : '' }}"
                       data-name="{{ strtolower($info['name']) }} {{ strtolower($info['symbol']) }}">
                        <div class="ex-mkt-icon" style="background:{{ $info['color'] }}22;border:1px solid {{ $info['color'] }}44;">
                            <i class="{{ $info['icon'] }}" style="color:{{ $info['color'] }};"></i>
                        </div>
                        <div class="ex-mkt-info">
                            <span class="ex-mkt-sym">{{ $info['symbol'] }}</span>
                            <span class="ex-mkt-nm">{{ $info['name'] }}</span>
                        </div>
                        @if ($coin === $sym)
                        <i class="bi bi-check2-circle" style="color:#1890ff;font-size:17px;margin-left:auto;"></i>
                        @endif
                    </a>
                    @endforeach
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    {{-- ═══ CHART ═══ --}}
    <div class="ex-chart">
        <iframe
            src="https://www.tradingview.com/widgetembed/?symbol={{ urlencode($coinInfo['tv']) }}&interval=1&theme=dark&style=1&locale=en&toolbar_bg=0b0e11&enable_publishing=false&hidesidetoolbar=1&allow_symbol_change=0&show_popup_button=0&details=0&calendar=0&studies=%5B%5D&hide_top_toolbar=0"
            style="width:100%;height:240px;border:none;display:block;"
            frameborder="0" allowtransparency="true" scrolling="no">
        </iframe>
    </div>

    {{-- ═══ ACTIVE TRADE ═══ --}}
    @if ($openTrade)
    <div class="ex-active" id="activeTrade">
        <div class="ex-active-accent {{ $openTrade->direction }}"></div>
        <div class="ex-active-body">
            <div class="ex-active-row1">
                <div class="ex-active-lhs">
                    <span class="ex-active-lbl">{{ __('app.active_trade') }}</span>
                    <span class="ex-chip {{ $openTrade->direction }}">
                        <i class="bi bi-arrow-{{ $openTrade->direction === 'call' ? 'up' : 'down' }}-short"></i>
                        {{ strtoupper($openTrade->direction) }}
                    </span>
                </div>
                <div class="ex-timer-ring">
                    <svg viewBox="0 0 48 48" style="width:48px;height:48px;transform:rotate(-90deg)">
                        <circle cx="24" cy="24" r="20" fill="none" stroke="#252d3d" stroke-width="3"/>
                        <circle cx="24" cy="24" r="20" fill="none" stroke="#f0b90b" stroke-width="3"
                            stroke-dasharray="125.6" stroke-dashoffset="0"
                            id="timerRing" stroke-linecap="round"/>
                    </svg>
                    <div class="ex-timer-inner">
                        <span class="ex-timer-num" id="tradeTimer">60</span>
                        <span class="ex-timer-s">s</span>
                    </div>
                </div>
            </div>
            <div class="ex-active-grid">
                <div class="ex-ag-cell">
                    <span class="ex-ag-lbl">{{ __('app.entry') }}</span>
                    <span class="ex-ag-val">${{ number_format($openTrade->entry_price, $openTrade->entry_price < 1 ? 6 : 2, '.', ',') }}</span>
                </div>
                <div class="ex-ag-cell">
                    <span class="ex-ag-lbl">{{ __('app.current') }}</span>
                    <span class="ex-ag-val" id="atCurrentPrice">—</span>
                </div>
                <div class="ex-ag-cell">
                    <span class="ex-ag-lbl">{{ __('app.amount') }}</span>
                    <span class="ex-ag-val">${{ number_format($openTrade->amount, 2) }}</span>
                </div>
                <div class="ex-ag-cell">
                    <span class="ex-ag-lbl">{{ __('app.potential') }}</span>
                    <span class="ex-ag-val c-buy">+${{ number_format($openTrade->amount * $openTrade->payout_rate / 100, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
    @else
    <div id="activeTrade" style="display:none;"></div>
    @endif

    {{-- ═══ TRADE ORDER PANEL ═══ --}}
    <div id="tradePanel" style="{{ $openTrade ? 'display:none' : '' }}">

        {{-- Duration / Expiry row (display only — logic unchanged: fixed 60s) --}}
        <div class="ex-meta-row">
            <div class="ex-meta-box">
                <span class="ex-meta-val">60s</span>
                <i class="bi bi-chevron-down"></i>
            </div>
            <div class="ex-meta-box ex-meta-box--wide">
                <span class="ex-meta-val" id="expiryWindow">--:-- - --:--</span>
                <i class="bi bi-chevron-down"></i>
            </div>
        </div>

        {{-- Amount input --}}
        <div class="ex-amount-row">
            <div class="ex-amount-box" id="amountBox">
                <input type="number" id="tradeAmount" class="ex-amount-input"
                    placeholder="0.00" min="1" step="1" autocomplete="off">
                <span class="ex-amount-currency">USDT</span>
            </div>
        </div>

        {{-- Available balance --}}
        <div class="ex-avail-row">
            <span class="ex-avail-lbl">{{ __('app.available') ?? 'available' }}</span>
            <span class="ex-avail-val" id="tradeBalance">{{ number_format(auth()->user()->trade_balance, 2) }}</span>
            <span class="ex-avail-cur">USDT</span>
        </div>

        {{-- Quick percent chips --}}
        <div class="ex-quick-grid">
            @foreach ([1, 50, 75, 100] as $q)
            <button class="ex-qbtn" onclick="setPercent({{ $q }})">{{ $q }}%</button>
            @endforeach
        </div>

        {{-- CALL / PUT buttons --}}
        <div class="ex-action-row">
            <button class="ex-action-btn ex-call" id="btnCall" onclick="openTrade('call')">
                <span class="ex-action-main">{{ __('app.call') }}</span>
            </button>
            <button class="ex-action-btn ex-put" id="btnPut" onclick="openTrade('put')">
                <span class="ex-action-main">{{ __('app.put') }}</span>
            </button>
        </div>

        {{-- Hidden preview fields kept for JS compatibility (not shown, logic untouched) --}}
        <div style="display:none;">
            <span id="payoutAmount">—</span>
            <span id="totalReturn">—</span>
            <input type="range" id="amountSlider" min="0" max="100" value="0">
        </div>
    </div>

    {{-- ═══ TABS: delivery order / historical orders / invite me ═══ --}}
    <div class="ex-tabs-row">
        <button class="ex-tab is-active" data-tab="delivery">{{ __('app.delivery_order') ?? 'delivery order' }}</button>
        <button class="ex-tab" data-tab="historical">{{ __('app.historical_orders') ?? 'historical orders' }}</button>
        <button class="ex-tab ex-tab--invite" data-tab="invite">
            {{ __('app.invite_me') ?? 'invite me' }}
            @if($openSignalCount > 0)
            <span class="ex-tab-badge">{{ $openSignalCount }}</span>
            @endif
        </button>
    </div>

    {{-- ═══ DELIVERY ORDER (empty state supaya tidak blank di HP) ═══ --}}
    <div id="tab-panel-delivery">
        <div class="cp-void">
            <div class="cp-void-hex"><i class="bi bi-hourglass-split"></i></div>
            <div class="cp-void-text">{{ __('app.no_delivery_order') ?? 'Tidak ada order berjalan' }}</div>
        </div>
        <div style="height:120px;"></div>
    </div>

   {{-- ═══ INVITE ME (Expert Signals / Invitation) ═══ --}}
@php
    $inviteTargetCoin = $latestSignal ? $latestSignal->coin : $coin;
    $coinSlug = strtolower(str_replace(['USDT','USD'], '', $inviteTargetCoin));
    $inviteCoinInfo = $latestSignal ? $latestSignal->getCoinInfo() : null;
@endphp
<div class="ex-signals-wrap" id="tab-panel-invite" style="display:none;">

    @if($latestSignal)
    <div class="sg-card">
        <div class="sg-card-hd">
            <div class="sg-card-hd-ico" style="background:{{ ($inviteCoinInfo['color'] ?? '#1890ff') }}22;border:1px solid {{ ($inviteCoinInfo['color'] ?? '#1890ff') }}55;">
                <i class="{{ $inviteCoinInfo['icon'] ?? 'bi bi-broadcast-pin' }}" style="color:{{ $inviteCoinInfo['color'] ?? '#1890ff' }};"></i>
            </div>
            <div class="sg-card-hd-txt">
                <div class="sg-card-title">{{ __('app.expert_signals') }}</div>
                @if($openSignalCount > 0)
                <div class="sg-card-badge">
                    <span class="sg-badge-dot"></span>
                    {{ $openSignalCount }} {{ __('app.active_signal') ?? 'sinyal aktif' }}
                </div>
                @endif
            </div>
            <span class="sg-status sg-status--{{ $latestSignal->status }}">{{ strtoupper($latestSignal->status) }}</span>
        </div>

        <div class="sg-rows">
            <div class="sg-row">
                <span class="sg-row-k">{{ __('app.title') ?? 'Title' }}</span>
                <span class="sg-row-v">{{ $latestSignal->title }}</span>
            </div>
            <div class="sg-row">
                <span class="sg-row-k">{{ __('app.trading_pair') ?? 'Trading pair' }}</span>
                <span class="sg-row-v">{{ $inviteCoinInfo['symbol'] ?? $latestSignal->coin }}</span>
            </div>
            <div class="sg-row">
                <span class="sg-row-k">{{ __('app.release_time') ?? 'Release time' }}</span>
                <span class="sg-row-v">{{ optional($latestSignal->opened_at ?? $latestSignal->created_at)->format('d/m/Y, H:i:s') }}</span>
            </div>
            
        </div>

        <a href="{{ route('member.invest.coin', ['coin' => $coinSlug]) }}" class="sg-cta">
            {{ __('app.confirm_follow_order') ?? 'Confirm to follow the order' }}
        </a>
    </div>
    @else
    <div class="cp-void">
        <div class="cp-void-hex"><i class="bi bi-broadcast-pin"></i></div>
        <div class="cp-void-text">{{ __('app.no_active_signal') ?? 'Belum ada sinyal aktif' }}</div>
    </div>
    @endif

    <div style="height:120px;"></div>
</div>

    {{-- ═══ HISTORICAL ORDERS (gabungan Futures + Signal, semua coin — sama seperti tampilan Coin) ═══ --}}
    <div id="tab-panel-historical" style="display:none;">

        @php
            $ftTotal    = $recentTrades->total();
            $ftWins     = $recentTrades->getCollection()->where('is_win', true)->count();
            $ftSettled  = $recentTrades->getCollection()->where('is_pending', false)->count();
            $ftWinRate  = $ftSettled > 0 ? ($ftWins / $ftSettled) * 100 : 0;
            $ftPnl      = $recentTrades->getCollection()->where('is_pending', false)->sum('net_result');
        @endphp

        @if($ftTotal > 0)
        <div class="cp-scorecard">
            <div class="cp-sc-cell">
                <div class="cp-sc-n">{{ $ftTotal }}</div>
                <div class="cp-sc-l">ORDER</div>
            </div>
            <div class="cp-sc-vr"></div>
            <div class="cp-sc-cell">
                <div class="cp-sc-n {{ $ftWinRate >= 50 ? 'g' : 'r' }}">{{ number_format($ftWinRate, 1) }}%</div>
                <div class="cp-sc-l">WIN RATE</div>
            </div>
            <div class="cp-sc-vr"></div>
            <div class="cp-sc-cell">
                <div class="cp-sc-n {{ $ftPnl >= 0 ? 'g' : 'r' }}">{{ $ftPnl >= 0 ? '+' : '' }}{{ number_format($ftPnl, 0) }}</div>
                <div class="cp-sc-l">P&amp;L</div>
            </div>
            <div class="cp-sc-vr"></div>
            <div class="cp-sc-cell">
                <div class="cp-sc-n a">0</div>
                <div class="cp-sc-l">FEE</div>
            </div>
        </div>
        @endif

        @forelse ($recentTrades as $t)
        @php
            $stateKey   = $t->is_pending ? 'p' : ($t->is_win ? 'w' : 'l');
            $ftEntry    = $t->entry_price;
            $ftClose    = $t->close_price;
            $ftMoved    = ($ftEntry !== null && $ftClose !== null) ? ($ftClose - $ftEntry) : null;
            $direction  = strtolower($t->direction ?? '');
        @endphp
        <div class="cp-tk cp-tk-{{ $stateKey }}">
            <div class="cp-tk-head">
                <div class="cp-tk-asset">
                    <span class="cp-tk-asset-dot" style="background:{{ $t->coin_color }};box-shadow:0 0 6px {{ $t->coin_color }};"></span>
                    <span class="cp-tk-asset-sym">{{ $t->coin_symbol }}</span>
                    <span class="cp-tk-asset-n">{{ $t->coin_name }}</span>
                </div>
                <div class="cp-tk-badge cp-tk-badge-{{ $stateKey }}">
                    @if($t->is_pending) <i class="bi bi-hourglass-split"></i> PENDING
                    @elseif($t->is_win) <i class="bi bi-check-circle-fill"></i> WIN
                    @else <i class="bi bi-x-circle-fill"></i> LOSS
                    @endif
                </div>
            </div>

            <div class="cp-tk-title">{{ $t->title }}</div>

{{-- Price gauge: Entry -> Close/Target (hidden while pending) --}}
@if(!$t->is_pending)
<div class="cp-tk-gauge">
    <div class="cp-tk-gauge-pt">
        <div class="cp-tk-gauge-lbl">ENTRY</div>
        <div class="cp-tk-gauge-val">{{ $ftEntry !== null ? number_format($ftEntry, $ftEntry < 1 ? 6 : 2) : '--' }}</div>
    </div>
    <div class="cp-tk-gauge-track">
        <div class="cp-tk-gauge-line {{ $ftMoved !== null ? ($ftMoved >= 0 ? 'up' : 'down') : '' }}"></div>
        <i class="bi bi-caret-right-fill cp-tk-gauge-chev {{ $ftMoved !== null ? ($ftMoved >= 0 ? 'up' : 'down') : '' }}"></i>
    </div>
    <div class="cp-tk-gauge-pt right">
        <div class="cp-tk-gauge-lbl">TARGET</div>
        <div class="cp-tk-gauge-val {{ $ftMoved !== null ? ($ftMoved >= 0 ? 'cp-val-g' : 'cp-val-r') : '' }}">{{ $ftClose !== null ? number_format($ftClose, $ftClose < 1 ? 6 : 2) : '--' }}</div>
    </div>
</div>
@else
<div class="cp-tk-pending-note">
    <i class="bi bi-hourglass-split"></i> Waiting
</div>
@endif
<div class="cp-tk-perf"></div>

            <div class="cp-tk-stats">
                <div class="cp-tk-stat">
                    <div class="cp-tk-stat-k">BET</div>
                    <div class="cp-tk-stat-v">{{ number_format($t->bet_amount, 2) }}</div>
                </div>
                <div class="cp-tk-stat-vr"></div>
                <div class="cp-tk-stat">
                    <div class="cp-tk-stat-k">PROFIT/LOSS</div>
                    <div class="cp-tk-stat-v {{ $t->is_pending ? '' : ($t->net_result >= 0 ? 'cp-val-g' : 'cp-val-r') }}">
                        @if($t->is_pending) ~
                        @else {{ ($t->net_result >= 0 ? '+' : '') . number_format($t->net_result, 2) }}
                        @endif
                    </div>
                </div>
                <div class="cp-tk-stat-vr"></div>
                <div class="cp-tk-stat">
                    <div class="cp-tk-stat-k">RATE</div>
                    <div class="cp-tk-stat-v">{{ $t->rate !== null ? number_format($t->rate, 1) . '%' : '--' }}</div>
                </div>
            </div>

            {{-- Meta footer --}}
            <div class="cp-tk-meta">
                <span class="cp-tk-meta-time">
                    <i class="bi bi-clock-history"></i>
                    {{ $t->opened_at ? \Illuminate\Support\Carbon::parse($t->opened_at)->format('d M · H:i') : '--' }}
                    –
                    {{ $t->closed_at ? \Illuminate\Support\Carbon::parse($t->closed_at)->format('H:i') : '~' }}
                </span>
                <span class="cp-tk-meta-dir {{ $direction === 'call' ? 'cp-val-g' : ($direction === 'put' ? 'cp-val-r' : '') }}">
                    @if($t->is_pending) —
                    @elseif($direction === 'call') <i class="bi bi-graph-up-arrow"></i> CALL
                    @elseif($direction === 'put') <i class="bi bi-graph-down-arrow"></i> PUT
                    @else N/A
                    @endif
                </span>
            </div>
        </div>
        @empty
        <div class="cp-void">
            <div class="cp-void-hex"><i class="bi bi-archive"></i></div>
            <div class="cp-void-text">{{ __('app.no_trades_yet') }}</div>
        </div>
        @endforelse

       @if($recentTrades->hasPages())
<div class="cp-pages">
    <nav class="cpp-nav" role="navigation" aria-label="Pagination">
        <ul class="cpp-list">
            @php
                $current = $recentTrades->currentPage();
                $last    = $recentTrades->lastPage();
                $onEachSide = 1;
            @endphp
            @for ($i = 1; $i <= $last; $i++)
                @if ($i == 1 || $i == $last || ($i >= $current - $onEachSide && $i <= $current + $onEachSide))
                    @if ($i == $current)
                        <li class="cpp-item active"><span class="cpp-link">{{ $i }}</span></li>
                    @else
                        <li class="cpp-item"><a href="{{ $recentTrades->url($i) }}" class="cpp-link">{{ $i }}</a></li>
                    @endif
                @elseif ($i == $current - $onEachSide - 1 || $i == $current + $onEachSide + 1)
                    <li class="cpp-item disabled"><span class="cpp-link cpp-dots">...</span></li>
                @endif
            @endfor
        </ul>
    </nav>
</div>
@endif
<div>
        <div style="height:40px;"></div>
    </div>

</div>

{{-- ═══ RESULT POPUP ═══ --}}
<div class="ex-result-mask" id="resultOverlay" style="display:none;">
    <div class="ex-result-card">
        <div class="ex-result-gfx" id="resultGfx"></div>
        <div class="ex-result-emoji" id="resultIcon"></div>
        <div class="ex-result-title" id="resultTitle"></div>
        <div class="ex-result-amt" id="resultAmount"></div>
        <div class="ex-result-detail" id="resultDetail"></div>
        <div class="ex-result-bal">New Balance <strong id="resultBalance"></strong></div>
        <button class="ex-result-cta" onclick="dismissResult()">{{ __('app.continue_trading') }}</button>
    </div>
</div>
@endsection

@push('styles')
<style>
:root {
    --bg:      #0b0e11;
    --panel:   #131820;
    --card:    #1c2231;
    --bd:      #252d3d;
    --bd2:     #1a2235;
    --buy:     #0ecb81;
    --sell:    #f6465d;
    --ylw:     #f0b90b;
    --blue:    #1890ff;
    --t1:      #eaecef;
    --t2:      #848e9c;
    --t3:      #474d57;
    --r:       6px;
}

/* ── Reset scroll ── */
html, body {
    scrollbar-width: none !important;
    -ms-overflow-style: none !important;
}
html::-webkit-scrollbar,
body::-webkit-scrollbar { display: none !important; width: 0 !important; }

.ex-root {
    background: var(--bg);
    color: var(--t1);
    font-family: -apple-system, 'SF Pro Text', Inter, system-ui, sans-serif;
    font-size: 13px;
    overflow-y: auto;
    overflow-x: hidden;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    -ms-overflow-style: none;
    min-height: 100vh;
    padding-bottom: 80px;
    touch-action: pan-y;
}
.ex-root::-webkit-scrollbar { display: none; width: 0; }

/* ── Topbar ── */
.ex-topbar {
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 14px; height: 50px;
    background: var(--panel);
    border-bottom: 1px solid var(--bd);
    position: sticky; top: 0; z-index: 100;
}
.ex-pair-btn {
    display: flex; align-items: center; gap: 9px;
    background: none; border: none; cursor: pointer; padding: 0;
}
.ex-pair-icon {
    width: 30px; height: 30px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.ex-pair-meta { text-align: left; }
.ex-pair-top { display: flex; align-items: center; gap: 5px; margin-bottom: 1px; }
.ex-pair-sym { color: var(--t1); font-size: 14px; font-weight: 700; }
.ex-perp-tag {
    background: rgba(24,144,255,.1); border: 1px solid rgba(24,144,255,.25);
    color: var(--blue); font-size: 9px; font-weight: 700;
    padding: 0 4px; border-radius: 3px; letter-spacing: .5px;
}
.ex-pair-nm { color: var(--t2); font-size: 10px; }
.ex-topbar-right { display: flex; align-items: center; gap: 10px; }
.ex-price-block { text-align: right; }
.ex-price-val {
    color: var(--t1); font-size: 17px; font-weight: 600;
    font-variant-numeric: tabular-nums; line-height: 1.1;
}
.ex-live-tag {
    display: inline-flex; align-items: center; gap: 4px;
    background: rgba(14,203,129,.08); border: 1px solid rgba(14,203,129,.18);
    color: var(--buy); font-size: 9px; font-weight: 700;
    letter-spacing: .8px; text-transform: uppercase;
    padding: 1px 6px; border-radius: 3px; margin-top: 3px;
}
.ex-live-dot {
    width: 4px; height: 4px; background: var(--buy);
    border-radius: 50%; animation: blink 1.4s ease-in-out infinite;
}
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:.15} }

/* ── Popup ── */
.ex-mask {
    position: fixed; inset: 0; z-index: 9000;
    background: rgba(0,0,0,.8); backdrop-filter: blur(4px);
    display: flex; align-items: flex-end; justify-content: center;
}
.ex-sheet {
    width: 100%; max-width: 480px;
    background: #161c28; border-radius: 16px 16px 0 0;
    border: 1px solid var(--bd); border-bottom: none;
    max-height: 80vh; display: flex; flex-direction: column;
    animation: slideUp .22s ease;
}
@keyframes slideUp { from{transform:translateY(60px);opacity:0} to{transform:translateY(0);opacity:1} }
.ex-sheet-notch {
    width: 32px; height: 3px; background: var(--bd);
    border-radius: 2px; margin: 8px auto 0;
}
.ex-sheet-hd {
    display: flex; align-items: center; justify-content: space-between;
    padding: 12px 16px 10px;
    border-bottom: 1px solid var(--bd2);
}
.ex-sheet-title { color: var(--t1); font-size: 14px; font-weight: 700; }
.ex-sheet-x {
    width: 26px; height: 26px; border-radius: 50%;
    background: var(--card); border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
}
.ex-sheet-x i { color: var(--t2); font-size: 13px; }
.ex-sheet-search {
    display: flex; align-items: center; gap: 8px;
    padding: 9px 14px; background: var(--card);
    border-bottom: 1px solid var(--bd2);
}
.ex-search-inp {
    flex: 1; background: none; border: none; outline: none;
    color: var(--t1); font-size: 13px;
}
.ex-search-inp::placeholder { color: var(--t3); }
.ex-sheet-body { overflow-y: auto; flex: 1; }
.ex-sheet-body::-webkit-scrollbar { width: 2px; }
.ex-sheet-body::-webkit-scrollbar-thumb { background: var(--bd); }
.ex-sheet-cat {
    display: flex; align-items: center; gap: 6px;
    padding: 8px 14px;
    color: var(--t3); font-size: 10px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 1px;
    background: rgba(255,255,255,.01);
}
.ex-mkt-row {
    display: flex; align-items: center; gap: 11px;
    padding: 11px 14px;
    border-bottom: 1px solid rgba(255,255,255,.03);
    text-decoration: none; transition: background .12s;
}
.ex-mkt-row:hover { background: rgba(255,255,255,.02); }
.ex-mkt-row.is-active {
    background: rgba(24,144,255,.06);
    border-left: 2px solid var(--blue);
    padding-left: 12px;
}
.ex-mkt-icon {
    width: 34px; height: 34px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.ex-mkt-icon i { font-size: 15px; }
.ex-mkt-sym { display: block; color: var(--t1); font-size: 13px; font-weight: 600; }
.ex-mkt-nm  { color: var(--t2); font-size: 11px; }

/* ── Chart ── */
.ex-chart { background: #0d1117; border-bottom: 1px solid var(--bd); touch-action: pan-y; position: relative; }
.ex-chart iframe { touch-action: pan-y; pointer-events: auto; }
.ex-chart::after {
    content: '';
    position: absolute; inset: 0;
    z-index: 1;
    pointer-events: none;
}

/* ── Active trade ── */
.ex-active {
    display: flex; margin: 10px 12px;
    background: var(--panel); border: 1px solid var(--bd);
    border-radius: var(--r); overflow: hidden;
}
.ex-active-accent { width: 3px; flex-shrink: 0; }
.ex-active-accent.call { background: var(--buy); }
.ex-active-accent.put  { background: var(--sell); }
.ex-active-body { flex: 1; padding: 12px 13px; }
.ex-active-row1 {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 10px;
}
.ex-active-lhs { display: flex; align-items: center; gap: 8px; }
.ex-active-lbl { color: var(--t2); font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: .8px; }
.ex-chip {
    display: inline-flex; align-items: center; gap: 2px;
    font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 3px;
}
.ex-chip i { font-size: 14px; }
.ex-chip.call { background: rgba(14,203,129,.1); color: var(--buy); border: 1px solid rgba(14,203,129,.2); }
.ex-chip.put  { background: rgba(246,70,93,.08); color: var(--sell); border: 1px solid rgba(246,70,93,.18); }

/* Timer ring */
.ex-timer-ring { position: relative; width: 48px; height: 48px; }
.ex-timer-inner {
    position: absolute; inset: 0;
    display: flex; align-items: center; justify-content: center; gap: 1px;
}
.ex-timer-num { color: var(--ylw); font-size: 14px; font-weight: 700; font-variant-numeric: tabular-nums; }
.ex-timer-s { color: var(--t3); font-size: 9px; margin-top: 3px; }

.ex-active-grid {
    display: grid; grid-template-columns: 1fr 1fr;
    border: 1px solid var(--bd2); border-radius: 4px; overflow: hidden;
}
.ex-ag-cell {
    display: flex; justify-content: space-between; align-items: center;
    padding: 7px 10px;
    border-bottom: 1px solid var(--bd2);
    border-right: 1px solid var(--bd2);
}
.ex-ag-cell:nth-child(2n) { border-right: none; }
.ex-ag-cell:nth-last-child(-n+2) { border-bottom: none; }
.ex-ag-lbl { color: var(--t2); font-size: 10px; }
.ex-ag-val { color: var(--t1); font-size: 12px; font-weight: 600; font-variant-numeric: tabular-nums; }

/* ── Order section (redesigned to match reference) ── */
#tradePanel {
    background: var(--bg);
    padding: 14px 12px 10px;
}


/* ── Signal detail card (invite me) ── */
.sg-card {
    margin: 10px 12px 0;
    background: linear-gradient(160deg, #0d1526 0%, #080f1d 100%);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 16px;
    padding: 16px;
}
.sg-card-hd {
    display: flex; align-items: center; gap: 10px;
    margin-bottom: 14px;
}
.sg-card-hd-ico {
    width: 34px; height: 34px; border-radius: 9px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
}
.sg-card-hd-ico i { font-size: 15px; }
.sg-card-hd-txt { flex: 1; min-width: 0; }
.sg-card-title { color: #fff; font-size: 13px; font-weight: 700; }
.sg-card-badge { display: flex; align-items: center; gap: 5px; margin-top: 2px; }
.sg-badge-dot {
    width: 5px; height: 5px; background: #0ecb81; border-radius: 50%;
    animation: blink 1.4s ease-in-out infinite;
}
.sg-card-badge { color: #0ecb81; font-size: 11px; font-weight: 500; }
.sg-status {
    font-size: 9px; font-weight: 800; letter-spacing: .5px;
    padding: 3px 8px; border-radius: 20px; flex-shrink: 0;
}
.sg-status--open    { background: rgba(14,203,129,.1); border: 1px solid rgba(14,203,129,.25); color: #0ecb81; }
.sg-status--closed   { background: rgba(240,185,11,.1); border: 1px solid rgba(240,185,11,.25); color: #f0b90b; }
.sg-status--settled  { background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.12); color: rgba(255,255,255,.5); }

.sg-rows {
    border-top: 1px dashed rgba(255,255,255,0.1);
    padding-top: 12px;
}
.sg-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid rgba(255,255,255,0.04);
}
.sg-row:last-child { border-bottom: none; }
.sg-row-k { color: rgba(255,255,255,0.4); font-size: 12px; }
.sg-row-v { color: #fff; font-size: 12px; font-weight: 600; text-align: right; }

.sg-cta {
    display: block; text-align: center;
    margin-top: 16px;
    padding: 13px;
    background: linear-gradient(160deg, #1890ff 0%, #0e6dd6 100%);
    color: #fff; font-size: 13px; font-weight: 700;
    border-radius: 24px; text-decoration: none;
    transition: filter .15s;
}
.sg-cta:hover { filter: brightness(1.1); color: #fff; }

/* Duration / expiry meta row */
.ex-meta-row {
    display: flex; gap: 8px;
    margin-bottom: 10px;
}
.ex-meta-box {
    flex: 1;
    display: flex; align-items: center; justify-content: space-between;
    background: var(--panel);
    border: 1px solid var(--bd);
    border-radius: 22px;
    padding: 11px 16px;
    color: var(--t1); font-size: 13px; font-weight: 600;
}
.ex-meta-box--wide { flex: 1.5; }
.ex-meta-box i { color: var(--t2); font-size: 11px; }
.ex-meta-val { font-variant-numeric: tabular-nums; }

/* Amount input */
.ex-amount-row { margin-bottom: 8px; }
.ex-amount-box {
    display: flex; align-items: center; justify-content: space-between;
    background: var(--panel);
    border: 1px solid var(--bd);
    border-radius: 22px;
    padding: 12px 16px;
    transition: border-color .15s;
}
.ex-amount-box:focus-within { border-color: var(--blue); }
.ex-amount-input {
    flex: 1; background: none; border: none; outline: none;
    color: var(--t1); font-size: 16px; font-weight: 600;
    font-variant-numeric: tabular-nums;
}
.ex-amount-input::placeholder { color: var(--t3); font-weight: 400; }
.ex-amount-input::-webkit-outer-spin-button,
.ex-amount-input::-webkit-inner-spin-button { -webkit-appearance: none; }
.ex-amount-currency {
    color: var(--t2); font-size: 12px; font-weight: 700;
    letter-spacing: .3px; margin-left: 8px; white-space: nowrap;
}

/* Available balance row */
.ex-avail-row {
    display: flex; align-items: baseline; gap: 6px;
    padding: 2px 6px 12px;
}
.ex-avail-lbl { color: var(--t2); font-size: 12px; }
.ex-avail-val { color: var(--t1); font-size: 13px; font-weight: 700; font-variant-numeric: tabular-nums; }
.ex-avail-cur { color: var(--t2); font-size: 11px; }

/* Quick percent chips */
.ex-quick-grid {
    display: grid; grid-template-columns: repeat(4,1fr); gap: 8px;
    margin-bottom: 14px;
}


.cpp-nav { display: flex; justify-content: center; }
.cpp-list {
    display: flex; align-items: center; gap: 6px;
    list-style: none; margin: 0; padding: 0; flex-wrap: wrap; justify-content: center;
}
.cpp-item { display: flex; }
.cpp-link {
    min-width: 34px; height: 34px; padding: 0 10px;
    display: flex; align-items: center; justify-content: center;
    background: var(--card);
    border: 1px solid var(--bd);
    border-radius: 8px;
    color: var(--t1);
    font-size: 12px; font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
}
a.cpp-link:hover { background: rgba(255,255,255,0.06); color: #fff; }
.cpp-item.active .cpp-link {
    background: linear-gradient(160deg, #0a8f5a 0%, #0ecb81 100%);
    border-color: transparent;
    color: #06120c;
}
.cpp-item.disabled .cpp-link {
    opacity: 0.35; cursor: not-allowed; background: rgba(255,255,255,0.02);
}
.cpp-dots { border: none; background: none; }
.ex-qbtn {
    padding: 9px 2px;
    background: var(--panel); border: 1px solid var(--bd);
    border-radius: 20px;
    color: var(--buy); font-size: 12px; font-weight: 700;
    cursor: pointer; transition: all .15s; text-align: center;
}
.ex-qbtn:hover {
    background: rgba(14,203,129,.08);
    border-color: rgba(14,203,129,.3);
}

/* Action buttons — CALL / PUT with odds */
.ex-action-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.ex-action-btn {
    display: flex; align-items: center; justify-content: center;
    padding: 15px 10px; border: none; border-radius: 24px;
    cursor: pointer; transition: filter .15s, transform .1s;
}
.ex-action-btn:active { transform: scale(.97); }
.ex-action-btn:disabled { opacity: .35; cursor: not-allowed; transform: none; }
.ex-action-main { font-size: 15px; font-weight: 700; letter-spacing: .1px; }
.ex-call {
    background: linear-gradient(160deg, #0a8f5a 0%, #0ecb81 100%);
    color: #ffffff;
}
.ex-call:hover { filter: brightness(1.1); }
.ex-put {
    background: linear-gradient(160deg, #b31a2d 0%, #f6465d 100%);
    color: #fff;
}
.ex-put:hover { filter: brightness(1.08); }

/* ── Tabs row (delivery order / historical orders) ── */
.ex-tabs-row {
    display: flex; align-items: center; gap: 24px;
    padding: 14px 16px 12px;
    border-bottom: 1px solid var(--bd);
    overflow-x: auto;
    scrollbar-width: none;
}
.ex-tabs-row::-webkit-scrollbar { display: none; }
.ex-tab {
    background: none; border: none; cursor: pointer;
    color: var(--t2); font-size: 13px; font-weight: 600;
    padding: 0 0 10px; white-space: nowrap;
    position: relative;
}
.ex-tab.is-active { color: var(--t1); }
.ex-tab.is-active::after {
    content: '';
    position: absolute; left: 0; right: 0; bottom: -1px;
    height: 2px; background: var(--buy); border-radius: 2px;
}
.ex-tab--invite { display: inline-flex; align-items: center; gap: 5px; }
.ex-tab-badge {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 16px; height: 16px; padding: 0 4px;
    background: var(--buy); color: #06120c;
    font-size: 10px; font-weight: 800; line-height: 1;
    border-radius: 999px;
    box-shadow: 0 0 0 2px rgba(14,203,129,.25);
    animation: exBadgePulse 1.6s ease-in-out infinite;
}
@keyframes exBadgePulse {
    0%,100% { box-shadow: 0 0 0 2px rgba(14,203,129,.25); }
    50%     { box-shadow: 0 0 0 4px rgba(14,203,129,.08); }
}

/* ── SCORECARD (historical orders summary) ── */
.cp-scorecard {
    display: flex; align-items: center;
    margin: 14px 12px 16px;
    background: linear-gradient(135deg, #0c1424, #080f1d);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 14px; padding: 14px 0;
}
.cp-sc-cell { flex: 1; text-align: center; }
.cp-sc-n { font-size: 17px; font-weight: 900; color: #fff; font-variant-numeric: tabular-nums; }
.cp-sc-n.g { color: #4ade80; }
.cp-sc-n.r { color: #f87171; }
.cp-sc-n.a { color: #fbbf24; }
.cp-sc-l { font-size: 9px; font-weight: 800; letter-spacing: 1.5px; color: rgba(255,255,255,0.2); margin-top: 4px; }
.cp-sc-vr { width: 1px; height: 28px; background: rgba(255,255,255,0.06); }

/* ── TRADE TICKET (historical order card) ── */
.cp-tk {
    margin: 0 12px 14px;
    background: linear-gradient(160deg, #0d1526 0%, #080f1d 100%);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 16px;
    padding: 16px 16px 14px;
    position: relative;
    overflow: hidden;
}
.cp-tk::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0; height: 2px;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
}


.cp-tk-pending-note {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 12px;
    background: rgba(251,191,36,0.06);
    border: 1px solid rgba(251,191,36,0.15);
    border-radius: 10px;
    color: #fbbf24; font-size: 12px; font-weight: 600;
}
.cp-tk-w { box-shadow: inset 3px 0 0 #4ade80; }
.cp-tk-l { box-shadow: inset 3px 0 0 #f87171; }
.cp-tk-p { box-shadow: inset 3px 0 0 #fbbf24; }

.cp-tk-head {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 6px; gap: 8px;
}
.cp-tk-asset { display: flex; align-items: center; gap: 6px; min-width: 0; }
.cp-tk-asset-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
.cp-tk-asset-sym { color: rgba(255,255,255,0.55); font-size: 11px; font-weight: 800; letter-spacing: 0.5px; }
.cp-tk-asset-n { color: rgba(255,255,255,0.25); font-size: 11px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

.cp-tk-badge {
    display: flex; align-items: center; gap: 5px;
    font-size: 10px; font-weight: 800; letter-spacing: 0.5px;
    padding: 4px 10px; border-radius: 20px; flex-shrink: 0;
}
.cp-tk-badge-w { background: rgba(74,222,128,0.1);  border: 1px solid rgba(74,222,128,0.25);  color: #4ade80; }
.cp-tk-badge-l { background: rgba(241,87,87,0.1);   border: 1px solid rgba(241,87,87,0.25);   color: #f87171; }
.cp-tk-badge-p { background: rgba(251,191,36,0.1);  border: 1px solid rgba(251,191,36,0.25);  color: #fbbf24; }

/* Title row — sama seperti cp-tk-title di halaman Coin */
.cp-tk-title { color: #fff; font-size: 15px; font-weight: 800; line-height: 1.3; margin-bottom: 14px; }

/* Price gauge */
.cp-tk-gauge {
    display: flex; align-items: center; gap: 10px;
}
.cp-tk-gauge-pt { flex-shrink: 0; }
.cp-tk-gauge-pt.right { text-align: right; }
.cp-tk-gauge-lbl {
    font-size: 9px; font-weight: 800; letter-spacing: 1.5px;
    color: rgba(255,255,255,0.25); margin-bottom: 4px;
}
.cp-tk-gauge-val {
    color: #fff;
    font-family: 'JetBrains Mono', monospace;
    font-size: 15px; font-weight: 700; font-variant-numeric: tabular-nums;
}
.cp-tk-gauge-track {
    flex: 1; position: relative; height: 2px;
    background: rgba(255,255,255,0.08); border-radius: 2px;
    min-width: 30px;
}
.cp-tk-gauge-line {
    position: absolute; inset: 0; border-radius: 2px;
    background: rgba(255,255,255,0.15);
}
.cp-tk-gauge-line.up   { background: linear-gradient(90deg, rgba(74,222,128,0.15), #4ade80); }
.cp-tk-gauge-line.down { background: linear-gradient(90deg, rgba(248,113,113,0.15), #f87171); }
.cp-tk-gauge-chev {
    position: absolute; right: -3px; top: 50%; transform: translateY(-50%);
    font-size: 11px; color: rgba(255,255,255,0.2);
}
.cp-tk-gauge-chev.up   { color: #4ade80; }
.cp-tk-gauge-chev.down { color: #f87171; }

/* Perforated divider */
.cp-tk-perf {
    border-top: 1px dashed rgba(255,255,255,0.1);
    margin: 16px -16px 12px;
}

/* Stat grid */
.cp-tk-stats {
    display: flex; align-items: center;
    margin-bottom: 12px;
}
.cp-tk-stat { flex: 1; text-align: center; }
.cp-tk-stat-k {
    font-size: 9px; font-weight: 800; letter-spacing: 1.2px;
    color: rgba(255,255,255,0.25); margin-bottom: 5px;
}
.cp-tk-stat-v {
    color: #fff;
    font-family: 'JetBrains Mono', monospace;
    font-size: 14px; font-weight: 700; font-variant-numeric: tabular-nums;
}
.cp-tk-stat-vr { width: 1px; height: 26px; background: rgba(255,255,255,0.06); }

/* Meta footer */
.cp-tk-meta {
    display: flex; align-items: center; justify-content: space-between;
    gap: 8px;
    padding-top: 12px; border-top: 1px solid rgba(255,255,255,0.04);
}
.cp-tk-meta-time {
    display: flex; align-items: center; gap: 5px;
    color: rgba(255,255,255,0.3); font-size: 11px;
}
.cp-tk-meta-dir {
    display: flex; align-items: center; gap: 4px;
    font-size: 11px; font-weight: 800; letter-spacing: 0.5px;
}
.cp-val-g { color: #4ade80; }
.cp-val-r { color: #f87171; }

/* Void state for empty historical orders */
.cp-void { padding: 60px 20px; text-align: center; }
.cp-void-hex {
    width: 60px; height: 60px;
    border: 1px dashed rgba(255,255,255,0.1);
    border-radius: 14px; rotate: 45deg;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 20px; font-size: 22px; color: rgba(255,255,255,0.15);
}
.cp-void-hex i { rotate: -45deg; }
.cp-void-text { color: rgba(255,255,255,0.3); font-size: 13px; font-weight: 600; margin-bottom: 5px; }

/* Pagination wrapper */
.cp-pages { padding: 14px; }

/* ── Signals ── */
.ex-signals-wrap { padding: 10px 12px 6px; }
.ex-signals-card {
    display: flex; align-items: center; gap: 12px;
    padding: 12px 14px;
    background: var(--panel);
    border: 1px solid var(--bd);
    border-radius: var(--r);
    text-decoration: none;
    transition: border-color .15s;
}
.ex-signals-card:hover { border-color: var(--blue); }

.ex-signals-ico {
    width: 36px; height: 36px; flex-shrink: 0;
    background: rgba(24,144,255,.08);
    border: 1px solid rgba(24,144,255,.2);
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
}
.ex-signals-ico i { color: var(--blue); font-size: 16px; }

.ex-signals-text { flex: 1; min-width: 0; }
.ex-signals-title { color: var(--t1); font-size: 13px; font-weight: 600; margin-bottom: 2px; }
.ex-signals-sub   { color: var(--t2); font-size: 11px; }

.ex-signals-notif { display: flex; align-items: center; gap: 5px; }
.ex-signals-notif-dot {
    width: 5px; height: 5px; background: var(--buy);
    border-radius: 50%; flex-shrink: 0;
    animation: blink 1.4s ease-in-out infinite;
}
.ex-signals-notif-txt { color: var(--buy); font-size: 11px; font-weight: 500; }

.ex-signals-arrow { color: var(--t3); font-size: 14px; flex-shrink: 0; }

/* ── Result popup ── */
.ex-result-mask {
    position: fixed; inset: 0; z-index: 9999;
    background: rgba(0,0,0,.9); backdrop-filter: blur(8px);
    display: flex; align-items: center; justify-content: center;
}
.ex-result-card {
    width: 290px; background: #161c28;
    border: 1px solid var(--bd); border-radius: 12px;
    padding: 28px 22px 22px;
    text-align: center; position: relative; overflow: hidden;
    animation: popIn .25s cubic-bezier(.34,1.56,.64,1);
}
@keyframes popIn { from{opacity:0;transform:scale(.82)} to{opacity:1;transform:scale(1)} }
.ex-result-gfx {
    position: absolute; top: -60px; left: 50%;
    transform: translateX(-50%);
    width: 200px; height: 150px; border-radius: 50%;
    opacity: .12; filter: blur(35px); pointer-events: none;
}
.ex-result-emoji { font-size: 44px; margin-bottom: 10px; line-height: 1; }
.ex-result-title { font-size: 18px; font-weight: 700; margin-bottom: 8px; }
.ex-result-amt   { font-size: 30px; font-weight: 800; margin-bottom: 5px; font-variant-numeric: tabular-nums; letter-spacing: -.5px; }
.ex-result-detail{ color: var(--t2); font-size: 11px; margin-bottom: 4px; font-variant-numeric: tabular-nums; }
.ex-result-bal   { color: var(--t2); font-size: 12px; margin-bottom: 18px; }
.ex-result-bal strong { color: var(--blue); }
.ex-result-cta {
    width: 100%; padding: 12px;
    background: var(--card); border: 1px solid var(--bd);
    border-radius: var(--r); color: var(--t1);
    font-size: 13px; font-weight: 600; cursor: pointer;
    transition: all .15s;
}
.ex-result-cta:hover { border-color: var(--blue); color: var(--blue); }

/* ── Utils ── */
.c-buy  { color: var(--buy) !important; }
.c-sell { color: var(--sell) !important; }
</style>
@endpush

@php
$activeTradeData = $openTrade ? [
    'id'       => $openTrade->id,
    'direction'=> $openTrade->direction,
    'amount'   => (float) $openTrade->amount,
    'entry'    => (float) $openTrade->entry_price,
    'closesAt' => $openTrade->opened_at->addSeconds(60)->toISOString(),
    'payout'   => (float) $openTrade->payout_rate,
] : null;
@endphp
@push('scripts')
<script>
const CSRF    = document.querySelector('meta[name="csrf-token"]').content;
const COIN    = '{{ $coin }}';
const PAYOUT  = {{ \App\Http\Controllers\Member\FuturesController::PAYOUT_RATE }};
const RING_C  = 125.6;
const MAX_T   = 60;
let priceInterval, timerInterval;
let activeTrade  = @json($activeTradeData);
let tradeBalance = parseFloat('{{ auth()->user()->trade_balance }}');

const T = {
    activeTrade:    "{{ __('app.active_trade') }}",
    secRemaining:   "{{ __('app.seconds_remaining') }}",
    entry:          "{{ __('app.entry') }}",
    current:        "{{ __('app.current') }}",
    amount:         "{{ __('app.amount') }}",
    potential:      "{{ __('app.potential') }}",
    enterMin:       "{{ __('app.enter_min_amount') }}",
    errRetry:       "{{ __('app.error_try_again') }}",
    win:            "{{ __('app.win') }}",
    lose:           "{{ __('app.lose') }}",
};

/* ─ Pair search ─ */
function filterPairs(q) {
    q = q.toLowerCase();
    document.querySelectorAll('.ex-mkt-row').forEach(r => {
        r.style.display = r.dataset.name.includes(q) ? '' : 'none';
    });
}

/* ─ Popup ─ */
function openCoinPopup() {
    document.getElementById('coinPopupOverlay').style.display = 'flex';
    document.body.style.overflow = 'hidden';
    setTimeout(() => document.getElementById('pairSearch')?.focus(), 250);
}
function closeCoinPopup() {
    document.getElementById('coinPopupOverlay').style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if(e.key==='Escape') closeCoinPopup(); });

/* ─ Price ─ */
async function fetchPrice() {
    try {
        const r = await fetch(`/member/futures/price/${COIN}`);
        const j = await r.json();
        if (!j.success || !j.price || j.price <= 0) return;
        const p = parseFloat(j.price);
        const el = document.getElementById('currentPrice');
        if (el) el.textContent = formatPrice(p);
        const atEl = document.getElementById('atCurrentPrice');
        if (atEl && activeTrade) {
            atEl.textContent = '$' + formatPrice(p);
            atEl.className = 'ex-ag-val ' + (p - activeTrade.entry >= 0 ? 'c-buy' : 'c-sell');
        }
    } catch(e) {}
}
function formatPrice(p) {
    if (p < 0.01) return p.toFixed(6);
    if (p < 1)    return p.toFixed(4);
    if (p < 100)  return p.toFixed(4);
    return p.toLocaleString('en-US', { minimumFractionDigits:2, maximumFractionDigits:2 });
}

/* ─ Amount ─ */
const amtInput  = () => document.getElementById('tradeAmount');
const sliderEl  = () => document.getElementById('amountSlider');

function setAmount(v) {
    amtInput().value = v;
    syncSlider(v);
    updatePayout();
}
function setMaxAmount() { setAmount(Math.floor(tradeBalance)); }
function setPercent(pct) { setAmount(Math.floor(tradeBalance * pct / 100)); }

function syncSlider(v) {
    const pct = tradeBalance > 0 ? Math.min(100, (v / tradeBalance) * 100) : 0;
    const sl = sliderEl();
    if (sl) {
        sl.value = pct;
        const fill = pct + '%';
        sl.style.background = `linear-gradient(to right, #1890ff ${fill}, #252d3d ${fill})`;
    }
}

amtInput()?.addEventListener('input', () => {
    syncSlider(parseFloat(amtInput().value) || 0);
    updatePayout();
});
sliderEl()?.addEventListener('input', function() {
    setAmount(Math.floor(tradeBalance * this.value / 100));
});

function updatePayout() {
    const amt    = parseFloat(amtInput()?.value) || 0;
    const profit = (amt * PAYOUT / 100).toFixed(2);
    const total  = (amt + parseFloat(profit)).toFixed(2);
    const pEl = document.getElementById('payoutAmount');
    const tEl = document.getElementById('totalReturn');
    if (pEl) { pEl.textContent = amt > 0 ? `+$${profit}` : '—'; pEl.className = amt > 0 ? 'ex-preview-val c-buy' : 'ex-preview-val'; }
    if (tEl)   tEl.textContent = amt > 0 ? `$${total}` : '—';
}

/* ─ Expiry window display (visual only) ─ */
function updateExpiryWindow() {
    const el = document.getElementById('expiryWindow');
    if (!el) return;
    const now = new Date();
    const start = new Date(now.getTime() + (60 - now.getSeconds() % 60) * 1000);
    const end = new Date(start.getTime() + 60000);
    const fmt = d => String(d.getHours()).padStart(2,'0') + ':' + String(d.getMinutes()).padStart(2,'0');
    el.textContent = fmt(start) + ' - ' + fmt(end);
}

/* ─ Open trade ─ */
async function openTrade(direction) {
    const amount = parseFloat(amtInput()?.value);
    if (!amount || amount < 1) { alert(T.enterMin); return; }
    setBtns(true);
    try {
        const r = await fetch('/member/futures/open', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ coin: COIN, direction, amount }),
        });
        const j = await r.json();
        if (!j.success) { alert(j.message); setBtns(false); return; }
        activeTrade = {
            id: j.trade.id, direction: j.trade.direction,
            amount: j.trade.amount, entry: j.trade.entry_price,
            closesAt: j.trade.closes_at, payout: j.trade.payout_rate,
        };
        renderActiveTrade(j.trade);
    } catch(e) { alert(T.errRetry); setBtns(false); }
}
function setBtns(state) {
    ['btnCall','btnPut'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.disabled = state;
    });
}

function renderActiveTrade(trade) {
    document.getElementById('tradePanel').style.display = 'none';
    const panel = document.getElementById('activeTrade');
    if (!panel) { window.location.reload(); return; }
    panel.className = 'ex-active';
    panel.style.display = '';
    panel.innerHTML = `
        <div class="ex-active-accent ${trade.direction}"></div>
        <div class="ex-active-body">
            <div class="ex-active-row1">
                <div class="ex-active-lhs">
                    <span class="ex-active-lbl">${T.activeTrade}</span>
                    <span class="ex-chip ${trade.direction}">
                        <i class="bi bi-arrow-${trade.direction==='call'?'up':'down'}-short"></i>
                        ${trade.direction.toUpperCase()}
                    </span>
                </div>
                <div class="ex-timer-ring">
                    <svg viewBox="0 0 48 48" style="width:48px;height:48px;transform:rotate(-90deg)">
                        <circle cx="24" cy="24" r="20" fill="none" stroke="#252d3d" stroke-width="3"/>
                        <circle cx="24" cy="24" r="20" fill="none" stroke="#f0b90b" stroke-width="3"
                            stroke-dasharray="${RING_C}" stroke-dashoffset="0"
                            id="timerRing" stroke-linecap="round"/>
                    </svg>
                    <div class="ex-timer-inner">
                        <span class="ex-timer-num" id="tradeTimer">60</span>
                        <span class="ex-timer-s">s</span>
                    </div>
                </div>
            </div>
            <div class="ex-active-grid">
                <div class="ex-ag-cell"><span class="ex-ag-lbl">${T.entry}</span><span class="ex-ag-val">$${formatPrice(trade.entry_price)}</span></div>
                <div class="ex-ag-cell"><span class="ex-ag-lbl">${T.current}</span><span class="ex-ag-val" id="atCurrentPrice">—</span></div>
                <div class="ex-ag-cell"><span class="ex-ag-lbl">${T.amount}</span><span class="ex-ag-val">$${parseFloat(trade.amount).toFixed(2)}</span></div>
                <div class="ex-ag-cell"><span class="ex-ag-lbl">${T.potential}</span><span class="ex-ag-val c-buy">+$${(trade.amount*trade.payout_rate/100).toFixed(2)}</span></div>
            </div>
        </div>`;
    startTimer();
}

/* ─ Timer ─ */
function startTimer() {
    if (!activeTrade) return;
    const closesAt = new Date(activeTrade.closesAt).getTime();
    clearInterval(timerInterval);
    timerInterval = setInterval(async () => {
        const rem = Math.max(0, Math.ceil((closesAt - Date.now()) / 1000));
        const numEl  = document.getElementById('tradeTimer');
        const ringEl = document.getElementById('timerRing');
        if (numEl) numEl.textContent = String(rem).padStart(2,'0');
        if (ringEl) {
            ringEl.style.strokeDashoffset = RING_C * (1 - rem / MAX_T);
            ringEl.style.stroke = rem <= 10 ? '#f6465d' : '#f0b90b';
        }
        if (rem <= 0) { clearInterval(timerInterval); await closeTrade(); }
    }, 250);
}

/* ─ Close ─ */
async function closeTrade() {
    if (!activeTrade) return;
    try {
        const r = await fetch('/member/futures/close', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ trade_id: activeTrade.id }),
        });
        const j = await r.json();
        if (!j.success) {
            if (j.remaining > 0) setTimeout(closeTrade, j.remaining*1000+500);
            return;
        }
        showResult(j.result);
        activeTrade = null;
    } catch(e) { setTimeout(closeTrade, 2000); }
}

/* ─ Result ─ */
function showResult(result) {
    const win = result.outcome === 'win';
    document.getElementById('resultGfx').style.background = win ? '#0ecb81' : '#f6465d';
    document.getElementById('resultIcon').textContent   = win ? '🎉' : '😔';
    document.getElementById('resultTitle').textContent  = win ? T.win : T.lose;
    document.getElementById('resultTitle').className    = 'ex-result-title ' + (win ? 'c-buy' : 'c-sell');
    document.getElementById('resultAmount').textContent = (win?'+':'') + '$' + Math.abs(result.profit_loss).toFixed(2);
    document.getElementById('resultAmount').className   = 'ex-result-amt ' + (win ? 'c-buy' : 'c-sell');
    document.getElementById('resultDetail').textContent = `Entry $${formatPrice(result.entry_price)} → Close $${formatPrice(result.close_price)}`;
    document.getElementById('resultBalance').textContent = '$' + parseFloat(result.new_balance).toFixed(2);
    document.getElementById('tradeBalance').textContent  = parseFloat(result.new_balance).toFixed(2);
    tradeBalance = parseFloat(result.new_balance);
    document.getElementById('resultOverlay').style.display = 'flex';
}
function dismissResult() {
    document.getElementById('resultOverlay').style.display = 'none';
    window.location.reload();
}

/* ─ Tabs (delivery order / historical orders / invite me) ─ */
document.addEventListener('click', e => {
    const tab = e.target.closest('.ex-tab');
    if (!tab) return;
    document.querySelectorAll('.ex-tab').forEach(t => t.classList.remove('is-active'));
    tab.classList.add('is-active');
    const name = tab.dataset.tab;
    const deliveryPanel   = document.getElementById('tab-panel-delivery');
    const historicalPanel = document.getElementById('tab-panel-historical');
    const invitePanel     = document.getElementById('tab-panel-invite');
    if (deliveryPanel)   deliveryPanel.style.display   = (name === 'delivery')   ? '' : 'none';
    if (historicalPanel) historicalPanel.style.display = (name === 'historical') ? '' : 'none';
    if (invitePanel)     invitePanel.style.display     = (name === 'invite')     ? '' : 'none';
});

/* ─ Fix: scroll terkunci di area chart pada HP (iframe menangkap touch) ─ */
(function() {
    const chartBox = document.querySelector('.ex-chart');
    if (!chartBox) return;
    let startY = 0;
    chartBox.addEventListener('touchstart', e => {
        startY = e.touches[0].clientY;
    }, { passive: true });
    chartBox.addEventListener('touchmove', e => {
        const dy = e.touches[0].clientY - startY;
        if (Math.abs(dy) > 4) {
            window.scrollBy(0, -dy * 0.35);
            startY = e.touches[0].clientY;
        }
    }, { passive: true });
})();

/* ─ Boot ─ */
document.addEventListener('DOMContentLoaded', () => {
    fetchPrice();
    priceInterval = setInterval(fetchPrice, 5000);
    if (activeTrade) startTimer();
    // Init slider fill
    sliderEl()?.dispatchEvent(new Event('input'));
    updateExpiryWindow();
    setInterval(updateExpiryWindow, 1000);
});
document.addEventListener('visibilitychange', () => {
    if (document.hidden) { clearInterval(priceInterval); }
    else { fetchPrice(); priceInterval = setInterval(fetchPrice, 5000); }
});
</script>
@endpush