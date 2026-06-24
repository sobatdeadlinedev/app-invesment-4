@extends('member.layouts.app')
@section('content')
<div class="scrollable-content">

    <!-- Header -->
    <div class="dp-header">
        <a href="{{ route('member.profile.index') }}" class="dp-back-btn">
            <i class="bi bi-chevron-left"></i>
        </a>
        <div class="dp-header-center">
            <h5 class="dp-title">{{ __('app.deposit') }} USDT</h5>
            <p class="dp-subtitle">{{ __('app.enter_amount_usdt') }}</p>
        </div>
        <a href="{{ route('member.deposit.history') }}" class="dp-hist-btn">
            <i class="bi bi-clock-history"></i>
        </a>
    </div>

    @if ($pendingDeposit)
    {{-- ══════════════════════════════════════════════════════
         ADA DEPOSIT PENDING → tampilkan info, kunci form baru
    ══════════════════════════════════════════════════════ --}}

    <div class="dp-pending-wrap">
        <div class="dp-pending-icon">
            <i class="bi bi-hourglass-split"></i>
        </div>
        <div class="dp-pending-body">
            <div class="dp-pending-title">Deposit Sedang Diproses</div>
            <div class="dp-pending-sub">Tunggu hingga deposit sebelumnya selesai sebelum membuat deposit baru.</div>
        </div>
    </div>

    <!-- Detail deposit pending -->
    <div class="dp-section">
        <div class="dp-label">Detail Deposit Aktif</div>

        <div class="dp-info-row">
            <span class="dp-info-label">Referensi</span>
            <div class="dp-info-val-wrap">
                <span class="dp-info-val dp-mono">{{ $pendingDeposit->reference }}</span>
                <button type="button" class="dp-copy-btn" onclick="copyRaw('{{ $pendingDeposit->reference }}', 'copy-ref-icon')" title="Salin">
                    <i class="bi bi-clipboard" id="copy-ref-icon"></i>
                </button>
            </div>
        </div>

        <div class="dp-info-row">
            <span class="dp-info-label">Jumlah</span>
            <div class="dp-info-val-wrap">
                <span class="dp-info-val">{{ number_format($pendingDeposit->amount, 2) }} USDT</span>
            </div>
        </div>

        <div class="dp-info-row">
            <span class="dp-info-label">Network</span>
            <div class="dp-info-val-wrap">
                <span class="dp-info-val">{{ $pendingDeposit->payment_method }}</span>
            </div>
        </div>

        @if ($pendingDeposit->wallet_address)
        <div class="dp-info-row">
            <span class="dp-info-label">Alamat Tujuan</span>
            <div class="dp-info-val-wrap">
                <span class="dp-info-val dp-mono">{{ $pendingDeposit->wallet_address }}</span>
                <button type="button" class="dp-copy-btn" onclick="copyRaw('{{ $pendingDeposit->wallet_address }}', 'copy-addr-icon')" title="Salin">
                    <i class="bi bi-clipboard" id="copy-addr-icon"></i>
                </button>
            </div>
        </div>
        @endif

        <div class="dp-info-row">
            <span class="dp-info-label">Waktu</span>
            <div class="dp-info-val-wrap">
                <span class="dp-info-val">{{ $pendingDeposit->created_at->format('d M Y H:i') }}</span>
            </div>
        </div>

        <div class="dp-info-row" style="border-bottom:none;">
            <span class="dp-info-label">Status</span>
            <div class="dp-info-val-wrap">
                <span class="dp-badge-pending">
                    <i class="bi bi-clock me-1"></i>Pending
                </span>
            </div>
        </div>
    </div>

    <!-- Tombol ke riwayat -->
    <div class="dp-footer">
        <a href="{{ route('member.deposit.history') }}" class="dp-submit-btn dp-hist-link">
            <i class="bi bi-clock-history me-2"></i>Lihat Riwayat Deposit
        </a>
    </div>

    @else
    {{-- ══════════════════════════════════════════════════════
         TIDAK ADA PENDING → form deposit normal
    ══════════════════════════════════════════════════════ --}}

    <form id="deposit-form" action="{{ route('member.deposit.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="amount" id="form-amount">
        <input type="hidden" name="wallet_type" id="form-wallet-type" value="trc20">
        <input type="hidden" name="wallet_address" id="form-wallet-address" value="{{ $walletTrc20['address'] }}">

        <!-- Amount -->
        <div class="dp-section">
            <div class="dp-label">{{ __('app.deposit_amount') }}</div>
            <div class="dp-qty-box">
                <input type="number" id="deposit-amount" class="dp-qty-input"
                    placeholder="{{ __('app.enter_amount_manually') }}"
                    step="0.01" min="200">
                <div class="dp-qty-right">
                    <span class="dp-usdt-lbl">USDT</span>
                </div>
            </div>
            <div class="dp-hint">
                <i class="bi bi-info-circle me-1"></i>{{ __('app.minimum_deposit_hint') }}
            </div>
        </div>

        <!-- Network Selection -->
        <div class="dp-section">
            <div class="dp-label">{{ __('app.select_network') }}</div>
            <div class="dp-net-row">
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

        <!-- Transfer Info -->
        <div class="dp-section">
            <div class="dp-label">{{ __('app.transfer_usdt_to_ewallet') }}</div>

            <div class="dp-info-row">
                <span class="dp-info-label">{{ __('app.network') }}</span>
                <div class="dp-info-val-wrap">
                    <span class="dp-info-val" id="display-network-name">{{ $walletTrc20['name'] }}</span>
                    <button type="button" class="dp-copy-btn" onclick="copyText('network')" title="{{ __('app.copy') }}">
                        <i class="bi bi-clipboard" id="copy-network-icon"></i>
                    </button>
                </div>
            </div>

            <div class="dp-info-row">
                <span class="dp-info-label">{{ __('app.deposit_address') }}</span>
                <div class="dp-info-val-wrap">
                    <span class="dp-info-val dp-mono" id="display-wallet-address">{{ $walletTrc20['address'] }}</span>
                    <button type="button" class="dp-copy-btn" onclick="copyText('address')" title="{{ __('app.copy') }}">
                        <i class="bi bi-clipboard" id="copy-address-icon"></i>
                    </button>
                </div>
            </div>

            <div class="dp-alert-info mt-3">
                <i class="bi bi-info-circle-fill me-2" style="flex-shrink:0;margin-top:2px;"></i>
                <span class="small">{!! __('app.transfer_info', ['network' => '<span id="display-network-type">TRC20</span>']) !!}</span>
            </div>
        </div>

        <!-- Upload Proof -->
        <div class="dp-section">
            <div class="dp-label">{{ __('app.upload_proof_of_transfer') }}</div>
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

        <!-- Submit -->
        <div class="dp-footer">
            <button type="button" class="dp-submit-btn" onclick="submitDeposit()">
                {{ __('app.submit') }}
            </button>
        </div>

    </form>
    @endif

