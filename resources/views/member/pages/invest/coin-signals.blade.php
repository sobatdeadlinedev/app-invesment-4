@extends('member.layouts.app')
@section('content')
    <div class="scrollable-content">
        <div class="content-section" style="padding: 0;">

            {{-- COIN HEADER - Seamless dengan button switch --}}
            <div class="seamless-header-section">
                <div class="d-flex align-items-center gap-3">
                    <div class="coin-icon-large"
                        style="background: linear-gradient(135deg, {{ $coinInfo['color'] }}33 0%, {{ $coinInfo['color'] }}1a 100%); 
                               border: 2px solid {{ $coinInfo['color'] }}66;">
                        <i class="bi {{ $coinInfo['icon'] }}" style="color: {{ $coinInfo['color'] }};"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-1 fw-bold" style="color: var(--text-primary); font-size: 18px;">
                            {{ $coinInfo['symbol'] }}
                        </h5>
                        <small class="text-muted">{{ $coinInfo['name'] }}</small>
                    </div>
                    <div class="text-end">
                        {{-- BUTTON SWITCH COIN --}}
                        <button onclick="openCoinPopup()" class="btn-switch-coin mb-2">
                            <i class="bi bi-arrow-left-right"></i>
                        </button>
                        <h6 class="text-gold mb-0 fw-bold" style="font-size: 20px;">{{ count($openSignals) }}</h6>
                        <small class="text-muted" style="font-size: 11px;">{{ __('app.open_signals') }}</small>
                    </div>
                </div>
            </div>

            {{-- POPUP MODAL - Coin Selector --}}
            <div id="coinPopupOverlay" class="coin-popup-overlay" onclick="closeCoinPopup()" style="display: none;">
                <div class="coin-popup-modal" onclick="event.stopPropagation()">
                    <div class="popup-header">
                        <h6 class="mb-0 fw-bold">{{ __('app.select_coin') }}</h6>
                        <button class="btn-popup-close" onclick="closeCoinPopup()">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <div class="popup-body">
                        @foreach ($allCoins as $symbol => $info)
                            <a href="{{ route('member.invest.coin', ['coin' => strtolower($symbol)]) }}"
                                class="coin-popup-item {{ $symbol === $coin ? 'active' : '' }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="coin-icon-small"
                                        style="background: linear-gradient(135deg, {{ $info['color'] }} 0%, {{ $info['color'] }}dd 100%);">
                                        <i class="bi {{ $info['icon'] }}"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold">{{ $info['symbol'] }}</div>
                                        <small class="text-muted">{{ $info['name'] }}</small>
                                    </div>
                                    <div class="text-end">
                                        @if ($signalCounts[$symbol] > 0)
                                            <span class="badge">
                                                {{ $signalCounts[$symbol] }} {{ __('app.signals') }}
                                            </span>
                                        @else
                                            <small class="text-muted">{{ __('app.no_signals') }}</small>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- WARNING NOTICE - Seamless --}}
            @if (!auth()->user()->canJoinSignal())
                <div class="seamless-warning-notice">
                    <div class="d-flex align-items-start gap-3">
                        <div class="warning-icon-wrapper">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold mb-1" style="font-size: 13px; color: #dc3545;">{{ __('app.notice') }}</div>
                            <p class="mb-0" style="font-size: 12px; color: #dc3545; line-height: 1.6;">
                                {!! __('app.minimum_balance_required') !!}
                                {!! __('app.please_transfer_funds', ['url' => route('member.balance.transfer')]) !!}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- SUCCESS ALERT --}}
            @if (session('success'))
                <div class="seamless-alert seamless-alert-success">
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-check-circle-fill" style="font-size: 20px; color: #28a745; flex-shrink: 0;"></i>
                        <div class="flex-grow-1">
                            <p class="mb-0" style="font-size: 13px; color: #28a745;">{{ session('success') }}</p>
                        </div>
                        <button type="button" class="btn-alert-close" onclick="this.parentElement.parentElement.remove()">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>
            @endif

            {{-- ERROR ALERT --}}
            @if (session('error'))
                <div class="seamless-alert seamless-alert-danger">
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-exclamation-circle-fill"
                            style="font-size: 20px; color: #dc3545; flex-shrink: 0;"></i>
                        <div class="flex-grow-1">
                            <p class="mb-0" style="font-size: 13px; color: #dc3545;">{{ session('error') }}</p>
                        </div>
                        <button type="button" class="btn-alert-close" onclick="this.parentElement.parentElement.remove()">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>
            @endif

            {{-- CHART SECTION - Full Width Seamless --}}
            <div class="seamless-chart-wrapper">
                <iframe id="tradingViewChart"
                    src="https://www.tradingview.com/widgetembed/?symbol={{ $coinInfo['tradingview_symbol'] }}&interval=60&theme=dark&style=1&locale=en&toolbar_bg=131d2e&enable_publishing=false&hidesidetoolbar=1&allow_symbol_change=0&show_popup_button=0&details=0&calendar=0&studies=%5B%5D"
                    style="width: 100%; height: 450px; border: none; display: block;" frameborder="0"
                    allowtransparency="true" scrolling="no">
                </iframe>
            </div>

            {{-- TABS NAVIGATION - Seamless Modern --}}
            <div class="seamless-tabs-wrapper">
                <div class="signal-tabs-modern">
                    <button class="tab-btn-modern active" data-tab="signals">
                        <i class="bi bi-broadcast"></i>
                        <span>{{ __('app.trading_signals') }}</span>
                    </button>
                    <button class="tab-btn-modern" data-tab="history">
                        <i class="bi bi-clock-history"></i>
                        <span>{{ __('app.historical_orders') }}</span>
                    </button>
                </div>
            </div>

            {{-- TAB CONTENT: TRADING SIGNALS (DETAILED) --}}
            <div class="tab-content active" id="tab-signals">
                <div class="seamless-tab-header">
                    <h6 class="mb-0 fw-bold" style="color: var(--text-primary); font-size: 14px;">
                        {{ __('app.available_trading_signals') }}
                    </h6>
                    <span class="badge-signals-count">{{ count($openSignals) }} {{ __('app.signals') }}</span>
                </div>

                <div class="seamless-signals-list">
                    @forelse($openSignals as $signal)
                        @php
                            $isPending = $signal->result === 'pending' || $signal->result === null;
                            $betAmountPreview = $signal->betAmountPreview ?? 0;
                            $hasJoined = in_array($signal->id, $joinedSignalIds);

                            // Check sufficient balance based on bet type
                            $hasSufficientBalance =
                                $signal->bet_type == 'percentage'
                                    ? auth()->user()->canJoinSignal()
                                    : auth()->user()->getAvailableTradeBalance() >= $betAmountPreview;

                            // Prepare bet type text for confirmation
                            $betTypeText =
                                $signal->bet_type == 'percentage'
                                    ? number_format($signal->bet_value, 2) . __('app.of_balance')
                                    : __('app.fixed') . ' ' . number_format($signal->bet_value, 2) . ' USDT';
                        @endphp

                        {{-- SIGNAL CARD DETAILED --}}
                        <div class="signal-card-detailed">
                            {{-- Header --}}
                            <div class="signal-header">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-2 fw-bold" style="font-size: 16px; color: var(--text-primary);">
                                            <i class="bi bi-broadcast text-gold me-2"></i>{{ $signal->title }}
                                        </h6>
                                        @if ($signal->description)
                                            <p class="text-muted mb-0" style="font-size: 13px;">
                                                {{ $signal->description }}
                                            </p>
                                        @endif
                                    </div>
                                    <span class="badge-status-open">
                                        <i class="bi bi-circle-fill" style="font-size: 6px;"></i> {{ __('app.open') }}
                                    </span>
                                </div>
                            </div>

                            {{-- Signal Configuration
                            <div class="signal-config-section mb-3">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block"
                                            style="font-size: 11px;">{{ __('app.signal_bet_type') }}</small>
                                        @if ($signal->bet_type == 'percentage')
                                            <span class="badge-bet-type badge-percentage">
                                                {{ number_format($signal->bet_value, 2) }}{{ __('app.of_balance') }}
                                            </span>
                                        @else
                                            <span class="badge-bet-type badge-fixed">
                                                {{ number_format($signal->bet_value, 2) }} USDT {{ __('app.fixed') }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block"
                                            style="font-size: 11px;">{{ __('app.signal_access') }}</small>
                                        @if ($signal->is_public)
                                            <span class="badge-access badge-public">
                                                <i class="bi bi-people"></i> {{ __('app.public') }}
                                            </span>
                                        @else
                                            <span class="badge-access badge-private">
                                                <i class="bi bi-lock"></i> {{ __('app.private') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div> --}}

                            {{-- Bet Preview --}}
                            <div class="bet-preview-section mb-3">
                                <div class="row g-3">
                                    <div class="col-4">
                                        <small class="text-muted d-block"
                                            style="font-size: 11px;">{{ __('app.your_balance') }}</small>
                                        <small class="fw-bold" style="color: var(--text-primary); font-size: 13px;">
                                            $ {{ number_format(auth()->user()->trade_balance, 2) }}
                                        </small>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block"
                                            style="font-size: 11px;">{{ __('app.your_bet') }}</small>
                                        <small class="text-gold fw-bold" style="font-size: 13px;">
                                            $ {{ number_format($betAmountPreview, 2) }}
                                        </small>
                                    </div>
                                    {{-- <div class="col-4">
                                        <small class="text-muted d-block"
                                            style="font-size: 11px;">{{ __('app.participants') }}</small>
                                        <small class="fw-bold" style="color: var(--text-primary); font-size: 13px;">
                                            {{ $signal->participants_count }}
                                        </small>
                                    </div> --}}
                                </div>
                            </div>

                            {{-- Calculation Explanation --}}
                            {{-- @if ($signal->bet_type == 'percentage')
                                <div class="calculation-box mb-3">
                                    <i class="bi bi-calculator me-2"></i>
                                    <strong>{{ __('app.calculation') }}</strong>
                                    {{ number_format(auth()->user()->trade_balance, 2) }}
                                    × {{ number_format($signal->bet_value, 2) }}% =
                                    {{ number_format($betAmountPreview, 2) }} USDT
                                </div>
                            @else
                                <div class="calculation-box mb-3">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>{{ __('app.fixed_bet') }}</strong>
                                    {{ __('app.all_participants_bet_exactly') }}
                                    {{ number_format($signal->bet_value, 2) }} USDT {{ __('app.regardless_of_balance') }}
                                </div>
                            @endif --}}

                            {{-- Price Information
                            <div class="price-info-section mb-3">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block"
                                            style="font-size: 11px;">{{ __('app.opening_price') }}</small>
                                        <small class="fw-bold" style="color: var(--text-primary); font-size: 13px;">
                                            @if ($isPending || !$signal->entry_price)
                                                ~
                                            @else
                                                $ {{ number_format($signal->entry_price, 2) }}
                                            @endif
                                        </small>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block"
                                            style="font-size: 11px;">{{ __('app.settlement_price') }}</small>
                                        <small class="text-gold fw-bold" style="font-size: 13px;">
                                            @if ($isPending || !$signal->target_price)
                                                ~
                                            @else
                                                $ {{ number_format($signal->target_price, 2) }}
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div> --}}

                            {{-- Signal Timing --}}
                            {{-- <div class="timing-info-section mb-3">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block"
                                            style="font-size: 11px;">{{ __('app.opened_at') }}</small>
                                        <small class="fw-bold" style="color: var(--text-primary); font-size: 12px;">
                                            @if ($signal->opened_at)
                                                {{ $signal->opened_at->format('M d, Y H:i') }}
                                            @else
                                                ~
                                            @endif
                                        </small>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block"
                                            style="font-size: 11px;">{{ __('app.win_rate') }}</small>
                                        <small class="fw-bold" style="color: var(--text-primary); font-size: 12px;">
                                            @if ($isPending || !$signal->rate_of_return)
                                                ~
                                            @else
                                                {{ number_format($signal->rate_of_return, 2) }}%
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div> --}}

                            {{-- Good News Alert --}}
                            {{-- <div class="good-news-alert mb-3">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>{{ __('app.good_news') }}</strong> {{ __('app.no_losses_no_fees') }}
                            </div> --}}

                            {{-- Action Section --}}
                            <div class="signal-action-section">
                                @if ($hasJoined)
                                    <div class="alert-joined">
                                        <i class="bi bi-check-circle me-2"></i>
                                        {{ __('app.you_have_joined') }}
                                    </div>
                                @else
                                    @if ($hasSufficientBalance)
                                        <form action="{{ route('member.signals.join', $signal->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-join-signal w-100"
                                                onclick="return confirm('{{ __('app.join_signal_confirmation', ['bet_amount' => number_format($betAmountPreview, 2)]) }}')">
                                                <i class="bi bi-check-circle me-2"></i>{{ __('app.join_this_signal') }}
                                            </button>
                                        </form>
                                    @else
                                        <div class="alert-insufficient">
                                            <i class="bi bi-exclamation-triangle me-2"></i>
                                            <strong>{{ __('app.insufficient_balance_message') }}</strong>
                                            @if ($signal->bet_type == 'percentage')
                                                {{ __('app.minimum_balance_required_short') }}
                                            @else
                                                {{ __('app.you_need_at_least') }}
                                                ${{ number_format($betAmountPreview, 2) }} {{ __('app.available_short') }}
                                            @endif
                                            <a href="{{ route('member.balance.transfer') }}"
                                                class="text-white"><u>{{ __('app.transfer_now') }}</u></a>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="seamless-empty-state">
                            <i class="bi bi-broadcast-pin"></i>
                            <p class="mb-1">{{ __('app.no_open_signals_for') }} {{ $coinInfo['name'] }}</p>
                            <small>{{ __('app.check_back_later') }}</small>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- TAB CONTENT: HISTORICAL ORDERS (DYNAMIC PER COIN) --}}
            <div class="tab-content" id="tab-history">
                <div class="seamless-tab-header">
                    <h6 class="mb-0 fw-bold" style="color: var(--text-primary); font-size: 14px;">
                        {{ __('app.your_historical_orders') }} {{ $coinInfo['name'] }}
                    </h6>
                    <span class="badge-signals-count">{{ $totalJoinedThisCoin }} {{ __('app.orders') }}</span>
                </div>

                {{-- Statistics Cards --}}
                @if ($totalJoinedThisCoin > 0)
                    <div class="history-stats-section">
                        <div class="row g-2">
                            <div class="col-3">
                                <div class="stat-card">
                                    <small class="text-muted">{{ __('app.total') }}</small>
                                    <div class="fw-bold text-white">{{ $totalJoinedThisCoin }}</div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="stat-card">
                                    <small class="text-muted">{{ __('app.win_rate') }}</small>
                                    <div class="fw-bold text-{{ $winRateThisCoin >= 50 ? 'success' : 'danger' }}">
                                        {{ number_format($winRateThisCoin, 1) }}%
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="stat-card">
                                    <small class="text-muted">{{ __('app.profit_loss') }}</small>
                                    <div class="fw-bold text-{{ $totalProfitLossThisCoin >= 0 ? 'success' : 'danger' }}">
                                        {{ $totalProfitLossThisCoin >= 0 ? '+' : '' }}${{ number_format($totalProfitLossThisCoin, 0) }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="stat-card">
                                    <small class="text-muted">{{ __('app.fees') }}</small>
                                    <div class="fw-bold text-warning">
                                        ${{ number_format($totalFeesThisCoin, 0) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- History List --}}
                <div class="seamless-history-list">
                    @forelse($historyForThisCoin as $participant)
                        @php
                            $signal = $participant->signal;
                            $isPending = $signal->status != 'settled' || $signal->result === null;
                            $isWin = $signal->result === 'win';
                            $isSettled = $participant->status === 'settled';

                            $profitLossAmount = $participant->profit_loss ?? 0;
                            $feeAmount = $participant->fee_amount ?? 0;
                            $netResult = $profitLossAmount - $feeAmount;
                            $isFinalProfit = $netResult > 0;

                            // Direction
                            $direction = '';
                            $directionIcon = '';
                            $textColor = 'text-muted';
                            $userOutcome = '';

                            if ($isPending) {
                                $direction = __('app.pending_status');
                                $textColor = 'text-warning';
                            } else {
                                $adminChoice = strtolower($signal->admin_choice ?? '');
                                if ($adminChoice === 'call') {
                                    $direction = __('app.call');
                                    $directionIcon = '↑';
                                    $textColor = 'text-success';
                                } elseif ($adminChoice === 'put') {
                                    $direction = __('app.put');
                                    $directionIcon = '↓';
                                    $textColor = 'text-danger';
                                } else {
                                    $direction = __('app.na');
                                }

                                if ($isSettled) {
                                    if ($signal->result === 'win') {
                                        $userOutcome =
                                            '<i class="bi bi-check-circle-fill text-success ms-1" style="font-size: 10px;"></i>';
                                    } else {
                                        $userOutcome =
                                            '<i class="bi bi-x-circle-fill text-danger ms-1" style="font-size: 10px;"></i>';
                                    }
                                }
                            }
                        @endphp

                        <div class="history-item">
                            {{-- Header --}}
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span
                                        class="badge {{ $signal->result === 'win' ? 'badge-success' : ($signal->result === 'loss' ? 'badge-danger' : 'badge-warning') }}"
                                        style="font-size: 11px; color: #212121 !important;">
                                        {{ strtoupper($signal->title ?? 'SIGNAL') }}
                                    </span>
                                </div>
                                <span class="fw-bold {{ $textColor }}" style="font-size: 12px;">
                                    {{ $direction }} {{ $directionIcon }} {!! $userOutcome !!}
                                </span>
                            </div>

                            {{-- Details --}}
                            <div class="d-flex flex-column gap-2">
                                {{-- Time Period --}}
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted" style="font-size: 12px;">{{ __('app.time_period') }}</span>
                                    <span class="text-white" style="font-size: 12px;">
                                        @if ($isPending)
                                            ~
                                        @else
                                            {{ $signal->opened_at ? $signal->opened_at->format('H:i') : '-' }} -
                                            {{ $signal->closed_at ? $signal->closed_at->format('H:i') : '-' }}
                                        @endif
                                    </span>
                                </div>

                                {{-- Fee Amount --}}
                                @if (!$isPending && $isSettled && $feeAmount > 0)
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted"
                                            style="font-size: 12px;">{{ __('app.trading_fee') }}</span>
                                        <span class="text-warning" style="font-size: 12px;">
                                            -{{ number_format($feeAmount, 2) }}
                                        </span>
                                    </div>
                                @endif

                                {{-- Net Result --}}
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted"
                                        style="font-size: 12px;">{{ __('app.net_profit_loss') }}</span>
                                    <span class="text-{{ $isFinalProfit ? 'success' : 'danger' }} fw-bold"
                                        style="font-size: 12px;">
                                        @if ($isPending)
                                            ~
                                        @else
                                            {{ $isSettled ? ($netResult >= 0 ? '+' : '') . number_format($netResult, 2) : '-' }}
                                        @endif
                                    </span>
                                </div>

                                {{-- Rate of Return --}}
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted"
                                        style="font-size: 12px;">{{ __('app.rate_of_return') }}</span>
                                    <span class="text-white" style="font-size: 12px;">
                                        @if ($isPending)
                                            ~
                                        @else
                                            {{ $isSettled ? number_format($signal->rate_of_return, 2) . '%' : '-' }}
                                        @endif
                                    </span>
                                </div>

                                {{-- Order Quantity --}}
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted"
                                        style="font-size: 12px;">{{ __('app.order_quantity') }}</span>
                                    <span class="text-white" style="font-size: 12px;">
                                        {{ number_format($participant->bet_amount, 2) }}
                                    </span>
                                </div>

                                {{-- Opening Price --}}
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted"
                                        style="font-size: 12px;">{{ __('app.opening_price') }}</span>
                                    <span class="text-white" style="font-size: 12px;">
                                        @if ($isPending)
                                            ~
                                        @else
                                            {{ number_format($signal->entry_price, 3) }}
                                        @endif
                                    </span>
                                </div>

                                {{-- Settlement Price --}}
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted"
                                        style="font-size: 12px;">{{ __('app.settlement_price') }}</span>
                                    <span class="text-white" style="font-size: 12px;">
                                        @if ($isPending)
                                            ~
                                        @else
                                            {{ $isSettled ? number_format($signal->target_price, 3) : '-' }}
                                        @endif
                                    </span>
                                </div>

                                {{-- Order Time --}}
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted" style="font-size: 12px;">{{ __('app.order_time') }}</span>
                                    <span class="text-white" style="font-size: 12px;">
                                        {{ $participant->joined_at->format('Y-m-d H:i:s') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="seamless-empty-state">
                            <i class="bi bi-clock-history"></i>
                            <p class="mb-1">{{ __('app.no_historical_orders_for') }} {{ $coinInfo['name'] }}</p>
                            <small>{{ __('app.join_signals_to_start') }}</small>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                @if ($historyForThisCoin->hasPages())
                    <div class="history-pagination">
                        {{ $historyForThisCoin->links() }}
                    </div>
                @endif
            </div>

            {{-- INFO SECTION - Seamless
            <div class="seamless-info-section">
                <div class="d-flex align-items-start gap-3">
                    <i class="bi bi-info-circle-fill text-gold"
                        style="font-size: 20px; margin-top: 2px; flex-shrink: 0;"></i>
                    <div>
                        <h6 class="mb-2 fw-bold" style="font-size: 13px; color: var(--text-primary);">
                            {{ __('app.how_it_works') }}</h6>
                        <ul class="small text-muted mb-0 ps-3" style="font-size: 12px; line-height: 1.8;">
                            <li>{{ __('app.bet_amount_varies') }}</li>
                            <li>{{ __('app.percentage_signals') }}</li>
                            <li>{{ __('app.fixed_signals') }}</li>
                            <li>{{ __('app.minimum_balance_info') }}</li>
                            <li>{{ __('app.bet_locked_info') }}</li>
                            <li><strong style="color: #28a745;">{{ __('app.always_win_rewards') }}</strong></li>
                            <li>{{ __('app.call_put_explanation') }}</li>
                        </ul>
                    </div>
                </div>
            </div> --}}

        </div>
    </div>

    @push('styles')
        <style>
            /* ===== EXISTING STYLES (Keep all previous styles) ===== */
            /* Seamless Header Section */
            .seamless-header-section {
                padding: 20px;
                background: transparent;
                border-bottom: 1px solid var(--border-color);
            }

            /* Button Switch Coin - NEW */
            .btn-switch-coin {
                background: linear-gradient(135deg, var(--gold-color) 0%, #00b8d4 100%);
                border: none;
                border-radius: 8px;
                width: 36px;
                height: 36px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s ease;
                box-shadow: 0 2px 8px rgba(0, 229, 255, 0.3);
            }

            .btn-switch-coin:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 229, 255, 0.4);
            }

            .btn-switch-coin i {
                font-size: 18px;
                color: white;
            }

            /* Popup Overlay - NEW */
            .coin-popup-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(33, 33, 33, 0.7);
                /* Charcoal with transparency */
                backdrop-filter: blur(8px);
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
                animation: fadeIn 0.2s ease;
            }

            .coin-popup-modal {
                background: var(--card-light);
                /* #FFFFFF */
                border-radius: 16px;
                width: 90%;
                max-width: 420px;
                max-height: 85vh;
                overflow: hidden;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                animation: slideUp 0.3s ease;
                border: 1px solid var(--border-color);
                /* #D1D1D1 */
            }

            .popup-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 20px 24px;
                border-bottom: 1px solid var(--border-color);
                background: rgba(0, 229, 255, 0.03);
                /* Very light gold tint */
            }

            .popup-header h6 {
                color: var(--text-primary);
                /* #212121 Charcoal */
                font-size: 16px;
                font-weight: 700;
                margin: 0;
            }

            .btn-popup-close {
                background: transparent;
                border: none;
                width: 36px;
                height: 36px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                border-radius: 50%;
                transition: all 0.2s ease;
            }

            .btn-popup-close:hover {
                background: rgba(0, 229, 255, 0.1);
                transform: rotate(90deg);
            }

            .btn-popup-close i {
                font-size: 24px;
                color: var(--text-secondary);
                /* #424242 */
            }

            .popup-body {
                padding: 0;
                max-height: calc(85vh - 80px);
                overflow-y: auto;
                background: var(--card-light);
                /* #FFFFFF */
            }

            /* Custom Scrollbar */
            .popup-body::-webkit-scrollbar {
                width: 6px;
            }

            .popup-body::-webkit-scrollbar-track {
                background: var(--secondary-light);
                /* #EBEBEB */
            }

            .popup-body::-webkit-scrollbar-thumb {
                background: rgba(0, 229, 255, 0.3);
                border-radius: 3px;
            }

            .popup-body::-webkit-scrollbar-thumb:hover {
                background: rgba(0, 229, 255, 0.5);
            }

            /* Coin Popup Item - FIXED */
            .coin-popup-item {
                display: block;
                padding: 16px 24px;
                border-bottom: 1px solid var(--border-color);
                /* #D1D1D1 */
                text-decoration: none;
                transition: all 0.2s ease;
                background: transparent;
            }

            .coin-popup-item:last-child {
                border-bottom: none;
            }

            .coin-popup-item:hover {
                background: rgba(0, 229, 255, 0.05);
                /* Light gold hover */
                transform: translateX(4px);
            }

            .coin-popup-item.active {
                background: rgba(0, 229, 255, 0.1);
                border-left: 4px solid var(--gold-color);
                padding-left: 20px;
            }

            .coin-icon-small {
                width: 44px;
                height: 44px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            }

            .coin-icon-small i {
                font-size: 22px;
                color: white;
            }

            .coin-popup-item .fw-bold {
                color: var(--text-primary) !important;
                /* #212121 Charcoal */
                font-size: 15px;
            }

            .coin-popup-item .text-muted {
                color: var(--text-muted) !important;
                /* #666666 */
                font-size: 12px;
            }

            .coin-popup-item .badge {
                background: rgba(40, 167, 69, 0.1) !important;
                color: #28a745 !important;
                font-size: 11px;
                padding: 4px 10px;
                border-radius: 12px;
                font-weight: 600;
                border: 1px solid rgba(40, 167, 69, 0.2);
            }

            /* No signals text */
            .coin-popup-item small.text-muted {
                font-size: 11px;
                color: var(--text-muted) !important;
                /* #666666 */
            }

            /* Signal Card Detailed - NEW */
            .signal-card-detailed {
                background: transparent;
                border: none;
                border-bottom: 2px solid var(--border-color);
                border-radius: 0;
                padding: 20px;
                margin-bottom: 0;
            }

            .signal-card-detailed:last-child {
                border-bottom: none;
            }

            .badge-bet-type {
                font-size: 10px;
                padding: 4px 8px;
                border-radius: 4px;
                display: inline-block;
            }

            .badge-percentage {
                background: rgba(13, 110, 253, 0.15);
                color: #0d6efd;
            }

            .badge-fixed {
                background: rgba(13, 202, 240, 0.15);
                color: #0dcaf0;
            }

            .badge-access {
                font-size: 10px;
                padding: 4px 8px;
                border-radius: 4px;
                display: inline-block;
            }

            .badge-public {
                background: rgba(40, 167, 69, 0.15);
                color: #28a745;
            }

            .badge-private {
                background: rgba(255, 193, 7, 0.15);
                color: #ffc107;
            }

            /* Calculation Box - NEW */
            .calculation-box {
                background: rgba(59, 130, 246, 0.1);
                border: 1px solid #3b82f6;
                color: #60a5fa;
                padding: 10px 12px;
                border-radius: 6px;
                font-size: 11px;
            }

            /* Good News Alert - NEW */
            .good-news-alert {
                background: rgba(34, 197, 94, 0.1);
                border: 1px solid #22c55e;
                color: #22c55e;
                padding: 10px 12px;
                border-radius: 6px;
                font-size: 12px;
            }

            /* Button Join Signal - NEW */
            .btn-join-signal {
                background: linear-gradient(135deg, var(--gold-color) 0%, #00b8d4 100%);
                border: none;
                color: #0a0f1e;
                padding: 12px 24px;
                border-radius: 8px;
                font-weight: 600;
                font-size: 13px;
                transition: all 0.3s ease;
            }

            .btn-join-signal:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 229, 255, 0.4);
            }

            /* Alert Joined - NEW */
            .alert-joined {
                background: rgba(13, 110, 253, 0.15);
                border: 1px solid #0d6efd;
                color: #0d6efd;
                padding: 12px 16px;
                border-radius: 6px;
                font-size: 13px;
            }

            /* Alert Insufficient - NEW */
            .alert-insufficient {
                background: rgba(220, 53, 69, 0.1);
                border: 1px solid #dc3545;
                color: #dc3545;
                padding: 12px 16px;
                border-radius: 6px;
                font-size: 12px;
            }

            /* History Stats Section - NEW */
            .history-stats-section {
                padding: 16px 20px;
                border-bottom: 1px solid var(--border-color);
            }

            .stat-card {
                background: rgba(0, 229, 255, 0.05);
                padding: 10px;
                border-radius: 6px;
                text-align: center;
            }

            .stat-card small {
                font-size: 10px;
                display: block;
                margin-bottom: 4px;
            }

            .stat-card div {
                font-size: 14px;
            }

            /* History Pagination - NEW */
            .history-pagination {
                padding: 16px 20px;
                border-top: 1px solid var(--border-color);
            }

            /* Animations */
            @keyframes fadeIn {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }

            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(30px) scale(0.95);
                }

                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }

            /* Keep all other existing styles... */
            .seamless-warning-notice {
                padding: 16px 20px;
                background: rgba(220, 53, 69, 0.05);
                border-bottom: 1px solid rgba(220, 53, 69, 0.2);
            }

            .warning-icon-wrapper {
                width: 40px;
                height: 40px;
                background: rgba(220, 53, 69, 0.15);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .warning-icon-wrapper i {
                font-size: 20px;
                color: #dc3545;
            }

            .seamless-alert {
                padding: 14px 20px;
                border-bottom: 1px solid;
            }

            .seamless-alert-success {
                background: rgba(40, 167, 69, 0.05);
                border-color: rgba(40, 167, 69, 0.2);
            }

            .seamless-alert-danger {
                background: rgba(220, 53, 69, 0.05);
                border-color: rgba(220, 53, 69, 0.2);
            }

            .btn-alert-close {
                background: transparent;
                border: none;
                width: 28px;
                height: 28px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                border-radius: 50%;
                transition: all 0.2s ease;
                padding: 0;
                flex-shrink: 0;
            }

            .btn-alert-close:hover {
                background: rgba(0, 0, 0, 0.1);
            }

            .btn-alert-close i {
                font-size: 20px;
                color: var(--text-muted);
            }

            .seamless-chart-wrapper {
                background: transparent;
                overflow: hidden;
                border-bottom: 1px solid var(--border-color);
            }

            .seamless-tabs-wrapper {
                padding: 0;
                background: transparent;
                border-bottom: 1px solid var(--border-color);
            }

            .signal-tabs-modern {
                display: flex;
                gap: 0;
                background: transparent;
                padding: 0;
                margin: 0;
            }

            .tab-btn-modern {
                flex: 1;
                padding: 16px 20px;
                background: transparent;
                border: none;
                color: var(--text-muted);
                font-size: 13px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s ease;
                border-bottom: 3px solid transparent;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }

            .tab-btn-modern i {
                font-size: 16px;
            }

            .tab-btn-modern:hover {
                color: var(--text-primary);
                background: rgba(0, 229, 255, 0.03);
            }

            .tab-btn-modern.active {
                color: var(--gold-color);
                border-bottom-color: var(--gold-color);
                background: transparent;
            }

            .seamless-tab-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 16px 20px;
                background: transparent;
                border-bottom: 1px solid var(--border-color);
            }

            .badge-signals-count {
                background: transparent;
                border: 1px solid var(--gold-color);
                color: var(--gold-color);
                padding: 4px 10px;
                border-radius: 12px;
                font-size: 11px;
                font-weight: 600;
            }

            .seamless-signals-list {
                background: transparent;
            }

            .history-item {
                background: transparent;
                border: none;
                border-bottom: 1px solid var(--border-color);
                border-radius: 0;
                padding: 16px 20px;
                margin-bottom: 0;
                transition: all 0.2s ease;
            }

            .history-item:last-child {
                border-bottom: none;
            }

            .history-item:hover {
                background: rgba(0, 229, 255, 0.03);
            }

            .seamless-history-list {
                background: transparent;
            }

            .badge-status-open {
                background: rgba(40, 167, 69, 0.15);
                color: #28a745;
                font-size: 10px;
                padding: 4px 8px;
                border-radius: 6px;
                display: inline-block;
                white-space: nowrap;
            }

            .seamless-empty-state {
                padding: 60px 20px;
                text-align: center;
                background: transparent;
            }

            .seamless-empty-state i {
                font-size: 48px;
                color: var(--border-color);
                display: block;
                margin-bottom: 12px;
            }

            .seamless-empty-state p {
                color: var(--text-muted);
                font-size: 14px;
                margin: 0;
            }

            .seamless-empty-state small {
                color: var(--text-muted);
                font-size: 12px;
            }

            .seamless-info-section {
                padding: 20px;
                background: linear-gradient(135deg, rgba(0, 229, 255, 0.05) 0%, rgba(0, 229, 255, 0.02) 100%);
                border-top: 1px solid rgba(0, 229, 255, 0.2);
                margin-top: 0;
            }

            .tab-content {
                display: none;
            }

            .tab-content.active {
                display: block;
                animation: fadeIn 0.3s ease;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            // Popup Functions
            function openCoinPopup() {
                document.getElementById('coinPopupOverlay').style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }

            function closeCoinPopup() {
                document.getElementById('coinPopupOverlay').style.display = 'none';
                document.body.style.overflow = 'auto';
            }

            // Close popup on ESC key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeCoinPopup();
                }
            });

            // Tab Switcher
            document.addEventListener('DOMContentLoaded', function() {
                console.log('Tab switcher initialized');

                function showTab(tabName) {
                    console.log('Showing tab:', tabName);

                    document.querySelectorAll('.tab-content').forEach(content => {
                        content.style.display = 'none';
                        content.classList.remove('active');
                    });

                    document.querySelectorAll('.tab-btn-modern').forEach(btn => {
                        btn.classList.remove('active');
                    });

                    const targetContent = document.getElementById('tab-' + tabName);
                    const targetButton = document.querySelector(`[data-tab="${tabName}"]`);

                    if (targetContent) {
                        targetContent.style.display = 'block';
                        targetContent.classList.add('active');
                    }

                    if (targetButton) {
                        targetButton.classList.add('active');
                    }
                }

                showTab('signals');

                document.querySelectorAll('.tab-btn-modern').forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        const tabName = this.getAttribute('data-tab');
                        showTab(tabName);
                    });
                });

                // Auto hide alerts after 5 seconds
                setTimeout(function() {
                    const alerts = document.querySelectorAll('.seamless-alert');
                    alerts.forEach(alert => {
                        alert.style.transition = 'opacity 0.5s ease';
                        alert.style.opacity = '0';
                        setTimeout(() => alert.remove(), 500);
                    });
                }, 5000);
            });
        </script>
    @endpush
@endsection
