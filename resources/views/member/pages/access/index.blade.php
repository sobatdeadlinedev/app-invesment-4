@extends('member.layouts.app')
@section('content')
<div class="scrollable-content">

    {{-- ═══ HEADER ═══ --}}
    <div class="ac-header">
        <div class="ac-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
        <div class="ac-info">
            <div class="ac-name">{{ $user->name }}</div>
            <div class="ac-uid">UID: {{ $user->id }}</div>
        </div>
        @if($user->is_verified)
        <span class="ac-verified-badge">
            <i class="bi bi-patch-check-fill"></i> Verified
        </span>
        @else
        <a href="{{ route('member.verification.index') }}" class="ac-unverified-badge">
            <i class="bi bi-shield-exclamation"></i> Unverified
        </a>
        @endif
    </div>

    {{-- ═══ FEATURE MENU ═══ --}}
    <div class="ac-section">
        <div class="ac-section-title">Account Management</div>

        <div class="ac-list">
            <a href="{{ route('member.wallet.index') }}" class="ac-item">
                <div class="ac-item-icon" style="background:rgba(37,99,235,0.15);border-color:rgba(37,99,235,0.3);">
                    <i class="bi bi-wallet2" style="color:#3b82f6;"></i>
                </div>
                <div class="ac-item-body">
                    <div class="ac-item-label">Daftar Wallet</div>
                    <div class="ac-item-sub">Kelola alamat wallet penarikan</div>
                </div>
                <i class="bi bi-chevron-right ac-chevron"></i>
            </a>

            <a href="{{ route('member.verification.index') }}" class="ac-item">
                <div class="ac-item-icon" style="background:rgba(34,197,94,0.12);border-color:rgba(34,197,94,0.3);">
                    <i class="bi bi-shield-check" style="color:#22c55e;"></i>
                </div>
                <div class="ac-item-body">
                    <div class="ac-item-label">Verifikasi Akun</div>
                    <div class="ac-item-sub">
                        @if($user->is_verified)
                            <span style="color:#22c55e;">Akun sudah terverifikasi</span>
                        @else
                            Verifikasi identitas kamu
                        @endif
                    </div>
                </div>
                <i class="bi bi-chevron-right ac-chevron"></i>
            </a>

            <a href="{{ route('member.team.index') }}" class="ac-item" style="border-bottom:none;">
                <div class="ac-item-icon" style="background:rgba(139,92,246,0.12);border-color:rgba(139,92,246,0.3);">
                    <i class="bi bi-people-fill" style="color:#a78bfa;"></i>
                </div>
                <div class="ac-item-body">
                    <div class="ac-item-label">Referral</div>
                    <div class="ac-item-sub">Undang teman & dapatkan komisi</div>
                </div>
                <i class="bi bi-chevron-right ac-chevron"></i>
            </a>
        </div>
    </div>

    {{-- ═══ OTHER MENU ═══ --}}
    <div class="ac-section">
        <div class="ac-section-title">Transaksi</div>

        <div class="ac-list">
            <a href="{{ route('member.deposit.index') }}" class="ac-item">
                <div class="ac-item-icon" style="background:rgba(0,229,255,0.1);border-color:rgba(0,229,255,0.25);">
                    <i class="bi bi-arrow-down-circle-fill" style="color:var(--gold-color);"></i>
                </div>
                <div class="ac-item-body">
                    <div class="ac-item-label">Deposit</div>
                    <div class="ac-item-sub">Top up saldo USDT</div>
                </div>
                <i class="bi bi-chevron-right ac-chevron"></i>
            </a>

            <a href="{{ route('member.withdraw.index') }}" class="ac-item">
                <div class="ac-item-icon" style="background:rgba(0,229,255,0.1);border-color:rgba(0,229,255,0.25);">
                    <i class="bi bi-arrow-up-circle-fill" style="color:var(--gold-color);"></i>
                </div>
                <div class="ac-item-body">
                    <div class="ac-item-label">Penarikan</div>
                    <div class="ac-item-sub">Tarik saldo ke wallet kamu</div>
                </div>
                <i class="bi bi-chevron-right ac-chevron"></i>
            </a>

            <a href="{{ route('member.deposit.history') }}" class="ac-item">
                <div class="ac-item-icon" style="background:rgba(0,229,255,0.1);border-color:rgba(0,229,255,0.25);">
                    <i class="bi bi-clock-history" style="color:var(--gold-color);"></i>
                </div>
                <div class="ac-item-body">
                    <div class="ac-item-label">Riwayat Deposit</div>
                    <div class="ac-item-sub">Lihat histori deposit kamu</div>
                </div>
                <i class="bi bi-chevron-right ac-chevron"></i>
            </a>

            <a href="{{ route('member.withdraw.history') }}" class="ac-item" style="border-bottom:none;">
                <div class="ac-item-icon" style="background:rgba(0,229,255,0.1);border-color:rgba(0,229,255,0.25);">
                    <i class="bi bi-arrow-up-circle" style="color:var(--gold-color);"></i>
                </div>
                <div class="ac-item-body">
                    <div class="ac-item-label">Riwayat Penarikan</div>
                    <div class="ac-item-sub">Lihat histori penarikan kamu</div>
                </div>
                <i class="bi bi-chevron-right ac-chevron"></i>
            </a>
        </div>
    </div>

    {{-- ═══ LOGOUT ═══ --}}
    <div class="ac-section">
        <div class="ac-list">
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="ac-item" style="border-bottom:none;">
                <div class="ac-item-icon" style="background:rgba(239,68,68,0.1);border-color:rgba(239,68,68,0.25);">
                    <i class="bi bi-box-arrow-right" style="color:#ef4444;"></i>
                </div>
                <div class="ac-item-body">
                    <div class="ac-item-label" style="color:#ef4444;">Logout</div>
                    <div class="ac-item-sub">Keluar dari akun</div>
                </div>
                <i class="bi bi-chevron-right ac-chevron" style="color:#ef4444;"></i>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
        </div>
    </div>

    <div style="height:16px;"></div>
