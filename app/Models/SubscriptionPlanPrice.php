<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPlanPrice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'subscription_plan_id',
        'inr_price',
        'usd_price',
        'status',
    ];

    protected $casts = [
        'inr_price' => 'decimal:2',
        'usd_price' => 'decimal:2',
        'status' => 'string',
    ];

    /**
     * Get the plan that owns this price
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    /**
     * Scope to get only active prices
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
