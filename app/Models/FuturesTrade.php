<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FuturesTrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'coin',
        'direction',
        'amount',
        'entry_price',
        'close_price',
        'profit_loss',
        'payout_rate',
        'result',
        'status',
        'opened_at',
        'closed_at',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'entry_price'  => 'decimal:8',
        'close_price'  => 'decimal:8',
        'profit_loss'  => 'decimal:2',
        'payout_rate'  => 'decimal:2',
        'opened_at'    => 'datetime',
        'closed_at'    => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function isExpired(): bool
    {
        return $this->opened_at->addSeconds(60)->isPast();
    }

    public function secondsRemaining(): int
    {
        $remaining = 60 - now()->diffInSeconds($this->opened_at, false);
        return max(0, (int) $remaining);
    }

    public function settle(float $closePrice): void
    {
        $isWin = ($this->direction === 'call' && $closePrice > (float) $this->entry_price)
              || ($this->direction === 'put'  && $closePrice < (float) $this->entry_price);

        $profitLoss = $isWin
            ? round((float) $this->amount * ((float) $this->payout_rate / 100), 2)
            : -(float) $this->amount;

        $this->update([
            'close_price' => $closePrice,
            'result'      => $isWin ? 'win' : 'lose',
            'profit_loss' => $profitLoss,
            'status'      => 'closed',
            'closed_at'   => now(),
        ]);

        $returnAmount = (float) $this->amount + $profitLoss;
        if ($returnAmount > 0) {
            $this->user()->increment('trade_balance', $returnAmount);
        }
    }
}
