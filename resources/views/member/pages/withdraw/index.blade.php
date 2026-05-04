@extends('member.layouts.app')
@section('content')
    <div class="scrollable-content">
        <div class="content-section" style="padding: 0;">

            <!-- Header -->
            <div class="pg-header">
                <a href="{{ route('member.profile.index') }}" class="pg-back">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div class="pg-header-text">
                    <h5 class="pg-title">{{ __('app.withdraw') }}</h5>
                </div>
                <a href="{{ route('member.withdraw.history') }}" class="pg-header-action">
                    <i class="bi bi-clock-history"></i> {{ __('app.history') }}
                </a>
            </div>

            <!-- Verification Alert -->
            @if (!auth()->user()->is_verified)
                <div class="form-block" style="background: rgba(255,193,7,0.05);">
                    <div class="d-flex align-items-start gap-3">
                        <div class="warning-icon-wrapper">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold" style="font-size: 14px; color: #ffc107;">
                                {{ __('app.account_not_verified') }}</h6>
                            <p class="small mb-0" style="font-size: 13px; color: var(--text-primary);">
                                {{ __('app.account_not_verified_message') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <form id="withdraw-form" action="{{ route('member.withdraw.store') }}" method="POST">
                @csrf

                <!-- Available Balance -->
                <div class="balance-hero">
                    <div>
                        <p class="text-muted mb-1 small">{{ __('app.available_balance') }}</p>
                        <h5 class="text-gold mb-0 fw-bold">{{ number_format($userBalance, 2) }} USDT</h5>
                    </div>
                    <div class="balance-hero-icon">
                        <i class="bi bi-wallet2"></i>
                    </div>
                </div>

                <!-- Currency info -->
                <div class="form-block" style="padding-top: 14px; padding-bottom: 14px;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="currency-icon-wrapper">
                            <span style="color: var(--gold-color); font-size: 20px; font-weight: bold;">₮</span>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small">{{ __('app.currency') }}</p>
                            <h6 class="mb-0 fw-bold" style="color: var(--text-primary);">{{ __('app.usdt_tether') }}</h6>
                        </div>
                    </div>
                </div>

                <!-- Withdrawal Amount -->
                <div class="form-block">
                    <h6 class="mb-3 fw-bold" style="color: var(--text-primary); font-size: 14px;">
                        {{ __('app.withdrawal_amount') }}</h6>
                    <div class="mb-3">
                        <label class="text-muted small mb-2 d-block">{{ __('app.amount_usdt') }}</label>
                        <div class="input-with-icon">
                            <span class="input-icon">₮</span>
                            <input type="number" name="amount" id="withdraw-amount" class="form-control-dark with-icon"
                                placeholder="{{ __('app.enter_amount') }}" value="{{ old('amount') }}" step="0.01"
                                min="50" {{ !auth()->user()->is_verified ? 'disabled' : 'required' }}>
                        </div>
                        @error('amount')
                            <small class="text-danger mt-1 d-block">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Select Wallet -->
                <div class="form-block">
                    <h6 class="mb-3 fw-bold" style="color: var(--text-primary); font-size: 14px;">
                        {{ __('app.select_wallet_account') }}</h6>
                    <div>
                        <label class="text-muted small mb-2 d-block">{{ __('app.choose_wallet_account') }}</label>
                        <select name="wallet_id" id="wallet-account" class="form-control-dark-select"
                            {{ !auth()->user()->is_verified || $wallets->isEmpty() ? 'disabled' : 'required' }}>
                            <option value="">{{ __('app.select_wallet_placeholder') }}</option>
                            @forelse($wallets as $wallet)
                                <option value="{{ $wallet->id }}"
                                    {{ old('wallet_id') == $wallet->id ? 'selected' : '' }}>
                                    {{ $wallet->account_number }} - {{ $wallet->type }}
                                </option>
                            @empty
                                <option value="" disabled>{{ __('app.no_wallet_available') }}</option>
                            @endforelse
                        </select>
                        @error('wallet_id')
                            <small class="text-danger mt-1 d-block">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Fee Calculation -->
                <div class="form-block" id="fee-card" style="display: none; background: rgba(0,229,255,0.04);">
                    <h6 class="mb-3 fw-bold" style="color: var(--text-primary); font-size: 14px;">
                        {{ __('app.withdrawal_summary') }}</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">{{ __('app.withdrawal_amount') }}</span>
                        <span class="fw-bold" style="color: var(--text-primary);" id="display-amount">0.00 USDT</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">{{ __('app.withdrawal_fee') }}</span>
                        <span class="fw-bold" style="color: var(--text-primary);" id="display-fee">0.00 USDT</span>
                    </div>
                    <hr style="border-color: var(--border-color);">
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold" style="color: var(--text-primary);">{{ __('app.you_will_receive') }}</span>
                        <span class="text-gold fw-bold" id="display-total">0.00 USDT</span>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="form-block">
                    <button type="button" class="btn-cta" onclick="submitWithdraw()"
                        {{ !auth()->user()->is_verified || $wallets->isEmpty() ? 'disabled' : '' }}>
                        {{ __('app.submit_withdrawal') }}
                    </button>
                </div>
            </form>

        </div>
    </div>

    @push('styles')
        <style>
            .warning-icon-wrapper {
                width: 40px; height: 40px;
                background: rgba(255, 193, 7, 0.15); border-radius: 50%;
                display: flex; align-items: center; justify-content: center; flex-shrink: 0;
            }
            .warning-icon-wrapper i { font-size: 20px; color: #ffc107; }

            .currency-icon-wrapper {
                width: 40px; height: 40px;
                background: rgba(0, 229, 255, 0.1); border-radius: 8px;
                display: flex; align-items: center; justify-content: center;
            }

            .form-control-dark-select {
                background: rgba(0, 229, 255, 0.05);
                border: 1px solid var(--border-color);
                border-radius: 8px; padding: 12px 16px;
                color: var(--text-primary); font-size: 14px; font-weight: 600;
                width: 100%; transition: all 0.2s ease;
                appearance: none; -webkit-appearance: none; -moz-appearance: none;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2300e5ff' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
                background-repeat: no-repeat; background-position: right 12px center;
                background-size: 16px; padding-right: 40px;
            }
            .form-control-dark-select:focus {
                outline: none; border-color: var(--gold-color);
                background-color: rgba(0, 229, 255, 0.1);
                box-shadow: 0 0 0 3px rgba(0, 229, 255, 0.1);
            }
            .form-control-dark-select option { background-color: #131d2e !important; color: var(--text-primary) !important; }
            .form-control-dark-select:disabled { opacity: 0.5; cursor: not-allowed; }

            .alert-info-box {
                background: rgba(0, 229, 255, 0.1); border: 1px solid rgba(0, 229, 255, 0.3);
                border-radius: 8px; padding: 12px;
                display: flex; align-items: flex-start; color: var(--gold-color);
            }

            @media (max-width: 480px) { .form-control-dark-select { font-size: 16px; } }
        </style>
    @endpush

    @push('scripts')
        <script>
            const userBalance = {{ $userBalance }};
            const isVerified = {{ auth()->user()->is_verified ? 'true' : 'false' }};

            const translations = {
                accountNotVerifiedAlert: "{{ __('app.account_not_verified_alert') }}",
                withdrawalNotProcessed: "{{ __('app.withdrawal_not_processed') }}",
                enterValidAmount: "{{ __('app.enter_valid_amount') }}",
                minimumWithdrawal50: "Minimum withdrawal is 50 USDT",
                insufficientBalance: "{{ __('app.insufficient_balance_withdraw') }}",
                pleaseSelectWallet: "{{ __('app.please_select_wallet') }}",
                amountTooSmall: "{{ __('app.amount_too_small') }}",
                confirmWithdrawal: "{{ __('app.confirm_withdrawal') }}"
            };

            @if (session('success'))
                alert('{{ session('success') }}');
            @endif
            @if (session('error'))
                alert('{{ session('error') }}');
            @endif
            @if ($errors->any())
                alert('{{ $errors->first() }}');
            @endif

            function setWithdrawAmount(amount) {
                if (!isVerified) { alert(translations.accountNotVerifiedAlert); return; }
                document.getElementById('withdraw-amount').value = amount;
                calculateFee();
            }

            document.getElementById('withdraw-amount').addEventListener('input', function() { calculateFee(); });

            function calculateFee() {
                const amount = parseFloat(document.getElementById('withdraw-amount').value) || 0;
                if (amount > 0) {
                    const fee = amount < 100 ? 5 : amount * 0.05;
                    const total = amount - fee;
                    document.getElementById('display-amount').textContent = amount.toFixed(2) + ' USDT';
                    document.getElementById('display-fee').textContent = fee.toFixed(2) + ' USDT';
                    document.getElementById('display-total').textContent = total.toFixed(2) + ' USDT';
                    document.getElementById('fee-card').style.display = 'block';
                } else {
                    document.getElementById('fee-card').style.display = 'none';
                }
            }

            function submitWithdraw() {
                if (!isVerified) { alert(translations.withdrawalNotProcessed); return; }
                const amount = parseFloat(document.getElementById('withdraw-amount').value);
                const walletSelect = document.getElementById('wallet-account');
                if (!amount || amount <= 0) { alert(translations.enterValidAmount); return; }
                if (amount < 50) { alert(translations.minimumWithdrawal50); return; }
                if (amount > userBalance) { alert(translations.insufficientBalance.replace(':balance', userBalance.toFixed(2))); return; }
                if (!walletSelect.value) { alert(translations.pleaseSelectWallet); return; }
                const fee = amount < 100 ? 5 : amount * 0.05;
                const total = amount - fee;
                if (total <= 0) { alert(translations.amountTooSmall); return; }
                const confirmMessage = translations.confirmWithdrawal
                    .replace(':amount', amount.toFixed(2))
                    .replace(':fee', fee.toFixed(2))
                    .replace(':total', total.toFixed(2));
                if (confirm(confirmMessage)) { document.getElementById('withdraw-form').submit(); }
            }

            document.getElementById('wallet-account').addEventListener('change', function() {
                this.style.borderColor = this.value ? 'var(--gold-color)' : 'var(--border-color)';
                this.style.backgroundColor = this.value ? 'rgba(0, 229, 255, 0.1)' : 'rgba(0, 229, 255, 0.05)';
            });

            window.addEventListener('DOMContentLoaded', function() {
                if (!isVerified) return;
                const walletSelect = document.getElementById('wallet-account');
                const options = walletSelect.querySelectorAll('option[value]:not([value=""])');
                if (options.length === 1 && !walletSelect.value) {
                    walletSelect.value = options[0].value;
                    walletSelect.dispatchEvent(new Event('change'));
                }
            });
        </script>
    @endpush
@endsection
