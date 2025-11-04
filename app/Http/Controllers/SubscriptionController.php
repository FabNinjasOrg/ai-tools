<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserPayment;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;

class SubscriptionController extends Controller
{
    protected Api $razorpayApi;

    public function __construct()
    {
        $this->razorpayApi = new Api(env('RAZOR_PAY_API_KEY_ID'), env('RAZOR_PAY_API_KEY_SECRET'));
    }

    public function subscribePlan(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'plan_id' => 'required|integer'
        ]);
        $planId = $validated['plan_id'];
        $userRegion = Auth::user()->country_code ?? 'US';

        // Get the plan with its price
        $plan = SubscriptionPlan::with('prices')->find($planId);

        if ($userRegion === 'IN') {
            // Implement Razorpay
			return $this->subscribeProcessRazorpay($plan);

        } else if($userRegion === 'US'){
            // Implement Stripe

        }
    }

	private function subscribeProcessRazorpay($plan)
    {
        try{
            $user = Auth::user();

            if(! isset($user->razorpay_customer_id) || empty($user->razorpay_customer_id)){
                // create the customer in razorpay
                $customer = $this->razorpayApi->customer->create([
                    'name' => $user->name,
                    'email' => $user->email
                ]);

                // update razorpay customer id for user
                $user->update([
                    'razorpay_customer_id' => $customer['id']
                ]);
            }

            // subscribe the user to the plan
            $subscription = $this->razorpayApi->subscription->create([
                'plan_id' => $plan->razorpay_plan_id,
                'total_count' => 12,
                'customer_notify' => true,
                'notes' => [
                    'user_id' => $user->id,
                    'plan_name' => $plan->name,
                ],
            ]);

			$userSubscription = $user->subscriptions()->update([
                'plan_id' => $plan->id,
                'type' => 'subscription',
                'payment_gateway' => 'razorpay',
                'subscription_id' => $subscription['id'],
                'status' => $subscription['status']
            ]);

			return redirect()->route('face_finder.manage_subscription')->with('razorPayDeatils', [
				'subscription_id' => $subscription['id'] ?? null,
			]);
        } catch (\Exception $e){
            logger(['error' => 'Subscription failed: ' . $e->getMessage()]);
			return redirect()->route('face_finder.manage_subscription')->withErrors('Subscription process failed: ' . $e->getMessage());
        }
    }

    public function cancelSubscription(Request $request)
    {
        $subscriptionId = $request->input('subscription_id');
        $user = Auth::user();

        // Fetch subscription
        $subscription = $this->razorpayApi->subscription->fetch($subscriptionId);

        if (!$subscription) {
            return redirect()->back()->with('error', 'Subscription not found.');
        }

        $cancellation = $subscription->cancel(['cancel_at_cycle_end' => 0]);

        $user->subscriptions()->where('subscription_id', $subscriptionId)
            ->update([
                'status' => $cancellation->status
            ]);

        Log::info('Subscription Cancelled', [
            'subscription_id' => $subscriptionId,
            'response' => $cancellation,
        ]);

        return redirect()->back()->with('success', 'Subscription cancelled successfully.');
    }

    public function handleRazorpayWebhook(Request $request)
    {
        $webhookSecret = env('RAZOR_PAY_WEBHOOK_SECRET');
        $signature = $request->header('X-Razorpay-Signature');
        $payload = $request->getContent();

        try {
            // Verify webhook authenticity
            $this->razorpayApi->utility->verifyWebhookSignature($payload, $signature, $webhookSecret);

            $data = json_decode($payload, true);
            $event = $data['event'] ?? null;

            $userId = $data['payload']['subscription']['entity']['notes']['user_id'] ?? null;
            $user = User::find($userId);

            // Handle events
            switch ($event) {
                case 'payment.captured':
                case 'payment.failed':
                    $payment = $data['payload']['payment']['entity'] ?? [];
                    Log::info('Payment Captured Event', [
                        'payload' => $payment,
                    ]);

                    $user = User::where('razorpay_customer_id', $payment['customer_id'])->first();

                    UserPayment::create([
                        'user_id' => $user ? $user->id : null,
                        'payment_id' => $payment['id'] ?? null,
                        'amount' => isset($payment['amount']) ? $payment['amount'] / 100 : 0,
                        'currency' => $payment['currency'] ?? null,
                        'invoice_id' => $payment['invoice_id'] ?? null,
                        'order_id' => $payment['order_id'] ?? null,
                        'payment_time' => isset($payment['created_at']) ? Carbon::createFromTimestamp($payment['created_at'])->setTimezone('UTC')->toDateTimeString() : null,
                        'payment_gateway' => 'razorpay',
                        'payment_via' => $payment['method'] ?? null,
                        'status' => $payment['status'] == 'captured' ? 'completed' : 'failed',
                    ]);

                    break;

                case 'subscription.activated':
                    $subscription = $data['payload']['subscription']['entity'] ?? [];
                    Log::info('Subscription Activated Event', [
                        'payload' => $subscription,
                    ]);

                    // update user's subscription status
                    $user->subscriptions()
                        ->where('subscription_id', $subscription['id'])
                        ->update([
                            'status' => $subscription['status'],
                            'start_date' => Carbon::createFromTimestamp($subscription['current_start'])->setTimezone('UTC')->toDateTimeString(),
                            'end_date' => Carbon::createFromTimestamp($subscription['current_end'])->setTimezone('UTC')->toDateTimeString(),
                        ]);

                    break;

                default:
                    Log::warning('Unhandled Razorpay Webhook Event', [
                        'event' => $event,
                    ]);
            }
        } catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
            // Invalid webhook signature
            Log::error('Razorpay Webhook Signature Verification Failed', [
                'message' => $e->getMessage(),
                'signature' => $signature,
            ]);
            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (\Exception $e) {
            Log::error('Razorpay Webhook Error', [
                'message' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Server error'], 500);
        }

        return response()->json(['status' => 'ok']);
    }
}
