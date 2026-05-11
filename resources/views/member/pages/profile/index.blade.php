@extends('member.layouts.app')
@section('content')
<div class="scrollable-content wallet-overview">

    {{-- ═══ HEADER ═══ --}}
    <div class="wo-header">
        <div class="wo-header-left">
            <div class="wo-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div>
                <div class="wo-name">{{ auth()->user()->name }}</div>
                <div class="wo-uid">UID: {{ auth()->user()->id }}</div>
            </div>
        </div>
    </div>

    {{-- ═══ BALANCE CARD ═══ --}}
    <div class="wo-balance-card">
        <div class="d-flex align-items-start justify-content-between">
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="wo-balance-label">Total Balance</span>
                    <button class="wo-eye-btn" onclick="toggleBalance()" id="eyeBtn">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                    <span class="wo-usdt-badge"><span>₮</span> USDT</span>
                </div>
                <div class="wo-balance-amount" id="balanceDisplay">
                    {{ number_format($balanceBreakdown['total_balance'], 2) }}
                </div>
            </div>
            <div class="wo-earnings-box">
                <div class="wo-earnings-label">Today's Earnings</div>
                <div class="wo-earnings-val {{ $todayPnl >= 0 ? 'positive' : 'negative' }}">
                    {{ $todayPnl >= 0 ? '+' : '' }}{{ number_format($todayPnl, 2) }}
                </div>
                @php
                    $total = $balanceBreakdown['total_balance'];
                    $pct = $total > 0 ? round(abs($todayPnl) / $total * 100, 2) : 0;
                @endphp
                <div class="wo-earnings-pct {{ $todayPnl >= 0 ? 'positive' : 'negative' }}">
                    {{ $todayPnl >= 0 ? '+' : '-' }}{{ $pct }}%
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ ACTION BUTTONS ═══ --}}
    <div class="wo-actions">
        <a href="{{ route('member.deposit.index') }}" class="wo-action-card">
            <div class="wo-action-icon"><i class="bi bi-arrow-down-circle-fill"></i></div>
            <span>{{ __('app.deposit') }}</span>
        </a>
        <a href="{{ route('member.withdraw.index') }}" class="wo-action-card">
            <div class="wo-action-icon"><i class="bi bi-arrow-up-circle-fill"></i></div>
            <span>{{ __('app.withdraw') }}</span>
        </a>
        <a href="{{ route('member.balance.transfer') }}" class="wo-action-card">
            <div class="wo-action-icon"><i class="bi bi-arrow-left-right"></i></div>
            <span>{{ __('app.transfer') }}</span>
        </a>
        <a href="{{ route('member.deposit.history') }}" class="wo-action-card">
            <div class="wo-action-icon"><i class="bi bi-clock-history"></i></div>
            <span>{{ __('app.history') }}</span>
        </a>
    </div>

    {{-- ═══ ASSET ALLOCATION ═══ --}}
    @php
        $spot    = (float) $balanceBreakdown['exchange_balance'];
        $trade   = (float) $balanceBreakdown['trade_balance'];
        $futures = (float) $openFuturesAmount;
        $totalAssets = $spot + $trade + $futures;
    @endphp
    <div class="wo-section">
        <div class="wo-section-head">
            <span>Asset Allocation</span>
            <a href="{{ route('member.profile.index') }}" class="wo-refresh-btn">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
        <div class="wo-total-assets">Total Assets: <strong>{{ number_format($totalAssets, 2) }}</strong></div>

        <div class="wo-donut-wrap">
            <canvas id="donutChart" width="160" height="160"></canvas>
            <div class="wo-donut-center" id="donutCenter">
                <span class="wo-donut-val">{{ number_format($totalAssets, 2) }}</span>
            </div>
        </div>

        <div class="wo-asset-list">
            <a href="{{ route('member.deposit.index') }}" class="wo-asset-row">
                <div class="wo-asset-dot" style="background:#F7931A;"></div>
                <div class="flex-grow-1">
                    <div class="wo-asset-name">Spot Wallet</div>
                </div>
                <div class="text-end">
                    <div class="wo-asset-amount">{{ number_format($spot, 2) }} USDT</div>
                    <div class="wo-asset-usd">≈ ${{ number_format($spot, 0) }}</div>
                </div>
                <i class="bi bi-chevron-right wo-asset-chevron"></i>
            </a>
            <a href="{{ route('member.balance.transfer') }}" class="wo-asset-row">
                <div class="wo-asset-dot" style="background:#2A5ADA;"></div>
                <div class="flex-grow-1">
                    <div class="wo-asset-name">Trading Wallet</div>
                </div>
                <div class="text-end">
                    <div class="wo-asset-amount">{{ number_format($trade, 2) }} USDT</div>
                    <div class="wo-asset-usd">≈ ${{ number_format($trade, 0) }}</div>
                </div>
                <i class="bi bi-chevron-right wo-asset-chevron"></i>
            </a>
            <a href="{{ route('member.futures.index') }}" class="wo-asset-row">
                <div class="wo-asset-dot" style="background:#E6007A;"></div>
                <div class="flex-grow-1">
                    <div class="wo-asset-name">Futures Account</div>
                    @if($futures > 0)
                    <div class="wo-asset-sub">In-play</div>
                    @endif
                </div>
                <div class="text-end">
                    <div class="wo-asset-amount">{{ number_format($futures, 2) }} USDT</div>
                    <div class="wo-asset-usd">≈ ${{ number_format($futures, 0) }}</div>
                </div>
                <i class="bi bi-chevron-right wo-asset-chevron"></i>
            </a>
        </div>
    </div>

    <div style="height:16px;"></div>
