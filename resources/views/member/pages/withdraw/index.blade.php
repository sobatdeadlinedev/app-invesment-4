@extends('member.layouts.app')
@section('content')
<div class="scrollable-content">

    <!-- Header -->
    <div class="wd-header">
        <a href="{{ route('member.profile.index') }}" class="wd-back-btn">
            <i class="bi bi-chevron-left"></i>
        </a>
        <div class="wd-header-center">
            <h5 class="wd-title">{{ __('app.withdrawal_usdt') }}</h5>
            <p class="wd-subtitle">{{ __('app.withdrawal_subtitle') }}</p>
        </div>
        <a href="{{ route('member.withdraw.history') }}" class="wd-hist-btn">
            <i class="bi bi-clock-history"></i>
        </a>
    </div>

    <form id="withdraw-form" action="{{ route('member.withdraw.store') }}" method="POST">
        @csrf
        <input type="hidden" name="wallet_id" id="selected-wallet-id">

        <!-- Currency -->
        <div class="wd-section">
            <div class="wd-currency-row">
                <div class="d-flex align-items-center gap-3">
                    <div class="wd-usdt-icon">₮</div>
                    <span class="wd-currency-name">USDT</span>
                </div>
                <div class="wd-select-currency">
                    <span>{{ __('app.select_currency') }}</span>
                    <i class="bi bi-chevron-down"></i>
                </div>
            </div>
        </div>

        <!-- Blockchain Network selector -->
        <div class="wd-section">
            <div class="wd-label">{{ __('app.blockchain_network') }}</div>
            <div class="wd-net-row">
                <button type="button" class="wd-net-btn active" data-net="trc20" onclick="selectNetwork('trc20', this)">TRC20</button>
                <button type="button" class="wd-net-btn" data-net="bep20" onclick="selectNetwork('bep20', this)">BEP20</button>
            </div>
        </div>

        <!-- Wallet Address -->
        <div class="wd-section">
            <div class="wd-label">{{ __('app.blockchain_network') }}</div>
            <div class="wd-addr-row">
                <span id="wd-addr" class="wd-addr-text no-addr">{{ __('app.no_withdrawal_address') }}</span>
                <a href="{{ route('member.wallet.index') }}" class="wd-bind-btn">
                    {{ __('app.bind') }} <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </div>

        <!-- Quantity -->
        <div class="wd-section">
            <div class="wd-label">{{ __('app.quantity') }}</div>
            <div class="wd-qty-box">
                <input type="number" id="withdraw-amount" name="amount"
                    class="wd-qty-input"
                    step="0.01" min="50" disabled>
                <div class="wd-qty-right">
                    <span class="wd-usdt-lbl">USDT</span>
                    <button type="button" class="wd-all-btn" onclick="fillAll()">{{ __('app.all') }}</button>
                </div>
            </div>
            <div class="wd-avail">
                {{ __('app.available') }}: <span id="wd-avail-val">{{ number_format($userBalance, 2) }}</span> USDT
            </div>
        </div>

        <!-- Receivable Amount -->
        <div class="wd-section wd-recv-section">
            <div class="wd-recv-lbl">{{ __('app.receivable_amount') }}</div>
            <div class="wd-recv-val" id="wd-recv-val">0 USDT</div>
            <div class="wd-recv-fee">{{ __('app.withdrawal_fee') }} <span id="wd-fee-val">0</span> USDT</div>
        </div>

        <!-- Withdrawal Instructions -->
        <div class="wd-section wd-instr-section">
            <div class="wd-instr-title">{{ __('app.withdrawal_instructions') }}</div>
            <ul class="wd-instr-list">
                <li>{{ __('app.wd_instr_1') }}</li>
                <li>{{ __('app.wd_instr_2') }}</li>
                <li>{{ __('app.wd_instr_3') }}</li>
                <li>{{ __('app.wd_instr_4') }}</li>
            </ul>
        </div>

        <!-- Submit -->
        <div class="wd-footer">
            <button type="button" class="wd-submit-btn" id="wd-submit-btn" disabled onclick="submitWithdraw()">
                {{ __('app.withdrawal_btn') }}
            </button>
        </div>

    </form>
</div>

