@extends('member.layouts.app')
@section('content')
<div class="scrollable-content">
<div class="cp-root">

    {{-- HEADER --}}
    <div class="cp-topbar">
        <button onclick="openCoinPopup()" class="cp-market-btn">
            <div class="cp-market-dot" style="background:{{ $coinInfo['color'] }};box-shadow:0 0 8px {{ $coinInfo['color'] }};"></div>
            <span class="cp-market-sym">{{ $coinInfo['symbol'] }}</span>
            <i class="bi bi-chevron-down cp-market-ico"></i>
        </button>
        <span class="cp-brand">SIGNAL TRADING</span>
        @if(count($openSignals) > 0)
        <div class="cp-live-chip">
            <span class="cp-live-blink"></span>
            <span>{{ count($openSignals) }} LIVE</span>
        </div>
        @else
        <div class="cp-live-chip-ghost"></div>
        @endif
    </div>

    {{-- COIN POPUP --}}
<div id="coinPopupOverlay" class="cp-overlay" onclick="closeCoinPopup()" style="display:none;">
    <div class="cp-drawer" onclick="event.stopPropagation()">
        <div class="cp-drawer-handle"></div>
        <div class="cp-drawer-hd">
            <div class="cp-drawer-title">Select Market</div>
            <button class="cp-drawer-x" onclick="closeCoinPopup()"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="cp-drawer-search">
            <i class="bi bi-search"></i>
            <input type="text" id="cpMarketSearch" placeholder="Search..." oninput="cpFilterMarkets(this.value)">
        </div>
        <div class="cp-drawer-list" id="cpMarketList">
            @php
                $categories = [
                    'crypto' => ['label' => 'Cryptocurrency', 'icon' => 'bi-currency-bitcoin'],
                    'fiat'   => ['label' => 'Fiat Currency',  'icon' => 'bi-currency-exchange'],
                    'metals' => ['label' => 'Commodities',    'icon' => 'bi-gem'],
                ];
                $cryptoKeys = ['BTCUSDT','ETHUSDT','XRPUSDT','LINKUSDT','DOTUSDT','DOGEUSDT','BCHUSDT','FILUSDT','LTCUSDT','ZECUSDT','DASHUSDT'];
                $metalKeys  = ['XAGUSD','XAUUSD','XPTUSD'];
            @endphp

            @foreach($categories as $catKey => $catInfo)
                @php
                    $catCoins = collect($allCoins)->filter(function($info, $sym) use ($catKey, $cryptoKeys, $metalKeys) {
                        if ($catKey === 'crypto') return in_array($sym, $cryptoKeys);
                        if ($catKey === 'metals') return in_array($sym, $metalKeys);
                        return !in_array($sym, $cryptoKeys) && !in_array($sym, $metalKeys);
                    });
                @endphp
                @if($catCoins->count())
                <div class="cp-mkt-cat"><i class="bi {{ $catInfo['icon'] }}"></i> {{ strtoupper($catInfo['label']) }}</div>
                @foreach($catCoins as $symbol => $info)
                <a href="{{ route('member.invest.coin', ['coin' => strtolower($symbol)]) }}"
                   class="cp-mkt {{ $symbol === $coin ? 'cp-mkt-on' : '' }}"
                   data-name="{{ strtolower($info['name']) }} {{ strtolower($info['symbol']) }}">
                    <div class="cp-mkt-icon" style="background:{{ $info['color'] }}22;border:1px solid {{ $info['color'] }}55;">
                        <i class="{{ $info['icon'] ?? 'bi bi-currency-bitcoin' }}" style="color:{{ $info['color'] }};"></i>
                    </div>
                    <div class="cp-mkt-txt">
                        <span class="cp-mkt-s">{{ $info['symbol'] }}</span>
                        <span class="cp-mkt-n">{{ $info['name'] }}</span>
                    </div>
                    @if($signalCounts[$symbol] > 0)
                        <span class="cp-mkt-sig">{{ $signalCounts[$symbol] }}</span>
                    @endif
                    @if($symbol === $coin)
                        <i class="bi bi-check2-circle cp-mkt-check"></i>
                    @endif
                </a>
                @endforeach
                @endif
            @endforeach
        </div>
    </div>
