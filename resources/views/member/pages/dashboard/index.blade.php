@extends('member.layouts.app')

@section('content')
<div class="scrollable-content">

    {{-- ═══ HERO TOP — Welcome greeting only ═══ --}}
    <div class="dash-hero-top">
        <div class="hero-top">
            <div class="hero-user">
                <div class="hero-avatar">
                    <i class="bi bi-person-fill"></i>
                </div>
                <div>
                    <p class="hero-greeting">{{ __('app.welcome_back') }}</p>
                    <h5 class="hero-name">{{ auth()->user()->name }}</h5>
                </div>
            </div>
            <div class="hero-badge">LIVE</div>
        </div>
    </div>

    {{-- ═══ MINI TICKER CARDS — Sparkline Preview ═══ --}}
    <div class="dash-ticker-row">
        <div class="ticker-track" id="tickerTrack">
            @php
                $tickerSymbols = ['BTCUSDT', 'ETHUSDT', 'DOGEUSDT'];
            @endphp
            @foreach ($tickerSymbols as $sym)
                @php
                    $coin      = $availableCoins[$sym] ?? null;
                    $priceData = $allPrices[$sym] ?? ['price' => '0.00', 'change' => '0.00', 'isPositive' => true];
                    if (!$coin) continue;
                    $base = preg_replace('/USD(T)?$/', '', $sym);
                @endphp
                <a href="{{ route('member.invest.coin', ['coin' => strtolower($sym)]) }}"
                   class="ticker-card ticker-card--{{ $priceData['isPositive'] ? 'up' : 'down' }}"
                   data-symbol="{{ $sym }}"
                   data-positive="{{ $priceData['isPositive'] ? '1' : '0' }}">
                    <div class="ticker-card-top">
                        <span class="ticker-symbol">{{ $base }}<span class="ticker-quote">/{{ str_ends_with($sym, 'USDT') ? 'USDT' : 'USD' }}</span></span>
                        <span class="ticker-badge ticker-badge--{{ $priceData['isPositive'] ? 'up' : 'down' }} ticker-change" data-symbol="{{ $sym }}">
                            {{ $priceData['isPositive'] ? '+' : '' }}{{ $priceData['change'] }}%
                        </span>
                    </div>
                    <div class="ticker-price ticker-price-val" data-symbol="{{ $sym }}">{{ $priceData['price'] }}</div>
                    <div class="ticker-spark">
                        <svg viewBox="0 0 100 32" preserveAspectRatio="none" class="ticker-spark-svg" data-symbol="{{ $sym }}">
                            <defs>
                                <linearGradient id="grad-{{ $sym }}" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="{{ $priceData['isPositive'] ? '#10b981' : '#ef4444' }}" stop-opacity="0.35"/>
                                    <stop offset="100%" stop-color="{{ $priceData['isPositive'] ? '#10b981' : '#ef4444' }}" stop-opacity="0"/>
                                </linearGradient>
                            </defs>
                            <path class="ticker-spark-area" fill="url(#grad-{{ $sym }})" stroke="none" d=""></path>
                            <path class="ticker-spark-line" fill="none" stroke="{{ $priceData['isPositive'] ? '#10b981' : '#ef4444' }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d=""></path>
                        </svg>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    {{-- ═══ MINI MARKET ═══ --}}
    <div class="dash-mini-market">

        <div class="mini-market-header px-3">
            <div class="mini-market-title-wrap">
                <span class="mini-market-title">{{ __('app.market') }}</span>
                <span class="mini-market-pulse"></span>
            </div>
            <a href="{{ route('member.market.index') }}" class="mini-market-see-all">
                {{ __('app.see_all') }} <i class="bi bi-chevron-right"></i>
            </a>
        </div>

        <div class="mini-market-cols px-3">
            <span class="mini-col-label">Pair</span>
            <span class="mini-col-label text-end">Price</span>
            <span class="mini-col-label text-end">24h</span>
        </div>

        @php
            $featured = ['BTCUSDT', 'ETHUSDT', 'DOGEUSDT', 'XAUUSD', 'EURUSDT'];
        @endphp

        @foreach ($featured as $sym)
            @php
                $coin      = $availableCoins[$sym] ?? null;
                $priceData = $allPrices[$sym] ?? ['price' => '0.00', 'change' => '0.00', 'isPositive' => true];
                if (!$coin) continue;
                $base = preg_replace('/USD(T)?$/', '', $sym);
            @endphp
            <a href="{{ route('member.invest.coin', ['coin' => strtolower($sym)]) }}"
               class="mini-row" data-symbol="{{ $sym }}">
                <div class="mini-coin">
                    <div class="mini-icon" style="background:{{ $coin['color'] }}18; border-color:{{ $coin['color'] }}40;">
                        <i class="{{ $coin['icon'] }}" style="color:{{ $coin['color'] }};"></i>
                    </div>
                    <div>
                        <div class="mini-symbol">{{ $base }}<span class="mini-quote">/{{ str_ends_with($sym, 'USDT') ? 'USDT' : 'USD' }}</span></div>
                        <div class="mini-name">{{ $coin['name'] }}</div>
                    </div>
                </div>
                <div class="mini-price coin-price" data-symbol="{{ $sym }}">${{ $priceData['price'] }}</div>
                <div class="mini-change price-change {{ $priceData['isPositive'] ? 'positive' : 'negative' }}" data-symbol="{{ $sym }}">
                    {{ $priceData['isPositive'] ? '+' : '' }}{{ $priceData['change'] }}%
                </div>
            </a>
        @endforeach

    </div>

