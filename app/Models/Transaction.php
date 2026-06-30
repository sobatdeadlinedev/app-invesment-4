<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id',
    'reference',
    'amount',
    'total_amount',
    'type',
    'balance_type',
    'wallet_id',
    'withdrawal_fee',
    'source_user_id',
    'status',
    'payment_method',
    'wallet_address',  // ✅ tambah ini
    'payment_proof',
    'approved_by',
    'expired_at',      // ✅ tambah ini
];

    protected $casts = [
        'amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'withdrawal_fee' => 'decimal:2',
        'expired_at' => 'datetime',
    ];

    // ==================== RELATIONSHIPS ====================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function sourceUser()
    {
        return $this->belongsTo(User::class, 'source_user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ==================== SCOPES ====================

    public function scopeDeposit($query)
    {
        return $query->where('type', 'deposit');
    }

    public function scopeWithdrawal($query)
    {
        return $query->where('type', 'withdrawal');
    }

    public function scopeCommission($query)
    {
        return $query->where('type', 'commission');
    }

    // NEW SCOPES
    public function scopeAdjustment($query)
    {
        return $query->where('type', 'adjustment');
    }

    public function scopeDeduction($query)
    {
        return $query->where('type', 'deduction');
    }

    public function scopeExchange($query)
    {
        return $query->where('balance_type', 'exchange');
    }

    public function scopeTrade($query)
    {
        return $query->where('balance_type', 'trade');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ==================== HELPER METHODS ====================

    /**
     * DEPRECATED - Gunakan User->exchange_balance atau User->trade_balance
     * Kept for backward compatibility
     */
    public static function getUserBalance($userId)
    {
        $user = \App\Models\User::find($userId);
        return $user ? $user->exchange_balance : 0;
    }

    public static function getUserBalanceBreakdown($userId)
    {
        $user = \App\Models\User::find($userId);

        if (!$user) {
            return [
                'exchange_balance' => 0,
                'trade_balance' => 0,
                'locked_balance' => 0,
                'available_trade_balance' => 0,
                'total_balance' => 0,
                'total_deposits' => 0,
                'total_withdrawals' => 0,
                'total_withdrawals_net' => 0,
                'total_withdrawal_fees' => 0,
                'total_commissions' => 0,
            ];
        }

        // Calculate totals from transactions (for history/audit)
        $totalDeposits = self::forUser($userId)
            ->deposit()
            ->approved()
            ->sum('total_amount');

        $totalWithdrawals = self::forUser($userId)
            ->withdrawal()
            ->whereIn('status', ['approved', 'pending'])
            ->sum('total_amount');

        $totalWithdrawalsNet = self::forUser($userId)
            ->withdrawal()
            ->whereIn('status', ['approved', 'pending'])
            ->sum('amount');

        $totalWithdrawalFees = self::forUser($userId)
            ->withdrawal()
            ->whereIn('status', ['approved', 'pending'])
            ->sum('withdrawal_fee');

        $totalCommissions = self::forUser($userId)
            ->commission()
            ->approved()
            ->sum('total_amount');

        return [
            // Current balances from user table
            'exchange_balance' => $user->exchange_balance,
            'trade_balance' => $user->trade_balance,
            'locked_balance' => $user->locked_balance,
            'available_trade_balance' => $user->getAvailableTradeBalance(),
            'total_balance' => $user->exchange_balance + $user->trade_balance,

            // Transaction history totals
            'total_deposits' => $totalDeposits,
            'total_withdrawals' => $totalWithdrawals,
            'total_withdrawals_net' => $totalWithdrawalsNet,
            'total_withdrawal_fees' => $totalWithdrawalFees,
            'total_commissions' => $totalCommissions,

            // Trading volume
            'target_volume' => $user->target_volume,
            'achieved_volume' => $user->achieved_volume,
            'remaining_volume' => $user->getRemainingVolume(),
        ];
    }

    public static function hasSufficientBalance($userId, $totalAmount, $balanceType = 'exchange')
    {
        $user = \App\Models\User::find($userId);
        if (!$user) return false;

        return $balanceType === 'trade'
            ? $user->trade_balance >= $totalAmount
            : $user->exchange_balance >= $totalAmount;
    }

    public static function generateReference($prefix = 'TXN')
    {
        do {
            $reference = strtoupper($prefix) . '-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        } while (self::where('reference', $reference)->exists());

        return $reference;
    }

    // ==================== ATTRIBUTES ====================

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'completed' => 'success',
            'rejected' => 'danger',
            'cancelled' => 'secondary',
            default => 'secondary',
        };
    }

    public function getTypeColorAttribute()
    {
        return match ($this->type) {
            'deposit' => 'success',
            'withdrawal' => 'danger',
            'commission' => 'info',
            'adjustment' => 'primary',
            'deduction' => 'warning',
            default => 'secondary',
        };
    }

    public function getFormattedAmountAttribute()
    {
        $sign = in_array($this->type, ['withdrawal', 'deduction']) ? '-' : '+';
        return $sign . ' ' . number_format($this->amount, 2);
    }

    public function getFormattedTotalAmountAttribute()
    {
        $sign = in_array($this->type, ['withdrawal', 'deduction']) ? '-' : '+';
        return $sign . ' ' . number_format($this->total_amount, 2);
    }
}
