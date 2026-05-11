@extends('member.layouts.app')
@section('content')
    <div class="scrollable-content">

        <!-- Header -->
        <div class="wd-header">
            <a href="{{ route('member.profile.index') }}" class="wd-back-btn">
                <i class="bi bi-chevron-left"></i>
            </a>
            <div class="wd-header-center">
                <h5 class="wd-title">{{ __('app.deposit') }} USDT</h5>
                <p class="wd-subtitle">{{ __('app.enter_amount_usdt') }}</p>
            </div>
            <a href="{{ route('member.deposit.history') }}" class="wd-hist-btn">
                <i class="bi bi-clock-history"></i>
            </a>
        </div>

        <!-- Step Indicator -->
        <div class="wd-section">
            <div class="dp-steps">
                <div class="dp-step active" id="step-1-indicator">
                    <div class="dp-step-circle">1</div>
                    <div class="dp-step-label">{{ __('app.amount') }}</div>
                </div>
                <div class="dp-step-line"></div>
                <div class="dp-step" id="step-2-indicator">
                    <div class="dp-step-circle">2</div>
                    <div class="dp-step-label">{{ __('app.payment') }}</div>
                </div>
            </div>
        </div>

        <!-- ─── STEP 1 ─── -->
        <div id="step-1" class="step-content active">

            <!-- Current Balance -->
            <div class="wd-section">
                <div class="wd-label">{{ __('app.current_balance') }}</div>
                <div class="dp-balance-val">{{ number_format($userBalance, 2) }} <span class="dp-balance-unit">USDT</span></div>
            </div>

            <!-- Amount Input -->
            <div class="wd-section">
                <div class="wd-label">{{ __('app.deposit_amount') }}</div>
                <div class="wd-qty-box">
                    <input type="number" id="deposit-amount" class="wd-qty-input"
                        placeholder="{{ __('app.enter_amount_manually') }}" value="" step="0.01" min="200">
                    <div class="wd-qty-right">
                        <span class="wd-usdt-lbl">USDT</span>
                    </div>
                </div>
                <div class="wd-avail">
                    <i class="bi bi-info-circle me-1"></i>{{ __('app.minimum_deposit_hint') }}
                </div>
            </div>

            <!-- Network Selection -->
            <div class="wd-section">
                <div class="wd-label">{{ __('app.select_network') }}</div>
                <div class="wd-net-row">
                    <div class="dp-net-option active" id="opt-trc20" onclick="selectWalletType('trc20')">
                        <input type="radio" name="wallet_type_display" id="wallet-trc20" value="trc20" checked class="d-none">
                        <span class="dp-net-name">TRC20</span>
                        <span class="dp-net-detail">{{ __('app.tron_network') }}</span>
                    </div>
                    <div class="dp-net-option" id="opt-bep20" onclick="selectWalletType('bep20')">
                        <input type="radio" name="wallet_type_display" id="wallet-bep20" value="bep20" class="d-none">
                        <span class="dp-net-name">BEP20</span>
                        <span class="dp-net-detail">{{ __('app.binance_smart_chain') }}</span>
                    </div>
                </div>
            </div>

            <div class="wd-footer">
                <button class="wd-submit-btn" type="button" onclick="goToStep2()">
                    {{ __('app.continue') }} <i class="bi bi-arrow-right ms-2"></i>
                </button>
            </div>
        </div>

        <!-- ─── STEP 2 ─── -->
        <div id="step-2" class="step-content">
            <form id="deposit-form" action="{{ route('member.deposit.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="amount" id="form-amount">
                <input type="hidden" name="wallet_type" id="form-wallet-type" value="trc20">

                <!-- Amount Summary -->
                <div class="wd-section">
                    <div class="wd-label">{{ __('app.deposit_amount') }}</div>
                    <div class="dp-balance-val" id="summary-amount">200.00 USDT</div>
                </div>

                <!-- Transfer Info -->
                <div class="wd-section">
                    <div class="wd-label">{{ __('app.transfer_usdt_to_ewallet') }}</div>

                    <div class="dp-info-row">
                        <span class="dp-info-label">{{ __('app.network') }}</span>
                        <div class="dp-info-val-wrap">
                            <span class="dp-info-val" id="display-network-name">{{ $walletTrc20['name'] }}</span>
                            <button type="button" class="dp-copy-btn" onclick="copyNetworkName()" title="{{ __('app.copy') }}">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                    </div>

                    <div class="dp-info-row">
                        <span class="dp-info-label">{{ __('app.deposit_address') }}</span>
                        <div class="dp-info-val-wrap">
                            <span class="dp-info-val dp-mono" id="display-wallet-address">{{ $walletTrc20['address'] }}</span>
                            <button type="button" class="dp-copy-btn" onclick="copyWalletAddress()" title="{{ __('app.copy') }}">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                    </div>

                    <div class="dp-alert-info mt-3">
                        <i class="bi bi-info-circle-fill me-2" style="flex-shrink:0;margin-top:2px;"></i>
                        <span class="small">{!! __('app.transfer_info', ['network' => '<span id="display-network-type">TRC20</span>']) !!}</span>
                    </div>
                </div>

                <!-- Upload Proof -->
                <div class="wd-section">
                    <div class="wd-label">{{ __('app.upload_proof_of_transfer') }}</div>
                    <div class="dp-upload-area" onclick="document.getElementById('file-upload').click()">
                        <input type="file" name="payment_proof" id="file-upload" accept="image/*"
                            style="display:none;" onchange="handleFileUpload(event)" required>
                        <div id="upload-placeholder">
                            <i class="bi bi-cloud-upload dp-upload-icon"></i>
                            <p class="mb-1" style="color:var(--text-primary);">{{ __('app.click_to_upload') }}</p>
                            <small class="text-muted">{{ __('app.file_size_limit') }}</small>
                        </div>
                        <div id="upload-preview" style="display:none;">
                            <img id="preview-image" src="" alt="Preview" class="dp-preview-img">
                            <p class="mb-0 mt-2" style="color:var(--text-primary);" id="file-name"></p>
                        </div>
                    </div>
                    @error('payment_proof')
                        <small class="text-danger mt-1 d-block">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="wd-footer">
                    <div class="row g-2">
                        <div class="col-5">
                            <button type="button" class="dp-back-btn" onclick="goToStep1()">
                                <i class="bi bi-arrow-left me-1"></i>{{ __('app.back') }}
                            </button>
                        </div>
                        <div class="col-7">
                            <button type="button" class="wd-submit-btn" onclick="submitDeposit()">
                                {{ __('app.submit') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>

    @push('styles')
        <style>
            /* ── Shared withdraw-style base ── */
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
            .wd-title  { color: #fff; font-size: 16px; font-weight: 700; margin: 0; }
            .wd-subtitle { color: var(--text-muted); font-size: 11px; margin: 3px 0 0; }
            .wd-hist-btn { color: var(--text-muted); font-size: 19px; flex-shrink: 0; text-decoration: none; }
            .wd-hist-btn:hover { color: var(--gold-color); }

            .wd-section { padding: 16px 20px; border-bottom: 1px solid var(--border-color); }
            .wd-label { color: var(--gold-color); font-size: 12px; font-weight: 700; margin-bottom: 12px; }

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
            .wd-qty-right { display: flex; align-items: center; gap: 8px; padding: 0 12px; flex-shrink: 0; }
            .wd-usdt-lbl { color: var(--text-muted); font-size: 13px; font-weight: 600; }
            .wd-avail { color: var(--text-muted); font-size: 12px; margin-top: 8px; }

            .wd-net-row { display: flex; gap: 10px; }

            .wd-footer { padding: 20px 20px 24px; }
            .wd-submit-btn {
                width: 100%; padding: 15px;
                background: linear-gradient(135deg, #22c55e, #16a34a);
                border: none; border-radius: 25px;
                color: #fff; font-size: 16px; font-weight: 700;
                cursor: pointer; transition: all 0.3s;
            }
            .wd-submit-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(34,197,94,0.35); }

            /* ── Step Indicator ── */
            .dp-steps { display: flex; align-items: center; justify-content: center; }
            .dp-step { display: flex; flex-direction: column; align-items: center; gap: 5px; }
            .dp-step-circle {
                width: 30px; height: 30px; border-radius: 50%;
                background: rgba(255,255,255,0.06); border: 2px solid var(--border-color);
                display: flex; align-items: center; justify-content: center;
                font-size: 12px; font-weight: 700; color: var(--text-muted);
                transition: all 0.3s;
            }
            .dp-step-label { font-size: 11px; color: var(--text-muted); font-weight: 600; white-space: nowrap; }
            .dp-step.active .dp-step-circle { background: var(--gold-color); border-color: var(--gold-color); color: #0a0f1e; }
            .dp-step.active .dp-step-label { color: var(--gold-color); }
            .dp-step.completed .dp-step-circle { background: rgba(0,229,255,0.1); border-color: var(--gold-color); color: var(--gold-color); }
            .dp-step-line { flex: 1; height: 2px; background: var(--border-color); margin: 0 12px; margin-bottom: 18px; }

            /* ── Balance display ── */
            .dp-balance-val { color: #fff; font-size: 24px; font-weight: 900; }
            .dp-balance-unit { color: var(--text-muted); font-size: 14px; font-weight: 600; }

            /* ── Network option (with sub-label) ── */
            .dp-net-option {
                flex: 1; padding: 12px 14px;
                background: rgba(255,255,255,0.04);
                border: 1px solid var(--border-color);
                border-radius: 8px; cursor: pointer; transition: all 0.2s;
                display: flex; flex-direction: column; gap: 3px;
            }
            .dp-net-option.active { background: rgba(0,229,255,0.08); border-color: var(--gold-color); }
            .dp-net-name { color: var(--text-muted); font-size: 14px; font-weight: 700; }
            .dp-net-option.active .dp-net-name { color: var(--gold-color); }
            .dp-net-detail { color: var(--text-muted); font-size: 11px; }

            /* ── Transfer info rows ── */
            .dp-info-row {
                display: flex; align-items: flex-start; justify-content: space-between;
                gap: 12px; padding: 12px 0;
                border-bottom: 1px solid var(--border-color);
            }
            .dp-info-row:last-of-type { border-bottom: none; }
            .dp-info-label { color: var(--text-muted); font-size: 12px; flex-shrink: 0; min-width: 90px; padding-top: 2px; }
            .dp-info-val-wrap { display: flex; align-items: center; gap: 8px; flex: 1; justify-content: flex-end; }
            .dp-info-val { color: #fff; font-size: 13px; font-weight: 600; text-align: right; word-break: break-all; line-height: 1.5; }
            .dp-mono { font-family: monospace; font-size: 12px; letter-spacing: 0.3px; }
            .dp-copy-btn {
                background: rgba(0,229,255,0.1); border: 1px solid rgba(0,229,255,0.2);
                border-radius: 6px; width: 30px; height: 30px;
                display: flex; align-items: center; justify-content: center;
                cursor: pointer; flex-shrink: 0; transition: all 0.2s;
            }
            .dp-copy-btn:hover { background: rgba(0,229,255,0.2); }
            .dp-copy-btn i { color: var(--gold-color); font-size: 13px; }

            .dp-alert-info {
                background: rgba(0,229,255,0.08); border: 1px solid rgba(0,229,255,0.2);
                border-radius: 8px; padding: 12px;
                display: flex; align-items: flex-start; color: var(--gold-color);
            }
            .dp-alert-info .small { line-height: 1.5; }

            /* ── Upload area ── */
            .dp-upload-area {
                border: 2px dashed var(--border-color); border-radius: 10px;
                padding: 28px 16px; text-align: center; cursor: pointer;
                transition: all 0.2s; background: rgba(0,229,255,0.02);
            }
            .dp-upload-area:hover { border-color: var(--gold-color); background: rgba(0,229,255,0.05); }
            .dp-upload-icon { font-size: 38px; color: var(--text-muted); opacity: 0.4; display: block; margin-bottom: 8px; }
            .dp-preview-img { max-width: 100%; max-height: 180px; border-radius: 8px; }

            /* ── Back button ── */
            .dp-back-btn {
                width: 100%; padding: 15px;
                background: rgba(255,255,255,0.06); border: 1px solid var(--border-color);
                border-radius: 25px; color: var(--text-primary);
                font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s;
            }
            .dp-back-btn:hover { background: rgba(255,255,255,0.1); }

            /* ── Step content ── */
            .step-content { display: none; }
            .step-content.active { display: block; }

            @media (max-width: 480px) {
                .dp-info-row { flex-wrap: wrap; }
                .dp-info-val-wrap { width: 100%; justify-content: space-between; }
            }
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
                minimumDepositAlert:    "{{ __('app.minimum_deposit_alert') }}",
                pleaseUploadProof:      "{{ __('app.please_upload_proof') }}",
                fileSizeExceeded:       "{{ __('app.file_size_exceeded') }}",
                fileTypeNotAllowed:     "{{ __('app.file_type_not_allowed') }}",
                depositConfirmation:    "{{ __('app.deposit_confirmation') }}",
                copied:                 "{{ __('app.copied') }}"
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
                document.querySelectorAll('.dp-net-option').forEach(el => el.classList.remove('active'));
                document.getElementById('opt-' + type).classList.add('active');
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

            function copyNetworkName()  { copyText(document.getElementById('display-network-name').textContent); }
            function copyWalletAddress() { copyText(document.getElementById('display-wallet-address').textContent); }
            function copyText(text) {
                navigator.clipboard.writeText(text).then(() => { alert(translations.copied + ': ' + text); });
            }

            function handleFileUpload(event) {
                const file = event.target.files[0];
                if (!file) return;
                if (file.size > 5 * 1024 * 1024) { alert(translations.fileSizeExceeded); event.target.value = ''; return; }
                const allowed = ['image/jpeg', 'image/png', 'image/jpg'];
                if (!allowed.includes(file.type)) { alert(translations.fileTypeNotAllowed); event.target.value = ''; return; }
                selectedFile = file;
                const reader = new FileReader();
                reader.onload = function (e) {
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
