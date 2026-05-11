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
            <p class="kv-subtitle">{{ __('app.complete_verification_data') }}</p>
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

            <div class="kv-pad">
                <div class="kv-field-label">{{ __('app.full_name') }}</div>
                <div class="kv-input-wrap">
                    <i class="bi bi-person-fill kv-field-icon"></i>
                    <input type="text" name="full_name"
                        class="kv-input @error('full_name') is-invalid @enderror"
                        placeholder="{{ __('app.enter_full_name') }}"
                        value="{{ old('full_name') }}" required>
                </div>
                @error('full_name')
                    <small class="kv-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="kv-pad">
                <div class="kv-field-label">{{ __('app.identity_number') }}</div>
                <div class="kv-input-wrap">
                    <i class="bi bi-card-text kv-field-icon"></i>
                    <input type="text" name="identity_number"
                        class="kv-input @error('identity_number') is-invalid @enderror"
                        placeholder="{{ __('app.enter_identity_number') }}"
                        value="{{ old('identity_number') }}" required>
                </div>
                @error('identity_number')
                    <small class="kv-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="kv-pad">
                <div class="kv-field-label">{{ __('app.upload_identity_photo') }}</div>
                <div class="kv-upload-area" onclick="document.getElementById('identity_photo').click()">
                    <input type="file" id="identity_photo" name="identity_photo" accept="image/*" class="d-none"
                        onchange="previewImage(this, 'identityPreview')" required>
                    <div id="identityPreview" class="kv-preview-inner">
                        <i class="bi bi-card-image kv-upload-icon"></i>
                        <span>{{ __('app.upload_identity_photo') }}</span>
                    </div>
                </div>
                @error('identity_photo')
                    <small class="kv-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="kv-pad">
                <div class="kv-field-label">{{ __('app.upload_selfie_photo') }}</div>
                <div class="kv-upload-area" onclick="document.getElementById('selfie_photo').click()">
                    <input type="file" id="selfie_photo" name="selfie_photo" accept="image/*" class="d-none"
                        onchange="previewImage(this, 'selfiePreview')" required>
                    <div id="selfiePreview" class="kv-preview-inner">
                        <i class="bi bi-camera-fill kv-upload-icon"></i>
                        <span>{{ __('app.upload_selfie_photo') }}</span>
                    </div>
                </div>
                @error('selfie_photo')
                    <small class="kv-error">{{ $message }}</small>
                @enderror
            </div>

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

        <div class="kv-pad">
            <div class="kv-field-label">{{ __('app.full_name') }}</div>
            <div class="kv-info-box">
                <i class="bi bi-person-fill kv-field-icon"></i>
                <span>{{ $verification->full_name }}</span>
            </div>
        </div>

        <div class="kv-pad" style="padding-bottom: 24px;">
            <div class="kv-field-label">{{ __('app.identity_number') }}</div>
            <div class="kv-info-box">
                <i class="bi bi-card-text kv-field-icon"></i>
                <span>{{ $verification->identity_number }}</span>
            </div>
        </div>

    {{-- ═══ STATE 3: VERIFIED ═══ --}}
    @elseif($user->is_verified)

        <div class="kv-pad">
            <div class="kv-field-label">{{ __('app.full_name') }}</div>
            <div class="kv-info-box">
                <i class="bi bi-person-fill kv-field-icon"></i>
                <span>{{ $verification->full_name }}</span>
            </div>
        </div>

        <div class="kv-pad">
            <div class="kv-field-label">{{ __('app.identity_number') }}</div>
            <div class="kv-info-box">
                <i class="bi bi-card-text kv-field-icon"></i>
                <span>{{ $verification->identity_number }}</span>
            </div>
        </div>

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
.kv-title   { color: #fff; font-size: 16px; font-weight: 700; margin: 0; }
.kv-subtitle { color: var(--text-muted); font-size: 11px; margin: 3px 0 0; }

/* Sections */
.kv-pad { padding: 18px 20px 0; }

/* Field label */
.kv-field-label {
    color: var(--text-muted); font-size: 13px; font-weight: 500; margin-bottom: 8px;
}

/* Input box */
.kv-input-wrap {
    display: flex; align-items: center; gap: 10px;
    background: rgba(255,255,255,0.04); border: 1px solid var(--border-color);
    border-radius: 10px; padding: 13px 14px;
}
.kv-field-icon { color: var(--text-muted); font-size: 16px; flex-shrink: 0; }
.kv-input {
    flex: 1; background: transparent; border: none; outline: none;
    color: #fff; font-size: 14px; font-weight: 500;
}
.kv-input::placeholder { color: var(--text-muted); }

/* Info box (read-only display) */
.kv-info-box {
    display: flex; align-items: center; gap: 10px;
    background: rgba(255,255,255,0.04); border: 1px solid var(--border-color);
    border-radius: 10px; padding: 13px 14px;
}
.kv-info-box span { color: #fff; font-size: 14px; font-weight: 600; letter-spacing: 0.3px; }

/* Upload area */
.kv-upload-area {
    background: rgba(0,229,255,0.04); border: 1.5px dashed rgba(0,229,255,0.2);
    border-radius: 10px; padding: 16px; cursor: pointer; transition: all 0.2s;
}
.kv-upload-area:hover { background: rgba(0,229,255,0.08); border-color: var(--gold-color); }
.kv-preview-inner {
    display: flex; align-items: center; gap: 10px;
    color: var(--text-muted); font-size: 13px;
}
.kv-preview-inner img { width: 100%; max-height: 180px; object-fit: cover; border-radius: 8px; }
.kv-upload-icon { font-size: 22px; flex-shrink: 0; }

/* Error */
.kv-error { color: #ef4444; font-size: 12px; display: block; margin-top: 5px; }

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
