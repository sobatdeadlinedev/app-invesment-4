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
                    <h5 class="pg-title">{{ __('app.invite_friends') }}</h5>
                    <p class="pg-subtitle">{{ __('app.earn_commission') }}</p>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mx-3 mt-3" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Stats -->
            <div class="stat-row">
                <div class="stat-cell">
                    <p class="text-muted mb-1" style="font-size: 11px;">{{ __('app.recommended_number') }}</p>
                    <h6 class="mb-0 fw-bold" style="color: #4ade80;">{{ $directTeam }} / {{ $totalTeam }}</h6>
                </div>
                <div class="stat-cell">
                    <p class="text-muted mb-1" style="font-size: 11px;">{{ __('app.total_revenue') }}</p>
                    <h6 class="mb-0 fw-bold" style="color: #4ade80;">{{ number_format($totalRevenue ?? 0, 2) }}</h6>
                </div>
            </div>

            <!-- QR Code -->
            <div class="w-card" style="text-align: center;">
                <div class="w-card-head" style="justify-content: center;"><i class="bi bi-qr-code"></i>{{ __('app.my_qr_code') }}</div>
                <div class="form-block" style="display: flex; flex-direction: column; align-items: center; gap: 12px;">
                    <div class="qr-code-wrapper">
                        <div id="qrcode"></div>
                    </div>
                    @if ($user->level)
                        <span class="badge-level">Level {{ $user->level }}</span>
                    @else
                        <span class="badge-level-empty">—</span>
                    @endif
                    <button class="btn-cta" onclick="saveQRCode()" style="max-width: 200px;">
                        <i class="bi bi-download me-2"></i>{{ __('app.save_qr') }}
                    </button>
                </div>
            </div>

            <!-- Invitation Code & Link -->
            <div class="w-card">
                <div class="w-card-head"><i class="bi bi-share"></i>{{ __('app.my_invitation_code') }}</div>
                <div class="form-block">
                    <p class="form-block-title">{{ __('app.my_invitation_code') }}</p>
                    <div class="invite-value-row mb-3">
                        <span class="invite-value" id="invitationCode">{{ $user->refferal_code }}</span>
                        <button class="btn-copy-icon" onclick="copyInvitationCode()" title="{{ __('app.copy_code') }}">
                            <i class="bi bi-clipboard" id="copyCodeIcon"></i>
                        </button>
                    </div>
                    <p class="form-block-title">{{ __('app.my_invitation_link') }}</p>
                    <div class="invite-value-row">
                        <span class="invite-value link" id="invitationLink">{{ $referralLink }}</span>
                        <button class="btn-copy-icon" onclick="copyInvitationLink()" title="{{ __('app.copy_link') }}">
                            <i class="bi bi-link-45deg" id="copyLinkIcon"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Rules -->
            <div class="w-card">
                <div class="w-card-head"><i class="bi bi-info-circle"></i>{{ __('app.rules') }}</div>
                <div class="form-block">
                    <div class="rules-box">
                        <ul>
                            <li>{{ __('app.rule_share_code') }}</li>
                            <li>{{ __('app.rule_earn_commission') }}</li>
                            <li>{{ __('app.rule_build_network') }}</li>
                            <li>{{ __('app.rule_higher_levels') }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div style="height: 16px;"></div>

        </div>
    </div>

    <style>
        .qr-code-wrapper {
            display: inline-block; padding: 16px;
            background: white; border-radius: 14px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        #qrcode { display: flex; justify-content: center; align-items: center; }
        #qrcode img, #qrcode canvas { border-radius: 6px; }

        .badge-level {
            display: inline-block;
            background: linear-gradient(135deg, #00e5ff 0%, #00b8d4 100%);
            color: #0a0f1e; padding: 6px 18px; border-radius: 20px;
            font-size: 13px; font-weight: 700;
            box-shadow: 0 2px 10px rgba(0,229,255,0.3);
        }
        .badge-level-empty {
            display: inline-block;
            background: rgba(255,255,255,0.08); color: var(--text-muted);
            padding: 6px 18px; border-radius: 20px; font-size: 13px; font-weight: 600;
        }

        .invite-value-row {
            display: flex; align-items: center; justify-content: space-between;
            background: rgba(0,229,255,0.05); border: 1px solid var(--border-color);
            border-radius: 10px; padding: 11px 14px; gap: 10px;
        }
        .invite-value {
            color: var(--text-white); font-size: 14px; font-weight: 500;
            flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        }
        .invite-value.link { font-family: monospace; font-size: 12px; }
        .btn-copy-icon {
            background: transparent; border: none;
            color: var(--gold-color); font-size: 18px; cursor: pointer;
            padding: 4px; transition: all 0.2s ease; flex-shrink: 0;
        }
        .btn-copy-icon:hover { color: #00b8d4; transform: scale(1.1); }

        .rules-box {
            background: rgba(0,229,255,0.04); border: 1px solid var(--border-color);
            border-radius: 10px; padding: 14px 16px;
        }
        .rules-box ul { margin: 0; padding-left: 18px; color: var(--text-muted); font-size: 13px; line-height: 1.9; }
        .rules-box ul li { margin-bottom: 4px; }
        .rules-box ul li:last-child { margin-bottom: 0; }

        /* Toast */
        .toast-notification {
            position: fixed; top: 20px; left: 50%; transform: translateX(-50%);
            padding: 10px 20px; border-radius: 8px; z-index: 9999;
            font-size: 13px; font-weight: 600;
            box-shadow: 0 4px 16px rgba(0,0,0,0.3);
            animation: toastIn 0.3s ease;
        }
        @keyframes toastIn { from { opacity: 0; transform: translateX(-50%) translateY(-10px); } to { opacity: 1; transform: translateX(-50%) translateY(0); } }
    </style>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
        <script>
            const translations = {
                invitationCodeCopied: "{{ __('app.invitation_code_copied') }}",
                invitationLinkCopied: "{{ __('app.invitation_link_copied') }}",
                qrCodeSaved: "{{ __('app.qr_code_saved') }}",
                failedToCopyCode: "{{ __('app.failed_to_copy_code') }}",
                failedToCopyLink: "{{ __('app.failed_to_copy_link') }}",
                failedToSaveQr: "{{ __('app.failed_to_save_qr') }}"
            };

            document.addEventListener('DOMContentLoaded', function() {
                const qrcodeDiv = document.getElementById("qrcode");
                qrcodeDiv.innerHTML = '';
                new QRCode(qrcodeDiv, {
                    text: "{{ $referralLink }}",
                    width: 180, height: 180,
                    colorDark: "#000000", colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
            });

            function showToast(message, type = 'success') {
                const toast = document.createElement('div');
                toast.className = 'toast-notification';
                toast.style.background = type === 'success' ? '#22c55e' : '#ef4444';
                toast.style.color = 'white';
                toast.innerHTML = `<i class="bi bi-check-circle me-2"></i>${message}`;
                document.body.appendChild(toast);
                setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 2000);
            }

            function copyInvitationCode() {
                const code = document.getElementById('invitationCode').textContent;
                const icon = document.getElementById('copyCodeIcon');
                navigator.clipboard.writeText(code).then(() => {
                    icon.classList.replace('bi-clipboard', 'bi-check-lg');
                    showToast(translations.invitationCodeCopied);
                    setTimeout(() => icon.classList.replace('bi-check-lg', 'bi-clipboard'), 2000);
                }).catch(() => showToast(translations.failedToCopyCode, 'error'));
            }

            function copyInvitationLink() {
                const link = document.getElementById('invitationLink').textContent;
                const icon = document.getElementById('copyLinkIcon');
                navigator.clipboard.writeText(link).then(() => {
                    icon.classList.replace('bi-link-45deg', 'bi-check-lg');
                    showToast(translations.invitationLinkCopied);
                    setTimeout(() => icon.classList.replace('bi-check-lg', 'bi-link-45deg'), 2000);
                }).catch(() => showToast(translations.failedToCopyLink, 'error'));
            }

            function saveQRCode() {
                const canvas = document.querySelector('#qrcode canvas');
                if (canvas) {
                    const link = document.createElement('a');
                    link.download = 'referral-qrcode.png';
                    link.href = canvas.toDataURL();
                    link.click();
                    showToast(translations.qrCodeSaved);
                } else {
                    showToast(translations.failedToSaveQr, 'error');
                }
            }

            setTimeout(function() {
                document.querySelectorAll('.alert').forEach(a => { a.style.opacity = '0'; setTimeout(() => a.remove(), 300); });
            }, 3000);
        </script>
    @endpush
@endsection
