<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_number',
        'lead_id',
        'user_id',
        'customer_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_company',
        'title',
        'description',
        'status',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'currency',
        'issue_date',
        'valid_until',
        'terms_conditions',
        'notes',
        'sent_at',
        'viewed_at',
        'accepted_at',
        'rejected_at',
        'converted_to_order_at',
        'created_by',
        'metadata'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'issue_date' => 'date',
        'valid_until' => 'date',
        'sent_at' => 'datetime',
        'viewed_at' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
        'converted_to_order_at' => 'datetime',
        'metadata' => 'array'
    ];

    // العلاقات
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'sent')
                    ->where('valid_until', '<', now()->toDateString());
    }

    public function scopeValid($query)
    {
        return $query->where('valid_until', '>=', now()->toDateString());
    }

    // Accessors
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'draft' => 'مسودة',
            'sent' => 'مرسل',
            'viewed' => 'تم العرض',
            'accepted' => 'مقبول',
            'rejected' => 'مرفوض',
            'expired' => 'منتهي الصلاحية',
            default => $this->status
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'draft' => 'gray',
            'sent' => 'blue',
            'viewed' => 'yellow',
            'accepted' => 'green',
            'rejected' => 'red',
            'expired' => 'orange',
            default => 'gray'
        };
    }

    /*public function getIsExpiredAttribute()
    {
        return $this->valid_until->isPast() && $this->status === 'sent';
    }*/

    /*public function getDaysUntilExpiryAttribute()
    {
        if ($this->status !== 'sent') {
            return null;
        }

        return now()->diffInDays($this->valid_until, false);
    }*/

    public function getConversionRateAttribute()
    {
        // حساب معدل التحويل (إذا كان هناك طلب مرتبط)
        return $this->status === 'accepted' ? 100 : 0;
    }

    // Methods
    /*public function calculateTotals()
    {
        $this->subtotal = $this->items->sum('total_price');
        $this->tax_amount = ($this->subtotal - $this->discount_amount) * ($this->tax_rate / 100);
        $this->total_amount = $this->subtotal - $this->discount_amount + $this->tax_amount;
        $this->save();
    }

    public function send()
    {
        if ($this->status !== 'draft') {
            throw new \Exception('لا يمكن إرسال عرض سعر غير في حالة مسودة');
        }

        $this->status = 'sent';
        $this->sent_at = now();
        $this->save();

        // تسجيل النشاط في العميل المحتمل
        if ($this->lead) {
            $this->lead->logActivity('quote_sent', "تم إرسال عرض سعر رقم {$this->quote_number}");
        }

        // إرسال بريد إلكتروني للعميل
        $this->sendEmailToCustomer();
    }
*/
    public function markAsViewed()
    {
        if ($this->status === 'sent') {
            $this->status = 'viewed';
            $this->viewed_at = now();
            $this->save();
        }
    }

    /*public function accept()
    {
        if (!in_array($this->status, ['sent', 'viewed'])) {
            throw new \Exception('لا يمكن قبول عرض السعر في هذه الحالة');
        }

        $this->status = 'accepted';
        $this->accepted_at = now();
        $this->save();

        // تسجيل النشاط في العميل المحتمل
        if ($this->lead) {
            $this->lead->logActivity('quote_accepted', "تم قبول عرض سعر رقم {$this->quote_number}");
            $this->lead->updateStatus('won');
        }

        // إنشاء طلب من عرض السعر
        $this->convertToOrder();
    }

    public function reject($reason = null)
    {
        if (!in_array($this->status, ['sent', 'viewed'])) {
            throw new \Exception('لا يمكن رفض عرض السعر في هذه الحالة');
        }

        $this->status = 'rejected';
        $this->rejected_at = now();

        if ($reason) {
            $this->notes = ($this->notes ? $this->notes . "\n\n" : '') . "سبب الرفض: " . $reason;
        }

        $this->save();

        // تسجيل النشاط في العميل المحتمل
        if ($this->lead) {
            $this->lead->logActivity('quote_rejected', "تم رفض عرض سعر رقم {$this->quote_number}" . ($reason ? " - السبب: {$reason}" : ""));
        }
    }
*/
    public function markAsExpired()
    {
        if ($this->status === 'sent' && $this->is_expired) {
            $this->status = 'expired';
            $this->save();

            // تسجيل النشاط في العميل المحتمل
            if ($this->lead) {
                $this->lead->logActivity('quote_expired', "انتهت صلاحية عرض سعر رقم {$this->quote_number}");
            }
        }
    }

    public function duplicate()
    {
        $newQuote = $this->replicate();
        $newQuote->quote_number = static::generateQuoteNumber();
        $newQuote->status = 'draft';
        $newQuote->sent_at = null;
        $newQuote->viewed_at = null;
        $newQuote->accepted_at = null;
        $newQuote->rejected_at = null;
        $newQuote->issue_date = now()->toDateString();
        $newQuote->valid_until = now()->addDays(30)->toDateString();
        $newQuote->save();

        // نسخ العناصر
        foreach ($this->items as $item) {
            $newItem = $item->replicate();
            $newItem->quote_id = $newQuote->id;
            $newItem->save();
        }

        $newQuote->calculateTotals();

        return $newQuote;
    }