</div>

@push('styles')
<style>
/* Header */
.dp-header {
    display: flex; align-items: center;
    padding: 16px 20px;
    border-bottom: 1px solid var(--border-color);
}
.dp-back-btn {
    width: 36px; height: 36px;
    background: rgba(255,255,255,0.06); border: 1px solid var(--border-color);
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    color: var(--text-primary); text-decoration: none; font-size: 16px; flex-shrink: 0;
}
.dp-header-center { flex: 1; text-align: center; padding: 0 10px; }
.dp-title    { color: #fff; font-size: 16px; font-weight: 700; margin: 0; }
.dp-subtitle { color: var(--text-muted); font-size: 11px; margin: 3px 0 0; }
.dp-hist-btn { color: var(--text-muted); font-size: 19px; flex-shrink: 0; text-decoration: none; }
.dp-hist-btn:hover { color: var(--gold-color); }

/* Pending banner */
.dp-pending-wrap {
    display: flex; align-items: flex-start; gap: 14px;
    margin: 16px 20px 0;
    background: rgba(234,179,8,0.08);
    border: 1px solid rgba(234,179,8,0.25);
    border-radius: 12px; padding: 14px 16px;
}
.dp-pending-icon {
    width: 40px; height: 40px; flex-shrink: 0;
    background: rgba(234,179,8,0.15); border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: #eab308; font-size: 18px;
}
.dp-pending-title { color: #eab308; font-size: 14px; font-weight: 700; margin-bottom: 3px; }
.dp-pending-sub   { color: var(--text-muted); font-size: 12px; line-height: 1.5; }

/* Badge pending */
.dp-badge-pending {
    display: inline-flex; align-items: center;
    background: rgba(234,179,8,0.12); border: 1px solid rgba(234,179,8,0.3);
    color: #eab308; font-size: 12px; font-weight: 600;
    padding: 4px 10px; border-radius: 20px;
}

/* History link styled as button */
.dp-hist-link {
    display: flex; align-items: center; justify-content: center;
    text-decoration: none;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8) !important;
}
.dp-hist-link:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(59,130,246,0.35) !important; }

