<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'minimum_amount',
        'maximum_discount',
        'usage_limit',
        'usage_limit_per_customer',
        'usage_limit_per_user',
        'used_count',
        'starts_at',
        'expires_at',
        'is_active',
        'applicable_to',
        'applicable_ids',
        'applicable_packages',
        'applicable_customers',
        'created_by',
        'metadata'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];




    // العلاقات
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        $now = now();
        return $query->where('is_active', true)
                    ->where(function($q) use ($now) {
                        $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
                    })
                    ->where(function($q) use ($now) {
                        $q->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
                    });
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Accessors
   /* public function getTypeTextAttribute()
    {
        return match($this->type) {
            'percentage' => 'نسبة مئوية',
            'fixed_amount' => 'مبلغ ثابت',
            default => $this->type
        };
    }*/

    public function getStatusAttribute()
    {
        if (!$this->is_active) {
            return 'inactive';
        }

        $now = now();

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return 'scheduled';
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return 'expired';
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return 'exhausted';
        }

        return 'active';
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'scheduled' => 'مجدول',
            'expired' => 'منتهي الصلاحية',
            'exhausted' => 'مستنفد',
            default => $this->status
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'green',
            'inactive' => 'gray',
            'scheduled' => 'blue',
            'expired' => 'red',
            'exhausted' => 'orange',
            default => 'gray'
        };
    }

    public function getUsagePercentageAttribute()
    {
        if (!$this->usage_limit) {
            return 0;
        }

        return min(100, ($this->used_count / $this->usage_limit) * 100);
    }

    public function getRemainingUsesAttribute()
    {
        if (!$this->usage_limit) {
            return null;
        }

        return max(0, $this->usage_limit - $this->used_count);
    }

    public function getDaysUntilExpiryAttribute()
    {
        if (!$this->expires_at) {
            return null;
        }

        return now()->diffInDays($this->expires_at, false);
    }

    // Methods
    public function isValid(): bool
    {
        return $this->status === 'active';
    }

    public function isApplicableToPackage($packageId): bool
    {
        if (!$this->applicable_packages) {
            return true; // ينطبق على جميع الباكجات
        }

        return in_array($packageId, $this->applicable_packages);
    }

    public function isApplicableToCustomer($customerId): bool
    {
        if (!$this->applicable_customers) {
            return true; // ينطبق على جميع العملاء
        }

        return in_array($customerId, $this->applicable_customers);
    }

    public function canBeUsedBy($customerId): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        if (!$this->isApplicableToCustomer($customerId)) {
            return false;
        }

        if ($this->usage_limit_per_customer) {
            $customerUsageCount = $this->usages()
                                      ->where('user_id', $customerId)
                                      ->count();

            if ($customerUsageCount >= $this->usage_limit_per_customer) {
                return false;
            }
        }

        return true;
    }

    public function calculateDiscount($orderAmount, $packageId = null): float
    {
        if (!$this->isValid()) {
            return 0;
        }

        if ($packageId && !$this->isApplicableToPackage($packageId)) {
            return 0;
        }

        if ($this->minimum_amount && $orderAmount < $this->minimum_amount) {
            return 0;
        }

        $discount = 0;

        if ($this->type === 'percentage') {
            $discount = ($orderAmount * $this->value) / 100;
        } else {
            $discount = $this->value;
        }

        // تطبيق الحد الأقصى للخصم
        if ($this->maximum_discount && $discount > $this->maximum_discount) {
            $discount = $this->maximum_discount;
        }

        // التأكد من أن الخصم لا يتجاوز قيمة الطلب
        return min($discount, $orderAmount);
    }

    public function use($orderId, $customerId, $discountAmount, $orderAmount)
    {
        // إنشاء سجل استخدام
        $this->usages()->create([
            'order_id' => $orderId,
            'user_id' => $customerId,
            'discount_amount' => $discountAmount,
            'order_amount' => $orderAmount
        ]);

        // زيادة عداد الاستخدام
        $this->increment('used_count');

        // تحديث حالة الكوبون إذا تم استنفاده
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            $this->is_active = false;
            $this->save();
        }
    }

    public function activate()
    {
        $this->is_active = true;
        $this->save();
    }

    public function deactivate()
    {
        $this->is_active = false;
        $this->save();
    }

    public function duplicate()
    {
        $newCoupon = $this->replicate();
        $newCoupon->code = $this->generateUniqueCode();
        $newCoupon->name = $this->name . ' (نسخة)';
        $newCoupon->used_count = 0;
        $newCoupon->is_active = false;
        $newCoupon->starts_at = null;
        $newCoupon->expires_at = null;
        $newCoupon->save();

        return $newCoupon;
    }

    // Static Methods
    public static function findByCode($code)
    {
        return static::where('code', $code)->first();
    }

    public static function generateCode($length = 8)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $code;
    }

    public static function generateUniqueCode($length = 8)
    {
        do {
            $code = static::generateCode($length);
        } while (static::where('code', $code)->exists());

        return $code;
    }

    public static function getTypeOptions()
    {
        return [
            'percentage' => 'نسبة مئوية',
            'fixed_amount' => 'مبلغ ثابت'
        ];
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($coupon) {
            if (!$coupon->code) {
                $coupon->code = static::generateUniqueCode();
            }
        });

        static::deleting(function ($coupon) {
            // حذف سجلات الاستخدام المرتبطة
            $coupon->usages()->delete();
        });
    }


    // Enhanced Methods
    public function getTypeTextAttribute()
    {
        return match($this->type) {
            'percentage' => 'نسبة مئوية',
            'fixed_amount' => 'مبلغ ثابت',
            'free_shipping' => 'شحن مجاني',
            default => $this->type
        };
    }

    public function getFormattedValueAttribute(): string
    {
        return match($this->type) {
            'percentage' => $this->value . '%',
            'fixed_amount' => number_format($this->value, 2) . ' ريال',
            'free_shipping' => 'شحن مجاني',
            default => $this->value
        };
    }

    public function isValidForUser(?User $user = null): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        // Check per-user usage limit
        if ($this->usage_limit_per_user && $user) {
            $userUsageCount = $this->usages()
                ->where('user_id', $user->id)
                ->count();

            if ($userUsageCount >= $this->usage_limit_per_user) {
                return false;
            }
        }

        return true;
    }

    public function isApplicableToOrder(Order $order): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        // Check minimum amount
        if ($this->minimum_amount && $order->subtotal < $this->minimum_amount) {
            return false;
        }

        // Check applicability
        switch ($this->applicable_to) {
            case 'all':
                return true;

            case 'packages':
                return $order->package_id &&
                       in_array($order->package_id, $this->applicable_ids ?? []);

            case 'categories':
                return $order->package &&
                       $order->package->category_id &&
                       in_array($order->package->category_id, $this->applicable_ids ?? []);

            case 'users':
                return $order->user_id &&
                       in_array($order->user_id, $this->applicable_ids ?? []);

            case 'first_order':
                return $order->user->orders()->where('id', '!=', $order->id)->count() === 0;

            default:
                // Fallback to old logic
                return $this->isApplicableToPackage($order->package_id) &&
                       $this->isApplicableToCustomer($order->user_id);
        }
    }

    public function applyToOrder(Order $order, ?User $user = null): array
    {
        if (!$this->isApplicableToOrder($order)) {
            return [
                'success' => false,
                'message' => 'الكوبون غير قابل للتطبيق على هذا الطلب'
            ];
        }

        if (!$this->isValidForUser($user)) {
            return [
                'success' => false,
                'message' => 'الكوبون غير صالح للاستخدام'
            ];
        }

        $discountAmount = $this->calculateDiscount($order->subtotal, $order->package_id);

        // Update order
        $order->update([
            'coupon_id' => $this->id,
            'coupon_code' => $this->code,
            'discount_amount' => $discountAmount
        ]);

        // Record usage
        $this->use($order->id, $user?->id ?? $order->user_id, $discountAmount, $order->subtotal);

        return [
            'success' => true,
            'message' => 'تم تطبيق الكوبون بنجاح',
            'discount_amount' => $discountAmount,
            'formatted_discount' => number_format($discountAmount, 2) . ' ريال'
        ];
    }

    public function extendExpiry(int $days): bool
    {
        if ($this->expires_at) {
            return $this->update([
                'expires_at' => $this->expires_at->addDays($days)
            ]);
        }

        return $this->update([
            'expires_at' => now()->addDays($days)
        ]);
    }

    public function increaseUsageLimit(int $amount): bool
    {
        if ($this->usage_limit) {
            return $this->update([
                'usage_limit' => $this->usage_limit + $amount
            ]);
        }

        return $this->update([
            'usage_limit' => $amount
        ]);
    }

    // Enhanced Static Methods
    public static function getExpiringSoon(int $days = 7): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('is_active', true)
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [now(), now()->addDays($days)])
            ->get();
    }

    public static function getStatistics(string $period = 'month'): array
    {
        $startDate = match($period) {
            'today' => today(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'quarter' => now()->startOfQuarter(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth()
        };

        $endDate = now();

        return [
            'total_coupons' => static::count(),
            'active_coupons' => static::where('is_active', true)->count(),
            'expired_coupons' => static::expired()->count(),
            'total_usages' => CouponUsage::whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_discount_given' => CouponUsage::whereBetween('created_at', [$startDate, $endDate])->sum('discount_amount'),
            'most_used_coupons' => static::withCount(['usages' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }])
                ->orderBy('usages_count', 'desc')
                ->limit(10)
                ->get(),
            'usage_by_type' => static::join('coupon_usages', 'coupons.id', '=', 'coupon_usages.coupon_id')
                ->whereBetween('coupon_usages.created_at', [$startDate, $endDate])
                ->groupBy('coupons.type')
                ->selectRaw('coupons.type, COUNT(*) as count, SUM(coupon_usages.discount_amount) as total_discount')
                ->get()
                ->keyBy('type')
                ->toArray(),
        ];
    }

    public static function createPromotionalCampaign(array $campaignData): array
    {
        $coupons = [];

        for ($i = 0; $i < ($campaignData['quantity'] ?? 1); $i++) {
            $coupons[] = static::create([
                'code' => static::generateUniqueCode($campaignData['code_length'] ?? 8),
                'name' => $campaignData['name'] . ' #' . ($i + 1),
                'description' => $campaignData['description'],
                'type' => $campaignData['type'],
                'value' => $campaignData['value'],
                'minimum_amount' => $campaignData['minimum_amount'] ?? null,
                'maximum_discount' => $campaignData['maximum_discount'] ?? null,
                'usage_limit' => $campaignData['usage_limit_per_coupon'] ?? null,
                'usage_limit_per_user' => $campaignData['usage_limit_per_user'] ?? 1,
                'is_active' => true,
                'starts_at' => $campaignData['starts_at'] ?? now(),
                'expires_at' => $campaignData['expires_at'] ?? null,
                'applicable_to' => $campaignData['applicable_to'] ?? 'all',
                'applicable_ids' => $campaignData['applicable_ids'] ?? null,
                'created_by' => auth()->id(),
                'metadata' => [
                    'campaign' => $campaignData['campaign_name'] ?? 'Promotional Campaign',
                    'created_by' => auth()->id(),
                ]
            ]);
        }

        return $coupons;
    }

    public static function cleanupExpired(): int
    {
        return static::where('expires_at', '<', now()->subDays(30))
            ->where('is_active', false)
            ->delete();
    }

    public static function deactivateExpired(): int
    {
        return static::where('expires_at', '<', now())
            ->where('is_active', true)
            ->update(['is_active' => false]);
    }

    public static function getApplicabilityOptions(): array
    {
        return [
            'all' => 'جميع الطلبات',
            'packages' => 'باكجات محددة',
            'categories' => 'فئات محددة',
            'users' => 'عملاء محددين',
            'first_order' => 'الطلب الأول فقط',
        ];
    }

    public static function getEnhancedTypeOptions(): array
    {
        return [
            'percentage' => 'نسبة مئوية',
            'fixed_amount' => 'مبلغ ثابت',
            'free_shipping' => 'شحن مجاني'
        ];
    }
}
