@extends('member.layouts.app')
@section('content')
<div class="scrollable-content">

    <!-- Header -->
    <div class="pg-header">
        <a href="{{ route('member.access.index') }}" class="pg-back">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div class="pg-header-text">
            <h5 class="pg-title">{{ __('app.wallet_list') }}</h5>
        </div>
        <span class="wl-count-badge">{{ $wallets->count() }}/3</span>
    </div>

    <div style="padding: 16px;">

        {{-- ═══ WALLET LIST ═══ --}}
        @forelse($wallets as $wallet)
        <div class="wo-wallet-row">
            <div class="wo-wallet-icon {{ $wallet->type }}">
                <i class="bi bi-wallet-fill"></i>
            </div>
            <div class="flex-grow-1">
                <div class="wo-wallet-type">{{ strtoupper($wallet->type) }}</div>
                <div class="wo-wallet-addr">{{ substr($wallet->account_number, 0, 12) }}...{{ substr($wallet->account_number, -6) }}</div>
            </div>
            <div class="d-flex gap-2">
                <button class="wo-action-btn edit"
                    onclick="openEditModal({{ $wallet->id }}, '{{ $wallet->type }}', '{{ $wallet->account_number }}')">
                    <i class="bi bi-pencil"></i>
                </button>
                <form action="{{ route('member.wallet.destroy', $wallet->id) }}" method="POST"
                    onsubmit="return confirm('{{ __('app.delete_wallet_confirmation') }}')">
                    @csrf @method('DELETE')
                    <button type="submit" class="wo-action-btn delete">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="wo-empty">
            <i class="bi bi-wallet2"></i>
            <p>{{ __('app.no_wallet_yet') }}</p>
        </div>
        @endforelse

        @if($wallets->count() < 3)
        <button class="wo-add-btn mt-2" data-bs-toggle="modal" data-bs-target="#addWalletModal">
            <i class="bi bi-plus-circle me-2"></i>{{ __('app.add_wallet') }}
        </button>
        @endif

        {{-- ═══ INFO CARD ═══ --}}
        <div class="wl-info-card">
            <div class="wl-info-title"><i class="bi bi-info-circle-fill"></i> {{ __('app.wallet_information') }}</div>
            <ul class="wl-info-list">
                <li>{{ __('app.wallet_max_3') }}</li>
                <li>{{ __('app.wallet_network_supported') }}</li>
                <li>{{ __('app.wallet_address_warning') }}</li>
            </ul>
        </div>

    </div>
</div>

