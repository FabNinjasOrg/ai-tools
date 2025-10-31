<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Auth;

class SubscriptionController extends Controller
{
    public function selectPlan(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'plan_id' => 'required|integer',
            'billing_cycle' => 'required|string|in:monthly,yearly'
        ]);
        
        $planId = $validated['plan_id'];
        $billingCycle = $validated['billing_cycle'];
        $userRegion = Auth::user()->country_code ?? 'US';
        $getPlanPrice = $billingCycle == 'monthly' ? SubscriptionPlan::find($planId)->monthlyPrice() : SubscriptionPlan::find($planId)->yearlyPrice();

        if ($userRegion === 'IN') {
            // Implement Razorpay

            $planPrice = $getPlanPrice->inr_price;
        } else {
            // Implement Stripe

            $planPrice = $getPlanPrice->inr_doller;
        }
    }
}
