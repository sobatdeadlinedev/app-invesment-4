@extends('member.layouts.app')
@section('content')
<div class="scrollable-content">

    <!-- Header -->
    <div class="tm-header">
        <a href="{{ route('member.access.index') }}" class="tm-back-btn">
            <i class="bi bi-chevron-left"></i>
        </a>
        <div class="tm-header-center">
            <h5 class="tm-title">{{ __('app.invite_friends') }}</h5>
            <p class="tm-subtitle">{{ __('app.earn_commission') }}</p>
        </div>
        <div style="width:36px;"></div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mx-3 mt-3" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Stats -->
    <div class="tm-pad">
        <div class="tm-stats-row">
            <div class="tm-stat">
                <div class="tm-stat-val green">{{ $directTeam }} / {{ $totalTeam }}</div>
                <div class="tm-stat-lbl">{{ __('app.recommended_number') }}</div>
            </div>
            <div class="tm-stat-sep"></div>
            <div class="tm-stat">
                <div class="tm-stat-val gold">{{ number_format($totalRevenue ?? 0, 2) }}</div>
                <div class="tm-stat-lbl">{{ __('app.total_revenue') }}</div>
            </div>
        </div>
    </div>

    <!-- QR Code -->
    <div class="tm-pad">
        <div class="tm-sec-label"><i class="bi bi-qr-code me-1"></i>{{ __('app.my_qr_code') }}</div>
        <div class="tm-qr-area">
            <div class="tm-qr-wrap">
                <div id="qrcode"></div>
            </div>
            @if ($user->level)
                <span class="tm-level-badge">Level {{ $user->level }}</span>
            @endif
            <button class="tm-save-btn" onclick="saveQRCode()">
                <i class="bi bi-download me-2"></i>{{ __('app.save_qr') }}
            </button>
        </div>
    </div>

    <!-- Invitation Code & Link -->
    <div class="tm-pad">
        <div class="tm-sec-label"><i class="bi bi-share me-1"></i>{{ __('app.my_invitation_code') }}</div>

        <div class="tm-field-lbl">{{ __('app.my_invitation_code') }}</div>
        <div class="tm-copy-row mb-3">
            <span id="invitationCode">{{ $user->refferal_code }}</span>
            <button class="tm-copy-btn" onclick="copyInvitationCode()" title="{{ __('app.copy_code') }}">
                <i class="bi bi-clipboard" id="copyCodeIcon"></i>
            </button>
        </div>

        <div class="tm-field-lbl">{{ __('app.my_invitation_link') }}</div>
        <div class="tm-copy-row">
            <span id="invitationLink" class="tm-link-mono">{{ $referralLink }}</span>
            <button class="tm-copy-btn" onclick="copyInvitationLink()" title="{{ __('app.copy_link') }}">
                <i class="bi bi-link-45deg" id="copyLinkIcon"></i>
            </button>
        </div>
    </div>

    <!-- Rules -->
    <div class="tm-pad" style="padding-bottom: 24px;">
        <div class="tm-sec-label"><i class="bi bi-info-circle me-1"></i>{{ __('app.rules') }}</div>
        <ul class="tm-rules-list">
            <li>{{ __('app.rule_share_code') }}</li>
            <li>{{ __('app.rule_earn_commission') }}</li>
            <li>{{ __('app.rule_build_network') }}</li>
            <li>{{ __('app.rule_higher_levels') }}</li>
        </ul>
    </div>

</div>
@endsection

