@extends('member.layouts.app')
@section('content')
<div class="scrollable-content">

    <!-- Header -->
    <div class="dp-header">
        <a href="{{ route('member.profile.index') }}" class="dp-back-btn">
            <i class="bi bi-chevron-left"></i>
        </a>
        <h5 class="dp-header-title">Recharge</h5>
        <a href="{{ route('member.deposit.history') }}" class="dp-hist-btn">
            <i class="bi bi-clock-history"></i>
        </a>
    </div>

    {{-- ══════════════════════════════════════════
         STEP 1: FORM — no active deposit
    ══════════════════════════════════════════ --}}
    @if (!$pendingDeposit)

    <div class="dp-body">

        <!-- Select Currency -->
        <div class="dp-field-group">
            <div class="dp-field-label">Select Currency</div>
            <div class="dp-select-box">
                <div class="dp-select-left">
                    <div class="dp-currency-dot" style="background:#26a17b;"></div>
                    <span class="dp-select-val">USDT</span>
                </div>
                <span class="dp-select-placeholder">Select Curr...</span>
                <i class="bi bi-chevron-right dp-select-chev"></i>
            </div>
        </div>

        <!-- Deposit Channel -->
        <div class="dp-field-group">
            <div class="dp-field-label">Deposit Channel</div>
            <div class="dp-net-row">
                <div class="dp-net-option active" id="opt-trc20" onclick="selectWalletType('trc20')">
                    <span class="dp-net-name">TRC20</span>
                    <span class="dp-net-sub">TRON Network</span>
                </div>
                <div class="dp-net-option" id="opt-bep20" onclick="selectWalletType('bep20')">
                    <span class="dp-net-name">BEP20</span>
                    <span class="dp-net-sub">Binance Smart Chain</span>
                </div>
            </div>
        </div>

        <!-- Recharge Amount -->
        <div class="dp-field-group">
            <div class="dp-field-label">Recharge amount</div>
            <div class="dp-amount-input-wrap">
                <input type="number" id="deposit-amount" class="dp-amount-input"
                    placeholder="Enter amount" step="0.01" min="200">
            </div>
            <!-- Quick chips -->
            <div class="dp-chips">
                <button type="button" class="dp-chip" onclick="setAmount(100)">100</button>
                <button type="button" class="dp-chip" onclick="setAmount(200)">200</button>
                <button type="button" class="dp-chip" onclick="setAmount(300)">300</button>
                <button type="button" class="dp-chip" onclick="setAmount(400)">400</button>
                <button type="button" class="dp-chip" onclick="setAmount(500)">500</button>
                <button type="button" class="dp-chip" onclick="setAmount(600)">600</button>
                <button type="button" class="dp-chip" onclick="setAmount(700)">700</button>
                <button type="button" class="dp-chip" onclick="setAmount(800)">800</button>
                <button type="button" class="dp-chip" onclick="setAmount(900)">900</button>
                <button type="button" class="dp-chip" onclick="setAmount(1000)">1000</button>
            </div>
        </div>

        <!-- Confirm Button -->
        <button type="button" class="dp-confirm-btn" onclick="confirmDeposit()">
            Confirm
        </button>

        <!-- Warm Reminder -->
        <div class="dp-reminder">
            <div class="dp-reminder-title">Warm reminder</div>
            <div class="dp-reminder-body">
                <p>Dear User,</p>
                <p>
                    To ensure fund security and the stability of recharge channels,
                    recharge addresses will be automatically updated periodically.
                    Each generated recharge channel is valid for only <strong>1 hour</strong>.
                    Please be sure to complete the recharge operation within the validity period.
                </p>
                <p>
                    Please note: Do not recharge to previous recharge addresses or
                    repeatedly to the same address to avoid funds not being credited
                    or being lost. If you have any questions, please contact online
                    customer service or the platform administrator promptly.
                </p>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════
         STEP 2: WAITING FOR PAYMENT
    ══════════════════════════════════════════ --}}
    @else

    @php
        $expiredAt = $pendingDeposit->expired_at;
        $now       = now();
        if (!$expiredAt) {
            $expiredAt = $pendingDeposit->created_at->copy()->addHour();
        }
        $isExpired   = $now->greaterThan($expiredAt);
        $secondsLeft = $isExpired ? 0 : (int) ($expiredAt->timestamp - $now->timestamp);
    @endphp

    @if ($isExpired)

    <!-- Expired State -->
    <div class="dp-body">
        <div class="dp-expired-box">
            <div class="dp-expired-icon"><i class="bi bi-clock-history"></i></div>
            <div class="dp-expired-title">Payment Expired</div>
            <div class="dp-expired-sub">
                Your deposit session has expired. Please create a new recharge request.
            </div>
            <a href="{{ route('member.deposit.index') }}" class="dp-confirm-btn" style="display:block;text-align:center;text-decoration:none;margin-top:24px;">
                New Recharge
            </a>
        </div>
    </div>

    @else

    <!-- Waiting for Payment -->
    <div class="dp-waiting-banner">
        <div class="dp-waiting-row">
            <i class="bi bi-info-circle me-2"></i>
            <div>
                <div class="dp-waiting-title">Waiting for payment</div>
                <div class="dp-waiting-timer">Please complete payment within <span id="countdown-display" class="dp-timer-val">--:--:--</span></div>
            </div>
        </div>
    </div>

    <div class="dp-body">

        <!-- Currency & Amount Info -->
        <div class="dp-info-card">
            <div class="dp-info-card-row">
                <div class="dp-info-card-left">
                    <div class="dp-currency-dot" style="background:#26a17b;width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                        <span style="color:#fff;font-size:10px;font-weight:700;">T</span>
                    </div>
                    <div>
                        <div class="dp-ic-label">USDT <span class="dp-ic-network">({{ $pendingDeposit->payment_method }})</span></div>
                    </div>
                </div>
                <div class="dp-ic-amount">{{ number_format($pendingDeposit->amount, 0) }} <span class="dp-ic-unit">USDT</span></div>
            </div>
            <div class="dp-info-card-divider"></div>
            <div class="dp-info-card-row">
                <div class="dp-info-card-left">
                    <div class="dp-order-icon"><i class="bi bi-layers-fill"></i></div>
                    <div class="dp-ic-label">Order amount</div>
                </div>
                <div class="dp-ic-amount">{{ number_format($pendingDeposit->amount, 0) }} <span class="dp-ic-unit">USDT</span></div>
            </div>
        </div>

        <!-- Transfer instruction -->
        <div class="dp-transfer-note">Please transfer USDT to this address</div>

        <!-- Payment Details -->
        <div class="dp-detail-card">
            <div class="dp-detail-row">
                <div class="dp-detail-label">Payment amount</div>
                <div class="dp-detail-val-row">
                    <span class="dp-detail-val">{{ number_format($pendingDeposit->amount, 0) }}</span>
                    <button class="dp-copy-btn" onclick="copyRaw('{{ $pendingDeposit->amount }}', 'copy-amt-icon')" title="Copy">
                        <i class="bi bi-copy" id="copy-amt-icon"></i>
                    </button>
                </div>
            </div>
            <div class="dp-detail-divider"></div>
            <div class="dp-detail-row">
                <div class="dp-detail-label">Wallet address</div>
                <div class="dp-detail-val-row">
                    <span class="dp-detail-val dp-mono">{{ $pendingDeposit->wallet_address }}</span>
                    <button class="dp-copy-btn" onclick="copyRaw('{{ $pendingDeposit->wallet_address }}', 'copy-addr-icon')" title="Copy">
                        <i class="bi bi-copy" id="copy-addr-icon"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Reference -->
        <div class="dp-ref-row">
            <span class="dp-ref-label">Reference:</span>
            <span class="dp-ref-val">{{ $pendingDeposit->reference }}</span>
        </div>

        <!-- Warm Reminder -->
        <div class="dp-reminder">
            <div class="dp-reminder-title">Warm reminder</div>
            <div class="dp-reminder-body">
                <p>
                    After completing the transfer, please send proof of payment to the
                    admin via <strong>Telegram</strong>. Your balance will be updated
                    once the admin verifies the transaction.
                </p>
                <p>
                    Please note: Ensure you transfer the <strong>exact amount</strong> shown above
                    and use the correct network (<strong>{{ $pendingDeposit->payment_method }}</strong>).
                    Transfers to the wrong network may result in permanent loss of funds.
                </p>
            </div>
        </div>

        <!-- History link -->
        <a href="{{ route('member.deposit.history') }}" class="dp-hist-link">
            <i class="bi bi-clock-history me-2"></i>View Deposit History
        </a>

    </div>

    <script>
    (function() {
        var secondsLeft = {{ $secondsLeft }};
        var display     = document.getElementById('countdown-display');
        var hasReloaded = false;

        if (secondsLeft <= 0) {
            display.textContent = '00:00:00';
            display.classList.add('urgent');
            setTimeout(function() {
                if (!hasReloaded) { hasReloaded = true; window.location.href = '{{ route('member.deposit.index') }}'; }
            }, 2000);
            return;
        }

        function tick() {
            if (secondsLeft <= 0) {
                display.textContent = '00:00:00';
                display.classList.add('urgent');
                if (!hasReloaded) {
                    hasReloaded = true;
                    setTimeout(function() { window.location.href = '{{ route('member.deposit.index') }}'; }, 1500);
                }
                return;
            }
            var h = Math.floor(secondsLeft / 3600);
            var m = Math.floor((secondsLeft % 3600) / 60);
            var s = secondsLeft % 60;
            display.textContent = String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
            if (secondsLeft <= 300) display.classList.add('urgent');
            secondsLeft--;
            setTimeout(tick, 1000);
        }
        tick();
    })();
    </script>

    @endif
    @endif

