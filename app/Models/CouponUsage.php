<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouponUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'coupon_id',
        'order_id',
        'user_id',
        'discount_amount',
        'order_amount'
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'order_amount' => 'decimal:2'
    ];

    // العلاقات
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getDiscountPercentageAttribute()
    {
        if ($this->order_amount > 0) {
            return ($this->discount_amount / $this->order_amount) * 100;
        }
        
        return 0;
    }

    public function getSavingsAttribute()
    {
        return $this->discount_amount;
    }
}
