<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('otp', function (Blueprint $table) {
            $table->string('purpose', 30)->default('reset_password')->after('email')->index();
        });
    }

    public function down(): void
    {
        Schema::table('otp', function (Blueprint $table) {
            $table->dropColumn('purpose');
        });
    }
};
