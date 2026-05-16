@extends('member.layouts.app')
@section('content')
<div class="scrollable-content">

    <!-- Header -->
    <div class="wh-header">
        <a href="{{ route('member.withdraw.index') }}" class="wh-back-btn">
            <i class="bi bi-chevron-left"></i>
        </a>
        <h5 class="wh-title">{{ __('app.withdrawal_history') }}</h5>
        <button class="wh-filter-btn" id="wh-filter-btn" onclick="whOpenFilter()">
            <i class="bi bi-sliders2"></i>
        </button>
    </div>

    <!-- Stat List -->
    <div class="wh-stat-list">
        <div class="wh-stat-row">
            <div class="wh-stat-dot" style="background:#fbbf24;box-shadow:0 0 6px rgba(251,191,36,0.5);"></div>
            <span class="wh-stat-lbl">{{ __('app.pending') }}</span>
            <span class="wh-stat-val">{{ $pendingCount }}</span>
        </div>
        <div class="wh-stat-row">
            <div class="wh-stat-dot" style="background:#22c55e;box-shadow:0 0 6px rgba(34,197,94,0.5);"></div>
            <span class="wh-stat-lbl">{{ __('app.completed') }}</span>
            <span class="wh-stat-val">{{ $completedCount }}</span>
        </div>
        <div class="wh-stat-row" style="border-bottom:none;">
            <div class="wh-stat-dot" style="background:rgba(255,255,255,0.3);"></div>
            <span class="wh-stat-lbl">{{ __('app.total') }}</span>
            <span class="wh-stat-val">{{ $transactions->total() }}</span>
        </div>
    </div>

    <!-- Active Filter Bar -->
    <div class="wh-active-bar" id="wh-active-bar" style="display:none;">
        <i class="bi bi-funnel-fill"></i>
        <span id="wh-active-label"></span>
        <button onclick="whResetFilter()" class="wh-active-clear"><i class="bi bi-x"></i></button>
    </div>

    <!-- Transaction Cards -->
    @if($transactions->isEmpty())
        <div class="wh-empty">
            <div class="wh-empty-inner">
                <i class="bi bi-arrow-up-circle wh-empty-ico"></i>
                <div class="wh-empty-h">{{ __('app.no_withdrawal_history') }}</div>
                <a href="{{ route('member.withdraw.index') }}" class="wh-empty-cta">
                    <i class="bi bi-plus-circle me-2"></i>{{ __('app.withdraw') }}
                </a>
            </div>
        </div>
    @else
        <div class="wh-cards-wrap">
            @foreach($transactions as $transaction)
                @php
                    $iconClass = match($transaction->status) {
                        'completed' => 'bi-check-circle-fill',
                        'approved'  => 'bi-hourglass-split',
                        'rejected'  => 'bi-x-circle-fill',
                        'cancelled' => 'bi-slash-circle',
                        default     => 'bi-clock-history',
                    };
                @endphp
                <div class="wh-card" data-status="{{ $transaction->status }}" onclick="whToggle(this)">
                    <div class="wh-card-main">
                        <div class="wh-card-ico {{ $transaction->status }}">
                            <i class="bi {{ $iconClass }}"></i>
                        </div>
                        <div class="wh-card-info">
                            <div class="wh-card-type">{{ __('app.withdrawal') }}</div>
                            <div class="wh-card-time">{{ $transaction->created_at->format('d M Y · H:i') }}</div>
                        </div>
                        <div class="wh-card-right">
                            <div class="wh-card-amount">-{{ number_format($transaction->total_amount, 2) }} <span class="wh-usdt">USDT</span></div>
                            <span class="wh-card-badge {{ $transaction->status }}">{{ ucfirst(__('app.' . $transaction->status)) }}</span>
                        </div>
                        <i class="bi bi-chevron-down wh-card-chev"></i>
                    </div>
                    <div class="wh-card-detail">
                        <div class="wh-dr">
                            <span class="wh-dl">{{ __('app.wallet_account') }}</span>
                            <span class="wh-dv">{{ $transaction->wallet ? $transaction->wallet->account_name : __('app.na') }}</span>
                        </div>
                        <div class="wh-dr">
                            <span class="wh-dl">{{ __('app.account_number') }}</span>
                            <span class="wh-dv mono">{{ $transaction->wallet ? $transaction->wallet->account_number : __('app.na') }}</span>
                        </div>
                        <div class="wh-dr">
                            <span class="wh-dl">{{ __('app.withdrawal_amount') }}</span>
                            <span class="wh-dv">{{ number_format($transaction->total_amount, 2) }} USDT</span>
                        </div>
                        @if($transaction->withdrawal_fee > 0)
                        <div class="wh-dr">
                            <span class="wh-dl">{{ __('app.fee_5_percent') }}</span>
                            <span class="wh-dv red">-{{ number_format($transaction->withdrawal_fee, 2) }} USDT</span>
                        </div>
                        @endif
                        <div class="wh-dr">
                            <span class="wh-dl">{{ __('app.you_receive') }}</span>
                            <span class="wh-dv green">{{ number_format($transaction->amount, 2) }} USDT</span>
                        </div>
                        <div class="wh-dr">
                            <span class="wh-dl">{{ __('app.date') }}</span>
                            <span class="wh-dv">{{ $transaction->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        @if($transaction->status === 'completed' && $transaction->updated_at)
                        <div class="wh-dr">
                            <span class="wh-dl">{{ __('app.completed_at') }}</span>
                            <span class="wh-dv">{{ $transaction->updated_at->format('d M Y, H:i') }}</span>
                        </div>
                        @endif
                        @if($transaction->status === 'pending')
                        <div class="wh-detail-cancel">
                            <button type="button" class="wh-cancel-btn"
                                onclick="event.stopPropagation(); cancelWithdrawal('{{ $transaction->reference }}')">
                                <i class="bi bi-x-circle me-1"></i>{{ __('app.cancel_withdrawal') }}
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if($transactions->hasPages())
        <div class="wh-pagination">{{ $transactions->links() }}</div>
    @endif

    <div style="height:24px;"></div>

</div>

<!-- Filter Bottom Sheet -->
<div class="wh-overlay" id="wh-overlay" onclick="whCloseFilter()"></div>
<div class="wh-sheet" id="wh-sheet">
    <div class="wh-sheet-handle"></div>
    <div class="wh-sheet-title">Filter</div>
    <div class="wh-sheet-opts">
        <div class="wh-sheet-opt active" data-val="all" onclick="whPickFilter(this)">
            <div class="wh-opt-radio"></div>
            <div class="wh-opt-body"><div class="wh-opt-name">{{ __('app.all') }}</div></div>
            <i class="bi bi-check2 wh-opt-check"></i>
        </div>
        <div class="wh-sheet-opt" data-val="pending" onclick="whPickFilter(this)">
            <div class="wh-opt-radio"></div>
            <div class="wh-opt-body"><div class="wh-opt-name">{{ __('app.pending') }}</div></div>
            <i class="bi bi-check2 wh-opt-check"></i>
        </div>
        <div class="wh-sheet-opt" data-val="approved" onclick="whPickFilter(this)">
            <div class="wh-opt-radio"></div>
            <div class="wh-opt-body"><div class="wh-opt-name">{{ __('app.approved') }}</div></div>
            <i class="bi bi-check2 wh-opt-check"></i>
        </div>
        <div class="wh-sheet-opt" data-val="rejected" onclick="whPickFilter(this)">
            <div class="wh-opt-radio"></div>
            <div class="wh-opt-body"><div class="wh-opt-name">{{ __('app.rejected') }}</div></div>
            <i class="bi bi-check2 wh-opt-check"></i>
        </div>
        <div class="wh-sheet-opt" data-val="cancelled" onclick="whPickFilter(this)">
            <div class="wh-opt-radio"></div>
            <div class="wh-opt-body"><div class="wh-opt-name">{{ __('app.cancelled') }}</div></div>
            <i class="bi bi-check2 wh-opt-check"></i>
        </div>
        <div class="wh-sheet-opt" data-val="completed" onclick="whPickFilter(this)">
            <div class="wh-opt-radio"></div>
            <div class="wh-opt-body"><div class="wh-opt-name">{{ __('app.completed') }}</div></div>
            <i class="bi bi-check2 wh-opt-check"></i>
        </div>
    </div>
</div>

@push('styles')
<style>
/* ── Header ── */
.wh-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid var(--border-color);
}
.wh-back-btn {
    width: 36px; height: 36px; border-radius: 50%;
    background: rgba(255,255,255,0.06); border: 1px solid var(--border-color);
    display: flex; align-items: center; justify-content: center;
    color: #fff; text-decoration: none; font-size: 16px;
}
.wh-back-btn:hover { background: rgba(255,255,255,0.1); color: #fff; }
.wh-title { color: #fff; font-size: 16px; font-weight: 700; margin: 0; }
.wh-filter-btn {
    width: 36px; height: 36px; border-radius: 50%;
    background: rgba(255,255,255,0.06); border: 1px solid var(--border-color);
    display: flex; align-items: center; justify-content: center;
    color: var(--text-muted); font-size: 15px; cursor: pointer;
    transition: all 0.2s; position: relative;
}
.wh-filter-btn:hover { background: rgba(255,255,255,0.1); }
.wh-filter-btn.has-filter { border-color: #ef4444; color: #ef4444; background: rgba(239,68,68,0.08); }
.wh-filter-dot {
    position: absolute; top: 5px; right: 5px;
    width: 8px; height: 8px; border-radius: 50%;
    background: #ef4444; border: 1.5px solid #0a0f1e;
}

/* ── Stat List ── */
.wh-stat-list {
    margin: 16px 20px 0;
    background: rgba(255,255,255,0.03);
    border: 1px solid var(--border-color);
    border-radius: 14px; overflow: hidden;
}
.wh-stat-row {
    display: flex; align-items: center; gap: 12px;
    padding: 13px 16px;
    border-bottom: 1px solid var(--border-color);
}
.wh-stat-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.wh-stat-lbl { flex: 1; color: var(--text-primary); font-size: 14px; font-weight: 500; }
.wh-stat-val { color: #fff; font-size: 14px; font-weight: 700; }
.wh-stat-chev { color: var(--text-muted); font-size: 12px; margin-left: 6px; }

/* ── Active Filter Bar ── */
.wh-active-bar {
    display: flex; align-items: center; gap: 8px;
    margin: 14px 20px 0;
    padding: 8px 14px;
    background: rgba(239,68,68,0.06); border: 1px solid rgba(239,68,68,0.2);
    border-radius: 8px; font-size: 12px; color: #ef4444; font-weight: 600;
}
.wh-active-bar span { flex: 1; }
.wh-active-clear {
    background: none; border: none; color: var(--text-muted);
    font-size: 16px; cursor: pointer; padding: 0; line-height: 1;
}
.wh-active-clear:hover { color: #ef4444; }

/* ── Cards Wrap ── */
.wh-cards-wrap { padding: 16px 20px 0; display: flex; flex-direction: column; gap: 10px; }

/* ── Individual Card ── */
.wh-card {
    background: rgba(255,255,255,0.03);
    border: 1px solid var(--border-color);
    border-radius: 14px; cursor: pointer;
    transition: background 0.15s; overflow: hidden;
}
.wh-card:hover { background: rgba(255,255,255,0.05); }
.wh-card-main { display: flex; align-items: center; gap: 12px; padding: 14px 16px; }
.wh-card-ico {
    width: 42px; height: 42px; border-radius: 12px; border: 1px solid;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; flex-shrink: 0;
}
.wh-card-ico.pending   { background: rgba(251,191,36,0.12);  border-color: rgba(251,191,36,0.3);  color: #fbbf24; }
.wh-card-ico.approved  { background: rgba(59,181,232,0.12);  border-color: rgba(59,181,232,0.3);  color: #3bb5e8; }
.wh-card-ico.completed { background: rgba(34,197,94,0.12);   border-color: rgba(34,197,94,0.3);   color: #22c55e; }
.wh-card-ico.rejected  { background: rgba(239,68,68,0.12);   border-color: rgba(239,68,68,0.3);   color: #ef4444; }
.wh-card-ico.cancelled { background: rgba(108,117,125,0.12); border-color: rgba(108,117,125,0.3); color: #6c757d; }
.wh-card-info { flex: 1; min-width: 0; }
.wh-card-type { color: #fff; font-size: 13px; font-weight: 600; }
.wh-card-time { color: var(--text-muted); font-size: 11px; margin-top: 2px; }
.wh-card-right { text-align: right; flex-shrink: 0; }
.wh-card-amount { color: #ef4444; font-size: 14px; font-weight: 800; white-space: nowrap; }
.wh-usdt { font-size: 10px; font-weight: 600; opacity: 0.75; }
.wh-card-badge {
    display: inline-block; padding: 2px 8px; border-radius: 4px;
    font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px;
}
.wh-card-badge.pending   { background: rgba(251,191,36,0.12);  color: #fbbf24; border: 1px solid rgba(251,191,36,0.3);  }
.wh-card-badge.approved  { background: rgba(59,181,232,0.12);  color: #3bb5e8; border: 1px solid rgba(59,181,232,0.3);  }
.wh-card-badge.completed { background: rgba(34,197,94,0.12);   color: #22c55e; border: 1px solid rgba(34,197,94,0.3);   }
.wh-card-badge.rejected  { background: rgba(239,68,68,0.12);   color: #ef4444; border: 1px solid rgba(239,68,68,0.3);   }
.wh-card-badge.cancelled { background: rgba(108,117,125,0.12); color: #6c757d; border: 1px solid rgba(108,117,125,0.3); }
.wh-card-chev { color: var(--text-muted); font-size: 12px; flex-shrink: 0; transition: transform 0.25s; margin-left: 4px; }
.wh-card.expanded .wh-card-chev { transform: rotate(180deg); }

/* ── Card Detail ── */
.wh-card-detail {
    display: none;
    background: rgba(239,68,68,0.03); border-top: 1px solid var(--border-color);
    padding: 10px 16px;
}
.wh-card.expanded .wh-card-detail { display: block; }
.wh-dr { display: flex; justify-content: space-between; align-items: center; padding: 6px 0; }
.wh-dr:not(:last-child) { border-bottom: 1px solid rgba(255,255,255,0.04); }
.wh-dl { color: var(--text-muted); font-size: 12px; }
.wh-dv { color: #fff; font-size: 12px; font-weight: 600; }
.wh-dv.green { color: #22c55e; }
.wh-dv.red   { color: #ef4444; }
.wh-dv.mono  { font-family: monospace; font-size: 11px; }
.wh-detail-cancel { padding: 8px 0 4px; }
.wh-cancel-btn {
    width: 100%; padding: 9px 12px;
    background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.3);
    border-radius: 8px; color: #ef4444; font-size: 13px; font-weight: 600;
    cursor: pointer; transition: background 0.2s;
}
.wh-cancel-btn:hover { background: rgba(239,68,68,0.15); }

/* ── Empty State ── */
.wh-empty { padding: 20px; }
.wh-empty-inner {
    border: 1.5px dashed rgba(239,68,68,0.2);
    border-radius: 18px;
    padding: 48px 24px;
    text-align: center;
}
.wh-empty-ico { font-size: 52px; color: #ef4444; opacity: 0.3; display: block; margin-bottom: 18px; }
.wh-empty-h { color: #fff; font-size: 16px; font-weight: 700; margin-bottom: 8px; }
.wh-empty-sub { color: var(--text-muted); font-size: 13px; margin-bottom: 26px; line-height: 1.6; }
.wh-empty-cta {
    display: inline-flex; align-items: center; padding: 12px 28px;
    background: linear-gradient(135deg, #ef4444, #b91c1c);
    border-radius: 25px; color: #fff; font-size: 14px; font-weight: 700;
    text-decoration: none; transition: all 0.25s;
}
.wh-empty-cta:hover { transform: translateY(-1px); opacity: 0.9; color: #fff; }

/* ── Pagination ── */
.wh-pagination { padding: 16px 20px; }

/* ── Bottom Sheet Overlay ── */
.wh-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.55); z-index: 1050;
}
.wh-overlay.open { display: block; }

/* ── Bottom Sheet ── */
.wh-sheet {
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
.wh-sheet.open { transform: translateY(0); }
.wh-sheet-handle {
    width: 36px; height: 4px; border-radius: 2px;
    background: rgba(255,255,255,0.15);
    margin: 0 auto 18px;
}
.wh-sheet-title { color: #fff; font-size: 15px; font-weight: 700; margin-bottom: 4px; }
.wh-sheet-opt {
    display: flex; align-items: center; gap: 14px;
    padding: 14px 0;
    border-bottom: 1px solid var(--border-color);
    cursor: pointer;
}
.wh-sheet-opt:last-child { border-bottom: none; }
.wh-opt-radio {
    width: 20px; height: 20px; border-radius: 50%;
    border: 2px solid var(--border-color);
    flex-shrink: 0; transition: all 0.2s;
}
.wh-sheet-opt.active .wh-opt-radio {
    border-color: #ef4444;
    background: #ef4444;
    box-shadow: 0 0 0 3px rgba(239,68,68,0.18);
}
.wh-opt-body { flex: 1; }
.wh-opt-name { color: #fff; font-size: 14px; font-weight: 600; }
.wh-opt-check { color: #ef4444; font-size: 17px; opacity: 0; transition: opacity 0.2s; }
.wh-sheet-opt.active .wh-opt-check { opacity: 1; }
</style>
@endpush

@push('scripts')
<script>
const whFilterLabels = {
    all:       '{{ __("app.all") }}',
    pending:   '{{ __("app.pending") }}',
    approved:  '{{ __("app.approved") }}',
    rejected:  '{{ __("app.rejected") }}',
    cancelled: '{{ __("app.cancelled") }}',
    completed: '{{ __("app.completed") }}',
};
const whTrans = { confirmCancelWithdrawal: "{{ __('app.confirm_cancel_withdrawal') }}" };

function whToggle(el) { el.classList.toggle('expanded'); }

function whOpenFilter() {
    document.getElementById('wh-overlay').classList.add('open');
    document.getElementById('wh-sheet').classList.add('open');
}
function whCloseFilter() {
    document.getElementById('wh-overlay').classList.remove('open');
    document.getElementById('wh-sheet').classList.remove('open');
}

function whPickFilter(optEl) {
    document.querySelectorAll('.wh-sheet-opt').forEach(o => o.classList.remove('active'));
    optEl.classList.add('active');
    whApplyFilter(optEl.dataset.val);
    setTimeout(whCloseFilter, 180);
}

function whApplyFilter(status) {
    document.querySelectorAll('.wh-card').forEach(card => {
        card.style.display = (status === 'all' || card.dataset.status === status) ? '' : 'none';
    });

    const btn   = document.getElementById('wh-filter-btn');
    const bar   = document.getElementById('wh-active-bar');
    const label = document.getElementById('wh-active-label');

    if (status !== 'all') {
        btn.classList.add('has-filter');
        if (!btn.querySelector('.wh-filter-dot')) {
            const dot = document.createElement('span');
            dot.className = 'wh-filter-dot';
            btn.appendChild(dot);
        }
        label.textContent = whFilterLabels[status];
        bar.style.display = 'flex';
    } else {
        btn.classList.remove('has-filter');
        const dot = btn.querySelector('.wh-filter-dot');
        if (dot) dot.remove();
        bar.style.display = 'none';
    }
}

function whResetFilter() {
    document.querySelectorAll('.wh-sheet-opt').forEach(o => o.classList.remove('active'));
    document.querySelector('.wh-sheet-opt[data-val="all"]').classList.add('active');
    whApplyFilter('all');
}

function cancelWithdrawal(reference) {
    if (!confirm(whTrans.confirmCancelWithdrawal)) return;
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

@if(session('success')) alert('{{ session('success') }}'); @endif
@if(session('error'))   alert('{{ session('error') }}');   @endif
</script>
@endpush
@endsection
