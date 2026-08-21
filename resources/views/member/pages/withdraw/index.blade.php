@extends('member.layouts.app')
@section('content')
<div class="scrollable-content wdv2">

    {{-- ═══ HEADER ═══ --}}
    <div class="wdv2-header">
        <a href="{{ route('member.profile.index') }}" class="wdv2-back">
            <i class="bi bi-chevron-left"></i>
        </a>
        <div class="wdv2-header-center">
            <div class="wdv2-title">{{ __('app.withdrawal_usdt') }}</div>
            <div class="wdv2-subtitle">{{ __('app.withdrawal_subtitle') }}</div>
        </div>
        <a href="{{ route('member.withdraw.history') }}" class="wdv2-histbtn">
            <i class="bi bi-clock-history"></i>
        </a>
    </div>

    <form id="withdraw-form" action="{{ route('member.withdraw.store') }}" method="POST">
        @csrf
        <input type="hidden" name="wallet_id" id="selected-wallet-id">

        {{-- ═══ BALANCE STRIP ═══ --}}
        <div class="wdv2-balance-strip">
            <div class="wdv2-bal-left">
                <div class="wdv2-usdt-badge">₮</div>
                <div>
                    <div class="wdv2-bal-label">{{ __('app.available') }}</div>
                    <div class="wdv2-bal-val">
                        <span id="wd-avail-val">{{ number_format($userBalance, 2) }}</span>
                        <span class="wdv2-bal-cur">USDT</span>
                    </div>
                </div>
            </div>
            <div class="wdv2-bal-right">
                <span class="wdv2-cur-label">{{ __('app.select_currency') }}</span>
                <i class="bi bi-chevron-right wdv2-cur-chev"></i>
            </div>
        </div>

        {{-- ═══ NETWORK SELECTOR ═══ --}}
        <div class="wdv2-block">
            <div class="wdv2-block-label">{{ __('app.blockchain_network') }}</div>
            <div class="wdv2-net-row">
                <button type="button" class="wdv2-net active" data-net="trc20" onclick="selectNetwork('trc20', this)">
                    <span class="wdv2-net-name">TRC20</span>
                    <span class="wdv2-net-sub">TRON</span>
                </button>
                <button type="button" class="wdv2-net" data-net="bep20" onclick="selectNetwork('bep20', this)">
                    <span class="wdv2-net-name">BEP20</span>
                    <span class="wdv2-net-sub">BSC</span>
                </button>
            </div>
        </div>

        {{-- ═══ WALLET ADDRESS ═══ --}}
        <div class="wdv2-block">
            <div class="wdv2-block-label">{{ __('app.blockchain_network') }}</div>
            <div class="wdv2-addr-card">
                <div class="wdv2-addr-ico"><i class="bi bi-wallet2"></i></div>
                <span id="wd-addr" class="wdv2-addr-text no-addr">{{ __('app.no_withdrawal_address') }}</span>
                <a href="{{ route('member.wallet.index') }}" class="wdv2-bind">
                    {{ __('app.bind') }}&nbsp;<i class="bi bi-arrow-right-circle-fill"></i>
                </a>
            </div>
        </div>

        {{-- ═══ AMOUNT INPUT ═══ --}}
        <div class="wdv2-block">
            <div class="wdv2-block-label">{{ __('app.quantity') }}</div>
            <div class="wdv2-amount-wrap">
                <input type="number" id="withdraw-amount" name="amount"
                    class="wdv2-amount-input"
                    step="0.01" min="10" disabled
                    placeholder="0.00">
                <div class="wdv2-amount-right">
                    <span class="wdv2-amount-cur">USDT</span>
                    <button type="button" class="wdv2-all" onclick="fillAll()">{{ __('app.all') }}</button>
                </div>
            </div>

            {{-- Receivable row --}}
            <div class="wdv2-recv-row">
                <div class="wdv2-recv-item">
                    <span class="wdv2-recv-lbl">{{ __('app.receivable_amount') }}</span>
                    <span class="wdv2-recv-val" id="wd-recv-val">0 USDT</span>
                </div>
                <div class="wdv2-recv-sep"></div>
                <div class="wdv2-recv-item">
                    <span class="wdv2-recv-lbl">{{ __('app.withdrawal_fee') }}</span>
                    <span class="wdv2-recv-fee"><span id="wd-fee-val">0</span> USDT</span>
                </div>
            </div>
        </div>

        {{-- ═══ INSTRUCTIONS ═══ --}}
        <div class="wdv2-block wdv2-instr">
            <div class="wdv2-instr-head">
                <i class="bi bi-info-circle-fill wdv2-instr-ico"></i>
                <span>{{ __('app.withdrawal_instructions') }}</span>
            </div>
            <ul class="wdv2-instr-list">
                <li>{{ __('app.wd_instr_1') }}</li>
                <li>{{ __('app.wd_instr_2') }}</li>
                <li>{{ __('app.wd_instr_3') }}</li>
                <li>{{ __('app.wd_instr_4') }}</li>
            </ul>
        </div>

        {{-- ═══ SUBMIT ═══ --}}
        <div class="wdv2-footer">
            <button type="button" class="wdv2-submit" id="wd-submit-btn" disabled onclick="submitWithdraw()">
                <i class="bi bi-arrow-up-circle-fill"></i>
                {{ __('app.withdrawal_btn') }}
            </button>
        </div>

    </form>
