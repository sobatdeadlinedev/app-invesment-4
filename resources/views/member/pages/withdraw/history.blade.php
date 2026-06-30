@extends('member.layouts.app')
@section('content')
<div class="scrollable-content whv2">

    {{-- ═══ HEADER ═══ --}}
    <div class="whv2-header">
        <a href="{{ route('member.withdraw.index') }}" class="whv2-back">
            <i class="bi bi-chevron-left"></i>
        </a>
        <div class="whv2-header-center">
            <div class="whv2-title">{{ __('app.withdrawal_history') }}</div>
        </div>
        <button class="whv2-filterbtn" id="wh-filter-btn" onclick="whOpenFilter()">
            <i class="bi bi-sliders2"></i>
        </button>
    </div>

    {{-- ═══ STAT CHIPS ═══ --}}
    <div class="whv2-stats">
        <div class="whv2-stat pending">
            <span class="whv2-stat-val">{{ $pendingCount }}</span>
            <span class="whv2-stat-lbl">{{ __('app.pending') }}</span>
        </div>
        <div class="whv2-stat-sep"></div>
        <div class="whv2-stat completed">
            <span class="whv2-stat-val">{{ $completedCount }}</span>
            <span class="whv2-stat-lbl">{{ __('app.completed') }}</span>
        </div>
        <div class="whv2-stat-sep"></div>
        <div class="whv2-stat total">
            <span class="whv2-stat-val">{{ $transactions->total() }}</span>
            <span class="whv2-stat-lbl">{{ __('app.total') }}</span>
        </div>
    </div>

    {{-- ═══ ACTIVE FILTER ═══ --}}
    <div class="whv2-filterbar" id="wh-active-bar" style="display:none;">
        <i class="bi bi-funnel-fill"></i>
        <span id="wh-active-label"></span>
        <button onclick="whResetFilter()" class="whv2-filterclear"><i class="bi bi-x-lg"></i></button>
    </div>

    {{-- ═══ TRANSACTION LIST ═══ --}}
    @if($transactions->isEmpty())
        <div class="whv2-empty">
            <div class="whv2-empty-ring">
                <i class="bi bi-arrow-up-circle"></i>
            </div>
            <div class="whv2-empty-title">{{ __('app.no_withdrawal_history') }}</div>
            <a href="{{ route('member.withdraw.index') }}" class="whv2-empty-cta">
                <i class="bi bi-plus-circle"></i> {{ __('app.withdraw') }}
            </a>
        </div>
    @else
        <div class="whv2-list">
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
            <div class="whv2-item" data-status="{{ $transaction->status }}" onclick="whToggle(this)">

                {{-- Main row --}}
                <div class="whv2-item-main">
                    <div class="whv2-item-ico {{ $transaction->status }}">
                        <i class="bi {{ $iconClass }}"></i>
                    </div>
                    <div class="whv2-item-info">
                        <div class="whv2-item-name">{{ __('app.withdrawal') }}</div>
                        <div class="whv2-item-time">{{ $transaction->created_at->format('d M Y · H:i') }}</div>
                    </div>
                    <div class="whv2-item-right">
                        <div class="whv2-item-amt">
                            −{{ number_format($transaction->total_amount, 2) }}
                            <span class="whv2-cur">USDT</span>
                        </div>
                        <span class="whv2-badge {{ $transaction->status }}">
                            {{ ucfirst(__('app.' . $transaction->status)) }}
                        </span>
                    </div>
                    <i class="bi bi-chevron-down whv2-chev"></i>
                </div>

                {{-- Expanded detail --}}
                <div class="whv2-detail">
                    <div class="whv2-detail-inner">
                        <div class="whv2-dr">
                            <span class="whv2-dl">{{ __('app.wallet_account') }}</span>
                            <span class="whv2-dv">{{ $transaction->wallet ? $transaction->wallet->account_name : __('app.na') }}</span>
                        </div>
                        <div class="whv2-dr">
                            <span class="whv2-dl">{{ __('app.account_number') }}</span>
                            <span class="whv2-dv mono">{{ $transaction->wallet ? $transaction->wallet->account_number : __('app.na') }}</span>
                        </div>
                        <div class="whv2-dr">
                            <span class="whv2-dl">{{ __('app.withdrawal_amount') }}</span>
                            <span class="whv2-dv">{{ number_format($transaction->total_amount, 2) }} USDT</span>
                        </div>
                        @if($transaction->withdrawal_fee > 0)
                        <div class="whv2-dr">
                            <span class="whv2-dl">{{ __('app.fee_5_percent') }}</span>
                            <span class="whv2-dv red">−{{ number_format($transaction->withdrawal_fee, 2) }} USDT</span>
                        </div>
                        @endif
                        <div class="whv2-dr">
                            <span class="whv2-dl">{{ __('app.you_receive') }}</span>
                            <span class="whv2-dv green">{{ number_format($transaction->amount, 2) }} USDT</span>
                        </div>
                        <div class="whv2-dr">
                            <span class="whv2-dl">{{ __('app.date') }}</span>
                            <span class="whv2-dv">{{ $transaction->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        @if($transaction->status === 'completed' && $transaction->updated_at)
                        <div class="whv2-dr">
                            <span class="whv2-dl">{{ __('app.completed_at') }}</span>
                            <span class="whv2-dv">{{ $transaction->updated_at->format('d M Y, H:i') }}</span>
                        </div>
                        @endif
                        @if($transaction->status === 'pending')
                        <button type="button" class="whv2-cancel"
                            onclick="event.stopPropagation(); cancelWithdrawal('{{ $transaction->reference }}')">
                            <i class="bi bi-x-circle"></i>
                            {{ __('app.cancel_withdrawal') }}
                        </button>
                        @endif
                    </div>
                </div>

            </div>
            @endforeach
        </div>
    @endif

    @if($transactions->hasPages())
        <div class="whv2-pagination">{{ $transactions->links() }}</div>
    @endif

    <div style="height:24px;"></div>
</div>

{{-- ═══ FILTER BOTTOM SHEET ═══ --}}
<div class="whv2-overlay" id="wh-overlay" onclick="whCloseFilter()"></div>
<div class="whv2-sheet" id="wh-sheet">
    <div class="whv2-sheet-handle"></div>
    <div class="whv2-sheet-title">Filter</div>
    <div class="whv2-sheet-opts">
        @foreach(['all','pending','approved','rejected','cancelled','completed'] as $opt)
        <div class="whv2-opt {{ $opt === 'all' ? 'active' : '' }}" data-val="{{ $opt }}" onclick="whPickFilter(this)">
            <div class="whv2-opt-radio"></div>
            <span class="whv2-opt-name">{{ ucfirst(__('app.' . $opt)) }}</span>
            <i class="bi bi-check2 whv2-opt-check"></i>
        </div>
        @endforeach
    </div>
</div>

@push('styles')
<style>
/* ══ Root ══════════════════════════════════════════════════════ */
.whv2 { background: var(--bg-dark); }

/* ══ Header ════════════════════════════════════════════════════ */
.whv2-header {
    display: flex; align-items: center;
    padding: 14px 16px;
    border-bottom: 1px solid rgba(255,255,255,0.06);
}
.whv2-back {
    width: 36px; height: 36px; border-radius: 10px;
    background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08);
    display: flex; align-items: center; justify-content: center;
    color: #fff; text-decoration: none; font-size: 15px; flex-shrink: 0;
}
.whv2-header-center { flex: 1; text-align: center; }
.whv2-title { color: #fff; font-size: 15px; font-weight: 800; }
.whv2-filterbtn {
    width: 36px; height: 36px; border-radius: 10px;
    background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08);
    display: flex; align-items: center; justify-content: center;
    color: rgba(255,255,255,0.45); font-size: 15px; cursor: pointer;
    transition: all 0.2s; position: relative;
}
.whv2-filterbtn.has-filter {
    border-color: rgba(167,139,250,0.4);
    color: #a78bfa;
    background: rgba(167,139,250,0.08);
}
.wh-filter-dot {
    position: absolute; top: 6px; right: 6px;
    width: 7px; height: 7px; border-radius: 50%;
    background: #a78bfa; border: 1.5px solid var(--bg-dark, #0a0f1e);
}

/* ══ Stats ═════════════════════════════════════════════════════ */
.whv2-stats {
    display: flex; align-items: center;
    margin: 14px 14px 0;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 14px; overflow: hidden;
    padding: 4px 0;
}
.whv2-stat {
    flex: 1; display: flex; flex-direction: column;
    align-items: center; gap: 2px; padding: 10px 8px;
}
.whv2-stat-val {
    font-size: 20px; font-weight: 900;
    font-variant-numeric: tabular-nums;
    font-family: 'SF Mono', 'Fira Code', monospace;
    line-height: 1;
}
.whv2-stat-lbl { color: rgba(255,255,255,0.3); font-size: 10px; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase; }
.whv2-stat.pending   .whv2-stat-val { color: #fbbf24; }
.whv2-stat.completed .whv2-stat-val { color: #34d399; }
.whv2-stat.total     .whv2-stat-val { color: rgba(255,255,255,0.7); }
.whv2-stat-sep { width: 1px; height: 36px; background: rgba(255,255,255,0.06); flex-shrink: 0; }

/* ══ Filter bar ════════════════════════════════════════════════ */
.whv2-filterbar {
    display: flex; align-items: center; gap: 8px;
    margin: 10px 14px 0;
    padding: 8px 12px;
    background: rgba(167,139,250,0.06);
    border: 1px solid rgba(167,139,250,0.2);
    border-radius: 10px;
    font-size: 12px; color: #a78bfa; font-weight: 600;
}
.whv2-filterbar span { flex: 1; }
.whv2-filterclear {
    background: none; border: none; color: rgba(255,255,255,0.25);
    font-size: 12px; cursor: pointer; padding: 0; line-height: 1;
}

/* ══ List ══════════════════════════════════════════════════════ */
.whv2-list { padding: 14px 14px 0; display: flex; flex-direction: column; gap: 8px; }

/* ══ Item ══════════════════════════════════════════════════════ */
.whv2-item {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 14px; overflow: hidden; cursor: pointer;
    transition: border-color 0.15s;
}
.whv2-item.expanded { border-color: rgba(255,255,255,0.12); }
.whv2-item-main {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 14px;
}
.whv2-item-ico {
    width: 40px; height: 40px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 17px; flex-shrink: 0; border: 1px solid;
}
.whv2-item-ico.pending   { background: rgba(251,191,36,0.1);  border-color: rgba(251,191,36,0.25);  color: #fbbf24; }
.whv2-item-ico.approved  { background: rgba(96,165,250,0.1);  border-color: rgba(96,165,250,0.25);  color: #60a5fa; }
.whv2-item-ico.completed { background: rgba(52,211,153,0.1);  border-color: rgba(52,211,153,0.25);  color: #34d399; }
.whv2-item-ico.rejected  { background: rgba(248,113,113,0.1); border-color: rgba(248,113,113,0.25); color: #f87171; }
.whv2-item-ico.cancelled { background: rgba(148,163,184,0.1); border-color: rgba(148,163,184,0.25); color: #94a3b8; }
.whv2-item-info { flex: 1; min-width: 0; }
.whv2-item-name { color: #fff; font-size: 13px; font-weight: 700; }
.whv2-item-time { color: rgba(255,255,255,0.3); font-size: 10px; margin-top: 3px; }
.whv2-item-right { text-align: right; flex-shrink: 0; }
.whv2-item-amt {
    color: #f87171; font-size: 14px; font-weight: 800;
    font-variant-numeric: tabular-nums;
    font-family: 'SF Mono', 'Fira Code', monospace;
}
.whv2-cur { font-size: 9px; opacity: 0.65; font-family: inherit; }
.whv2-badge {
    display: inline-block; padding: 2px 7px; border-radius: 5px;
    font-size: 9px; font-weight: 800; text-transform: uppercase;
    letter-spacing: 0.5px; margin-top: 4px;
}
.whv2-badge.pending   { background: rgba(251,191,36,0.1);  color: #fbbf24; border: 1px solid rgba(251,191,36,0.25);  }
.whv2-badge.approved  { background: rgba(96,165,250,0.1);  color: #60a5fa; border: 1px solid rgba(96,165,250,0.25);  }
.whv2-badge.completed { background: rgba(52,211,153,0.1);  color: #34d399; border: 1px solid rgba(52,211,153,0.25);  }
.whv2-badge.rejected  { background: rgba(248,113,113,0.1); color: #f87171; border: 1px solid rgba(248,113,113,0.25); }
.whv2-badge.cancelled { background: rgba(148,163,184,0.1); color: #94a3b8; border: 1px solid rgba(148,163,184,0.25); }
.whv2-chev {
    color: rgba(255,255,255,0.2); font-size: 11px; flex-shrink: 0;
    transition: transform 0.25s; margin-left: 4px;
}
.whv2-item.expanded .whv2-chev { transform: rotate(180deg); }

/* ══ Detail ════════════════════════════════════════════════════ */
.whv2-detail { display: none; }
.whv2-item.expanded .whv2-detail { display: block; }
.whv2-detail-inner {
    border-top: 1px solid rgba(255,255,255,0.05);
    padding: 10px 14px 14px;
    display: flex; flex-direction: column; gap: 0;
}
.whv2-dr {
    display: flex; justify-content: space-between; align-items: flex-start;
    padding: 7px 0;
    border-bottom: 1px solid rgba(255,255,255,0.04);
}
.whv2-dr:last-of-type { border-bottom: none; }
.whv2-dl { color: rgba(255,255,255,0.3); font-size: 11px; }
.whv2-dv { color: rgba(255,255,255,0.8); font-size: 11px; font-weight: 700; text-align: right; max-width: 60%; word-break: break-all; }
.whv2-dv.green { color: #34d399; }
.whv2-dv.red   { color: #f87171; }
.whv2-dv.mono  { font-family: 'SF Mono', 'Fira Code', monospace; font-size: 10px; }
.whv2-cancel {
    width: 100%; margin-top: 10px;
    padding: 10px;
    background: rgba(248,113,113,0.07);
    border: 1px solid rgba(248,113,113,0.2);
    border-radius: 10px;
    color: #f87171; font-size: 12px; font-weight: 700;
    cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;
    transition: background 0.15s;
}
.whv2-cancel:active { background: rgba(248,113,113,0.14); }

/* ══ Empty ═════════════════════════════════════════════════════ */
.whv2-empty {
    padding: 60px 24px;
    display: flex; flex-direction: column; align-items: center; gap: 14px;
    text-align: center;
}
.whv2-empty-ring {
    width: 72px; height: 72px; border-radius: 50%;
    border: 2px dashed rgba(248,113,113,0.25);
    display: flex; align-items: center; justify-content: center;
    font-size: 30px; color: rgba(248,113,113,0.4);
}
.whv2-empty-title { color: rgba(255,255,255,0.5); font-size: 14px; font-weight: 600; }
.whv2-empty-cta {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 10px 22px; border-radius: 10px;
    background: rgba(248,113,113,0.1); border: 1px solid rgba(248,113,113,0.25);
    color: #f87171; font-size: 13px; font-weight: 700; text-decoration: none;
}

/* ══ Pagination ════════════════════════════════════════════════ */
.whv2-pagination { padding: 14px 14px; }

/* ══ Bottom Sheet ══════════════════════════════════════════════ */
.whv2-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.6); z-index: 1050;
}
.whv2-overlay.open { display: block; }
.whv2-sheet {
    position: fixed; bottom: 0; left: 0; right: 0;
    background: #0d1120;
    border: 1px solid rgba(255,255,255,0.08);
    border-bottom: none;
    border-radius: 20px 20px 0 0;
    padding: 12px 16px 72px;
    z-index: 1051;
    transform: translateY(100%);
    transition: transform 0.3s cubic-bezier(0.4,0,0.2,1);
    max-height: 70vh; overflow-y: auto;
}
.whv2-sheet.open { transform: translateY(0); }
.whv2-sheet-handle {
    width: 36px; height: 4px; border-radius: 2px;
    background: rgba(255,255,255,0.12);
    margin: 0 auto 16px;
}
.whv2-sheet-title {
    color: #fff; font-size: 14px; font-weight: 800;
    margin-bottom: 8px; letter-spacing: 0.3px;
}
.whv2-sheet-opts { display: flex; flex-direction: column; }
.whv2-opt {
    display: flex; align-items: center; gap: 14px;
    padding: 13px 0;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    cursor: pointer;
}
.whv2-opt:last-child { border-bottom: none; }
.whv2-opt-radio {
    width: 20px; height: 20px; border-radius: 50%;
    border: 2px solid rgba(255,255,255,0.15);
    flex-shrink: 0; transition: all 0.2s;
}
.whv2-opt.active .whv2-opt-radio {
    border-color: #a78bfa;
    background: #a78bfa;
    box-shadow: 0 0 0 3px rgba(167,139,250,0.2);
}
.whv2-opt-name { flex: 1; color: rgba(255,255,255,0.7); font-size: 14px; font-weight: 600; }
.whv2-opt.active .whv2-opt-name { color: #fff; }
.whv2-opt-check { color: #a78bfa; font-size: 16px; opacity: 0; transition: opacity 0.2s; }
.whv2-opt.active .whv2-opt-check { opacity: 1; }
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
    document.querySelectorAll('.whv2-opt').forEach(o => o.classList.remove('active'));
    optEl.classList.add('active');
    whApplyFilter(optEl.dataset.val);
    setTimeout(whCloseFilter, 180);
}
function whApplyFilter(status) {
    document.querySelectorAll('.whv2-item').forEach(card => {
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
    document.querySelectorAll('.whv2-opt').forEach(o => o.classList.remove('active'));
    document.querySelector('.whv2-opt[data-val="all"]').classList.add('active');
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