@push('styles')
<style>
/* Header */
.wd-header {
    display: flex; align-items: center;
    padding: 16px 20px;
    border-bottom: 1px solid var(--border-color);
}
.wd-back-btn {
    width: 36px; height: 36px;
    background: rgba(255,255,255,0.06); border: 1px solid var(--border-color);
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    color: var(--text-primary); text-decoration: none; font-size: 16px; flex-shrink: 0;
}
.wd-header-center { flex: 1; text-align: center; padding: 0 10px; }
.wd-title { color: #fff; font-size: 16px; font-weight: 700; margin: 0; }
.wd-subtitle { color: var(--text-muted); font-size: 11px; margin: 3px 0 0; }
.wd-hist-btn { color: var(--text-muted); font-size: 19px; flex-shrink: 0; text-decoration: none; }
.wd-hist-btn:hover { color: var(--gold-color); }

/* Sections */
.wd-section { padding: 16px 20px; border-bottom: 1px solid var(--border-color); }
.wd-label { color: var(--gold-color); font-size: 12px; font-weight: 700; margin-bottom: 12px; }

/* Currency */
.wd-currency-row { display: flex; align-items: center; justify-content: space-between; }
.wd-usdt-icon {
    width: 42px; height: 42px; border-radius: 50%;
    background: #26a17b; display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 22px; font-weight: 900; flex-shrink: 0;
}
.wd-currency-name { color: #fff; font-size: 17px; font-weight: 700; }
.wd-select-currency {
    display: flex; align-items: center; gap: 5px;
    color: var(--text-muted); font-size: 13px;
}

/* Network buttons */
.wd-net-row { display: flex; gap: 10px; }
.wd-net-btn {
    flex: 1; padding: 11px 10px;
    background: rgba(255,255,255,0.04);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    color: var(--text-muted); font-size: 14px; font-weight: 700;
    cursor: pointer; transition: all 0.2s;
}
.wd-net-btn.active {
    background: rgba(0,229,255,0.08);
    border-color: var(--gold-color);
    color: var(--gold-color);
}

/* Address */
.wd-addr-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
.wd-addr-text { font-size: 13px; word-break: break-all; flex: 1; line-height: 1.5; }
.wd-addr-text.no-addr { color: #ef4444; }
.wd-addr-text.has-addr { color: #fff; font-family: monospace; }
.wd-bind-btn { color: #22c55e; font-size: 13px; font-weight: 600; text-decoration: none; white-space: nowrap; flex-shrink: 0; }
.wd-bind-btn:hover { color: #16a34a; }

/* Quantity */
.wd-qty-box {
    display: flex; align-items: center;
    border: 1px solid var(--border-color); border-radius: 8px;
    background: rgba(255,255,255,0.03);
}
.wd-qty-input {
    flex: 1; background: transparent; border: none; outline: none;
    color: #fff; font-size: 15px; padding: 13px 14px; min-width: 0;
}
.wd-qty-input::placeholder { color: var(--text-muted); font-size: 13px; }
.wd-qty-input::-webkit-outer-spin-button,
.wd-qty-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
.wd-qty-input:disabled { opacity: 0.6; cursor: not-allowed; }
.wd-qty-right { display: flex; align-items: center; gap: 8px; padding: 0 12px; flex-shrink: 0; }
.wd-usdt-lbl { color: var(--text-muted); font-size: 13px; font-weight: 600; }
.wd-all-btn { background: none; border: none; color: var(--gold-color); font-size: 13px; font-weight: 700; cursor: pointer; padding: 0; }
.wd-avail { color: var(--text-muted); font-size: 12px; margin-top: 8px; }

/* Receivable */
.wd-recv-section { background: rgba(255,255,255,0.01); }
.wd-recv-lbl { color: var(--text-muted); font-size: 12px; margin-bottom: 6px; }
.wd-recv-val { color: #fff; font-size: 26px; font-weight: 900; margin-bottom: 4px; }
.wd-recv-fee { color: var(--text-muted); font-size: 12px; }

/* Instructions */
.wd-instr-section { background: rgba(0,229,255,0.02); }
.wd-instr-title { color: var(--gold-color); font-size: 13px; font-weight: 700; margin-bottom: 10px; }
.wd-instr-list { color: var(--text-muted); font-size: 12px; padding-left: 16px; margin: 0; }
.wd-instr-list li { margin-bottom: 6px; line-height: 1.5; }

/* Submit */
.wd-footer { padding: 20px 20px 24px; }
.wd-submit-btn {
    width: 100%; padding: 15px;
    background: linear-gradient(135deg, #22c55e, #16a34a);
    border: none; border-radius: 25px;
    color: #fff; font-size: 16px; font-weight: 700;
    cursor: pointer; transition: all 0.3s;
}
.wd-submit-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(34,197,94,0.35); }
.wd-submit-btn:disabled { opacity: 0.45; cursor: not-allowed; transform: none; box-shadow: none; }
</style>
@endpush

@push('scripts')
<script>
const userBalance = {{ $userBalance }};

@php
$trc20Wallet = $wallets->where('type', 'trc20')->first();
$bep20Wallet = $wallets->where('type', 'bep20')->first();
@endphp

const walletsByNetwork = {
    trc20: {!! $trc20Wallet ? json_encode(['id' => $trc20Wallet->id, 'address' => $trc20Wallet->account_number]) : 'null' !!},
    bep20: {!! $bep20Wallet ? json_encode(['id' => $bep20Wallet->id, 'address' => $bep20Wallet->account_number]) : 'null' !!},
};

const FWTrans = {
    noAddress:           "{{ __('app.no_withdrawal_address') }}",
    enterValidAmount:    "{{ __('app.enter_valid_amount') }}",
    minimumWithdrawal:   "{{ __('app.minimum_withdrawal_50') }}",
    insufficientBalance: "{{ __('app.insufficient_balance_withdraw') }}",
    amountTooSmall:      "{{ __('app.amount_too_small') }}",
    confirmWithdrawal:   "{{ __('app.confirm_withdrawal') }}"
};

@if(session('success')) alert('{{ session('success') }}'); @endif
@if(session('error')) alert('{{ session('error') }}'); @endif
@if($errors->any()) alert('{{ $errors->first() }}'); @endif

let currentNetwork = 'trc20';

function selectNetwork(net, btn) {
    currentNetwork = net;
    document.querySelectorAll('.wd-net-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    updateAddressDisplay();
    calculateFee();
}

function updateAddressDisplay() {
    const wallet = walletsByNetwork[currentNetwork];
    const addrEl      = document.getElementById('wd-addr');
    const walletInput = document.getElementById('selected-wallet-id');
    const amountInput = document.getElementById('withdraw-amount');
    const submitBtn   = document.getElementById('wd-submit-btn');

    if (wallet) {
        const a = wallet.address;
        addrEl.textContent = a.substring(0, 12) + '...' + a.slice(-6);
        addrEl.className   = 'wd-addr-text has-addr';
        walletInput.value  = wallet.id;
        amountInput.disabled     = false;
        amountInput.placeholder  = '';
        submitBtn.disabled       = false;
    } else {
        addrEl.textContent = FWTrans.noAddress;
        addrEl.className   = 'wd-addr-text no-addr';
        walletInput.value  = '';
        amountInput.disabled    = true;
        amountInput.placeholder = FWTrans.noAddress;
        amountInput.value       = '';
        submitBtn.disabled      = true;
        document.getElementById('wd-recv-val').textContent = '0 USDT';
        document.getElementById('wd-fee-val').textContent  = '0';
    }
}

function fillAll() {
    const amountInput = document.getElementById('withdraw-amount');
    if (amountInput.disabled) return;
    amountInput.value = userBalance.toFixed(2);
    calculateFee();
}

function calculateFee() {
    const amount = parseFloat(document.getElementById('withdraw-amount').value) || 0;
    const fee    = amount > 0 ? (amount < 100 ? 5 : amount * 0.05) : 0;
    const recv   = Math.max(0, amount - fee);
    document.getElementById('wd-recv-val').textContent = recv.toFixed(2) + ' USDT';
    document.getElementById('wd-fee-val').textContent  = fee.toFixed(2);
}

function submitWithdraw() {
    const wallet = walletsByNetwork[currentNetwork];
    if (!wallet) return;
    const amount = parseFloat(document.getElementById('withdraw-amount').value);
    if (!amount || amount <= 0) { alert(FWTrans.enterValidAmount); return; }
    if (amount < 50) { alert(FWTrans.minimumWithdrawal); return; }
    if (amount > userBalance) { alert(FWTrans.insufficientBalance.replace(':balance', userBalance.toFixed(2))); return; }
    const fee   = amount < 100 ? 5 : amount * 0.05;
    const total = amount - fee;
    if (total <= 0) { alert(FWTrans.amountTooSmall); return; }
    const msg = FWTrans.confirmWithdrawal
        .replace(':amount', amount.toFixed(2))
        .replace(':fee',    fee.toFixed(2))
        .replace(':total',  total.toFixed(2));
    if (confirm(msg)) { document.getElementById('withdraw-form').submit(); }
}

document.addEventListener('DOMContentLoaded', function () {
    selectNetwork('trc20', document.querySelector('.wd-net-btn[data-net="trc20"]'));
    document.getElementById('withdraw-amount').addEventListener('input', calculateFee);
});
</script>
@endpush
@endsection
