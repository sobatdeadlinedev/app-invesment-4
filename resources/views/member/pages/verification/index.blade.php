@extends('member.layouts.app')
@section('content')
<div class="scrollable-content">

    <!-- Header -->
    <div class="kv-header">
        <a href="{{ route('member.access.index') }}" class="kv-back-btn">
            <i class="bi bi-chevron-left"></i>
        </a>
        <div class="kv-header-center">
            <h5 class="kv-title">{{ __('app.account_verification') }}</h5>
        </div>
        <div style="width:36px;"></div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mx-3 mt-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mx-3 mt-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ═══ STATE 1: FORM ═══ --}}
    @if (!$verification || !$verification->submitted_at)
        <form action="{{ route('member.verification.store') }}" method="POST" enctype="multipart/form-data" id="verificationForm">
            @csrf

            <div class="kv-section">
                <div class="kv-field-label">{{ __('app.full_name') }}</div>
                <input type="text" name="full_name"
                    class="kv-plain-input @error('full_name') is-invalid @enderror"
                    placeholder="{{ __('app.enter_full_name') }}"
                    value="{{ old('full_name') }}" required>
                @error('full_name')
                    <small class="kv-error">{{ $message }}</small>
                @enderror
            </div>
            <div class="kv-divider"></div>

            <div class="kv-section">
                <div class="kv-field-label">{{ __('app.identity_number') }}</div>
                <input type="text" name="identity_number"
                    class="kv-plain-input @error('identity_number') is-invalid @enderror"
                    placeholder="{{ __('app.enter_identity_number') }}"
                    value="{{ old('identity_number') }}" required>
                @error('identity_number')
                    <small class="kv-error">{{ $message }}</small>
                @enderror
            </div>
            <div class="kv-divider"></div>

            <div class="kv-section">
                <div class="kv-field-label">{{ __('app.upload_identity_photo') }}</div>
                <div class="kv-upload-box" onclick="document.getElementById('identity_photo').click()">
                    <input type="file" id="identity_photo" name="identity_photo" accept="image/*" class="d-none"
                        onchange="previewImage(this, 'identityPreview')" required>
                    <div id="identityPreview" class="kv-upload-inner">
                        <i class="bi bi-plus-lg"></i>
                    </div>
                </div>
                @error('identity_photo')
                    <small class="kv-error">{{ $message }}</small>
                @enderror
            </div>
            <div class="kv-divider"></div>

            <div class="kv-section">
                <div class="kv-field-label">{{ __('app.upload_selfie_photo') }}</div>
                <div class="kv-upload-box" onclick="document.getElementById('selfie_photo').click()">
                    <input type="file" id="selfie_photo" name="selfie_photo" accept="image/*" class="d-none"
                        onchange="previewImage(this, 'selfiePreview')" required>
                    <div id="selfiePreview" class="kv-upload-inner">
                        <i class="bi bi-plus-lg"></i>
                    </div>
                </div>
                @error('selfie_photo')
                    <small class="kv-error">{{ $message }}</small>
                @enderror
            </div>
            <div class="kv-divider"></div>

            <div class="kv-footer">
                <button type="submit" class="kv-submit-btn">{{ __('app.submit_verification') }}</button>
            </div>
        </form>

    {{-- ═══ STATE 2: PENDING ═══ --}}
    @elseif($verification->submitted_at && !$user->is_verified)

        <div class="kv-status-banner pending">
            <div class="kv-status-icon pending">
                <i class="bi bi-clock-history"></i>
            </div>
            <div>
                <div class="kv-status-title">{{ __('app.verification_pending') }}</div>
                <div class="kv-status-msg">{{ __('app.verification_pending_message') }}</div>
                <div class="kv-status-date">{{ __('app.submitted_on') }}: {{ $verification->submitted_at->format('d M Y, H:i') }}</div>
            </div>
        </div>

        <div class="kv-section">
            <div class="kv-field-label">{{ __('app.full_name') }}</div>
            <div class="kv-plain-value">{{ $verification->full_name }}</div>
        </div>
        <div class="kv-divider"></div>

        <div class="kv-section">
            <div class="kv-field-label">{{ __('app.identity_number') }}</div>
            <div class="kv-plain-value">{{ $verification->identity_number }}</div>
        </div>
        <div class="kv-divider"></div>

    {{-- ═══ STATE 3: VERIFIED ═══ --}}
    @elseif($user->is_verified)

        <div class="kv-section">
            <div class="kv-field-label">{{ __('app.full_name') }}</div>
            <div class="kv-plain-value">{{ $verification->full_name }}</div>
        </div>
        <div class="kv-divider"></div>

        <div class="kv-section">
            <div class="kv-field-label">{{ __('app.identity_number') }}</div>
            <div class="kv-plain-value">{{ $verification->identity_number }}</div>
        </div>
        <div class="kv-divider"></div>

        <div class="kv-verified-row">
            <i class="bi bi-patch-check-fill me-2"></i>{{ __('app.account_verified') }}
            @if ($verification->verified_at)
                <span class="kv-verified-date">· {{ $verification->verified_at->format('d M Y') }}</span>
            @endif
        </div>

    @endif

    <div style="height: 16px;"></div>
