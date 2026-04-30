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
            $table->dateTime('last_whatsapp_photos_request_at')->nullable()->after('zip_s3_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('otp_verification_attempts', function (Blueprint $table) {
            $table->dropColumn('last_whatsapp_photos_request_at');
        });
    }
};
