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
            $table->text('zip_s3_url')->nullable()->after('matched_photo_id_json');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('otp_verification_attempts', function (Blueprint $table) {
            $table->dropColumn('zip_s3_url');
        });
    }
};
