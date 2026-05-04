@extends('member.layouts.app')
@section('content')
    <div class="scrollable-content">
        <div class="content-section" style="padding: 0;">

            <!-- Header -->
            <div class="pg-header">
                <a href="{{ route('member.withdraw.index') }}" class="pg-back">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div class="pg-header-text">
                    <h5 class="pg-title">{{ __('app.withdrawal_history') }}</h5>
                </div>
            </div>

            <!-- Summary Stats -->
            <div class="stat-row">
                <div class="stat-cell">
                    <div class="d-flex align-items-center gap-2">
                        <div class="stat-icon pending">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0" style="font-size: 11px;">{{ __('app.pending') }}</p>
                            <h6 class="text-white mb-0 fw-bold">{{ $pendingCount }}</h6>
                        </div>
                    </div>
                </div>
                <div class="stat-cell">
                    <div class="d-flex align-items-center gap-2">
                        <div class="stat-icon completed">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0" style="font-size: 11px;">{{ __('app.completed') }}</p>
                            <h6 class="text-white mb-0 fw-bold">{{ $completedCount }}</h6>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="form-block" style="padding-bottom: 12px;">
                <div class="filter-tabs">
                    <button class="filter-tab active" onclick="filterTransactions('all', event)">{{ __('app.all') }}</button>
                    <button class="filter-tab" onclick="filterTransactions('pending', event)">{{ __('app.pending') }}</button>
                    <button class="filter-tab" onclick="filterTransactions('approved', event)">{{ __('app.approved') }}</button>
                    <button class="filter-tab" onclick="filterTransactions('rejected', event)">{{ __('app.rejected') }}</button>
                    <button class="filter-tab" onclick="filterTransactions('completed', event)">{{ __('app.completed') }}</button>
                </div>
            </div>

            <!-- Transactions List -->
            @forelse($transactions as $transaction)
                <div class="txn-row" data-status="{{ $transaction->status }}">
                    <div class="txn-icon {{ in_array($transaction->status, ['completed']) ? 'completed' : ($transaction->status === 'rejected' ? 'rejected' : ($transaction->status === 'approved' ? 'approved' : ($transaction->status === 'cancelled' ? 'cancelled' : 'pending'))) }}">
                        @if ($transaction->status === 'pending')
                            <i class="bi bi-clock-history"></i>
                        @elseif($transaction->status === 'approved')
                            <i class="bi bi-hourglass-split"></i>
                        @elseif($transaction->status === 'completed')
                            <i class="bi bi-check-circle"></i>
                        @elseif($transaction->status === 'cancelled')
                            <i class="bi bi-slash-circle"></i>
                        @else
                            <i class="bi bi-x-circle"></i>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <div>
                                <h6 class="text-white mb-0 fw-bold" style="font-size: 14px;">{{ __('app.withdrawal') }}</h6>
                                <p class="text-muted mb-0" style="font-size: 11px;">{{ $transaction->reference }}</p>
                            </div>
                            <div class="text-end">
                                <h6 class="text-danger mb-0 fw-bold" style="font-size: 14px;">
                                    -{{ number_format($transaction->total_amount, 2) }} USDT
                                </h6>
                                <span class="s-badge {{ $transaction->status }}">
                                    {{ ucfirst(__('app.' . $transaction->status)) }}
                                </span>
                            </div>
                        </div>
                        <div class="txn-detail-box mt-2">
                            <div class="detail-row">
                                <span class="text-muted small">{{ __('app.wallet_account') }}:</span>
                                <span class="text-white small fw-bold">
                                    {{ $transaction->wallet ? $transaction->wallet->account_name : __('app.na') }}
                                </span>
                            </div>
                            <div class="detail-row">
                                <span class="text-muted small">{{ __('app.account_number') }}:</span>
                                <span class="text-white small fw-bold">
                                    {{ $transaction->wallet ? $transaction->wallet->account_number : __('app.na') }}
                                </span>
                            </div>
                            <div class="detail-row">
                                <span class="text-muted small">{{ __('app.withdrawal_amount') }}:</span>
                                <span class="text-white small fw-bold">{{ number_format($transaction->total_amount, 2) }} USDT</span>
                            </div>
                            @if ($transaction->withdrawal_fee > 0)
                                <div class="detail-row">
                                    <span class="text-muted small">{{ __('app.fee_5_percent') }}:</span>
                                    <span class="text-danger small fw-bold">-{{ number_format($transaction->withdrawal_fee, 2) }} USDT</span>
                                </div>
                            @endif
                            <div class="detail-row">
                                <span class="text-muted small">{{ __('app.you_receive') }}:</span>
                                <span class="text-success small fw-bold">{{ number_format($transaction->amount, 2) }} USDT</span>
                            </div>
                            <div class="detail-row">
                                <span class="text-muted small">{{ __('app.date') }}:</span>
                                <span class="text-white small">{{ $transaction->created_at->format('d M Y, H:i') }}</span>
                            </div>
                            @if ($transaction->status === 'completed' && $transaction->updated_at)
                                <div class="detail-row">
                                    <span class="text-muted small">{{ __('app.completed_at') }}:</span>
                                    <span class="text-white small">{{ $transaction->updated_at->format('d M Y, H:i') }}</span>
                                </div>
                            @endif
                        </div>
                        @if ($transaction->status === 'pending')
                            <div class="mt-2">
                                <button type="button" class="btn-cancel-withdrawal"
                                    onclick="cancelWithdrawal('{{ $transaction->reference }}')">
                                    <i class="bi bi-x-circle me-1"></i>{{ __('app.cancel_withdrawal') }}
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="pg-empty">
                    <i class="bi bi-inbox" style="font-size: 56px; color: var(--text-muted); opacity: 0.3; display: block; margin-bottom: 12px;"></i>
                    <p class="text-muted mb-0">{{ __('app.no_withdrawal_history') }}</p>
                </div>
            @endforelse

            <!-- Pagination -->
            @if ($transactions->hasPages())
                <div class="form-block">
                    {{ $transactions->links() }}
                </div>
            @endif

        </div>
    </div>

    <style>
        /* Stat icons */
        .stat-icon {
            width: 36px; height: 36px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            border: 1px solid; flex-shrink: 0;
        }
        .stat-icon i { font-size: 18px; }
        .stat-icon.pending { background: rgba(255,193,7,0.15); border-color: rgba(255,193,7,0.3); }
        .stat-icon.pending i { color: #ffc107; }
        .stat-icon.completed { background: rgba(40,167,69,0.15); border-color: rgba(40,167,69,0.3); }
        .stat-icon.completed i { color: #28a745; }

        /* Filter Tabs */
        .filter-tabs { display: flex; gap: 8px; overflow-x: auto; padding-bottom: 4px; }
        .filter-tabs::-webkit-scrollbar { height: 3px; }
        .filter-tabs::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 4px; }
        .filter-tab {
            padding: 7px 14px;
            background: rgba(0,229,255,0.06); border: 1px solid rgba(0,229,255,0.2);
            border-radius: 20px; color: var(--text-muted);
            font-size: 12px; font-weight: 600; cursor: pointer;
            transition: all 0.2s ease; white-space: nowrap; flex-shrink: 0;
        }
        .filter-tab:hover { background: rgba(0,229,255,0.12); }
        .filter-tab.active { background: var(--gold-color); border-color: var(--gold-color); color: #0a0f1e; }

        /* Extended txn-icon statuses */
        .txn-icon.approved { background: rgba(59,181,232,0.15); border-color: rgba(59,181,232,0.3); }
        .txn-icon.approved i { color: #3bb5e8; }
        .txn-icon.rejected { background: rgba(220,53,69,0.15); border-color: rgba(220,53,69,0.3); }
        .txn-icon.rejected i { color: #dc3545; }
        .txn-icon.cancelled { background: rgba(108,117,125,0.15); border-color: rgba(108,117,125,0.3); }
        .txn-icon.cancelled i { color: #6c757d; }

        /* s-badge extended */
        .s-badge { display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .s-badge.pending { background: rgba(255,193,7,0.15); color: #ffc107; border: 1px solid rgba(255,193,7,0.3); }
        .s-badge.approved { background: rgba(59,181,232,0.15); color: #3bb5e8; border: 1px solid rgba(59,181,232,0.3); }
        .s-badge.completed { background: rgba(40,167,69,0.15); color: #28a745; border: 1px solid rgba(40,167,69,0.3); }
        .s-badge.rejected { background: rgba(220,53,69,0.15); color: #dc3545; border: 1px solid rgba(220,53,69,0.3); }
        .s-badge.cancelled { background: rgba(108,117,125,0.15); color: #6c757d; border: 1px solid rgba(108,117,125,0.3); }

        /* Detail box */
        .txn-detail-box {
            background: rgba(0,229,255,0.04); border: 1px solid var(--border-color);
            border-radius: 8px; padding: 10px 12px;
        }
        .detail-row { display: flex; justify-content: space-between; align-items: center; padding: 4px 0; }
        .detail-row:not(:last-child) { border-bottom: 1px solid rgba(255,255,255,0.04); }

        /* Cancel button */
        .btn-cancel-withdrawal {
            width: 100%; padding: 8px 12px;
            background: rgba(220,53,69,0.08); border: 1px solid rgba(220,53,69,0.3);
            border-radius: 8px; color: #dc3545; font-size: 13px; font-weight: 600;
            cursor: pointer; transition: all 0.2s ease;
        }
        .btn-cancel-withdrawal:hover { background: rgba(220,53,69,0.15); }
    </style>

    <script>
        const translations = { confirmCancelWithdrawal: "{{ __('app.confirm_cancel_withdrawal') }}" };

        function filterTransactions(status, event) {
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            event.target.classList.add('active');
            document.querySelectorAll('.txn-row').forEach(item => {
                item.style.display = (status === 'all' || item.dataset.status === status) ? 'flex' : 'none';
            });
        }

        function cancelWithdrawal(reference) {
            if (!confirm(translations.confirmCancelWithdrawal)) return;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/member/withdraw/cancel/' + reference;
            const csrf = document.createElement('input');
            csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
            const method = document.createElement('input');
            method.type = 'hidden'; method.name = '_method'; method.value = 'DELETE';
            form.appendChild(csrf); form.appendChild(method);
            document.body.appendChild(form); form.submit();
        }

        @if (session('success')) alert('{{ session('success') }}'); @endif
        @if (session('error')) alert('{{ session('error') }}'); @endif
    </script>
@endsection
