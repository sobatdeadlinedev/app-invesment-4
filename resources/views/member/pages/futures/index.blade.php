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

        {{-- Info strip --}}
        <div class="ex-info-strip">
            <div class="ex-info-item">
                <span class="ex-info-lbl">Balance</span>
                <span class="ex-info-val" id="tradeBalance">${{ number_format(auth()->user()->trade_balance, 2) }}</span>
            </div>
            <div class="ex-info-div"></div>
            <div class="ex-info-item">
                <span class="ex-info-lbl">Payout</span>
                <span class="ex-info-val c-buy">{{ \App\Http\Controllers\Member\FuturesController::PAYOUT_RATE }}%</span>
            </div>
            <div class="ex-info-div"></div>
            <div class="ex-info-item">
                <span class="ex-info-lbl">Duration</span>
                <span class="ex-info-val">60s</span>
            </div>
        </div>

        {{-- Order section --}}
        <div class="ex-order-section">

            {{-- Amount input --}}
            <div class="ex-field-label">{{ __('app.trade_amount_usdt') }}</div>
            <div class="ex-amount-row">
                <div class="ex-amount-box" id="amountBox">
                    <span class="ex-amount-currency">USDT</span>
                    <div class="ex-amount-divider"></div>
                    <input type="number" id="tradeAmount" class="ex-amount-input"
                        placeholder="0.00" min="1" step="1" autocomplete="off">
                </div>
                <button class="ex-max-btn" onclick="setMaxAmount()">MAX</button>
            </div>

            {{-- Slider --}}
            <div class="ex-slider-wrap">
                <input type="range" id="amountSlider" min="0" max="100" value="0" class="ex-slider">
                <div class="ex-slider-ticks">
                    @foreach([0,25,50,75,100] as $tick)
                    <span class="ex-slider-tick" onclick="setPercent({{ $tick }})">{{ $tick }}%</span>
                    @endforeach
                </div>
            </div>

            {{-- Quick amounts --}}
            <div class="ex-quick-grid">
                @foreach ([10, 25, 50, 100, 250, 500] as $q)
                <button class="ex-qbtn" onclick="setAmount({{ $q }})">{{ $q }}</button>
                @endforeach
            </div>

            {{-- Payout preview --}}
            <div class="ex-preview-box">
                <div class="ex-preview-row">
                    <span class="ex-preview-lbl">Est. Profit</span>
                    <span class="ex-preview-val c-buy" id="payoutAmount">—</span>
                </div>
                <div class="ex-preview-sep"></div>
                <div class="ex-preview-row">
                    <span class="ex-preview-lbl">Total Return</span>
                    <span class="ex-preview-val" id="totalReturn">—</span>
                </div>
            </div>

            {{-- CALL / PUT buttons --}}
            <div class="ex-action-row">
                <button class="ex-action-btn ex-call" id="btnCall" onclick="openTrade('call')">
                    <i class="bi bi-graph-up-arrow"></i>
                    <div class="ex-action-txt">
                        <span class="ex-action-main">{{ __('app.call') }}</span>
                        <span class="ex-action-sub">{{ strtoupper(__('app.up')) }}</span>
                    </div>
                </button>
                <button class="ex-action-btn ex-put" id="btnPut" onclick="openTrade('put')">
                    <div class="ex-action-txt">
                        <span class="ex-action-main">{{ __('app.put') }}</span>
                        <span class="ex-action-sub">{{ strtoupper(__('app.down')) }}</span>
                    </div>
                    <i class="bi bi-graph-down-arrow"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- ═══ SIGNALS LINK ═══ --}}
    @php $coinSlug = strtolower(str_replace(['USDT','USD'], '', $coin)); @endphp
    <div class="ex-signals-wrap">
        <a href="{{ route('member.invest.coin', ['coin' => $coinSlug]) }}" class="ex-signals-card">
            <div class="ex-signals-glow"></div>
            <div class="ex-signals-body">
                <div class="ex-signals-ico">
                    <i class="bi bi-broadcast-pin"></i>
                    <span class="ex-signals-pulse"></span>
                </div>
                <div class="ex-signals-text">
                    <div class="ex-signals-badge">PRO</div>
                    <div class="ex-signals-title">{{ __('app.expert_signals') }}</div>
                    <div class="ex-signals-sub">AI-powered · Real-time analysis</div>
                </div>
            </div>
            <div class="ex-signals-cta">
                <span>Open</span>
                <i class="bi bi-arrow-right"></i>
            </div>
        </a>
    </div>

    {{-- ═══ TRADE HISTORY ═══ --}}
    <div class="ex-hist-hd">
        <span class="ex-hist-title">{{ __('app.trade_history') }}</span>
        <span class="ex-hist-count">{{ $recentTrades->count() }} trades</span>
    </div>
    <div class="ex-hist-list">
        @forelse ($recentTrades as $t)
        <div class="ex-hist-item">
            <div class="ex-hist-accent {{ $t->direction }}"></div>
            <div class="ex-hist-ico {{ $t->result }}">
                <i class="bi bi-arrow-{{ $t->direction === 'call' ? 'up' : 'down' }}-short"></i>
            </div>
            <div class="ex-hist-body">
                <div class="ex-hist-r1">
                    <span class="ex-hist-sym">{{ isset($coins[$t->coin]) ? $coins[$t->coin]['symbol'] : $t->coin }}</span>
                    <span class="ex-hist-dir {{ $t->direction }}">{{ strtoupper($t->direction) }}</span>
                </div>
                <div class="ex-hist-prices">
                    ${{ number_format($t->entry_price, $t->entry_price < 1 ? 6 : 2, '.', ',') }}
                    <i class="bi bi-arrow-right" style="font-size:8px;opacity:.35;margin:0 2px;"></i>
                    ${{ number_format($t->close_price, $t->close_price < 1 ? 6 : 2, '.', ',') }}
                </div>
            </div>
            <div class="ex-hist-rhs">
                <div class="ex-hist-pnl {{ $t->result }}">{{ $t->result === 'win' ? '+' : '' }}${{ number_format($t->profit_loss, 2) }}</div>
                <div class="ex-hist-date">{{ $t->closed_at->format('d M H:i') }}</div>
            </div>
        </div>
        @empty
        <div class="ex-hist-empty">
            <i class="bi bi-clock-history"></i>
            <p>{{ __('app.no_trades_yet') }}</p>
        </div>
        @endforelse
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
.ex-chart { background: #0d1117; border-bottom: 1px solid var(--bd); }

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

/* ── Info strip ── */
.ex-info-strip {
    display: flex; align-items: center;
    padding: 10px 14px;
    background: var(--panel);
    border-bottom: 1px solid var(--bd);
    border-top: 1px solid var(--bd);
    margin-top: 10px;
}
.ex-info-item { flex: 1; }
.ex-info-div { width: 1px; height: 24px; background: var(--bd); margin: 0 10px; }
.ex-info-lbl { display: block; color: var(--t2); font-size: 10px; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 2px; }
.ex-info-val { color: var(--t1); font-size: 13px; font-weight: 600; font-variant-numeric: tabular-nums; }

/* ── Order section ── */
.ex-order-section {
    background: var(--panel);
    padding: 14px;
    border-bottom: 1px solid var(--bd);
}
.ex-field-label {
    color: var(--t2); font-size: 10px; font-weight: 600;
    text-transform: uppercase; letter-spacing: .6px;
    margin-bottom: 8px;
}

/* Amount row */
.ex-amount-row { display: flex; gap: 8px; margin-bottom: 12px; }
.ex-amount-box {
    flex: 1; display: flex; align-items: center;
    background: var(--card);
    border: 1px solid var(--bd);
    border-radius: var(--r);
    transition: border-color .15s;
}
.ex-amount-box:focus-within { border-color: var(--blue); }
.ex-amount-currency {
    color: var(--t2); font-size: 11px; font-weight: 700;
    padding: 0 10px; white-space: nowrap;
    border-right: 1px solid var(--bd);
    height: 100%; display: flex; align-items: center;
    background: rgba(255,255,255,.02);
}
.ex-amount-divider { width: 1px; }
.ex-amount-input {
    flex: 1; background: none; border: none; outline: none;
    color: var(--t1); font-size: 17px; font-weight: 600;
    padding: 12px 10px;
    font-variant-numeric: tabular-nums;
}
.ex-amount-input::placeholder { color: var(--t3); font-size: 14px; font-weight: 400; }
.ex-amount-input::-webkit-outer-spin-button,
.ex-amount-input::-webkit-inner-spin-button { -webkit-appearance: none; }
.ex-max-btn {
    background: rgba(24,144,255,.08); border: 1px solid rgba(24,144,255,.25);
    border-radius: var(--r); color: var(--blue);
    font-size: 11px; font-weight: 700; padding: 0 14px;
    cursor: pointer; letter-spacing: .5px; white-space: nowrap;
    transition: all .15s;
}
.ex-max-btn:hover { background: rgba(24,144,255,.16); }

/* Slider */
.ex-slider-wrap { margin-bottom: 12px; }
.ex-slider {
    width: 100%; height: 3px; -webkit-appearance: none;
    background: var(--bd); border-radius: 2px; outline: none;
    cursor: pointer; margin-bottom: 8px;
}
.ex-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 16px; height: 16px; border-radius: 50%;
    background: var(--blue);
    border: 2px solid #0b0e11;
    box-shadow: 0 0 0 2px rgba(24,144,255,.3);
}
.ex-slider-ticks {
    display: flex; justify-content: space-between;
}
.ex-slider-tick {
    color: var(--t3); font-size: 10px; cursor: pointer;
    transition: color .15s;
}
.ex-slider-tick:hover { color: var(--blue); }