</div>

@push('styles')
<style>
/* ══ Root ══════════════════════════════════════════════════════ */
.wdv2 { background: var(--bg-dark); }

/* ══ Header ════════════════════════════════════════════════════ */
.wdv2-header {
    display: flex; align-items: center;
    padding: 14px 16px;
    border-bottom: 1px solid rgba(255,255,255,0.06);
}
.wdv2-back {
    width: 36px; height: 36px; border-radius: 10px;
    background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08);
    display: flex; align-items: center; justify-content: center;
    color: #fff; text-decoration: none; font-size: 15px; flex-shrink: 0;
}
.wdv2-header-center { flex: 1; text-align: center; padding: 0 10px; }
.wdv2-title { color: #fff; font-size: 15px; font-weight: 800; }
.wdv2-subtitle { color: rgba(255,255,255,0.3); font-size: 10px; margin-top: 2px; letter-spacing: 0.3px; }
.wdv2-histbtn {
    width: 36px; height: 36px; border-radius: 10px;
    background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08);
    display: flex; align-items: center; justify-content: center;
    color: rgba(255,255,255,0.45); text-decoration: none; font-size: 16px; flex-shrink: 0;
}

/* ══ Balance Strip ══════════════════════════════════════════════ */
.wdv2-balance-strip {
    display: flex; align-items: center; justify-content: space-between;
    margin: 14px 14px 0;
    padding: 16px;
    background: linear-gradient(135deg, rgba(52,211,153,0.08), rgba(167,139,250,0.06));
    border: 1px solid rgba(52,211,153,0.2);
    border-radius: 16px;
}
.wdv2-bal-left { display: flex; align-items: center; gap: 12px; }
.wdv2-usdt-badge {
    width: 40px; height: 40px; border-radius: 12px;
    background: #26a17b;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 20px; font-weight: 900; flex-shrink: 0;
}
.wdv2-bal-label { color: rgba(255,255,255,0.35); font-size: 10px; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase; }
.wdv2-bal-val {
    color: #fff; font-size: 20px; font-weight: 900;
    font-family: 'SF Mono', 'Fira Code', monospace;
    letter-spacing: -0.5px; margin-top: 2px;
    display: flex; align-items: baseline; gap: 5px;
}
.wdv2-bal-cur { font-size: 11px; color: #34d399; font-weight: 700; font-family: inherit; }
.wdv2-bal-right { display: flex; align-items: center; gap: 5px; }
.wdv2-cur-label { color: rgba(255,255,255,0.3); font-size: 12px; }
.wdv2-cur-chev  { color: rgba(255,255,255,0.2); font-size: 11px; }

/* ══ Block (generic section) ════════════════════════════════════ */
.wdv2-block { padding: 16px 14px 0; }
.wdv2-block-label {
    font-size: 10px; font-weight: 800; letter-spacing: 1.8px;
    text-transform: uppercase; color: rgba(255,255,255,0.3);
    margin-bottom: 10px; padding: 0 2px;
}

/* ══ Network Selector ══════════════════════════════════════════ */
.wdv2-net-row { display: flex; gap: 10px; }
.wdv2-net {
    flex: 1; padding: 12px 10px;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 12px;
    cursor: pointer; transition: all 0.2s;
    display: flex; flex-direction: column; align-items: center; gap: 2px;
}
.wdv2-net.active {
    background: rgba(167,139,250,0.1);
    border-color: rgba(167,139,250,0.4);
}
.wdv2-net-name {
    color: rgba(255,255,255,0.5); font-size: 15px; font-weight: 800;
    transition: color 0.2s;
}
.wdv2-net-sub {
    color: rgba(255,255,255,0.25); font-size: 10px; font-weight: 600;
    letter-spacing: 0.5px;
    transition: color 0.2s;
}
.wdv2-net.active .wdv2-net-name { color: #a78bfa; }
.wdv2-net.active .wdv2-net-sub  { color: rgba(167,139,250,0.6); }

/* ══ Address Card ═══════════════════════════════════════════════ */
.wdv2-addr-card {
    display: flex; align-items: center; gap: 10px;
    padding: 14px;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 12px;
}
.wdv2-addr-ico {
    width: 34px; height: 34px; border-radius: 10px; flex-shrink: 0;
    background: rgba(96,165,250,0.1); border: 1px solid rgba(96,165,250,0.2);
    display: flex; align-items: center; justify-content: center;
    color: #60a5fa; font-size: 15px;
}
.wdv2-addr-text {
    flex: 1; font-size: 12px; word-break: break-all; line-height: 1.5;
}
.wdv2-addr-text.no-addr  { color: #f87171; }
.wdv2-addr-text.has-addr { color: rgba(255,255,255,0.8); font-family: 'SF Mono', 'Fira Code', monospace; }
.wdv2-bind {
    display: inline-flex; align-items: center; gap: 4px;
    color: #34d399; font-size: 12px; font-weight: 700;
    text-decoration: none; white-space: nowrap; flex-shrink: 0;
}

/* ══ Amount Input ═══════════════════════════════════════════════ */
.wdv2-amount-wrap {
    display: flex; align-items: center;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 12px; overflow: hidden;
    transition: border-color 0.2s;
}
.wdv2-amount-wrap:focus-within {
    border-color: rgba(167,139,250,0.5);
}
.wdv2-amount-input {
    flex: 1; background: transparent; border: none; outline: none;
    color: #fff; font-size: 22px; font-weight: 800; padding: 14px 16px;
    font-family: 'SF Mono', 'Fira Code', monospace;
    letter-spacing: -0.5px; min-width: 0;
}
.wdv2-amount-input::placeholder { color: rgba(255,255,255,0.15); font-size: 22px; }
.wdv2-amount-input::-webkit-outer-spin-button,
.wdv2-amount-input::-webkit-inner-spin-button { -webkit-appearance: none; }
.wdv2-amount-input:disabled { opacity: 0.5; cursor: not-allowed; }
.wdv2-amount-right {
    display: flex; align-items: center; gap: 8px;
    padding: 0 14px; flex-shrink: 0;
}
.wdv2-amount-cur { color: rgba(255,255,255,0.3); font-size: 12px; font-weight: 700; }
.wdv2-all {
    background: rgba(167,139,250,0.1); border: 1px solid rgba(167,139,250,0.25);
    border-radius: 6px; padding: 4px 8px;
    color: #a78bfa; font-size: 11px; font-weight: 800;
    cursor: pointer; letter-spacing: 0.5px;
}

/* ══ Receivable row ═════════════════════════════════════════════ */
.wdv2-recv-row {
    display: flex; align-items: center;
    margin-top: 10px;
    background: rgba(255,255,255,0.02);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 10px; overflow: hidden;
}
.wdv2-recv-item {
    flex: 1; padding: 12px 14px;
    display: flex; flex-direction: column; gap: 3px;
}
.wdv2-recv-sep { width: 1px; height: 36px; background: rgba(255,255,255,0.06); flex-shrink: 0; }
.wdv2-recv-lbl { color: rgba(255,255,255,0.3); font-size: 10px; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase; }
.wdv2-recv-val {
    color: #34d399; font-size: 14px; font-weight: 800;
    font-variant-numeric: tabular-nums;
}
.wdv2-recv-fee {
    color: rgba(255,255,255,0.55); font-size: 13px; font-weight: 700;
    font-variant-numeric: tabular-nums;
}

/* ══ Instructions ═══════════════════════════════════════════════ */
.wdv2-instr {
    margin-top: 4px;
}
.wdv2-instr-head {
    display: flex; align-items: center; gap: 7px;
    margin-bottom: 10px;
    color: rgba(251,146,60,0.9); font-size: 12px; font-weight: 700;
}
.wdv2-instr-ico { font-size: 14px; }
.wdv2-instr-list {
    list-style: none; padding: 0; margin: 0;
    display: flex; flex-direction: column; gap: 7px;
}
.wdv2-instr-list li {
    display: flex; align-items: flex-start; gap: 8px;
    color: rgba(255,255,255,0.3); font-size: 11px; line-height: 1.5;
}
.wdv2-instr-list li::before {
    content: '·'; color: #fb923c; font-size: 18px; line-height: 1; flex-shrink: 0; margin-top: -1px;
}

/* ══ Footer / Submit ════════════════════════════════════════════ */
.wdv2-footer { padding: 20px 14px 28px; }
.wdv2-submit {
    width: 100%; padding: 15px;
    background: linear-gradient(135deg, #34d399, #059669);
    border: none; border-radius: 14px;
    color: #fff; font-size: 15px; font-weight: 800;
    cursor: pointer; transition: all 0.2s;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    letter-spacing: 0.3px;
}
.wdv2-submit:not(:disabled):active { transform: scale(0.98); }
.wdv2-submit:disabled {
    opacity: 0.35; cursor: not-allowed;
    background: rgba(255,255,255,0.08);
}
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
    document.querySelectorAll('.wdv2-net').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    updateAddressDisplay();
    calculateFee();
}

function updateAddressDisplay() {
    const wallet      = walletsByNetwork[currentNetwork];
    const addrEl      = document.getElementById('wd-addr');
    const walletInput = document.getElementById('selected-wallet-id');
    const amountInput = document.getElementById('withdraw-amount');
    const submitBtn   = document.getElementById('wd-submit-btn');

    if (wallet) {
        addrEl.textContent = wallet.address;
        addrEl.className   = 'wdv2-addr-text has-addr';
        walletInput.value  = wallet.id;
        amountInput.disabled    = false;
        amountInput.placeholder = '0.00';
        submitBtn.disabled      = false;
    } else {
        addrEl.textContent = FWTrans.noAddress;
        addrEl.className   = 'wdv2-addr-text no-addr';
        walletInput.value  = '';
        amountInput.disabled    = true;
        amountInput.placeholder = '0.00';
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
    const fee    = amount > 0 ? (amount < 100 ? 5 : amount * 0.07) : 0;
    const recv   = Math.max(0, amount - fee);
    document.getElementById('wd-recv-val').textContent = recv.toFixed(2) + ' USDT';
    document.getElementById('wd-fee-val').textContent  = fee.toFixed(2);
}

function submitWithdraw() {
    const wallet = walletsByNetwork[currentNetwork];
    if (!wallet) return;
    const amount = parseFloat(document.getElementById('withdraw-amount').value);
    if (!amount || amount <= 0) { alert(FWTrans.enterValidAmount); return; }
    if (amount < 10) { alert(FWTrans.minimumWithdrawal); return; }
    if (amount > userBalance) { alert(FWTrans.insufficientBalance.replace(':balance', userBalance.toFixed(2))); return; }
    const fee   = amount < 100 ? 5 : amount * 0.07;
    const total = amount - fee;
    if (total <= 0) { alert(FWTrans.amountTooSmall); return; }
    const msg = FWTrans.confirmWithdrawal
        .replace(':amount', amount.toFixed(2))
        .replace(':fee',    fee.toFixed(2))
        .replace(':total',  total.toFixed(2));
    if (confirm(msg)) { document.getElementById('withdraw-form').submit(); }
}

document.addEventListener('DOMContentLoaded', function () {
    selectNetwork('trc20', document.querySelector('.wdv2-net[data-net="trc20"]'));
    document.getElementById('withdraw-amount').addEventListener('input', calculateFee);
});
</script>
@endpush
@endsection