</div>
@endsection

@push('styles')
<style>
/* ════════════════════════════════
   HERO TOP (greeting only)
════════════════════════════════ */
.dash-hero-top {
    padding: 20px 20px 16px;
    background: #070e1a;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    position: relative;
    overflow: hidden;
}
.dash-hero-top::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 220px; height: 220px;
    background: radial-gradient(circle, rgba(59,130,246,0.12) 0%, transparent 70%);
    pointer-events: none;
}

.hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    z-index: 1;
}
.hero-user {
    display: flex;
    align-items: center;
    gap: 12px;
}
.hero-avatar {
    width: 40px; height: 40px;
    border-radius: 10px;
    background: rgba(59,130,246,0.15);
    border: 1px solid rgba(59,130,246,0.3);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.hero-avatar i { font-size: 20px; color: #60a5fa; }
.hero-greeting {
    color: rgba(255,255,255,0.4);
    font-size: 11px;
    margin: 0 0 1px;
    letter-spacing: 0.3px;
}
.hero-name {
    color: #fff;
    font-size: 15px;
    font-weight: 700;
    margin: 0;
}
.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: rgba(16,185,129,0.12);
    border: 1px solid rgba(16,185,129,0.3);
    color: #10b981;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 1.5px;
    padding: 4px 10px;
    border-radius: 20px;
}
.hero-badge::before {
    content: '';
    width: 6px; height: 6px;
    background: #10b981;
    border-radius: 50%;
    animation: pulse-dot 1.5s infinite;
}
@keyframes pulse-dot {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.4; transform: scale(0.7); }
}

/* ════════════════════════════════
   MINI TICKER CARDS (sparkline)
════════════════════════════════ */
.dash-ticker-row {
    background: #070e1a;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    padding: 16px 0 18px;
}
.ticker-track {
    display: flex;
    gap: 10px;
    overflow-x: auto;
    scrollbar-width: none;
    padding: 0 20px;
}
.ticker-track::-webkit-scrollbar { display: none; }

.ticker-card {
    flex: 0 0 auto;
    width: 132px;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 14px;
    padding: 12px 12px 8px;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    transition: border-color 0.2s, transform 0.2s;
}
.ticker-card:active { transform: scale(0.97); }
.ticker-card--up   { border-color: rgba(16,185,129,0.18); }
.ticker-card--down { border-color: rgba(239,68,68,0.18); }

