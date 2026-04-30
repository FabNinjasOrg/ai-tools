<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('otp_verification_attempts', function (Blueprint $table) {
            $table->integer('attempts')->default(1)->after('user_agent');
            $table->integer('matched_found_photos')->default(0)->after('attempts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('otp_verification_attempts', function (Blueprint $table) {
            $table->dropColumn(['attempts', 'matched_found_photos']);
        });
    }
};