</div>

    {{-- ALERTS --}}
    @if(!auth()->user()->canJoinSignal())
    <div class="cp-notice"><i class="bi bi-shield-x"></i> <span>{!! __('app.minimum_balance_required') !!}</span> <a href="{{ route('member.balance.transfer') }}" class="cp-notice-a">Transfer →</a></div>
    @endif
    @if(session('success'))
    <div class="cp-flash cp-flash-ok" id="cp-flash"><i class="bi bi-check2-circle"></i> {{ session('success') }} <button onclick="this.parentElement.remove()">×</button></div>
    @endif
    @if(session('error'))
    <div class="cp-flash cp-flash-err" id="cp-flash"><i class="bi bi-x-circle"></i> {{ session('error') }} <button onclick="this.parentElement.remove()">×</button></div>
    @endif

    {{-- CHART --}}
    <div class="cp-chart-box">
        <iframe src="https://www.tradingview.com/widgetembed/?symbol={{ $coinInfo['tradingview_symbol'] }}&interval=60&theme=dark&style=1&locale=en&toolbar_bg=060a12&enable_publishing=false&hidesidetoolbar=1&allow_symbol_change=0&show_popup_button=0&details=0&calendar=0&studies=%5B%5D"
            style="width:100%;height:250px;border:none;display:block;" frameborder="0" allowtransparency="true" scrolling="no">
        </iframe>
    </div>

    {{-- TABS --}}
    <div class="cp-tabs">
        <button class="cp-tab active" data-tab="signals">
            <i class="bi bi-reception-4"></i> {{ __('app.trading_signals') }}
        </button>
        <button class="cp-tab" data-tab="history">
            <i class="bi bi-archive"></i> {{ __('app.historical_orders') }}
        </button>
    </div>

    {{-- SIGNALS PANEL --}}
    <div class="cp-panel" id="tab-signals">

        @forelse($openSignals as $signal)
        @php
            $hasJoined = in_array($signal->id, $joinedSignalIds);
            $betAmountPreview = $signal->betAmountPreview ?? 0;
            $hasSufficientBalance = $signal->bet_type == 'percentage'
                ? auth()->user()->canJoinSignal()
                : auth()->user()->getAvailableTradeBalance() >= $betAmountPreview;
        @endphp

        <div class="cp-sig-wrap {{ $hasJoined ? 'joined' : '' }} {{ !$hasSufficientBalance && !$hasJoined ? 'insuf' : '' }}">

            {{-- Left accent --}}
            <div class="cp-sig-accent">
                <div class="cp-sig-accent-line"></div>
            </div>

            {{-- Main body --}}
            <div class="cp-sig-body">
                <div class="cp-sig-row-top">
                    <div class="cp-sig-pulse-badge">
                        <span class="cp-dot-live"></span>LIVE
                    </div>
                    <div class="cp-sig-name">{{ $signal->title }}</div>
                </div>

                @if($signal->description)
                <div class="cp-sig-desc">{{ $signal->description }}</div>
                @endif

                {{-- Stats row --}}
                <div class="cp-sig-stats-row">
                    <div class="cp-sig-stat-block">
                        <div class="cp-sig-stat-key">SALDO</div>
                        <div class="cp-sig-stat-figure">
                            {{ number_format(auth()->user()->trade_balance, 2) }}
                            <em>USDT</em>
                        </div>
                    </div>
                    <div class="cp-sig-stat-arrow">
                        <i class="bi bi-arrow-right"></i>
                    </div>
                    <div class="cp-sig-stat-block right">
                        <div class="cp-sig-stat-key">TARUHAN</div>
                        <div class="cp-sig-stat-figure gold">
                            {{ number_format($betAmountPreview, 2) }}
                            <em>USDT</em>
                        </div>
                    </div>
                </div>

                {{-- Action --}}
                @if($hasJoined)
                <div class="cp-sig-joined-strip">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ __('app.you_have_joined') }}
                </div>
                @elseif($hasSufficientBalance)
                <form action="{{ route('member.signals.join', $signal->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="cp-sig-join-btn"
                        onclick="return confirm('{{ __('app.join_signal_confirmation', ['bet_amount' => number_format($betAmountPreview, 2)]) }}')">
                        <span class="cp-join-shine"></span>
                        IKUT SIGNAL
                        <i class="bi bi-arrow-right-circle-fill"></i>
                    </button>
                </form>
                @else
                <div class="cp-sig-insuf-strip">
                    <span><i class="bi bi-exclamation-triangle-fill"></i> {{ __('app.insufficient_balance_message') }}</span>
                    <a href="{{ route('member.balance.transfer') }}" class="cp-insuf-go">{{ __('app.transfer_now') }}</a>
                </div>
                @endif

            </div>
        </div>

        @empty
        <div class="cp-void">
            <div class="cp-void-hex">
                <i class="bi bi-reception-1"></i>
            </div>
            <div class="cp-void-text">Tidak ada sinyal aktif untuk {{ $coinInfo['name'] }}</div>
            <div class="cp-void-sub">Periksa kembali nanti</div>
        </div>
        @endforelse
    </div>

    {{-- HISTORY PANEL (semua coin) --}}
    <div class="cp-panel" id="tab-history" style="display:none;">

        @if($totalJoinedThisCoin > 0)
        <div class="cp-scorecard">
            <div class="cp-sc-cell">
                <div class="cp-sc-n">{{ $totalJoinedThisCoin }}</div>
                <div class="cp-sc-l">ORDER</div>
            </div>
            <div class="cp-sc-vr"></div>
            <div class="cp-sc-cell">
                <div class="cp-sc-n {{ $winRateThisCoin >= 50 ? 'g' : 'r' }}">{{ number_format($winRateThisCoin, 1) }}%</div>
                <div class="cp-sc-l">WIN RATE</div>
            </div>
            <div class="cp-sc-vr"></div>
            <div class="cp-sc-cell">
                <div class="cp-sc-n {{ $totalProfitLossThisCoin >= 0 ? 'g' : 'r' }}">{{ $totalProfitLossThisCoin >= 0 ? '+' : '' }}{{ number_format($totalProfitLossThisCoin, 0) }}</div>
                <div class="cp-sc-l">P&L</div>
            </div>
            <div class="cp-sc-vr"></div>
            <div class="cp-sc-cell">
                <div class="cp-sc-n a">{{ number_format($totalFeesThisCoin, 0) }}</div>
                <div class="cp-sc-l">FEE</div>
            </div>
        </div>
        @endif

        @forelse($historyForThisCoin as $participant)
        @php
            $signal      = $participant->signal;
            $isPending   = $signal->status !== 'settled' || $signal->result === null;
            $isWin       = $signal->result === 'win';
            $isSettled   = $participant->status === 'settled';
            $netResult   = ($participant->profit_loss ?? 0) - ($participant->fee_amount ?? 0);
            $adminChoice = strtolower($signal->admin_choice ?? '');
            $stateKey    = $isPending ? 'p' : ($isWin ? 'w' : 'l');

            $openPrice   = $signal->entry_price ?? null;
            $closePrice  = $signal->target_price ?? null;
            $priceMoved  = ($openPrice !== null && $closePrice !== null) ? ($closePrice - $openPrice) : null;
            $priceUpPct  = ($priceMoved !== null && $openPrice > 0) ? ($priceMoved / $openPrice) * 100 : null;

            // Riwayat sekarang lintas coin — ambil info coin dari signal tiap baris,
            // bukan dari coin yang sedang aktif dipilih di popup ($coinInfo).
            $rowCoinInfo = $allCoins[$signal->coin] ?? $coinInfo;
        @endphp

        <div class="cp-tk cp-tk-{{ $stateKey }}">
            {{-- Head: asset + status --}}
            <div class="cp-tk-head">
                <div class="cp-tk-asset">
                    <span class="cp-tk-asset-dot" style="background:{{ $rowCoinInfo['color'] }};box-shadow:0 0 6px {{ $rowCoinInfo['color'] }};"></span>
                    <span class="cp-tk-asset-sym">{{ $rowCoinInfo['symbol'] }}</span>
                    <span class="cp-tk-asset-n">{{ $rowCoinInfo['name'] }}</span>
                </div>
                <div class="cp-tk-badge cp-tk-badge-{{ $stateKey }}">
                    @if($isPending) <i class="bi bi-hourglass-split"></i> PENDING
                    @elseif($isWin) <i class="bi bi-check-circle-fill"></i> WIN
                    @else <i class="bi bi-x-circle-fill"></i> LOSS
                    @endif
                </div>
            </div>

            <div class="cp-tk-title">{{ $signal->title }}</div>

            {{-- Price gauge: Entry -> Target (hidden while pending — belum settle) --}}
            @if(!$isPending)
            <div class="cp-tk-gauge">
                <div class="cp-tk-gauge-pt">
                    <div class="cp-tk-gauge-lbl">ENTRY</div>
                    <div class="cp-tk-gauge-val">{{ $openPrice !== null ? number_format($openPrice, 2) : '--' }}</div>
                </div>
                <div class="cp-tk-gauge-track">
                    <div class="cp-tk-gauge-line {{ $priceMoved !== null ? ($priceMoved >= 0 ? 'up' : 'down') : '' }}"></div>
                    <i class="bi bi-caret-right-fill cp-tk-gauge-chev {{ $priceMoved !== null ? ($priceMoved >= 0 ? 'up' : 'down') : '' }}"></i>
                </div>
                <div class="cp-tk-gauge-pt right">
                    <div class="cp-tk-gauge-lbl">TARGET</div>
                    <div class="cp-tk-gauge-val {{ $priceMoved !== null ? ($priceMoved >= 0 ? 'cp-val-g' : 'cp-val-r') : '' }}">{{ $closePrice !== null ? number_format($closePrice, 2) : '--' }}</div>
                </div>
            </div>
            @else
            <div class="cp-tk-pending-note">
                <i class="bi bi-hourglass-split"></i> Waiting
            </div>
            @endif
            <div class="cp-tk-perf"></div>

            {{-- Stat grid --}}
            <div class="cp-tk-stats">
                <div class="cp-tk-stat">
                    <div class="cp-tk-stat-k">BET</div>
                    <div class="cp-tk-stat-v">{{ number_format($participant->bet_amount, 2) }}</div>
                </div>
                <div class="cp-tk-stat-vr"></div>
                <div class="cp-tk-stat">
                    <div class="cp-tk-stat-k">PROFIT/LOSS</div>
                    <div class="cp-tk-stat-v {{ $isPending ? '' : ($netResult >= 0 ? 'cp-val-g' : 'cp-val-r') }}">
                        @if($isPending) ~
                        @elseif($isSettled) {{ ($netResult >= 0 ? '+' : '') . number_format($netResult, 2) }}
                        @else —
                        @endif
                    </div>
                </div>
                <div class="cp-tk-stat-vr"></div>
                <div class="cp-tk-stat">
                    <div class="cp-tk-stat-k">RATE</div>
                    <div class="cp-tk-stat-v">{{ ($signal->rate_of_return ?? 0) > 0 ? number_format($signal->rate_of_return, 1) . '%' : '--' }}</div>
                </div>
            </div>

            {{-- Meta footer --}}
            <div class="cp-tk-meta">
                <span class="cp-tk-meta-time">
                    <i class="bi bi-clock-history"></i>
                    {{ $signal->opened_at ? $signal->opened_at->format('d M · H:i') : '--' }}
                    –
                    {{ $signal->closed_at ? $signal->closed_at->format('H:i') : '~' }}
                </span>
                <span class="cp-tk-meta-dir {{ $adminChoice === 'call' ? 'cp-val-g' : ($adminChoice === 'put' ? 'cp-val-r' : '') }}">
                    @if($isPending) —
                    @elseif($adminChoice === 'call') <i class="bi bi-graph-up-arrow"></i> CALL
                    @elseif($adminChoice === 'put') <i class="bi bi-graph-down-arrow"></i> PUT
                    @else N/A
                    @endif
                </span>
            </div>
        </div>

        @empty
        <div class="cp-void">
            <div class="cp-void-hex"><i class="bi bi-archive"></i></div>
            <div class="cp-void-text">Belum ada riwayat order</div>
            <div class="cp-void-sub">Ikuti sinyal untuk mulai</div>
        </div>
        @endforelse

        @if($historyForThisCoin->hasPages())
