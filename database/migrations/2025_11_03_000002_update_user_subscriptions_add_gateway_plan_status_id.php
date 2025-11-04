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
        Schema::table('user_subscriptions', function (Blueprint $table) {
            // Add plan reference
            $table->foreignId('plan_id')->nullable()->after('user_id')->constrained('subscription_plans');

            // Add payment gateway indicator
            $table->enum('payment_gateway', ['razorpay', 'stripe'])->nullable()->after('type');

            // Add gateway subscription identifier
            $table->string('subscription_id')->nullable()->after('payment_gateway');

            $table->string('status')->default('active')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            // Drop added columns and constraints
            $table->dropConstrainedForeignId('plan_id');
            $table->dropColumn(['payment_gateway', 'subscription_id']);
        });
    }
};


