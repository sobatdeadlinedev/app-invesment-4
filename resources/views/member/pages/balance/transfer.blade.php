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

            <!-- Balance Summary -->
            <div class="stat-row">
                <div class="stat-cell">
                    <div class="d-flex align-items-center gap-2">
                        <div class="transfer-bal-icon exchange">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0" style="font-size: 11px;">{{ __('app.exchange_balance') }}</p>
                            <h6 class="text-white mb-0 fw-bold">{{ number_format($exchangeBalance, 2) }}</h6>
                            <small class="text-muted" style="font-size: 10px;">USDT</small>
                        </div>
                    </div>
                </div>
                <div class="stat-cell">
                    <div class="d-flex align-items-center gap-2">
                        <div class="transfer-bal-icon trade">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0" style="font-size: 11px;">{{ __('app.trade_balance') }}</p>
                            <h6 class="text-white mb-0 fw-bold">{{ number_format($tradeBalance, 2) }}</h6>
                            <small class="text-muted" style="font-size: 10px;">USDT</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transfer Form -->
            <form id="transferForm" action="" method="POST">
                @csrf

                <!-- From -->
                <div class="form-block">
                    <label class="form-block-title">{{ __('app.from') }}</label>
                    <div class="select-wrapper">
                        <select class="form-select-dark" id="fromAccount" name="from_account">
                            <option value="exchange">{{ __('app.exchange_balance') }}</option>
                            <option value="trade">{{ __('app.trade_balance') }}</option>
                        </select>
                        <i class="bi bi-chevron-down select-arrow"></i>
                    </div>
                </div>

                <!-- To -->
                <div class="form-block">
                    <label class="form-block-title">{{ __('app.transfer_to') }}</label>
                    <div class="select-wrapper">
                        <select class="form-select-dark" id="toAccount" name="to_account" disabled>
                            <option value="trade">{{ __('app.trade_balance') }}</option>
                        </select>
                        <i class="bi bi-chevron-down select-arrow"></i>
                    </div>
                </div>

                <!-- Currency -->
                <div class="form-block">
                    <p class="form-block-title">{{ __('app.select_currency') }}</p>
                    <div class="currency-option selected">
                        <div class="d-flex align-items-center gap-2">
                            <div class="currency-icon-box">
                                <span>₮</span>
                            </div>
                            <span class="text-white fw-bold">USDT</span>
                        </div>
                        <i class="bi bi-check-circle-fill text-gold"></i>
                    </div>
                </div>

                <!-- Amount -->
                <div class="form-block">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <p class="form-block-title mb-0">{{ __('app.amount_of_transfers') }}</p>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted" style="font-size: 11px;">
                                {{ __('app.available') }}: <span class="text-gold fw-bold" id="availableAmount">0</span>
                            </span>
                            <button type="button" class="btn-all" id="btnAll">{{ __('app.all') }}</button>
                        </div>
                    </div>
                    <div class="input-with-icon">
                        <span class="input-icon">₮</span>
                        <input type="number" class="form-control-dark with-icon" id="transferAmount" name="amount"
                            placeholder="{{ __('app.enter_amount') }}" min="10" step="0.01" required>
                    </div>
                    <small class="text-muted d-block mt-2">{{ __('app.minimum_transfer') }}: 10.00 USDT</small>
                </div>

                <!-- Penalty Warning -->
                <div id="penaltyWarning" style="display: none;">
                    <div class="form-block" style="background: rgba(220,53,69,0.06);">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-exclamation-triangle-fill" style="color: #ff6b6b; font-size: 16px; margin-top: 2px; flex-shrink: 0;"></i>
                            <div style="color: #ff6b6b; font-size: 12px;">
                                <strong>{{ __('app.warning') }}:</strong> {{ __('app.penalty_warning') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Volume Info -->
                <div id="volumeInfo" style="display: none;">
                    <div class="form-block" style="background: rgba(0,229,255,0.04);">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-info-circle-fill text-gold" style="font-size: 16px; margin-top: 2px; flex-shrink: 0;"></i>
                            <small class="text-muted">{{ __('app.volume_info') }}</small>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="form-block">
                    <button type="submit" class="btn-cta">
                        <i class="bi bi-check-circle me-2"></i>{{ __('app.confirm') }}
                    </button>
                </div>
            </form>

        </div>
    </div>

    <style>
        .transfer-bal-icon {
            width: 38px; height: 38px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            border: 1px solid; flex-shrink: 0;
        }
        .transfer-bal-icon i { font-size: 18px; }
        .transfer-bal-icon.exchange {
            background: rgba(0,229,255,0.15); border-color: rgba(0,229,255,0.3);
        }
        .transfer-bal-icon.exchange i { color: var(--gold-color); }
        .transfer-bal-icon.trade {
            background: rgba(0,229,255,0.1); border-color: rgba(0,229,255,0.25);
        }
        .transfer-bal-icon.trade i { color: #00e5ff; }

        .select-wrapper { position: relative; }
        .form-select-dark {
            width: 100%; background: rgba(0,229,255,0.05);
            border: 1px solid var(--border-color); border-radius: 10px;
            padding: 13px 44px 13px 16px; color: var(--text-white);
            font-size: 14px; font-weight: 500; appearance: none; cursor: pointer;
            transition: all 0.2s ease;
        }
        .form-select-dark:focus { outline: none; border-color: var(--gold-color); background: rgba(0,229,255,0.08); }
        .form-select-dark:disabled { opacity: 0.5; cursor: not-allowed; }
        .form-select-dark option { background-color: #131d2e; color: var(--text-white); }
        .select-arrow {
            position: absolute; right: 14px; top: 50%;
            transform: translateY(-50%); color: var(--text-muted); pointer-events: none;
        }

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

        .btn-all {
            background: transparent; border: 1px solid var(--gold-color);
            color: var(--gold-color); padding: 3px 10px;
            border-radius: 6px; font-size: 11px; font-weight: 600;
            cursor: pointer; transition: all 0.2s ease;
        }
        .btn-all:hover { background: rgba(0,229,255,0.1); }
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
                exchangeBalance: "{{ __('app.exchange_balance') }}",
                tradeBalance: "{{ __('app.trade_balance') }}",
                minimumTransferAlert: "{{ __('app.minimum_transfer_alert') }}",
                insufficientBalance: "{{ __('app.insufficient_balance') }}",
                warning: "{{ __('app.warning') }}"
            };

            const fromAccount = document.getElementById('fromAccount');
            const toAccount = document.getElementById('toAccount');
            const transferAmount = document.getElementById('transferAmount');
            const availableAmount = document.getElementById('availableAmount');
            const btnAll = document.getElementById('btnAll');
            const transferForm = document.getElementById('transferForm');
            const penaltyWarning = document.getElementById('penaltyWarning');
            const volumeInfo = document.getElementById('volumeInfo');

            function updateAvailableBalance() {
                const from = fromAccount.value;
                let available = 0;
                if (from === 'exchange') {
                    available = exchangeBalance;
                    toAccount.innerHTML = `<option value="trade">${translations.tradeBalance}</option>`;
                    toAccount.value = 'trade';
                    penaltyWarning.style.display = 'none';
                    volumeInfo.style.display = 'block';
                    transferForm.action = "{{ route('member.balance.transfer.to-trade') }}";
                } else {
                    available = availableTradeBalance;
                    toAccount.innerHTML = `<option value="exchange">${translations.exchangeBalance}</option>`;
                    toAccount.value = 'exchange';
                    volumeInfo.style.display = 'none';
                    penaltyWarning.style.display = needsPenalty ? 'block' : 'none';
                    transferForm.action = "{{ route('member.balance.transfer.to-exchange') }}";
                }
                availableAmount.textContent = available.toFixed(2);
                transferAmount.max = available;
            }

            btnAll.addEventListener('click', function() {
                transferAmount.value = parseFloat(availableAmount.textContent).toFixed(2);
            });

            fromAccount.addEventListener('change', updateAvailableBalance);

            transferForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const amount = parseFloat(transferAmount.value);
                const from = fromAccount.value;
                const to = toAccount.value;

                if (amount < 10) { alert(translations.minimumTransferAlert); return; }
                if (amount > parseFloat(availableAmount.textContent)) { alert(translations.insufficientBalance); return; }

                let message = `Transfer ${amount.toFixed(2)} USDT from ${from.toUpperCase()} to ${to.toUpperCase()}?`;
                if (from === 'trade' && needsPenalty) {
                    const penalty = amount * 0.20;
                    const net = amount - penalty;
                    const remainingPercent = (100 - volumePercentage).toFixed(2);
                    message = `${translations.warning}: 20% Penalty will be applied!\n\n` +
                        `Remaining Trading Volume: ${remainingVolume.toFixed(2)} USDT (${remainingPercent}%)\n\n` +
                        `Transfer Amount: ${amount.toFixed(2)} USDT\n` +
                        `Penalty (20%): ${penalty.toFixed(2)} USDT\n` +
                        `You will receive: ${net.toFixed(2)} USDT\n\nDo you want to continue?`;
                }
                if (confirm(message)) { this.submit(); }
            });

            updateAvailableBalance();

            setTimeout(function() {
                document.querySelectorAll('.alert').forEach(function(alert) {
                    alert.style.opacity = '0';
                    setTimeout(function() { alert.remove(); }, 300);
                });
            }, 5000);
        </script>
    @endpush
@endsection