</div>

@push('styles')
<style>
/* ── Header ── */
.dp-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid var(--border-color);
    background: var(--card-dark, #0e1929);
}
.dp-back-btn {
    width: 36px; height: 36px; border-radius: 50%;
    background: rgba(255,255,255,0.06); border: 1px solid var(--border-color);
    display: flex; align-items: center; justify-content: center;
    color: #fff; text-decoration: none; font-size: 16px;
}
.dp-header-title { color: #fff; font-size: 17px; font-weight: 700; margin: 0; }
.dp-hist-btn { color: var(--text-muted); font-size: 19px; text-decoration: none; }
.dp-hist-btn:hover { color: var(--gold-color); }

/* ── Body ── */
.dp-body { padding: 16px 16px 32px; }

/* ── Field groups ── */
.dp-field-group { margin-bottom: 18px; }
.dp-field-label { color: var(--text-muted, #8a9bb0); font-size: 13px; font-weight: 500; margin-bottom: 8px; }

/* ── Select box (currency) ── */
.dp-select-box {
    display: flex; align-items: center; gap: 10px;
    background: var(--input-bg, rgba(255,255,255,0.05));
    border: 1px solid var(--border-color);
    border-radius: 8px; padding: 12px 14px;
}
.dp-select-left { display: flex; align-items: center; gap: 8px; flex: 1; }
.dp-currency-dot { width: 22px; height: 22px; border-radius: 50%; flex-shrink: 0; }
.dp-select-val { color: #fff; font-size: 14px; font-weight: 600; }
.dp-select-placeholder { color: var(--text-muted); font-size: 13px; }
.dp-select-chev { color: var(--text-muted); font-size: 13px; }

/* ── Network tabs ── */
.dp-net-row { display: flex; gap: 8px; }
.dp-net-option {
    flex: 1; padding: 11px 10px;
    background: rgba(255,255,255,0.04); border: 1px solid var(--border-color);
    border-radius: 8px; cursor: pointer; transition: all 0.2s; text-align: center;
}
.dp-net-option.active { background: rgba(0,229,255,0.08); border-color: var(--gold-color); }
.dp-net-name { display: block; color: var(--text-muted); font-size: 13px; font-weight: 700; }
.dp-net-option.active .dp-net-name { color: var(--gold-color); }
.dp-net-sub { display: block; color: var(--text-muted); font-size: 10px; margin-top: 2px; }

/* ── Amount input ── */
.dp-amount-input-wrap {
    border: 1px solid var(--border-color); border-radius: 8px;
    background: rgba(255,255,255,0.03); margin-bottom: 10px;
}
.dp-amount-input {
    width: 100%; background: transparent; border: none; outline: none;
    color: #fff; font-size: 18px; font-weight: 700; padding: 13px 14px;
}
.dp-amount-input::placeholder { color: var(--text-muted); font-size: 14px; font-weight: 400; }
.dp-amount-input::-webkit-outer-spin-button,
.dp-amount-input::-webkit-inner-spin-button { -webkit-appearance: none; }

/* ── Quick chips ── */
.dp-chips {
    display: grid; grid-template-columns: repeat(5, 1fr); gap: 6px;
}
.dp-chip {
    padding: 8px 4px; text-align: center;
    background: rgba(255,255,255,0.05); border: 1px solid var(--border-color);
    border-radius: 6px; color: var(--text-muted); font-size: 12px; font-weight: 600;
    cursor: pointer; transition: all 0.2s;
}
.dp-chip:hover, .dp-chip.active {
    background: rgba(0,229,255,0.08); border-color: var(--gold-color); color: var(--gold-color);
}

/* ── Confirm button ── */
.dp-confirm-btn {
    width: 100%; padding: 15px;
    background: linear-gradient(135deg, #00c6a2, #00a87e);
    border: none; border-radius: 8px;
    color: #fff; font-size: 16px; font-weight: 700;
    cursor: pointer; transition: all 0.3s; margin: 18px 0;
    display: block;
}
.dp-confirm-btn:hover { opacity: 0.9; transform: translateY(-1px); }

/* ── Warm reminder ── */
.dp-reminder {
    background: rgba(255,255,255,0.03);
    border: 1px solid var(--border-color);
    border-radius: 10px; padding: 16px;
    margin-top: 4px;
}
.dp-reminder-title {
    color: #fff; font-size: 14px; font-weight: 700; margin-bottom: 10px;
}
.dp-reminder-body { color: var(--text-muted); font-size: 12px; line-height: 1.7; }
.dp-reminder-body p { margin: 0 0 8px; }
.dp-reminder-body p:last-child { margin-bottom: 0; }
.dp-reminder-body strong { color: #fff; }

/* ── Waiting banner ── */
.dp-waiting-banner {
    background: #f0f4f8; padding: 12px 16px;
    border-bottom: 1px solid #dde3ea;
}
.dp-waiting-row { display: flex; align-items: flex-start; gap: 6px; color: #555; font-size: 13px; }
.dp-waiting-title { font-weight: 600; color: #333; font-size: 13px; }
.dp-waiting-timer { font-size: 12px; color: #666; margin-top: 2px; }
.dp-timer-val { font-weight: 700; color: #333; font-family: monospace; }
.dp-timer-val.urgent { color: #ef4444; animation: dp-blink 1s infinite; }
@keyframes dp-blink { 0%,100%{opacity:1} 50%{opacity:0.5} }

/* ── Info card (USDT / Order amount) ── */
.dp-info-card {
    background: #fff; border-radius: 10px;
    border: 1px solid #eee; margin-bottom: 6px;
    overflow: hidden;
}
.dp-info-card-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 16px; gap: 12px;
}
.dp-info-card-left { display: flex; align-items: center; gap: 10px; }
.dp-order-icon {
    width: 28px; height: 28px; border-radius: 50%;
    background: #f0f0f0; display: flex; align-items: center; justify-content: center;
    color: #666; font-size: 13px;
}
.dp-ic-label { color: #333; font-size: 13px; font-weight: 600; }
.dp-ic-network { color: #888; font-size: 11px; font-weight: 400; }
.dp-ic-amount { color: #111; font-size: 15px; font-weight: 700; }
.dp-ic-unit { color: #888; font-size: 11px; font-weight: 500; }
.dp-info-card-divider { height: 1px; background: #f0f0f0; margin: 0 16px; }

/* ── Transfer note ── */
.dp-transfer-note {
    text-align: center; color: #888; font-size: 12px;
    padding: 10px 0 14px; letter-spacing: 0.2px;
}

/* ── Detail card (amount + address) ── */
.dp-detail-card {
    background: #fff; border-radius: 10px;
    border: 1px solid #eee; padding: 0 16px;
    margin-bottom: 12px;
}
.dp-detail-row { padding: 14px 0; }
.dp-detail-label { color: #999; font-size: 11px; margin-bottom: 6px; }
.dp-detail-val-row { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
.dp-detail-val { color: #111; font-size: 15px; font-weight: 600; word-break: break-all; flex: 1; }
.dp-mono { font-family: monospace; font-size: 13px; }
.dp-copy-btn {
    background: #f5f5f5; border: 1px solid #ddd;
    border-radius: 6px; width: 32px; height: 32px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; flex-shrink: 0; transition: all 0.2s; color: #666;
}
.dp-copy-btn:hover { background: #eee; }
.dp-detail-divider { height: 1px; background: #f0f0f0; }

/* ── Reference row ── */
.dp-ref-row {
    display: flex; align-items: center; gap: 8px;
    padding: 8px 4px; margin-bottom: 8px;
}
.dp-ref-label { color: var(--text-muted); font-size: 12px; }
.dp-ref-val { color: #fff; font-size: 12px; font-weight: 600; font-family: monospace; }

/* ── History link ── */
.dp-hist-link {
    display: flex; align-items: center; justify-content: center;
    padding: 13px; margin-top: 16px;
    background: rgba(59,130,246,0.1); border: 1px solid rgba(59,130,246,0.3);
    border-radius: 8px; color: #60a5fa; font-size: 14px; font-weight: 600;
    text-decoration: none; transition: all 0.2s;
}
.dp-hist-link:hover { background: rgba(59,130,246,0.2); color: #60a5fa; }

/* ── Expired box ── */
.dp-expired-box {
    background: rgba(239,68,68,0.06); border: 1px solid rgba(239,68,68,0.2);
    border-radius: 14px; padding: 36px 24px; text-align: center; margin-top: 8px;
}
.dp-expired-icon {
    width: 60px; height: 60px; border-radius: 50%;
    background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25);
    display: flex; align-items: center; justify-content: center;
    font-size: 26px; color: #ef4444; margin: 0 auto 16px;
}
.dp-expired-title { color: #ef4444; font-size: 18px; font-weight: 700; margin-bottom: 8px; }
.dp-expired-sub { color: var(--text-muted); font-size: 13px; line-height: 1.6; }
</style>
@endpush

@push('scripts')
<script>
function copyRaw(text, iconId) {
    navigator.clipboard.writeText(text).then(function () {
        var icon = document.getElementById(iconId);
        if (icon) {
            icon.className = 'bi bi-check-lg';
            setTimeout(function () { icon.className = 'bi bi-copy'; }, 2000);
        }
    });
}

@if (!$pendingDeposit)
const walletData = {
    trc20: { name: 'TRC20 (TRON)', address: '{{ $walletTrc20['address'] }}' },
    bep20: { name: 'BEP20 (BSC)',  address: '{{ $walletBep20['address'] }}' }
};

let selectedWalletType = 'trc20';

function selectWalletType(type) {
    selectedWalletType = type;
    document.querySelectorAll('.dp-net-option').forEach(el => el.classList.remove('active'));
    document.getElementById('opt-' + type).classList.add('active');
}

function setAmount(val) {
    document.getElementById('deposit-amount').value = val;
    document.querySelectorAll('.dp-chip').forEach(c => {
        c.classList.toggle('active', parseInt(c.textContent) === val);
    });
}

function confirmDeposit() {
    var amount = parseFloat(document.getElementById('deposit-amount').value);
    if (!amount || amount <= 0) { alert('Please enter a valid amount.'); return; }
    if (amount < 200) { alert('Minimum deposit is 200 USDT.'); return; }

    var network = selectedWalletType.toUpperCase();
    var msg = 'Deposit Confirmation\n\n'
            + 'Amount  : ' + amount.toFixed(2) + ' USDT\n'
            + 'Network : ' + network + '\n\n'
            + 'After confirmation, the wallet address will be shown and a 1-hour timer will start.\n'
            + 'Do you want to proceed?';

    if (confirm(msg)) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('member.deposit.store') }}';

        [
            { name: '_token',         value: '{{ csrf_token() }}' },
            { name: 'amount',         value: amount },
            { name: 'wallet_type',    value: selectedWalletType },
            { name: 'wallet_address', value: walletData[selectedWalletType].address },
        ].forEach(function(f) {
            var input = document.createElement('input');
            input.type = 'hidden'; input.name = f.name; input.value = f.value;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    }
}

document.addEventListener('DOMContentLoaded', function () { selectWalletType('trc20'); });
@endif

@if (session('success')) alert('{{ session('success') }}'); @endif
@if (session('error'))   alert('{{ session('error') }}'); @endif
@if ($errors->any())     alert('{{ $errors->first() }}'); @endif
</script>
@endpush
@endsection