@push('styles')
<style>
/* Header */
.tm-header {
    display: flex; align-items: center;
    padding: 16px 20px;
    border-bottom: 1px solid var(--border-color);
}
.tm-back-btn {
    width: 36px; height: 36px; border-radius: 50%;
    background: rgba(255,255,255,0.06); border: 1px solid var(--border-color);
    display: flex; align-items: center; justify-content: center;
    color: var(--text-primary); text-decoration: none; font-size: 16px; flex-shrink: 0;
    transition: background 0.2s;
}
.tm-back-btn:hover { background: rgba(255,255,255,0.1); }
.tm-header-center { flex: 1; text-align: center; padding: 0 10px; }
.tm-title  { color: #fff; font-size: 16px; font-weight: 700; margin: 0; }
.tm-subtitle { color: var(--text-muted); font-size: 11px; margin: 3px 0 0; }

/* Sections – no dividers, just padding */
.tm-pad { padding: 20px 20px 0; }

/* Stats */
.tm-stats-row {
    display: flex; align-items: center;
    background: rgba(255,255,255,0.03);
    border: 1px solid var(--border-color);
    border-radius: 14px; padding: 16px 20px;
    margin-bottom: 4px;
}
.tm-stat { flex: 1; text-align: center; }
.tm-stat-val { font-size: 18px; font-weight: 800; }
.tm-stat-val.green { color: #22c55e; }
.tm-stat-val.gold  { color: var(--gold-color); }
.tm-stat-lbl { color: var(--text-muted); font-size: 11px; margin-top: 3px; }
.tm-stat-sep { width: 1px; height: 38px; background: var(--border-color); margin: 0 16px; }

/* Section label */
.tm-sec-label {
    color: var(--gold-color); font-size: 12px; font-weight: 700; margin-bottom: 14px;
}

/* QR */
.tm-qr-area {
    display: flex; flex-direction: column; align-items: center; gap: 14px;
    padding-bottom: 4px;
}
.tm-qr-wrap {
    display: inline-block; padding: 14px;
    background: #fff; border-radius: 14px;
    box-shadow: 0 6px 24px rgba(0,0,0,0.35);
}
#qrcode { display: flex; justify-content: center; align-items: center; }
#qrcode img, #qrcode canvas { border-radius: 6px; }
.tm-level-badge {
    display: inline-block;
    background: linear-gradient(135deg, #00e5ff, #00b8d4);
    color: #0a0f1e; padding: 5px 18px; border-radius: 20px;
    font-size: 13px; font-weight: 700;
    box-shadow: 0 2px 10px rgba(0,229,255,0.25);
}
.tm-save-btn {
    width: 100%; max-width: 220px; padding: 13px;
    background: linear-gradient(135deg, #22c55e, #16a34a);
    border: none; border-radius: 25px;
    color: #fff; font-size: 14px; font-weight: 700;
    cursor: pointer; transition: all 0.25s;
}
.tm-save-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(34,197,94,0.35); }

/* Copy rows */
.tm-field-lbl { color: var(--text-muted); font-size: 11px; margin-bottom: 6px; }
.tm-copy-row {
    display: flex; align-items: center; justify-content: space-between; gap: 10px;
    background: rgba(255,255,255,0.03); border: 1px solid var(--border-color);
    border-radius: 10px; padding: 12px 14px;
}
.tm-copy-row span {
    color: #fff; font-size: 14px; font-weight: 500;
    flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}
.tm-link-mono { font-family: monospace !important; font-size: 12px !important; }
.tm-copy-btn {
    background: none; border: none; color: var(--gold-color);
    font-size: 18px; cursor: pointer; padding: 4px; flex-shrink: 0; transition: all 0.2s;
}
.tm-copy-btn:hover { color: #00b8d4; transform: scale(1.1); }

/* Rules */
.tm-rules-list {
    margin: 0; padding-left: 18px;
    color: var(--text-muted); font-size: 12px; line-height: 2;
}
.tm-rules-list li { margin-bottom: 3px; }

/* Toast */
.tm-toast {
    position: fixed; top: 20px; left: 50%; transform: translateX(-50%);
    padding: 10px 22px; border-radius: 8px; z-index: 9999;
    font-size: 13px; font-weight: 600; color: #fff; pointer-events: none;
    box-shadow: 0 4px 16px rgba(0,0,0,0.3); animation: tmToastIn 0.3s ease;
}
@keyframes tmToastIn {
    from { opacity:0; transform: translateX(-50%) translateY(-10px); }
    to   { opacity:1; transform: translateX(-50%) translateY(0); }
}
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
const translations = {
    invitationCodeCopied: "{{ __('app.invitation_code_copied') }}",
    invitationLinkCopied: "{{ __('app.invitation_link_copied') }}",
    qrCodeSaved:          "{{ __('app.qr_code_saved') }}",
    failedToCopyCode:     "{{ __('app.failed_to_copy_code') }}",
    failedToCopyLink:     "{{ __('app.failed_to_copy_link') }}",
    failedToSaveQr:       "{{ __('app.failed_to_save_qr') }}"
};

document.addEventListener('DOMContentLoaded', function () {
    new QRCode(document.getElementById('qrcode'), {
        text: "{{ $referralLink }}",
        width: 180, height: 180,
        colorDark: '#000000', colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.H
    });
});

function showToast(message, ok = true) {
    const t = document.createElement('div');
    t.className = 'tm-toast';
    t.style.background = ok ? '#22c55e' : '#ef4444';
    t.innerHTML = `<i class="bi bi-check-circle me-2"></i>${message}`;
    document.body.appendChild(t);
    setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 300); }, 2000);
}

function copyInvitationCode() {
    const code = document.getElementById('invitationCode').textContent;
    const icon = document.getElementById('copyCodeIcon');
    navigator.clipboard.writeText(code).then(() => {
        icon.classList.replace('bi-clipboard', 'bi-check-lg');
        showToast(translations.invitationCodeCopied);
        setTimeout(() => icon.classList.replace('bi-check-lg', 'bi-clipboard'), 2000);
    }).catch(() => showToast(translations.failedToCopyCode, false));
}

function copyInvitationLink() {
    const link = document.getElementById('invitationLink').textContent;
    const icon = document.getElementById('copyLinkIcon');
    navigator.clipboard.writeText(link).then(() => {
        icon.classList.replace('bi-link-45deg', 'bi-check-lg');
        showToast(translations.invitationLinkCopied);
        setTimeout(() => icon.classList.replace('bi-check-lg', 'bi-link-45deg'), 2000);
    }).catch(() => showToast(translations.failedToCopyLink, false));
}

function saveQRCode() {
    const canvas = document.querySelector('#qrcode canvas');
    if (!canvas) { showToast(translations.failedToSaveQr, false); return; }
    const a = document.createElement('a');
    a.download = 'referral-qrcode.png';
    a.href = canvas.toDataURL();
    a.click();
    showToast(translations.qrCodeSaved);
}

setTimeout(() => {
    document.querySelectorAll('.alert').forEach(a => {
        a.style.opacity = '0'; setTimeout(() => a.remove(), 300);
    });
}, 3000);
</script>
@endpush