{{-- ═══ MODAL ADD WALLET ═══ --}}
<div class="modal fade" id="addWalletModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:var(--card-light);border:1px solid var(--border-color);">
            <div class="modal-header" style="border-bottom:1px solid var(--border-color);">
                <h5 class="modal-title text-white">{{ __('app.add_wallet') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('member.wallet.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-white small">{{ __('app.network') }}</label>
                        <div class="d-flex gap-2">
                            @foreach(['trc20' => 'TRC20 (Tron)', 'bep20' => 'BEP20 (BSC)'] as $val => $label)
                            <label class="flex-1" style="flex:1;">
                                <input type="radio" name="type" value="{{ $val }}" class="d-none network-radio" {{ $val === 'trc20' ? 'checked' : '' }}>
                                <div class="network-pill">{{ $label }}</div>
                            </label>
                            @endforeach
                        </div>
                        @error('type')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="mb-2">
                        <label class="form-label text-white small">{{ __('app.wallet_address') }}</label>
                        <input type="text" name="account_number" class="form-control-dark"
                            placeholder="{{ __('app.enter_wallet_address') }}" required value="{{ old('account_number') }}">
                        @error('account_number')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border-color);">
                    <button type="button" class="btn btn-outline-gold" data-bs-dismiss="modal">{{ __('app.cancel') }}</button>
                    <button type="submit" class="btn btn-gold">{{ __('app.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══ MODAL EDIT WALLET ═══ --}}
<div class="modal fade" id="editWalletModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:var(--card-light);border:1px solid var(--border-color);">
            <div class="modal-header" style="border-bottom:1px solid var(--border-color);">
                <h5 class="modal-title text-white">{{ __('app.edit_wallet') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editWalletForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-white small">{{ __('app.network') }}</label>
                        <div class="d-flex gap-2">
                            @foreach(['trc20' => 'TRC20 (Tron)', 'bep20' => 'BEP20 (BSC)'] as $val => $label)
                            <label class="flex-1" style="flex:1;">
                                <input type="radio" name="type" value="{{ $val }}" id="edit_{{ $val }}" class="d-none network-radio">
                                <div class="network-pill">{{ $label }}</div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label text-white small">{{ __('app.wallet_address') }}</label>
                        <input type="text" name="account_number" id="edit_account_number"
                            class="form-control-dark" placeholder="{{ __('app.wallet_address') }}" required>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border-color);">
                    <button type="button" class="btn btn-outline-gold" data-bs-dismiss="modal">{{ __('app.cancel') }}</button>
                    <button type="submit" class="btn btn-gold">{{ __('app.update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.wl-count-badge {
    background: rgba(0,229,255,0.1); border: 1px solid rgba(0,229,255,0.2);
    color: var(--gold-color); font-size: 11px; font-weight: 700;
    padding: 3px 10px; border-radius: 10px; margin-left: auto;
}

/* Wallet rows */
.wo-wallet-row {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 16px; background: rgba(255,255,255,0.03);
    border-radius: 12px; margin-bottom: 8px;
    border: 1px solid var(--border-color);
}
.wo-wallet-icon {
    width: 40px; height: 40px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; flex-shrink: 0;
}
.wo-wallet-icon.trc20 { background: rgba(0,229,255,0.1); border: 1px solid rgba(0,229,255,0.2); color: var(--gold-color); }
.wo-wallet-icon.bep20 { background: rgba(0,229,255,0.1); border: 1px solid rgba(0,229,255,0.2); color: #00e5ff; }
.wo-wallet-type { color: var(--text-muted); font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
.wo-wallet-addr { color: #fff; font-size: 12px; font-weight: 600; font-family: monospace; }
.wo-action-btn {
    width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border-color);
    background: transparent; display: flex; align-items: center; justify-content: center;
    cursor: pointer; font-size: 13px; transition: background 0.15s;
}
.wo-action-btn.edit { color: var(--gold-color); }
.wo-action-btn.edit:hover { background: rgba(0,229,255,0.1); }
.wo-action-btn.delete { color: #ef4444; }
.wo-action-btn.delete:hover { background: rgba(239,68,68,0.1); }

.wo-empty { text-align: center; padding: 32px 16px; color: var(--text-muted); font-size: 13px; }
.wo-empty i { font-size: 36px; display: block; margin-bottom: 8px; opacity: 0.3; }

.wo-add-btn {
    width: 100%; padding: 12px; border-radius: 10px; border: 1px dashed rgba(0,229,255,0.3);
    background: rgba(0,229,255,0.04); color: var(--gold-color);
    font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s;
}
.wo-add-btn:hover { background: rgba(0,229,255,0.08); }

.wl-info-card {
    margin-top: 20px; padding: 14px 16px;
    background: rgba(0,229,255,0.04); border: 1px solid rgba(0,229,255,0.15);
    border-radius: 12px;
}
.wl-info-title { color: var(--gold-color); font-size: 12px; font-weight: 700; margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }
.wl-info-list { color: var(--text-muted); font-size: 12px; padding-left: 18px; margin: 0; }
.wl-info-list li { margin-bottom: 4px; }

/* Network pill in modal */
.network-radio:checked ~ .network-pill {
    background: rgba(0,229,255,0.15); border-color: var(--gold-color); color: var(--gold-color);
}
.network-pill {
    padding: 10px 12px; border-radius: 8px; text-align: center;
    border: 1px solid var(--border-color); color: var(--text-muted);
    font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s;
    background: rgba(255,255,255,0.03);
}
</style>
@endpush

@push('scripts')
<script>
function openEditModal(id, type, addr) {
    document.getElementById('editWalletForm').action = '/member/wallet/' + id;
    document.getElementById('edit_account_number').value = addr;
    document.getElementById('edit_' + type).checked = true;
    new bootstrap.Modal(document.getElementById('editWalletModal')).show();
}

@if(session('success')) alert('{{ session('success') }}'); @endif
@if(session('error')) alert('{{ session('error') }}'); @endif
</script>
@endpush
