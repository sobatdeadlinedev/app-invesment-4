@extends('admin.layouts.app')
@section('content')
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar pt-5 pt-lg-10">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack flex-wrap">
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                    <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">App
                        Configuration</h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.dashboard.index') }}" class="text-muted text-hover-primary">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">App Configuration</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!--begin::Card-->
            <div class="card">
                <form action="{{ route('admin.config.update') }}" method="POST" enctype="multipart/form-data"
                    id="kt_project_settings_form" class="form">
                    @csrf
                    <div class="card-body p-9">

                        {{-- ===== App Logo ===== --}}
                        <div class="row mb-5">
                            <div class="col-xl-3">
                                <div class="fs-6 fw-semibold mt-2 mb-3">App Logo</div>
                            </div>
                            <div class="col-lg-8">
                                <div class="image-input image-input-outline" data-kt-image-input="true"
                                    style="background-image: url('{{ asset('assets/media/svg/avatars/blank.svg') }}')">
                                    <div class="image-input-wrapper w-125px h-125px bgi-position-center"
                                        style="background-size: 75%; background-image: url('{{ $configs['app_logo']['value'] ?: asset('assets/media/svg/brand-logos/volicity-9.svg') }}')">
                                    </div>
                                    <label
                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow"
                                        data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change logo">
                                        <i class="ki-outline ki-pencil fs-7"></i>
                                        <input type="file" name="app_logo" accept=".png, .jpg, .jpeg" />
                                        <input type="hidden" name="logo_remove" />
                                    </label>
                                    <span
                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow"
                                        data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel">
                                        <i class="ki-outline ki-cross fs-2"></i>
                                    </span>
                                    <span
                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-white shadow"
                                        data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove">
                                        <i class="ki-outline ki-cross fs-2"></i>
                                    </span>
                                </div>
                                <div class="form-text">Allowed file types: png, jpg, jpeg. Max size: 2MB</div>
                            </div>
                        </div>

                        {{-- ===== App Name ===== --}}
                        <div class="row mb-8">
                            <div class="col-xl-3">
                                <div class="fs-6 fw-semibold mt-2 mb-3">App Name</div>
                            </div>
                            <div class="col-xl-9 fv-row">
                                <input type="text" class="form-control form-control-solid" name="app_name"
                                    value="{{ old('app_name', $configs['app_name']['value']) }}" required />
                            </div>
                        </div>

                        {{-- ===== App Announcement ===== --}}
                        <div class="row mb-8">
                            <div class="col-xl-3">
                                <div class="fs-6 fw-semibold mt-2 mb-3">App Announcement</div>
                            </div>
                            <div class="col-xl-9 fv-row">
                                <textarea name="app_announcement"
                                    class="form-control form-control-solid h-100px">{{ old('app_announcement', $configs['app_announcement']['value']) }}</textarea>
                            </div>
                        </div>

                        {{-- ===== Wallet TRC20 ===== --}}
                        <div class="row mb-8">
                            <div class="col-xl-3">
                                <div class="fs-6 fw-semibold mt-2 mb-3">Wallet TRC20</div>
                                <div class="text-muted fs-7">TRON Network (TRC20)<br>Maks. 10 alamat</div>
                            </div>
                            <div class="col-xl-9 fv-row">
                                @php
                                    $trc20Addresses = old(
                                        'wallet_trc20_addresses',
                                        $configs['app_wallet_trc20']['addresses'] ?? [''],
                                    );
                                    if (empty($trc20Addresses)) {
                                        $trc20Addresses = [''];
                                    }
                                @endphp
                                <div id="trc20-wrapper">
                                    @foreach ($trc20Addresses as $i => $addr)
                                        <div class="wallet-row d-flex gap-2 mb-3">
                                            <input type="text"
                                                class="form-control form-control-solid"
                                                name="wallet_trc20_addresses[]"
                                                value="{{ $addr }}"
                                                placeholder="e.g., TXYZa1b2c3d4e5f6..." />
                                            @if ($i > 0)
                                                <button type="button"
                                                    class="btn btn-icon btn-light-danger btn-remove-wallet"
                                                    title="Hapus">
                                                    <i class="ki-outline ki-trash fs-4"></i>
                                                </button>
                                            @else
                                                <span style="width:38px;flex-shrink:0"></span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button"
                                    class="btn btn-sm btn-light-primary mt-1 btn-add-wallet"
                                    data-wrapper="trc20-wrapper"
                                    data-name="wallet_trc20_addresses[]"
                                    data-placeholder="e.g., TXYZa1b2c3d4e5f6..."
                                    data-max="10">
                                    <i class="ki-outline ki-plus fs-4 me-1"></i> Add TRC20 Address
                                </button>
                                <div class="form-text mt-2">Alamat wallet TRON (TRC20) untuk menerima deposit</div>
                            </div>
                        </div>

                        {{-- ===== Wallet BEP20 ===== --}}
                        <div class="row mb-8">
                            <div class="col-xl-3">
                                <div class="fs-6 fw-semibold mt-2 mb-3">Wallet BEP20</div>
                                <div class="text-muted fs-7">Binance Smart Chain (BEP20)<br>Maks. 10 alamat</div>
                            </div>
                            <div class="col-xl-9 fv-row">
                                @php
                                    $bep20Addresses = old(
                                        'wallet_bep20_addresses',
                                        $configs['app_wallet_bep20']['addresses'] ?? [''],
                                    );
                                    if (empty($bep20Addresses)) {
                                        $bep20Addresses = [''];
                                    }
                                @endphp
                                <div id="bep20-wrapper">
                                    @foreach ($bep20Addresses as $i => $addr)
                                        <div class="wallet-row d-flex gap-2 mb-3">
                                            <input type="text"
                                                class="form-control form-control-solid"
                                                name="wallet_bep20_addresses[]"
                                                value="{{ $addr }}"
                                                placeholder="e.g., 0x1234567890abcdef..." />
                                            @if ($i > 0)
                                                <button type="button"
                                                    class="btn btn-icon btn-light-danger btn-remove-wallet"
                                                    title="Hapus">
                                                    <i class="ki-outline ki-trash fs-4"></i>
                                                </button>
                                            @else
                                                <span style="width:38px;flex-shrink:0"></span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button"
                                    class="btn btn-sm btn-light-primary mt-1 btn-add-wallet"
                                    data-wrapper="bep20-wrapper"
                                    data-name="wallet_bep20_addresses[]"
                                    data-placeholder="e.g., 0x1234567890abcdef..."
                                    data-max="10">
                                    <i class="ki-outline ki-plus fs-4 me-1"></i> Add BEP20 Address
                                </button>
                                <div class="form-text mt-2">Alamat wallet BSC (BEP20) untuk menerima deposit</div>
                            </div>
                        </div>

                    </div>
                    <!--end::Card body-->

                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <button type="reset" class="btn btn-light btn-active-light-primary me-2">Discard</button>
                        <button type="submit" class="btn btn-primary" id="kt_project_settings_submit">Save
                            Changes</button>
                    </div>
                </form>
            </div>
            <!--end::Card-->

        </div>
    </div>
    <!--end::Content-->