/* Sections */
.dp-section { padding: 16px 20px; border-bottom: 1px solid var(--border-color); }
.dp-label { color: var(--gold-color); font-size: 12px; font-weight: 700; margin-bottom: 12px; }

/* Amount input */
.dp-qty-box {
    display: flex; align-items: center;
    border: 1px solid var(--border-color); border-radius: 8px;
    background: rgba(255,255,255,0.03);
}
.dp-qty-input {
    flex: 1; background: transparent; border: none; outline: none;
    color: #fff; font-size: 15px; padding: 13px 14px; min-width: 0;
}
.dp-qty-input::placeholder { color: var(--text-muted); font-size: 13px; }
.dp-qty-input::-webkit-outer-spin-button,
.dp-qty-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
.dp-qty-right { display: flex; align-items: center; padding: 0 12px; }
.dp-usdt-lbl { color: var(--text-muted); font-size: 13px; font-weight: 600; }
.dp-hint { color: var(--text-muted); font-size: 12px; margin-top: 8px; }

/* Network buttons */
.dp-net-row { display: flex; gap: 10px; }
.dp-net-option {
    flex: 1; padding: 12px 14px;
    background: rgba(255,255,255,0.04); border: 1px solid var(--border-color);
    border-radius: 8px; cursor: pointer; transition: all 0.2s;
    display: flex; flex-direction: column; gap: 3px;
}
.dp-net-option.active { background: rgba(0,229,255,0.08); border-color: var(--gold-color); }
.dp-net-name { color: var(--text-muted); font-size: 14px; font-weight: 700; }
.dp-net-option.active .dp-net-name { color: var(--gold-color); }
.dp-net-detail { color: var(--text-muted); font-size: 11px; }

/* Transfer info rows */
.dp-info-row {
    display: flex; align-items: flex-start; justify-content: space-between;
    gap: 12px; padding: 12px 0;
    border-bottom: 1px solid var(--border-color);
}
.dp-info-row:last-of-type { border-bottom: none; }
.dp-info-label { color: var(--text-muted); font-size: 12px; flex-shrink: 0; min-width: 100px; padding-top: 2px; }
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

/* Upload */
.dp-upload-area {
    border: 2px dashed var(--border-color); border-radius: 10px;
    padding: 28px 16px; text-align: center; cursor: pointer;
    transition: all 0.2s; background: rgba(0,229,255,0.02);
}
.dp-upload-area:hover { border-color: var(--gold-color); background: rgba(0,229,255,0.05); }
.dp-upload-icon { font-size: 38px; color: var(--text-muted); opacity: 0.4; display: block; margin-bottom: 8px; }
.dp-preview-img { max-width: 100%; max-height: 180px; border-radius: 8px; }

/* Submit */
.dp-footer { padding: 20px 20px 24px; }
.dp-submit-btn {
    width: 100%; padding: 15px;
    background: linear-gradient(135deg, #22c55e, #16a34a);
    border: none; border-radius: 25px;
    color: #fff; font-size: 16px; font-weight: 700;
    cursor: pointer; transition: all 0.3s;
}
.dp-submit-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(34,197,94,0.35); }

@media (max-width: 480px) {
    .dp-info-row { flex-wrap: wrap; }
    .dp-info-val-wrap { width: 100%; justify-content: space-between; }
}
</style>
@endpush

