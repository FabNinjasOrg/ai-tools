<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'razorpay_plan_id',
        'storage',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the price for this plan (one-to-one relationship)
     */
    public function price()
    {
        return $this->hasOne(SubscriptionPlanPrice::class);
    }

    /**
     * Get the prices for this plan (for backward compatibility)
     */
    public function prices(): HasMany
    {
        return $this->hasMany(SubscriptionPlanPrice::class);
    }

    /**
     * Scope to get only active plans
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
