<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Yajra\DataTables\Facades\DataTables;

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
        $userRegion = 'IN'; // Auth::user()->country_code ?? 'IN';

        // Get the plan with its price
        $plan = SubscriptionPlan::with('prices')->find($planId);

        if ($userRegion === 'IN') {
            // Implement Razorpay
			return $this->subscribeProcessRazorpay($plan);
        }

        return;
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
			return redirect()->route('face_finder.buy_subscription')->withErrors('Subscription process failed: ' . $e->getMessage());
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

    public function billing()
    {
        // Redirect trial users to buy subscription page
        if (!userHasAccessibility()) {
            return redirect()->route('face_finder.buy_subscription');
        }

        $userId = Auth::id();

        // Get payment statistics
        $totalPayments = UserPayment::where('user_id', $userId)
            ->where('status', 'completed')
            ->sum('amount');

        $failedPayments = UserPayment::where('user_id', $userId)
            ->where('status', 'failed')
            ->count();

        $totalTransactions = UserPayment::where('user_id', $userId)->count();

        $firstPayment = UserPayment::where('user_id', $userId)
            ->where('status', 'completed')
            ->first();

        $currency = $firstPayment ? $firstPayment->currency : 'INR';

        return view('faceFinder.billing', compact('totalPayments', 'failedPayments', 'totalTransactions', 'currency'));
    }

    public function billingData(Request $request)
    {
        if ($request->ajax()) {
            $data = UserPayment::with('user')
                ->where('user_id', Auth::id())
                ->orderBy('payment_time', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('payment_time', function ($row) {
                    return $row->payment_time ? $row->payment_time->format('d M Y, h:i A') : '-';
                })
                ->editColumn('amount', function ($row) {
                    return $row->currency . ' ' . number_format($row->amount, 2);
                })
                ->editColumn('status', function ($row) {
                    $statusColors = [
                        'completed' => 'bg-green-100 text-green-700',
                        'failed' => 'bg-red-100 text-red-700',
                        'pending' => 'bg-yellow-100 text-yellow-700',
                        'refunded' => 'bg-blue-100 text-blue-700',
                    ];

                    $colorClass = $statusColors[$row->status] ?? 'bg-gray-100 text-gray-700';

                    return '<span class="px-2 py-1 text-xs font-semibold rounded-full ' . $colorClass . '">'
                           . ucfirst($row->status)
                           . '</span>';
                })
                ->rawColumns(['status'])
                ->make(true);
        }
    }

    public function buySubscription()
    {
        $user = Auth::user();
        $currency = 'INR'; // Always INR regardless of country

        $plans = $this->getPlanData($currency);

        $currentPlanId = null;
        $paymentPending = userSubscribedButPaymentPending();

        if (userSubscriptionActivated()) {
            $currentSubscription = $user->subscriptions()
                ->where('type', 'subscription')
                ->where('status', 'active')
                ->latest()
                ->first();

            if ($currentSubscription && $currentSubscription->plan_id) {
                $currentPlanId = $currentSubscription->plan_id;
            }
        }

        return view('faceFinder.buy-subscription', compact('plans', 'currency', 'currentPlanId', 'paymentPending'));
    }

    public function manageSubscription()
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        $userSubscribed = $user->subscriptions()
            ->where('type', 'subscription')
            ->exists();

        if (!($userSubscribed)) {
            return redirect()->route('face_finder.buy_subscription');
        }

        $currency = 'INR'; // Always INR regardless of country

        $currentSubscription = $user->subscriptions()
            ->where('type', 'subscription')
            ->latest()
            ->first();

        $planData = null;
        $subscriptionDetails = null;

        if ($currentSubscription && $currentSubscription->plan_id) {
            // Get plan data for the specific plan
            $planData = $this->getPlanData($currency, $currentSubscription->plan_id);

            // Build subscription details
            $subscriptionDetails = [
                'subscription_id' => $currentSubscription->subscription_id,
                'status' => $currentSubscription->status,
                'start_date' => $currentSubscription->start_date,
                'end_date' => $currentSubscription->end_date,
                'payment_gateway' => $currentSubscription->payment_gateway,
            ];
        }
        $currencySymbol = $currency === 'INR' ? '₹' : '$';

        return view('faceFinder.manage-subscription', [
            'currency' => $currency,
            'currencySymbol' => $currencySymbol,
            'planData' => $planData,
            'subscriptionDetails' => $subscriptionDetails
        ]);
    }

    public function pricing()
    {
        if (Auth::check()) {
            return redirect()->route('face_finder.buy_subscription');
        }

        $currency = 'INR'; // Always INR
        $plans = $this->getPlanData($currency);

        return view('faceFinder.pricing', compact('plans', 'currency'));
    }

    private function getPlanData($currency = 'USD', $planId = null)
    {
        $subscriptionPlan = SubscriptionPlan::query()
            ->with(['prices' => function ($query) {
                $query->active();
            }]);

        // If plan ID is provided, get specific plan
        if ($planId) {
            $subscriptionPlan->where('id', $planId);
            $plan = $subscriptionPlan->first();

            if (!$plan) {
                return null;
            }

            return $this->formatPlanData($plan, $currency);
        }

        // Get all active plans
        $plans = $subscriptionPlan->active()->orderBy('id')->get();

        return $plans->map(function ($plan) use ($currency) {
            return $this->formatPlanData($plan, $currency);
        })->toArray();
    }

    private function formatPlanData($plan, $currency)
    {
        $priceField = $currency === 'INR' ? 'inr_price' : 'usd_price';
        $planIdField = $currency === 'INR' ? 'razorpay_plan_id' : 'stripe_plan_id';

        $price = $plan->prices->first();

        return [
            'id' => $plan->id,
            'name' => $plan->name,
            'plan_id' => $plan->$planIdField,
            'storage' => $plan->storage,
            'price' => $price ? $price->$priceField : 0,
            'amount' => $price ? $price->$priceField : null,
            'billing_type' => str_contains(strtolower($plan->name), 'monthly') ? 'monthly' : 'yearly',
        ];
    }
}
