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
        'storage',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the prices for this plan
     */
    public function prices(): HasMany
    {
        return $this->hasMany(SubscriptionPlanPrice::class);
    }

    /**
     * Get monthly price for this plan
     */
    public function monthlyPrice()
    {
        return $this->prices()->where('plan_interval', 'monthly')->first();
    }

    /**
     * Get yearly price for this plan
     */
    public function yearlyPrice()
    {
        return $this->prices()->where('plan_interval', 'yearly')->first();
    }

    /**
     * Scope to get only active plans
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
