@extends('member.layouts.app')
@section('content')
<div class="scrollable-content">

    <!-- Header -->
    <div class="wh-header">
        <a href="{{ route('member.withdraw.index') }}" class="wh-back-btn">
            <i class="bi bi-chevron-left"></i>
        </a>
        <div class="wh-header-center">
            <h5 class="wh-title">{{ __('app.withdrawal_history') }}</h5>
            <p class="wh-subtitle">{{ __('app.view_withdrawal_history') }}</p>
        </div>
        <div style="width:36px;"></div>
    </div>

    <!-- Stats -->
    <div class="wh-pad">
        <div class="wh-stats-row">
            <div class="wh-stat">
                <div class="wh-stat-val amber">{{ $pendingCount }}</div>
                <div class="wh-stat-lbl">{{ __('app.pending') }}</div>
            </div>
            <div class="wh-stat-sep"></div>
            <div class="wh-stat">
                <div class="wh-stat-val green">{{ $completedCount }}</div>
                <div class="wh-stat-lbl">{{ __('app.completed') }}</div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="wh-filter-wrap">
        <div class="wh-filter-tabs">
            <button class="wh-filter-tab active" onclick="whFilter('all', event)">{{ __('app.all') }}</button>
            <button class="wh-filter-tab" onclick="whFilter('pending', event)">{{ __('app.pending') }}</button>
            <button class="wh-filter-tab" onclick="whFilter('approved', event)">{{ __('app.approved') }}</button>
            <button class="wh-filter-tab" onclick="whFilter('rejected', event)">{{ __('app.rejected') }}</button>
            <button class="wh-filter-tab" onclick="whFilter('cancelled', event)">{{ __('app.cancelled') }}</button>
            <button class="wh-filter-tab" onclick="whFilter('completed', event)">{{ __('app.completed') }}</button>
        </div>
    </div>

    <!-- Transaction List -->
    <div class="wh-list">
        @forelse($transactions as $transaction)
            @php
                $iconClass = match($transaction->status) {
                    'completed' => 'bi-check-circle',
                    'approved'  => 'bi-hourglass-split',
                    'rejected'  => 'bi-x-circle',
                    'cancelled' => 'bi-slash-circle',
                    default     => 'bi-clock-history',
                };
            @endphp
            <div class="wh-card" data-status="{{ $transaction->status }}">
                <div class="wh-card-head">
                    <div class="wh-icon {{ $transaction->status }}">
                        <i class="bi {{ $iconClass }}"></i>
                    </div>
                    <div class="wh-card-main">
                        <div class="wh-card-name">{{ __('app.withdrawal') }}</div>
                        <div class="wh-card-ref">{{ $transaction->reference }}</div>
                    </div>
                    <div class="wh-card-right">
                        <div class="wh-amount">-{{ number_format($transaction->total_amount, 2) }} USDT</div>
                        <span class="wh-badge {{ $transaction->status }}">{{ ucfirst(__('app.' . $transaction->status)) }}</span>
                    </div>
                </div>
                <div class="wh-detail">
                    <div class="wh-detail-row">
                        <span class="wh-detail-lbl">{{ __('app.wallet_account') }}</span>
                        <span class="wh-detail-val">{{ $transaction->wallet ? $transaction->wallet->account_name : __('app.na') }}</span>
                    </div>
                    <div class="wh-detail-row">
                        <span class="wh-detail-lbl">{{ __('app.account_number') }}</span>
                        <span class="wh-detail-val">{{ $transaction->wallet ? $transaction->wallet->account_number : __('app.na') }}</span>
                    </div>
                    <div class="wh-detail-row">
                        <span class="wh-detail-lbl">{{ __('app.withdrawal_amount') }}</span>
                        <span class="wh-detail-val">{{ number_format($transaction->total_amount, 2) }} USDT</span>
                    </div>
                    @if ($transaction->withdrawal_fee > 0)
                        <div class="wh-detail-row">
                            <span class="wh-detail-lbl">{{ __('app.fee_5_percent') }}</span>
                            <span class="wh-detail-val red">-{{ number_format($transaction->withdrawal_fee, 2) }} USDT</span>
                        </div>
                    @endif
                    <div class="wh-detail-row">
                        <span class="wh-detail-lbl">{{ __('app.you_receive') }}</span>
                        <span class="wh-detail-val green">{{ number_format($transaction->amount, 2) }} USDT</span>
                    </div>
                    <div class="wh-detail-row">
                        <span class="wh-detail-lbl">{{ __('app.date') }}</span>
                        <span class="wh-detail-val">{{ $transaction->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    @if ($transaction->status === 'completed' && $transaction->updated_at)
                        <div class="wh-detail-row">
                            <span class="wh-detail-lbl">{{ __('app.completed_at') }}</span>
                            <span class="wh-detail-val">{{ $transaction->updated_at->format('d M Y, H:i') }}</span>
                        </div>
                    @endif
                </div>
                @if ($transaction->status === 'pending')
                    <div class="wh-cancel-wrap">
                        <button type="button" class="wh-cancel-btn"
                            onclick="cancelWithdrawal('{{ $transaction->reference }}')">
                            <i class="bi bi-x-circle me-1"></i>{{ __('app.cancel_withdrawal') }}
                        </button>
                    </div>
                @endif
            </div>
        @empty
            <div class="wh-empty">
                <i class="bi bi-inbox wh-empty-icon"></i>
                <p class="text-muted mb-0">{{ __('app.no_withdrawal_history') }}</p>
            </div>
        @endforelse
    </div>

    @if ($transactions->hasPages())
        <div class="wh-pagination">{{ $transactions->links() }}</div>
    @endif

    <div style="height: 24px;"></div>

</div>

@push('styles')
<style>
/* Header */
.wh-header {
    display: flex; align-items: center;
    padding: 16px 20px;
    border-bottom: 1px solid var(--border-color);
}
.wh-back-btn {
    width: 36px; height: 36px; border-radius: 50%;
    background: rgba(255,255,255,0.06); border: 1px solid var(--border-color);
    display: flex; align-items: center; justify-content: center;
    color: var(--text-primary); text-decoration: none; font-size: 16px; flex-shrink: 0;
    transition: background 0.2s;
}
.wh-back-btn:hover { background: rgba(255,255,255,0.1); }
.wh-header-center { flex: 1; text-align: center; padding: 0 10px; }
.wh-title    { color: #fff; font-size: 16px; font-weight: 700; margin: 0; }
.wh-subtitle { color: var(--text-muted); font-size: 11px; margin: 3px 0 0; }

/* Padding */
.wh-pad { padding: 16px 20px; }

/* Stats */
.wh-stats-row {
    display: flex; align-items: center;
    background: rgba(255,255,255,0.03); border: 1px solid var(--border-color);
    border-radius: 14px; padding: 16px 20px;
}
.wh-stat { flex: 1; text-align: center; }
.wh-stat-val { font-size: 22px; font-weight: 800; }
.wh-stat-val.amber { color: #fbbf24; }
.wh-stat-val.green { color: #22c55e; }
.wh-stat-lbl { color: var(--text-muted); font-size: 11px; margin-top: 3px; }
.wh-stat-sep { width: 1px; height: 38px; background: var(--border-color); margin: 0 16px; }

/* Filter Tabs */
.wh-filter-wrap { padding: 0 20px 16px; }
.wh-filter-tabs { display: flex; gap: 8px; overflow-x: auto; padding-bottom: 4px; }
.wh-filter-tabs::-webkit-scrollbar { height: 3px; }
.wh-filter-tabs::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 4px; }
.wh-filter-tab {
    padding: 7px 14px;
    background: rgba(255,255,255,0.04); border: 1px solid var(--border-color);
    border-radius: 20px; color: var(--text-muted);
    font-size: 12px; font-weight: 600; cursor: pointer;
    transition: all 0.2s; white-space: nowrap; flex-shrink: 0;
}
.wh-filter-tab.active { background: var(--gold-color); border-color: var(--gold-color); color: #0a0f1e; }

/* Transaction List */
.wh-list { padding: 0 20px; display: flex; flex-direction: column; gap: 10px; }
.wh-card {
    background: rgba(255,255,255,0.03); border: 1px solid var(--border-color);
    border-radius: 12px; overflow: hidden;
}
.wh-card-head { display: flex; align-items: flex-start; gap: 12px; padding: 14px; }

/* Status Icon */
.wh-icon {
    width: 40px; height: 40px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; border: 1px solid; font-size: 18px;
}
.wh-icon.pending   { background: rgba(251,191,36,0.15);  border-color: rgba(251,191,36,0.3);  color: #fbbf24; }
.wh-icon.approved  { background: rgba(59,181,232,0.15);  border-color: rgba(59,181,232,0.3);  color: #3bb5e8; }
.wh-icon.completed { background: rgba(34,197,94,0.15);   border-color: rgba(34,197,94,0.3);   color: #22c55e; }
.wh-icon.rejected  { background: rgba(239,68,68,0.15);   border-color: rgba(239,68,68,0.3);   color: #ef4444; }
.wh-icon.cancelled { background: rgba(108,117,125,0.15); border-color: rgba(108,117,125,0.3); color: #6c757d; }

.wh-card-main { flex: 1; min-width: 0; }
.wh-card-name { color: #fff; font-size: 14px; font-weight: 700; }
.wh-card-ref  { color: var(--text-muted); font-size: 11px; margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

.wh-card-right { text-align: right; flex-shrink: 0; }
.wh-amount { color: #ef4444; font-size: 15px; font-weight: 800; }

/* Status Badge */
.wh-badge {
    display: inline-block; padding: 3px 8px; border-radius: 4px;
    font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px;
}
.wh-badge.pending   { background: rgba(251,191,36,0.15);  color: #fbbf24; border: 1px solid rgba(251,191,36,0.3);  }
.wh-badge.approved  { background: rgba(59,181,232,0.15);  color: #3bb5e8; border: 1px solid rgba(59,181,232,0.3);  }
.wh-badge.completed { background: rgba(34,197,94,0.15);   color: #22c55e; border: 1px solid rgba(34,197,94,0.3);   }
.wh-badge.rejected  { background: rgba(239,68,68,0.15);   color: #ef4444; border: 1px solid rgba(239,68,68,0.3);   }
.wh-badge.cancelled { background: rgba(108,117,125,0.15); color: #6c757d; border: 1px solid rgba(108,117,125,0.3); }

/* Detail Section */
.wh-detail { background: rgba(0,229,255,0.03); border-top: 1px solid var(--border-color); padding: 10px 14px; }
.wh-detail-row { display: flex; justify-content: space-between; align-items: center; padding: 5px 0; }
.wh-detail-row:not(:last-child) { border-bottom: 1px solid rgba(255,255,255,0.04); }
.wh-detail-lbl { color: var(--text-muted); font-size: 12px; }
.wh-detail-val { color: #fff; font-size: 12px; font-weight: 600; }
.wh-detail-val.green { color: #22c55e; }
.wh-detail-val.red   { color: #ef4444; }

/* Cancel Button */
.wh-cancel-wrap { padding: 0 14px 14px; }
.wh-cancel-btn {
    width: 100%; padding: 9px 12px;
    background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.3);
    border-radius: 8px; color: #ef4444; font-size: 13px; font-weight: 600;
    cursor: pointer; transition: all 0.2s;
}
.wh-cancel-btn:hover { background: rgba(239,68,68,0.15); }

/* Empty */
.wh-empty { text-align: center; padding: 60px 20px; }
.wh-empty-icon { font-size: 56px; color: var(--text-muted); opacity: 0.3; display: block; margin-bottom: 12px; }

/* Pagination */
.wh-pagination { padding: 16px 20px; }
</style>
@endpush

@push('scripts')
<script>
const whTranslations = { confirmCancelWithdrawal: "{{ __('app.confirm_cancel_withdrawal') }}" };

function whFilter(status, event) {
    document.querySelectorAll('.wh-filter-tab').forEach(t => t.classList.remove('active'));
    event.target.classList.add('active');
    document.querySelectorAll('.wh-card').forEach(card => {
        card.style.display = (status === 'all' || card.dataset.status === status) ? '' : 'none';
    });
}

function cancelWithdrawal(reference) {
    if (!confirm(whTranslations.confirmCancelWithdrawal)) return;
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
@if (session('error'))   alert('{{ session('error') }}');   @endif
</script>
@endpush
@endsection
