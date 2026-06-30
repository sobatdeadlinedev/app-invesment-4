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
            <div class="cp-drawer-title">PILIH MARKET</div>
            <div class="cp-drawer-list">
                @foreach($allCoins as $symbol => $info)
                <a href="{{ route('member.invest.coin', ['coin' => strtolower($symbol)]) }}"
                   class="cp-mkt {{ $symbol === $coin ? 'cp-mkt-on' : '' }}">
                    <span class="cp-mkt-dot" style="background:{{ $info['color'] }};box-shadow:0 0 6px {{ $info['color'] }};"></span>
                    <span class="cp-mkt-s">{{ $info['symbol'] }}</span>
                    <span class="cp-mkt-n">{{ $info['name'] }}</span>
                    @if($signalCounts[$symbol] > 0)
                        <span class="cp-mkt-sig">{{ $signalCounts[$symbol] }}</span>
                    @endif
                </a>
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

    {{-- HISTORY PANEL --}}
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
            $signal     = $participant->signal;
            $isPending  = $signal->status !== 'settled' || $signal->result === null;
            $isWin      = $signal->result === 'win';
            $isSettled  = $participant->status === 'settled';
            $netResult  = ($participant->profit_loss ?? 0) - ($participant->fee_amount ?? 0);
            $adminChoice = strtolower($signal->admin_choice ?? '');
            $stateKey   = $isPending ? 'p' : ($isWin ? 'w' : 'l');
        @endphp

        <div class="cp-hcard cp-hcard-{{ $stateKey }}">
            {{-- Card header --}}
            <div class="cp-hcard-head">
                <div class="cp-hcard-title">{{ $signal->title }}</div>
                <div class="cp-hcard-badge cp-hbadge-{{ $stateKey }}">
                    @if($isPending) PENDING
                    @elseif($isWin) WIN ✓
                    @else LOSS ✗
                    @endif
                </div>
            </div>
            {{-- Rows --}}
            <div class="cp-hcard-row">
                <span class="cp-hcard-lbl">Jumlah Order</span>
                <span class="cp-hcard-val">{{ number_format($participant->bet_amount, 2) }} USDT</span>
            </div>
            <div class="cp-hcard-row">
                <span class="cp-hcard-lbl">Laba/Rugi Bersih</span>
                <span class="cp-hcard-val {{ $isPending ? '' : ($netResult >= 0 ? 'cp-val-g' : 'cp-val-r') }}">
                    @if($isPending) ~
                    @elseif($isSettled) {{ ($netResult >= 0 ? '+' : '') . number_format($netResult, 2) }} USDT
                    @else —
                    @endif
                </span>
            </div>
            <div class="cp-hcard-row">
                <span class="cp-hcard-lbl">Periode Waktu</span>
                <span class="cp-hcard-val">
                    {{ $signal->opened_at ? $signal->opened_at->format('H:i') : '--' }}
                    –
                    {{ $signal->closed_at ? $signal->closed_at->format('H:i') : '~' }}
                </span>
            </div>
            <div class="cp-hcard-row">
                <span class="cp-hcard-lbl">Arah</span>
                <span class="cp-hcard-val {{ $adminChoice === 'call' ? 'cp-val-g' : ($adminChoice === 'put' ? 'cp-val-r' : '') }}">
                    @if($isPending) —
                    @elseif($adminChoice === 'call') CALL ↑
                    @elseif($adminChoice === 'put') PUT ↓
                    @else N/A
                    @endif
                </span>
            </div>
            @if($isSettled && ($signal->rate_of_return ?? 0) > 0)
            <div class="cp-hcard-row">
                <span class="cp-hcard-lbl">Tingkat Pengembalian</span>
                <span class="cp-hcard-val">{{ number_format($signal->rate_of_return, 2) }}%</span>
            </div>
            @endif
        </div>

        @empty
        <div class="cp-void">
            <div class="cp-void-hex"><i class="bi bi-archive"></i></div>
            <div class="cp-void-text">Belum ada riwayat order untuk {{ $coinInfo['name'] }}</div>
            <div class="cp-void-sub">Ikuti sinyal untuk mulai</div>
        </div>
        @endforelse

        @if($historyForThisCoin->hasPages())
        <div class="cp-pages">{{ $historyForThisCoin->links() }}</div>
        @endif
    </div>

    <div style="height:40px;"></div>
</div>
</div>

