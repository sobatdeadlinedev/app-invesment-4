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
                    <h5 class="pg-title">{{ __('app.deposit') }}</h5>
                </div>
                <a href="{{ route('member.deposit.history') }}" class="pg-header-action">
                    <i class="bi bi-clock-history"></i> {{ __('app.history') }}
                </a>
            </div>

            <!-- Step Indicator -->
            <div class="form-block">
                <div class="step-indicator">
                    <div class="step-item active" id="step-1-indicator">
                        <div class="step-circle">1</div>
                        <div class="step-label">{{ __('app.amount') }}</div>
                    </div>
                    <div class="step-line"></div>
                    <div class="step-item" id="step-2-indicator">
                        <div class="step-circle">2</div>
                        <div class="step-label">{{ __('app.payment') }}</div>
                    </div>
                </div>
            </div>

            <!-- Step 1: Amount -->
            <div id="step-1" class="step-content active">

                <!-- Current Balance -->
                <div class="balance-hero">
                    <div>
                        <p class="text-muted mb-1 small">{{ __('app.current_balance') }}</p>
                        <h5 class="text-gold mb-0 fw-bold">{{ number_format($userBalance, 2) }} USDT</h5>
                    </div>
                    <div class="balance-hero-icon">
                        <i class="bi bi-wallet2"></i>
                    </div>
                </div>

                <div class="w-card">
                    <div class="w-card-head"><i class="bi bi-currency-dollar"></i>{{ __('app.deposit_amount') }}</div>
                    <div class="form-block">
                        <div class="mb-2">
                            <label class="text-muted small mb-2 d-block">{{ __('app.enter_amount_usdt') }}</label>
                            <div class="input-with-icon">
                                <span class="input-icon">₮</span>
                                <input type="number" id="deposit-amount" class="form-control-dark with-icon"
                                    placeholder="{{ __('app.enter_amount_manually') }}" value="" step="0.01" min="200">
                            </div>
                        </div>
                        <small class="text-muted d-block mt-1">
                            <i class="bi bi-info-circle me-1"></i>{{ __('app.minimum_deposit_hint') }}
                        </small>
                    </div>
                </div>

                <div class="w-card">
                    <div class="w-card-head"><i class="bi bi-hdd-network"></i>{{ __('app.select_network') }}</div>
                    <div class="form-block">
                        <div class="wallet-type-selection">
                            <div class="wallet-type-option" onclick="selectWalletType('trc20')">
                                <input type="radio" name="wallet_type_display" id="wallet-trc20" value="trc20" checked>
                                <label for="wallet-trc20" class="wallet-type-label">
                                    <div class="wallet-type-header">
                                        <i class="bi bi-circle-fill me-2"></i>
                                        <span class="fw-bold">TRC20</span>
                                    </div>
                                    <div class="wallet-type-details">
                                        <small class="text-muted">{{ __('app.tron_network') }}</small>
                                    </div>
                                </label>
                            </div>
                            <div class="wallet-type-option" onclick="selectWalletType('bep20')">
                                <input type="radio" name="wallet_type_display" id="wallet-bep20" value="bep20">
                                <label for="wallet-bep20" class="wallet-type-label">
                                    <div class="wallet-type-header">
                                        <i class="bi bi-circle-fill me-2"></i>
                                        <span class="fw-bold">BEP20</span>
                                    </div>
                                    <div class="wallet-type-details">
                                        <small class="text-muted">{{ __('app.binance_smart_chain') }}</small>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Continue Button -->
                <div class="w-page-footer">
                    <button class="btn-cta" type="button" onclick="goToStep2()">
                        {{ __('app.continue') }} <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>

            <!-- Step 2: Payment Method -->
            <div id="step-2" class="step-content">
                <form id="deposit-form" action="{{ route('member.deposit.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="amount" id="form-amount">
                    <input type="hidden" name="wallet_type" id="form-wallet-type" value="trc20">

                    <!-- Amount Summary -->
                    <div class="balance-hero">
                        <p class="text-muted mb-0">{{ __('app.deposit_amount') }}</p>
                        <h5 class="text-gold mb-0 fw-bold" id="summary-amount">200.00 USDT</h5>
                    </div>

                    <div class="w-card">
                        <div class="w-card-head"><i class="bi bi-arrow-up-circle"></i>{{ __('app.transfer_usdt_to_ewallet') }}</div>
                        <div class="form-block">
                            <div class="payment-info-item">
                                <div class="payment-info-row">
                                    <span class="text-muted small payment-label">{{ __('app.network') }}</span>
                                    <div class="payment-value-with-copy">
                                        <span class="fw-bold payment-value-text" style="color: var(--text-primary);"
                                            id="display-network-name">{{ $walletTrc20['name'] }}</span>
                                        <button type="button" class="btn-copy-mini" onclick="copyNetworkName()"
                                            title="{{ __('app.copy') }}">
                                            <i class="bi bi-clipboard"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="payment-info-item">
                                <div class="payment-info-row">
                                    <span class="text-muted small payment-label">{{ __('app.deposit_address') }}</span>
                                    <div class="payment-value-with-copy">
                                        <span class="fw-bold payment-value-text wallet-address"
                                            style="color: var(--text-primary);"
                                            id="display-wallet-address">{{ $walletTrc20['address'] }}</span>
                                        <button type="button" class="btn-copy-mini" onclick="copyWalletAddress()"
                                            title="{{ __('app.copy') }}">
                                            <i class="bi bi-clipboard"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="alert-info-box mt-3">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                <span class="small">{!! __('app.transfer_info', ['network' => '<span id="display-network-type">TRC20</span>']) !!}</span>
                            </div>
                        </div>
                    </div>

                    <div class="w-card">
                        <div class="w-card-head"><i class="bi bi-cloud-upload"></i>{{ __('app.upload_proof_of_transfer') }}</div>
                        <div class="form-block">
                            <div class="upload-area" onclick="document.getElementById('file-upload').click()">
                                <input type="file" name="payment_proof" id="file-upload" accept="image/*"
                                    style="display: none;" onchange="handleFileUpload(event)" required>
                                <div id="upload-placeholder">
                                    <i class="bi bi-cloud-upload upload-icon"></i>
                                    <p class="mb-1" style="color: var(--text-primary);">{{ __('app.click_to_upload') }}</p>
                                    <small class="text-muted">{{ __('app.file_size_limit') }}</small>
                                </div>
                                <div id="upload-preview" style="display: none;">
                                    <img id="preview-image" src="" alt="Preview" class="preview-image">
                                    <p class="mb-0 mt-2" style="color: var(--text-primary);" id="file-name"></p>
                                </div>
                            </div>
                            @error('payment_proof')
                                <small class="text-danger mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="w-page-footer">
                        <div class="row g-2">
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-gold w-100" onclick="goToStep1()">
                                    <i class="bi bi-arrow-left me-2"></i>{{ __('app.back') }}
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn-cta" onclick="submitDeposit()">
                                    {{ __('app.submit') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>

    @push('styles')
        <style>
            /* Step Indicator */
            .step-indicator {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0;
            }
            .step-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 6px;
            }
            .step-circle {
                width: 32px; height: 32px;
                border-radius: 50%;
                background: rgba(0,229,255,0.1);
                border: 2px solid var(--border-color);
                display: flex; align-items: center; justify-content: center;
                font-size: 13px; font-weight: 700;
                color: var(--text-muted);
                transition: all 0.3s ease;
            }
            .step-label {
                font-size: 11px; color: var(--text-muted);
                font-weight: 600; white-space: nowrap;
            }
            .step-item.active .step-circle {
                background: var(--gold-color);
                border-color: var(--gold-color);
                color: #0a0f1e;
            }
            .step-item.active .step-label { color: var(--gold-color); }
            .step-item.completed .step-circle {
                background: rgba(0,229,255,0.15);
                border-color: var(--gold-color);
                color: var(--gold-color);
            }
            .step-line {
                flex: 1; height: 2px;
                background: var(--border-color);
                margin: 0 12px;
                margin-bottom: 20px;
            }

            /* Wallet Type Selection */
            .wallet-type-selection { display: flex; flex-direction: column; gap: 12px; }
            .wallet-type-option { position: relative; cursor: pointer; }
            .wallet-type-option input[type="radio"] { position: absolute; opacity: 0; pointer-events: none; }
            .wallet-type-label {
                display: block; padding: 16px;
                background: rgba(0, 229, 255, 0.05);
                border: 2px solid var(--border-color);
                border-radius: 12px; cursor: pointer;
                transition: all 0.2s ease; margin: 0;
            }
            .wallet-type-option input[type="radio"]:checked+.wallet-type-label {
                background: rgba(0, 229, 255, 0.1);
                border-color: var(--gold-color);
            }
            .wallet-type-label:hover {
                background: rgba(0, 229, 255, 0.08);
                border-color: rgba(0, 229, 255, 0.5);
            }
            .wallet-type-header { display: flex; align-items: center; color: var(--text-primary); margin-bottom: 4px; }
            .wallet-type-header i { font-size: 10px; color: var(--text-muted); transition: color 0.2s ease; }
            .wallet-type-option input[type="radio"]:checked+.wallet-type-label .wallet-type-header i { color: var(--gold-color); }
            .wallet-type-details { padding-left: 18px; }

            /* Payment Info */
            .payment-info-item { padding: 12px 0; }
            .payment-info-item:not(:last-child) { border-bottom: 1px solid var(--border-color); }
            .payment-info-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 15px; flex-wrap: wrap; }
            .payment-label { flex-shrink: 0; min-width: 100px; }
            .payment-value-with-copy { display: flex; align-items: center; gap: 8px; flex: 1; justify-content: flex-end; }
            .payment-value-text { font-size: 14px; line-height: 1.5; word-break: break-all; text-align: right; }
            .wallet-address { font-family: 'Courier New', Courier, monospace; font-size: 13px; letter-spacing: 0.5px; }

            @media (max-width: 576px) {
                .payment-info-row { flex-direction: column; gap: 8px; align-items: flex-start; }
                .payment-label { min-width: auto; }
                .payment-value-with-copy { width: 100%; justify-content: space-between; }
                .payment-value-text { text-align: left; font-size: 12px; flex: 1; word-break: break-all; }
                .wallet-address { font-size: 11px; }
            }

            /* Copy button */
            .btn-copy-mini {
                background: rgba(0, 229, 255, 0.15); border: 1px solid rgba(0, 229, 255, 0.3);
                border-radius: 6px; width: 32px; height: 32px;
                display: flex; align-items: center; justify-content: center;
                transition: all 0.2s ease; cursor: pointer; padding: 0; flex-shrink: 0;
            }
            .btn-copy-mini:hover { background: rgba(0, 229, 255, 0.25); border-color: rgba(0, 229, 255, 0.5); }
            .btn-copy-mini i { color: var(--gold-color); font-size: 14px; }

            /* Alert info box */
            .alert-info-box {
                background: rgba(0, 229, 255, 0.1); border: 1px solid rgba(0, 229, 255, 0.3);
                border-radius: 8px; padding: 12px;
                display: flex; align-items: flex-start; color: var(--gold-color);
            }
            .alert-info-box i { flex-shrink: 0; margin-top: 2px; }
            .alert-info-box .small { line-height: 1.5; }

            /* Upload area */
            .upload-area {
                border: 2px dashed var(--border-color); border-radius: 12px;
                padding: 32px 20px; text-align: center; cursor: pointer;
                transition: all 0.2s ease; background: rgba(0,229,255,0.03);
            }
            .upload-area:hover { border-color: var(--gold-color); background: rgba(0,229,255,0.05); }
            .upload-icon { font-size: 42px; color: var(--text-muted); opacity: 0.4; display: block; margin-bottom: 8px; }
            .preview-image { max-width: 100%; max-height: 200px; border-radius: 8px; }

            /* Step content */
            .step-content { display: none; }
            .step-content.active { display: block; }
        </style>
    @endpush

    @push('scripts')
        <script>
            let selectedFile = null;
            let selectedWalletType = 'trc20';
            const MIN_DEPOSIT = 200;

            const walletData = {
                trc20: { name: '{{ $walletTrc20['name'] }}', address: '{{ $walletTrc20['address'] }}' },
                bep20: { name: '{{ $walletBep20['name'] }}', address: '{{ $walletBep20['address'] }}' }
            };

            const translations = {
                pleaseEnterValidAmount: "{{ __('app.please_enter_valid_amount') }}",
                minimumDepositAlert: "{{ __('app.minimum_deposit_alert') }}",
                pleaseUploadProof: "{{ __('app.please_upload_proof') }}",
                fileSizeExceeded: "{{ __('app.file_size_exceeded') }}",
                fileTypeNotAllowed: "{{ __('app.file_type_not_allowed') }}",
                depositConfirmation: "{{ __('app.deposit_confirmation') }}",
                copied: "{{ __('app.copied') }}"
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

            function setAmount(amount) {
                document.getElementById('deposit-amount').value = amount;
            }

            function selectWalletType(type) {
                selectedWalletType = type;
                document.getElementById('wallet-' + type).checked = true;
            }

            function goToStep2() {
                const amount = parseFloat(document.getElementById('deposit-amount').value);
                if (!amount || amount <= 0) { alert(translations.pleaseEnterValidAmount); return; }
                if (amount < MIN_DEPOSIT) { alert(translations.minimumDepositAlert); return; }

                const walletType = document.querySelector('input[name="wallet_type_display"]:checked').value;
                document.getElementById('summary-amount').textContent = amount.toFixed(2) + ' USDT';
                document.getElementById('form-amount').value = amount;
                document.getElementById('form-wallet-type').value = walletType;

                const wallet = walletData[walletType];
                document.getElementById('display-network-name').textContent = wallet.name;
                document.getElementById('display-wallet-address').textContent = wallet.address;
                document.getElementById('display-network-type').textContent = walletType.toUpperCase();

                document.getElementById('step-1').classList.remove('active');
                document.getElementById('step-2').classList.add('active');
                document.getElementById('step-1-indicator').classList.remove('active');
                document.getElementById('step-1-indicator').classList.add('completed');
                document.getElementById('step-2-indicator').classList.add('active');
                document.querySelector('.scrollable-content').scrollTop = 0;
            }

            function goToStep1() {
                document.getElementById('step-2').classList.remove('active');
                document.getElementById('step-1').classList.add('active');
                document.getElementById('step-2-indicator').classList.remove('active');
                document.getElementById('step-1-indicator').classList.add('active');
                document.getElementById('step-1-indicator').classList.remove('completed');
                document.querySelector('.scrollable-content').scrollTop = 0;
            }

            function copyNetworkName() { copyText(document.getElementById('display-network-name').textContent); }
            function copyWalletAddress() { copyText(document.getElementById('display-wallet-address').textContent); }
            function copyText(text) {
                navigator.clipboard.writeText(text).then(() => { alert(translations.copied + ': ' + text); });
            }

            function handleFileUpload(event) {
                const file = event.target.files[0];
                if (!file) return;
                if (file.size > 5 * 1024 * 1024) { alert(translations.fileSizeExceeded); event.target.value = ''; return; }
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                if (!allowedTypes.includes(file.type)) { alert(translations.fileTypeNotAllowed); event.target.value = ''; return; }
                selectedFile = file;
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-image').src = e.target.result;
                    document.getElementById('file-name').textContent = file.name;
                    document.getElementById('upload-placeholder').style.display = 'none';
                    document.getElementById('upload-preview').style.display = 'block';
                };
                reader.readAsDataURL(file);
            }

            function submitDeposit() {
                const fileInput = document.getElementById('file-upload');
                if (!fileInput.files || !fileInput.files[0]) { alert(translations.pleaseUploadProof); return; }
                if (confirm(translations.depositConfirmation)) {
                    document.getElementById('deposit-form').submit();
                }
            }
        </script>
    @endpush
@endsection
