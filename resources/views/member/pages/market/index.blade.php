@extends('member.layouts.app')

@section('content')
<div class="scrollable-content">

    {{-- ═══ HEADER ═══ --}}
    <div class="mkt-header">
        <div>
            <h5 class="mkt-title">{{ __('app.market') }}</h5>
            <p class="mkt-sub">Real-time prices</p>
        </div>
        <span class="mkt-live-badge">
            <span class="live-dot"></span> {{ __('app.live') }}
        </span>
    </div>

    {{-- ═══ FILTER TABS ═══ --}}
    <div class="mfilter-wrap">
        <button class="mfilter-tab active" data-filter="all">
            <i class="bi bi-fire"></i> {{ __('app.hot') }}
        </button>
        <button class="mfilter-tab" data-filter="crypto">
            <i class="bi bi-currency-bitcoin"></i> {{ __('app.crypto') }}
        </button>
        <button class="mfilter-tab" data-filter="forex">
            <i class="bi bi-currency-exchange"></i> {{ __('app.forex') }}
        </button>
        <button class="mfilter-tab" data-filter="precious">
            <i class="bi bi-gem"></i> {{ __('app.metals') }}
        </button>
    </div>

    {{-- ═══ TABLE HEADER ═══ --}}
    <div class="mlist-header">
        <span>{{ __('app.name_col') }}</span>
        <span>{{ __('app.last_price') }}</span>
        <span>{{ __('app.change_24h') }}</span>
    </div>

    <div class="mlist-body">
    {{-- ═══ CRYPTO ═══ --}}
    @foreach ($coinsByCategory['crypto'] as $coin)
        @php
            $priceData = $allPrices[$coin['symbol']] ?? ['price' => '0.00', 'change' => '0.00', 'isPositive' => true];
            $base = str_replace('USDT', '', $coin['symbol']);
        @endphp
        <a href="{{ route('member.invest.coin', ['coin' => strtolower($coin['symbol'])]) }}"
           class="mlist-row" data-symbol="{{ $coin['symbol'] }}" data-category="crypto">
            <div class="mlist-coin">
                <div>
                    <div class="mlist-symbol">{{ $base }}<span class="mlist-quote">/USDT</span></div>
                    <div class="mlist-name">{{ $coin['name'] }}</div>
                </div>
            </div>
            <div class="mlist-price coin-price" data-symbol="{{ $coin['symbol'] }}">${{ $priceData['price'] }}</div>
            <div class="mlist-change price-change {{ $priceData['isPositive'] ? 'positive' : 'negative' }}" data-symbol="{{ $coin['symbol'] }}">
                {{ $priceData['isPositive'] ? '+' : '' }}{{ $priceData['change'] }}%
            </div>
        </a>
    @endforeach

    {{-- ═══ FOREX ═══ --}}
    @foreach ($coinsByCategory['forex'] as $coin)
        @php
            $priceData = $allPrices[$coin['symbol']] ?? ['price' => '0.00', 'change' => '0.00', 'isPositive' => true];
            $base = preg_replace('/USD(T)?$/', '', $coin['symbol']);
        @endphp
        <a href="{{ route('member.invest.coin', ['coin' => strtolower($coin['symbol'])]) }}"
           class="mlist-row" data-symbol="{{ $coin['symbol'] }}" data-category="forex">
            <div class="mlist-coin">
                <div>
                    <div class="mlist-symbol">{{ $base }}<span class="mlist-quote">/USD</span></div>
                    <div class="mlist-name">{{ $coin['name'] }}</div>
                </div>
            </div>
            <div class="mlist-price coin-price" data-symbol="{{ $coin['symbol'] }}">${{ $priceData['price'] }}</div>
            <div class="mlist-change price-change {{ $priceData['isPositive'] ? 'positive' : 'negative' }}" data-symbol="{{ $coin['symbol'] }}">
                {{ $priceData['isPositive'] ? '+' : '' }}{{ $priceData['change'] }}%
            </div>
        </a>
    @endforeach

    {{-- ═══ PRECIOUS METALS ═══ --}}
    @foreach ($coinsByCategory['precious'] as $coin)
        @php
            $priceData = $allPrices[$coin['symbol']] ?? ['price' => '0.00', 'change' => '0.00', 'isPositive' => true];
            $base = str_replace('USD', '', $coin['symbol']);
        @endphp
        <a href="{{ route('member.invest.coin', ['coin' => strtolower($coin['symbol'])]) }}"
           class="mlist-row" data-symbol="{{ $coin['symbol'] }}" data-category="precious">
            <div class="mlist-coin">
                <div>
                    <div class="mlist-symbol">{{ $base }}<span class="mlist-quote">/USD</span></div>
                    <div class="mlist-name">{{ $coin['name'] }}</div>
                </div>
            </div>
            <div class="mlist-price coin-price" data-symbol="{{ $coin['symbol'] }}">${{ $priceData['price'] }}</div>
            <div class="mlist-change price-change {{ $priceData['isPositive'] ? 'positive' : 'negative' }}" data-symbol="{{ $coin['symbol'] }}">
                {{ $priceData['isPositive'] ? '+' : '' }}{{ $priceData['change'] }}%
            </div>
        </a>
    @endforeach

    </div>

    <div style="height: 16px;"></div>