.ticker-card-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    margin-bottom: 8px;
}
.ticker-symbol {
    color: #fff;
    font-size: 12px;
    font-weight: 700;
}
.ticker-quote {
    color: rgba(255,255,255,0.3);
    font-size: 9px;
    font-weight: 500;
}
.ticker-badge {
    font-size: 10px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 6px;
    white-space: nowrap;
    font-variant-numeric: tabular-nums;
}
.ticker-badge--up   { color: #10b981; background: rgba(16,185,129,0.12); }
.ticker-badge--down { color: #ef4444; background: rgba(239,68,68,0.1); }

.ticker-price {
    color: #fff;
    font-size: 15px;
    font-weight: 800;
    letter-spacing: -0.3px;
    font-variant-numeric: tabular-nums;
    margin-bottom: 6px;
}

.ticker-spark { width: 100%; height: 32px; }
.ticker-spark-svg { width: 100%; height: 100%; display: block; }

@media (max-width: 375px) {
    .ticker-card { width: 116px; }
}

/* ════════════════════════════════
   MINI MARKET
════════════════════════════════ */
.dash-mini-market {
    padding-top: 20px;
    padding-bottom: 8px;
}
.mini-market-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}
.mini-market-title-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
}
.mini-market-title {
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 0.2px;
}
.mini-market-pulse {
    width: 7px; height: 7px;
    background: #10b981;
    border-radius: 50%;
    animation: pulse-dot 1.5s infinite;
}
.mini-market-see-all {
    color: #3b82f6;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 3px;
    opacity: 0.85;
    transition: opacity 0.2s;
}
.mini-market-see-all:hover { opacity: 1; }
.mini-market-see-all i { font-size: 10px; }

.mini-market-cols {
    display: grid;
    grid-template-columns: 1fr auto auto;
    gap: 8px;
    padding-bottom: 6px;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    margin-bottom: 2px;
}
.mini-col-label {
    color: rgba(255,255,255,0.25);
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.8px;
    text-transform: uppercase;
}