<div class="cp-pages">
    <nav class="cpp-nav" role="navigation" aria-label="Pagination">
        <ul class="cpp-list">
            @php
                $current = $historyForThisCoin->currentPage();
                $last    = $historyForThisCoin->lastPage();
                $onEachSide = 1;
            @endphp
            @for ($i = 1; $i <= $last; $i++)
                @if ($i == 1 || $i == $last || ($i >= $current - $onEachSide && $i <= $current + $onEachSide))
                    @if ($i == $current)
                        <li class="cpp-item active"><span class="cpp-link">{{ $i }}</span></li>
                    @else
                        <li class="cpp-item"><a href="{{ $historyForThisCoin->url($i) }}" class="cpp-link">{{ $i }}</a></li>
                    @endif
                @elseif ($i == $current - $onEachSide - 1 || $i == $current + $onEachSide + 1)
                    <li class="cpp-item disabled"><span class="cpp-link cpp-dots">...</span></li>
                @endif
            @endfor
        </ul>
    </nav>
</div>
@endif
    

    <div style="height:40px;"></div>
</div>
</div>

{{-- CONFIRM MODAL --}}
@if($unjoinedOpenSignal)
<div class="cp-cm-bg" id="cp-bs-bg" onclick="cpBsClose()"></div>
<div class="cp-cm" id="cp-bs">
    <div class="cp-cm-hd">
        <span>Confirm to follow the order</span>
        <button onclick="cpBsClose()" class="cp-cm-x"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="cp-cm-amount">
        Order amount
        <span class="cp-cm-figure">{{ number_format($unjoinedOpenSignal->betAmountPreview, 2) }} <em>USDT</em></span>
    </div>
    <form action="{{ route('member.signals.join', $unjoinedOpenSignal->id) }}" method="POST">
        @csrf
        <button type="submit" class="cp-cm-go">Sure</button>
    </form>
