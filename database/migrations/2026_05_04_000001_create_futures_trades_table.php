<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('futures_trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('coin', 20);
            $table->enum('direction', ['call', 'put']);
            $table->decimal('amount', 15, 2);
            $table->decimal('entry_price', 20, 8);
            $table->decimal('close_price', 20, 8)->nullable();
            $table->decimal('profit_loss', 15, 2)->nullable();
            $table->decimal('payout_rate', 5, 2)->default(85.00);
            $table->enum('result', ['win', 'lose'])->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['status', 'opened_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('futures_trades');
    }
};
