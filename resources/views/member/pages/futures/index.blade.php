@extends('member.layouts.app')

@section('content')
<div class="scrollable-content" id="futuresPage">

    {{-- ═══ COIN HEADER ═══ --}}
    <div class="seamless-header-section">
        <div class="d-flex align-items-center gap-3">
            <div class="coin-icon-large"
                style="background: linear-gradient(135deg, {{ $coinInfo['color'] }}33 0%, {{ $coinInfo['color'] }}1a 100%);
                       border: 2px solid {{ $coinInfo['color'] }}66;">
                <i class="{{ $coinInfo['icon'] }}" style="color: {{ $coinInfo['color'] }};"></i>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="mb-0 fw-bold" style="color: #fff; font-size: 17px;">
                        {{ $coinInfo['symbol'] }}
                    </h5>
                    <span class="price-live-badge">
                        <span class="live-dot"></span> LIVE
                    </span>
                </div>
                <small class="text-muted">{{ $coinInfo['name'] }}</small>
            </div>
            <div class="d-flex flex-column align-items-end gap-2">
                <button onclick="openCoinPopup()" class="btn-switch-coin">
                    <i class="bi bi-arrow-left-right"></i>
                </button>
                <div class="price-main-header" id="currentPrice">
                    ${{ $currentPrice ? number_format($currentPrice, $currentPrice < 1 ? 6 : ($currentPrice < 100 ? 4 : 2), '.', ',') : '—' }}
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ COIN POPUP ═══ --}}
    <div id="coinPopupOverlay" class="coin-popup-overlay" onclick="closeCoinPopup()" style="display: none;">
        <div class="coin-popup-modal" onclick="event.stopPropagation()">
            <div class="popup-header">
                <h6 class="mb-0 fw-bold">Pilih Instrumen</h6>
                <button class="btn-popup-close" onclick="closeCoinPopup()">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <div class="popup-body">
                @php
                    $categories = [
                        'crypto' => ['label' => 'Cryptocurrency', 'icon' => 'bi-currency-bitcoin'],
                        'forex'  => ['label' => 'Forex',          'icon' => 'bi-currency-exchange'],
                        'metals' => ['label' => 'Precious Metals','icon' => 'bi-gem'],
                    ];
                @endphp
                @foreach ($categories as $cat => $catInfo)
                    @php $catCoins = array_filter($coins, fn($c) => $c['cat'] === $cat); @endphp
                    @if (count($catCoins))
                    <div class="popup-category-header">
                        <i class="bi {{ $catInfo['icon'] }}"></i>
                        {{ $catInfo['label'] }}
                    </div>
                    @foreach ($catCoins as $sym => $info)
                    <a href="{{ route('member.futures.index', ['coin' => $sym]) }}"
                       class="coin-popup-item {{ $coin === $sym ? 'active' : '' }}">
                        <div class="d-flex align-items-center gap-3">
                            <div class="coin-icon-small"
                                style="background: linear-gradient(135deg, {{ $info['color'] }} 0%, {{ $info['color'] }}dd 100%);">
                                <i class="{{ $info['icon'] }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">{{ $info['symbol'] }}</div>
                                <small class="text-muted">{{ $info['name'] }}</small>
                            </div>
                            @if ($coin === $sym)
                            <i class="bi bi-check-circle-fill" style="color: var(--gold-color); font-size: 16px;"></i>
                            @endif
                        </div>
                    </a>
                    @endforeach
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    {{-- ═══ CHART (TradingView) ═══ --}}
    <div class="futures-chart-outer">
        <iframe
            src="https://www.tradingview.com/widgetembed/?symbol={{ urlencode($coinInfo['tv']) }}&interval=1&theme=dark&style=1&locale=en&toolbar_bg=0d1117&enable_publishing=false&hidesidetoolbar=1&allow_symbol_change=0&show_popup_button=0&details=0&calendar=0&studies=%5B%5D&hide_top_toolbar=1"
            style="width:100%; height:220px; border:none; display:block;"
            frameborder="0" allowtransparency="true" scrolling="no">
        </iframe>
    </div>

    {{-- ═══ ACTIVE TRADE PANEL ═══ --}}
    @if ($openTrade)
    <div class="active-trade-panel" id="activeTrade">
        <div class="at-header">
            <span class="at-title">Trade Aktif</span>
            <span class="at-direction {{ $openTrade->direction }}">
                <i class="bi bi-arrow-{{ $openTrade->direction === 'call' ? 'up' : 'down' }}-circle-fill"></i>
                {{ strtoupper($openTrade->direction) }}
            </span>
        </div>
        <div class="at-timer" id="tradeTimer">00</div>
        <div class="at-timer-label">detik tersisa</div>
        <div class="at-stats">
            <div class="at-stat">
                <div class="at-stat-label">Entry</div>
                <div class="at-stat-val">${{ number_format($openTrade->entry_price, $openTrade->entry_price < 1 ? 6 : 2, '.', ',') }}</div>
            </div>
            <div class="at-stat">
                <div class="at-stat-label">Current</div>
                <div class="at-stat-val" id="atCurrentPrice">—</div>
            </div>
            <div class="at-stat">
                <div class="at-stat-label">Amount</div>
                <div class="at-stat-val">${{ number_format($openTrade->amount, 2) }}</div>
            </div>
            <div class="at-stat">
                <div class="at-stat-label">Potential</div>
                <div class="at-stat-val text-win">+${{ number_format($openTrade->amount * $openTrade->payout_rate / 100, 2) }}</div>
            </div>
        </div>
    </div>
    @else
    <div class="active-trade-panel" id="activeTrade" style="display:none;"></div>
    @endif

    {{-- ═══ TRADE PANEL ═══ --}}
    <div class="trade-panel" id="tradePanel" style="{{ $openTrade ? 'display:none' : '' }}">
        <div class="trade-panel-header">
            <span class="trade-panel-title">Open Trade</span>
            <span class="trade-balance">
                <i class="bi bi-wallet2"></i>
                Trade: <strong id="tradeBalance">${{ number_format(auth()->user()->trade_balance, 2) }}</strong>
            </span>
        </div>

        <div class="form-block">
            <div class="form-block-title">Jumlah (USDT)</div>
            <input type="number" id="tradeAmount" class="form-control-dark" placeholder="0.00" min="1" step="1">
            <div class="quick-btns mt-2">
                @foreach ([10, 50, 100, 500] as $q)
                <button class="quick-amount-btn" onclick="setAmount({{ $q }})">{{ $q }}</button>
                @endforeach
            </div>
        </div>

        <div class="payout-info">
            <div class="payout-row">
                <span>Payout (85%)</span>
                <span class="text-win" id="payoutAmount">—</span>
            </div>
            <div class="payout-row">
                <span>Total Return</span>
                <span id="totalReturn">—</span>
            </div>
        </div>

        <div class="trade-btns">
            <button class="btn-trade-call" id="btnCall" onclick="openTrade('call')">
                <i class="bi bi-arrow-up-circle-fill"></i>
                CALL <small>Naik</small>
            </button>
            <button class="btn-trade-put" id="btnPut" onclick="openTrade('put')">
                <i class="bi bi-arrow-down-circle-fill"></i>
                PUT <small>Turun</small>
            </button>
        </div>
    </div>

    {{-- ═══ EXPERT SIGNALS LINK ═══ --}}
    @php $coinSlug = strtolower(str_replace(['USDT','USD'], '', $coin)); @endphp
    <div class="px-4 pb-3">
        <a href="{{ route('member.invest.coin', ['coin' => $coinSlug]) }}" class="expert-signals-btn">
            <i class="bi bi-broadcast"></i>
            Expert Signals
            <span class="es-badge">Lama</span>
            <i class="bi bi-chevron-right ms-auto"></i>
        </a>
    </div>

    {{-- ═══ RECENT TRADES ═══ --}}
    <div class="section-label">Riwayat Trade</div>
    <div class="card-dark mx-4 mb-4">
        @forelse ($recentTrades as $t)
        <div class="txn-row">
            <div class="txn-icon {{ $t->result === 'win' ? 'completed' : 'rejected' }}">
                <i class="bi bi-arrow-{{ $t->direction === 'call' ? 'up' : 'down' }}-circle"></i>
            </div>
            <div class="txn-body">
                <div class="txn-title">
                    {{ isset($coins[$t->coin]) ? $coins[$t->coin]['symbol'] : $t->coin }}
                    · {{ strtoupper($t->direction) }}
                </div>
                <div class="txn-sub">${{ number_format($t->entry_price, $t->entry_price < 1 ? 6 : 2, '.', ',') }} → ${{ number_format($t->close_price, $t->close_price < 1 ? 6 : 2, '.', ',') }}</div>
            </div>
            <div class="txn-meta">
                <div class="txn-amount {{ $t->result === 'win' ? 'in' : 'out' }}">
                    {{ $t->result === 'win' ? '+' : '' }}${{ number_format($t->profit_loss, 2) }}
                </div>
                <div class="txn-date">{{ $t->closed_at->format('d M H:i') }}</div>
            </div>
        </div>
        @empty
        <div class="pg-empty" style="padding:32px 20px;">
            <i class="bi bi-clock-history"></i>
            <p>Belum ada trade</p>
        </div>
        @endforelse
    </div>