</div>
@endif

@push('styles')
<style>
/* ROOT */
.cp-root { background: transparent; min-height: 100vh; }

/* TOPBAR */
.cp-topbar {
    display: flex; align-items: center; justify-content: space-between;
    padding: 12px 16px;
    background: rgba(6,10,18,0.9);
    border-bottom: 1px solid rgba(255,255,255,0.05);
}
.cp-market-btn {
    display: flex; align-items: center; gap: 7px;
    background: none; border: 1px solid rgba(255,255,255,0.1);
    border-radius: 8px; padding: 7px 11px;
    cursor: pointer; color: inherit; transition: border-color 0.2s;
    flex-shrink: 0;
}
.cp-market-btn:hover { border-color: rgba(255,255,255,0.25); }
.cp-market-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.cp-market-sym  { color: #fff; font-size: 13px; font-weight: 800; white-space: nowrap; }
.cp-market-ico  { color: rgba(255,255,255,0.3); font-size: 11px; }
.cp-brand {
    font-size: 10px; font-weight: 800; letter-spacing: 3px;
    color: rgba(255,255,255,0.2); white-space: nowrap;
}
.cp-live-chip {
    display: flex; align-items: center; gap: 5px;
    background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.2);
    border-radius: 20px; padding: 5px 11px;
    color: #22c55e; font-size: 10px; font-weight: 800; letter-spacing: 0.5px;
    white-space: nowrap; flex-shrink: 0;
}
.cp-live-chip-ghost { width: 72px; flex-shrink: 0; }
.cp-live-blink {
    width: 6px; height: 6px; border-radius: 50%; background: #22c55e; flex-shrink: 0;
    animation: cpBlink 1.2s ease infinite;
}
@keyframes cpBlink { 0%,100%{opacity:1} 50%{opacity:0.2} }

