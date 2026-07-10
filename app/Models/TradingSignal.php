<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradingSignal extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'coin',
        'description',
        'entry_price',
        'target_price',
        'stop_loss',
        'bet_type',
        'bet_value',
        'is_public',
        'status',
        'admin_choice',
        'result',
        'rate_of_return',
        'scheduled_at',
        'opened_at',
        'closed_at',
        'settled_at',
        'created_by',
    ];

    protected $casts = [
        'entry_price' => 'decimal:2',
        'target_price' => 'decimal:2',
        'stop_loss' => 'decimal:2',
        'bet_value' => 'decimal:2',
        'is_public' => 'boolean',
        'rate_of_return' => 'decimal:2',
        'scheduled_at' => 'datetime',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'settled_at' => 'datetime',
    ];

    // ==================== COIN CONFIGURATION ====================

    public static function getAvailableCoins()
    {
        return [
            // Cryptocurrencies
            'BTCUSDT' => [
                'name' => 'Bitcoin',
                'symbol' => 'BTC/USDT',
                'icon' => 'bi-currency-bitcoin',
                'color' => '#f7931a',
                'tradingview_symbol' => 'BINANCE:BTCUSDT',
            ],
            'ETHUSDT' => [
                'name' => 'Ethereum',
                'symbol' => 'ETH/USDT',
                'icon' => 'bi-currency-exchange',
                'color' => '#627eea',
                'tradingview_symbol' => 'BINANCE:ETHUSDT',
            ],
            'XRPUSDT' => [
                'name' => 'Ripple',
                'symbol' => 'XRP/USDT',
                'icon' => 'bi-water',
                'color' => '#23292f',
                'tradingview_symbol' => 'BINANCE:XRPUSDT',
            ],
            'LINKUSDT' => [
                'name' => 'Chainlink',
                'symbol' => 'LINK/USDT',
                'icon' => 'bi-link-45deg',
                'color' => '#2a5ada',
                'tradingview_symbol' => 'BINANCE:LINKUSDT',
            ],
            'DOTUSDT' => [
                'name' => 'Polkadot',
                'symbol' => 'DOT/USDT',
                'icon' => 'bi-circle-fill',
                'color' => '#e6007a',
                'tradingview_symbol' => 'BINANCE:DOTUSDT',
            ],
            'DOGEUSDT' => [
                'name' => 'Dogecoin',
                'symbol' => 'DOGE/USDT',
                'icon' => 'bi-coin',
                'color' => '#c2a633',
                'tradingview_symbol' => 'BINANCE:DOGEUSDT',
            ],
            'BCHUSDT' => [
                'name' => 'Bitcoin Cash',
                'symbol' => 'BCH/USDT',
                'icon' => 'bi-cash-coin',
                'color' => '#8dc351',
                'tradingview_symbol' => 'BINANCE:BCHUSDT',
            ],
            'FILUSDT' => [
                'name' => 'Filecoin',
                'symbol' => 'FIL/USDT',
                'icon' => 'bi-database',
                'color' => '#0090ff',
                'tradingview_symbol' => 'BINANCE:FILUSDT',
            ],
            'LTCUSDT' => [
                'name' => 'Litecoin',
                'symbol' => 'LTC/USDT',
                'icon' => 'bi-coin',
                'color' => '#345d9d',
                'tradingview_symbol' => 'BINANCE:LTCUSDT',
            ],
            'ZECUSDT' => [
                'name' => 'Zcash',
                'symbol' => 'ZEC/USDT',
                'icon' => 'bi-shield-lock',
                'color' => '#ecb244',
                'tradingview_symbol' => 'BINANCE:ZECUSDT',
            ],
            'DASHUSDT' => [
                'name' => 'Dash',
                'symbol' => 'DASH/USDT',
                'icon' => 'bi-dash-circle',
                'color' => '#008ce7',
                'tradingview_symbol' => 'BINANCE:DASHUSDT',
            ],

            // Fiat Currencies
            'HKDUSD' => [
                'name' => 'Hong Kong Dollar',
                'symbol' => 'HKD/USD',
                'icon' => 'bi-currency-dollar',
                'color' => '#dc2626',
                'tradingview_symbol' => 'FX:HKDUSD',
            ],
            'INRUSD' => [
                'name' => 'Indian Rupee',
                'symbol' => 'INR/USD',
                'icon' => 'bi-currency-rupee',
                'color' => '#ff9933',
                'tradingview_symbol' => 'FX:INRUSD',
            ],
            'KRWUSD' => [
                'name' => 'Korean Won',
                'symbol' => 'KRW/USD',
                'icon' => 'bi-currency-won',
                'color' => '#0047a0',
                'tradingview_symbol' => 'FX:KRWUSD',
            ],
            'SGDUSD' => [
                'name' => 'Singapore Dollar',
                'symbol' => 'SGD/USD',
                'icon' => 'bi-currency-dollar',
                'color' => '#ed2939',
                'tradingview_symbol' => 'FX:SGDUSD',
            ],
            'BRLUSDT' => [
                'name' => 'Brazilian Real',
                'symbol' => 'BRL/USDT',
                'icon' => 'bi-currency-dollar',
                'color' => '#009739',
                'tradingview_symbol' => 'FX:BRLUSD',
            ],
            'TRYUSDT' => [
                'name' => 'Turkish Lira',
                'symbol' => 'TRY/USDT',
                'icon' => 'bi-currency-exchange',
                'color' => '#e30a17',
                'tradingview_symbol' => 'FX:TRYUSD',
            ],
            'EURUSDT' => [
                'name' => 'Euro',
                'symbol' => 'EUR/USDT',
                'icon' => 'bi-currency-euro',
                'color' => '#003399',
                'tradingview_symbol' => 'FX:EURUSD',
            ],
            'GBPUSDT' => [
                'name' => 'British Pound',
                'symbol' => 'GBP/USDT',
                'icon' => 'bi-currency-pound',
                'color' => '#012169',
                'tradingview_symbol' => 'FX:GBPUSD',
            ],
            'AUDUSDT' => [
                'name' => 'Australian Dollar',
                'symbol' => 'AUD/USDT',
                'icon' => 'bi-currency-dollar',
                'color' => '#012169',
                'tradingview_symbol' => 'FX:AUDUSD',
            ],
            'NZDUSDT' => [
                'name' => 'New Zealand Dollar',
                'symbol' => 'NZD/USDT',
                'icon' => 'bi-currency-dollar',
                'color' => '#00247d',
                'tradingview_symbol' => 'FX:NZDUSD',
            ],

            // Commodities
            'XAGUSD' => [
                'name' => 'Silver',
                'symbol' => 'XAG/USD',
                'icon' => 'bi-gem',
                'color' => '#c0c0c0',
                'tradingview_symbol' => 'OANDA:XAGUSD',
            ],
            'XAUUSD' => [
                'name' => 'Gold',
                'symbol' => 'XAU/USD',
                'icon' => 'bi-trophy',
                'color' => '#ffd700',
                'tradingview_symbol' => 'OANDA:XAUUSD',
            ],
            'XPTUSD' => [
                'name' => 'Platinum',
                'symbol' => 'XPT/USD',
                'icon' => 'bi-star-fill',
                'color' => '#e5e4e2',
                'tradingview_symbol' => 'OANDA:XPTUSD',
            ],
        ];
    }

    public function getCoinInfo()
    {
        $coins = self::getAvailableCoins();
        return $coins[$this->coin] ?? $coins['BTCUSDT'];
    }

    public function getTradingViewSymbol()
    {
        $coinInfo = $this->getCoinInfo();
        return $coinInfo['tradingview_symbol'] ?? 'BINANCE:BTCUSDT';
    }

    // ==================== RELATIONSHIPS ====================

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants()
    {
        return $this->hasMany(SignalParticipant::class, 'signal_id');
    }

    public function allowedUsers()
    {
        return $this->hasMany(SignalAllowedUser::class, 'signal_id');
    }

    // ==================== SCOPES ====================

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeSettled($query)
    {
        return $query->where('status', 'settled');
    }

    public function scopeForCoin($query, $coin)
    {
        return $query->where('coin', strtoupper($coin));
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    /**
     * Scope: signals yang bisa diakses oleh user tertentu
     */
    public function scopeAccessibleBy($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            // Public signals ATAU private signals yang user ada di allowed list
            $q->where('is_public', true)
                ->orWhereHas('allowedUsers', function ($subQuery) use ($userId) {
                    $subQuery->where('user_id', $userId);
                });
        });
    }

    /**
     * Scope: signals yang sudah waktunya tayang ke member
     * (scheduled_at null ATAU scheduled_at sudah lewat/sama dengan sekarang)
     */
    public function scopeVisible($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('scheduled_at')
                ->orWhere('scheduled_at', '<=', now());
        });
    }

    // ==================== HELPER METHODS ====================

    public function isOpen()
    {
        return $this->status === 'open';
    }

    public function isClosed()
    {
        return $this->status === 'closed';
    }

    public function isSettled()
    {
        return $this->status === 'settled';
    }

    /**
     * Cek apakah signal ini masih terjadwal (belum waktunya tayang)
     */
    public function isScheduledForFuture()
    {
        return $this->scheduled_at && $this->scheduled_at->isFuture();
    }

    /**
     * Cek apakah signal ini sudah bisa dilihat member
     */
    public function isVisibleNow()
    {
        return is_null($this->scheduled_at) || $this->scheduled_at->lte(now());
    }

    public function getTotalParticipantsAttribute()
    {
        return $this->participants()->count();
    }

    public function getTotalBetAmountAttribute()
    {
        return $this->participants()->sum('bet_amount');
    }

    /**
     * Check if user is allowed to access this signal
     */
    public function isUserAllowed($userId)
    {
        // Jika public, semua user bisa akses
        if ($this->is_public) {
            return true;
        }

        // Jika private, cek di allowed users
        return SignalAllowedUser::isUserAllowed($this->id, $userId);
    }

    /**
     * Calculate bet amount for specific user based on signal configuration
     */
    public function calculateUserBetAmount($user)
    {
        if ($this->bet_type === 'percentage') {
            // Percentage dari trade balance
            return $user->trade_balance * ($this->bet_value / 100);
        } else {
            // Fixed amount
            return $this->bet_value;
        }
    }

    /**
     * Get bet display text
     */
    public function getBetDisplayAttribute()
    {
        if ($this->bet_type === 'percentage') {
            return number_format($this->bet_value, 2) . '% of Trade Balance';
        } else {
            return number_format($this->bet_value, 2) . ' USDT (Fixed)';
        }
    }

    /**
     * Get allowed user IDs array
     */
    public function getAllowedUserIdsAttribute()
    {
        return $this->allowedUsers()->pluck('user_id')->toArray();
    }

    /**
     * Close signal - set opened_at saat status closed
     */
    public function closeSignal($result, $rateOfReturn, $adminChoice)
    {
        $this->update([
            'status' => 'closed',
            'result' => $result,
            'admin_choice' => $adminChoice,
            'rate_of_return' => $rateOfReturn,
            'opened_at' => now(),
        ]);
    }

    /**
     * Mark as settled - set closed_at saat status settled
     */
    public function markAsSettled()
    {
        $this->update([
            'status' => 'settled',
            'settled_at' => now(),
            'closed_at' => now(),
        ]);
    }
}