</div>

@endsection

@push('styles')
<style>
.wallet-overview { background: var(--bg-dark); }

/* Header */
.wo-header {
    display: flex; align-items: center;
    padding: 16px 20px; border-bottom: 1px solid var(--border-color);
}
.wo-header-left { display: flex; align-items: center; gap: 12px; }
.wo-avatar {
    width: 42px; height: 42px; border-radius: 50%;
    background: linear-gradient(135deg, var(--gold-color), #00b8d4);
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; font-weight: 800; color: #0a0f1e; flex-shrink: 0;
}
.wo-name { color: #fff; font-size: 15px; font-weight: 700; }
.wo-uid { color: var(--text-muted); font-size: 11px; }

/* Balance Card */
.wo-balance-card {
    margin: 16px 16px 0; padding: 20px;
    background: linear-gradient(135deg, #0d1928 0%, #0a1420 100%);
    border-radius: 16px; border: 1px solid rgba(0,229,255,0.15);
}
.wo-balance-label { color: var(--text-muted); font-size: 12px; }
.wo-eye-btn {
    background: none; border: none; color: var(--text-muted);
    padding: 0; cursor: pointer; font-size: 15px; line-height: 1;
}
.wo-usdt-badge {
    display: inline-flex; align-items: center; gap: 4px;
    background: rgba(0,229,255,0.1); border: 1px solid rgba(0,229,255,0.2);
    border-radius: 20px; padding: 2px 8px; font-size: 11px; font-weight: 700; color: var(--gold-color);
}
.wo-balance-amount { font-size: 32px; font-weight: 900; color: #fff; letter-spacing: -1px; margin-top: 6px; }
.wo-earnings-box {
    background: rgba(255,255,255,0.05); border-radius: 10px;
    padding: 10px 12px; min-width: 100px; text-align: right; flex-shrink: 0;
}
.wo-earnings-label { color: var(--text-muted); font-size: 10px; margin-bottom: 4px; }
.wo-earnings-val { font-size: 14px; font-weight: 700; }
.wo-earnings-val.positive { color: #22c55e; }
.wo-earnings-val.negative { color: #ef4444; }
.wo-earnings-pct { font-size: 11px; margin-top: 2px; }
.wo-earnings-pct.positive { color: #22c55e; }
.wo-earnings-pct.negative { color: #ef4444; }

/* Actions */
.wo-actions {
    display: grid; grid-template-columns: repeat(2, 1fr);
    gap: 10px; padding: 16px; margin: 0 0 4px;
}
.wo-action-card {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 16px;
    background: rgba(0,229,255,0.05);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.2s;
}
.wo-action-card:hover {
    background: rgba(0,229,255,0.08);
    border-color: rgba(0,229,255,0.3);
    transform: translateY(-1px);
}
.wo-action-icon {
    width: 40px; height: 40px; border-radius: 10px;
    background: rgba(0,229,255,0.1);
    border: 1px solid rgba(0,229,255,0.2);
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; color: var(--gold-color);
    flex-shrink: 0;
}
.wo-action-card span { color: var(--text-primary); font-size: 13px; font-weight: 600; }

/* Section */
.wo-section { margin: 12px 16px 0; }
.wo-section-head {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 12px; padding: 0 2px;
}
.wo-section-head > span { color: #fff; font-size: 14px; font-weight: 700; }
.wo-refresh-btn { background: none; border: none; color: var(--text-muted); font-size: 16px; cursor: pointer; }
.wo-total-assets { color: var(--text-muted); font-size: 12px; margin-bottom: 16px; }
.wo-total-assets strong { color: #fff; }

/* Donut Chart */
.wo-donut-wrap {
    position: relative; width: 160px; height: 160px; margin: 0 auto 20px;
    display: flex; align-items: center; justify-content: center;
}
.wo-donut-center {
    position: absolute; inset: 0; display: flex; flex-direction: column;
    align-items: center; justify-content: center; pointer-events: none;
}
.wo-donut-val { color: #fff; font-size: 14px; font-weight: 800; }

/* Asset List */
.wo-asset-list {
    background: rgba(255,255,255,0.03); border-radius: 14px;
    border: 1px solid var(--border-color); overflow: hidden;
}
.wo-asset-row {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 16px; border-bottom: 1px solid var(--border-color);
    text-decoration: none; transition: background 0.15s;
}
.wo-asset-row:last-child { border-bottom: none; }
.wo-asset-row:hover { background: rgba(255,255,255,0.03); }
.wo-asset-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.wo-asset-name { color: #fff; font-size: 13px; font-weight: 600; }
.wo-asset-sub { color: var(--text-muted); font-size: 10px; margin-top: 1px; }
.wo-asset-amount { color: #fff; font-size: 13px; font-weight: 700; }
.wo-asset-usd { color: var(--text-muted); font-size: 11px; }
.wo-asset-chevron { color: var(--text-muted); font-size: 12px; margin-left: 4px; }


</style>
@endpush

@push('scripts')
<script>
// ── Balance hide/show ─────────────────────────────────────────
const REAL_BALANCE = '{{ number_format($balanceBreakdown['total_balance'], 2) }}';
let balanceHidden = false;
function toggleBalance() {
    balanceHidden = !balanceHidden;
    document.getElementById('balanceDisplay').textContent = balanceHidden ? '••••••' : REAL_BALANCE;
    document.getElementById('eyeIcon').className = balanceHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
}

// ── Donut Chart ───────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('donutChart');
    const ctx    = canvas.getContext('2d');
    const cx = 80, cy = 80, r = 58, inner = 38;

    const data = [
        { val: {{ $spot }},    color: '#F7931A' },
        { val: {{ $trade }},   color: '#2A5ADA' },
        { val: {{ $futures }}, color: '#E6007A' },
    ];
    const total = data.reduce((s, d) => s + d.val, 0);

    if (total <= 0) {
        // Empty state: gray ring with 3 equal dashed segments
        const segAngle = (Math.PI * 2) / 3;
        const gap = 0.08;
        data.forEach((d, i) => {
            ctx.beginPath();
            ctx.arc(cx, cy, (r + inner) / 2, i * segAngle + gap, (i + 1) * segAngle - gap);
            ctx.strokeStyle = d.color;
            ctx.lineWidth = r - inner;
            ctx.stroke();
        });
    } else {
        let angle = -Math.PI / 2;
        data.forEach(d => {
            if (d.val <= 0) return;
            const sweep = (d.val / total) * Math.PI * 2;
            ctx.beginPath();
            ctx.moveTo(cx, cy);
            ctx.arc(cx, cy, r, angle, angle + sweep);
            ctx.closePath();
            ctx.fillStyle = d.color;
            ctx.fill();
            angle += sweep;
        });
        // Punch inner hole
        ctx.beginPath();
        ctx.arc(cx, cy, inner, 0, Math.PI * 2);
        ctx.fillStyle = getComputedStyle(document.documentElement).getPropertyValue('--bg-dark').trim() || '#0a0f1e';
        ctx.fill();
    }
});

</script>
@endpush