</div>
@endsection

@push('styles')
<style>
/* ── HEADER ── */
.mkt-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 20px 16px;
    background: #070e1a;
    border-bottom: 1px solid rgba(255,255,255,0.06);
}
.mkt-title {
    color: #fff;
    font-size: 18px;
    font-weight: 800;
    margin: 0 0 2px;
    letter-spacing: -0.3px;
}
.mkt-sub {
    color: rgba(255,255,255,0.3);
    font-size: 11px;
    margin: 0;
    letter-spacing: 0.3px;
}
.mkt-live-badge {
    display: flex;
    align-items: center;
    gap: 6px;
    background: rgba(16,185,129,0.1);
    border: 1px solid rgba(16,185,129,0.25);
    border-radius: 20px;
    padding: 5px 12px;
    font-size: 11px;
    font-weight: 700;
    color: #10b981;
    letter-spacing: 0.8px;
    text-transform: uppercase;
}
.live-dot {
    width: 6px; height: 6px;
    background: #10b981;
    border-radius: 50%;
    animation: livePulse 1.5s infinite;
}
@keyframes livePulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%       { opacity: 0.3; transform: scale(0.7); }
}

/* ── FILTER TABS ── */
.mfilter-wrap {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    padding: 14px 20px;
    scrollbar-width: none;
    background: #070e1a;
    border-bottom: 1px solid rgba(255,255,255,0.06);
}
.mfilter-wrap::-webkit-scrollbar { display: none; }
.mfilter-tab {
    flex-shrink: 0;
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 7px 14px;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 8px;
    color: rgba(255,255,255,0.4);
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
    letter-spacing: 0.2px;
}
.mfilter-tab:hover {
    background: rgba(59,130,246,0.08);
    border-color: rgba(59,130,246,0.2);
    color: #fff;
}
.mfilter-tab.active {
    background: rgba(59,130,246,0.15);
    border-color: rgba(59,130,246,0.4);
    color: #60a5fa;
}
.mfilter-tab i { font-size: 12px; }

/* ── TABLE HEADER ── */
.mlist-header {
    display: grid;
    grid-template-columns: 1fr auto auto;
    gap: 10px;
    padding: 10px 20px;
    color: rgba(255,255,255,0.3);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
}
.mlist-header span:nth-child(2) { min-width: 90px; text-align: right; }
.mlist-header span:nth-child(3) { min-width: 78px; text-align: right; }

/* ── BODY WRAPPER ── */
.mlist-body {
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 2px 14px 0;
}

/* ── ROWS ── */
.mlist-row {
    display: grid;
    grid-template-columns: 1fr auto auto;
    gap: 10px;
    align-items: center;
    padding: 14px 16px;
    text-decoration: none;
    transition: background 0.15s ease, border-color 0.15s ease, transform 0.15s ease;
    position: relative;
    cursor: pointer;
    background: rgba(255,255,255,0.025);
    border: 1px solid rgba(255,255,255,0.055);
    border-radius: 14px;
}
.mlist-row:hover {
    background: rgba(59,130,246,0.06);
    border-color: rgba(59,130,246,0.25);
    transform: translateY(-1px);
}
.mlist-row:active { transform: translateY(0); }

