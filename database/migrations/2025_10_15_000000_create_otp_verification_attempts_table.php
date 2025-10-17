<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_verification_attempts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('album_id')->index();
            $table->string('album_uuid')->index();
            $table->string('phone_number', 32)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->timestamps();

            $table->foreign('album_id')->references('id')->on('albums')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_verification_attempts');
    }
};


