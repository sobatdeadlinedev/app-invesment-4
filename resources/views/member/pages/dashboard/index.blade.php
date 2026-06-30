@extends('member.layouts.app')

@section('content')
<div class="scrollable-content">

    {{-- ═══ HERO — Welcome + Balance ═══ --}}
    <div class="dash-hero">
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

        <div class="hero-balance-block">
            <p class="hero-balance-label">{{ __('app.total_balance') }}</p>
            <h2 class="hero-balance-amount">
                <span class="hero-balance-int">{{ number_format((auth()->user()->exchange_balance ?? 0) + (auth()->user()->trade_balance ?? 0), 2) }}</span>
                <span class="hero-balance-currency">USDT</span>
            </h2>
        </div>

        <div class="hero-cards-row">
            <div class="hero-card">
                <div class="hero-card-icon hero-card-icon--blue">
                    <i class="bi bi-arrow-down-circle-fill"></i>
                </div>
                <div>
                    <div class="hero-card-label">{{ __('app.exchange') }}</div>
                    <div class="hero-card-val">{{ number_format(auth()->user()->exchange_balance ?? 0, 2) }} <span>USDT</span></div>
                </div>
            </div>
            <div class="hero-card-divider"></div>
            <div class="hero-card">
                <div class="hero-card-icon hero-card-icon--green">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <div>
                    <div class="hero-card-label">{{ __('app.trade') }}</div>
                    <div class="hero-card-val">{{ number_format(auth()->user()->trade_balance ?? 0, 2) }} <span>USDT</span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ QUICK ACTIONS ═══ --}}
    <div class="dash-actions">
        <a href="{{ route('member.deposit.index') }}" class="action-item">
            <div class="action-icon action-icon--deposit">
                <i class="bi bi-arrow-down-circle-fill"></i>
            </div>
            <span>{{ __('app.deposit') }}</span>
        </a>
        <a href="{{ route('member.withdraw.index') }}" class="action-item">
            <div class="action-icon action-icon--withdraw">
                <i class="bi bi-arrow-up-circle-fill"></i>
            </div>
            <span>{{ __('app.withdraw') }}</span>
        </a>
        <a href="{{ route('member.balance.transfer') }}" class="action-item">
            <div class="action-icon action-icon--transfer">
                <i class="bi bi-arrow-left-right"></i>
            </div>
            <span>{{ __('app.transfer') }}</span>
        </a>
        <a href="{{ route('member.team.index') }}" class="action-item">
            <div class="action-icon action-icon--invite">
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
   HERO
════════════════════════════════ */
.dash-hero {
    padding: 20px 20px 0;
    background: #070e1a;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    position: relative;
    overflow: hidden;
}
.dash-hero::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 220px; height: 220px;
    background: radial-gradient(circle, rgba(59,130,246,0.12) 0%, transparent 70%);
    pointer-events: none;
}
.dash-hero::after {
    content: '';
    position: absolute;
    bottom: 0; left: -40px;
    width: 140px; height: 140px;
    background: radial-gradient(circle, rgba(16,185,129,0.08) 0%, transparent 70%);
    pointer-events: none;
}

.hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
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

.hero-balance-block {
    margin-bottom: 20px;
}
.hero-balance-label {
    color: rgba(255,255,255,0.4);
    font-size: 11px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    margin: 0 0 6px;
}
.hero-balance-amount {
    display: flex;
    align-items: baseline;
    gap: 8px;
    margin: 0;
}
.hero-balance-int {
    color: #fff;
    font-size: 34px;
    font-weight: 800;
    letter-spacing: -1px;
    line-height: 1;
    font-variant-numeric: tabular-nums;
}
.hero-balance-currency {
    color: #3b82f6;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 0.5px;
}