.mlist-coin { display: flex; align-items: center; }
.mlist-symbol {
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: -0.2px;
}
.mlist-quote  { color: rgba(255,255,255,0.32); font-size: 11px; font-weight: 500; }
.mlist-name   { color: rgba(255,255,255,0.38); font-size: 11px; margin-top: 3px; }

.mlist-price {
    min-width: 90px;
    text-align: right;
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    font-variant-numeric: tabular-nums;
    letter-spacing: -0.3px;
}
.mlist-change {
    min-width: 78px;
    text-align: center;
    font-size: 11.5px;
    font-weight: 700;
    padding: 6px 10px;
    border-radius: 20px;
    font-variant-numeric: tabular-nums;
    border: 1px solid transparent;
}
.mlist-change.positive {
    color: #34d399;
    background: rgba(16,185,129,0.12);
    border-color: rgba(16,185,129,0.25);
}
.mlist-change.negative {
    color: #f87171;
    background: rgba(239,68,68,0.1);
    border-color: rgba(239,68,68,0.22);
}

/* ── SECTION LABEL (optional group heading) ── */
.mlist-group-label {
    color: rgba(255,255,255,0.28);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    padding: 10px 6px 2px;
}

/* ── PRICE FLASH ── */
@keyframes priceFlash {
    0%   { color: #60a5fa; }
    100% { color: #fff; }
}
.price-updated { animation: priceFlash 0.6s ease; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Filter Tabs ──
    document.querySelectorAll('.mfilter-tab').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.mfilter-tab').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const filter = this.dataset.filter;
            document.querySelectorAll('.mlist-row').forEach(row => {
                row.style.display = (filter === 'all' || row.dataset.category === filter) ? '' : 'none';
            });
        });
    });

    // ── Real-time Price Updates ──
    const CONFIG = {
        interval  : 10000,
        priceRoute: '{{ route('member.market.prices') }}',
        csrf      : '{{ csrf_token() }}'
    };

    let timer   = null;
    let visible = true;

    document.addEventListener('visibilitychange', () => {
        visible = !document.hidden;
        visible ? start() : stop();
    });

    function stop() { clearInterval(timer); timer = null; }

    function start() {
        stop();
        const symbols = getSymbols();
        fetchPrices(symbols);
        timer = setInterval(() => { if (visible) fetchPrices(symbols); }, CONFIG.interval);
    }

    function getSymbols() {
        return [...new Set(
            Array.from(document.querySelectorAll('[data-symbol]'))
                 .map(el => el.dataset.symbol)
        )];
    }

    async function fetchPrices(symbols) {
        try {
            const ctrl = new AbortController();
            const tid  = setTimeout(() => ctrl.abort(), 8000);
            const res  = await fetch(CONFIG.priceRoute, {
                method : 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CONFIG.csrf },
                body   : JSON.stringify({ symbols }),
                signal : ctrl.signal
            });
            clearTimeout(tid);
            if (!res.ok) return;
            const { success, data } = await res.json();
            if (success && data) {
                requestAnimationFrame(() => {
                    Object.entries(data).forEach(([sym, d]) => updateRow(sym, d));
                });
            }
        } catch (_) {}
    }

    function updateRow(symbol, d) {
        document.querySelectorAll(`.coin-price[data-symbol="${symbol}"]`).forEach(el => {
            el.textContent = '$' + d.price;
            el.classList.add('price-updated');
            setTimeout(() => el.classList.remove('price-updated'), 600);
        });
        document.querySelectorAll(`.price-change[data-symbol="${symbol}"]`).forEach(el => {
            el.classList.remove('positive', 'negative');
            el.classList.add(d.isPositive ? 'positive' : 'negative');
            el.textContent = (d.isPositive ? '+' : '') + d.change + '%';
        });
    }

    start();
    window.addEventListener('beforeunload', stop);
});
</script>
@endpush