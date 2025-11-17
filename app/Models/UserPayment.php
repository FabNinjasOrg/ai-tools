<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPayment extends Model
{
    use HasFactory;

    protected $table = 'user_payments';

    protected $fillable = [
        'user_id',
        'payment_id',
        'amount',
        'currency',
        'invoice_id',
        'order_id',
        'payment_time',
        'payment_gateway',
        'payment_via',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_time' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }
}