</div>
@endsection

@push('styles')
<style>
/* ── Access Page ── */
.ac-header {
    display: flex; align-items: center; gap: 14px;
    padding: 20px 20px 16px;
    background: linear-gradient(135deg, #0d1928 0%, #0a1420 100%);
    border-bottom: 1px solid var(--border-color);
}
.ac-avatar {
    width: 48px; height: 48px; border-radius: 50%; flex-shrink: 0;
    background: linear-gradient(135deg, var(--gold-color), #00b8d4);
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; font-weight: 800; color: #0a0f1e;
}
.ac-info { flex: 1; min-width: 0; }
.ac-name { color: #fff; font-size: 15px; font-weight: 700; }
.ac-uid  { color: var(--text-muted); font-size: 11px; margin-top: 2px; }
.ac-verified-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 5px 10px; border-radius: 20px; font-size: 11px; font-weight: 700;
    background: rgba(34,197,94,0.12); border: 1px solid rgba(34,197,94,0.3); color: #22c55e;
    white-space: nowrap; flex-shrink: 0; text-decoration: none;
}
.ac-unverified-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 5px 10px; border-radius: 20px; font-size: 11px; font-weight: 700;
    background: rgba(255,193,7,0.12); border: 1px solid rgba(255,193,7,0.3); color: #ffc107;
    white-space: nowrap; flex-shrink: 0; text-decoration: none;
}

/* ── Sections ── */
.ac-section { padding: 16px 16px 0; }
.ac-section-title {
    font-size: 11px; font-weight: 700; color: var(--text-muted);
    text-transform: uppercase; letter-spacing: 0.6px;
    margin-bottom: 10px; padding-left: 2px;
}
.ac-list {
    background: rgba(255,255,255,0.03);
    border: 1px solid var(--border-color);
    border-radius: 14px; overflow: hidden;
}

/* ── Item rows ── */
.ac-item {
    display: flex; align-items: center; gap: 14px;
    padding: 14px 16px;
    border-bottom: 1px solid var(--border-color);
    text-decoration: none; color: inherit;
    transition: background 0.15s;
}
.ac-item:hover { background: rgba(255,255,255,0.03); }
.ac-item-icon {
    width: 40px; height: 40px; border-radius: 11px; border: 1px solid;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; flex-shrink: 0;
}
.ac-item-body { flex: 1; min-width: 0; }
.ac-item-label { color: #fff; font-size: 14px; font-weight: 600; }
.ac-item-sub   { color: var(--text-muted); font-size: 11px; margin-top: 2px; }
.ac-chevron    { color: var(--text-muted); font-size: 12px; flex-shrink: 0; }
</style>
@endpush
