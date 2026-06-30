<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tambah kolom expired_at ke tabel transactions untuk fitur timer deposit 1 jam.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // expired_at: waktu kadaluarsa deposit (null = tidak ada batas waktu)
            $table->timestamp('expired_at')->nullable()->after('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('expired_at');
        });
    }
};