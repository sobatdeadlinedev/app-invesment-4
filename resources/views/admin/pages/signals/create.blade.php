@extends('admin.layouts.app')
@section('content')
    <div id="kt_app_toolbar" class="app-toolbar pt-5 pt-lg-10">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack flex-wrap">
            <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                    <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                        Create New Trading Signal</h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.dashboard.index') }}" class="text-muted text-hover-primary">Home</a>
                        </li>
                        <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.signals.index') }}" class="text-muted text-hover-primary">Signals</a>
                        </li>
                        <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                        <li class="breadcrumb-item text-muted">Create</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible d-flex align-items-center p-5 mb-10">
                    <i class="ki-outline ki-cross-circle fs-2hx text-danger me-4"></i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-dark">Error</h4>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.signals.store') }}" method="POST" id="signalForm">
                        @csrf

                        <div class="mb-10">
                            <label class="form-label required">Select Coin</label>
                            <select name="coin" class="form-select @error('coin') is-invalid @enderror" required>
                                <option value="">Choose a coin...</option>
                                @foreach ($coins as $symbol => $info)
                                    <option value="{{ $symbol }}" {{ old('coin') == $symbol ? 'selected' : '' }}>
                                        {{ $info['symbol'] }} - {{ $info['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('coin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Select the cryptocurrency for this trading signal</div>
                        </div>

                        <div class="mb-10">
                            <label class="form-label required">Signal Title</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                placeholder="e.g., BTC Long Position - Breakout Signal" value="{{ old('title') }}"
                                required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-10">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3"
                                placeholder="Optional description about this signal">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Bet Configuration Section -->
                        <div class="card mb-10">
                            <div class="card-header">
                                <h3 class="card-title">Bet Configuration</h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-7">
                                    <label class="form-label required">Bet Type</label>
                                    <div class="d-flex gap-5">
                                        <label class="form-check form-check-custom form-check-solid">
                                            <input class="form-check-input" type="radio" name="bet_type"
                                                value="percentage" id="bet_type_percentage"
                                                {{ old('bet_type', 'percentage') == 'percentage' ? 'checked' : '' }}
                                                required>
                                            <span class="form-check-label">
                                                <span class="fw-bold">Percentage (%)</span>
                                                <span class="text-muted d-block fs-7">Bet amount based on user's trade balance</span>
                                            </span>
                                        </label>
                                        <label class="form-check form-check-custom form-check-solid">
                                            <input class="form-check-input" type="radio" name="bet_type" value="fixed"
                                                id="bet_type_fixed" {{ old('bet_type') == 'fixed' ? 'checked' : '' }}
                                                required>
                                            <span class="form-check-label">
                                                <span class="fw-bold">Fixed Amount (USDT)</span>
                                                <span class="text-muted d-block fs-7">Fixed bet amount for all users</span>
                                            </span>
                                        </label>
                                    </div>
                                    @error('bet_type')
                                        <div class="text-danger fs-7 mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-5">
                                    <label class="form-label required">Bet Value</label>
                                    <div class="input-group">
                                        <input type="number" name="bet_value" id="bet_value"
                                            class="form-control @error('bet_value') is-invalid @enderror" min="0.01"
                                            step="0.01" value="{{ old('bet_value', '1.00') }}" required>
                                        <span class="input-group-text" id="bet_value_unit">%</span>
                                    </div>
                                    @error('bet_value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text" id="bet_value_help">
                                        Enter 1.00 for default 1% of trade balance
                                    </div>
                                </div>

                                <div class="alert alert-primary d-flex align-items-center p-5">
                                    <i class="ki-outline ki-information-5 fs-2hx text-primary me-4"></i>
                                    <div class="d-flex flex-column">
                                        <span id="bet_info_text">
                                            <strong>Percentage:</strong> Users with 10,000 USDT trade balance will bet 100 USDT (1%)<br>
                                            Example: 1% = 100 USDT, 2% = 200 USDT, 5% = 500 USDT
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- User Access Control -->
                        <div class="card mb-10">
                            <div class="card-header">
                                <h3 class="card-title">User Access Control</h3>
                            </div>
                            <div class="card-body">

                                {{-- Mode Selector --}}
                                <div class="mb-7">
                                    <label class="form-label required fw-bold">Access Mode</label>
                                    <div class="d-flex gap-3 flex-wrap">

                                        {{-- Public --}}
                                        <label class="form-check form-check-custom form-check-solid border rounded p-4 cursor-pointer flex-grow-1"
                                            id="tab_public" style="min-width:200px">
                                            <input class="form-check-input" type="radio" name="access_mode"
                                                value="public" id="access_mode_public"
                                                {{ old('access_mode', 'public') == 'public' ? 'checked' : '' }}>
                                            <span class="form-check-label">
                                                <span class="fw-bold d-block">
                                                    <i class="ki-outline ki-globe fs-4 text-primary me-1"></i>
                                                    Public
                                                </span>
                                                <span class="text-muted fs-7">Semua user bisa akses</span>
                                            </span>
                                        </label>

                                        {{-- Specific Users --}}
                                        <label class="form-check form-check-custom form-check-solid border rounded p-4 cursor-pointer flex-grow-1"
                                            id="tab_specific" style="min-width:200px">
                                            <input class="form-check-input" type="radio" name="access_mode"
                                                value="specific" id="access_mode_specific"
                                                {{ old('access_mode') == 'specific' ? 'checked' : '' }}>
                                            <span class="form-check-label">
                                                <span class="fw-bold d-block">
                                                    <i class="ki-outline ki-profile-user fs-4 text-warning me-1"></i>
                                                    Specific Users
                                                </span>
                                                <span class="text-muted fs-7">Pilih user satu per satu</span>
                                            </span>
                                        </label>

                                    </div>
                                </div>

                                {{-- Panel: Specific Users --}}
                                <div id="panel_specific" style="display:none;">

                                    {{-- Quick Action Buttons --}}
                                    <div class="d-flex align-items-center gap-3 mb-4 p-4 bg-light-primary rounded flex-wrap">
                                        <span class="fw-bold text-gray-700 me-2">Quick Select:</span>
                                        <button type="button" class="btn btn-sm btn-primary" id="btn_select_all">
                                            <i class="ki-outline ki-check-square fs-4 me-1"></i>Select All
                                        </button>
                                        <button type="button" class="btn btn-sm btn-light-warning" id="btn_select_all_except_mode">
                                            <i class="ki-outline ki-minus-square fs-4 me-1"></i>Select All Except...
                                        </button>
                                        <button type="button" class="btn btn-sm btn-light-danger" id="btn_clear_all">
                                            <i class="ki-outline ki-cross-square fs-4 me-1"></i>Clear All
                                        </button>
                                        <span class="ms-auto badge badge-light-primary fs-7" id="selected_count_badge">0 users selected</span>
                                    </div>

                                    {{-- Select All Except Panel --}}
                                    <div id="exclude_panel" style="display:none;" class="mb-4 p-4 border border-warning border-dashed rounded bg-light-warning">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="ki-outline ki-information-5 fs-4 text-warning me-2"></i>
                                            <span class="fw-bold text-warning-emphasis">Select All Except Mode</span>
                                            <span class="text-muted fs-7 ms-2">— Uncheck user yang ingin <strong>dikecualikan</strong>.</span>
                                            <button type="button" class="btn btn-sm btn-icon btn-light-warning ms-auto" id="btn_close_except_mode">
                                                <i class="ki-outline ki-cross fs-4"></i>
                                            </button>
                                        </div>
                                        <input type="text" id="exclude_search" class="form-control form-control-sm mb-3"
                                            placeholder="Cari user untuk dikecualikan...">
                                        <div id="exclude_user_list" style="max-height:220px;overflow-y:auto;"
                                            class="border rounded bg-white p-3">
                                            @foreach ($users as $user)
                                                <label class="d-flex align-items-center gap-2 py-1 px-2 rounded user-exclude-item cursor-pointer"
                                                    data-name="{{ strtolower($user->name) }}"
                                                    data-email="{{ strtolower($user->email) }}"
                                                    data-phone="{{ strtolower($user->phone ?? '') }}">
                                                    <input type="checkbox" class="form-check-input exclude-user-checkbox"
                                                        data-user-id="{{ $user->id }}" checked>
                                                    <span>
                                                        <span class="fw-semibold">{{ $user->name }}</span>
                                                        <span class="text-muted fs-7 ms-1">{{ $user->email }}</span>
                                                        @if ($user->phone)
                                                            <span class="text-muted fs-7">({{ $user->phone }})</span>
                                                        @endif
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mt-3">
                                            <span class="text-muted fs-7" id="exclude_summary">All users included</span>
                                            <button type="button" class="btn btn-sm btn-warning" id="btn_apply_except">
                                                <i class="ki-outline ki-check fs-4 me-1"></i>Apply Selection
                                            </button>
                                        </div>
                                    </div>

                                    <label class="form-label required">Selected Allowed Users</label>
                                    <select name="allowed_user_ids[]" id="allowed_user_ids"
                                        class="form-select @error('allowed_user_ids') is-invalid @enderror" multiple
                                        data-control="select2" data-placeholder="Search by name, email, or phone..."
                                        data-allow-clear="true">
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ in_array($user->id, old('allowed_user_ids', [])) ? 'selected' : '' }}>
                                                {{ $user->name }} - {{ $user->email }}
                                                @if ($user->phone)
                                                    ({{ $user->phone }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('allowed_user_ids')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Pilih satu atau lebih user yang boleh mengakses signal ini</div>
                                </div>

                                {{-- Hidden field is_public --}}
                                <input type="hidden" name="is_public" id="is_public_hidden" value="1">

                            </div>
                        </div>

                        <div class="row mb-10">
                            <div class="col-md-6">
                                <label class="form-label required">Opening Price (USDT)</label>
                                <input type="hidden" name="entry_price" id="entry_price_hidden"
                                    value="{{ old('entry_price') }}">
                                <input type="text" id="entry_price_display"
                                    class="form-control @error('entry_price') is-invalid @enderror"
                                    placeholder="92,920.80"
                                    value="{{ old('entry_price') ? number_format(old('entry_price'), 2) : '' }}" required>
                                @error('entry_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Set the starting price for this signal (e.g., 92,920.80)</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">Settlement Price (USDT)</label>
                                <input type="hidden" name="target_price" id="target_price_hidden"
                                    value="{{ old('target_price') }}">
                                <input type="text" id="target_price_display"
                                    class="form-control @error('target_price') is-invalid @enderror"
                                    placeholder="95,840.50"
                                    value="{{ old('target_price') ? number_format(old('target_price'), 2) : '' }}"
                                    required>
                                @error('target_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Set the expected settlement price (e.g., 95,840.50)</div>
                            </div>
                        </div>

                        <div class="alert alert-info d-flex align-items-center p-5 mb-10">
                            <i class="ki-outline ki-information-5 fs-2hx text-info me-4"></i>
                            <div class="d-flex flex-column">
                                <h5 class="mb-1">Important Information</h5>
                                <span>
                                    • Both Opening and Settlement prices must be set when creating the signal<br>
                                    • When closing, you'll only need to select Call/Put and set the win rate<br>
                                    • Call = Market up (settlement > opening) | Put = Market down (settlement &lt; opening)<br>
                                    • All participants will receive rewards based on the win rate you set
                                </span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-3">
                            <a href="{{ route('admin.signals.index') }}" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ki-outline ki-check fs-2"></i>Create Signal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        // ─── Bet Type Toggle ───────────────────────────────────────────────────────
        const betTypePercentage = document.getElementById('bet_type_percentage');
        const betTypeFixed      = document.getElementById('bet_type_fixed');
        const betValueUnit      = document.getElementById('bet_value_unit');
        const betValueHelp      = document.getElementById('bet_value_help');
        const betInfoText       = document.getElementById('bet_info_text');
        const betValueInput     = document.getElementById('bet_value');

        function updateBetType() {
            if (betTypePercentage.checked) {
                betValueUnit.textContent = '%';
                betValueHelp.innerHTML   = 'Enter percentage of user\'s trade balance (e.g., 1.00 = 1%, 5.00 = 5%)';
                betInfoText.innerHTML    =
                    '<strong>Percentage:</strong> Users with 10,000 USDT trade balance will bet ' +
                    (betValueInput.value * 100).toFixed(0) + ' USDT (' + betValueInput.value + '%)<br>' +
                    'Example: 1% = 100 USDT, 2% = 200 USDT, 5% = 500 USDT';
            } else {
                betValueUnit.textContent = 'USDT';
                betValueHelp.innerHTML   = 'Enter fixed amount in USDT (e.g., 100.00 = all users bet 100 USDT)';
                betInfoText.innerHTML    =
                    '<strong>Fixed:</strong> All users will bet exactly ' +
                    parseFloat(betValueInput.value).toFixed(2) + ' USDT regardless of their balance<br>' +
                    'Make sure users have sufficient balance to join';
            }
        }
        betTypePercentage.addEventListener('change', updateBetType);
        betTypeFixed.addEventListener('change', updateBetType);
        betValueInput.addEventListener('input', updateBetType);
        updateBetType();

        // ─── Access Mode Toggle ────────────────────────────────────────────────────
        const panelSpecific  = document.getElementById('panel_specific');
        const isPublicHidden = document.getElementById('is_public_hidden');
        const accessRadios   = document.querySelectorAll('input[name="access_mode"]');

        function applyAccessMode(mode) {
            panelSpecific.style.display = (mode === 'specific') ? 'block' : 'none';
            isPublicHidden.value        = (mode === 'public')   ? '1'     : '0';

            const allowedSelect = document.getElementById('allowed_user_ids');
            allowedSelect.required = (mode === 'specific');
        }

        accessRadios.forEach(function (r) {
            r.addEventListener('change', function () { applyAccessMode(this.value); });
        });

        const initialMode = document.querySelector('input[name="access_mode"]:checked')?.value || 'public';
        applyAccessMode(initialMode);

        // ─── Select2: Allowed Users ────────────────────────────────────────────────
        const $select = $('#allowed_user_ids');
        $select.select2({
            width: '100%',
            placeholder: 'Search by name, email, or phone...',
            allowClear: true,
            matcher: function (params, data) {
                if ($.trim(params.term) === '') return data;
                return data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1 ? data : null;
            }
        });

        function updateBadge() {
            const count = $select.val() ? $select.val().length : 0;
            document.getElementById('selected_count_badge').textContent =
                count + (count === 1 ? ' user selected' : ' users selected');
        }
        $select.on('change', updateBadge);
        updateBadge();

        // Select All
        document.getElementById('btn_select_all').addEventListener('click', function () {
            $select.find('option').prop('selected', true);
            $select.trigger('change');
        });

        // Clear All
        document.getElementById('btn_clear_all').addEventListener('click', function () {
            $select.val(null).trigger('change');
        });

        // ─── Select All Except Panel ───────────────────────────────────────────────
        const excludePanel      = document.getElementById('exclude_panel');
        const excludeSearch     = document.getElementById('exclude_search');
        const excludeSummary    = document.getElementById('exclude_summary');
        const excludeCheckboxes = document.querySelectorAll('.exclude-user-checkbox');

        document.getElementById('btn_select_all_except_mode').addEventListener('click', function () {
            excludeCheckboxes.forEach(cb => cb.checked = true);
            updateExcludeSummary();
            excludeSearch.value = '';
            filterExcludeList('');
            excludePanel.style.display = 'block';
        });

        document.getElementById('btn_close_except_mode').addEventListener('click', function () {
            excludePanel.style.display = 'none';
        });

        excludeSearch.addEventListener('input', function () {
            filterExcludeList(this.value.toLowerCase());
        });

        function filterExcludeList(term) {
            document.querySelectorAll('.user-exclude-item').forEach(function (item) {
                const match = !term
                    || (item.dataset.name  || '').includes(term)
                    || (item.dataset.email || '').includes(term)
                    || (item.dataset.phone || '').includes(term);
                item.style.display = match ? '' : 'none';
            });
        }

        excludeCheckboxes.forEach(function (cb) {
            cb.addEventListener('change', updateExcludeSummary);
        });

        function updateExcludeSummary() {
            const total    = excludeCheckboxes.length;
            const excluded = Array.from(excludeCheckboxes).filter(cb => !cb.checked).length;
            const included = total - excluded;
            excludeSummary.textContent = excluded === 0
                ? 'All ' + total + ' users included'
                : included + ' users included, ' + excluded + ' excluded';
        }

        document.getElementById('btn_apply_except').addEventListener('click', function () {
            const includedIds = Array.from(excludeCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.dataset.userId);
            $select.val(includedIds).trigger('change');
            excludePanel.style.display = 'none';
        });

        // ─── Price Formatting ──────────────────────────────────────────────────────
        function formatNumber(value) {
            let num   = value.replace(/[^\d.]/g, '');
            let parts = num.split('.');
            parts[0]  = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            if (parts[1]) parts[1] = parts[1].substring(0, 2);
            return parts.join('.');
        }
        function parseFormattedNumber(value) { return value.replace(/,/g, ''); }

        const entryPriceDisplay = document.getElementById('entry_price_display');
        const entryPriceHidden  = document.getElementById('entry_price_hidden');

        entryPriceDisplay.addEventListener('input', function (e) {
            let cursor    = e.target.selectionStart;
            let old       = e.target.value;
            let formatted = formatNumber(e.target.value);
            e.target.value         = formatted;
            entryPriceHidden.value = parseFormattedNumber(formatted);
            e.target.selectionStart = e.target.selectionEnd = cursor + (formatted.length - old.length);
        });
        entryPriceDisplay.addEventListener('blur', function (e) {
            let value = parseFormattedNumber(e.target.value);
            if (value && !isNaN(value)) {
                let num = parseFloat(value);
                e.target.value         = num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                entryPriceHidden.value = num;
            }
        });

        const targetPriceDisplay = document.getElementById('target_price_display');
        const targetPriceHidden  = document.getElementById('target_price_hidden');

        targetPriceDisplay.addEventListener('input', function (e) {
            let cursor    = e.target.selectionStart;
            let old       = e.target.value;
            let formatted = formatNumber(e.target.value);
            e.target.value          = formatted;
            targetPriceHidden.value = parseFormattedNumber(formatted);
            e.target.selectionStart = e.target.selectionEnd = cursor + (formatted.length - old.length);
        });
        targetPriceDisplay.addEventListener('blur', function (e) {
            let value = parseFormattedNumber(e.target.value);
            if (value && !isNaN(value)) {
                let num = parseFloat(value);
                e.target.value          = num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                targetPriceHidden.value = num;
            }
        });

        // ─── Form Validation ───────────────────────────────────────────────────────
        document.getElementById('signalForm').addEventListener('submit', function (e) {
            const entryPrice  = parseFloat(entryPriceHidden.value);
            const targetPrice = parseFloat(targetPriceHidden.value);

            if (isNaN(entryPrice) || entryPrice <= 0) {
                e.preventDefault();
                alert('Please enter a valid Opening Price');
                entryPriceDisplay.focus();
                return false;
            }
            if (isNaN(targetPrice) || targetPrice <= 0) {
                e.preventDefault();
                alert('Please enter a valid Settlement Price');
                targetPriceDisplay.focus();
                return false;
            }

            const mode = document.querySelector('input[name="access_mode"]:checked')?.value;
            if (mode === 'specific') {
                const selected = $select.val();
                if (!selected || selected.length === 0) {
                    e.preventDefault();
                    alert('Pilih minimal satu user untuk mode Specific Users.');
                    return false;
                }
            }
        });
    });
    </script>
@endsection