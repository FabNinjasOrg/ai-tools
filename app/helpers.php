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

        /** @phpstan-ignore-next-line */
        return $user->subscriptions()
            ->where('type', 'trial')
            ->where('status', 'active')
            ->exists();
    }
}

