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
            $table->string('session_token', 255)->nullable()->after('matched_found_photos');
            $table->json('matched_photo_id_json')->nullable()->after('session_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('otp_verification_attempts', function (Blueprint $table) {
            $table->dropColumn(['session_token', 'matched_photo_id_json']);
        });
    }
};


