<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'customer_id',
        'user_id',
        'invoice_number',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'base_amount',
        'status',
        'payment_status',
        'issue_date',
        'due_date',
        'sent_at',
        'paid_at',
        'cancelled_at',
        'invoice_data',
        'notes',
        'terms_conditions',
        'created_by',
        'metadata',
        'quote_id',
        'paid_amount'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'issue_date' => 'date',
        'due_date' => 'date',
        'sent_at' => 'datetime',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'invoice_data' => 'array',
        'metadata' => 'array'
    ];

    // العلاقات
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function package()
    {
        return $this->hasOneThrough(
            Package::class,
            Order::class,
            'id',          // Foreign key on orders table
            'id',          // Foreign key on packages table
            'order_id',    // Local key on invoices table
            'package_id'   // Local key on orders table
        );
    }


    // Helper Methods


    public function quote()
{
    return $this->belongsTo(Quote::class);
}


    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'draft' => 'secondary',
            'sent' => 'info',
            'paid' => 'success',
            'overdue' => 'danger',
            'cancelled' => 'dark',
            default => 'secondary'
        };
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'draft' => 'مسودة',
            'sent' => 'مرسلة',
            'paid' => 'مدفوعة',
            'overdue' => 'متأخرة',
            'cancelled' => 'ملغاة',
            default => 'غير محدد'
        };
    }

    public function isOverdue()
    {
        return $this->status === 'sent' && $this->due_date < now()->toDateString();
    }

    public function getDaysUntilDue()
    {
        return now()->diffInDays($this->due_date, false);
    }

    public function calculateTax()
    {
        $this->tax_amount = ($this->subtotal * $this->tax_rate) / 100;
        $this->total_amount = $this->subtotal + $this->tax_amount;
        return $this;
    }

    public function markAsPaid()
    {
        $this->status = 'paid';
        $this->paid_at = now();
        $this->save();
    }

    public function markAsOverdue()
    {
        if ($this->status === 'sent' && $this->due_date < now()->toDateString()) {
            $this->status = 'overdue';
            $this->save();
        }
    }


    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }


    public function assignedEmployee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('payment_status', 'pending')
                    ->where('due_date', '<', now());
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    // Enhanced Accessors
    public function getPaymentStatusTextAttribute(): string
    {
        return match($this->payment_status ?? 'pending') {
            'pending' => 'في الانتظار',
            'partial' => 'دفع جزئي',
            'paid' => 'مدفوعة',
            'refunded' => 'مسترد',
            'cancelled' => 'ملغية',
            default => 'غير محدد'
        };
    }

    public function getPaymentStatusColorAttribute(): string
    {
        return match($this->payment_status ?? 'pending') {
            'pending' => 'warning',
            'partial' => 'info',
            'paid' => 'success',
            'refunded' => 'secondary',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    public function getRemainingAmountAttribute(): float
    {
        $paidAmount = $this->payments()->where('status', 'completed')->sum('amount');
        return max(0, $this->total_amount - $paidAmount);
    }

    public function getItemsAttribute()
    {
        if ($this->quote) {
            // الفاتورة مرتبطة بعرض سعر
            return $this->quote->items->map(function($item) {
                return [
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total' => $item->total_price,
                ];
            });
        }

        if ($this->order) {
            // الفاتورة مرتبطة بطلب فقط
            $items = collect();

            if ($this->order->package) {
                $items->push([
                    'description' => $this->order->package->name,
                    'quantity' => $this->order->units_count ?? 1,
                    'unit_price' => $this->subtotal,
                    'total' => $this->subtotal,
                ]);
            }

            return $items;
        }

        return collect();
    }


    public function items()
{
    return $this->hasMany(InvoiceItem::class);
}



    public function getPaidAmountAttribute(): float
    {
        return $this->payments()->where('status', 'completed')->sum('amount');
    }

    public function getIsOverdueAttribute(): bool
    {
        return ($this->payment_status === 'pending' || $this->payment_status === 'partial')
               && $this->due_date < now();
    }

    public function getDaysOverdueAttribute(): int
    {
        if (!$this->is_overdue) {
            return 0;
        }
        return $this->due_date->diffInDays(now());
    }

    // Enhanced Methods
    public static function generateInvoiceNumber(): string
    {
        $year = now()->year;
        $month = now()->format('m');

        $lastInvoice = static::whereYear('created_at', $year)
            ->whereMonth('created_at', now()->month)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastInvoice ?
            (int) substr($lastInvoice->invoice_number, -4) + 1 : 1;

        return "INV-{$year}{$month}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public static function createFromOrder(Order $order, ?User $createdBy = null): self
    {
        $invoice = static::create([
            'invoice_number' => static::generateInvoiceNumber(),
            'order_id' => $order->id,
            'customer_id' => $order->user_id,
            'issue_date' => now(),
            'due_date' => now()->addDays(30),
            'subtotal' => $order->subtotal ?? $order->total_amount,
            'tax_rate' => 15, // Default VAT rate
            'tax_amount' => $order->tax_amount ?? 0,
            'discount_amount' => $order->discount_amount ?? 0,
            'total_amount' => $order->total_amount,
            'status' => 'draft',
            'payment_status' => 'pending',
            'created_by' => $createdBy?->id ?? auth()->id(),
            'terms_conditions' => config('invoice.default_terms', 'الدفع خلال 30 يوم من تاريخ الفاتورة'),
            'metadata' => [
                'order_number' => $order->order_number,
                'package_name' => $order->package?->name,
                'units_count' => $order->units_count,
                'colors' => $order->colors,
            ]
        ]);

        // Calculate tax if not set
        if (!$invoice->tax_amount) {
            $invoice->calculateTax();
        }

        return $invoice;
    }

    public function markAsSent(): bool
    {
        return $this->update([
            'status' => 'sent',
            'sent_at' => now()
        ]);
    }

    public function markAsCancelled(): bool
    {
        return $this->update([
            'status' => 'cancelled',
            'payment_status' => 'cancelled',
            'cancelled_at' => now()
        ]);
    }

    public function updatePaymentStatus(): bool
    {
        $paidAmount = $this->paid_amount;
        $totalAmount = $this->total_amount;

        if ($paidAmount >= $totalAmount) {
            $status = 'paid';
            $this->paid_at = now();
        } elseif ($paidAmount > 0) {
            $status = 'partial';
        } else {
            $status = 'pending';
        }

        return $this->update(['payment_status' => $status]);
    }

    public function canBeEdited(): bool
    {
        return in_array($this->status, ['draft']);
    }

    public function canBeSent(): bool
    {
        return in_array($this->status, ['draft']);
    }

    public function canBePaid(): bool
    {
        return in_array($this->payment_status, ['pending', 'partial']);
    }

    public function canBeCancelled(): bool
    {
        return !in_array($this->status, ['paid', 'cancelled']);
    }

    public function applyDiscount(float $amount, string $type = 'amount'): bool
    {
        if ($type === 'percentage') {
            $discountAmount = round(($this->subtotal * $amount) / 100, 2);
        } else {
            $discountAmount = $amount;
        }

        $newTotal = $this->subtotal + $this->tax_amount - $discountAmount;

        return $this->update([
            'discount_amount' => $discountAmount,
            'total_amount' => max(0, $newTotal)
        ]);
    }
}
