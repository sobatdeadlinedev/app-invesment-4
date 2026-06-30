<!-- Profile Dropdown Menu -->
<div class="profile-dropdown">
    <button class="btn-header-icon profile-trigger" id="profileDropdownBtn">
        <i class="bi bi-person-circle"></i>
    </button>

    <div class="profile-dropdown-menu" id="profileDropdownMenu">

        <!-- Header bar -->
        <div class="pc-topbar">
            <button class="pc-back" onclick="document.getElementById('profileDropdownMenu').classList.remove('show'); document.body.style.overflow='auto';">
                <i class="bi bi-chevron-left"></i>
            </button>
            <span class="pc-topbar-title">{{ app()->getLocale() == 'id' ? 'Pusat Akun' : 'Personal Center' }}</span>
            <span class="pc-topbar-spacer"></span>
        </div>

        <div class="pc-scroll">

            <!-- User Info -->
            <div class="pc-header">
                <div class="pc-email">{{ \Illuminate\Support\Str::mask(auth()->user()->email, '*', 3, -8) }}</div>
                <div class="pc-uid">ID:{{ auth()->user()->id }}</div>
            </div>

            <!-- Banner -->
            <div class="pc-banner">
                <span>{{ app()->getLocale() == 'id' ? 'AJAK TEMAN' : 'INVITE FRIENDS' }}<br>{{ app()->getLocale() == 'id' ? 'TRADING BARENG.' : 'TO TRADE TOGETHER.' }}</span>
            </div>

            <!-- Menu List -->
            <div class="pc-list">

                <a href="{{ route('member.wallet.index') }}" class="pc-item">
                    <span>{{ app()->getLocale() == 'id' ? 'Dompet Saya' : 'My Wallet' }}</span>
                    <i class="bi bi-chevron-right pc-chev"></i>
                </a>

                <a href="{{ route('member.deposit.history') }}" class="pc-item">
                    <span>{{ app()->getLocale() == 'id' ? 'Riwayat Deposit' : 'Deposit History' }}</span>
                    <i class="bi bi-chevron-right pc-chev"></i>
                </a>

                <a href="{{ route('member.withdraw.history') }}" class="pc-item">
                    <span>{{ app()->getLocale() == 'id' ? 'Riwayat Penarikan' : 'Withdrawal History' }}</span>
                    <i class="bi bi-chevron-right pc-chev"></i>
                </a>

                <a href="{{ route('member.balance.transfer') }}" class="pc-item">
                    <span>{{ app()->getLocale() == 'id' ? ' Transfer' : 'Transfer ' }}</span>
                    <i class="bi bi-chevron-right pc-chev"></i>
                </a>



                <a href="{{ route('member.team.index') }}" class="pc-item">
                    <span>{{ app()->getLocale() == 'id' ? 'Referral' : 'Referral' }}</span>
                    <i class="bi bi-chevron-right pc-chev"></i>
                </a>

                <a href="{{ route('member.verification.index') }}" class="pc-item">
                    <span>{{ app()->getLocale() == 'id' ? 'Verifikasi Identitas' : 'Real name authentication' }}</span>
                    <span class="pc-status">
                        @if(auth()->user()->is_verified)
                            <i class="bi bi-check-circle-fill pc-status-icon pc-status-ok"></i>
                            {{ app()->getLocale() == 'id' ? 'Terverifikasi' : 'Verified' }}
                        @else
                            <i class="bi bi-check-circle-fill pc-status-icon pc-status-pending"></i>
                            {{ app()->getLocale() == 'id' ? 'Belum' : 'Unrealized' }}
                        @endif
                    </span>
                </a>

            </div>

            <!-- Logout Button -->
            <div class="pc-logout-wrap">
                <a href="{{ route('logout') }}" class="pc-logout-btn"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    {{ app()->getLocale() == 'id' ? 'Keluar' : 'Log Out' }}
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>

        </div>
    </div>
</div>

<style>
/* ── Trigger ───────────────────────────────────────────── */
.profile-dropdown { position: relative; display: inline-block; }

