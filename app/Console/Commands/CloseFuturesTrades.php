<?php

namespace App\Console\Commands;

use App\Models\FuturesTrade;
use App\Http\Controllers\Member\FuturesController;
use Illuminate\Console\Command;

class CloseFuturesTrades extends Command
{
    protected $signature   = 'futures:close-expired';
    protected $description = 'Auto-close futures trades that have passed 60 seconds';

    public function handle(): void
    {
        $expired = FuturesTrade::open()
            ->where('opened_at', '<=', now()->subSeconds(60))
            ->get();

        if ($expired->isEmpty()) {
            return;
        }

        $controller = app(FuturesController::class);

        foreach ($expired as $trade) {
            $closePrice = $controller->freshPrice($trade->coin)
                       ?? $controller->cachedPrice($trade->coin)
                       ?? (float) $trade->entry_price;

            $trade->settle($closePrice);

            $this->line("Settled trade #{$trade->id} ({$trade->coin} {$trade->direction}) → {$trade->result}");
        }

        $this->info("Closed {$expired->count()} trade(s).");
    }
}
