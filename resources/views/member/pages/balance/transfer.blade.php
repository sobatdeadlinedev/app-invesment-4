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
                    <h5 class="pg-title">{{ __('app.transfer') }}</h5>
                    <p class="pg-subtitle">{{ __('app.exchange_balance') }} ↔ {{ __('app.trade_balance') }}</p>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mx-3 mt-3" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mx-3 mt-3" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Transfer Form -->
            <form id="transferForm" action="{{ route('member.balance.transfer.to-trade') }}" method="POST">
                @csrf
                <input type="hidden" name="from_account" id="fromAccountInput" value="exchange">
                <input type="hidden" name="to_account" id="toAccountInput" value="trade">

                <!-- Select Currency -->
                <div class="w-card">
                    <div class="w-card-head"><i class="bi bi-coin"></i>{{ __('app.select_currency') }}</div>
                    <div class="form-block">
                        <div class="currency-option selected">
                            <div class="d-flex align-items-center gap-2">
                                <div class="currency-icon-box"><span>₮</span></div>
                                <span class="text-white fw-bold">USDT</span>
                            </div>
                            <i class="bi bi-check-circle-fill text-gold"></i>
                        </div>
                    </div>
                </div>

                <!-- Wallet Swap -->
                <div class="w-card">
                    <div class="w-card-head"><i class="bi bi-wallet2"></i>{{ __('app.wallet') }}</div>
                    <div class="form-block wallet-swap-wrap">
                        <div class="wallet-row" id="fromWalletRow">
                            <div class="wallet-row-icon from"><i class="bi bi-chevron-down"></i></div>
                            <div class="wallet-info">
                                <span class="wallet-name" id="fromWalletName">{{ __('app.exchange') }}</span>
                                <span class="wallet-available">{{ __('app.available') }}:
                                    <span id="fromWalletBalance">0</span> USDT</span>
                            </div>
                        </div>

                        <button type="button" class="swap-btn" id="swapBtn">
                            <i class="bi bi-arrow-down-up"></i>
                        </button>

                        <div class="wallet-row" id="toWalletRow">
                            <div class="wallet-row-icon to"><i class="bi bi-chevron-up"></i></div>
                            <div class="wallet-info">
                                <span class="wallet-name" id="toWalletName">{{ __('app.trade') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quantity -->
                <div class="w-card">
                    <div class="w-card-head"><i class="bi bi-currency-dollar"></i>{{ __('app.amount_of_transfers') }}</div>
                    <div class="form-block">
                        <div class="qty-input-row">
                            <input type="number" class="qty-input" id="transferAmount" name="amount"
                                placeholder="{{ __('app.enter_transfer_amount') }}" min="10" step="0.01" required>
                            <span class="qty-currency">USDT</span>
                            <button type="button" class="qty-max" id="btnAll">{{ __('app.all') }}</button>
                        </div>
                        <small class="text-muted d-block mt-2">{{ __('app.minimum_transfer') }}: 10.00 USDT</small>
                    </div>
                </div>

                <!-- Penalty Warning -->
                <div id="penaltyWarning" style="display: none;">
                    <div class="w-card" style="border-color: rgba(220,53,69,0.3);">
                        <div class="form-block" style="background: rgba(220,53,69,0.06);">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-exclamation-triangle-fill" style="color: #ff6b6b; font-size: 16px; margin-top: 2px; flex-shrink: 0;"></i>
                                <div style="color: #ff6b6b; font-size: 12px;">
                                    <strong>{{ __('app.warning') }}:</strong> {{ __('app.penalty_warning') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Volume Info -->
                <div id="volumeInfo" style="display: none;">
                    <div class="w-card">
                        <div class="form-block">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-info-circle-fill text-gold" style="font-size: 16px; margin-top: 2px; flex-shrink: 0;"></i>
                                <small class="text-muted">{{ __('app.volume_info') }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-page-footer">
                    <button type="submit" class="btn-cta">
                        <i class="bi bi-check-circle me-2"></i>{{ __('app.confirm') }}
                    </button>
                </div>
            </form>

        </div>
    </div>

    <style>
        .select-wrapper { position: relative; }

        .currency-option {
            display: flex; align-items: center; justify-content: space-between;
            padding: 12px 14px; border-radius: 10px;
            background: rgba(0,229,255,0.08); border: 1px solid rgba(0,229,255,0.2);
        }
        .currency-option.selected { border-left: 3px solid var(--gold-color); }
        .currency-icon-box {
            width: 30px; height: 30px; background: rgba(0,229,255,0.2);
            border-radius: 6px; display: flex; align-items: center; justify-content: center;
        }
        .currency-icon-box span { color: var(--gold-color); font-size: 15px; font-weight: bold; }

        /* Wallet swap section */
        .wallet-swap-wrap { position: relative; padding-top: 4px; padding-bottom: 4px; }
        .wallet-row {
            display: flex; align-items: center; gap: 12px;
            background: rgba(0,229,255,0.05); border: 1px solid var(--border-color);
            border-radius: 10px; padding: 12px 14px; margin-bottom: 10px;
        }
        .wallet-row:last-child { margin-bottom: 0; }
        .wallet-row-icon {
            width: 26px; height: 26px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            background: rgba(0,229,255,0.15); border: 1px solid rgba(0,229,255,0.3);
            color: var(--gold-color); font-size: 12px; flex-shrink: 0;
        }
        .wallet-info { display: flex; flex-direction: column; gap: 2px; }
        .wallet-name { color: var(--text-white); font-weight: 600; font-size: 14px; }
        .wallet-available { color: var(--text-muted); font-size: 11px; }
        .wallet-available #fromWalletBalance { color: var(--gold-color); font-weight: 600; }

        .swap-btn {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            width: 30px; height: 30px; border-radius: 50%;
            background: var(--gold-color); border: none; color: #0b1420;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; cursor: pointer; z-index: 2;
            box-shadow: 0 0 0 4px #0f1a2b;
            transition: transform 0.2s ease;
        }
        .swap-btn:active { transform: translateY(-50%) rotate(180deg); }
        .wallet-row { padding-left: 56px; }

        /* Quantity */
        .qty-input-row {
            display: flex; align-items: center; gap: 10px;
            background: rgba(0,229,255,0.05); border: 1px solid var(--border-color);
            border-radius: 10px; padding: 4px 14px;
        }
        .qty-input {
            flex: 1; background: transparent; border: none; outline: none;
            color: var(--text-white); font-size: 14px; padding: 10px 0;
        }
        .qty-input::placeholder { color: var(--text-muted); }
        .qty-currency { color: var(--text-muted); font-size: 12px; font-weight: 600; }
        .qty-max {
            background: transparent; border: none; color: var(--gold-color);
            font-size: 12px; font-weight: 700; cursor: pointer; padding: 0;
        }
    </style>

    @push('scripts')
        <script>
            const exchangeBalance = {{ $exchangeBalance }};
            const tradeBalance = {{ $tradeBalance }};
            const availableTradeBalance = {{ $availableTradeBalance }};
            const needsPenalty = {{ $needsPenalty ? 'true' : 'false' }};
            const remainingVolume = {{ $remainingVolume ?? 0 }};
            const volumePercentage = {{ $volumePercentage ?? 0 }};

            const translations = {
                exchange: "{{ __('app.exchange') }}",
                trade: "{{ __('app.trade') }}",
                minimumTransferAlert: "{{ __('app.minimum_transfer_alert') }}",
                insufficientBalance: "{{ __('app.insufficient_balance') }}",
                warning: "{{ __('app.warning') }}"
            };

            const fromAccountInput = document.getElementById('fromAccountInput');
            const toAccountInput = document.getElementById('toAccountInput');
            const fromWalletName = document.getElementById('fromWalletName');
            const toWalletName = document.getElementById('toWalletName');
            const fromWalletBalance = document.getElementById('fromWalletBalance');
            const swapBtn = document.getElementById('swapBtn');
            const transferAmount = document.getElementById('transferAmount');
            const btnAll = document.getElementById('btnAll');
            const transferForm = document.getElementById('transferForm');
            const penaltyWarning = document.getElementById('penaltyWarning');
            const volumeInfo = document.getElementById('volumeInfo');

            let direction = 'toTrade'; // 'toTrade' = exchange -> trade, 'toExchange' = trade -> exchange

            function updateDirection() {
                let available = 0;

                if (direction === 'toTrade') {
                    fromAccountInput.value = 'exchange';
                    toAccountInput.value = 'trade';
                    fromWalletName.textContent = translations.exchange;
                    toWalletName.textContent = translations.trade;
                    available = exchangeBalance;

                    penaltyWarning.style.display = 'none';
                    volumeInfo.style.display = 'block';
                    transferForm.action = "{{ route('member.balance.transfer.to-trade') }}";
                } else {
                    fromAccountInput.value = 'trade';
                    toAccountInput.value = 'exchange';
                    fromWalletName.textContent = translations.trade;
                    toWalletName.textContent = translations.exchange;
                    available = availableTradeBalance;

                    volumeInfo.style.display = 'none';
                    penaltyWarning.style.display = needsPenalty ? 'block' : 'none';
                    transferForm.action = "{{ route('member.balance.transfer.to-exchange') }}";
                }

                fromWalletBalance.textContent = available.toFixed(2);
                transferAmount.max = available;
            }

            swapBtn.addEventListener('click', function() {
                direction = direction === 'toTrade' ? 'toExchange' : 'toTrade';
                updateDirection();
            });

            btnAll.addEventListener('click', function() {
                transferAmount.value = parseFloat(fromWalletBalance.textContent).toFixed(2);
            });

            transferForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const amount = parseFloat(transferAmount.value);
                const from = fromAccountInput.value;
                const to = toAccountInput.value;

                if (amount < 10) { alert(translations.minimumTransferAlert); return; }
                if (amount > parseFloat(fromWalletBalance.textContent)) { alert(translations.insufficientBalance); return; }

                let message = `Transfer ${amount.toFixed(2)} USDT from ${from.toUpperCase()} to ${to.toUpperCase()}?`;
                if (from === 'trade' && needsPenalty) {
                    const penalty = amount * 0.30;
                    const net = amount - penalty;
                    const remainingPercent = (100 - volumePercentage).toFixed(2);
                    message = `${translations.warning}: 30% Penalty will be applied!\n\n` +
                        `Remaining Trading Volume: ${remainingVolume.toFixed(2)} USDT (${remainingPercent}%)\n\n` +
                        `Transfer Amount: ${amount.toFixed(2)} USDT\n` +
                        `Penalty (30%): ${penalty.toFixed(2)} USDT\n` +
                        `You will receive: ${net.toFixed(2)} USDT\n\nDo you want to continue?`;
                }
                if (confirm(message)) { this.submit(); }
            });

            updateDirection();

            setTimeout(function() {
                document.querySelectorAll('.alert').forEach(function(alert) {
                    alert.style.opacity = '0';
                    setTimeout(function() { alert.remove(); }, 300);
                });
            }, 5000);
        </script>
    @endpush
@endsection