/* OVERLAY / DRAWER */
.cp-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.8); backdrop-filter: blur(8px);
    z-index: 9999;
    display: flex; align-items: flex-end; justify-content: center;
}
.cp-drawer {
    background: #0d1420;
    border-top: 1px solid rgba(255,255,255,0.08);
    border-radius: 20px 20px 0 0;
    width: 100%; max-width: 480px;
    max-height: 78vh; overflow: hidden;
    display: flex; flex-direction: column;
    padding-bottom: env(safe-area-inset-bottom, 10px);
    animation: cpDrawerUp 0.25s ease;
}
@keyframes cpDrawerUp { from{transform:translateY(40px);opacity:0} to{transform:translateY(0);opacity:1} }

.cp-drawer-handle {
    width: 34px; height: 4px; background: rgba(255,255,255,0.15);
    border-radius: 3px; margin: 10px auto 2px; flex-shrink: 0;
}

.cp-drawer-hd {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 18px 12px;
    flex-shrink: 0;
}
.cp-drawer-title { color: #fff; font-size: 17px; font-weight: 800; }
.cp-drawer-x {
    width: 28px; height: 28px; border-radius: 50%;
    background: rgba(255,255,255,0.06); border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    color: rgba(255,255,255,0.5); font-size: 13px;
    transition: background .15s;
}
.cp-drawer-x:hover { background: rgba(255,255,255,0.12); color: #fff; }

.cp-drawer-search {
    display: flex; align-items: center; gap: 9px;
    margin: 0 16px 10px;
    padding: 10px 14px;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 10px;
    flex-shrink: 0;
}
.cp-drawer-search i { color: rgba(255,255,255,0.3); font-size: 13px; }
.cp-drawer-search input {
    flex: 1; background: none; border: none; outline: none;
    color: #fff; font-size: 13px;
}
.cp-drawer-search input::placeholder { color: rgba(255,255,255,0.25); }

.cp-drawer-list { overflow-y: auto; flex: 1; padding-bottom: 8px; }
.cp-drawer-list::-webkit-scrollbar { width: 3px; }
.cp-drawer-list::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 3px; }

.cp-mkt-cat {
    display: flex; align-items: center; gap: 6px;
    padding: 12px 18px 6px;
    color: rgba(255,255,255,0.25); font-size: 10px; font-weight: 800;
    letter-spacing: 1.5px; text-transform: uppercase;
}
.cp-mkt-cat i { font-size: 11px; }

.cp-mkt {
    display: flex; align-items: center; gap: 12px;
    padding: 11px 18px;
    text-decoration: none;
    border-bottom: 1px solid rgba(255,255,255,0.03);
    border-left: 3px solid transparent;
    transition: background 0.15s;
}
.cp-mkt:hover { background: rgba(255,255,255,0.03); }
.cp-mkt-on {
    background: rgba(24,144,255,0.08);
    border-left-color: #1890ff;
}

.cp-mkt-icon {
    width: 34px; height: 34px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.cp-mkt-icon i { font-size: 15px; }

.cp-mkt-txt { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 1px; }
.cp-mkt-s { color: #fff; font-size: 14px; font-weight: 700; }
.cp-mkt-n { color: rgba(255,255,255,0.35); font-size: 11.5px; }

.cp-mkt-sig {
    background: rgba(34,197,94,0.12); color: #22c55e;
    font-size: 10px; font-weight: 700;
    padding: 3px 9px; border-radius: 20px; flex-shrink: 0;
}
.cp-mkt-check { color: #1890ff; font-size: 18px; flex-shrink: 0; margin-left: 2px; }
/* ALERTS */
.cp-notice {
    padding: 10px 16px; font-size: 12px; color: #f87171;
    background: rgba(239,68,68,0.06); border-bottom: 1px solid rgba(239,68,68,0.12);
    display: flex; align-items: center; gap: 8px;
    line-height: 1.4;
}
.cp-notice i { flex-shrink: 0; font-size: 14px; }
.cp-notice span { flex: 1; }
.cp-notice-a { color: #fff; font-weight: 700; text-decoration: none; white-space: nowrap; flex-shrink: 0; margin-left: auto; }
.cp-flash {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 16px; font-size: 13px; border-bottom: 1px solid;
}
.cp-flash button { background: none; border: none; margin-left: auto; cursor: pointer; font-size: 16px; opacity: 0.4; color: inherit; }
.cp-flash-ok  { background: rgba(34,197,94,0.06);  border-color: rgba(34,197,94,0.15);  color: #4ade80; }
.cp-flash-err { background: rgba(239,68,68,0.06);  border-color: rgba(239,68,68,0.15);  color: #f87171; }

/* CHART */
.cp-chart-box { border-bottom: 1px solid rgba(255,255,255,0.05); }

/* TABS */
.cp-tabs {
    display: grid; grid-template-columns: 1fr 1fr;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    position: sticky; top: 0; z-index: 20;
    background: rgba(6,10,18,0.95); backdrop-filter: blur(12px);
}
.cp-tab {
    padding: 13px 8px; border: none; background: none;
    color: rgba(255,255,255,0.3); font-size: 11px; font-weight: 700;
    letter-spacing: 0.5px; cursor: pointer;
    border-bottom: 2px solid transparent;
    display: flex; align-items: center; justify-content: center; gap: 7px;
    transition: all 0.2s; text-transform: uppercase;
}
.cp-tab i { font-size: 13px; }
.cp-tab.active { color: #fff; border-bottom-color: #fff; }

/* PANEL */
.cp-panel { padding: 20px 0 8px; }

/* ══════════════════════════════════════════
   SIGNAL ITEM
══════════════════════════════════════════ */
.cp-sig-wrap {
    display: flex;
    margin: 0 14px 16px;
    border-radius: 14px;
    overflow: hidden;
    background: linear-gradient(135deg, #0c1424 0%, #080f1d 100%);
    box-shadow: 0 2px 20px rgba(0,0,0,0.4);
    border: 1px solid rgba(255,255,255,0.06);
    position: relative;
}
.cp-sig-wrap::before {
    content: '';
    position: absolute; inset: 0; border-radius: 14px;
    background: radial-gradient(ellipse at top left, rgba(99,179,237,0.06) 0%, transparent 60%);
    pointer-events: none;
}
.cp-sig-wrap.joined { opacity: 0.7; }
.cp-sig-wrap.insuf::before {
    background: radial-gradient(ellipse at top left, rgba(239,68,68,0.05) 0%, transparent 60%);
}

.cp-sig-accent {
    width: 4px; flex-shrink: 0;
    background: linear-gradient(180deg, #63b3ed 0%, #3182ce 100%);
    position: relative;
}
.cp-sig-wrap.joined .cp-sig-accent { background: linear-gradient(180deg, #4ade80, #16a34a); }
.cp-sig-wrap.insuf  .cp-sig-accent { background: linear-gradient(180deg, #f87171, #dc2626); }
.cp-sig-accent-line {
    position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%);
    width: 2px; height: 60%;
    background: rgba(255,255,255,0.3); border-radius: 1px;
}

.cp-sig-body { flex: 1; padding: 14px 14px 14px 12px; }

.cp-sig-row-top {
    display: flex; align-items: center; gap: 10px;
    margin-bottom: 8px;
}
.cp-sig-pulse-badge {
    display: flex; align-items: center; gap: 5px;
    font-size: 9px; font-weight: 800; letter-spacing: 1.5px;
    color: #4ade80; flex-shrink: 0;
}
.cp-dot-live {
    width: 6px; height: 6px; border-radius: 50%; background: #4ade80;
    animation: cpLivePop 1.5s ease infinite;
}
@keyframes cpLivePop {
    0%,100% { box-shadow: 0 0 0 0 rgba(74,222,128,0.6); }
    50%      { box-shadow: 0 0 0 5px rgba(74,222,128,0); }
}
.cp-sig-name {
    color: #fff; font-size: 15px; font-weight: 800;
    flex: 1; line-height: 1.2;
}
.cp-sig-desc {
    color: rgba(255,255,255,0.35); font-size: 11px; line-height: 1.5;
    margin-bottom: 12px;
}

.cp-sig-stats-row {
    display: flex; align-items: center;
    background: rgba(0,0,0,0.25);
    border-radius: 10px; padding: 10px 12px;
    margin-bottom: 12px; gap: 8px;
}
.cp-sig-stat-block { flex: 1; }
.cp-sig-stat-block.right { text-align: right; }
.cp-sig-stat-key {
    font-size: 9px; font-weight: 800; letter-spacing: 1.5px;
    color: rgba(255,255,255,0.25); margin-bottom: 4px;
}
.cp-sig-stat-figure {
    color: #fff; font-size: 18px; font-weight: 900;
    font-variant-numeric: tabular-nums;
    line-height: 1;
}
.cp-sig-stat-figure.gold { color: #fbbf24; }
.cp-sig-stat-figure em {
    font-style: normal; font-size: 9px; font-weight: 700;
    color: rgba(255,255,255,0.3); margin-left: 3px;
}
.cp-sig-stat-arrow {
    color: rgba(255,255,255,0.12); font-size: 16px;
    padding: 0 4px;
}

.cp-sig-join-btn {
    width: 100%; padding: 11px 16px;
    background: #fff; border: none; border-radius: 10px;
    color: #060a12; font-size: 13px; font-weight: 900;
    letter-spacing: 0.5px; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    position: relative; overflow: hidden;
    transition: all 0.2s;
}
.cp-sig-join-btn:hover { background: #e2e8f0; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(255,255,255,0.15); }
.cp-join-shine {
    position: absolute; top: 0; left: -60%; width: 40%; height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transform: skewX(-20deg);
    animation: cpShine 2.5s ease infinite 1s;
}
@keyframes cpShine { 0%{left:-60%} 40%,100%{left:120%} }

.cp-sig-joined-strip {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 14px; border-radius: 10px;
    background: rgba(74,222,128,0.08); border: 1px solid rgba(74,222,128,0.15);
    color: #4ade80; font-size: 13px; font-weight: 700;
}
.cp-sig-insuf-strip {
    display: flex; align-items: center; justify-content: space-between; gap: 8px;
    padding: 10px 14px; border-radius: 10px;
    background: rgba(239,68,68,0.07); border: 1px solid rgba(239,68,68,0.15);
    color: #f87171; font-size: 12px;
}
.cp-insuf-go { color: #fff; font-weight: 800; text-decoration: none; white-space: nowrap; }

/* SCORECARD */
.cp-scorecard {
    display: flex; align-items: center;
    margin: 0 14px 16px;
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

/* ══════════════════════════════════════════
   TRADE TICKET — historical order card
══════════════════════════════════════════ */
.cp-tk {
    margin: 0 14px 14px;
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
    color: #fff; font-family: 'JetBrains Mono', monospace;
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

.cp-tk-delta {
    display: flex; align-items: center; gap: 4px;
    font-size: 11px; font-weight: 700; margin-top: 6px;
}

/* Pending state note — shown while entry/target aren't revealed yet */
.cp-tk-pending-note {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 12px;
    background: rgba(251,191,36,0.06);
    border: 1px solid rgba(251,191,36,0.15);
    border-radius: 10px;
    color: #fbbf24; font-size: 12px; font-weight: 600;
}

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
    color: #fff; font-family: 'JetBrains Mono', monospace;
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
    color: rgba(255,255,255,0.4); font-size: 11px; font-weight: 800; letter-spacing: 0.5px;
}

.cp-val-g { color: #4ade80; }
.cp-val-r { color: #f87171; }

/* VOID */
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
.cp-void-sub  { color: rgba(255,255,255,0.15); font-size: 11px; }

/* PAGES */
.cp-pages { padding: 14px; }

/* ══════════════════════════════════════════
   CONFIRM MODAL (polished, center)
══════════════════════════════════════════ */
.cp-cm-bg {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.55); backdrop-filter: blur(5px);
    z-index: 2000;
}
.cp-cm-bg.on { display: block; }

.cp-cm {
    display: none;
    position: fixed; top: 50%; left: 50%;
    transform: translate(-50%, -50%) scale(0.96);
    width: 88%; max-width: 340px;
    background: #fff; border-radius: 18px;
    padding: 22px 22px 18px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.35), 0 4px 16px rgba(0,0,0,0.15);
    z-index: 2001;
    opacity: 0;
    transition: transform 0.22s cubic-bezier(0.34,1.56,0.64,1), opacity 0.2s ease;
}
.cp-cm.on { display: block; }
.cp-cm.on.show { transform: translate(-50%, -50%) scale(1); opacity: 1; }

.cp-cm-hd {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 18px;
}
.cp-cm-hd span {
    font-size: 16px; font-weight: 800; color: #0f172a;
    letter-spacing: -0.2px;
}
.cp-cm-x {
    width: 26px; height: 26px; border-radius: 50%;
    background: rgba(0,0,0,0.04); border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    color: rgba(0,0,0,0.4); font-size: 12px;
    transition: background 0.15s;
}
.cp-cm-x:hover { background: rgba(0,0,0,0.08); color: #0f172a; }

.cp-cm-amount {
    display: flex; align-items: center; justify-content: space-between;
    background: rgba(15,23,42,0.03);
    border: 1px solid rgba(15,23,42,0.06);
    border-radius: 12px;
    padding: 12px 14px;
    margin-bottom: 22px;
    font-size: 13px; font-weight: 600; color: rgba(15,23,42,0.5);
}
.cp-cm-figure {
    font-size: 16px; font-weight: 900; color: #d97706;
    font-variant-numeric: tabular-nums;
}
.cp-cm-figure em {
    font-style: normal; font-size: 10px; font-weight: 700;
    color: rgba(217,119,6,0.6); margin-left: 3px;
}

.cp-cm-go {
    width: 100%; padding: 13px;
    background: #0f172a; border: none; border-radius: 12px;
    color: #fff; font-size: 14px; font-weight: 800; letter-spacing: 0.3px;
    cursor: pointer;
    transition: all 0.2s;
}
.cp-cm-go:hover { background: #1e293b; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(15,23,42,0.25); }
.cp-cm-go:active { transform: translateY(0); }

/* BOTTOM SHEET (kept for other coin pages that may still reference it) */
.cp-bs-bg {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.75); backdrop-filter: blur(6px);
    z-index: 2000;
}
.cp-bs-bg.on { display: block; }
.cp-bs {
    position: fixed; bottom: 0; left: 0; right: 0;
    background: #060a12;
    border-top: 1px solid rgba(255,255,255,0.07);
    border-radius: 22px 22px 0 0;
    padding: 12px 22px 80px;
    z-index: 2001;
    transform: translateY(100%);
    transition: transform 0.35s cubic-bezier(0.4,0,0.2,1);
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
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 8px;
    color: #fff;
    font-size: 12px; font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
}
a.cpp-link:hover { background: rgba(255,255,255,0.08); }
.cpp-item.active .cpp-link {
    background: #fff;
    border-color: transparent;
    color: #060a12;
}
.cpp-item.disabled .cpp-link {
    opacity: 0.35; cursor: not-allowed; background: rgba(255,255,255,0.02);
}
.cpp-dots { border: none; background: none; }

.cp-bs.on { transform: translateY(0); }
</style>
@endpush

@push('scripts')
<script>
function openCoinPopup()  { document.getElementById('coinPopupOverlay').style.display = 'flex'; }
function closeCoinPopup() { document.getElementById('coinPopupOverlay').style.display = 'none'; }
document.addEventListener('keydown', e => { if(e.key==='Escape'){closeCoinPopup();cpBsClose();} });

document.addEventListener('DOMContentLoaded', function() {
    const initialTab = '{{ $tab }}';

    function showTab(name) {
        document.querySelectorAll('.cp-panel').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.cp-tab').forEach(b => b.classList.remove('active'));
        const c = document.getElementById('tab-' + name);
        const b = document.querySelector('[data-tab="' + name + '"]');
        if(c) c.style.display = 'block';
        if(b) b.classList.add('active');
    }

    showTab(initialTab);
    document.querySelectorAll('.cp-tab').forEach(btn => btn.addEventListener('click', () => showTab(btn.dataset.tab)));

    setTimeout(() => {
        const f = document.getElementById('cp-flash');
        if(f) { f.style.transition='opacity .4s'; f.style.opacity='0'; setTimeout(()=>f.remove(),400); }
    }, 5000);

    @if($unjoinedOpenSignal)
    const k = 'sig_{{ $unjoinedOpenSignal->id }}';
    if(!sessionStorage.getItem(k) && initialTab !== 'history') setTimeout(cpBsOpen, 700);
    @endif
});

function cpBsOpen() {
    const bg = document.getElementById('cp-bs-bg');
    const modal = document.getElementById('cp-bs');
    bg?.classList.add('on');
    modal?.classList.add('on');
    requestAnimationFrame(() => modal?.classList.add('show'));
}
function cpBsClose() {
    @if($unjoinedOpenSignal)
    sessionStorage.setItem('sig_{{ $unjoinedOpenSignal->id }}', '1');
    @endif
    const bg = document.getElementById('cp-bs-bg');
    const modal = document.getElementById('cp-bs');
    modal?.classList.remove('show');
    bg?.classList.remove('on');
    setTimeout(() => modal?.classList.remove('on'), 200);
}

function cpFilterMarkets(q) {
    q = q.toLowerCase().trim();
    document.querySelectorAll('#cpMarketList .cp-mkt').forEach(row => {
        row.style.display = row.dataset.name.includes(q) ? '' : 'none';
    });
    document.querySelectorAll('#cpMarketList .cp-mkt-cat').forEach(cat => {
        let sib = cat.nextElementSibling, hasVisible = false;
        while (sib && !sib.classList.contains('cp-mkt-cat')) {
            if (sib.style.display !== 'none') hasVisible = true;
            sib = sib.nextElementSibling;
        }
        cat.style.display = hasVisible ? '' : 'none';
    });
}
</script>
@endpush
@endsection