</div>
@endsection

@push('styles')
<style>
/* Header */
.kv-header {
    display: flex; align-items: center;
    padding: 16px 20px;
    border-bottom: 1px solid var(--border-color);
}
.kv-back-btn {
    width: 36px; height: 36px; border-radius: 50%;
    background: rgba(255,255,255,0.06); border: 1px solid var(--border-color);
    display: flex; align-items: center; justify-content: center;
    color: var(--text-primary); text-decoration: none; font-size: 16px; flex-shrink: 0;
    transition: background 0.2s;
}
.kv-back-btn:hover { background: rgba(255,255,255,0.1); }
.kv-header-center { flex: 1; text-align: center; padding: 0 10px; }
.kv-title { color: #fff; font-size: 17px; font-weight: 700; margin: 0; }

/* Section like the reference: label on top, plain box below, divider under */
.kv-section { padding: 18px 20px 14px; }
.kv-field-label {
    color: #fff; font-size: 14px; font-weight: 500; margin-bottom: 10px;
}
.kv-divider { height: 1px; background: rgba(255,255,255,0.08); margin: 0 20px; }

/* Plain bordered input, no icon - matches reference style */
.kv-plain-input {
    width: 100%; background: transparent;
    border: 1px solid rgba(255,255,255,0.25); border-radius: 6px;
    padding: 13px 14px; color: #fff; font-size: 14px; outline: none;
}
.kv-plain-input::placeholder { color: rgba(255,255,255,0.35); }
.kv-plain-input.is-invalid { border-color: #ef4444; }

/* Read-only value display (pending/verified states) */
.kv-plain-value {
    border: 1px solid rgba(255,255,255,0.25); border-radius: 6px;
    padding: 13px 14px; color: #fff; font-size: 14px; font-weight: 600;
}

/* Square upload box with + icon, like reference */
.kv-upload-box {
    width: 120px; height: 120px;
    border: 1px solid rgba(255,255,255,0.35); border-radius: 6px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; overflow: hidden; transition: border-color 0.2s;
}
.kv-upload-box:hover { border-color: var(--gold-color, #22c55e); }
.kv-upload-inner {
    display: flex; align-items: center; justify-content: center;
    width: 100%; height: 100%; color: rgba(255,255,255,0.5); font-size: 26px;
}
.kv-upload-inner img { width: 100%; height: 100%; object-fit: cover; }

/* Error */
.kv-error { color: #ef4444; font-size: 12px; display: block; margin-top: 6px; }

/* Submit footer */
.kv-footer { padding: 20px 20px 24px; }
.kv-submit-btn {
    width: 100%; padding: 15px;
    background: linear-gradient(135deg, #22c55e, #16a34a);
    border: none; border-radius: 25px;
    color: #fff; font-size: 16px; font-weight: 700;
    cursor: pointer; transition: all 0.3s;
}
.kv-submit-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(34,197,94,0.35); }

/* Status banner (pending) */
.kv-status-banner {
    display: flex; align-items: flex-start; gap: 14px;
    margin: 16px 20px 0;
    padding: 16px; border-radius: 12px;
}
.kv-status-banner.pending {
    background: rgba(251,191,36,0.08); border: 1px solid rgba(251,191,36,0.2);
}
.kv-status-icon {
    width: 44px; height: 44px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: 20px;
}
.kv-status-icon.pending { background: rgba(251,191,36,0.15); color: #fbbf24; }
.kv-status-title { color: #fff; font-size: 14px; font-weight: 700; margin-bottom: 3px; }
.kv-status-msg   { color: var(--text-muted); font-size: 12px; line-height: 1.5; margin-bottom: 4px; }
.kv-status-date  { color: var(--text-muted); font-size: 11px; }

/* Verified row */
.kv-verified-row {
    margin: 20px 20px 0;
    padding: 14px;
    background: rgba(34,197,94,0.08); border: 1px solid rgba(34,197,94,0.25);
    border-radius: 12px;
    color: #22c55e; font-size: 15px; font-weight: 700;
    text-align: center;
}
.kv-verified-date { color: rgba(34,197,94,0.7); font-size: 12px; font-weight: 400; }
</style>
@endpush

@push('scripts')
<script>
const translations = {
    uploadAllPhotos: "{{ __('app.upload_all_photos') }}",
    maxFileSize: "{{ __('app.max_file_size') }}"
};

function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

document.getElementById('verificationForm')?.addEventListener('submit', function(e) {
    const identityPhoto = document.getElementById('identity_photo').files[0];
    const selfiePhoto   = document.getElementById('selfie_photo').files[0];
    if (!identityPhoto || !selfiePhoto) { e.preventDefault(); alert(translations.uploadAllPhotos); return false; }
    if (identityPhoto.size > 2048000 || selfiePhoto.size > 2048000) { e.preventDefault(); alert(translations.maxFileSize); return false; }
});

setTimeout(() => {
    document.querySelectorAll('.alert').forEach(a => {
        a.style.opacity = '0'; setTimeout(() => a.remove(), 300);
    });
}, 3000);
</script>
@endpush