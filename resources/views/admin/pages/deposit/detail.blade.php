@extends('admin.layouts.app')
@section('content')
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar pt-5 pt-lg-10">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack flex-wrap">
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                    <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                        Deposit Detail - {{ $deposit->reference }}</h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.dashboard.index') }}" class="text-muted text-hover-primary">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.deposit.index') }}" class="text-muted text-hover-primary">Deposit</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">{{ $deposit->reference }}</li>
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
                <div class="alert alert-success alert-dismissible d-flex align-items-center p-5 mb-10">
                    <i class="ki-outline ki-shield-tick fs-2hx text-success me-4"></i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-dark">Success</h4>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible d-flex align-items-center p-5 mb-10">
                    <i class="ki-outline ki-cross-circle fs-2hx text-danger me-4"></i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-dark">Error</h4>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!--begin::Layout-->
            <div class="d-flex flex-column flex-lg-row">
                <!--begin::Content-->
                <div class="flex-lg-row-fluid me-lg-15 order-2 order-lg-1 mb-10 mb-lg-0">
                    <!--begin::Card-->
                    <div class="card card-flush pt-3 mb-5 mb-xl-10">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Payment Proof</h2>
                            </div>
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            @if ($deposit->payment_proof)
                                <div class="text-center">
                                    <img src="{{ asset('storage/' . $deposit->payment_proof) }}" alt="Payment Proof"
                                        class="img-fluid rounded" style="max-height: 600px; cursor: pointer;"
                                        data-bs-toggle="modal" data-bs-target="#paymentProofModal">
                                    <p class="text-muted mt-3">Click image to enlarge</p>
                                </div>

                                <!-- Modal for full image -->
                                <div class="modal fade" id="paymentProofModal" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-xl">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Payment Proof - {{ $deposit->reference }}</h5>
                                                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2"
                                                    data-bs-dismiss="modal">
                                                    <i class="ki-outline ki-cross fs-1"></i>
                                                </div>
                                            </div>
                                            <div class="modal-body text-center">
                                                <img src="{{ asset('storage/' . $deposit->payment_proof) }}"
                                                    alt="Payment Proof" class="img-fluid">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-10">
                                    <i class="ki-outline ki-file-deleted fs-5x text-muted mb-5"></i>
                                    <p class="text-muted">No payment proof uploaded</p>
                                </div>
                            @endif
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Content-->

                <!--begin::Sidebar-->
                <div class="flex-column flex-lg-row-auto w-lg-250px w-xl-300px mb-10 order-1 order-lg-2">
                    <!--begin::Card-->
                    <div class="card card-flush mb-0" data-kt-sticky="true" data-kt-sticky-name="subscription-summary"
                        data-kt-sticky-offset="{default: false, lg: '200px'}"
                        data-kt-sticky-width="{lg: '250px', xl: '300px'}" data-kt-sticky-left="auto"
                        data-kt-sticky-top="150px" data-kt-sticky-animation="false" data-kt-sticky-zindex="95">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Summary</h2>
                            </div>
                        </div>
                        <!--end::Card header-->

                        <!--begin::Card body-->
                        <div class="card-body pt-0 fs-6">
                            <!--begin::Section - User Info-->
                            <div class="mb-7">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-60px symbol-circle me-3">
                                        <img alt="Pic" src="{{ asset('assets/media/avatars/blank.png') }}" />
                                    </div>
                                    <div class="d-flex flex-column">
                                        <a href="#" class="fs-4 fw-bold text-gray-900 text-hover-primary me-2">
                                            {{ $deposit->user->name }}
                                        </a>
                                        <a href="#" class="fw-semibold text-gray-600 text-hover-primary">
                                            {{ $deposit->user->email }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <!--end::Section-->

                            <!--begin::Separator-->
                            <div class="separator separator-dashed mb-7"></div>
                            <!--end::Separator-->

                            <!--begin::Section - Transaction Details-->
                            <div class="mb-7">
                                <h5 class="mb-4">Transaction Details</h5>
                                <table class="table fs-6 fw-semibold gs-0 gy-2 gx-2 m-0">
                                    <tr>
                                        <td class="text-gray-500">Reference:</td>
                                        <td class="text-gray-800">
                                            <span class="badge badge-light-primary">{{ $deposit->reference }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-gray-500">Amount:</td>
                                        <td class="text-gray-800 fw-bold">{{ number_format($deposit->amount, 2) }} USDT
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-gray-500">Payment Method:</td>
                                        <td class="text-gray-800">{{ ucfirst($deposit->payment_method ?? '-') }}</td>
                                    </tr>
                                    @if ($deposit->wallet_address)
                                        <tr>
                                            <td class="text-gray-500 align-top">Wallet Address:</td>
                                            <td class="text-gray-800">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="font-monospace" style="word-break: break-all;">
                                                        {{ $deposit->wallet_address }}
                                                    </span>
                                                    <i class="ki-outline ki-copy fs-5 text-muted cursor-pointer copy-wallet-btn"
                                                        data-clipboard-text="{{ $deposit->wallet_address }}"
                                                        title="Copy address"></i>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td class="text-gray-500">Status:</td>
                                        <td>
                                            <span class="badge badge-light-{{ $deposit->status_color }}">
                                                {{ ucfirst($deposit->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-gray-500">Date:</td>
                                        <td class="text-gray-800">{{ $deposit->created_at->format('d M Y, h:i a') }}</td>
                                    </tr>
                                    @if ($deposit->approver)
                                        <tr>
                                            <td class="text-gray-500">Approved By:</td>
                                            <td class="text-gray-800">{{ $deposit->approver->name }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                            <!--end::Section-->

                            <!--begin::Separator-->
                            <div class="separator separator-dashed mb-7"></div>
                            <!--end::Separator-->

                            <!--begin::Actions-->
                            @if ($deposit->status === 'pending')
                                <div class="mb-0">
                                    <h5 class="mb-4">Actions</h5>

                                    <form action="{{ route('admin.deposit.approve', $deposit->id) }}" method="POST"
                                        class="mb-3">
                                        @csrf
                                        <button type="submit" class="btn btn-success w-100"
                                            onclick="return confirm('Are you sure you want to approve this deposit?')">
                                            <i class="ki-outline ki-check fs-2"></i>
                                            Approve Deposit
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.deposit.reject', $deposit->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-danger w-100"
                                            onclick="return confirm('Are you sure you want to reject this deposit?')">
                                            <i class="ki-outline ki-cross fs-2"></i>
                                            Reject Deposit
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="mb-0">
                                    <div class="alert alert-info d-flex align-items-center p-5">
                                        <i class="ki-outline ki-information fs-2hx text-info me-4"></i>
                                        <div class="d-flex flex-column">
                                            <span>This deposit has been {{ $deposit->status }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <!--end::Actions-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Sidebar-->
            </div>
            <!--end::Layout-->
        </div>
    </div>
    <!--end::Content-->

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Auto hide alert after 5 seconds
                setTimeout(function() {
                    $('.alert').fadeOut('slow');
                }, 5000);
            });

            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.copy-wallet-btn');
                if (!btn) return;
                const text = btn.getAttribute('data-clipboard-text');
                navigator.clipboard.writeText(text).then(() => {
                    const original = btn.className;
                    btn.classList.remove('ki-copy');
                    btn.classList.add('ki-check');
                    setTimeout(() => { btn.className = original; }, 1200);
                });
            });
        </script>
    @endpush
@endsection