/*
    public function convertToOrder()
    {
        // إنشاء عميل إذا لم يكن موجوداً
        $customer = $this->customer;
        if (!$customer && $this->lead) {
            $customer = $this->lead->convertToCustomer();
        } elseif (!$customer) {
            $customer = User::create([
                'name' => $this->customer_name,
                'email' => $this->customer_email,
                'phone' => $this->customer_phone,
                'role' => 'customer',
                'password' => bcrypt('temporary_password'),
                'email_verified_at' => now()
            ]);
        }

        // إنشاء الطلب
        $order = Order::create([
            'user_id' => $customer->id,
            'lead_id' => $this->lead_id,
            'quote_id' => $this->id,
            'order_number' => Order::generateOrderNumber(),
            'name' => $this->customer_name,
            'email' => $this->customer_email,
            'phone' => $this->customer_phone,
            'status' => 'pending',
            'total_amount' => $this->total_amount,
            'payment_status' => 'unpaid'
        ]);

        return $order;
    }
*/
    private function sendEmailToCustomer()
    {
        // إرسال بريد إلكتروني للعميل مع عرض السعر
        // يمكن تنفيذ هذا لاحقاً
    }

    // Static Methods
    public static function generateQuoteNumber()
    {
        $year = date('Y');
        $lastQuote = static::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $nextNumber = $lastQuote ? ($lastQuote->id + 1) : 1;

        return 'QUO-' . $year . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public static function getStatusOptions()
    {
        return [
            'draft' => 'مسودة',
            'sent' => 'مرسل',
            'viewed' => 'تم العرض',
            'accepted' => 'مقبول',
            'rejected' => 'مرفوض',
            'expired' => 'منتهي الصلاحية'
        ];
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($quote) {
            if (!$quote->quote_number) {
                $quote->quote_number = static::generateQuoteNumber();
            }
        });

        static::deleting(function ($quote) {
            // حذف العناصر المرتبطة
            $quote->items()->delete();
        });
    }


    // Enhanced Accessors
    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total_amount, 2) . ' ' . ($this->currency ?? 'SAR');
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->valid_until && $this->valid_until < now() &&
               !in_array($this->status, ['accepted', 'rejected', 'converted']);
    }

    public function getDaysUntilExpiryAttribute(): int
    {
        if (!$this->valid_until || $this->is_expired) {
            return 0;
        }
        return now()->diffInDays($this->valid_until);
    }

    // Enhanced Methods
    public static function createFromLead(Lead $lead, array $quoteData = []): self
    {
        $quote = static::create(array_merge([
            'quote_number' => static::generateQuoteNumber(),
            'lead_id' => $lead->id,
            'customer_id' => $lead->converted_customer_id,
            'customer_name' => $lead->name,
            'customer_email' => $lead->email,
            'customer_phone' => $lead->phone,
            'customer_company' => $lead->company,
            'title' => "عرض سعر لمشروع {$lead->name}",
            'description' => $lead->description,
            'currency' => 'SAR',
            'tax_rate' => 15, // Default VAT rate
            'status' => 'draft',
            'issue_date' => now(),
            'valid_until' => now()->addDays(30),
            'created_by' => auth()->id(),
            'terms_conditions' => config('quote.default_terms', 'العرض صالح لمدة 30 يوم من تاريخ الإصدار'),
            'metadata' => [
                'lead_source' => $lead->source,
                'project_type' => $lead->project_type,
                'units_count' => $lead->units_count,
                'budget_range' => $lead->budget_range,
            ]
        ], $quoteData));

        // Add default items based on lead information
        if ($lead->project_type && $lead->units_count) {
            $quote->addDefaultItems($lead);
        }

        return $quote;
    }

    public function addDefaultItems(Lead $lead): void
    {
        $basePrice = $this->getBasePriceForProjectType($lead->project_type);
        $unitsCount = $lead->units_count ?? 1;

        $this->items()->create([
            'description' => "تصميم وتنفيذ مشروع {$lead->project_type_text}",
            'quantity' => $unitsCount,
            'unit_price' => $basePrice,
            'total_price' => $basePrice * $unitsCount,
            'metadata' => [
                'project_type' => $lead->project_type,
                'auto_generated' => true
            ]
        ]);

        $this->calculateTotals();
    }

    private function getBasePriceForProjectType(string $projectType): float
    {
        return match($projectType) {
            'compound' => 150000,
            'building' => 100000,
            'hotel_apartments' => 120000,
            'villa' => 80000,
            'commercial' => 90000,
            default => 50000
        };
    }

    public function calculateTotals()
    {
        $this->subtotal = $this->items->sum('total_price');
        $this->tax_amount = round(($this->subtotal * $this->tax_rate) / 100, 2);
        $this->total_amount = $this->subtotal + $this->tax_amount - ($this->discount_amount ?? 0);
        $this->save();
    }

    public function send()
    {
        if ($this->status !== 'draft') {
            throw new \Exception('لا يمكن إرسال عرض سعر غير في حالة مسودة');
        }

        $this->status = 'sent';
        $this->sent_at = now();
        $this->save();

        // Log activity in lead
        if ($this->lead) {
            $this->lead->logActivity('quote_sent', "تم إرسال عرض سعر رقم {$this->quote_number}");
        }

        // Send email to customer
        $this->sendEmailToCustomer();
    }

    public function accept()
    {
        if (!in_array($this->status, ['sent', 'viewed'])) {
            throw new \Exception('لا يمكن قبول عرض السعر في هذه الحالة');
        }

        $this->status = 'accepted';
        $this->accepted_at = now();
        $this->save();

        // Log activity in lead
        if ($this->lead) {
            $this->lead->logActivity('quote_accepted', "تم قبول عرض سعر رقم {$this->quote_number}");
            $this->lead->updateStatus('won');
        }
    }

    public function reject($reason = null)
    {
        if (!in_array($this->status, ['sent', 'viewed'])) {
            throw new \Exception('لا يمكن رفض عرض السعر في هذه الحالة');
        }

        $this->status = 'rejected';
        $this->rejected_at = now();

        if ($reason) {
            $metadata = $this->metadata ?? [];
            $metadata['rejection_reason'] = $reason;
            $this->metadata = $metadata;
        }

        $this->save();

        // Log activity in lead
        if ($this->lead) {
            $this->lead->logActivity('quote_rejected', "تم رفض عرض سعر رقم {$this->quote_number}" . ($reason ? " - السبب: {$reason}" : ""));
        }
    }

    public function convertToOrder($orderData = [])
    {
        if ($this->status !== 'accepted') {
            throw new \Exception('لا يمكن تحويل عرض السعر إلى طلب إلا إذا كان مقبولاً');
        }

        try {
            \DB::beginTransaction();

            // Ensure customer exists
            $customer = $this->customer;
            if (!$customer && $this->lead) {
                $customer = $this->lead->convertToCustomer();
            } elseif (!$customer) {
                $customer = User::create([
                    'name' => $this->customer_name,
                    'email' => $this->customer_email,
                    'phone' => $this->customer_phone,
                    'role' => 'customer',
                    'company' => $this->customer_company,
                    'password' => bcrypt('temporary_password'),
                    'email_verified_at' => now()
                ]);
            }

            $order = Order::create(array_merge([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $customer->id,
                'quote_id' => $this->id,
                'lead_id' => $this->lead_id,
                'name' => $this->customer_name,
                'email' => $this->customer_email,
                'phone' => $this->customer_phone,
                'company' => $this->customer_company,
                'project_type' => $this->metadata['project_type'] ?? null,
                'units_count' => $this->metadata['units_count'] ?? 1,
                'subtotal' => $this->subtotal,
                'tax_amount' => $this->tax_amount,
                'discount_amount' => $this->discount_amount,
                'total_amount' => $this->total_amount,
                'status' => 'new',
                'payment_status' => 'pending',
                'description' => $this->description,
                'notes' => $this->notes,
                'metadata' => array_merge($this->metadata ?? [], [
                    'converted_from_quote' => $this->id,
                    'quote_number' => $this->quote_number,
                ])
            ], $orderData));

            // Update quote status
            $this->update([
                'status' => 'converted',
                'converted_to_order_at' => now()
            ]);

            \DB::commit();
            return $order;

        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }

    public function applyDiscount(float $amount, string $type = 'amount'): bool
    {
        if ($type === 'percentage') {
            $discountAmount = round(($this->subtotal * $amount) / 100, 2);
        } else {
            $discountAmount = $amount;
        }

        $this->discount_amount = $discountAmount;
        $this->calculateTotals();
        return true;
    }

    public function extendValidity(int $days): bool
    {
        return $this->update([
            'valid_until' => $this->valid_until->addDays($days)
        ]);
    }

    public function canBeEdited(): bool
    {
        return in_array($this->status, ['draft']);
    }

    public function canBeSent(): bool
    {
        return in_array($this->status, ['draft']) && $this->items()->count() > 0;
    }

    public function canBeAccepted(): bool
    {
        return in_array($this->status, ['sent', 'viewed']) && !$this->is_expired;
    }

    public function canBeRejected(): bool
    {
        return in_array($this->status, ['sent', 'viewed']) && !$this->is_expired;
    }

    public function canBeConverted(): bool
    {
        return $this->status === 'accepted';
    }

    // Enhanced Static Methods
    public static function getStatistics($period = 'month'): array
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
            'total_quotes' => static::whereBetween('created_at', [$startDate, $endDate])->count(),
            'sent_quotes' => static::sent()->whereBetween('sent_at', [$startDate, $endDate])->count(),
            'accepted_quotes' => static::accepted()->whereBetween('accepted_at', [$startDate, $endDate])->count(),
            'rejected_quotes' => static::rejected()->whereBetween('rejected_at', [$startDate, $endDate])->count(),
            'converted_quotes' => static::where('status', 'converted')->whereBetween('converted_to_order_at', [$startDate, $endDate])->count(),
            'expired_quotes' => static::expired()->count(),
            'total_value' => static::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount'),
            'accepted_value' => static::accepted()->whereBetween('accepted_at', [$startDate, $endDate])->sum('total_amount'),
            'acceptance_rate' => static::getAcceptanceRate($startDate, $endDate),
            'conversion_rate' => static::getConversionRate($startDate, $endDate),
        ];
    }

    public static function getAcceptanceRate($startDate = null, $endDate = null): float
    {
        $query = static::query();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $total = $query->whereIn('status', ['sent', 'viewed', 'accepted', 'rejected'])->count();
        $accepted = $query->where('status', 'accepted')->count();

        return $total > 0 ? round(($accepted / $total) * 100, 2) : 0;
    }

    public static function getConversionRate($startDate = null, $endDate = null): float
    {
        $query = static::query();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $accepted = $query->where('status', 'accepted')->count();
        $converted = $query->where('status', 'converted')->count();

        return $accepted > 0 ? round(($converted / $accepted) * 100, 2) : 0;
    }

    public static function updateExpiredQuotes(): int
    {
        return static::where('valid_until', '<', now())
            ->whereNotIn('status', ['accepted', 'rejected', 'expired', 'converted'])
            ->update(['status' => 'expired']);
    }
}
