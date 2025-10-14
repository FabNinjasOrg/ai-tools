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
        Schema::create('albums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('zip_filename')->nullable();
            $table->string('zip_path');
            $table->unsignedBigInteger('zip_size_bytes')->default(0);
            $table->unsignedInteger('photos_count')->default(0);
            $table->string('public_url')->nullable()->unique();
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('albums');
    }
};
