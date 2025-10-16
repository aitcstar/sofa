<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'invoice_id',
        'customer_id',
        'payment_method_id',
        'payment_number',
        'amount',
        'currency',
        'payment_method',
        'status',
        'reference_number',
        'transaction_id',
        'gateway_response',
        'notes',
        'payment_details',
        'payment_date',
        'processed_by',
        'confirmed_at',
        'failed_at',
        'refunded_at',
        'metadata'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_details' => 'array',
        'gateway_response' => 'array',
        'metadata' => 'array',
        'payment_date' => 'datetime',
        'confirmed_at' => 'datetime',
        'failed_at' => 'datetime',
        'refunded_at' => 'datetime'
    ];

    // العلاقات
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }


    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'completed' => 'success',
            'failed' => 'danger',
            'refunded' => 'info',
            default => 'secondary'
        };
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending' => 'في الانتظار',
            'completed' => 'مكتمل',
            'failed' => 'فاشل',
            'refunded' => 'مسترد',
            default => 'غير محدد'
        };
    }

    public function getPaymentMethodTextAttribute()
    {
        return match($this->payment_method) {
            'cash' => 'نقداً',
            'bank_transfer' => 'تحويل بنكي',
            'credit_card' => 'بطاقة ائتمان',
            'check' => 'شيك',
            'online' => 'دفع إلكتروني',
            default => 'غير محدد'
        };
    }

    public function getPaymentMethodIconAttribute()
    {
        return match($this->payment_method) {
            'cash' => 'fas fa-money-bill-wave',
            'bank_transfer' => 'fas fa-university',
            'credit_card' => 'fas fa-credit-card',
            'check' => 'fas fa-money-check',
            'online' => 'fas fa-globe',
            default => 'fas fa-question-circle'
        };
    }





    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // Enhanced Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('payment_date', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('payment_date', now()->month)
                    ->whereYear('payment_date', now()->year);
    }

    // Enhanced Methods
    public static function generatePaymentNumber(): string
    {
        $year = now()->year;
        $month = now()->format('m');

        $lastPayment = static::whereYear('created_at', $year)
            ->whereMonth('created_at', now()->month)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastPayment ?
            (int) substr($lastPayment->payment_number, -4) + 1 : 1;

        return "PAY-{$year}{$month}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public static function createPayment(array $data): self
    {
        $data['payment_number'] = static::generatePaymentNumber();
        $data['currency'] = $data['currency'] ?? 'SAR';
        $data['status'] = $data['status'] ?? 'pending';
        $data['payment_date'] = $data['payment_date'] ?? now();

        return static::create($data);
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2) . ' ' . ($this->currency ?? 'SAR');
    }

    public function markAsCompleted(?User $processedBy = null): bool
    {
        $updated = $this->update([
            'status' => 'completed',
            'confirmed_at' => now(),
            'processed_by' => $processedBy?->id ?? auth()->id()
        ]);

        if ($updated) {
            // Update related invoice payment status
            if ($this->invoice) {
                $this->invoice->updatePaymentStatus();
            }

            // Update order payment status
            $this->order->updatePaymentStatus();
        }

        return $updated;
    }

    public function markAsFailed(string $reason = null): bool
    {
        $details = $this->payment_details ?? [];
        if ($reason) {
            $details['failure_reason'] = $reason;
        }

        return $this->update([
            'status' => 'failed',
            'failed_at' => now(),
            'payment_details' => $details
        ]);
    }

    public function refund(float $amount = null, string $reason = null): bool
    {
        $refund_amount = $amount ?? $this->amount;

        $details = $this->payment_details ?? [];
        $details['refund_amount'] = $refund_amount;
        $details['refund_reason'] = $reason;
        $details['refund_date'] = now()->toDateTimeString();

        $updated = $this->update([
            'status' => 'refunded',
            'refunded_at' => now(),
            'payment_details' => $details,
            'metadata' => array_merge($this->metadata ?? [], [
                'refund_amount' => $refund_amount,
                'refund_reason' => $reason,
                'original_amount' => $this->amount
            ])
        ]);

        if ($updated) {
            // Update order payment status
            $this->order->updatePaymentStatus();
        }

        return $updated;
    }

    public function canBeCompleted(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    public function canBeFailed(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    public function canBeRefunded(): bool
    {
        return $this->status === 'completed';
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending']);
    }

    public function processPayment(): bool
    {
        try {
            $this->update(['status' => 'processing']);

            // Simulate gateway processing
            $gatewayResponse = $this->simulateGatewayResponse();

            $this->update([
                'gateway_response' => $gatewayResponse,
                'transaction_id' => $gatewayResponse['transaction_id'] ?? null
            ]);

            if ($gatewayResponse['success']) {
                return $this->markAsCompleted();
            } else {
                return $this->markAsFailed($gatewayResponse['error'] ?? 'Gateway error');
            }

        } catch (\Exception $e) {
            return $this->markAsFailed($e->getMessage());
        }
    }

    private function simulateGatewayResponse(): array
    {
        // Simulate 95% success rate
        $success = rand(1, 100) <= 95;

        return [
            'success' => $success,
            'transaction_id' => $success ? 'TXN-' . uniqid() : null,
            'gateway' => 'simulation',
            'timestamp' => now()->toISOString(),
            'error' => $success ? null : 'Simulated payment failure'
        ];
    }

    public function getReceiptData(): array
    {
        return [
            'payment_number' => $this->payment_number,
            'amount' => $this->formatted_amount,
            'payment_date' => $this->payment_date->format('Y-m-d H:i'),
            'payment_method' => $this->payment_method_text,
            'status' => $this->status_text,
            'customer' => [
                'name' => $this->customer?->name,
                'email' => $this->customer?->email,
            ],
            'order' => [
                'number' => $this->order?->order_number,
                'total' => $this->order?->total_amount,
            ],
            'invoice' => [
                'number' => $this->invoice?->invoice_number,
                'total' => $this->invoice?->total_amount,
            ]
        ];
    }

    public static function getStatistics(string $period = 'month'): array
    {
        $startDate = match($period) {
            'today' => today(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth()
        };

        $endDate = now();

        return [
            'total_payments' => static::whereBetween('payment_date', [$startDate, $endDate])->count(),
            'total_amount' => static::completed()
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->sum('amount'),
            'successful_payments' => static::completed()
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->count(),
            'failed_payments' => static::failed()
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->count(),
            'pending_payments' => static::pending()
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->count(),
            'refunded_payments' => static::refunded()
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->count(),
            'success_rate' => static::getSuccessRate($startDate, $endDate),
        ];
    }

    public static function getSuccessRate(?\Carbon\Carbon $startDate = null, ?\Carbon\Carbon $endDate = null): float
    {
        $query = static::query();

        if ($startDate && $endDate) {
            $query->whereBetween('payment_date', [$startDate, $endDate]);
        }

        $total = $query->count();
        $successful = $query->where('status', 'completed')->count();

        return $total > 0 ? round(($successful / $total) * 100, 2) : 0;
    }
}
