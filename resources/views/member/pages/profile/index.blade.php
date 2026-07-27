@extends('member.layouts.app')
@section('content')
<div class="scrollable-content wov2">

    {{-- ═══ TOP BAR ═══ --}}
    <div class="wov2-topbar">
        <div class="wov2-user">
            <div class="wov2-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="wov2-userinfo">
                <span class="wov2-username">{{ auth()->user()->name }}</span>
                <span class="wov2-uid">UID #{{ auth()->user()->id }}</span>
            </div>
        </div>
        <div class="wov2-topbar-right">
            <div class="wov2-pnl-chip {{ $todayPnl >= 0 ? 'up' : 'down' }}">
                <span class="wov2-pnl-dot"></span>
                <span>{{ $todayPnl >= 0 ? '+' : '' }}{{ number_format($todayPnl, 2) }}</span>
                <span class="wov2-pnl-sep">/</span>
                @php
                    $total = $balanceBreakdown['total_balance'];
                    $pct = $total > 0 ? round(abs($todayPnl) / $total * 100, 2) : 0;
                @endphp
                <span>{{ $todayPnl >= 0 ? '+' : '-' }}{{ $pct }}%</span>
            </div>
        </div>
    </div>

    {{-- ═══ BALANCE HERO ═══ --}}
    <div class="wov2-hero">
        <div class="wov2-hero-mesh"></div>
        <div class="wov2-hero-inner">
            <div class="wov2-hero-label">
                <span>TOTAL BALANCE</span>
                <button class="wov2-eye" onclick="toggleBalance()" id="eyeBtn" type="button">
                    <i class="bi bi-eye" id="eyeIcon"></i>
                </button>
            </div>
            <div class="wov2-hero-amount">
                <span id="balanceDisplay">{{ number_format($balanceBreakdown['total_balance'], 2) }}</span>
                <span class="wov2-hero-currency">USDT</span>
            </div>
            <div class="wov2-hero-sub">≈ ${{ number_format($balanceBreakdown['total_balance'], 0) }} USD</div>

            {{-- Action Strip --}}
            <div class="wov2-actions">
                <a href="{{ route('member.deposit.index') }}" class="wov2-act">
                    <span class="wov2-act-icon deposit"><i class="bi bi-arrow-down-circle-fill"></i></span>
                    <span class="wov2-act-label">{{ __('app.deposit') }}</span>
                </a>
                <a href="{{ route('member.withdraw.index') }}" class="wov2-act" id="btnWithdraw">
                    <span class="wov2-act-icon withdraw"><i class="bi bi-arrow-up-circle-fill"></i></span>
                    <span class="wov2-act-label">{{ __('app.withdraw') }}</span>
                </a>
                <a href="{{ route('member.balance.transfer') }}" class="wov2-act">
                    <span class="wov2-act-icon transfer"><i class="bi bi-arrow-left-right"></i></span>
                    <span class="wov2-act-label">{{ __('app.transfer') }}</span>
                </a>
                <a href="{{ route('member.deposit.history') }}" class="wov2-act">
                    <span class="wov2-act-icon history"><i class="bi bi-clock-history"></i></span>
                    <span class="wov2-act-label">{{ __('app.history') }}</span>
                </a>
            </div>
        </div>
    </div>

    {{-- ═══ ASSET ALLOCATION ═══ --}}
    @php
        $spot    = (float) $balanceBreakdown['exchange_balance'];
        $trade   = (float) $balanceBreakdown['trade_balance'];
        $futures = (float) $openFuturesAmount;
        $totalAssets = $spot + $trade + $futures;
    @endphp

    <div class="wov2-section">
        <div class="wov2-sec-head">
            <div>
                <div class="wov2-sec-title">Asset Allocation</div>
                <div class="wov2-sec-sub">{{ number_format($totalAssets, 2) }} USDT total</div>
            </div>
            <a href="{{ route('member.profile.index') }}" class="wov2-iconbtn">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>

        {{-- Donut + Legend side by side --}}
        <div class="wov2-chart-row">
            <div class="wov2-donut-wrap">
                <canvas id="donutChart" width="140" height="140"></canvas>
                <div class="wov2-donut-center">
                    <span class="wov2-donut-val" id="donutVal">{{ number_format($totalAssets, 2) }}</span>
                    <span class="wov2-donut-label" id="donutLabel">TOTAL</span>
                </div>
            </div>
            <div class="wov2-legend">
                <div class="wov2-legend-item" data-idx="0">
                    <div class="wov2-legend-dot" style="background:#a78bfa;"></div>
                    <div>
                        <div class="wov2-legend-name">Spot</div>
                        <div class="wov2-legend-pct">
                            {{ $totalAssets > 0 ? round($spot / $totalAssets * 100, 1) : 0 }}%
                        </div>
                    </div>
                    <div class="wov2-legend-val">{{ number_format($spot, 2) }}</div>
                </div>
                <div class="wov2-legend-item" data-idx="1">
                    <div class="wov2-legend-dot" style="background:#34d399;"></div>
                    <div>
                        <div class="wov2-legend-name">Trading</div>
                        <div class="wov2-legend-pct">
                            {{ $totalAssets > 0 ? round($trade / $totalAssets * 100, 1) : 0 }}%
                        </div>
                    </div>
                    <div class="wov2-legend-val">{{ number_format($trade, 2) }}</div>
                </div>
                <div class="wov2-legend-item" data-idx="2">
                    <div class="wov2-legend-dot" style="background:#fb923c;"></div>
                    <div>
                        <div class="wov2-legend-name">Futures</div>
                        <div class="wov2-legend-pct">
                            {{ $totalAssets > 0 ? round($futures / $totalAssets * 100, 1) : 0 }}%
                        </div>
                    </div>
                    <div class="wov2-legend-val">{{ number_format($futures, 2) }}</div>
                </div>
            </div>
        </div>

        {{-- Horizontal asset cards --}}
        <div class="wov2-asset-cards">
            <a href="{{ route('member.deposit.index') }}" class="wov2-acard">
                <div class="wov2-acard-top">
                    <div class="wov2-acard-icon" style="background:rgba(167,139,250,0.15);color:#a78bfa;">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <i class="bi bi-arrow-up-right wov2-acard-arrow"></i>
                </div>
                <div class="wov2-acard-name">Spot Wallet</div>
                <div class="wov2-acard-amt">{{ number_format($spot, 2) }}</div>
                <div class="wov2-acard-usd">≈ ${{ number_format($spot, 0) }}</div>
                <div class="wov2-acard-bar">
                    <div class="wov2-acard-fill" style="width:{{ $totalAssets > 0 ? round($spot/$totalAssets*100) : 0 }}%;background:#a78bfa;"></div>
                </div>
            </a>
            <a href="{{ route('member.balance.transfer') }}" class="wov2-acard">
                <div class="wov2-acard-top">
                    <div class="wov2-acard-icon" style="background:rgba(52,211,153,0.15);color:#34d399;">
                        <i class="bi bi-bar-chart-fill"></i>
                    </div>
                    <i class="bi bi-arrow-up-right wov2-acard-arrow"></i>
                </div>
                <div class="wov2-acard-name">Trading</div>
                <div class="wov2-acard-amt">{{ number_format($trade, 2) }}</div>
                <div class="wov2-acard-usd">≈ ${{ number_format($trade, 0) }}</div>
                <div class="wov2-acard-bar">
                    <div class="wov2-acard-fill" style="width:{{ $totalAssets > 0 ? round($trade/$totalAssets*100) : 0 }}%;background:#34d399;"></div>
                </div>
            </a>
            <a href="{{ route('member.futures.index') }}" class="wov2-acard">
                <div class="wov2-acard-top">
                    <div class="wov2-acard-icon" style="background:rgba(251,146,60,0.15);color:#fb923c;">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <i class="bi bi-arrow-up-right wov2-acard-arrow"></i>
                </div>
                <div class="wov2-acard-name">Futures</div>
                @if($futures > 0)
                <div class="wov2-acard-badge">In-play</div>
                @endif
                <div class="wov2-acard-amt">{{ number_format($futures, 2) }}</div>
                <div class="wov2-acard-usd">≈ ${{ number_format($futures, 0) }}</div>
                <div class="wov2-acard-bar">
                    <div class="wov2-acard-fill" style="width:{{ $totalAssets > 0 ? round($futures/$totalAssets*100) : 0 }}%;background:#fb923c;"></div>
                </div>
            </a>
        </div>
    </div>

    {{-- ═══ TRADING VOLUME ═══ --}}
    @php
        $targetVolume = (float) (auth()->user()->target_volume ?? 0);
        $achievedVolume = (float) (auth()->user()->achieved_volume ?? 0);
        $remainingVolume = max(0, $targetVolume - $achievedVolume);
        $volumePct = $targetVolume > 0
            ? min(100, round($achievedVolume / $targetVolume * 100, 1))
            : 100;
    @endphp

    <div class="wov2-section">
        <div class="wov2-sec-head">
            <div>
                <div class="wov2-sec-title">Trading Volume</div>
                <div class="wov2-sec-sub">Progress menuju target volume</div>
            </div>
        </div>

        <div class="wov2-volume-card">
            <div class="wov2-volume-simple">
                <span class="wov2-volume-simple-label">Volume Trading</span>
                <span class="wov2-volume-simple-val">{{ number_format($achievedVolume, 2) }} USDT</span>
            </div>

            @if($targetVolume > 0 && $achievedVolume < $targetVolume)
            <div class="wov2-volume-note">
                <i class="bi bi-info-circle"></i>
                Selesaikan target volume untuk menghindari penalti saat transfer balance ke exchange.
            </div>
            @endif
        </div>
    </div>

    <div style="height:24px;"></div>