@push('scripts')
<script>
// Copy universal — dipakai di halaman pending maupun form normal
function copyRaw(text, iconId) {
    navigator.clipboard.writeText(text).then(function () {
        var icon = document.getElementById(iconId);
        if (icon) {
            icon.className = 'bi bi-check-lg';
            setTimeout(function () { icon.className = 'bi bi-clipboard'; }, 2000);
        }
    });
}

@if (!$pendingDeposit)
// ─── Data wallet dari server (fresh random karena tidak ada pending) ──────────
const walletData = {
    trc20: { name: '{{ $walletTrc20['name'] }}', address: '{{ $walletTrc20['address'] }}' },
    bep20: { name: '{{ $walletBep20['name'] }}', address: '{{ $walletBep20['address'] }}' }
};

let selectedWalletType = 'trc20';
const MIN_DEPOSIT = 200;

const translations = {
    pleaseEnterValidAmount: "{{ __('app.please_enter_valid_amount') }}",
    minimumDepositAlert:    "{{ __('app.minimum_deposit_alert') }}",
    pleaseUploadProof:      "{{ __('app.please_upload_proof') }}",
    fileSizeExceeded:       "{{ __('app.file_size_exceeded') }}",
    fileTypeNotAllowed:     "{{ __('app.file_type_not_allowed') }}",
    depositConfirmation:    "{{ __('app.deposit_confirmation') }}",
};

function selectWalletType(type) {
    selectedWalletType = type;
    document.getElementById('wallet-' + type).checked = true;
    document.querySelectorAll('.dp-net-option').forEach(function (el) {
        el.classList.remove('active');
    });
    document.getElementById('opt-' + type).classList.add('active');

    var wallet = walletData[type];
    document.getElementById('display-network-name').textContent   = wallet.name;
    document.getElementById('display-wallet-address').textContent = wallet.address;
    document.getElementById('display-network-type').textContent   = type.toUpperCase();

    // Update hidden input → wallet_address yang akan disimpan ke DB
    document.getElementById('form-wallet-address').value = wallet.address;
}

function copyText(target) {
    var text = target === 'network'
        ? document.getElementById('display-network-name').textContent
        : document.getElementById('display-wallet-address').textContent;
    var iconId = target === 'network' ? 'copy-network-icon' : 'copy-address-icon';
    copyRaw(text, iconId);
}

function handleFileUpload(event) {
    var file = event.target.files[0];
    if (!file) return;
    if (file.size > 5 * 1024 * 1024) {
        alert(translations.fileSizeExceeded);
        event.target.value = '';
        return;
    }
    var allowed = ['image/jpeg', 'image/png', 'image/jpg'];
    if (!allowed.includes(file.type)) {
        alert(translations.fileTypeNotAllowed);
        event.target.value = '';
        return;
    }
    var reader = new FileReader();
    reader.onload = function (e) {
        document.getElementById('preview-image').src                    = e.target.result;
        document.getElementById('file-name').textContent                = file.name;
        document.getElementById('upload-placeholder').style.display     = 'none';
        document.getElementById('upload-preview').style.display         = 'block';
    };
    reader.readAsDataURL(file);
}

function submitDeposit() {
    var amount = parseFloat(document.getElementById('deposit-amount').value);
    if (!amount || amount <= 0) { alert(translations.pleaseEnterValidAmount); return; }
    if (amount < MIN_DEPOSIT)   { alert(translations.minimumDepositAlert); return; }

    var fileInput = document.getElementById('file-upload');
    if (!fileInput.files || !fileInput.files[0]) { alert(translations.pleaseUploadProof); return; }

    document.getElementById('form-amount').value      = amount;
    document.getElementById('form-wallet-type').value = selectedWalletType;
    // form-wallet-address sudah di-update oleh selectWalletType()

    if (confirm(translations.depositConfirmation)) {
        document.getElementById('deposit-form').submit();
    }
}

document.addEventListener('DOMContentLoaded', function () {
    selectWalletType('trc20');
});
@endif

@if (session('success')) alert('{{ session('success') }}'); @endif
@if (session('error'))   alert('{{ session('error') }}'); @endif
@if ($errors->any())     alert('{{ $errors->first() }}'); @endif
</script>
@endpush
@endsection