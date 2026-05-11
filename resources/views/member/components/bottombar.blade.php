<!-- Fixed Bottom Navbar -->
<div class="bottom-nav">
    <a href="{{ route('member.dashboard.index') }}"
        class="nav-item {{ request()->routeIs('member.dashboard.*') ? 'active' : '' }}">
        <i class="bi bi-house-door-fill"></i>
        <span>Welcome</span>
    </a>
    <a href="{{ route('member.market.index') }}"
        class="nav-item {{ request()->routeIs('member.market.*') ? 'active' : '' }}">
        <i class="bi bi-bar-chart-fill"></i>
        <span>Market</span>
    </a>
    <a href="{{ route('member.futures.index') }}"
        class="nav-item nav-item-futures {{ request()->routeIs('member.futures.*') || request()->routeIs('member.invest.*') ? 'active' : '' }}">
        <div class="futures-fab">
            <i class="bi bi-graph-up-arrow"></i>
        </div>
        <span>Futures</span>
    </a>
    <a href="{{ route('member.access.index') }}"
        class="nav-item {{ request()->routeIs('member.access.*') || request()->routeIs('member.wallet.*') || request()->routeIs('member.verification.*') || request()->routeIs('member.team.*') ? 'active' : '' }}">
        <i class="bi bi-shield-lock-fill"></i>
        <span>Access</span>
    </a>
    <a href="{{ route('member.profile.index') }}"
        class="nav-item {{ request()->routeIs('member.profile.*') || request()->routeIs('member.deposit.*') || request()->routeIs('member.withdraw.*') || request()->routeIs('member.balance.*') ? 'active' : '' }}">
        <i class="bi bi-wallet-fill"></i>
        <span>Wallet</span>
    </a>
</div>
