<?php

namespace App\Http\Controllers;

use App\Services\CalculateUserStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StorageController extends Controller
{
    private $calculateUserStorageService;

    public function __construct(CalculateUserStorageService $calculateUserStorageService)
    {
        $this->calculateUserStorageService = $calculateUserStorageService;
    }

    /**
     * Display the storage usage page
     */
    public function index()
    {
        // Redirect trial users to buy subscription page
        if (!userHasAccessibility()) {
            return redirect()->route('face_finder.buy_subscription');
        }

        $user = Auth::user();

        // Calculate user storage
        $storageData = $this->calculateUserStorageService->calculate($user);

        // Get user's subscription plan storage limit
        $storageLimit = 0;
        $planName = null;
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

        if ($activeSubscription && $activeSubscription->plan && $activeSubscription->plan->storage) {
            $storageLimit = (float) preg_replace('/[^0-9.]/', '', $activeSubscription->plan->storage);
            $planName = $activeSubscription->plan->name;
        }

        $storageLeftGB = max(0, $storageLimit - $storageData['gigabytes']);
        $storageLeftMB = $storageLeftGB * 1024;
        $percentageUsed = $storageLimit > 0 ? ($storageData['gigabytes'] / $storageLimit) * 100 : 0;
        $percentageUsed = min(100, $percentageUsed);

        return view('faceFinder.storage', [
            'storageUsedBytes' => $storageData['bytes'],
            'storageUsedKB' => $storageData['kilobytes'],
            'storageUsedMB' => $storageData['megabytes'],
            'storageUsedGB' => $storageData['gigabytes'],
            'storageLimitGB' => $storageLimit,
            'storageLeftGB' => $storageLeftGB,
            'storageLeftMB' => $storageLeftMB,
            'percentageUsed' => $percentageUsed,
            'planName' => $planName,
        ]);
    }
}

