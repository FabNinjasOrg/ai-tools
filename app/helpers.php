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

if (!function_exists('userHasAccessibility')) {
    /**
     * @return bool
     */
    function userHasAccessibility(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        $userSubscribed = $user->subscriptions()
            ->where('type', 'subscription')
            ->where('status', 'active')
            ->exists();

        $userCancelButActive = $user->subscriptions()
            ->where('type', 'subscription')
            ->where('status', 'cancelled')
            ->whereDate('end_date', '>', now())
            ->exists();

        return $userSubscribed || $userCancelButActive;
    }
}

if (!function_exists('isUserStorageFull')) {
    /**
     * @return bool
     */
    function isUserStorageFull($user = null): bool
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            return false;
        }

        // Get the user's active subscription with plan
        $activeSubscription = $user->subscriptions()
            ->with('plan')
            ->where(function ($query) {
                $query->where('status', 'active')
                    ->whereDate('end_date', '>=', now())
                    ->orWhere(function ($q) {
                        $q->where('status', 'cancelled')
                            ->whereDate('end_date', '>', now());
                    });
            })
            ->first();

        $plan = $activeSubscription->plan;

        $storageLimit = $plan->storage;
        $limitInGB = (float) preg_replace('/[^0-9.]/', '', $storageLimit);

        // Convert GB limit to bytes for accurate comparison
        $limitInBytes = $limitInGB * 1024 * 1024 * 1024;

        // Calculate user's current storage
        $calculateService = app(\App\Services\CalculateUserStorageService::class);
        $storageUsed = $calculateService->calculate($user);

        // Compare used storage in bytes with the limit
        $usedBytes = $storageUsed['bytes'];

        return $usedBytes >= $limitInBytes;
    }
}
