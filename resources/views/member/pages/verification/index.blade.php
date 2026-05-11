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
                    <h5 class="pg-title">{{ __('app.account_verification') }}</h5>
                    <p class="pg-subtitle">{{ __('app.complete_verification_data') }}</p>
                </div>
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

            @if (!$verification || !$verification->submitted_at)
                <!-- Verification Form -->
                <form action="{{ route('member.verification.store') }}" method="POST" enctype="multipart/form-data"
                    id="verificationForm">
                    @csrf

                    <div class="w-card">
                        <div class="w-card-head"><i class="bi bi-person-badge"></i>{{ __('app.personal_information') }}</div>
                        <div class="form-block">
                            <p class="form-block-title">{{ __('app.full_name') }}</p>
                            <input type="text" name="full_name"
                                class="form-control-dark @error('full_name') is-invalid @enderror"
                                placeholder="{{ __('app.enter_full_name') }}" value="{{ old('full_name') }}" required>
                            @error('full_name')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-block">
                            <p class="form-block-title">{{ __('app.identity_number') }}</p>
                            <input type="text" name="identity_number"
                                class="form-control-dark @error('identity_number') is-invalid @enderror"
                                placeholder="{{ __('app.enter_identity_number') }}" value="{{ old('identity_number') }}" required>
                            @error('identity_number')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="w-card">
                        <div class="w-card-head"><i class="bi bi-images"></i>{{ __('app.upload_documents') }}</div>
                        <div class="form-block">
                            <p class="form-block-title">{{ __('app.upload_identity_photo') }}</p>
                            <div class="upload-area-simple" onclick="document.getElementById('identity_photo').click()">
                                <input type="file" id="identity_photo" name="identity_photo" accept="image/*" class="d-none"
                                    onchange="previewImage(this, 'identityPreview')" required>
                                <div id="identityPreview" class="preview-container-simple">
                                    <i class="bi bi-card-image" style="font-size: 24px; color: var(--text-muted);"></i>
                                    <span class="text-muted ms-2">{{ __('app.upload_identity_photo') }}</span>
                                </div>
                            </div>
                            @error('identity_photo')
                                <small class="text-danger d-block mt-2">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-block">
                            <p class="form-block-title">{{ __('app.upload_selfie_photo') }}</p>
                            <div class="upload-area-simple" onclick="document.getElementById('selfie_photo').click()">
                                <input type="file" id="selfie_photo" name="selfie_photo" accept="image/*" class="d-none"
                                    onchange="previewImage(this, 'selfiePreview')" required>
                                <div id="selfiePreview" class="preview-container-simple">
                                    <i class="bi bi-camera-fill" style="font-size: 24px; color: var(--text-muted);"></i>
                                    <span class="text-muted ms-2">{{ __('app.upload_selfie_photo') }}</span>
                                </div>
                            </div>
                            @error('selfie_photo')
                                <small class="text-danger d-block mt-2">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="w-page-footer">
                        <button type="submit" class="btn-cta">
                            {{ __('app.submit_verification') }}
                        </button>
                    </div>
                </form>

            @elseif($verification->submitted_at && !$user->is_verified)
                <!-- Status: Pending -->
                <div class="w-card" style="text-align: center;">
                    <div class="form-block" style="padding-top: 36px; padding-bottom: 36px;">
                        <div class="status-icon-circle pending mx-auto mb-3">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <h5 class="text-white fw-bold mb-2">{{ __('app.verification_pending') }}</h5>
                        <p class="text-muted mb-3" style="font-size: 14px;">{{ __('app.verification_pending_message') }}</p>
                        <small class="text-muted">{{ __('app.submitted_on') }}:
                            {{ $verification->submitted_at->format('d M Y, H:i') }}</small>
                    </div>
                </div>

                <div class="w-card">
                    <div class="w-card-head"><i class="bi bi-person-lines-fill"></i>{{ __('app.submitted_data') }}</div>
                    <div class="form-block">
                        <div class="verif-data-row">
                            <span class="text-muted small">{{ __('app.full_name') }}</span>
                            <span class="text-white fw-bold small">{{ $verification->full_name }}</span>
                        </div>
                        <div class="verif-data-row" style="border-bottom: none;">
                            <span class="text-muted small">{{ __('app.identity_number') }}</span>
                            <span class="text-white fw-bold small">{{ $verification->identity_number }}</span>
                        </div>
                    </div>
                </div>

            @elseif($user->is_verified)
                <!-- Status: Verified -->
                <div class="w-card" style="text-align: center;">
                    <div class="form-block" style="padding-top: 36px; padding-bottom: 36px;">
                        <div class="status-icon-circle verified mx-auto mb-3">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <h5 class="text-white fw-bold mb-2">{{ __('app.account_verified') }}</h5>
                        <p class="text-muted mb-3" style="font-size: 14px;">{{ __('app.account_verified_message') }}</p>
                        @if ($verification->verified_at)
                            <small class="text-muted">{{ __('app.verified_on') }}:
                                {{ $verification->verified_at->format('d M Y, H:i') }}</small>
                        @endif
                    </div>
                </div>

                <div class="w-card">
                    <div class="w-card-head"><i class="bi bi-shield-check"></i>{{ __('app.verified_data') }}</div>
                    <div class="form-block">
                        <div class="verif-data-row">
                            <span class="text-muted small">{{ __('app.full_name') }}</span>
                            <span class="text-white fw-bold small">{{ $verification->full_name }}</span>
                        </div>
                        <div class="verif-data-row" style="border-bottom: none;">
                            <span class="text-muted small">{{ __('app.identity_number') }}</span>
                            <span class="text-white fw-bold small">{{ $verification->identity_number }}</span>
                        </div>
                    </div>
                </div>
            @endif
            <div style="height: 16px;"></div>

        </div>
    </div>

    <style>
        .upload-area-simple {
            background: rgba(0,229,255,0.05); border: 1px dashed rgba(0,229,255,0.25);
            border-radius: 10px; padding: 14px; cursor: pointer; transition: all 0.2s ease;
        }
        .upload-area-simple:hover { background: rgba(0,229,255,0.09); border-color: var(--gold-color); }
        .preview-container-simple { display: flex; align-items: center; }
        .preview-container-simple img { width: 100%; max-height: 180px; object-fit: cover; border-radius: 8px; }

        .status-icon-circle {
            width: 80px; height: 80px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; border: 2px solid;
        }
        .status-icon-circle i { font-size: 38px; }
        .status-icon-circle.pending {
            background: rgba(255,193,7,0.12); border-color: rgba(255,193,7,0.3);
        }
        .status-icon-circle.pending i { color: #ffc107; }
        .status-icon-circle.verified {
            background: rgba(40,167,69,0.12); border-color: rgba(40,167,69,0.3);
        }
        .status-icon-circle.verified i { color: #28a745; }

        .verif-data-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 10px 0; border-bottom: 1px solid var(--border-color);
        }
    </style>

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
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview" style="width:100%;max-height:180px;object-fit:cover;border-radius:8px;">`;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        document.getElementById('verificationForm')?.addEventListener('submit', function(e) {
            const identityPhoto = document.getElementById('identity_photo').files[0];
            const selfiePhoto = document.getElementById('selfie_photo').files[0];
            if (!identityPhoto || !selfiePhoto) { e.preventDefault(); alert(translations.uploadAllPhotos); return false; }
            if (identityPhoto.size > 2048000 || selfiePhoto.size > 2048000) { e.preventDefault(); alert(translations.maxFileSize); return false; }
        });
    </script>
@endsection