.mini-row {
    display: grid;
    grid-template-columns: 1fr auto auto;
    gap: 8px;
    align-items: center;
    padding: 11px 20px;
    border-bottom: 1px solid rgba(255,255,255,0.04);
    text-decoration: none;
    transition: background 0.15s;
    position: relative;
}
.mini-row::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 2px;
    background: transparent;
    transition: background 0.2s;
}
.mini-row:hover { background: rgba(59,130,246,0.04); }
.mini-row:hover::before { background: #3b82f6; }
.mini-row:last-child { border-bottom: none; }

.mini-coin { display: flex; align-items: center; gap: 10px; }
.mini-icon {
    width: 32px; height: 32px; flex-shrink: 0;
    border-radius: 8px; border: 1px solid;
    display: flex; align-items: center; justify-content: center;
}
.mini-icon i { font-size: 15px; }
.mini-symbol { color: #fff; font-size: 12px; font-weight: 700; }
.mini-quote  { color: rgba(255,255,255,0.3); font-size: 10px; font-weight: 400; }
.mini-name   { color: rgba(255,255,255,0.35); font-size: 10px; margin-top: 1px; }

.mini-price {
    min-width: 80px;
    text-align: right;
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    font-variant-numeric: tabular-nums;
    letter-spacing: -0.3px;
}

.mini-change {
    min-width: 60px;
    text-align: right;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
    font-variant-numeric: tabular-nums;
}
.mini-change.positive { color: #10b981; background: rgba(16,185,129,0.1); }
.mini-change.negative { color: #ef4444; background: rgba(239,68,68,0.08); }

@keyframes priceFlash {
    0%   { color: #60a5fa; }
    100% { color: #fff; }
}
.price-updated { animation: priceFlash 0.6s ease; }

@media (max-width: 375px) {
    .mini-price { min-width: 68px; font-size: 11px; }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Mini Ticker Cards: Sparkline Renderer ──
    (function () {
        const cards = document.querySelectorAll('.ticker-card');
        if (!cards.length) return;

        const history = {};

        function seedHistory(sym, currentPrice, isPositive) {
            const points = 20;
            const arr = [];
            let val = 50;
            for (let i = 0; i < points; i++) {
                const trendBias = isPositive ? 0.6 : -0.6;
                const noise = (Math.random() - 0.5) * 8;
                val += trendBias + noise;
                val = Math.max(10, Math.min(90, val));
                arr.push(val);
            }
            arr[arr.length - 1] = isPositive ? Math.max(arr[arr.length - 2] + 3, 60) : Math.min(arr[arr.length - 2] - 3, 40);
            history[sym] = arr;
        }

        function renderSpark(card, sym) {
            const svg = card.querySelector('.ticker-spark-svg');
            if (!svg || !history[sym]) return;

            const linePath = svg.querySelector('.ticker-spark-line');
            const areaPath = svg.querySelector('.ticker-spark-area');
            const data = history[sym];
            const w = 100, h = 32;
            const step = w / (data.length - 1);

            let d = '';
            data.forEach((v, i) => {
                const x = i * step;
                const y = h - (v / 100) * h;
                d += (i === 0 ? 'M' : 'L') + x.toFixed(1) + ',' + y.toFixed(1) + ' ';
            });
            linePath.setAttribute('d', d.trim());

            const areaD = d.trim() + ` L${w},${h} L0,${h} Z`;
            areaPath.setAttribute('d', areaD);
        }

        cards.forEach(card => {
            const sym = card.dataset.symbol;
            const isPositive = card.dataset.positive === '1';
            seedHistory(sym, 0, isPositive);
            renderSpark(card, sym);
        });

        const ROUTE = '{{ route('member.dashboard.prices') }}';
        const CSRF  = '{{ csrf_token() }}';
        const TICKER_SYMS = Array.from(cards).map(c => c.dataset.symbol);

        function refreshTickers() {
            fetch(ROUTE, {
                method : 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body   : JSON.stringify({ symbols: TICKER_SYMS })
            }).then(r => r.json()).then(({ success, data }) => {
                if (!success || !data) return;
                Object.entries(data).forEach(([sym, d]) => {
                    const card = document.querySelector(`.ticker-card[data-symbol="${sym}"]`);
                    if (!card) return;

                    const priceEl = card.querySelector('.ticker-price-val');
                    if (priceEl) priceEl.textContent = d.price;

                    const badgeEl = card.querySelector('.ticker-change');
                    if (badgeEl) {
                        badgeEl.textContent = (d.isPositive ? '+' : '') + d.change + '%';
                        badgeEl.classList.remove('ticker-badge--up', 'ticker-badge--down');
                        badgeEl.classList.add(d.isPositive ? 'ticker-badge--up' : 'ticker-badge--down');
                    }

                    const arr = history[sym] || [];
                    const nextVal = d.isPositive
                        ? Math.min(90, (arr[arr.length - 1] || 50) + Math.random() * 4)
                        : Math.max(10, (arr[arr.length - 1] || 50) - Math.random() * 4);
                    arr.push(nextVal);
                    if (arr.length > 20) arr.shift();
                    history[sym] = arr;

                    renderSpark(card, sym);
                });
            }).catch(() => {});
        }

        setInterval(refreshTickers, 10000);
    })();

    // ── Mini Market Real-time Prices ──
    (function () {
        const ROUTE = '{{ route('member.dashboard.prices') }}';
        const CSRF  = '{{ csrf_token() }}';
        const SYMS  = ['BTCUSDT', 'ETHUSDT', 'DOGEUSDT', 'XAUUSD', 'EURUSDT'];
        let timer   = null;
        let visible = true;

        document.addEventListener('visibilitychange', () => {
            visible = !document.hidden;
            visible ? start() : stop();
        });

        function stop() { clearInterval(timer); timer = null; }

        function start() {
            stop();
            fetch(ROUTE, {
                method : 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body   : JSON.stringify({ symbols: SYMS })
            }).then(r => r.json()).then(({ success, data }) => {
                if (success && data) updateAll(data);
            }).catch(() => {});

            timer = setInterval(() => {
                if (!visible) return;
                fetch(ROUTE, {
                    method : 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                    body   : JSON.stringify({ symbols: SYMS })
                }).then(r => r.json()).then(({ success, data }) => {
                    if (success && data) requestAnimationFrame(() => updateAll(data));
                }).catch(() => {});
            }, 10000);
        }

        function updateAll(data) {
            Object.entries(data).forEach(([sym, d]) => {
                document.querySelectorAll(`.coin-price[data-symbol="${sym}"]`).forEach(el => {
                    el.textContent = '$' + d.price;
                    el.classList.add('price-updated');
                    setTimeout(() => el.classList.remove('price-updated'), 600);
                });
                document.querySelectorAll(`.price-change[data-symbol="${sym}"]`).forEach(el => {
                    el.classList.remove('positive', 'negative');
                    el.classList.add(d.isPositive ? 'positive' : 'negative');
                    el.textContent = (d.isPositive ? '+' : '') + d.change + '%';
                });
            });
        }

        start();
        window.addEventListener('beforeunload', stop);
    })();

});
</script>
@endpush