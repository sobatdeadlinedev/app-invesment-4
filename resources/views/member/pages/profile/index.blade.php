@extends('member.layouts.app')
@section('content')
    <!-- Scrollable Content Area -->
    <div class="scrollable-content">
        <div class="content-section">

            <!-- Flash Messages -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Section 1: Total Assets & PnL -->
            <div class="seamless-section mb-4">
                <div class="section-content">
                    <!-- Total Assets -->
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <p class="text-muted mb-1 small">{{ __('app.total_assets') }}</p>
                            <h2 class="text-white mb-0 fw-bold" style="font-size: 36px;">
                                {{ number_format($balanceBreakdown['total_balance'], 2) }}
                            </h2>
                            <small class="text-muted">USDT</small>
                        </div>
                    </div>

                    <!-- Today's PnL -->
                    <div class="info-box pnl-box">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block mb-1" style="font-size: 10px;">
                                    {{ __('app.today_pnl') }}
                                </small>
                                <h6 class="mb-0 fw-bold {{ $todayPnl >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 16px;">
                                    {{ $todayPnl >= 0 ? '+' : '' }}{{ number_format($todayPnl, 2) }} USDT
                                </h6>
                            </div>
                            <div class="pnl-icon-wrapper {{ $todayPnl >= 0 ? 'positive' : 'negative' }}">
                                <i
                                    class="bi bi-{{ $todayPnl >= 0 ? 'arrow-up-circle-fill' : 'arrow-down-circle-fill' }}"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Action Buttons -->
            <div class="seamless-section mb-4">
                <div class="section-content">
                    <div class="row g-2">
                        <div class="col-4">
                            <a href="{{ route('member.deposit.index') }}" class="btn-action-main">
                                <div class="action-icon deposit">
                                    <i class="bi bi-arrow-down-circle"></i>
                                </div>
                                <span>{{ __('app.deposit') }}</span>
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="{{ route('member.withdraw.index') }}" class="btn-action-main">
                                <div class="action-icon withdrawal">
                                    <i class="bi bi-arrow-up-circle"></i>
                                </div>
                                <span>{{ __('app.withdrawal') }}</span>
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="{{ route('member.balance.transfer') }}" class="btn-action-main">
                                <div class="action-icon transfer">
                                    <i class="bi bi-arrow-left-right"></i>
                                </div>
                                <span>{{ __('app.transfer') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: My Account -->
            <div class="seamless-section mb-4">
                <div class="section-header">
                    <h6 class="section-title">{{ __('app.my_account') }}</h6>
                </div>
                <div class="section-content">
                    <!-- Exchange Balance -->
                    <div class="account-item">
                        <div class="d-flex align-items-center gap-3">
                            <div class="account-icon exchange">
                                <i class="bi bi-wallet2"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1 small">{{ __('app.exchange') }}</p>
                                <h5 class="text-white mb-0 fw-bold">
                                    {{ number_format($balanceBreakdown['exchange_balance'], 2) }}
                                </h5>
                            </div>
                            <div class="text-end">
                                <i class="bi bi-chevron-right text-muted"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Trade Balance -->
                    <div class="account-item">
                        <div class="d-flex align-items-center gap-3">
                            <div class="account-icon trade">
                                <i class="bi bi-graph-up"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="text-muted mb-1 small">{{ __('app.trade') }}</p>
                                <h5 class="text-white mb-0 fw-bold">
                                    {{ number_format($balanceBreakdown['trade_balance'], 2) }}
                                </h5>
                                @if ($balanceBreakdown['locked_balance'] > 0)
                                    <small class="text-warning" style="font-size: 10px;">
                                        <i class="bi bi-lock-fill"></i>
                                        {{ number_format($balanceBreakdown['locked_balance'], 2) }} {{ __('app.locked') }}
                                    </small>
                                @endif
                            </div>
                            <div class="text-end">
                                <i class="bi bi-chevron-right text-muted"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Wallet List -->
            <div class="seamless-section mb-4">
                <div class="section-header">
                    <h6 class="section-title">{{ __('app.wallet_list') }}</h6>
                    <span class="badge-count">{{ $wallets->count() }}/3</span>
                </div>

                <!-- Currency Info -->
                <div class="currency-info-banner">
                    <div class="d-flex align-items-center gap-2">
                        <div class="currency-icon">
                            <span>₮</span>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small" style="font-size: 11px;">{{ __('app.currency') }}</p>
                            <h6 class="text-white mb-0 fw-bold" style="font-size: 13px;">{{ __('app.usdt_tether') }}</h6>
                        </div>
                    </div>
                </div>

                @forelse($wallets as $wallet)
                    <div class="wallet-item">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="d-flex align-items-start gap-3 flex-grow-1">
                                <div class="bank-icon-circle {{ $wallet->type }}">
                                    <i class="bi bi-wallet-fill"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="wallet-type-badge {{ $wallet->type }}">
                                            {{ strtoupper(__('app.' . $wallet->type)) }}
                                        </span>
                                    </div>
                                    <div class="text-white fw-bold mb-1" style="font-size: 13px;">
                                        {{ $wallet->account_number }}
                                    </div>
                                    <small class="text-gold" style="font-size: 11px;">
                                        <i class="bi bi-info-circle me-1"></i>{{ $wallet->getTypeLabel() }}
                                    </small>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn-bank-action btn-bank-edit"
                                    onclick="openEditModal({{ $wallet->id }}, '{{ $wallet->type }}', '{{ $wallet->account_number }}')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('member.wallet.destroy', $wallet->id) }}" method="POST"
                                    onsubmit="return confirm('{{ __('app.delete_wallet_confirmation') }}')"
                                    style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-bank-action btn-bank-delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="bi bi-wallet2"></i>
                        <p class="text-muted mb-0">{{ __('app.no_wallet_yet') }}</p>
                    </div>
                @endforelse

                <!-- Add Wallet Button -->
                <div class="section-footer">
                    <button class="btn btn-outline-gold w-100" data-bs-toggle="modal" data-bs-target="#addWalletModal"
                        @if ($wallets->count() >= 3) disabled @endif>
                        <i class="bi bi-plus-circle me-2"></i>{{ __('app.add_wallet') }}
                    </button>
                </div>
            </div>
            <!-- Section 5: Logout -->
           <div class="seamless-section mb-4">
    <div class="section-content">
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
            class="wallet-item" style="display: block; text-decoration: none; margin-bottom: 0;">
            <div class="d-flex align-items-center gap-3">
                <div class="bank-icon-circle"
                    style="background: rgba(0, 229, 255, 0.15); border-color: rgba(0, 229, 255, 0.3);">
                    <i class="bi bi-box-arrow-right" style="color: #00e5ff;"></i>
                </div>
                <div class="flex-grow-1">
                    <span class="text-white fw-bold" style="font-size: 14px;">{{ __('app.logout') }}</span>
                </div>
                <i class="bi bi-chevron-right text-muted"></i>
            </div>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</div>
        </div>
    </div>

    <!-- Modal Add Wallet -->
    <div class="modal fade" id="addWalletModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content"
                style="background-color: var(--card-light); border: 1px solid var(--border-color);">
                <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                    <h5 class="modal-title text-white">{{ __('app.add_wallet') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('member.wallet.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <!-- Currency Info in Modal -->
                        <div class="mb-3 p-3"
                            style="background: rgba(0, 229, 255, 0.05); border-radius: 8px; border: 1px solid var(--border-color);">
                            <div class="d-flex align-items-center gap-2">
                                <div
                                    style="width: 36px; height: 36px; background: rgba(0, 229, 255, 0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <span style="color: var(--gold-color); font-size: 18px; font-weight: bold;">₮</span>
                                </div>
                                <div>
                                    <p class="text-muted mb-0 small" style="font-size: 11px;">{{ __('app.currency') }}
                                    </p>
                                    <h6 class="text-white mb-0 fw-bold" style="font-size: 13px;">
                                        {{ __('app.usdt_tether') }}</h6>
                                </div>
                            </div>
                        </div>

                        <!-- Type Selection -->
                        <div class="mb-3">
                            <label class="form-label text-white">{{ __('app.network_type') }}</label>
                            <div class="network-type-selector">
                                <label class="network-type-option">
                                    <input type="radio" name="type" value="trc20" checked>
                                    <div class="network-type-card">
                                        <div class="network-icon trc20">
                                            <i class="bi bi-circle-fill"></i>
                                        </div>
                                        <div class="network-info">
                                            <div class="network-name">{{ __('app.trc20') }}</div>
                                            <small class="network-desc">{{ __('app.tron_network') }}</small>
                                        </div>
                                        <div class="network-check">
                                            <i class="bi bi-check-circle-fill"></i>
                                        </div>
                                    </div>
                                </label>
                                <label class="network-type-option">
                                    <input type="radio" name="type" value="bep20">
                                    <div class="network-type-card">
                                        <div class="network-icon bep20">
                                            <i class="bi bi-circle-fill"></i>
                                        </div>
                                        <div class="network-info">
                                            <div class="network-name">{{ __('app.bep20') }}</div>
                                            <small class="network-desc">{{ __('app.binance_smart_chain') }}</small>
                                        </div>
                                        <div class="network-check">
                                            <i class="bi bi-check-circle-fill"></i>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            @error('type')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Wallet Address -->
                        <div class="mb-3">
                            <label class="form-label text-white">{{ __('app.wallet_address') }}</label>
                            <input type="text" name="account_number" class="form-control-dark"
                                placeholder="{{ __('app.enter_wallet_address') }}" required
                                value="{{ old('account_number') }}">
                            <small class="text-muted d-block mt-1" style="font-size: 11px;">
                                <i class="bi bi-info-circle me-1"></i>{{ __('app.ensure_address_match') }}
                            </small>
                            @error('account_number')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                        <button type="button" class="btn btn-outline-gold"
                            data-bs-dismiss="modal">{{ __('app.cancel') }}</button>
                        <button type="submit" class="btn btn-gold">{{ __('app.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Wallet -->
    <div class="modal fade" id="editWalletModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content"
                style="background-color: var(--card-light); border: 1px solid var(--border-color);">
                <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                    <h5 class="modal-title text-white">{{ __('app.edit_wallet') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editWalletForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <!-- Currency Info in Modal -->
                        <div class="mb-3 p-3"
                            style="background: rgba(0, 229, 255, 0.05); border-radius: 8px; border: 1px solid var(--border-color);">
                            <div class="d-flex align-items-center gap-2">
                                <div
                                    style="width: 36px; height: 36px; background: rgba(0, 229, 255, 0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <span style="color: var(--gold-color); font-size: 18px; font-weight: bold;">₮</span>
                                </div>
                                <div>
                                    <p class="text-muted mb-0 small" style="font-size: 11px;">{{ __('app.currency') }}
                                    </p>
                                    <h6 class="text-white mb-0 fw-bold" style="font-size: 13px;">
                                        {{ __('app.usdt_tether') }}</h6>
                                </div>
                            </div>
                        </div>

                        <!-- Type Selection -->
                        <div class="mb-3">
                            <label class="form-label text-white">{{ __('app.network_type') }}</label>
                            <div class="network-type-selector">
                                <label class="network-type-option">
                                    <input type="radio" name="type" value="trc20" id="edit_type_trc20">
                                    <div class="network-type-card">
                                        <div class="network-icon trc20">
                                            <i class="bi bi-circle-fill"></i>
                                        </div>
                                        <div class="network-info">
                                            <div class="network-name">{{ __('app.trc20') }}</div>
                                            <small class="network-desc">{{ __('app.tron_network') }}</small>
                                        </div>
                                        <div class="network-check">
                                            <i class="bi bi-check-circle-fill"></i>
                                        </div>
                                    </div>
                                </label>
                                <label class="network-type-option">
                                    <input type="radio" name="type" value="bep20" id="edit_type_bep20">
                                    <div class="network-type-card">
                                        <div class="network-icon bep20">
                                            <i class="bi bi-circle-fill"></i>
                                        </div>
                                        <div class="network-info">
                                            <div class="network-name">{{ __('app.bep20') }}</div>
                                            <small class="network-desc">{{ __('app.binance_smart_chain') }}</small>
                                        </div>
                                        <div class="network-check">
                                            <i class="bi bi-check-circle-fill"></i>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            @error('type')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Wallet Address -->
                        <div class="mb-3">
                            <label class="form-label text-white">{{ __('app.wallet_address') }}</label>
                            <input type="text" name="account_number" id="edit_account_number"
                                class="form-control-dark" placeholder="{{ __('app.enter_wallet_address') }}" required>
                            <small class="text-muted d-block mt-1" style="font-size: 11px;">
                                <i class="bi bi-info-circle me-1"></i>{{ __('app.ensure_address_match') }}
                            </small>
                            @error('account_number')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                        <button type="button" class="btn btn-outline-gold"
                            data-bs-dismiss="modal">{{ __('app.cancel') }}</button>
                        <button type="submit" class="btn btn-gold">{{ __('app.update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* Seamless Section Styles */
        .seamless-section {
            background: transparent;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            margin-bottom: 12px;
        }

        .section-title {
            color: var(--text-primary);
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .section-content {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 16px;
            backdrop-filter: blur(10px);
        }

        .section-footer {
            padding: 16px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 0 0 16px 16px;
            margin-top: -12px;
        }

        /* Balance Icon Wrapper */
        .balance-icon-wrapper {
            width: 48px;
            height: 48px;
            background: rgba(0, 229, 255, 0.15);
            border: 1px solid rgba(0, 229, 255, 0.3);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .balance-icon-wrapper i {
            font-size: 24px;
            color: var(--gold-color);
        }

        /* Info Box */
        .info-box {
            padding: 12px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            border: 1px solid rgba(0, 229, 255, 0.1);
        }

        /* PnL Icon Wrapper */
        .pnl-icon-wrapper {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .pnl-icon-wrapper i {
            font-size: 20px;
        }

        .pnl-icon-wrapper.positive {
            background: rgba(0, 229, 255, 0.15);
            border: 1px solid rgba(0, 229, 255, 0.3);
        }

        .pnl-icon-wrapper.positive i {
            color: var(--gold-color);
        }

        .pnl-icon-wrapper.negative {
            background: rgba(0, 229, 255, 0.15);
            border: 1px solid rgba(0, 229, 255, 0.3);
        }

        .pnl-icon-wrapper.negative i {
            color: #00e5ff;
        }

        /* Action Buttons */
        .btn-action-main {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            padding: 16px 8px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-action-main:hover {
            background: rgba(0, 229, 255, 0.1);
            transform: translateY(-2px);
        }

        .action-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid;
        }

        .action-icon i {
            font-size: 24px;
        }

        /* Deposit */
        .action-icon.deposit {
            background: rgba(0, 229, 255, 0.15);
            border-color: rgba(0, 229, 255, 0.3);
        }

        .action-icon.deposit i {
            color: var(--gold-color);
        }

        /* Withdrawal */
        .action-icon.withdrawal {
            background: rgba(0, 229, 255, 0.15);
            border-color: rgba(0, 229, 255, 0.3);
        }

        .action-icon.withdrawal i {
            color: var(--gold-color);
        }

        /* Transfer */
        .action-icon.transfer {
            background: rgba(0, 229, 255, 0.15);
            border-color: rgba(0, 229, 255, 0.3);
        }

        .action-icon.transfer i {
            color: #00e5ff;
        }

        .btn-action-main span {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-primary);
        }

        /* Account Item */
        .account-item {
            padding: 14px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            margin-bottom: 8px;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .account-item:hover {
            background: rgba(0, 229, 255, 0.08);
            transform: translateX(4px);
        }

        .account-item:last-child {
            margin-bottom: 0;
        }

        .account-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid;
            flex-shrink: 0;
        }

        .account-icon i {
            font-size: 22px;
        }

        /* Exchange */
        .account-icon.exchange {
            background: rgba(0, 229, 255, 0.15);
            border-color: rgba(0, 229, 255, 0.3);
        }

        .account-icon.exchange i {
            color: var(--gold-color);
        }

        /* Trade */
        .account-icon.trade {
            background: rgba(0, 229, 255, 0.15);
            border-color: rgba(0, 229, 255, 0.3);
        }

        .account-icon.trade i {
            color: #00e5ff;
        }

        /* Currency Info Banner */
        .currency-info-banner {
            padding: 12px 16px;
            background: rgba(0, 229, 255, 0.08);
            border-radius: 12px;
            margin-bottom: 8px;
        }

        .currency-icon {
            width: 36px;
            height: 36px;
            background: rgba(0, 229, 255, 0.2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .currency-icon span {
            color: var(--gold-color);
            font-size: 18px;
            font-weight: bold;
        }

        /* Wallet Item */
        .wallet-item {
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            margin-bottom: 8px;
            transition: all 0.2s ease;
        }

        .wallet-item:hover {
            background: rgba(0, 229, 255, 0.08);
            transform: translateX(4px);
        }

        /* Empty State */
        .empty-state {
            padding: 32px 20px;
            text-align: center;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 12px;
            margin-bottom: 8px;
        }

        .empty-state i {
            font-size: 42px;
            color: var(--text-muted);
            opacity: 0.3;
            margin-bottom: 10px;
            display: block;
        }

        /* Wallet Type Badge */
        .wallet-type-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .wallet-type-badge.trc20 {
            background: rgba(0, 229, 255, 0.15);
            color: var(--gold-color);
            border: 1px solid rgba(0, 229, 255, 0.3);
        }

        .wallet-type-badge.bep20 {
            background: rgba(0, 229, 255, 0.15);
            color: #00e5ff;
            border: 1px solid rgba(0, 229, 255, 0.3);
        }

        /* Bank Icon with Color Type */
        .bank-icon-circle {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid;
            flex-shrink: 0;
        }

        .bank-icon-circle i {
            font-size: 22px;
        }

        .bank-icon-circle.trc20 {
            background: rgba(0, 229, 255, 0.15);
            border-color: rgba(0, 229, 255, 0.3);
        }

        .bank-icon-circle.trc20 i {
            color: var(--gold-color);
        }

        .bank-icon-circle.bep20 {
            background: rgba(0, 229, 255, 0.15);
            border-color: rgba(0, 229, 255, 0.3);
        }

        .bank-icon-circle.bep20 i {
            color: #00e5ff;
        }

        /* Bank Action Buttons */
        .btn-bank-action {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: 1px solid;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-bank-action i {
            font-size: 14px;
        }

        .btn-bank-edit {
            border-color: rgba(0, 229, 255, 0.3);
            color: var(--gold-color);
        }

        .btn-bank-edit:hover {
            background: rgba(0, 229, 255, 0.15);
        }

        .btn-bank-delete {
            border-color: rgba(0, 229, 255, 0.3);
            color: #00e5ff;
        }

        .btn-bank-delete:hover {
            background: rgba(0, 229, 255, 0.15);
        }

        /* Badge Count */
        .badge-count {
            background: rgba(0, 229, 255, 0.15);
            border: 1px solid rgba(0, 229, 255, 0.3);
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            color: var(--gold-color);
        }

        /* Network Type Selector */
        .network-type-selector {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .network-type-option {
            cursor: pointer;
            margin: 0;
        }

        .network-type-option input[type="radio"] {
            display: none;
        }

        .network-type-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.03);
            border: 2px solid var(--border-color);
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .network-type-option:hover .network-type-card {
            background: rgba(0, 229, 255, 0.05);
            border-color: rgba(0, 229, 255, 0.3);
        }

        .network-type-option input[type="radio"]:checked~.network-type-card {
            background: rgba(0, 229, 255, 0.1);
            border-color: var(--gold-color);
        }

        .network-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .network-icon i {
            font-size: 20px;
        }

        .network-icon.trc20 {
            background: rgba(0, 229, 255, 0.15);
            border: 1px solid rgba(0, 229, 255, 0.3);
        }

        .network-icon.trc20 i {
            color: var(--gold-color);
        }

        .network-icon.bep20 {
            background: rgba(0, 229, 255, 0.15);
            border: 1px solid rgba(0, 229, 255, 0.3);
        }

        .network-icon.bep20 i {
            color: #00e5ff;
        }

        .network-info {
            flex-grow: 1;
        }

        .network-name {
            color: var(--text-white);
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 2px;
        }

        .network-desc {
            color: var(--text-muted);
            font-size: 11px;
        }

        .network-check {
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .network-check i {
            font-size: 20px;
            color: var(--gold-color);
        }

        .network-type-option input[type="radio"]:checked~.network-type-card .network-check {
            opacity: 1;
        }

        /* Logout Button */
        .btn-logout {
            display: block;
            padding: 14px 16px;
            text-decoration: none;
            transition: all 0.2s ease;
            border-radius: 12px;
        }

        .btn-logout:hover {
            background: rgba(0, 229, 255, 0.05);
            transform: translateX(4px);
        }

        .logout-icon {
            width: 48px;
            height: 48px;
            background: rgba(0, 229, 255, 0.15);
            border: 1px solid rgba(0, 229, 255, 0.3);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .logout-icon i {
            font-size: 22px;
            color: #00e5ff;
        }

        .logout-text {
            color: var(--text-white);
            font-weight: 600;
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 375px) {
            .section-content {
                padding: 14px;
            }

            .section-footer {
                padding: 14px;
            }

            .btn-action-main {
                padding: 12px 6px;
            }

            .action-icon {
                width: 40px;
                height: 40px;
            }

            .action-icon i {
                font-size: 20px;
            }

            .btn-action-main span {
                font-size: 11px;
            }
        }
    </style>

    <script>
        function openEditModal(id, type, accountNumber) {
            document.getElementById('editWalletForm').action = "{{ url('member/wallet') }}/" + id;
            document.getElementById('edit_account_number').value = accountNumber;

            if (type === 'trc20') {
                document.getElementById('edit_type_trc20').checked = true;
            } else {
                document.getElementById('edit_type_bep20').checked = true;
            }

            var editModal = new bootstrap.Modal(document.getElementById('editWalletModal'));
            editModal.show();
        }

        // Auto hide alerts after 5 seconds
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
@endsection
