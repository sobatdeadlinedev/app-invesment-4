@extends('admin.layouts.app')
@section('content')
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar pt-5 pt-lg-10">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack flex-wrap">
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                    <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                        Trading Signals Management</h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.dashboard.index') }}" class="text-muted text-hover-primary">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">Trading Signals</li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="{{ route('admin.signals.create') }}" class="btn btn-sm btn-primary">
                        <i class="ki-outline ki-plus fs-2"></i>Create New Signal
                    </a>
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

            <!--begin::Card-->
            <div class="card">
                <!--begin::Card body-->
                <div class="card-body py-4">
                    <!--begin::Table-->
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_signals">
                        <thead>
                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                <th class="min-w-200px">Signal</th>
                                <th class="min-w-100px">Bet Config</th>
                                <th class="min-w-100px">Access</th>
                                <th class="min-w-100px">Opening Price</th>
                                <th class="min-w-100px">Settlement Price</th>
                                <th class="min-w-125px">Jadwal</th>
                                <th class="min-w-100px">Status</th>
                                <th class="min-w-100px">Participants</th>
                                <th class="min-w-125px">Created</th>
                                <th class="text-end min-w-125px">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                            @forelse($signals as $signal)
                                <tr>
                                    <!-- Signal -->
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-gray-800 fw-bold mb-1">{{ $signal->title }}</span>
                                            @if ($signal->description)
                                                <span
                                                    class="text-muted small">{{ Str::limit($signal->description, 50) }}</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Bet Config -->
                                    <td>
                                        <div class="d-flex flex-column">
                                            @if ($signal->bet_type == 'percentage')
                                                <span
                                                    class="badge badge-light-primary mb-1">{{ number_format($signal->bet_value, 2) }}%</span>
                                            @else
                                                <span
                                                    class="badge badge-light-info mb-1">{{ number_format($signal->bet_value, 2) }}
                                                    USDT</span>
                                            @endif
                                            <span class="text-muted fs-8">{{ ucfirst($signal->bet_type) }}</span>
                                        </div>
                                    </td>

                                    <!-- Access -->
                                    <td>
                                        @if ($signal->is_public)
                                            <span class="badge badge-light-success">
                                                <i class="ki-outline ki-people fs-6"></i> Public
                                            </span>
                                        @else
                                            <span class="badge badge-light-warning">
                                                <i class="ki-outline ki-lock fs-6"></i> Private
                                            </span>
                                            <div class="text-muted fs-8 mt-1">{{ $signal->allowedUsers->count() }} users
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Opening Price -->
                                    <td>
                                        @if ($signal->entry_price)
                                            <span class="text-gray-800">$
                                                {{ number_format($signal->entry_price, 2) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    <!-- Settlement Price -->
                                    <td>
                                        @if ($signal->target_price)
                                            <span class="text-success">$
                                                {{ number_format($signal->target_price, 2) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    <!-- Jadwal -->
                                    <td>
                                        @if ($signal->scheduled_at && $signal->isScheduledForFuture())
                                            <span class="badge badge-light-warning">
                                                <i class="ki-outline ki-time fs-6"></i> Terjadwal
                                            </span>
                                            <div class="text-muted fs-8 mt-1">
                                                {{ $signal->scheduled_at->format('d M Y, H:i') }}</div>
                                        @else
                                            <span class="badge badge-light-success">
                                                <i class="ki-outline ki-check-circle fs-6"></i> Tayang
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Status -->
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

                                    <!-- Participants -->
                                    <td>
                                        <span class="badge badge-light-info">{{ $signal->participants_count }} Users</span>
                                    </td>

                                    <!-- Created -->
                                    <td>{{ $signal->created_at->format('d M Y, H:i:s') }}</td>

                                    <!-- Action -->
                                    <td class="text-end">
                                        <a href="{{ route('admin.signals.show', $signal->id) }}"
                                            class="btn btn-light btn-active-light-primary btn-sm">
                                            <i class="ki-outline ki-eye fs-5"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-10">
                                        <div class="text-gray-600">No signals found</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <!--end::Table-->

                    <!--begin::Pagination-->
                    <div class="d-flex justify-content-center mt-5">
                        {{ $signals->links() }}
                    </div>
                    <!--end::Pagination-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
    </div>
    <!--end::Content-->

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