{{-- BOTTOM SHEET --}}
@if($unjoinedOpenSignal)
<div class="cp-bs-bg" id="cp-bs-bg" onclick="cpBsClose()"></div>
<div class="cp-bs" id="cp-bs">
    <div class="cp-bs-pill"></div>
    <div class="cp-bs-icon-row">
        <div class="cp-bs-orb">
            <div class="cp-bs-orb-r1"></div>
            <div class="cp-bs-orb-r2"></div>
            <i class="bi bi-reception-4"></i>
        </div>
    </div>
    <div class="cp-bs-eyebrow">SINYAL BARU MASUK</div>
    <div class="cp-bs-asset">{{ $coinInfo['symbol'] }} · {{ $coinInfo['name'] }}</div>
    <div class="cp-bs-signame">{{ $unjoinedOpenSignal->title }}</div>
    <div class="cp-bs-numbers">
        <div class="cp-bs-num-block">
            <div class="cp-bs-num-k">TARUHAN</div>
            <div class="cp-bs-num-v amber">{{ number_format($unjoinedOpenSignal->betAmountPreview, 2) }}<sup>USDT</sup></div>
        </div>
        <div class="cp-bs-num-vr"></div>
        <div class="cp-bs-num-block">
            <div class="cp-bs-num-k">SALDO</div>
            <div class="cp-bs-num-v">{{ number_format(auth()->user()->trade_balance, 2) }}<sup>USDT</sup></div>
        </div>
    </div>
    <form action="{{ route('member.signals.join', $unjoinedOpenSignal->id) }}" method="POST">
        @csrf
        <button type="submit" class="cp-bs-go"><i class="bi bi-lightning-charge-fill"></i> KONFIRMASI IKUT SIGNAL</button>
    </form>
    <button onclick="cpBsClose()" class="cp-bs-skip">Nanti saja</button>
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
    display: flex; align-items: flex-end;
}
.cp-drawer {
    background: #060a12;
    border-top: 1px solid rgba(255,255,255,0.07);
    border-radius: 20px 20px 0 0;
    width: 100%; max-height: 75vh; overflow: hidden;
    padding-bottom: 24px;
    animation: cpDrawerUp 0.25s ease;
}
@keyframes cpDrawerUp { from{transform:translateY(40px);opacity:0} to{transform:translateY(0);opacity:1} }
.cp-drawer-handle { width: 32px; height: 3px; background: rgba(255,255,255,0.1); border-radius: 2px; margin: 12px auto 16px; }
.cp-drawer-title { font-size: 10px; font-weight: 800; letter-spacing: 3px; color: rgba(255,255,255,0.2); text-align: center; margin-bottom: 14px; }
.cp-drawer-list { overflow-y: auto; max-height: calc(75vh - 80px); }
.cp-mkt {
    display: flex; align-items: center; gap: 12px;
    padding: 13px 20px; text-decoration: none;
    border-bottom: 1px solid rgba(255,255,255,0.04);
    transition: background 0.15s;
}
.cp-mkt:hover { background: rgba(255,255,255,0.03); }
.cp-mkt-on { background: rgba(255,255,255,0.04); }
.cp-mkt-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.cp-mkt-s { color: #fff; font-size: 14px; font-weight: 700; }
.cp-mkt-n { color: rgba(255,255,255,0.3); font-size: 12px; flex: 1; }
.cp-mkt-sig { background: rgba(34,197,94,0.1); color: #22c55e; font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 10px; }

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
   SIGNAL ITEM — totally new layout
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

/* Left accent bar */
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

/* Body */
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

/* Stats row */
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

/* Join button */
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

/* Join/insuf strips */
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

/* HISTORY CARD */
.cp-hcard {
    margin: 0 14px 12px;
    background: linear-gradient(135deg, #0c1424, #080f1d);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 14px; overflow: hidden;
    border-left: 3px solid rgba(255,255,255,0.08);
}
.cp-hcard-w { border-left-color: #4ade80; }
.cp-hcard-l { border-left-color: #f87171; }
.cp-hcard-p { border-left-color: #fbbf24; }

.cp-hcard-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 12px 14px 10px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}
.cp-hcard-title {
    color: #fff; font-size: 13px; font-weight: 800;
    flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}
.cp-hcard-badge {
    font-size: 10px; font-weight: 800; letter-spacing: 0.5px;
    padding: 3px 9px; border-radius: 6px; flex-shrink: 0; margin-left: 10px;
}
.cp-hbadge-w { background: rgba(74,222,128,0.1);  border: 1px solid rgba(74,222,128,0.25);  color: #4ade80; }
.cp-hbadge-l { background: rgba(241,87,87,0.1);   border: 1px solid rgba(241,87,87,0.25);   color: #f87171; }
.cp-hbadge-p { background: rgba(251,191,36,0.1);  border: 1px solid rgba(251,191,36,0.25);  color: #fbbf24; }

.cp-hcard-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 8px 14px;
    border-bottom: 1px solid rgba(255,255,255,0.03);
}
.cp-hcard-row:last-child { border-bottom: none; }
.cp-hcard-lbl { color: rgba(255,255,255,0.35); font-size: 11px; }
.cp-hcard-val { color: #fff; font-size: 12px; font-weight: 700; font-variant-numeric: tabular-nums; }
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

/* BOTTOM SHEET */
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
.cp-bs.on { transform: translateY(0); }
.cp-bs-pill { width: 32px; height: 3px; border-radius: 2px; background: rgba(255,255,255,0.1); margin: 0 auto 20px; }

.cp-bs-icon-row { text-align: center; margin-bottom: 12px; }
.cp-bs-orb {
    display: inline-flex; align-items: center; justify-content: center;
    width: 56px; height: 56px;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 16px; font-size: 24px; color: #fff;
    position: relative;
}
.cp-bs-orb-r1, .cp-bs-orb-r2 {
    position: absolute; border-radius: 16px;
    border: 1px solid rgba(255,255,255,0.05);
    animation: cpOrbPulse 2s ease infinite;
}
.cp-bs-orb-r1 { inset: -8px; animation-delay: 0s; }
.cp-bs-orb-r2 { inset: -16px; animation-delay: 0.5s; }
@keyframes cpOrbPulse {
    0%   { opacity: 0.6; transform: scale(0.95); }
    100% { opacity: 0; transform: scale(1.05); }
}

.cp-bs-eyebrow { text-align: center; font-size: 10px; font-weight: 800; letter-spacing: 3px; color: rgba(255,255,255,0.25); margin-bottom: 3px; }
.cp-bs-asset   { text-align: center; color: rgba(255,255,255,0.35); font-size: 12px; margin-bottom: 14px; }
.cp-bs-signame {
    text-align: center; color: #fff; font-size: 17px; font-weight: 900;
    margin-bottom: 18px; line-height: 1.3;
}
.cp-bs-numbers {
    display: flex; align-items: center;
    background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06);
    border-radius: 14px; padding: 16px 0; margin-bottom: 20px;
}
.cp-bs-num-block { flex: 1; text-align: center; }
.cp-bs-num-k { font-size: 9px; font-weight: 800; letter-spacing: 1.5px; color: rgba(255,255,255,0.2); margin-bottom: 6px; }
.cp-bs-num-v { font-size: 24px; font-weight: 900; color: #fff; font-variant-numeric: tabular-nums; }
.cp-bs-num-v sup { font-size: 10px; font-weight: 700; color: rgba(255,255,255,0.25); margin-left: 3px; vertical-align: super; }
.cp-bs-num-v.amber { color: #fbbf24; }
.cp-bs-num-vr { width: 1px; height: 44px; background: rgba(255,255,255,0.06); }
.cp-bs-go {
    width: 100%; padding: 15px;
    background: #fff; border: none; border-radius: 14px;
    color: #060a12; font-size: 14px; font-weight: 900; letter-spacing: 0.5px;
    cursor: pointer; margin-bottom: 10px;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    transition: all 0.2s;
}
.cp-bs-go:hover { background: #e2e8f0; transform: translateY(-1px); box-shadow: 0 8px 24px rgba(255,255,255,0.15); }
.cp-bs-skip {
    width: 100%; padding: 12px; background: none;
    border: 1px solid rgba(255,255,255,0.07); border-radius: 14px;
    color: rgba(255,255,255,0.25); font-size: 13px; cursor: pointer;
    transition: all 0.2s;
}
.cp-bs-skip:hover { background: rgba(255,255,255,0.03); color: rgba(255,255,255,0.4); }
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
    document.getElementById('cp-bs-bg')?.classList.add('on');
    document.getElementById('cp-bs')?.classList.add('on');
}
function cpBsClose() {
    @if($unjoinedOpenSignal)
    sessionStorage.setItem('sig_{{ $unjoinedOpenSignal->id }}', '1');
    @endif
    document.getElementById('cp-bs-bg')?.classList.remove('on');
    document.getElementById('cp-bs')?.classList.remove('on');
}
</script>
@endpush
@endsection