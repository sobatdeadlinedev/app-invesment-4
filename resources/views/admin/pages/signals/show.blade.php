@extends('admin.layouts.app')
@section('content')
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar pt-5 pt-lg-10">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack flex-wrap">
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                    <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                        Signal Detail - {{ $signal->title }}</h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.dashboard.index') }}" class="text-muted text-hover-primary">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.signals.index') }}" class="text-muted text-hover-primary">Signals</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">{{ $signal->title }}</li>
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
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible d-flex align-items-center p-5 mb-10">
                    <i class="ki-outline ki-cross-circle fs-2hx text-danger me-4"></i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-dark">Error</h4>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!--begin::Layout-->
            <div class="d-flex flex-column flex-lg-row">
                <!--begin::Content-->
                <div class="flex-lg-row-fluid me-lg-15 order-2 order-lg-1 mb-10 mb-lg-0">

                    <!--begin::Card - Participants List-->
                    <div class="card card-flush pt-3 mb-5 mb-xl-10">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Participants ({{ $totalParticipants }})</h2>
                            </div>
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            @if ($totalParticipants > 0)
                                <div class="table-responsive">
                                    <table class="table table-row-dashed align-middle gs-0 gy-4 my-0">
                                        <thead>
                                            <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                                <th class="p-0 pb-3 min-w-175px text-start">USER</th>
                                                <th class="p-0 pb-3 min-w-100px text-end">BET AMOUNT</th>
                                                <th class="p-0 pb-3 min-w-100px text-end">PROFIT/LOSS</th>
                                                <th class="p-0 pb-3 min-w-100px text-end">FEE</th>
                                                <th class="p-0 pb-3 min-w-100px text-end">NET RESULT</th>
                                                <th class="p-0 pb-3 min-w-100px text-end">STATUS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($signal->participants as $participant)
                                                <tr>
                                                    <td class="text-start">
                                                        <div class="d-flex flex-column">
                                                            <span
                                                                class="text-gray-800 fw-bold">{{ $participant->user->name }}</span>
                                                            <span
                                                                class="text-muted fs-7">{{ $participant->user->email }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="text-end">
                                                        <span class="text-gray-800 fw-bold">$
                                                            {{ number_format($participant->bet_amount, 2) }}</span>
                                                    </td>
                                                    <td class="text-end">
                                                        @if ($participant->status === 'settled')
                                                            <span
                                                                class="text-{{ $participant->profit_loss >= 0 ? 'success' : 'danger' }} fw-bold">
                                                                {{ $participant->profit_loss >= 0 ? '+' : '' }} $
                                                                {{ number_format($participant->profit_loss, 2) }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        @if ($participant->status === 'settled')
                                                            <span class="text-warning fw-bold">$
                                                                {{ number_format($participant->fee_amount, 2) }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        @if ($participant->status === 'settled')
                                                            <span
                                                                class="text-{{ $participant->net_result >= 0 ? 'success' : 'danger' }} fw-bold">
                                                                {{ $participant->net_result >= 0 ? '+' : '' }} $
                                                                {{ number_format($participant->net_result, 2) }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        @if ($participant->status === 'joined')
                                                            <span class="badge badge-light-warning">
                                                                <i class="ki-outline ki-time fs-5"></i> Pending
                                                            </span>
                                                        @else
                                                            <span class="badge badge-light-success">
                                                                <i class="ki-outline ki-check fs-5"></i> Settled
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-10">
                                    <i class="ki-outline ki-people fs-5x text-muted mb-5"></i>
                                    <p class="text-muted">No participants yet</p>
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
                    <div class="card card-flush mb-0" data-kt-sticky="true" data-kt-sticky-name="signal-summary"
                        data-kt-sticky-offset="{default: false, lg: '200px'}"
                        data-kt-sticky-width="{lg: '250px', xl: '300px'}" data-kt-sticky-left="auto"
                        data-kt-sticky-top="150px" data-kt-sticky-animation="false" data-kt-sticky-zindex="95">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Signal Details</h2>
                            </div>
                        </div>
                        <!--end::Card header-->

                        <!--begin::Card body-->
                        <div class="card-body pt-0 fs-6">
                            <!--begin::Section - Signal Info-->
                            <div class="mb-7">
                                <h5 class="mb-4">Basic Information</h5>
                                <table class="table fs-6 fw-semibold gs-0 gy-2 gx-2 m-0">
                                    <tr>
                                        <td class="text-gray-500">Title:</td>
                                        <td class="text-gray-800">{{ $signal->title }}</td>
                                    </tr>
                                    @if ($signal->description)
                                        <tr>
                                            <td class="text-gray-500">Description:</td>
                                            <td class="text-gray-800">{{ $signal->description }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td class="text-gray-500">Status:</td>
                                        <td>
                                            @if ($signal->status === 'open')
                                                <span class="badge badge-light-success">
                                                    <i class="ki-outline ki-check-circle fs-5"></i> Open
                                                </span>
                                            @elseif($signal->status === 'closed')
                                                <span class="badge badge-light-warning">
                                                    <i class="ki-outline ki-time fs-5"></i> Closed
                                                </span>
                                            @else
                                                <span class="badge badge-light-primary">
                                                    <i class="ki-outline ki-check fs-5"></i> Settled
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-gray-500">Jadwal Tayang:</td>
                                        <td>
                                            @if ($signal->scheduled_at && $signal->isScheduledForFuture())
                                                <span class="badge badge-light-warning">
                                                    <i class="ki-outline ki-time fs-5"></i>
                                                    {{ $signal->scheduled_at->format('d M Y, H:i') }}
                                                </span>
                                            @else
                                                <span class="badge badge-light-success">
                                                    <i class="ki-outline ki-check-circle fs-5"></i> Sudah Tayang
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-gray-500">Bet Type:</td>
                                        <td>
                                            @if ($signal->bet_type == 'percentage')
                                                <span class="badge badge-light-primary">Percentage</span>
                                            @else
                                                <span class="badge badge-light-info">Fixed Amount</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-gray-500">Bet Value:</td>
                                        <td class="text-gray-800 fw-bold">
                                            {{ $signal->bet_display }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-gray-500">Access:</td>
                                        <td>
                                            @if ($signal->is_public)
                                                <span class="badge badge-light-success">
                                                    <i class="ki-outline ki-people fs-5"></i> Public
                                                </span>
                                            @else
                                                <span class="badge badge-light-warning">
                                                    <i class="ki-outline ki-lock fs-5"></i> Private
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-gray-500">Created By:</td>
                                        <td class="text-gray-800">{{ $signal->creator->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-gray-500">Created:</td>
                                        <td class="text-gray-800">{{ $signal->created_at->format('d M Y, H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <!--end::Section-->

                            <!--begin::Separator-->
                            <div class="separator separator-dashed mb-7"></div>
                            <!--end::Separator-->

                            <!--begin::Section - Prices-->
                            <div class="mb-7">
                                <h5 class="mb-4">Price Levels</h5>
                                <table class="table fs-6 fw-semibold gs-0 gy-2 gx-2 m-0">
                                    @if ($signal->entry_price)
                                        <tr>
                                            <td class="text-gray-500">Opening Price:</td>
                                            <td class="text-gray-800">$ {{ number_format($signal->entry_price, 2) }}</td>
                                        </tr>
                                    @endif
                                    @if ($signal->target_price)
                                        <tr>
                                            <td class="text-gray-500">Settlement Price:</td>
                                            <td class="text-success">$ {{ number_format($signal->target_price, 2) }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                            <!--end::Section-->

                            <!--begin::Separator-->
                            <div class="separator separator-dashed mb-7"></div>
                            <!--end::Separator-->

                            <!--begin::Section - Allowed Users (Only for Private Signals)-->
                            @if (!$signal->is_public)
                                <div class="mb-7">
                                    <h5 class="mb-4">Allowed Users ({{ $signal->allowedUsers->count() }})</h5>

                                    @if ($signal->allowedUsers->count() > 0)
                                        <div class="scroll-y mh-300px">
                                            @foreach ($signal->allowedUsers as $allowedUser)
                                                <div class="d-flex align-items-center py-2">
                                                    <div class="symbol symbol-35px me-3">
                                                        <div class="symbol-label bg-light-primary">
                                                            <i class="ki-outline ki-user fs-2 text-primary"></i>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <span
                                                            class="text-gray-800 fw-bold fs-7">{{ $allowedUser->user->name }}</span>
                                                        <span
                                                            class="text-muted fs-8">{{ $allowedUser->user->email }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center text-muted py-5">
                                            <i class="ki-outline ki-information fs-2x"></i>
                                            <p class="mt-2">No users selected</p>
                                        </div>
                                    @endif
                                </div>

                                <!--begin::Separator-->
                                <div class="separator separator-dashed mb-7"></div>
                                <!--end::Separator-->
                            @endif
                            <!--end::Section-->

                            <!--begin::Section - Statistics-->
                            <div class="mb-7">
                                <h5 class="mb-4">Statistics</h5>
                                <table class="table fs-6 fw-semibold gs-0 gy-2 gx-2 m-0">
                                    <tr>
                                        <td class="text-gray-500">Participants:</td>
                                        <td class="text-gray-800">{{ $totalParticipants }} Users</td>
                                    </tr>
                                    <tr>
                                        <td class="text-gray-500">Total Bets:</td>
                                        <td class="text-gray-800">$ {{ number_format($totalBetAmount, 2) }}</td>
                                    </tr>
                                    @if ($signal->status !== 'open')
                                        <tr>
                                            <td class="text-gray-500">Result:</td>
                                            <td>
                                                @if ($signal->result === 'win')
                                                    <span class="badge badge-light-success">WIN</span>
                                                @else
                                                    <span class="badge badge-light-danger">LOSS</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-gray-500">Rate of Return:</td>
                                            <td class="text-gray-800">{{ number_format($signal->rate_of_return, 2) }}%
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($signal->opened_at)
                                        <tr>
                                            <td class="text-gray-500">Closed At:</td>
                                            <td class="text-gray-800">{{ $signal->opened_at->format('d M Y, H:i:s') }}
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($signal->closed_at)
                                        <tr>
                                            <td class="text-gray-500">Settled At:</td>
                                            <td class="text-gray-800">{{ $signal->closed_at->format('d M Y, H:i:s') }}
                                            </td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                            <!--end::Section-->

                            <!--begin::Separator-->
                            <div class="separator separator-dashed mb-7"></div>
                            <!--end::Separator-->

                            <!--begin::Actions-->
                            <div class="mb-0">
                                <h5 class="mb-4">Actions</h5>

                                @if ($signal->status === 'open')
                                    <!-- Edit Button -->
                                    <a href="{{ route('admin.signals.edit', $signal->id) }}"
                                        class="btn btn-light-primary w-100 mb-3">
                                        <i class="ki-outline ki-pencil fs-2"></i>
                                        Edit Signal
                                    </a>

                                    <!-- Close Signal Button -->
                                    <button type="button" class="btn btn-warning w-100 mb-3" data-bs-toggle="modal"
                                        data-bs-target="#closeSignalModal">
                                        <i class="ki-outline ki-cross-circle fs-2"></i>
                                        Close Signal
                                    </button>

                                    <!-- Delete Button (if no participants) -->
                                    @if ($totalParticipants === 0)
                                        <form action="{{ route('admin.signals.destroy', $signal->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger w-100"
                                                onclick="return confirm('Are you sure? This action cannot be undone.')">
                                                <i class="ki-outline ki-trash fs-2"></i>
                                                Delete Signal
                                            </button>
                                        </form>
                                    @endif
                                @elseif($signal->status === 'closed')
                                    <!-- Settle Button -->
                                    <form action="{{ route('admin.signals.settle', $signal->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success w-100"
                                            onclick="return confirm('Settle this signal? All participants will be processed and balances will be updated. This action cannot be undone.')">
                                            <i class="ki-outline ki-check fs-2"></i>
                                            Settle All Participants
                                        </button>
                                    </form>
                                @else
                                    <!-- Settled -->
                                    <div class="alert alert-success d-flex align-items-center p-5">
                                        <i class="ki-outline ki-shield-tick fs-2hx text-success me-4"></i>
                                        <div class="d-flex flex-column">
                                            <span>This signal has been settled</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
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

    <!-- Close Signal Modal -->
    <div class="modal fade" id="closeSignalModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Close Trading Signal</h5>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                        <i class="ki-outline ki-cross fs-1"></i>
                    </div>
                </div>
                <form action="{{ route('admin.signals.close', $signal->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-5">
                            <label class="form-label required">Result</label>
                            <select name="admin_choice" class="form-select" required>
                                <option value="">Select Result</option>
                                <option value="call">Call (Price UP ↑)</option>
                                <option value="put">Put (Price DOWN ↓)</option>
                            </select>
                        </div>
                        <div class="mb-5">
                            <label class="form-label required">Rate of Return (%)</label>
                            <input type="number" name="rate_of_return" class="form-control" min="0"
                                max="100" step="0.01" placeholder="e.g., 60.00" required>
                            <div class="form-text">
                                For WIN: Enter profit percentage (e.g., 60 = 60% profit)<br>
                                For LOSS: Enter loss percentage (usually 100 = full loss)
                            </div>
                        </div>
                        <div class="alert alert-warning">
                            <i class="ki-outline ki-information fs-2"></i>
                            <strong>Note:</strong> Closing the signal will lock it and prepare for settlement. You can
                            settle participants after closing.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Close Signal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                setTimeout(function() {
                    $('.alert').fadeOut('slow');
                }, 5000);
            });
        </script>
    @endpush
@endsection