</div>
@endsection

@push('styles')
<style>
/* ══ Root ══════════════════════════════════════════════════════ */
.wov2 {
    background: var(--bg-dark);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

/* ══ Topbar ════════════════════════════════════════════════════ */
.wov2-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 20px;
    position: relative;
    z-index: 1;
}
.wov2-user { display: flex; align-items: center; gap: 10px; }
.wov2-avatar {
    width: 38px; height: 38px; border-radius: 12px;
    background: linear-gradient(135deg, #a78bfa, #34d399);
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; font-weight: 800; color: #0a0f1e;
    flex-shrink: 0;
}
.wov2-username { display: block; color: #fff; font-size: 14px; font-weight: 700; line-height: 1.2; }
.wov2-uid { color: rgba(255,255,255,0.35); font-size: 10px; letter-spacing: 0.5px; }
.wov2-pnl-chip {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 10px; border-radius: 20px;
    font-size: 11px; font-weight: 700;
}
.wov2-pnl-chip.up   { background: rgba(52,211,153,0.12); color: #34d399; border: 1px solid rgba(52,211,153,0.25); }
.wov2-pnl-chip.down { background: rgba(251,99,99,0.12);  color: #fb6363; border: 1px solid rgba(251,99,99,0.25); }
.wov2-pnl-dot {
    width: 6px; height: 6px; border-radius: 50%;
    background: currentColor;
}
.wov2-pnl-sep { opacity: 0.4; }

/* ══ Hero ══════════════════════════════════════════════════════ */
.wov2-hero {
    position: relative;
    margin: 0 12px 4px;
    border-radius: 20px;
    overflow: visible; /* was hidden — could clip/alter tap area on some mobile renderers */
    background: #0d111f;
    border: 1px solid rgba(167,139,250,0.2);
    z-index: 1;
}
.wov2-hero-mesh {
    position: absolute; inset: 0; pointer-events: none;
    background:
        radial-gradient(ellipse 60% 80% at 15% 20%, rgba(167,139,250,0.18) 0%, transparent 60%),
        radial-gradient(ellipse 50% 60% at 85% 80%, rgba(52,211,153,0.12) 0%, transparent 60%);
    z-index: 0;
    border-radius: 20px;
}
.wov2-hero-inner { position: relative; padding: 24px 20px 20px; z-index: 1; }
.wov2-hero-label {
    display: flex; align-items: center; gap: 8px;
    color: rgba(255,255,255,0.4);
    font-size: 10px; font-weight: 700;
    letter-spacing: 1.5px; text-transform: uppercase;
    margin-bottom: 8px;
}
.wov2-eye {
    background: none; border: none; color: rgba(255,255,255,0.35);
    padding: 0; cursor: pointer; font-size: 13px; line-height: 1;
    position: relative;
    z-index: 2;
}
.wov2-hero-amount {
    display: flex; align-items: flex-end; gap: 8px;
    line-height: 1;
}
.wov2-hero-amount > span:first-child {
    font-size: 36px; font-weight: 900; color: #fff;
    font-variant-numeric: tabular-nums;
    letter-spacing: -1.5px;
    font-family: 'SF Mono', 'Fira Code', monospace;
}
.wov2-hero-currency {
    font-size: 13px; font-weight: 700;
    color: #a78bfa;
    padding-bottom: 4px;
    letter-spacing: 0.5px;
}
.wov2-hero-sub {
    color: rgba(255,255,255,0.3); font-size: 11px;
    margin-top: 6px; margin-bottom: 20px;
}

/* ══ Actions (strip inside hero) ══════════════════════════════ */
.wov2-actions {
    position: relative;
    z-index: 5;
    display: grid; grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    padding-top: 20px;
    border-top: 1px solid rgba(255,255,255,0.06);
}
.wov2-act {
    position: relative;
    z-index: 5;
    display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px;
    text-decoration: none;
    -webkit-tap-highlight-color: rgba(0,229,255,0.15);
    touch-action: manipulation;
    -webkit-user-select: none;
    user-select: none;
    /* seluruh kotak ini jadi target tap yang solid */
    padding: 8px 4px;
    min-height: 74px;
    cursor: pointer;
}
.wov2-act-icon {
    width: 44px; height: 44px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; transition: transform 0.2s;
    pointer-events: none;
}
.wov2-act i { pointer-events: none; }
.wov2-act:active .wov2-act-icon { transform: scale(0.92); }
.wov2-act-icon.deposit  { background: rgba(167,139,250,0.15); color: #a78bfa; border: 1px solid rgba(167,139,250,0.25); }
.wov2-act-icon.withdraw { background: rgba(251,146,60,0.15);  color: #fb923c; border: 1px solid rgba(251,146,60,0.25); }
.wov2-act-icon.transfer { background: rgba(52,211,153,0.15);  color: #34d399; border: 1px solid rgba(52,211,153,0.25); }
.wov2-act-icon.history  { background: rgba(96,165,250,0.15);  color: #60a5fa; border: 1px solid rgba(96,165,250,0.25); }
.wov2-act-label {
    font-size: 10px; font-weight: 600;
    color: rgba(255,255,255,0.55);
    letter-spacing: 0.3px;
    pointer-events: none;
}

/* ══ Section ═══════════════════════════════════════════════════ */
.wov2-section { padding: 20px 12px 0; }
.wov2-sec-head {
    display: flex; align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 16px;
}
.wov2-sec-title { color: #fff; font-size: 15px; font-weight: 800; }
.wov2-sec-sub { color: rgba(255,255,255,0.3); font-size: 11px; margin-top: 2px; }
.wov2-iconbtn {
    width: 32px; height: 32px; border-radius: 10px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.08);
    display: flex; align-items: center; justify-content: center;
    color: rgba(255,255,255,0.4); font-size: 14px;
    text-decoration: none;
}

/* ══ Chart row ═════════════════════════════════════════════════ */
.wov2-chart-row {
    display: flex; align-items: center; gap: 20px;
    margin-bottom: 20px;
}
.wov2-donut-wrap {
    position: relative;
    width: 140px; height: 140px;
    flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
}
.wov2-donut-center {
    position: absolute; inset: 0;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    pointer-events: none;
}
.wov2-donut-val {
    font-size: 13px; font-weight: 800; color: #fff;
    font-variant-numeric: tabular-nums;
    font-family: 'SF Mono', 'Fira Code', monospace;
    letter-spacing: -0.5px;
}
.wov2-donut-label {
    font-size: 9px; font-weight: 700; letter-spacing: 1.5px;
    color: rgba(255,255,255,0.3); margin-top: 2px;
}

/* ══ Legend ════════════════════════════════════════════════════ */
.wov2-legend { flex: 1; display: flex; flex-direction: column; gap: 10px; }
.wov2-legend-item {
    display: flex; align-items: center; gap: 10px;
    cursor: pointer;
    padding: 4px 0;
}
.wov2-legend-dot { width: 8px; height: 8px; border-radius: 2px; flex-shrink: 0; }
.wov2-legend-item > div:nth-child(2) { flex: 1; }
.wov2-legend-name { color: rgba(255,255,255,0.7); font-size: 11px; font-weight: 600; }
.wov2-legend-pct  { color: rgba(255,255,255,0.3); font-size: 10px; }
.wov2-legend-val  {
    color: #fff; font-size: 11px; font-weight: 700;
    font-variant-numeric: tabular-nums;
    text-align: right;
}

/* ══ Asset cards (horizontal scroll) ══════════════════════════ */
.wov2-asset-cards {
    display: flex; gap: 10px;
    overflow-x: auto; padding-bottom: 4px;
    scrollbar-width: none;
    margin: 0 -12px; padding-left: 12px; padding-right: 12px;
}
.wov2-asset-cards::-webkit-scrollbar { display: none; }
.wov2-acard {
    flex: 0 0 140px;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 16px; padding: 14px;
    text-decoration: none;
    display: flex; flex-direction: column; gap: 3px;
    transition: border-color 0.2s, background 0.2s;
    position: relative; overflow: hidden;
    touch-action: manipulation;
}
.wov2-acard:active {
    background: rgba(255,255,255,0.06);
    border-color: rgba(255,255,255,0.15);
}
.wov2-acard-top {
    display: flex; align-items: center;
    justify-content: space-between; margin-bottom: 8px;
}
.wov2-acard-icon {
    width: 34px; height: 34px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 15px;
}
.wov2-acard-arrow { color: rgba(255,255,255,0.2); font-size: 11px; }
.wov2-acard-name { color: rgba(255,255,255,0.45); font-size: 10px; font-weight: 600; letter-spacing: 0.3px; }
.wov2-acard-badge {
    display: inline-block;
    background: rgba(251,146,60,0.15); color: #fb923c;
    font-size: 8px; font-weight: 700; letter-spacing: 0.5px;
    padding: 1px 5px; border-radius: 4px;
    margin-top: 2px;
}
.wov2-acard-amt {
    color: #fff; font-size: 14px; font-weight: 800;
    font-variant-numeric: tabular-nums;
    letter-spacing: -0.5px; margin-top: 4px;
}
.wov2-acard-usd { color: rgba(255,255,255,0.25); font-size: 10px; }
.wov2-acard-bar {
    height: 3px; background: rgba(255,255,255,0.06);
    border-radius: 2px; margin-top: 10px; overflow: hidden;
}
.wov2-acard-fill { height: 100%; border-radius: 2px; transition: width 0.8s ease; }

/* ══ Volume card ═══════════════════════════════════════════════ */
.wov2-volume-card {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 16px;
    padding: 18px;
}
.wov2-volume-top {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 12px;
}
.wov2-volume-pct {
    font-size: 24px; font-weight: 900; color: #fff;
    font-variant-numeric: tabular-nums;
    font-family: 'SF Mono', 'Fira Code', monospace;
}
.wov2-volume-badge {
    background: rgba(167,139,250,0.15); color: #a78bfa;
    font-size: 10px; font-weight: 700; letter-spacing: 0.5px;
    padding: 3px 10px; border-radius: 20px;
}
.wov2-volume-badge.done {
    background: rgba(52,211,153,0.15); color: #34d399;
}
.wov2-volume-bar {
    height: 8px; background: rgba(255,255,255,0.06);
    border-radius: 4px; overflow: hidden; margin-bottom: 16px;
}
.wov2-volume-fill {
    height: 100%; border-radius: 4px;
    background: linear-gradient(90deg, #a78bfa, #34d399);
    transition: width 0.8s ease;
}
.wov2-volume-stats {
    display: flex; justify-content: space-between; gap: 10px;
}
.wov2-volume-stat {
    display: flex; flex-direction: column; gap: 3px;
    flex: 1;
}
.wov2-volume-stat-label {
    font-size: 10px; color: rgba(255,255,255,0.35);
    text-transform: uppercase; letter-spacing: 0.5px;
}
.wov2-volume-stat-val {
    font-size: 13px; font-weight: 700; color: #fff;
    font-variant-numeric: tabular-nums;
}
.wov2-volume-note {
    display: flex; align-items: flex-start; gap: 6px;
    margin-top: 14px; padding-top: 14px;
    border-top: 1px solid rgba(255,255,255,0.06);
    font-size: 11px; color: rgba(255,255,255,0.4);
    line-height: 1.5;
}
.wov2-volume-note i { margin-top: 1px; }
.wov2-volume-simple {
    display: flex; flex-direction: column; gap: 6px;
}
.wov2-volume-simple-label {
    font-size: 11px; color: rgba(255,255,255,0.4);
    text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;
}
.wov2-volume-simple-val {
    font-size: 22px; font-weight: 900; color: #fff;
    font-variant-numeric: tabular-nums;
    font-family: 'SF Mono', 'Fira Code', monospace;
}
</style>
@endpush

@push('scripts')
<script>
// ── Balance toggle ─────────────────────────────────────────────
const REAL_BAL = '{{ number_format($balanceBreakdown['total_balance'], 2) }}';
let balHidden = false;
function toggleBalance() {
    balHidden = !balHidden;
    document.getElementById('balanceDisplay').textContent = balHidden ? '••••••' : REAL_BAL;
    document.getElementById('eyeIcon').className = balHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
}

// ── Donut Chart ────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('donutChart');
    const ctx = canvas.getContext('2d');
    const cx = 70, cy = 70, r = 54, inner = 36;

    const data = [
        { val: {{ $spot }},    color: '#a78bfa', label: 'Spot',    amt: '{{ number_format($spot, 2) }}' },
        { val: {{ $trade }},   color: '#34d399', label: 'Trading', amt: '{{ number_format($trade, 2) }}' },
        { val: {{ $futures }}, color: '#fb923c', label: 'Futures', amt: '{{ number_format($futures, 2) }}' },
    ];
    const total = data.reduce((s, d) => s + d.val, 0);
    const TOTAL_LABEL = '{{ number_format($totalAssets, 2) }}';

    const drawDonut = (highlightIdx = -1) => {
        ctx.clearRect(0, 0, 140, 140);
        if (total <= 0) {
            const segAngle = (Math.PI * 2) / 3;
            const gap = 0.1;
            data.forEach((d, i) => {
                ctx.beginPath();
                ctx.arc(cx, cy, (r + inner) / 2, i * segAngle + gap, (i + 1) * segAngle - gap);
                ctx.strokeStyle = d.color;
                ctx.globalAlpha = 0.3;
                ctx.lineWidth = r - inner;
                ctx.stroke();
                ctx.globalAlpha = 1;
            });
        } else {
            let angle = -Math.PI / 2;
            data.forEach((d, i) => {
                if (d.val <= 0) return;
                const sweep = (d.val / total) * Math.PI * 2;
                const isHL = highlightIdx === i;
                const gap = 0.04;
                ctx.beginPath();
                ctx.arc(cx, cy, isHL ? r + 4 : r, angle + gap, angle + sweep - gap);
                ctx.arc(cx, cy, isHL ? inner - 4 : inner, angle + sweep - gap, angle + gap, true);
                ctx.closePath();
                ctx.fillStyle = d.color;
                ctx.globalAlpha = isHL ? 1 : (highlightIdx >= 0 ? 0.45 : 1);
                ctx.fill();
                ctx.globalAlpha = 1;
                angle += sweep;
            });
        }
    };

    drawDonut();

    // Legend hover interaction
    document.querySelectorAll('.wov2-legend-item').forEach((el, i) => {
        el.addEventListener('mouseenter', () => {
            drawDonut(i);
            document.getElementById('donutVal').textContent   = data[i].amt;
            document.getElementById('donutLabel').textContent = data[i].label.toUpperCase();
        });
        el.addEventListener('mouseleave', () => {
            drawDonut();
            document.getElementById('donutVal').textContent   = TOTAL_LABEL;
            document.getElementById('donutLabel').textContent = 'TOTAL';
        });
    });

    // Fallback: if a click on the withdraw button somehow doesn't navigate
    // (e.g. blocked by an overlay/gesture library on some mobile browsers),
    // force navigation manually as a safety net.
    const btnWithdraw = document.getElementById('btnWithdraw');
    if (btnWithdraw) {
        btnWithdraw.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            // let default happen; if for some reason it's stopped elsewhere,
            // this ensures navigation still occurs.
            setTimeout(() => {
                if (window.location.pathname.indexOf('withdraw') === -1) {
                    window.location.href = href;
                }
            }, 300);
        });
    }
});
</script>
@endpush