@extends('member.layouts.app')
@section('content')
<div class="scrollable-content pfv2">

    {{-- ═══ TOP IDENTITY STRIP ═══ --}}
    <div class="pfv2-identity">
        <div class="pfv2-id-mesh"></div>
        <div class="pfv2-id-inner">
            <div class="pfv2-avatar">
                <span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                @if($user->is_verified)
                <div class="pfv2-avatar-tick"><i class="bi bi-check-lg"></i></div>
                @endif
            </div>
            <div class="pfv2-id-info">
                <div class="pfv2-name">{{ $user->name }}</div>
                <div class="pfv2-uid">UID #{{ $user->id }}</div>
            </div>
            @if($user->is_verified)
            <span class="pfv2-status pfv2-status-ok">
                <i class="bi bi-patch-check-fill"></i>
                {{ __('app.verified') }}
            </span>
            @else
            <a href="{{ route('member.verification.index') }}" class="pfv2-status pfv2-status-warn">
                <i class="bi bi-shield-exclamation"></i>
                {{ __('app.unverified') }}
            </a>
            @endif
        </div>
    </div>

    {{-- ═══ MENU SECTIONS ═══ --}}
    <div class="pfv2-body">

        {{-- Account Management --}}
        <div class="pfv2-section-label">{{ __('app.account_management') }}</div>
        <div class="pfv2-menu">

            <a href="{{ route('member.wallet.index') }}" class="pfv2-item">
                <div class="pfv2-item-left">
                    <div class="pfv2-icon" style="--c:#60a5fa;">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div class="pfv2-item-text">
                        <span class="pfv2-item-title">{{ __('app.wallet_list') }}</span>
                        <span class="pfv2-item-sub">{{ __('app.manage_wallet_address') }}</span>
                    </div>
                </div>
                <i class="bi bi-chevron-right pfv2-chev"></i>
            </a>

            <div class="pfv2-divider"></div>

            <a href="{{ route('member.verification.index') }}" class="pfv2-item">
                <div class="pfv2-item-left">
                    <div class="pfv2-icon" style="--c:#34d399;">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div class="pfv2-item-text">
                        <span class="pfv2-item-title">{{ __('app.account_verification') }}</span>
                        <span class="pfv2-item-sub">
                            @if($user->is_verified)
                                <span class="pfv2-ok">{{ __('app.account_already_verified') }}</span>
                            @else
                                {{ __('app.verify_your_identity') }}
                            @endif
                        </span>
                    </div>
                </div>
                <i class="bi bi-chevron-right pfv2-chev"></i>
            </a>

            <div class="pfv2-divider"></div>

            <a href="{{ route('member.team.index') }}" class="pfv2-item">
                <div class="pfv2-item-left">
                    <div class="pfv2-icon" style="--c:#a78bfa;">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="pfv2-item-text">
                        <span class="pfv2-item-title">{{ __('app.referral') }}</span>
                        <span class="pfv2-item-sub">{{ __('app.invite_and_earn') }}</span>
                    </div>
                </div>
                <i class="bi bi-chevron-right pfv2-chev"></i>
            </a>

        </div>

        {{-- Transactions --}}
        <div class="pfv2-section-label">{{ __('app.transactions') }}</div>
        <div class="pfv2-menu pfv2-menu-tx">

            <a href="{{ route('member.deposit.index') }}" class="pfv2-tx-item">
                <div class="pfv2-tx-icon" style="--c:#34d399;">
                    <i class="bi bi-arrow-down-circle-fill"></i>
                </div>
                <span class="pfv2-tx-label">{{ __('app.deposit') }}</span>
                <i class="bi bi-chevron-right pfv2-chev"></i>
            </a>

            <a href="{{ route('member.withdraw.index') }}" class="pfv2-tx-item">
                <div class="pfv2-tx-icon" style="--c:#fb923c;">
                    <i class="bi bi-arrow-up-circle-fill"></i>
                </div>
                <span class="pfv2-tx-label">{{ __('app.withdrawal') }}</span>
                <i class="bi bi-chevron-right pfv2-chev"></i>
            </a>

            <a href="{{ route('member.deposit.history') }}" class="pfv2-tx-item">
                <div class="pfv2-tx-icon" style="--c:#60a5fa;">
                    <i class="bi bi-clock-history"></i>
                </div>
                <span class="pfv2-tx-label">{{ __('app.deposit_history') }}</span>
                <i class="bi bi-chevron-right pfv2-chev"></i>
            </a>

            <a href="{{ route('member.withdraw.history') }}" class="pfv2-tx-item">
                <div class="pfv2-tx-icon" style="--c:#e879f9;">
                    <i class="bi bi-arrow-up-circle"></i>
                </div>
                <span class="pfv2-tx-label">{{ __('app.withdrawal_history') }}</span>
                <i class="bi bi-chevron-right pfv2-chev"></i>
            </a>

        </div>

        {{-- Logout --}}
        <a href="#"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           class="pfv2-logout">
            <i class="bi bi-box-arrow-right"></i>
            <span>{{ __('app.logout') }}</span>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>

    </div>

    <div style="height:24px;"></div>
</div>
@endsection

@push('styles')
<style>
/* ══ Root ══════════════════════════════════════════════════════ */
.pfv2 { background: var(--bg-dark); }
.pfv2-body { padding: 0 12px; }

