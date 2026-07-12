<?php

namespace App\Models;

use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use HasRoles;

    protected $fillable = [
        'name',
        'level',
        'username',
        'phone',
        'email',
        'password',
        'refferal_code',
        'is_verified',
        'exchange_balance',
        'trade_balance',
        'locked_balance',
        'target_volume',
        'achieved_volume',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'is_verified' => 'boolean',
        'exchange_balance' => 'decimal:2',
        'trade_balance' => 'decimal:2',
        'locked_balance' => 'decimal:2',
        'target_volume' => 'decimal:2',
        'achieved_volume' => 'decimal:2',
    ];

    // ==================== EXISTING RELATIONS ====================

    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function commissionSources()
    {
        return $this->hasMany(Transaction::class, 'source_user_id');
    }

    public function approvedTransactions()
    {
        return $this->hasMany(Transaction::class, 'approved_by');
    }

    public function verification()
    {
        return $this->hasOne(UserVerification::class);
    }

    public function referrals()
    {
        return $this->hasMany(ReferralUsage::class, 'referrer_id');
    }

    public function usedReferral()
    {
        return $this->hasOne(ReferralUsage::class, 'referred_id');
    }

    public function referredUsers()
    {
        return $this->hasManyThrough(
            User::class,
            ReferralUsage::class,
            'referrer_id',
            'id',
            'id',
            'referred_id'
        );
    }

    // ==================== NEW RELATIONS ====================

    public function signalParticipants()
    {
        return $this->hasMany(SignalParticipant::class);
    }

    public function tradingSignals()
    {
        return $this->hasMany(TradingSignal::class, 'created_by');
    }

    // ==================== TODAY'S PNL METHODS (NEW) ====================

    /**
     * Get today's PnL (Profit and Loss) from settled signals
     * This is an accessor that can be accessed as $user->today_pnl
     */
    public function getTodayPnlAttribute()
    {
        return $this->signalParticipants()
            ->where('status', 'settled')
            ->whereDate('settled_at', today())
            ->get()
            ->sum(function ($participant) {
                return $participant->net_result;
            });
    }

    /**
     * Get PnL for specific date
     */
    public function getPnlForDate($date)
    {
        return $this->signalParticipants()
            ->where('status', 'settled')
            ->whereDate('settled_at', $date)
            ->get()
            ->sum(function ($participant) {
                return $participant->net_result;
            });
    }

    /**
     * Get today's trading statistics
     */
    public function getTodayTradingStats()
    {
        $todayParticipants = $this->signalParticipants()
            ->where('status', 'settled')
            ->whereDate('settled_at', today())
            ->get();

        $totalBetAmount = $todayParticipants->sum('bet_amount');
        $totalProfitLoss = $todayParticipants->sum('profit_loss');
        $totalFees = $todayParticipants->sum('fee_amount');
        $netResult = $todayParticipants->sum(function ($p) {
            return $p->net_result;
        });

        $winCount = $todayParticipants->filter(function ($p) {
            return $p->profit_loss > 0;
        })->count();

        $lossCount = $todayParticipants->filter(function ($p) {
            return $p->profit_loss <= 0;
        })->count();

        return [
            'total_trades' => $todayParticipants->count(),
            'total_bet_amount' => $totalBetAmount,
            'total_profit_loss' => $totalProfitLoss,
            'total_fees' => $totalFees,
            'net_result' => $netResult,
            'win_count' => $winCount,
            'loss_count' => $lossCount,
            'win_rate' => $todayParticipants->count() > 0
                ? ($winCount / $todayParticipants->count()) * 100
                : 0,
        ];
    }

    // ==================== BALANCE METHODS ====================

    /**
     * Get exchange balance
     */
    public function getExchangeBalance()
    {
        return $this->exchange_balance;
    }

    /**
     * Get trade balance (available = trade - locked)
     */
    public function getTradeBalance()
    {
        return $this->trade_balance;
    }

    /**
     * Get available trade balance (tidak termasuk yang di-lock)
     */
    public function getAvailableTradeBalance()
    {
        return $this->trade_balance - $this->locked_balance;
    }

    /**
     * Add to exchange balance
     */
    public function addExchangeBalance($amount)
    {
        $this->increment('exchange_balance', $amount);
        return $this->fresh();
    }

    /**
     * Deduct from exchange balance
     */
    public function deductExchangeBalance($amount)
    {
        if ($this->exchange_balance < $amount) {
            throw new \Exception('Insufficient exchange balance');
        }
        $this->decrement('exchange_balance', $amount);
        return $this->fresh();
    }

    /**
     * Add to trade balance
     */
    public function addTradeBalance($amount)
    {
        $this->increment('trade_balance', $amount);
        return $this->fresh();
    }

    /**
     * Deduct from trade balance
     */
    public function deductTradeBalance($amount)
    {
        if ($this->trade_balance < $amount) {
            throw new \Exception('Insufficient trade balance');
        }
        $this->decrement('trade_balance', $amount);
        return $this->fresh();
    }

    /**
     * Lock balance (untuk betting)
     */
    public function lockBalance($amount)
    {
        if ($this->getAvailableTradeBalance() < $amount) {
            throw new \Exception('Insufficient available trade balance to lock');
        }

        // Kurangi trade balance
        $this->decrement('trade_balance', $amount);

        // Tambah locked balance
        $this->increment('locked_balance', $amount);

        return $this->fresh();
    }

    /**
     * Unlock balance (setelah settle)
     */
    public function unlockBalance($amount)
    {
        if ($this->locked_balance < $amount) {
            throw new \Exception('Cannot unlock more than locked balance');
        }

        // Kurangi locked balance
        $this->decrement('locked_balance', $amount);

        // Tambah kembali ke trade balance
        $this->increment('trade_balance', $amount);

        return $this->fresh();
    }
    public function removeLockedBalance($amount)
    {
        if ($this->locked_balance < $amount) {
            throw new \Exception('Cannot remove more than locked balance');
        }

        // Kurangi locked balance saja (trade balance sudah dikurangi saat lock)
        $this->decrement('locked_balance', $amount);

        return $this->fresh();
    }
    // ==================== VOLUME METHODS ====================

    /**
     * Add target volume
     */
    public function addTargetVolume($amount)
    {
        $this->increment('target_volume', $amount);
        return $this->fresh();
    }

    /**
     * Add achieved volume
     */
    public function addAchievedVolume($amount)
    {
        $this->increment('achieved_volume', $amount);
        return $this->fresh();
    }

    /**
     * Get remaining volume
     */
    public function getRemainingVolume()
    {
        return max(0, $this->target_volume - $this->achieved_volume);
    }

    /**
     * Check if volume is completed
     */
    public function isVolumeCompleted()
    {
        return $this->achieved_volume >= $this->target_volume;
    }

    /**
     * Check if user needs penalty when transferring trade to exchange
     */
    public function needsPenalty()
    {
        return !$this->isVolumeCompleted() && $this->target_volume > 0;
    }

    /**
     * Get volume completion percentage
     */
    public function getVolumeCompletionPercentage()
    {
        if ($this->target_volume == 0) {
            return 100;
        }
        return min(100, ($this->achieved_volume / $this->target_volume) * 100);
    }

    /**
     * Calculate penalty amount (20%)
     */
    public function calculatePenalty($amount)
{
    if (!$this->needsPenalty()) {
        return 0;
    }
    return $amount * 0.30;
}

    /**
     * Check if user can join signal (min 100 USDT available trade balance)
     */
    public function canJoinSignal()
    {
        return $this->getAvailableTradeBalance() >= 200;
    }

    /**
     * Calculate bet amount (1% of current trade balance)
     */
    public function calculateBetAmount()
    {
        return $this->trade_balance * 0.01;
    }

    /**
     * Calculate trading fee (1% of current trade balance)
     */
    public function calculateTradingFee()
    {
        return $this->trade_balance * 0.01;
    }

    // ==================== EXISTING METHODS ====================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->refferal_code)) {
                $user->refferal_code = self::generateUniqueReferralCode();
            }
        });
    }

    private static function generateUniqueReferralCode(): string
    {
        $exists = true;

        while ($exists) {
            $code = strtoupper(Str::random(6));

            if (!preg_match('/[A-Z]/', $code) || !preg_match('/[0-9]/', $code)) {
                continue;
            }

            $exists = self::where('refferal_code', $code)->exists();
        }

        return $code;
    }

    public static function current()
    {
        return auth()->user();
    }

    public function getTotalReferralsAttribute()
    {
        return $this->referrals()->count();
    }

    public function verify()
    {
        $this->update(['is_verified' => true]);
    }

    public function unverify()
    {
        $this->update(['is_verified' => false]);
    }

    public function isVerified(): bool
    {
        return $this->is_verified;
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    public function hasVerificationData(): bool
    {
        return !is_null($this->verification);
    }

    public function hasBasicVerification(): bool
    {
        return $this->verification &&
            $this->verification->isBasicComplete();
    }

    public function hasAdvancedVerification(): bool
    {
        return $this->verification &&
            $this->verification->isAdvancedComplete();
    }

    public function isDocumentVerified(): bool
    {
        return $this->verification &&
            $this->verification->isVerified();
    }

    public function isDocumentPending(): bool
    {
        return $this->verification &&
            $this->verification->isSubmitted() &&
            !$this->verification->isVerified();
    }

    public function getMultiLevelReferralsAttribute()
    {
        return $this->getMultiLevelReferralsEfficient();
    }

    private function getMultiLevelReferralsEfficient($maxLevel = 10)
    {
        $results = collect([]);
        $currentLevelIds = [$this->id];

        for ($level = 1; $level <= $maxLevel; $level++) {
            if (empty($currentLevelIds)) {
                break;
            }

            $referrals = ReferralUsage::whereIn('referrer_id', $currentLevelIds)
                ->with(['referred', 'referrer'])
                ->get();

            if ($referrals->isEmpty()) {
                break;
            }

            foreach ($referrals as $referral) {
                $results->push([
                    'level' => $level,
                    'referral_id' => $referral->id,
                    'referrer_id' => $referral->referrer_id,
                    'referrer_name' => $referral->referrer->name ?? 'Unknown',
                    'referred_id' => $referral->referred_id,
                    'referred' => $referral->referred,
                    'referral_code' => $referral->referral_code,
                    'used_at' => $referral->used_at,
                ]);
            }

            $currentLevelIds = $referrals->pluck('referred_id')->toArray();
        }

        return $results;
    }

    public function getTotalMultiLevelReferralsAttribute()
    {
        return $this->multi_level_referrals->count();
    }
}
