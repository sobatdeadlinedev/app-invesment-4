<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('trading_signals', function (Blueprint $table) {
            $table->timestamp('scheduled_at')->nullable()->after('is_public');
        });
    }

    public function down()
    {
        Schema::table('trading_signals', function (Blueprint $table) {
            $table->dropColumn('scheduled_at');
        });
    }
};