/* ══ Identity Hero ══════════════════════════════════════════════ */
.pfv2-identity {
    position: relative; overflow: hidden;
    padding: 28px 20px 24px;
    margin-bottom: 4px;
}
.pfv2-id-mesh {
    position: absolute; inset: 0; pointer-events: none;
    background:
        radial-gradient(ellipse 100% 140% at 0% 0%, rgba(167,139,250,0.15) 0%, transparent 55%),
        radial-gradient(ellipse 70% 100% at 100% 100%, rgba(52,211,153,0.08) 0%, transparent 55%);
    border-bottom: 1px solid rgba(255,255,255,0.05);
}
.pfv2-id-inner {
    position: relative; display: flex;
    align-items: center; gap: 14px;
}
.pfv2-avatar {
    position: relative; flex-shrink: 0;
    width: 52px; height: 52px; border-radius: 16px;
    background: linear-gradient(135deg, #a78bfa, #34d399);
    display: flex; align-items: center; justify-content: center;
    font-size: 21px; font-weight: 900; color: #0a0f1e;
    box-shadow: 0 4px 20px rgba(167,139,250,0.3);
}
.pfv2-avatar-tick {
    position: absolute; bottom: -4px; right: -4px;
    width: 17px; height: 17px; border-radius: 50%;
    background: #34d399; border: 2px solid var(--bg-dark, #060a14);
    display: flex; align-items: center; justify-content: center;
    font-size: 8px; color: #0a0f1e; font-weight: 900;
}
.pfv2-id-info { flex: 1; min-width: 0; }
.pfv2-name {
    color: #fff; font-size: 16px; font-weight: 800;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}
.pfv2-uid {
    color: rgba(255,255,255,0.3); font-size: 10px;
    font-family: 'SF Mono', 'Fira Code', monospace;
    letter-spacing: 0.5px; margin-top: 3px;
}
.pfv2-status {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 10px; border-radius: 8px;
    font-size: 10px; font-weight: 700;
    white-space: nowrap; flex-shrink: 0;
    text-decoration: none; letter-spacing: 0.3px;
}
.pfv2-status-ok   { background: rgba(52,211,153,0.1);  border: 1px solid rgba(52,211,153,0.25);  color: #34d399; }
.pfv2-status-warn { background: rgba(251,146,60,0.1);  border: 1px solid rgba(251,146,60,0.25);  color: #fb923c; }

/* ══ Section Label ══════════════════════════════════════════════ */
.pfv2-section-label {
    font-size: 10px; font-weight: 800; letter-spacing: 2px;
    color: rgba(255,255,255,0.2); text-transform: uppercase;
    padding: 18px 4px 8px;
}

/* ══ Menu (list style) ══════════════════════════════════════════ */
.pfv2-menu {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 16px; overflow: hidden;
}
.pfv2-item {
    display: flex; align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    text-decoration: none;
    transition: background 0.15s;
}
.pfv2-item:active { background: rgba(255,255,255,0.04); }
.pfv2-item-left { display: flex; align-items: center; gap: 12px; }
.pfv2-icon {
    width: 38px; height: 38px; border-radius: 12px; flex-shrink: 0;
    background: color-mix(in srgb, var(--c) 12%, transparent);
    border: 1px solid color-mix(in srgb, var(--c) 22%, transparent);
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; color: var(--c);
}
.pfv2-item-text { display: flex; flex-direction: column; gap: 2px; }
.pfv2-item-title { color: #e2e8f0; font-size: 13px; font-weight: 600; }
.pfv2-item-sub   { color: rgba(255,255,255,0.28); font-size: 11px; }
.pfv2-ok         { color: #34d399; }
.pfv2-chev       { color: rgba(255,255,255,0.15); font-size: 11px; flex-shrink: 0; }
.pfv2-divider    { height: 1px; background: rgba(255,255,255,0.04); margin: 0 16px; }

/* ══ Transaction grid ══════════════════════════════════════════ */
.pfv2-menu-tx {
    display: grid; grid-template-columns: 1fr 1fr;
    background: none; border: none;
    gap: 10px; padding: 0;
}
.pfv2-tx-item {
    display: flex; align-items: center; gap: 10px;
    padding: 14px 14px;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 14px;
    text-decoration: none;
    transition: background 0.15s, border-color 0.15s;
}
.pfv2-tx-item:active {
    background: rgba(255,255,255,0.06);
    border-color: rgba(255,255,255,0.14);
}
.pfv2-tx-icon {
    width: 34px; height: 34px; border-radius: 10px; flex-shrink: 0;
    background: color-mix(in srgb, var(--c) 13%, transparent);
    border: 1px solid color-mix(in srgb, var(--c) 22%, transparent);
    display: flex; align-items: center; justify-content: center;
    font-size: 15px; color: var(--c);
}
.pfv2-tx-label {
    color: rgba(255,255,255,0.65); font-size: 12px; font-weight: 600;
    flex: 1; line-height: 1.2;
}
.pfv2-tx-item .pfv2-chev { margin-left: auto; }

/* ══ Logout ════════════════════════════════════════════════════ */
.pfv2-logout {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    margin: 20px 0 0;
    padding: 14px;
    border-radius: 14px;
    background: rgba(239,68,68,0.06);
    border: 1px solid rgba(239,68,68,0.15);
    color: #f87171; font-size: 13px; font-weight: 700;
    text-decoration: none; letter-spacing: 0.3px;
    transition: background 0.15s, border-color 0.15s;
}
.pfv2-logout:active {
    background: rgba(239,68,68,0.12);
    border-color: rgba(239,68,68,0.3);
}
.pfv2-logout i { font-size: 16px; }
</style>
@endpush