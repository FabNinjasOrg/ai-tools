<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert 6 plans (3 monthly and 3 yearly)
        $plans = [
            // Monthly Plans
            [
                'name' => 'Basic Monthly',
                'razorpay_plan_id' => 'plan_RbBJXn4WELE4cl',
                'storage' => '5GB',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Standard Monthly',
                'razorpay_plan_id' => 'plan_RbBSDFcevGrKq6',
                'storage' => '10GB',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pro Monthly',
                'razorpay_plan_id' => 'plan_RbBU89l7FBoLmM',
                'storage' => '20GB',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Yearly Plans
            [
                'name' => 'Basic Yearly',
                'razorpay_plan_id' => 'plan_RbBQf3O0rJgXIv',
                'storage' => '5GB',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Standard Yearly',
                'razorpay_plan_id' => 'plan_RbBSsFJ3jKOERj',
                'storage' => '10GB',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pro Yearly',
                'razorpay_plan_id' => 'plan_RbBTYYqU3O7ooP',
                'storage' => '20GB',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('subscription_plans')->insert($plans);

        // Get the inserted plan IDs
        $basicMonthly = DB::table('subscription_plans')->where('name', 'Basic Monthly')->first();
        $standardMonthly = DB::table('subscription_plans')->where('name', 'Standard Monthly')->first();
        $proMonthly = DB::table('subscription_plans')->where('name', 'Pro Monthly')->first();
        $basicYearly = DB::table('subscription_plans')->where('name', 'Basic Yearly')->first();
        $standardYearly = DB::table('subscription_plans')->where('name', 'Standard Yearly')->first();
        $proYearly = DB::table('subscription_plans')->where('name', 'Pro Yearly')->first();

        // Insert pricing for each plan (6 records total - 1 per plan)
        $prices = [
            // Monthly Plan Prices
            [
                'subscription_plan_id' => $basicMonthly->id,
                'inr_price' => 399.00,
                'usd_price' => 4.99,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subscription_plan_id' => $standardMonthly->id,
                'inr_price' => 799.00,
                'usd_price' => 9.99,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subscription_plan_id' => $proMonthly->id,
                'inr_price' => 1599.00,
                'usd_price' => 19.99,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Yearly Plan Prices
            [
                'subscription_plan_id' => $basicYearly->id,
                'inr_price' => 3999.00,
                'usd_price' => 49.99,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subscription_plan_id' => $standardYearly->id,
                'inr_price' => 7999.00,
                'usd_price' => 99.99,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subscription_plan_id' => $proYearly->id,
                'inr_price' => 15999.00,
                'usd_price' => 199.99,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('subscription_plan_prices')->insert($prices);
    }
}