.hero-cards-row {
    display: flex;
    align-items: stretch;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 14px 14px 0 0;
    overflow: hidden;
    margin: 0 -20px;
    padding: 0 20px;
}
.hero-card {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 0;
}
.hero-card-divider {
    width: 1px;
    background: rgba(255,255,255,0.07);
    margin: 12px 16px;
}
.hero-card-icon {
    width: 34px; height: 34px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.hero-card-icon--blue { background: rgba(59,130,246,0.15); }
.hero-card-icon--blue i { color: #60a5fa; font-size: 16px; }
.hero-card-icon--green { background: rgba(16,185,129,0.15); }
.hero-card-icon--green i { color: #34d399; font-size: 16px; }
.hero-card-label {
    color: rgba(255,255,255,0.4);
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    margin-bottom: 2px;
}
.hero-card-val {
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    font-variant-numeric: tabular-nums;
}
.hero-card-val span {
    color: rgba(255,255,255,0.35);
    font-size: 10px;
    font-weight: 500;
}

/* ════════════════════════════════
   QUICK ACTIONS
════════════════════════════════ */
.dash-actions {
    display: flex;
    justify-content: space-around;
    padding: 20px 8px;
    background: #070e1a;
    border-bottom: 1px solid rgba(255,255,255,0.06);
}
.action-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 7px;
    text-decoration: none;
    color: rgba(255,255,255,0.45);
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.3px;
    text-transform: uppercase;
    transition: color 0.2s;
}
.action-item:hover { color: #fff; }
.action-icon {
    width: 50px; height: 50px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    transition: transform 0.2s, box-shadow 0.2s;
    border: 1px solid transparent;
}
.action-item:hover .action-icon { transform: translateY(-2px); }
.action-icon i { font-size: 20px; }

.action-icon--deposit {
    background: rgba(59,130,246,0.12);
    border-color: rgba(59,130,246,0.25);
}
.action-icon--deposit i { color: #60a5fa; }
.action-item:hover .action-icon--deposit { box-shadow: 0 6px 20px rgba(59,130,246,0.25); }

.action-icon--withdraw {
    background: rgba(239,68,68,0.1);
    border-color: rgba(239,68,68,0.2);
}
.action-icon--withdraw i { color: #f87171; }
.action-item:hover .action-icon--withdraw { box-shadow: 0 6px 20px rgba(239,68,68,0.2); }

.action-icon--transfer {
    background: rgba(139,92,246,0.12);
    border-color: rgba(139,92,246,0.25);
}
.action-icon--transfer i { color: #a78bfa; }
.action-item:hover .action-icon--transfer { box-shadow: 0 6px 20px rgba(139,92,246,0.25); }

.action-icon--invite {
    background: rgba(16,185,129,0.1);
    border-color: rgba(16,185,129,0.2);
}
.action-icon--invite i { color: #34d399; }
.action-item:hover .action-icon--invite { box-shadow: 0 6px 20px rgba(16,185,129,0.2); }

/* ════════════════════════════════
   BANNERS
════════════════════════════════ */
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
    border: 1px solid rgba(255,255,255,0.07);
}
.banner-announcement {
    background: linear-gradient(135deg, rgba(59,130,246,0.1) 0%, rgba(16,185,129,0.06) 100%);
    border-color: rgba(59,130,246,0.2);
}
.banner-trade {
    background: linear-gradient(135deg, rgba(59,130,246,0.1) 0%, rgba(99,102,241,0.06) 100%);
    border-color: rgba(59,130,246,0.2);
}
.banner-invite {
    background: linear-gradient(135deg, rgba(16,185,129,0.1) 0%, rgba(59,130,246,0.05) 100%);
    border-color: rgba(16,185,129,0.2);
}
.banner-signal {
    background: linear-gradient(135deg, rgba(139,92,246,0.1) 0%, rgba(59,130,246,0.05) 100%);
    border-color: rgba(139,92,246,0.2);
}
.banner-title { color: #fff; font-size: 13px; font-weight: 700; margin: 0 0 4px; }
.banner-text  { color: rgba(255,255,255,0.45); font-size: 12px; margin: 0; line-height: 1.5; }
.banner-ico {
    width: 38px; height: 38px; flex-shrink: 0;
    background: rgba(59,130,246,0.15); border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
}
.banner-ico i { font-size: 18px; color: #60a5fa; }
.banner-thumb {
    width: 52px; height: 52px; flex-shrink: 0; border-radius: 12px;
    background: rgba(59,130,246,0.12);
    display: flex; align-items: center; justify-content: center;
}
.banner-thumb i { font-size: 24px; color: #60a5fa; }
.banner-thumb-gold { background: rgba(234,179,8,0.12); }
.banner-thumb-gold i { color: #fbbf24; }
.banner-thumb-purple { background: rgba(139,92,246,0.15); }
.banner-thumb-purple i { color: #a78bfa; }
.banner-dots {
    display: flex; justify-content: center; align-items: center;
    gap: 5px; margin-top: 10px;
}
.banner-dot {
    width: 5px; height: 5px; border-radius: 50%;
    background: rgba(255,255,255,0.15); transition: all 0.3s ease;
    cursor: pointer;
}
.banner-dot.active {
    background: #3b82f6; width: 16px; border-radius: 3px;
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
    .hero-balance-int { font-size: 28px; }
    .action-icon { width: 44px; height: 44px; }
    .action-icon i { font-size: 18px; }
    .mini-price { min-width: 68px; font-size: 11px; }
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

        setInterval(() => goTo((current + 1) % slides.length), 4000);

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