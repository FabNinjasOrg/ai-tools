<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('isUserOnTrial')) {
    /**
     * Check if the authenticated user has an active trial subscription
     *
     * @return bool
     */
    function isUserOnTrial(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        return $user->subscriptions()
            ->where('type', 'trial')
            ->where('status', 'active')
            ->exists();
    }
}

if (!function_exists('userSubscribedButPaymentPending')) {
    /**
     * @return bool
     */
    function userSubscribedButPaymentPending(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        return $user->subscriptions()
            ->where('type', 'subscription')
            ->where('status', 'created')
            ->exists();
    }
}

if (!function_exists('userSubscriptionActivated')) {
    /**
     * @return bool
     */
    function userSubscriptionActivated(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        return $user->subscriptions()
            ->where('type', 'subscription')
            ->where('status', 'active')
            ->exists();
    }
}

if (!function_exists('userOnLastSubscriptionCycle')) {
    /**
     * @return bool
     */
    function userOnLastSubscriptionCycle(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        return $user->subscriptions()
            ->where('type', 'subscription')
            ->where('status', 'cancelled')
            ->whereDate('end_date', '>', now())
            ->exists();
    }
}
