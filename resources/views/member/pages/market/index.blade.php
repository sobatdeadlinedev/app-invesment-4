@extends('member.layouts.app')

@section('content')
<div class="scrollable-content">

    {{-- ═══ HEADER ═══ --}}
    <div class="mkt-header">
        <h5 class="mkt-title">{{ __('app.market') }}</h5>
        <span class="mkt-live-badge">
            <span class="live-dot"></span> {{ __('app.live') }}
        </span>
    </div>

    {{-- ═══ FILTER TABS ═══ --}}
    <div class="mfilter-wrap px-3 mb-2">
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
    <div class="mlist-header px-3">
        <span>{{ __('app.name_col') }}</span>
        <span>{{ __('app.last_price') }}</span>
        <span>{{ __('app.change_24h') }}</span>
    </div>

    {{-- ═══ CRYPTO ═══ --}}
    @foreach ($coinsByCategory['crypto'] as $coin)
        @php
            $priceData = $allPrices[$coin['symbol']] ?? ['price' => '0.00', 'change' => '0.00', 'isPositive' => true];
            $base = str_replace('USDT', '', $coin['symbol']);
        @endphp
        <a href="{{ route('member.invest.coin', ['coin' => strtolower($coin['symbol'])]) }}"
           class="mlist-row" data-symbol="{{ $coin['symbol'] }}" data-category="crypto">
            <div class="mlist-coin">
                <div class="mlist-icon" style="background:{{ $coin['color'] }}20; border-color:{{ $coin['color'] }}50;">
                    <i class="{{ $coin['icon'] }}" style="color:{{ $coin['color'] }};"></i>
                </div>
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
                <div class="mlist-icon" style="background:{{ $coin['color'] }}20; border-color:{{ $coin['color'] }}50;">
                    <i class="{{ $coin['icon'] }}" style="color:{{ $coin['color'] }};"></i>
                </div>
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
                <div class="mlist-icon" style="background:{{ $coin['color'] }}20; border-color:{{ $coin['color'] }}50;">
                    <i class="{{ $coin['icon'] }}" style="color:{{ $coin['color'] }};"></i>
                </div>
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
        padding: 20px 20px 12px;
        border-bottom: 1px solid var(--border-color);
    }
    .mkt-title {
        color: #fff;
        font-size: 18px;
        font-weight: 700;
        margin: 0;
    }
    .mkt-live-badge {
        display: flex;
        align-items: center;
        gap: 6px;
        background: rgba(34,197,94,0.1);
        border: 1px solid rgba(34,197,94,0.3);
        border-radius: 20px;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 600;
        color: #22c55e;
    }
    .live-dot {
        width: 7px;
        height: 7px;
        background: #22c55e;
        border-radius: 50%;
        animation: livePulse 1.5s infinite;
    }
    @keyframes livePulse {
        0%, 100% { opacity: 1; }
        50%       { opacity: 0.3; }
    }

    /* ── FILTER TABS ── */
    .mfilter-wrap {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding: 16px 20px 8px;
        scrollbar-width: none;
    }
    .mfilter-wrap::-webkit-scrollbar { display: none; }
    .mfilter-tab {
        flex-shrink: 0;
        padding: 7px 14px;
        background: rgba(255,255,255,0.04);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        color: var(--text-muted);
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .mfilter-tab:hover { background: rgba(0,229,255,0.08); color: var(--text-primary); }
    .mfilter-tab.active {
        background: rgba(0,229,255,0.12);
        border-color: rgba(0,229,255,0.4);
        color: var(--gold-color);
    }
    .mfilter-tab i { font-size: 12px; }

    /* ── TABLE HEADER ── */
    .mlist-header {
        display: grid;
        grid-template-columns: 1fr auto auto;
        gap: 8px;
        padding: 10px 20px;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 600;
    }
    .mlist-header span:nth-child(2) { min-width: 80px; text-align: right; }
    .mlist-header span:nth-child(3) { min-width: 64px; text-align: right; }

    /* ── ROWS ── */
    .mlist-row {
        display: grid;
        grid-template-columns: 1fr auto auto;
        gap: 8px;
        align-items: center;
        padding: 12px 20px;
        border-bottom: 1px solid var(--border-color);
        text-decoration: none;
        transition: background 0.2s ease;
        cursor: pointer;
    }
    .mlist-row:hover { background: rgba(0,229,255,0.03); }
    .mlist-row:last-of-type { border-bottom: none; }

    .mlist-coin { display: flex; align-items: center; gap: 12px; }
    .mlist-icon {
        width: 38px; height: 38px; flex-shrink: 0;
        border-radius: 50%; border: 1px solid;
        display: flex; align-items: center; justify-content: center;
    }
    .mlist-icon i { font-size: 18px; }
    .mlist-symbol { color: #fff; font-size: 13px; font-weight: 700; }
    .mlist-quote  { color: var(--text-muted); font-size: 11px; font-weight: 400; }
    .mlist-name   { color: var(--text-muted); font-size: 11px; margin-top: 1px; }

    .mlist-price {
        min-width: 80px; text-align: right;
        color: #fff; font-size: 13px; font-weight: 600;
    }
    .mlist-change {
        min-width: 64px; text-align: right;
        font-size: 12px; font-weight: 700;
        padding: 4px 8px; border-radius: 6px;
    }
    .mlist-change.positive { color: #22c55e; background: rgba(34,197,94,0.1); }
    .mlist-change.negative { color: #ef4444; background: rgba(239,68,68,0.1); }

    /* ── PRICE FLASH ── */
    @keyframes priceFlash {
        0%   { background: rgba(0,229,255,0.15); }
        100% { background: transparent; }
    }
    .price-updated { animation: priceFlash 0.5s ease; }
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
            setTimeout(() => el.classList.remove('price-updated'), 500);
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