@push('scripts')
<script>
    // Attach remove handler ke existing rows dari Blade render
    document.querySelectorAll('.btn-remove-wallet').forEach(function (btn) {
        btn.addEventListener('click', function () {
            this.closest('.wallet-row').remove();
        });
    });

    // Add wallet row
    document.querySelectorAll('.btn-add-wallet').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const wrapperId  = this.dataset.wrapper;
            const fieldName  = this.dataset.name;
            const placeholder = this.dataset.placeholder;
            const max        = parseInt(this.dataset.max) || 10;
            const wrapper    = document.getElementById(wrapperId);
            const rows       = wrapper.querySelectorAll('.wallet-row');

            if (rows.length >= max) {
                alert('Maksimal ' + max + ' alamat wallet.');
                return;
            }

            const div = document.createElement('div');
            div.className = 'wallet-row d-flex gap-2 mb-3';
            div.innerHTML = `
                <input type="text"
                       class="form-control form-control-solid"
                       name="${fieldName}"
                       placeholder="${placeholder}" />
                <button type="button" class="btn btn-icon btn-light-danger btn-remove-wallet" title="Hapus">
                    <i class="ki-outline ki-trash fs-4"></i>
                </button>`;
            wrapper.appendChild(div);

            div.querySelector('.btn-remove-wallet').addEventListener('click', function () {
                div.remove();
            });
        });
    });
</script>
@endpush

@endsection