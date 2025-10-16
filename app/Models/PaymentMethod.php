<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'description',
        'icon',
        'is_active',
        'is_online',
        'processing_fee',
        'processing_fee_type',
        'min_amount',
        'max_amount',
        'supported_currencies',
        'gateway_config',
        'display_order',
        'metadata'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_online' => 'boolean',
        'processing_fee' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'supported_currencies' => 'array',
        'gateway_config' => 'array',
        'metadata' => 'array'
    ];

    /**
     * Relationships
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOnline($query)
    {
        return $query->where('is_online', true);
    }

    public function scopeOffline($query)
    {
        return $query->where('is_online', false);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }

    /**
     * Accessors
     */
    public function getTypeTextAttribute(): string
    {
        return match($this->type) {
            'cash' => 'نقداً',
            'bank_transfer' => 'تحويل بنكي',
            'credit_card' => 'بطاقة ائتمان',
            'debit_card' => 'بطاقة خصم',
            'check' => 'شيك',
            'online_payment' => 'دفع إلكتروني',
            'mobile_payment' => 'دفع عبر الجوال',
            'digital_wallet' => 'محفظة رقمية',
            default => 'غير محدد'
        };
    }

    public function getIconClassAttribute(): string
    {
        if ($this->icon) {
            return $this->icon;
        }

        return match($this->type) {
            'cash' => 'fas fa-money-bill-wave',
            'bank_transfer' => 'fas fa-university',
            'credit_card' => 'fas fa-credit-card',
            'debit_card' => 'fas fa-credit-card',
            'check' => 'fas fa-money-check',
            'online_payment' => 'fas fa-globe',
            'mobile_payment' => 'fas fa-mobile-alt',
            'digital_wallet' => 'fas fa-wallet',
            default => 'fas fa-question-circle'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return $this->is_active ? 'success' : 'danger';
    }

    public function getStatusTextAttribute(): string
    {
        return $this->is_active ? 'نشط' : 'غير نشط';
    }

    /**
     * Methods
     */
    public function calculateProcessingFee(float $amount): float
    {
        if (!$this->processing_fee) {
            return 0;
        }

        if ($this->processing_fee_type === 'percentage') {
            return round(($amount * $this->processing_fee) / 100, 2);
        }

        return $this->processing_fee;
    }

    public function isAmountSupported(float $amount): bool
    {
        if ($this->min_amount && $amount < $this->min_amount) {
            return false;
        }

        if ($this->max_amount && $amount > $this->max_amount) {
            return false;
        }

        return true;
    }

    public function isCurrencySupported(string $currency): bool
    {
        if (!$this->supported_currencies) {
            return true; // If no specific currencies defined, support all
        }

        return in_array($currency, $this->supported_currencies);
    }

    public function canProcessPayment(float $amount, string $currency = 'SAR'): bool
    {
        return $this->is_active 
            && $this->isAmountSupported($amount) 
            && $this->isCurrencySupported($currency);
    }

    /**
     * Create default payment methods.
     */
    public static function createDefaults(): void
    {
        $methods = [
            [
                'name' => 'نقداً',
                'type' => 'cash',
                'description' => 'الدفع نقداً عند التسليم',
                'is_active' => true,
                'is_online' => false,
                'display_order' => 1,
            ],
            [
                'name' => 'تحويل بنكي',
                'type' => 'bank_transfer',
                'description' => 'تحويل بنكي مباشر',
                'is_active' => true,
                'is_online' => false,
                'display_order' => 2,
            ],
            [
                'name' => 'بطاقة ائتمان',
                'type' => 'credit_card',
                'description' => 'الدفع بالبطاقة الائتمانية',
                'is_active' => true,
                'is_online' => true,
                'processing_fee' => 2.5,
                'processing_fee_type' => 'percentage',
                'display_order' => 3,
            ],
            [
                'name' => 'شيك',
                'type' => 'check',
                'description' => 'الدفع بالشيك',
                'is_active' => true,
                'is_online' => false,
                'display_order' => 4,
            ],
            [
                'name' => 'دفع إلكتروني',
                'type' => 'online_payment',
                'description' => 'الدفع عبر البوابات الإلكترونية',
                'is_active' => true,
                'is_online' => true,
                'processing_fee' => 3.0,
                'processing_fee_type' => 'percentage',
                'display_order' => 5,
            ],
        ];

        foreach ($methods as $method) {
            static::firstOrCreate(
                ['type' => $method['type']],
                $method
            );
        }
    }

    /**
     * Get available payment methods for amount and currency.
     */
    public static function getAvailableForPayment(float $amount, string $currency = 'SAR'): \Illuminate\Database\Eloquent\Collection
    {
        return static::active()
            ->ordered()
            ->get()
            ->filter(function ($method) use ($amount, $currency) {
                return $method->canProcessPayment($amount, $currency);
            });
    }

    /**
     * Get payment statistics.
     */
    public function getPaymentStatistics(string $period = 'month'): array
    {
        $startDate = match($period) {
            'today' => today(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth()
        };

        $endDate = now();

        $payments = $this->payments()
            ->whereBetween('payment_date', [$startDate, $endDate]);

        return [
            'total_payments' => $payments->count(),
            'total_amount' => $payments->where('status', 'completed')->sum('amount'),
            'successful_payments' => $payments->where('status', 'completed')->count(),
            'failed_payments' => $payments->where('status', 'failed')->count(),
            'success_rate' => $this->getSuccessRate($startDate, $endDate),
        ];
    }

    /**
     * Get success rate for this payment method.
     */
    public function getSuccessRate(?\Carbon\Carbon $startDate = null, ?\Carbon\Carbon $endDate = null): float
    {
        $query = $this->payments();
        
        if ($startDate && $endDate) {
            $query->whereBetween('payment_date', [$startDate, $endDate]);
        }

        $total = $query->count();
        $successful = $query->where('status', 'completed')->count();

        return $total > 0 ? round(($successful / $total) * 100, 2) : 0;
    }

    /**
     * Process payment through this method.
     */
    public function processPayment(Payment $payment): bool
    {
        if (!$this->canProcessPayment($payment->amount, $payment->currency ?? 'SAR')) {
            return false;
        }

        try {
            if ($this->is_online) {
                return $this->processOnlinePayment($payment);
            } else {
                return $this->processOfflinePayment($payment);
            }
        } catch (\Exception $e) {
            $payment->markAsFailed($e->getMessage());
            return false;
        }
    }

    /**
     * Process online payment.
     */
    private function processOnlinePayment(Payment $payment): bool
    {
        // This would integrate with actual payment gateways
        // For now, simulate the process
        return $payment->processPayment();
    }

    /**
     * Process offline payment.
     */
    private function processOfflinePayment(Payment $payment): bool
    {
        // Offline payments are usually marked as pending
        // and completed manually after verification
        return $payment->update(['status' => 'pending']);
    }
}
