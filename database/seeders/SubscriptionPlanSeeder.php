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
        // Insert base plans (normalized - only 3 records)
        $plans = [
            [
                'name' => 'Basic',
                'storage' => '5GB',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Standard',
                'storage' => '10GB',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pro',
                'storage' => '20GB',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('subscription_plans')->insert($plans);

        // Get the inserted plan IDs
        $basicPlan = DB::table('subscription_plans')->where('name', 'Basic')->first();
        $standardPlan = DB::table('subscription_plans')->where('name', 'Standard')->first();
        $proPlan = DB::table('subscription_plans')->where('name', 'Pro')->first();

        // Insert pricing for each plan (6 records total - 2 per plan)
        $prices = [
            // Basic Plan Prices
            [
                'subscription_plan_id' => $basicPlan->id,
                'plan_interval' => 'monthly',
                'inr_price' => 399.00,
                'usd_price' => 4.99,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subscription_plan_id' => $basicPlan->id,
                'plan_interval' => 'yearly',
                'inr_price' => 3999.00,
                'usd_price' => 49.99,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Standard Plan Prices
            [
                'subscription_plan_id' => $standardPlan->id,
                'plan_interval' => 'monthly',
                'inr_price' => 799.00,
                'usd_price' => 9.99,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subscription_plan_id' => $standardPlan->id,
                'plan_interval' => 'yearly',
                'inr_price' => 7999.00,
                'usd_price' => 99.99,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Pro Plan Prices
            [
                'subscription_plan_id' => $proPlan->id,
                'plan_interval' => 'monthly',
                'inr_price' => 1599.00,
                'usd_price' => 19.99,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subscription_plan_id' => $proPlan->id,
                'plan_interval' => 'yearly',
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
