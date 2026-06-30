<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpireDeposits extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'deposits:expire';

    /**
     * The console command description.
     */
    protected $description = 'Auto-expire deposit yang melewati batas waktu 1 jam';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $expired = Transaction::where('type', 'deposit')
            ->where('status', 'pending')
            ->where('expired_at', '<', now())
            ->whereNotNull('expired_at')
            ->get();

        if ($expired->isEmpty()) {
            $this->info('Tidak ada deposit yang perlu di-expire.');
            return self::SUCCESS;
        }

        $count = 0;
        foreach ($expired as $deposit) {
            $deposit->update(['status' => 'expired']);
            $count++;

            Log::info("Deposit auto-expired", [
                'id'         => $deposit->id,
                'reference'  => $deposit->reference,
                'user_id'    => $deposit->user_id,
                'amount'     => $deposit->amount,
                'expired_at' => $deposit->expired_at,
            ]);
        }

        $this->info("Berhasil expire {$count} deposit.");
        return self::SUCCESS;
    }
}