/* Quick grid */
.ex-quick-grid {
    display: grid; grid-template-columns: repeat(6,1fr); gap: 5px;
    margin-bottom: 12px;
}
.ex-qbtn {
    padding: 6px 2px;
    background: var(--card); border: 1px solid var(--bd);
    border-radius: 4px;
    color: var(--t2); font-size: 11px; font-weight: 600;
    cursor: pointer; transition: all .15s; text-align: center;
}
.ex-qbtn:hover {
    background: rgba(24,144,255,.08);
    border-color: rgba(24,144,255,.3);
    color: var(--blue);
}

/* Preview box */
.ex-preview-box {
    background: var(--card); border: 1px solid var(--bd);
    border-radius: var(--r); padding: 10px 12px;
    margin-bottom: 14px;
}
.ex-preview-row { display: flex; align-items: center; justify-content: space-between; }
.ex-preview-sep { height: 1px; background: var(--bd); margin: 7px 0; }
.ex-preview-lbl { color: var(--t2); font-size: 11px; }
.ex-preview-val { color: var(--t1); font-size: 13px; font-weight: 600; font-variant-numeric: tabular-nums; }

/* Action buttons */
.ex-action-row { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.ex-action-btn {
    display: flex; align-items: center; gap: 8px;
    justify-content: center;
    padding: 14px 10px; border: none; border-radius: var(--r);
    cursor: pointer; transition: filter .15s, transform .1s;
    position: relative; overflow: hidden;
}
.ex-action-btn:active { transform: scale(.97); }
.ex-action-btn:disabled { opacity: .35; cursor: not-allowed; transform: none; }
.ex-action-btn i { font-size: 24px; line-height: 1; }
.ex-action-txt { text-align: left; }
.ex-action-main { display: block; font-size: 16px; font-weight: 800; letter-spacing: .1px; }
.ex-action-sub  { display: block; font-size: 9px; font-weight: 600; opacity: .7; letter-spacing: 1px; text-transform: uppercase; }
.ex-call {
    background: linear-gradient(160deg, #0a8f5a 0%, #0ecb81 100%);
    color: #001a0e;
}
.ex-call:hover { filter: brightness(1.1); }
.ex-put {
    background: linear-gradient(160deg, #b31a2d 0%, #f6465d 100%);
    color: #fff;
}
.ex-put:hover { filter: brightness(1.08); }

/* ── Signals ── */
.ex-signals-wrap { padding: 10px 12px 6px; }
.ex-signals-card {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 14px;
    background: linear-gradient(135deg, #1a1535 0%, #131820 60%, #0f1a2e 100%);
    border: 1px solid rgba(139,92,246,.25);
    border-radius: 10px;
    text-decoration: none;
    position: relative; overflow: hidden;
    transition: border-color .2s, transform .15s;
}
.ex-signals-card:hover {
    border-color: rgba(139,92,246,.5);
    transform: translateY(-1px);
}
.ex-signals-glow {
    position: absolute; top: -20px; left: -20px;
    width: 120px; height: 80px;
    background: radial-gradient(ellipse, rgba(139,92,246,.18) 0%, transparent 70%);
    pointer-events: none;
}
.ex-signals-body { display: flex; align-items: center; gap: 12px; position: relative; z-index: 1; }
.ex-signals-ico {
    width: 42px; height: 42px; flex-shrink: 0;
    background: rgba(139,92,246,.15);
    border: 1px solid rgba(139,92,246,.3);
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    position: relative;
}
.ex-signals-ico i { color: #a78bfa; font-size: 18px; }
.ex-signals-pulse {
    position: absolute; top: -3px; right: -3px;
    width: 10px; height: 10px;
    background: var(--buy); border-radius: 50%;
    border: 2px solid #131820;
    animation: signalPulse 2s ease-in-out infinite;
}
@keyframes signalPulse {
    0%,100% { box-shadow: 0 0 0 0 rgba(14,203,129,.5); }
    50%      { box-shadow: 0 0 0 5px rgba(14,203,129,0); }
}
.ex-signals-text { flex: 1; }
.ex-signals-badge {
    display: inline-block;
    background: linear-gradient(90deg, #7c3aed, #a78bfa);
    color: #fff; font-size: 9px; font-weight: 800;
    padding: 1px 6px; border-radius: 3px;
    letter-spacing: 1px; margin-bottom: 3px;
}
.ex-signals-title { color: var(--t1); font-size: 13px; font-weight: 700; margin-bottom: 1px; }
.ex-signals-sub   { color: var(--t2); font-size: 10px; }
.ex-signals-cta {
    display: flex; align-items: center; gap: 5px;
    color: #a78bfa; font-size: 11px; font-weight: 600;
    position: relative; z-index: 1;
    background: rgba(139,92,246,.1);
    border: 1px solid rgba(139,92,246,.2);
    border-radius: 6px; padding: 6px 10px;
    transition: background .15s;
}
.ex-signals-cta i { font-size: 13px; }
.ex-signals-card:hover .ex-signals-cta { background: rgba(139,92,246,.2); }

/* ── History ── */
.ex-hist-hd {
    display: flex; align-items: center; justify-content: space-between;
    padding: 12px 14px 6px;
}
.ex-hist-title { color: var(--t2); font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; }
.ex-hist-count { color: var(--t3); font-size: 10px; }
.ex-hist-list {
    margin: 0 12px 80px;
    background: var(--panel); border: 1px solid var(--bd);
    border-radius: var(--r); overflow: hidden;
}
.ex-hist-item {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 12px 10px 0;
    border-bottom: 1px solid rgba(255,255,255,.03);
    position: relative;
}
.ex-hist-item:last-child { border-bottom: none; }
.ex-hist-accent {
    width: 2px; height: 100%; position: absolute; left: 0; top: 0;
}
.ex-hist-accent.call { background: var(--buy); }
.ex-hist-accent.put  { background: var(--sell); }
.ex-hist-ico {
    width: 30px; height: 30px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 19px; flex-shrink: 0; margin-left: 10px;
}
.ex-hist-ico.win  { background: rgba(14,203,129,.08); color: var(--buy); }
.ex-hist-ico.lose { background: rgba(246,70,93,.08);  color: var(--sell); }
.ex-hist-body { flex: 1; min-width: 0; }
.ex-hist-r1 { display: flex; align-items: center; gap: 6px; margin-bottom: 3px; }
.ex-hist-sym { color: var(--t1); font-size: 12px; font-weight: 600; }
.ex-hist-dir {
    font-size: 9px; font-weight: 700; padding: 1px 5px;
    border-radius: 3px; text-transform: uppercase;
}
.ex-hist-dir.call { background: rgba(14,203,129,.1); color: var(--buy); }
.ex-hist-dir.put  { background: rgba(246,70,93,.08); color: var(--sell); }
.ex-hist-prices { color: var(--t2); font-size: 10px; font-variant-numeric: tabular-nums; }
.ex-hist-rhs { text-align: right; flex-shrink: 0; }
.ex-hist-pnl { font-size: 13px; font-weight: 600; font-variant-numeric: tabular-nums; margin-bottom: 2px; }
.ex-hist-pnl.win  { color: var(--buy); }
.ex-hist-pnl.lose { color: var(--sell); }
.ex-hist-date { color: var(--t3); font-size: 10px; }
.ex-hist-empty { padding: 30px; text-align: center; color: var(--t3); }
.ex-hist-empty i { font-size: 24px; display: block; margin-bottom: 8px; }
.ex-hist-empty p { margin: 0; font-size: 12px; }

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
    document.getElementById('tradeBalance').textContent  = '$' + parseFloat(result.new_balance).toFixed(2);
    tradeBalance = parseFloat(result.new_balance);
    document.getElementById('resultOverlay').style.display = 'flex';
}
function dismissResult() {
    document.getElementById('resultOverlay').style.display = 'none';
    window.location.reload();
}

/* ─ Boot ─ */
document.addEventListener('DOMContentLoaded', () => {
    fetchPrice();
    priceInterval = setInterval(fetchPrice, 5000);
    if (activeTrade) startTimer();
    // Init slider fill
    sliderEl()?.dispatchEvent(new Event('input'));
});
document.addEventListener('visibilitychange', () => {
    if (document.hidden) { clearInterval(priceInterval); }
    else { fetchPrice(); priceInterval = setInterval(fetchPrice, 5000); }
});
</script>
@endpush