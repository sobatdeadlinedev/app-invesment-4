@extends('member.layouts.app')
@section('content')
<div class="scrollable-content">

    <!-- Header -->
    <div class="dh-header">
        <a href="{{ route('member.deposit.index') }}" class="dh-back-btn">
            <i class="bi bi-chevron-left"></i>
        </a>
        <div class="dh-header-center">
            <h5 class="dh-title">{{ __('app.deposit_history') }}</h5>
            <p class="dh-subtitle">{{ __('app.view_deposit_history') }}</p>
        </div>
        <div style="width:36px;"></div>
    </div>

    <!-- Stats -->
    <div class="dh-pad">
        <div class="dh-stats-row">
            <div class="dh-stat">
                <div class="dh-stat-val amber">{{ $pendingCount }}</div>
                <div class="dh-stat-lbl">{{ __('app.pending') }}</div>
            </div>
            <div class="dh-stat-sep"></div>
            <div class="dh-stat">
                <div class="dh-stat-val green">{{ $completedCount }}</div>
                <div class="dh-stat-lbl">{{ __('app.completed') }}</div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="dh-filter-wrap">
        <div class="dh-filter-tabs">
            <button class="dh-filter-tab active" onclick="dhFilter('all', event)">{{ __('app.all') }}</button>
            <button class="dh-filter-tab" onclick="dhFilter('pending', event)">{{ __('app.pending') }}</button>
            <button class="dh-filter-tab" onclick="dhFilter('approved', event)">{{ __('app.approved') }}</button>
            <button class="dh-filter-tab" onclick="dhFilter('rejected', event)">{{ __('app.rejected') }}</button>
            <button class="dh-filter-tab" onclick="dhFilter('completed', event)">{{ __('app.completed') }}</button>
        </div>
    </div>

    <!-- Transaction List -->
    <div class="dh-list">
        @forelse($transactions as $transaction)
            @php
                $iconClass = match($transaction->status) {
                    'completed' => 'bi-check-circle',
                    'approved'  => 'bi-hourglass-split',
                    'rejected'  => 'bi-x-circle',
                    default     => 'bi-clock-history',
                };
            @endphp
            <div class="dh-card" data-status="{{ $transaction->status }}">
                <div class="dh-card-head">
                    <div class="dh-icon {{ $transaction->status }}">
                        <i class="bi {{ $iconClass }}"></i>
                    </div>
                    <div class="dh-card-main">
                        <div class="dh-card-name">{{ __('app.deposit') }}</div>
                        <div class="dh-card-ref">{{ $transaction->reference }}</div>
                    </div>
                    <div class="dh-card-right">
                        <div class="dh-amount">+{{ number_format($transaction->amount, 2) }} USDT</div>
                        <span class="dh-badge {{ $transaction->status }}">{{ __('app.' . $transaction->status) }}</span>
                    </div>
                </div>
                <div class="dh-detail">
                    <div class="dh-detail-row">
                        <span class="dh-detail-lbl">{{ __('app.amount') }}</span>
                        <span class="dh-detail-val green">{{ number_format($transaction->total_amount, 2) }} USDT</span>
                    </div>
                    <div class="dh-detail-row">
                        <span class="dh-detail-lbl">{{ __('app.date') }}</span>
                        <span class="dh-detail-val">{{ $transaction->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    @if (in_array($transaction->status, ['approved', 'completed']) && $transaction->updated_at)
                        <div class="dh-detail-row">
                            <span class="dh-detail-lbl">{{ __('app.processed_at') }}</span>
                            <span class="dh-detail-val">{{ $transaction->updated_at->format('d M Y, H:i') }}</span>
                        </div>
                    @endif
                    @if ($transaction->payment_proof)
                        <div class="dh-detail-row">
                            <span class="dh-detail-lbl">{{ __('app.payment_proof') }}</span>
                            <button type="button" class="dh-proof-btn"
                                onclick="viewProof('{{ asset('storage/' . $transaction->payment_proof) }}')">
                                <i class="bi bi-eye me-1"></i>{{ __('app.view') }}
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="dh-empty">
                <i class="bi bi-inbox dh-empty-icon"></i>
                <p class="text-muted mb-0">{{ __('app.no_deposit_history') }}</p>
            </div>
        @endforelse
    </div>

    @if ($transactions->hasPages())
        <div class="dh-pagination">{{ $transactions->links() }}</div>
    @endif

    <div style="height: 24px;"></div>

</div>

<!-- Payment Proof Modal -->
<div class="modal fade" id="dhProofModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-color: var(--card-dark); border: 1px solid var(--border-color);">
            <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                <h5 class="modal-title text-white">{{ __('app.payment_proof') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="dhProofImage" src="" alt="{{ __('app.payment_proof') }}"
                    style="max-width: 100%; border-radius: 8px;">
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Header */
.dh-header {
    display: flex; align-items: center;
    padding: 16px 20px;
    border-bottom: 1px solid var(--border-color);
}
.dh-back-btn {
    width: 36px; height: 36px; border-radius: 50%;
    background: rgba(255,255,255,0.06); border: 1px solid var(--border-color);
    display: flex; align-items: center; justify-content: center;
    color: var(--text-primary); text-decoration: none; font-size: 16px; flex-shrink: 0;
    transition: background 0.2s;
}
.dh-back-btn:hover { background: rgba(255,255,255,0.1); }
.dh-header-center { flex: 1; text-align: center; padding: 0 10px; }
.dh-title    { color: #fff; font-size: 16px; font-weight: 700; margin: 0; }
.dh-subtitle { color: var(--text-muted); font-size: 11px; margin: 3px 0 0; }

/* Padding */
.dh-pad { padding: 16px 20px; }

/* Stats */
.dh-stats-row {
    display: flex; align-items: center;
    background: rgba(255,255,255,0.03); border: 1px solid var(--border-color);
    border-radius: 14px; padding: 16px 20px;
}
.dh-stat { flex: 1; text-align: center; }
.dh-stat-val { font-size: 22px; font-weight: 800; }
.dh-stat-val.amber { color: #fbbf24; }
.dh-stat-val.green { color: #22c55e; }
.dh-stat-lbl { color: var(--text-muted); font-size: 11px; margin-top: 3px; }
.dh-stat-sep { width: 1px; height: 38px; background: var(--border-color); margin: 0 16px; }

/* Filter Tabs */
.dh-filter-wrap { padding: 0 20px 16px; }
.dh-filter-tabs { display: flex; gap: 8px; overflow-x: auto; padding-bottom: 4px; }
.dh-filter-tabs::-webkit-scrollbar { height: 3px; }
.dh-filter-tabs::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 4px; }
.dh-filter-tab {
    padding: 7px 14px;
    background: rgba(255,255,255,0.04); border: 1px solid var(--border-color);
    border-radius: 20px; color: var(--text-muted);
    font-size: 12px; font-weight: 600; cursor: pointer;
    transition: all 0.2s; white-space: nowrap; flex-shrink: 0;
}
.dh-filter-tab.active { background: var(--gold-color); border-color: var(--gold-color); color: #0a0f1e; }

/* Transaction List */
.dh-list { padding: 0 20px; display: flex; flex-direction: column; gap: 10px; }
.dh-card {
    background: rgba(255,255,255,0.03); border: 1px solid var(--border-color);
    border-radius: 12px; overflow: hidden;
}
.dh-card-head { display: flex; align-items: flex-start; gap: 12px; padding: 14px; }

/* Status Icon */
.dh-icon {
    width: 40px; height: 40px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; border: 1px solid; font-size: 18px;
}
.dh-icon.pending   { background: rgba(251,191,36,0.15);  border-color: rgba(251,191,36,0.3);  color: #fbbf24; }
.dh-icon.approved  { background: rgba(59,181,232,0.15);  border-color: rgba(59,181,232,0.3);  color: #3bb5e8; }
.dh-icon.completed { background: rgba(34,197,94,0.15);   border-color: rgba(34,197,94,0.3);   color: #22c55e; }
.dh-icon.rejected  { background: rgba(239,68,68,0.15);   border-color: rgba(239,68,68,0.3);   color: #ef4444; }

.dh-card-main { flex: 1; min-width: 0; }
.dh-card-name { color: #fff; font-size: 14px; font-weight: 700; }
.dh-card-ref  { color: var(--text-muted); font-size: 11px; margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

.dh-card-right { text-align: right; flex-shrink: 0; }
.dh-amount { color: #22c55e; font-size: 15px; font-weight: 800; }

/* Status Badge */
.dh-badge {
    display: inline-block; padding: 3px 8px; border-radius: 4px;
    font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px;
}
.dh-badge.pending   { background: rgba(251,191,36,0.15);  color: #fbbf24; border: 1px solid rgba(251,191,36,0.3);  }
.dh-badge.approved  { background: rgba(59,181,232,0.15);  color: #3bb5e8; border: 1px solid rgba(59,181,232,0.3);  }
.dh-badge.completed { background: rgba(34,197,94,0.15);   color: #22c55e; border: 1px solid rgba(34,197,94,0.3);   }
.dh-badge.rejected  { background: rgba(239,68,68,0.15);   color: #ef4444; border: 1px solid rgba(239,68,68,0.3);   }

/* Detail Section */
.dh-detail { background: rgba(0,229,255,0.03); border-top: 1px solid var(--border-color); padding: 10px 14px; }
.dh-detail-row { display: flex; justify-content: space-between; align-items: center; padding: 5px 0; }
.dh-detail-row:not(:last-child) { border-bottom: 1px solid rgba(255,255,255,0.04); }
.dh-detail-lbl { color: var(--text-muted); font-size: 12px; }
.dh-detail-val { color: #fff; font-size: 12px; font-weight: 600; }
.dh-detail-val.green { color: #22c55e; }

/* View Proof Button */
.dh-proof-btn {
    padding: 3px 10px;
    background: rgba(59,181,232,0.1); border: 1px solid rgba(59,181,232,0.3);
    border-radius: 4px; color: #3bb5e8; font-size: 11px; font-weight: 600;
    cursor: pointer; transition: all 0.2s;
}
.dh-proof-btn:hover { background: rgba(59,181,232,0.2); }

/* Empty */
.dh-empty { text-align: center; padding: 60px 20px; }
.dh-empty-icon { font-size: 56px; color: var(--text-muted); opacity: 0.3; display: block; margin-bottom: 12px; }

/* Pagination */
.dh-pagination { padding: 16px 20px; }
</style>
@endpush

@push('scripts')
<script>
function dhFilter(status, event) {
    document.querySelectorAll('.dh-filter-tab').forEach(t => t.classList.remove('active'));
    event.target.classList.add('active');
    document.querySelectorAll('.dh-card').forEach(card => {
        card.style.display = (status === 'all' || card.dataset.status === status) ? '' : 'none';
    });
}

function viewProof(imageUrl) {
    document.getElementById('dhProofImage').src = imageUrl;
    new bootstrap.Modal(document.getElementById('dhProofModal')).show();
}

@if (session('success')) alert('{{ session('success') }}'); @endif
@if (session('error'))   alert('{{ session('error') }}');   @endif
</script>
@endpush
@endsection
