@extends('member.layouts.app')

@section('content')
<div class="scrollable-content">

    {{-- ═══ HERO — Welcome + Balance ═══ --}}
    <div class="dash-hero">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <p class="hero-greeting">{{ __('app.welcome_back') }}</p>
                <h5 class="hero-name">{{ auth()->user()->name }}</h5>
            </div>
            <div class="hero-avatar">
                <i class="bi bi-person-fill"></i>
            </div>
        </div>
        <p class="hero-balance-label">{{ __('app.total_balance') }}</p>
        <h2 class="hero-balance-amount">
            {{ number_format((auth()->user()->exchange_balance ?? 0) + (auth()->user()->trade_balance ?? 0), 2) }}
            <span class="hero-balance-currency">USDT</span>
        </h2>
        <div class="hero-stats-row">
            <div class="hero-stat">
                <span class="hero-stat-label">{{ __('app.exchange') }}</span>
                <span class="hero-stat-val">{{ number_format(auth()->user()->exchange_balance ?? 0, 2) }}</span>
            </div>
            <div class="hero-stat-sep"></div>
            <div class="hero-stat">
                <span class="hero-stat-label">{{ __('app.trade') }}</span>
                <span class="hero-stat-val">{{ number_format(auth()->user()->trade_balance ?? 0, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- ═══ QUICK ACTIONS ═══ --}}
    <div class="dash-actions">
        <a href="{{ route('member.deposit.index') }}" class="action-item">
            <div class="action-icon">
                <i class="bi bi-arrow-down-circle-fill"></i>
            </div>
            <span>{{ __('app.deposit') }}</span>
        </a>
        <a href="{{ route('member.withdraw.index') }}" class="action-item">
            <div class="action-icon">
                <i class="bi bi-arrow-up-circle-fill"></i>
            </div>
            <span>{{ __('app.withdraw') }}</span>
        </a>
        <a href="{{ route('member.balance.transfer') }}" class="action-item">
            <div class="action-icon">
                <i class="bi bi-arrow-left-right"></i>
            </div>
            <span>{{ __('app.transfer') }}</span>
        </a>
        <a href="{{ route('member.team.index') }}" class="action-item">
            <div class="action-icon">
                <i class="bi bi-person-plus-fill"></i>
            </div>
            <span>{{ __('app.invite') }}</span>
        </a>
    </div>

    {{-- ═══ BANNER CAROUSEL ═══ --}}
    <div class="dash-banners px-3 mb-4">
        <div class="banner-track" id="bannerTrack">

            @if ($announcement && !empty($announcement))
            <div class="banner-slide">
                <div class="banner-card banner-announcement">
                    <div class="d-flex align-items-start gap-3">
                        <div class="banner-ico"><i class="bi bi-megaphone-fill"></i></div>
                        <div>
                            <h6 class="banner-title">{{ __('app.announcement') }}</h6>
                            <p class="banner-text">{{ $announcement }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="banner-slide">
                <div class="banner-card banner-trade">
                    <div class="d-flex align-items-center gap-3">
                        <div class="banner-thumb">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <div>
                            <h6 class="banner-title">{{ __('app.banner_trade_title') }}</h6>
                            <p class="banner-text">{{ __('app.banner_trade_text') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="banner-slide">
                <div class="banner-card banner-invite">
                    <div class="d-flex align-items-center gap-3">
                        <div class="banner-thumb banner-thumb-gold">
                            <i class="bi bi-trophy-fill"></i>
                        </div>
                        <div>
                            <h6 class="banner-title">{{ __('app.banner_invite_title') }}</h6>
                            <p class="banner-text">{{ __('app.banner_invite_text') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="banner-slide">
                <div class="banner-card banner-signal">
                    <div class="d-flex align-items-center gap-3">
                        <div class="banner-thumb banner-thumb-purple">
                            <i class="bi bi-broadcast"></i>
                        </div>
                        <div>
                            <h6 class="banner-title">{{ __('app.trading_signals') }}</h6>
                            <p class="banner-text">{{ __('app.banner_signal_text') }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="banner-dots" id="bannerDots"></div>
    </div>

    {{-- ═══ MINI MARKET ═══ --}}
    <div class="dash-mini-market">

        {{-- Section Header --}}
        <div class="mini-market-header px-3">
            <span class="mini-market-title">{{ __('app.market') }}</span>
            <a href="{{ route('member.market.index') }}" class="mini-market-see-all">
                {{ __('app.see_all') }} <i class="bi bi-chevron-right"></i>
            </a>
        </div>

        {{-- Featured Coins: BTC, ETH, DOGE, XAU, EUR --}}
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
                    <div class="mini-icon" style="background:{{ $coin['color'] }}20; border-color:{{ $coin['color'] }}50;">
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
    /* ── HERO ── */
    .dash-hero {
        padding: 24px 20px 20px;
        background: linear-gradient(135deg, #0d1928 0%, #0a1420 60%, #091320 100%);
        border-bottom: 1px solid var(--border-color);
        position: relative;
        overflow: hidden;
    }
    .dash-hero::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 160px; height: 160px;
        background: radial-gradient(circle, rgba(0,229,255,0.12) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .hero-greeting {
        color: var(--text-muted);
        font-size: 13px;
        margin: 0 0 2px;
    }
    .hero-name {
        color: #fff;
        font-size: 18px;
        font-weight: 700;
        margin: 0;
    }
    .hero-avatar {
        width: 44px; height: 44px;
        border-radius: 50%;
        background: rgba(0,229,255,0.1);
        border: 2px solid rgba(0,229,255,0.3);
        display: flex; align-items: center; justify-content: center;
    }
    .hero-avatar i { font-size: 24px; color: var(--gold-color); }
    .hero-balance-label {
        color: var(--text-muted);
        font-size: 12px;
        margin-bottom: 4px;
    }
    .hero-balance-amount {
        color: #fff;
        font-size: 32px;
        font-weight: 800;
        letter-spacing: -0.5px;
        margin: 0 0 12px;
        line-height: 1;
    }
    .hero-balance-currency {
        font-size: 16px;
        font-weight: 600;
        color: var(--gold-color);
    }
    .hero-stats-row {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 10px 14px;
        background: rgba(255,255,255,0.04);
        border: 1px solid var(--border-color);
        border-radius: 10px;
    }
    .hero-stat { display: flex; flex-direction: column; gap: 2px; }
    .hero-stat-label { color: var(--text-muted); font-size: 11px; }
    .hero-stat-val { color: var(--gold-color); font-size: 13px; font-weight: 700; }
    .hero-stat-sep { width: 1px; height: 28px; background: var(--border-color); }

    /* ── QUICK ACTIONS ── */
    .dash-actions {
        display: flex;
        justify-content: space-around;
        padding: 20px 12px;
        border-bottom: 1px solid var(--border-color);
    }
    .action-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .action-item:hover { color: var(--gold-color); }
    .action-item:hover .action-icon { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37,99,235,0.45); }
    .action-icon {
        width: 52px; height: 52px;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        border-radius: 50%;
        box-shadow: 0 4px 14px rgba(37,99,235,0.35);
        display: flex; align-items: center; justify-content: center;
        transition: all 0.2s ease;
    }
    .action-icon i { font-size: 22px; color: #fff; }

    /* ── BANNERS ── */
    .dash-banners { padding-top: 20px; }
    .banner-track {
        display: flex;
        gap: 12px;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        scrollbar-width: none;
        padding-bottom: 4px;
    }
    .banner-track::-webkit-scrollbar { display: none; }
    .banner-slide {
        flex-shrink: 0;
        width: calc(100% - 32px);
        scroll-snap-align: start;
    }
    .banner-card {
        border-radius: 14px;
        padding: 16px;
        border: 1px solid var(--border-color);
    }
    .banner-announcement {
        background: linear-gradient(135deg, rgba(0,229,255,0.08) 0%, rgba(0,184,212,0.05) 100%);
        border-color: rgba(0,229,255,0.2);
    }
    .banner-trade {
        background: linear-gradient(135deg, rgba(13,110,253,0.12) 0%, rgba(0,229,255,0.06) 100%);
        border-color: rgba(13,110,253,0.2);
    }
    .banner-invite {
        background: linear-gradient(135deg, rgba(40,167,69,0.1) 0%, rgba(0,229,255,0.05) 100%);
        border-color: rgba(40,167,69,0.2);
    }
    .banner-signal {
        background: linear-gradient(135deg, rgba(138,43,226,0.1) 0%, rgba(0,229,255,0.05) 100%);
        border-color: rgba(138,43,226,0.2);
    }
    .banner-title { color: #fff; font-size: 13px; font-weight: 700; margin: 0 0 4px; }
    .banner-text  { color: var(--text-muted); font-size: 12px; margin: 0; line-height: 1.5; }
    .banner-ico {
        width: 40px; height: 40px; flex-shrink: 0;
        background: rgba(0,229,255,0.1); border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
    }
    .banner-ico i { font-size: 20px; color: var(--gold-color); }
    .banner-thumb {
        width: 56px; height: 56px; flex-shrink: 0; border-radius: 12px;
        background: rgba(13,110,253,0.15);
        display: flex; align-items: center; justify-content: center;
    }
    .banner-thumb i { font-size: 26px; color: #4d94ff; }
    .banner-thumb-gold { background: rgba(255,215,0,0.12); }
    .banner-thumb-gold i { color: #ffd700; }
    .banner-thumb-purple { background: rgba(138,43,226,0.15); }
    .banner-thumb-purple i { color: #a855f7; }
    .banner-dots {
        display: flex; justify-content: flex-end; align-items: center;
        gap: 6px; margin-top: 10px; padding-right: 4px;
    }
    .banner-dot {
        width: 6px; height: 6px; border-radius: 50%;
        background: var(--border-color); transition: all 0.3s ease;
    }
    .banner-dot.active {
        background: var(--gold-color); width: 18px; border-radius: 3px;
    }

    /* ── MINI MARKET ── */
    .dash-mini-market { padding-top: 20px; }
    .mini-market-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
    }
    .mini-market-title {
        color: #fff;
        font-size: 15px;
        font-weight: 700;
    }
    .mini-market-see-all {
        color: var(--gold-color);
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 3px;
        transition: opacity 0.2s ease;
    }
    .mini-market-see-all:hover { opacity: 0.75; }
    .mini-market-see-all i { font-size: 11px; }

    .mini-row {
        display: grid;
        grid-template-columns: 1fr auto auto;
        gap: 8px;
        align-items: center;
        padding: 11px 20px;
        border-bottom: 1px solid var(--border-color);
        text-decoration: none;
        transition: background 0.2s ease;
    }
    .mini-row:hover { background: rgba(0,229,255,0.03); }
    .mini-row:last-child { border-bottom: none; }

    .mini-coin { display: flex; align-items: center; gap: 10px; }
    .mini-icon {
        width: 34px; height: 34px; flex-shrink: 0;
        border-radius: 50%; border: 1px solid;
        display: flex; align-items: center; justify-content: center;
    }
    .mini-icon i { font-size: 16px; }
    .mini-symbol { color: #fff; font-size: 12px; font-weight: 700; }
    .mini-quote  { color: var(--text-muted); font-size: 10px; font-weight: 400; }
    .mini-name   { color: var(--text-muted); font-size: 10px; margin-top: 1px; }
    .mini-price  { min-width: 76px; text-align: right; color: #fff; font-size: 12px; font-weight: 600; }
    .mini-change {
        min-width: 58px; text-align: right;
        font-size: 11px; font-weight: 700;
        padding: 3px 7px; border-radius: 5px;
    }
    .mini-change.positive { color: #22c55e; background: rgba(34,197,94,0.1); }
    .mini-change.negative { color: #ef4444; background: rgba(239,68,68,0.1); }

    @keyframes priceFlash {
        0%   { background: rgba(0,229,255,0.15); }
        100% { background: transparent; }
    }
    .price-updated { animation: priceFlash 0.5s ease; }

    /* ── MARKET CTA ── */
    .dash-market-cta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: 16px 20px 8px;
        padding: 16px;
        background: rgba(0,229,255,0.06);
        border: 1px solid rgba(0,229,255,0.2);
        border-radius: 14px;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .dash-market-cta:hover { background: rgba(0,229,255,0.1); border-color: rgba(0,229,255,0.35); }
    .dash-market-icon {
        width: 42px; height: 42px;
        background: rgba(0,229,255,0.12);
        border: 1px solid rgba(0,229,255,0.3);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
    }
    .dash-market-icon i { font-size: 20px; color: var(--gold-color); }
    .dash-market-label { color: #fff; font-size: 14px; font-weight: 700; }
    .dash-market-sub   { color: var(--text-muted); font-size: 11px; margin-top: 2px; }
    .dash-market-arrow { color: var(--text-muted); font-size: 16px; }

    @media (max-width: 375px) {
        .hero-balance-amount { font-size: 26px; }
        .mlist-row { padding: 10px 16px; }
        .action-icon { width: 46px; height: 46px; }
        .action-icon i { font-size: 20px; }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Banner Slider ──
    (function () {
        const track   = document.getElementById('bannerTrack');
        const dotsEl  = document.getElementById('bannerDots');
        const slides  = track ? track.querySelectorAll('.banner-slide') : [];
        if (!slides.length) return;

        let current = 0;

        // Build dots
        slides.forEach((_, i) => {
            const d = document.createElement('span');
            d.className = 'banner-dot' + (i === 0 ? ' active' : '');
            dotsEl.appendChild(d);
        });

        function goTo(idx) {
            current = idx;
            slides[idx].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
            dotsEl.querySelectorAll('.banner-dot').forEach((d, i) => {
                d.classList.toggle('active', i === idx);
            });
        }

        // Auto-slide every 4s
        setInterval(() => goTo((current + 1) % slides.length), 4000);

        // Sync dots on manual scroll
        if (track) {
            track.addEventListener('scroll', () => {
                const idx = Math.round(track.scrollLeft / track.clientWidth);
                if (idx !== current) {
                    current = idx;
                    dotsEl.querySelectorAll('.banner-dot').forEach((d, i) => {
                        d.classList.toggle('active', i === idx);
                    });
                }
            }, { passive: true });
        }
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
                    setTimeout(() => el.classList.remove('price-updated'), 500);
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