.profile-trigger {
    cursor: pointer;
    background: #000000;
    border: none;
    font-size: 20px;
    color: #ffffff !important;
    padding: 10px;
    border-radius: 12px;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
}
.profile-trigger:hover { background: #1a1a1a; transform: scale(1.05); }
.profile-trigger i { color: #ffffff !important; }

/* ── Fullscreen-style panel (mimics "Personal Center" page) ── */
.profile-dropdown-menu {
    display: none;
    position: fixed;
    background: #0d1320;
    z-index: 9999;
    overflow: hidden;
    flex-direction: column;
}
.profile-dropdown-menu.show { display: flex; animation: fadeIn 0.2s ease; }

@keyframes fadeIn {
    from { opacity: 0; }
    to   { opacity: 1; }
}

/* ── Top bar ── */
.pc-topbar {
    display: flex; align-items: center; justify-content: center;
    position: relative;
    padding: 16px;
    background: #0d1320;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    flex-shrink: 0;
}
.pc-back {
    position: absolute; left: 16px;
    background: none; border: none; color: #fff;
    font-size: 20px; cursor: pointer; padding: 4px;
}
.pc-topbar-title { color: #fff; font-size: 16px; font-weight: 600; }
.pc-topbar-spacer { width: 20px; }

/* ── Scroll body ── */
.pc-scroll {
    overflow-y: auto;
    flex: 1;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;       /* Firefox */
    -ms-overflow-style: none;    /* IE/Edge */
}
.pc-scroll::-webkit-scrollbar {
    width: 0px;                  /* Chrome/Safari */
    background: transparent;
}

/* ── Header ── */
.pc-header { padding: 18px 18px 14px; }
.pc-email { color: #fff; font-size: 14px; font-weight: 500; word-break: break-all; }
.pc-uid { color: rgba(255,255,255,0.45); font-size: 12px; margin-top: 6px; }

/* ── Banner ── */
.pc-banner {
    margin: 4px 18px 18px;
    border-radius: 10px;
    height: 110px;
    background:
        linear-gradient(135deg, rgba(13,30,60,0.55), rgba(20,50,90,0.4)),
        radial-gradient(circle at 30% 30%, rgba(56,189,248,0.35), transparent 60%),
        linear-gradient(135deg, #0a1e3d, #142d52);
    display: flex; align-items: center; justify-content: center; text-align: center;
    color: #fff; font-size: 18px; font-weight: 800; line-height: 1.35;
    letter-spacing: 0.3px;
    box-shadow: inset 0 0 40px rgba(0,0,0,0.25);
}

/* ── List ── */
.pc-list { border-top: 1px solid rgba(255,255,255,0.06); }
.pc-item {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 18px;
    color: rgba(255,255,255,0.9);
    font-size: 14px; font-weight: 500;
    text-decoration: none;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    transition: background 0.15s;
}
.pc-item:hover, .pc-item:active { background: rgba(255,255,255,0.04); color: #fff; }
.pc-item-disabled { opacity: 0.45; cursor: not-allowed; }
.pc-item-disabled:hover { background: none; color: rgba(255,255,255,0.9); }
.pc-chev { color: rgba(255,255,255,0.2); font-size: 13px; }

.pc-status {
    display: inline-flex; align-items: center; gap: 6px;
    color: rgba(255,255,255,0.45); font-size: 13px;
}
.pc-status-icon { font-size: 15px; }
.pc-status-ok { color: #34d399; }
.pc-status-pending { color: rgba(255,255,255,0.3); }

/* ── Toggle row ── */
.pc-item-toggle { cursor: default; }
.pc-switch { position: relative; display: inline-block; width: 42px; height: 24px; }
.pc-switch input { opacity: 0; width: 0; height: 0; }
.pc-switch-slider {
    position: absolute; cursor: not-allowed; inset: 0;
    background: rgba(255,255,255,0.15);
    border-radius: 24px; transition: 0.2s;
}
.pc-switch-slider::before {
    content: ""; position: absolute;
    height: 18px; width: 18px; left: 3px; top: 3px;
    background: #fff; border-radius: 50%; transition: 0.2s;
}

/* ── Logout ── */
.pc-logout-wrap { padding: 22px 18px 32px; }
.pc-logout-btn {
    display: block;
    text-align: center;
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #ef4444, #dc2626);
    border-radius: 10px;
    color: #fff; font-size: 15px; font-weight: 700;
    text-decoration: none;
    transition: opacity 0.2s;
}
.pc-logout-btn:hover { opacity: 0.9; color: #fff; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropdownBtn = document.getElementById('profileDropdownBtn');
    const dropdownMenu = document.getElementById('profileDropdownMenu');
    const container    = document.querySelector('.mobile-container');
    const header        = document.querySelector('.fixed-header');
    const bottomNav      = document.querySelector('.bottom-nav');

    function positionDropdown() {
        if (!container) return;
        const rect = container.getBoundingClientRect();
        const headerH = header ? header.getBoundingClientRect().height : 0;
        const bottomH = bottomNav ? bottomNav.getBoundingClientRect().height : 0;

        dropdownMenu.style.left   = rect.left + 'px';
        dropdownMenu.style.width  = rect.width + 'px';
        dropdownMenu.style.top    = (rect.top + headerH) + 'px';
        dropdownMenu.style.bottom = (window.innerHeight - rect.bottom + bottomH) + 'px';
    }

    if (dropdownBtn && dropdownMenu) {
        dropdownBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            positionDropdown();
            dropdownMenu.classList.add('show');
            document.body.style.overflow = 'hidden';
        });

        window.addEventListener('resize', function() {
            if (dropdownMenu.classList.contains('show')) positionDropdown();
        });
    }
});
</script>