</div>

{{-- ═══ RESULT POPUP ═══ --}}
<div class="result-overlay" id="resultOverlay" style="display:none;">
    <div class="result-card">
        <div class="result-icon" id="resultIcon"></div>
        <div class="result-title" id="resultTitle"></div>
        <div class="result-amount" id="resultAmount"></div>
        <div class="result-detail" id="resultDetail"></div>
        <div class="result-balance">Balance baru: <strong id="resultBalance"></strong></div>
        <button class="btn-cta mt-3" onclick="dismissResult()">Lanjut Trading</button>
    </div>
</div>
@endsection

@push('styles')
<style>
/* ── Header ── */
.seamless-header-section {
    padding: 14px 20px; background: transparent;
    border-bottom: 1px solid var(--border-color);
}
.coin-icon-large {
    width: 46px; height: 46px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.coin-icon-large i { font-size: 20px; }
.price-main-header { font-size: 19px; font-weight: 800; color: #fff; letter-spacing: -0.5px; }
.live-dot { width: 6px; height: 6px; background: #22c55e; border-radius: 50%; display: inline-block; margin-right: 4px; animation: blink 1.5s infinite; }
.price-live-badge { display: inline-flex; align-items: center; font-size: 10px; font-weight: 700; color: #22c55e; background: rgba(34,197,94,0.12); border: 1px solid rgba(34,197,94,0.25); border-radius: 8px; padding: 2px 7px; }

/* ── Switch Coin Button ── */
.btn-switch-coin {
    background: linear-gradient(135deg, var(--gold-color) 0%, #00b8d4 100%);
    border: none; border-radius: 8px; width: 34px; height: 34px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: all 0.25s; box-shadow: 0 2px 8px rgba(0,229,255,0.3);
}
.btn-switch-coin:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,229,255,0.4); }
.btn-switch-coin i { font-size: 15px; color: white; }

/* ── Coin Popup ── */
.coin-popup-overlay {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.75); backdrop-filter: blur(8px);
    z-index: 9999; display: flex; align-items: flex-end; justify-content: center;
    animation: fadeIn 0.15s ease;
}
.coin-popup-modal {
    background: var(--card-light); border-radius: 20px 20px 0 0;
    width: 100%; max-width: 480px; max-height: 80vh; overflow: hidden;
    box-shadow: 0 -8px 40px rgba(0,0,0,0.5); animation: slideUp 0.25s ease;
    border: 1px solid var(--border-color); border-bottom: none;
}
.popup-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 20px; border-bottom: 1px solid var(--border-color);
}
.popup-header h6 { color: #fff; font-size: 15px; font-weight: 700; margin: 0; }
.btn-popup-close {
    background: transparent; border: none; width: 30px; height: 30px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; border-radius: 50%; transition: all 0.2s;
}
.btn-popup-close:hover { background: rgba(255,255,255,0.1); }
.btn-popup-close i { font-size: 20px; color: var(--text-muted); }
.popup-body { max-height: calc(80vh - 58px); overflow-y: auto; }
.popup-body::-webkit-scrollbar { width: 3px; }
.popup-body::-webkit-scrollbar-thumb { background: rgba(0,229,255,0.3); border-radius: 2px; }
.popup-category-header {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 20px 6px; font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.8px;
    color: var(--gold-color); background: rgba(0,229,255,0.03);
    border-bottom: 1px solid var(--border-color);
}
.popup-category-header i { font-size: 13px; }
.coin-popup-item {
    display: block; padding: 12px 20px;
    border-bottom: 1px solid var(--border-color);
    text-decoration: none; transition: background 0.15s;
}
.coin-popup-item:last-child { border-bottom: none; }
.coin-popup-item:hover { background: rgba(255,255,255,0.03); }
.coin-popup-item.active { background: rgba(0,229,255,0.07); border-left: 3px solid var(--gold-color); padding-left: 17px; }
.coin-popup-item .fw-bold { color: #fff !important; font-size: 13px; }
.coin-popup-item .text-muted { color: var(--text-muted) !important; font-size: 11px; }
.coin-icon-small {
    width: 38px; height: 38px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.coin-icon-small i { font-size: 17px; color: white; }

/* ── Chart ── */
.futures-chart-outer { border-bottom: 1px solid var(--border-color); background: #0d1117; }

/* ── Active Trade ── */
.active-trade-panel {
    margin: 14px; border-radius: 14px; overflow: hidden;
    background: linear-gradient(135deg, #0d1928 0%, #0a1420 100%);
    border: 1px solid rgba(0,229,255,0.2);
}
.at-header { display: flex; align-items: center; justify-content: space-between; padding: 14px 16px 0; }
.at-title { color: var(--text-muted); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
.at-direction { display: flex; align-items: center; gap: 5px; font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 6px; }
.at-direction.call { background: rgba(34,197,94,0.15); color: #22c55e; }
.at-direction.put  { background: rgba(239,68,68,0.15);  color: #ef4444; }
.at-timer { text-align: center; font-size: 60px; font-weight: 900; color: var(--gold-color); line-height: 1; padding: 10px 0 2px; font-variant-numeric: tabular-nums; }
.at-timer-label { text-align: center; color: var(--text-muted); font-size: 11px; margin-bottom: 12px; }
.at-stats { display: grid; grid-template-columns: 1fr 1fr; border-top: 1px solid var(--border-color); }
.at-stat { padding: 10px 14px; border-right: 1px solid var(--border-color); }
.at-stat:nth-child(2n) { border-right: none; }
.at-stat:nth-child(n+3) { border-top: 1px solid var(--border-color); }
.at-stat-label { color: var(--text-muted); font-size: 10px; margin-bottom: 2px; }
.at-stat-val { color: #fff; font-size: 13px; font-weight: 700; }
.text-win  { color: #22c55e !important; }
.text-lose { color: #ef4444 !important; }

/* ── Trade Panel ── */
.trade-panel { margin: 0 14px 14px; background: var(--card-light); border: 1px solid var(--border-color); border-radius: 14px; overflow: hidden; }
.trade-panel-header { display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border-bottom: 1px solid var(--border-color); }
.trade-panel-title { color: #fff; font-size: 13px; font-weight: 700; }
.trade-balance { color: var(--text-muted); font-size: 12px; display: flex; align-items: center; gap: 4px; }
.trade-balance strong { color: var(--gold-color); }
.quick-btns { display: grid; grid-template-columns: repeat(4, 1fr); gap: 6px; }
.payout-info { padding: 10px 18px; background: rgba(0,229,255,0.03); border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color); }
.payout-row { display: flex; justify-content: space-between; font-size: 12px; color: var(--text-muted); margin-bottom: 3px; }
.payout-row:last-child { margin-bottom: 0; }
.trade-btns { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; padding: 14px; }
.btn-trade-call, .btn-trade-put {
    display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 3px;
    padding: 14px 10px; border: none; border-radius: 10px;
    font-size: 17px; font-weight: 800; cursor: pointer; transition: all 0.2s;
}
.btn-trade-call small, .btn-trade-put small { font-size: 10px; font-weight: 600; opacity: 0.85; }
.btn-trade-call { background: linear-gradient(135deg, #22c55e, #16a34a); color: #fff; }
.btn-trade-call:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(34,197,94,0.4); }
.btn-trade-put  { background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff; }
.btn-trade-put:hover  { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(239,68,68,0.4); }
.btn-trade-call:disabled, .btn-trade-put:disabled { opacity: 0.5; cursor: not-allowed; transform: none; box-shadow: none; }

/* ── Expert Signals ── */
.expert-signals-btn {
    display: flex; align-items: center; gap: 10px; padding: 12px 16px;
    background: rgba(0,229,255,0.04); border: 1px solid rgba(0,229,255,0.18);
    border-radius: 10px; color: var(--gold-color); text-decoration: none;
    font-size: 13px; font-weight: 600; transition: all 0.2s;
}
.expert-signals-btn:hover { background: rgba(0,229,255,0.09); color: var(--gold-color); }
.es-badge { background: rgba(0,229,255,0.12); border: 1px solid rgba(0,229,255,0.25); color: var(--gold-color); font-size: 10px; padding: 1px 7px; border-radius: 6px; }

/* ── Result Overlay ── */
.result-overlay {
    position: fixed; inset: 0; z-index: 9999;
    background: rgba(0,0,0,0.8); backdrop-filter: blur(5px);
    display: flex; align-items: center; justify-content: center;
}
.result-card {
    width: 300px; background: var(--card-light); border-radius: 18px;
    padding: 28px 22px; text-align: center; border: 1px solid var(--border-color);
    animation: popIn 0.3s cubic-bezier(.34,1.56,.64,1);
}
.result-icon { font-size: 52px; margin-bottom: 10px; }
.result-title { font-size: 20px; font-weight: 800; margin-bottom: 6px; }
.result-amount { font-size: 30px; font-weight: 900; margin-bottom: 5px; }
.result-detail { color: var(--text-muted); font-size: 11px; margin-bottom: 5px; }
.result-balance { color: var(--text-muted); font-size: 12px; }
.result-balance strong { color: var(--gold-color); }

/* ── Animations ── */
@keyframes blink { 0%,100% { opacity:1; } 50% { opacity:0.3; } }
@keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
@keyframes slideUp { from { opacity:0; transform:translateY(40px); } to { opacity:1; transform:translateY(0); } }
@keyframes popIn { from { opacity:0; transform:scale(0.8); } to { opacity:1; transform:scale(1); } }
</style>
@endpush

@php
$activeTradeData = $openTrade ? [
    'id'        => $openTrade->id,
    'direction' => $openTrade->direction,
    'amount'    => (float) $openTrade->amount,
    'entry'     => (float) $openTrade->entry_price,
    'closesAt'  => $openTrade->opened_at->addSeconds(60)->toISOString(),
    'payout'    => (float) $openTrade->payout_rate,
] : null;
@endphp
@push('scripts')
<script>
const CSRF   = document.querySelector('meta[name="csrf-token"]').content;
const COIN   = '{{ $coin }}';
const PAYOUT = {{ \App\Http\Controllers\Member\FuturesController::PAYOUT_RATE }};
let priceInterval, timerInterval;
let activeTrade = @json($activeTradeData);

// ─── Popup ────────────────────────────────────────────────────
function openCoinPopup() {
    document.getElementById('coinPopupOverlay').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeCoinPopup() {
    document.getElementById('coinPopupOverlay').style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeCoinPopup(); });

// ─── Price polling ────────────────────────────────────────────
async function fetchPrice() {
    try {
        const res  = await fetch(`/member/futures/price/${COIN}`);
        const json = await res.json();
        if (!json.success || !json.price || json.price <= 0) return;

        const price = parseFloat(json.price);
        const el    = document.getElementById('currentPrice');
        if (el) el.textContent = '$' + formatPrice(price);

        const atEl = document.getElementById('atCurrentPrice');
        if (atEl && activeTrade) {
            atEl.textContent = '$' + formatPrice(price);
            const diff = price - activeTrade.entry;
            atEl.className = 'at-stat-val ' + (diff >= 0 ? 'text-win' : 'text-lose');
        }
    } catch(e) {}
}

function formatPrice(p) {
    if (p < 0.01) return p.toFixed(6);
    if (p < 1)    return p.toFixed(4);
    if (p < 100)  return p.toFixed(4);
    return p.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// ─── Amount input ─────────────────────────────────────────────
function setAmount(val) {
    document.getElementById('tradeAmount').value = val;
    updatePayout();
}
document.getElementById('tradeAmount')?.addEventListener('input', updatePayout);
function updatePayout() {
    const amt    = parseFloat(document.getElementById('tradeAmount')?.value) || 0;
    const profit = (amt * PAYOUT / 100).toFixed(2);
    const total  = (amt + parseFloat(profit)).toFixed(2);
    document.getElementById('payoutAmount').textContent = amt > 0 ? `+$${profit}` : '—';
    document.getElementById('totalReturn').textContent  = amt > 0 ? `$${total}` : '—';
}

// ─── Open trade ───────────────────────────────────────────────
async function openTrade(direction) {
    const amount = parseFloat(document.getElementById('tradeAmount')?.value);
    if (!amount || amount < 1) { alert('Masukkan jumlah minimal $1'); return; }
    setBtnsLoading(true);
    try {
        const res  = await fetch('/member/futures/open', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ coin: COIN, direction, amount }),
        });
        const json = await res.json();
        if (!json.success) { alert(json.message); setBtnsLoading(false); return; }

        activeTrade = {
            id: json.trade.id, direction: json.trade.direction,
            amount: json.trade.amount, entry: json.trade.entry_price,
            closesAt: json.trade.closes_at, payout: json.trade.payout_rate,
        };
        showActiveTrade(json.trade);
    } catch(e) {
        alert('Terjadi kesalahan. Coba lagi.');
        setBtnsLoading(false);
    }
}
function setBtnsLoading(state) {
    document.getElementById('btnCall').disabled = state;
    document.getElementById('btnPut').disabled  = state;
}
function showActiveTrade(trade) {
    document.getElementById('tradePanel').style.display = 'none';
    const panel = document.getElementById('activeTrade');
    if (!panel) { window.location.reload(); return; }
    panel.innerHTML = `
        <div class="at-header">
            <span class="at-title">Trade Aktif</span>
            <span class="at-direction ${trade.direction}">
                <i class="bi bi-arrow-${trade.direction === 'call' ? 'up' : 'down'}-circle-fill"></i>
                ${trade.direction.toUpperCase()}
            </span>
        </div>
        <div class="at-timer" id="tradeTimer">60</div>
        <div class="at-timer-label">detik tersisa</div>
        <div class="at-stats">
            <div class="at-stat"><div class="at-stat-label">Entry</div><div class="at-stat-val">$${formatPrice(trade.entry_price)}</div></div>
            <div class="at-stat"><div class="at-stat-label">Current</div><div class="at-stat-val" id="atCurrentPrice">—</div></div>
            <div class="at-stat"><div class="at-stat-label">Amount</div><div class="at-stat-val">$${parseFloat(trade.amount).toFixed(2)}</div></div>
            <div class="at-stat"><div class="at-stat-label">Potential</div><div class="at-stat-val text-win">+$${(trade.amount * trade.payout_rate / 100).toFixed(2)}</div></div>
        </div>`;
    panel.style.display = '';
    startTimer();
}

// ─── Timer ────────────────────────────────────────────────────
function startTimer() {
    if (!activeTrade) return;
    const closesAt = new Date(activeTrade.closesAt).getTime();
    clearInterval(timerInterval);
    timerInterval = setInterval(async () => {
        const remaining = Math.max(0, Math.ceil((closesAt - Date.now()) / 1000));
        const el = document.getElementById('tradeTimer');
        if (el) el.textContent = String(remaining).padStart(2, '0');
        if (remaining <= 0) { clearInterval(timerInterval); await closeTrade(); }
    }, 250);
}

// ─── Close trade ──────────────────────────────────────────────
async function closeTrade() {
    if (!activeTrade) return;
    try {
        const res  = await fetch('/member/futures/close', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ trade_id: activeTrade.id }),
        });
        const json = await res.json();
        if (!json.success) {
            if (json.remaining > 0) setTimeout(closeTrade, (json.remaining * 1000) + 500);
            return;
        }
        showResult(json.result);
        activeTrade = null;
    } catch(e) {
        setTimeout(closeTrade, 2000);
    }
}

// ─── Result popup ─────────────────────────────────────────────
function showResult(result) {
    const isWin = result.outcome === 'win';
    document.getElementById('resultIcon').textContent    = isWin ? '🎉' : '😔';
    document.getElementById('resultTitle').textContent   = isWin ? 'MENANG!' : 'KALAH';
    document.getElementById('resultTitle').className     = 'result-title ' + (isWin ? 'text-win' : 'text-lose');
    document.getElementById('resultAmount').textContent  = (isWin ? '+' : '') + '$' + Math.abs(result.profit_loss).toFixed(2);
    document.getElementById('resultAmount').className    = 'result-amount ' + (isWin ? 'text-win' : 'text-lose');
    document.getElementById('resultDetail').textContent  = `Entry $${formatPrice(result.entry_price)} → Close $${formatPrice(result.close_price)}`;
    document.getElementById('resultBalance').textContent = '$' + parseFloat(result.new_balance).toFixed(2);
    document.getElementById('tradeBalance').textContent  = '$' + parseFloat(result.new_balance).toFixed(2);
    document.getElementById('resultOverlay').style.display = 'flex';
}
function dismissResult() {
    document.getElementById('resultOverlay').style.display = 'none';
    window.location.reload();
}

// ─── Boot ─────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    fetchPrice();
    priceInterval = setInterval(fetchPrice, 5000);
    if (activeTrade) startTimer();
});
document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        clearInterval(priceInterval);
    } else {
        fetchPrice();
        priceInterval = setInterval(fetchPrice, 5000);
    }
});
</script>
@endpush
