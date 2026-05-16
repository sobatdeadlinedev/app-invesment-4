@extends('member.layouts.app')
@section('content')
<div class="scrollable-content">

    <!-- Header -->
    <div class="dh-header">
        <a href="{{ route('member.deposit.index') }}" class="dh-back-btn">
            <i class="bi bi-chevron-left"></i>
        </a>
        <h5 class="dh-title">{{ __('app.deposit_history') }}</h5>
        <button class="dh-filter-btn" id="dh-filter-btn" onclick="dhOpenFilter()">
            <i class="bi bi-sliders2"></i>
        </button>
    </div>

    <!-- Stat List -->
    <div class="dh-stat-list">
        <div class="dh-stat-row">
            <div class="dh-stat-dot" style="background:#fbbf24;box-shadow:0 0 6px rgba(251,191,36,0.5);"></div>
            <span class="dh-stat-lbl">{{ __('app.pending') }}</span>
            <span class="dh-stat-val">{{ $pendingCount }}</span>
        </div>
        <div class="dh-stat-row">
            <div class="dh-stat-dot" style="background:#22c55e;box-shadow:0 0 6px rgba(34,197,94,0.5);"></div>
            <span class="dh-stat-lbl">{{ __('app.completed') }}</span>
            <span class="dh-stat-val">{{ $completedCount }}</span>
        </div>
        <div class="dh-stat-row" style="border-bottom:none;">
            <div class="dh-stat-dot" style="background:rgba(255,255,255,0.3);"></div>
            <span class="dh-stat-lbl">{{ __('app.total') }}</span>
            <span class="dh-stat-val">{{ $transactions->total() }}</span>
        </div>
    </div>

    <!-- Active Filter Bar -->
    <div class="dh-active-bar" id="dh-active-bar" style="display:none;">
        <i class="bi bi-funnel-fill"></i>
        <span id="dh-active-label"></span>
        <button onclick="dhResetFilter()" class="dh-active-clear"><i class="bi bi-x"></i></button>
    </div>

    <!-- Transaction Cards -->
    @if($transactions->isEmpty())
        <div class="dh-empty">
            <div class="dh-empty-inner">
                <i class="bi bi-receipt-cutoff dh-empty-ico"></i>
                <div class="dh-empty-h">{{ __('app.no_deposit_history') }}</div>
                <a href="{{ route('member.deposit.index') }}" class="dh-empty-cta">
                    <i class="bi bi-plus-circle me-2"></i>{{ __('app.deposit') }}
                </a>
            </div>
        </div>
    @else
        <div class="dh-cards-wrap">
            @foreach($transactions as $transaction)
                @php
                    $iconClass = match($transaction->status) {
                        'completed' => 'bi-check-circle-fill',
                        'approved'  => 'bi-hourglass-split',
                        'rejected'  => 'bi-x-circle-fill',
                        default     => 'bi-clock-history',
                    };
                @endphp
                <div class="dh-card" data-status="{{ $transaction->status }}" onclick="dhToggle(this)">
                    <div class="dh-card-main">
                        <div class="dh-card-ico {{ $transaction->status }}">
                            <i class="bi {{ $iconClass }}"></i>
                        </div>
                        <div class="dh-card-info">
                            <div class="dh-card-type">{{ __('app.deposit') }}</div>
                            <div class="dh-card-time">{{ $transaction->created_at->format('d M Y · H:i') }}</div>
                        </div>
                        <div class="dh-card-right">
                            <div class="dh-card-amount">+{{ number_format($transaction->amount, 2) }} <span class="dh-usdt">USDT</span></div>
                            <span class="dh-card-badge {{ $transaction->status }}">{{ __('app.' . $transaction->status) }}</span>
                        </div>
                        <i class="bi bi-chevron-down dh-card-chev"></i>
                    </div>
                    <div class="dh-card-detail">
                        <div class="dh-dr">
                            <span class="dh-dl">{{ __('app.reference') }}</span>
                            <span class="dh-dv mono">{{ $transaction->reference }}</span>
                        </div>
                        <div class="dh-dr">
                            <span class="dh-dl">{{ __('app.amount') }}</span>
                            <span class="dh-dv green">{{ number_format($transaction->total_amount, 2) }} USDT</span>
                        </div>
                        <div class="dh-dr">
                            <span class="dh-dl">{{ __('app.date') }}</span>
                            <span class="dh-dv">{{ $transaction->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        @if(in_array($transaction->status, ['approved', 'completed']) && $transaction->updated_at)
                        <div class="dh-dr">
                            <span class="dh-dl">{{ __('app.processed_at') }}</span>
                            <span class="dh-dv">{{ $transaction->updated_at->format('d M Y, H:i') }}</span>
                        </div>
                        @endif
                        @if($transaction->payment_proof)
                        <div class="dh-dr">
                            <span class="dh-dl">{{ __('app.payment_proof') }}</span>
                            <button type="button" class="dh-proof-btn"
                                onclick="event.stopPropagation(); viewProof('{{ asset('storage/' . $transaction->payment_proof) }}')">
                                <i class="bi bi-eye me-1"></i>{{ __('app.view') }}
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if($transactions->hasPages())
        <div class="dh-pagination">{{ $transactions->links() }}</div>
    @endif

    <div style="height:24px;"></div>

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
                    style="max-width:100%; border-radius:8px;">
            </div>
        </div>
    </div>
</div>

<!-- Filter Bottom Sheet -->
<div class="dh-overlay" id="dh-overlay" onclick="dhCloseFilter()"></div>
<div class="dh-sheet" id="dh-sheet">
    <div class="dh-sheet-handle"></div>
    <div class="dh-sheet-title">Filter</div>
    <div class="dh-sheet-opts">
        <div class="dh-sheet-opt active" data-val="all" onclick="dhPickFilter(this)">
            <div class="dh-opt-radio"></div>
            <div class="dh-opt-body"><div class="dh-opt-name">{{ __('app.all') }}</div></div>
            <i class="bi bi-check2 dh-opt-check"></i>
        </div>
        <div class="dh-sheet-opt" data-val="pending" onclick="dhPickFilter(this)">
            <div class="dh-opt-radio"></div>
            <div class="dh-opt-body"><div class="dh-opt-name">{{ __('app.pending') }}</div></div>
            <i class="bi bi-check2 dh-opt-check"></i>
        </div>
        <div class="dh-sheet-opt" data-val="approved" onclick="dhPickFilter(this)">
            <div class="dh-opt-radio"></div>
            <div class="dh-opt-body"><div class="dh-opt-name">{{ __('app.approved') }}</div></div>
            <i class="bi bi-check2 dh-opt-check"></i>
        </div>
        <div class="dh-sheet-opt" data-val="rejected" onclick="dhPickFilter(this)">
            <div class="dh-opt-radio"></div>
            <div class="dh-opt-body"><div class="dh-opt-name">{{ __('app.rejected') }}</div></div>
            <i class="bi bi-check2 dh-opt-check"></i>
        </div>
        <div class="dh-sheet-opt" data-val="completed" onclick="dhPickFilter(this)">
            <div class="dh-opt-radio"></div>
            <div class="dh-opt-body"><div class="dh-opt-name">{{ __('app.completed') }}</div></div>
            <i class="bi bi-check2 dh-opt-check"></i>
        </div>
    </div>
</div>

@push('styles')
<style>
/* ── Header ── */
.dh-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid var(--border-color);
}
.dh-back-btn {
    width: 36px; height: 36px; border-radius: 50%;
    background: rgba(255,255,255,0.06); border: 1px solid var(--border-color);
    display: flex; align-items: center; justify-content: center;
    color: #fff; text-decoration: none; font-size: 16px;
}
.dh-back-btn:hover { background: rgba(255,255,255,0.1); color: #fff; }
.dh-title { color: #fff; font-size: 16px; font-weight: 700; margin: 0; }
.dh-filter-btn {
    width: 36px; height: 36px; border-radius: 50%;
    background: rgba(255,255,255,0.06); border: 1px solid var(--border-color);
    display: flex; align-items: center; justify-content: center;
    color: var(--text-muted); font-size: 15px; cursor: pointer;
    transition: all 0.2s; position: relative;
}
.dh-filter-btn:hover { background: rgba(255,255,255,0.1); }
.dh-filter-btn.has-filter { border-color: var(--gold-color); color: var(--gold-color); background: rgba(0,229,255,0.08); }
.dh-filter-dot {
    position: absolute; top: 5px; right: 5px;
    width: 8px; height: 8px; border-radius: 50%;
    background: var(--gold-color); border: 1.5px solid #0a0f1e;
}

/* ── Stat List ── */
.dh-stat-list {
    margin: 16px 20px 0;
    background: rgba(255,255,255,0.03);
    border: 1px solid var(--border-color);
    border-radius: 14px; overflow: hidden;
}
.dh-stat-row {
    display: flex; align-items: center; gap: 12px;
    padding: 13px 16px;
    border-bottom: 1px solid var(--border-color);
}
.dh-stat-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.dh-stat-lbl { flex: 1; color: var(--text-primary); font-size: 14px; font-weight: 500; }
.dh-stat-val { color: #fff; font-size: 14px; font-weight: 700; }
.dh-stat-chev { color: var(--text-muted); font-size: 12px; margin-left: 6px; }

/* ── Active Filter Bar ── */
.dh-active-bar {
    display: flex; align-items: center; gap: 8px;
    margin: 14px 20px 0;
    padding: 8px 14px;
    background: rgba(0,229,255,0.06); border: 1px solid rgba(0,229,255,0.2);
    border-radius: 8px; font-size: 12px; color: var(--gold-color); font-weight: 600;
}
.dh-active-bar span { flex: 1; }
.dh-active-clear {
    background: none; border: none; color: var(--text-muted);
    font-size: 16px; cursor: pointer; padding: 0; line-height: 1;
}
.dh-active-clear:hover { color: #ef4444; }

/* ── Cards Wrap ── */
.dh-cards-wrap { padding: 16px 20px 0; display: flex; flex-direction: column; gap: 10px; }

/* ── Individual Card ── */
.dh-card {
    background: rgba(255,255,255,0.03);
    border: 1px solid var(--border-color);
    border-radius: 14px; cursor: pointer;
    transition: background 0.15s; overflow: hidden;
}
.dh-card:hover { background: rgba(255,255,255,0.05); }
.dh-card-main { display: flex; align-items: center; gap: 12px; padding: 14px 16px; }
.dh-card-ico {
    width: 42px; height: 42px; border-radius: 12px; border: 1px solid;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; flex-shrink: 0;
}
.dh-card-ico.pending   { background: rgba(251,191,36,0.12);  border-color: rgba(251,191,36,0.3);  color: #fbbf24; }
.dh-card-ico.approved  { background: rgba(59,181,232,0.12);  border-color: rgba(59,181,232,0.3);  color: #3bb5e8; }
.dh-card-ico.completed { background: rgba(34,197,94,0.12);   border-color: rgba(34,197,94,0.3);   color: #22c55e; }
.dh-card-ico.rejected  { background: rgba(239,68,68,0.12);   border-color: rgba(239,68,68,0.3);   color: #ef4444; }
.dh-card-info { flex: 1; min-width: 0; }
.dh-card-type { color: #fff; font-size: 13px; font-weight: 600; }
.dh-card-time { color: var(--text-muted); font-size: 11px; margin-top: 2px; }
.dh-card-right { text-align: right; flex-shrink: 0; }
.dh-card-amount { color: #22c55e; font-size: 14px; font-weight: 800; white-space: nowrap; }
.dh-usdt { font-size: 10px; font-weight: 600; opacity: 0.75; }
.dh-card-badge {
    display: inline-block; padding: 2px 8px; border-radius: 4px;
    font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px;
}
.dh-card-badge.pending   { background: rgba(251,191,36,0.12);  color: #fbbf24; border: 1px solid rgba(251,191,36,0.3);  }
.dh-card-badge.approved  { background: rgba(59,181,232,0.12);  color: #3bb5e8; border: 1px solid rgba(59,181,232,0.3);  }
.dh-card-badge.completed { background: rgba(34,197,94,0.12);   color: #22c55e; border: 1px solid rgba(34,197,94,0.3);   }
.dh-card-badge.rejected  { background: rgba(239,68,68,0.12);   color: #ef4444; border: 1px solid rgba(239,68,68,0.3);   }
.dh-card-chev { color: var(--text-muted); font-size: 12px; flex-shrink: 0; transition: transform 0.25s; margin-left: 4px; }
.dh-card.expanded .dh-card-chev { transform: rotate(180deg); }

/* ── Card Detail ── */
.dh-card-detail {
    display: none;
    background: rgba(0,229,255,0.03); border-top: 1px solid var(--border-color);
    padding: 10px 16px;
}
.dh-card.expanded .dh-card-detail { display: block; }
.dh-dr { display: flex; justify-content: space-between; align-items: center; padding: 6px 0; }
.dh-dr:not(:last-child) { border-bottom: 1px solid rgba(255,255,255,0.04); }
.dh-dl { color: var(--text-muted); font-size: 12px; }
.dh-dv { color: #fff; font-size: 12px; font-weight: 600; }
.dh-dv.green { color: #22c55e; }
.dh-dv.mono { font-family: monospace; font-size: 11px; }
.dh-proof-btn {
    padding: 4px 10px;
    background: rgba(59,181,232,0.1); border: 1px solid rgba(59,181,232,0.3);
    border-radius: 4px; color: #3bb5e8; font-size: 11px; font-weight: 600;
    cursor: pointer; transition: background 0.2s;
}
.dh-proof-btn:hover { background: rgba(59,181,232,0.2); }

/* ── Empty State ── */
.dh-empty { padding: 20px; }
.dh-empty-inner {
    border: 1.5px dashed rgba(0,229,255,0.2);
    border-radius: 18px;
    padding: 48px 24px;
    text-align: center;
}
.dh-empty-ico { font-size: 52px; color: var(--gold-color); opacity: 0.35; display: block; margin-bottom: 18px; }
.dh-empty-h { color: #fff; font-size: 16px; font-weight: 700; margin-bottom: 8px; }
.dh-empty-sub { color: var(--text-muted); font-size: 13px; margin-bottom: 26px; line-height: 1.6; }
.dh-empty-cta {
    display: inline-flex; align-items: center; padding: 12px 28px;
    background: linear-gradient(135deg, var(--gold-color), #00b8d4);
    border-radius: 25px; color: #0a0f1e; font-size: 14px; font-weight: 700;
    text-decoration: none; transition: all 0.25s;
}
.dh-empty-cta:hover { transform: translateY(-1px); opacity: 0.9; color: #0a0f1e; }

/* ── Pagination ── */
.dh-pagination { padding: 16px 20px; }

/* ── Bottom Sheet Overlay ── */
.dh-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.55); z-index: 1050;
}
.dh-overlay.open { display: block; }

/* ── Bottom Sheet ── */
.dh-sheet {
    position: fixed; bottom: 0; left: 0; right: 0;
    background: #0e1929;
    border-top: 1px solid var(--border-color);
    border-radius: 20px 20px 0 0;
    padding: 12px 20px 70px;
    z-index: 1051;
    transform: translateY(100%);
    transition: transform 0.3s cubic-bezier(0.4,0,0.2,1);
    max-height: 70vh; overflow-y: auto;
}
.dh-sheet.open { transform: translateY(0); }
.dh-sheet-handle {
    width: 36px; height: 4px; border-radius: 2px;
    background: rgba(255,255,255,0.15);
    margin: 0 auto 18px;
}
.dh-sheet-title { color: #fff; font-size: 15px; font-weight: 700; margin-bottom: 4px; }
.dh-sheet-opt {
    display: flex; align-items: center; gap: 14px;
    padding: 14px 0;
    border-bottom: 1px solid var(--border-color);
    cursor: pointer;
}
.dh-sheet-opt:last-child { border-bottom: none; }
.dh-opt-radio {
    width: 20px; height: 20px; border-radius: 50%;
    border: 2px solid var(--border-color);
    flex-shrink: 0; transition: all 0.2s;
}
.dh-sheet-opt.active .dh-opt-radio {
    border-color: var(--gold-color);
    background: var(--gold-color);
    box-shadow: 0 0 0 3px rgba(0,229,255,0.18);
}
.dh-opt-body { flex: 1; }
.dh-opt-name { color: #fff; font-size: 14px; font-weight: 600; }
.dh-opt-check { color: var(--gold-color); font-size: 17px; opacity: 0; transition: opacity 0.2s; }
.dh-sheet-opt.active .dh-opt-check { opacity: 1; }
</style>
@endpush

@push('scripts')
<script>
const dhFilterLabels = {
    all:       '{{ __("app.all") }}',
    pending:   '{{ __("app.pending") }}',
    approved:  '{{ __("app.approved") }}',
    rejected:  '{{ __("app.rejected") }}',
    completed: '{{ __("app.completed") }}',
};

function dhToggle(el) { el.classList.toggle('expanded'); }

function dhOpenFilter() {
    document.getElementById('dh-overlay').classList.add('open');
    document.getElementById('dh-sheet').classList.add('open');
}
function dhCloseFilter() {
    document.getElementById('dh-overlay').classList.remove('open');
    document.getElementById('dh-sheet').classList.remove('open');
}

function dhPickFilter(optEl) {
    document.querySelectorAll('.dh-sheet-opt').forEach(o => o.classList.remove('active'));
    optEl.classList.add('active');
    dhApplyFilter(optEl.dataset.val);
    setTimeout(dhCloseFilter, 180);
}

function dhApplyFilter(status) {
    document.querySelectorAll('.dh-card').forEach(card => {
        card.style.display = (status === 'all' || card.dataset.status === status) ? '' : 'none';
    });

    const btn   = document.getElementById('dh-filter-btn');
    const bar   = document.getElementById('dh-active-bar');
    const label = document.getElementById('dh-active-label');

    if (status !== 'all') {
        btn.classList.add('has-filter');
        if (!btn.querySelector('.dh-filter-dot')) {
            const dot = document.createElement('span');
            dot.className = 'dh-filter-dot';
            btn.appendChild(dot);
        }
        label.textContent = dhFilterLabels[status];
        bar.style.display = 'flex';
    } else {
        btn.classList.remove('has-filter');
        const dot = btn.querySelector('.dh-filter-dot');
        if (dot) dot.remove();
        bar.style.display = 'none';
    }
}

function dhResetFilter() {
    document.querySelectorAll('.dh-sheet-opt').forEach(o => o.classList.remove('active'));
    document.querySelector('.dh-sheet-opt[data-val="all"]').classList.add('active');
    dhApplyFilter('all');
}

function viewProof(url) {
    document.getElementById('dhProofImage').src = url;
    new bootstrap.Modal(document.getElementById('dhProofModal')).show();
}

@if(session('success')) alert('{{ session('success') }}'); @endif
@if(session('error'))   alert('{{ session('error') }}');   @endif
</script>
@endpush
@endsection
