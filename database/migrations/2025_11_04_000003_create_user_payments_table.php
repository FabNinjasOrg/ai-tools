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
        Schema::create('user_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('payment_id');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->nullable();
            $table->string('invoice_id')->nullable();
            $table->string('order_id')->nullable();
            $table->timestamp('payment_time')->nullable();
            $table->enum('payment_gateway', ['razorpay', 'stripe'])->nullable();
            $table->string('payment_via', 50)->nullable();
            $table->enum('status', ['pending', 'completed', 'failed'])->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_payments');
    }
};


