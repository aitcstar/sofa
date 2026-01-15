<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'package_id',
        'order_number',
        'name',
        'phone',
        'country_code',
        'email',
        'units_count',
        'project_type',
        'current_stage',
        'has_interior_design',
        'needs_finishing_help',
        'needs_color_help',
        'diagrams_path',
        'colors',
        'assigned_to',
        'employee_id',
        'status',
        'delivered_at',
        'client_type',
        'commercial_register',
        'tax_number',
        'internal_notes',
        'timeline_data',
        'total_amount',
        'paid_amount',
        'payment_status',
        'payment_schedule',
        'priority',
        'expected_delivery_date',
        'custom_fields',
        'is_duplicate',
        'duplicate_of',
        'last_activity_at',
        'activity_log',
        'base_amount',
        'tax_amount',
        'total_amount'
    ];

    protected $casts = [
        'colors' => 'array',
        'delivered_at' => 'datetime',
        'timeline_data' => 'array',
        'payment_schedule' => 'array',
        'custom_fields' => 'array',
        'activity_log' => 'array',
        'expected_delivery_date' => 'datetime',
        'last_activity_at' => 'datetime',
        'is_duplicate' => 'boolean',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2'
    ];


    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function assignedEmployee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function logs()
    {
        return $this->hasMany(OrderLog::class);
    }

    public function timeline()
    {
        return $this->hasMany(OrderTimeline::class);
    }

    public function assignments()
    {
        return $this->hasMany(OrderAssignment::class);
    }

    public function activeAssignments()
    {
        return $this->hasMany(OrderAssignment::class)->where('is_active', true);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function paymentSchedules()
    {
        return $this->hasMany(PaymentSchedule::class);
    }

    public function duplicateOrders()
    {
        return $this->hasMany(Order::class, 'duplicate_of');
    }

    public function originalOrder()
    {
        return $this->belongsTo(Order::class, 'duplicate_of');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    public function scopeOverdue($query)
    {
        return $query->where('expected_delivery_date', '<', now())
                    ->whereNotIn('status', ['delivered', 'archived', 'cancelled']);
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', '>=', 4);
    }

    // Helper Methods
    public function generateOrderNumber()
    {
        return 'ORD-' . date('Y') . '-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'confirmed' => 'info',
            'processing' => 'primary',
            'shipped' => 'secondary',
            'delivered' => 'success',
            'cancelled' => 'danger',
            'archived' => 'dark',
            default => 'secondary'
        };
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending' => 'جديد',
            'confirmed' => 'مؤكد',
            'processing' => 'قيد التنفيذ',
            'shipped' => 'تم الشحن',
            'delivered' => 'تم التسليم',
            'cancelled' => 'ملغى',
            'archived' => 'مؤرشف',
            default => 'غير محدد'
        };
    }

    public function getPriorityTextAttribute()
    {
        return match($this->priority) {
            1 => 'منخفضة',
            2 => 'عادية',
            3 => 'متوسطة',
            4 => 'عالية',
            5 => 'عاجلة',
            default => 'عادية'
        };
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            1 => 'secondary',
            2 => 'info',
            3 => 'warning',
            4 => 'danger',
            5 => 'dark',
            default => 'info'
        };
    }

   /* public function getPaymentStatusTextAttribute()
    {
        return match($this->payment_status) {
            'unpaid' => 'غير مدفوع',
            'partial' => 'مدفوع جزئياً',
            'paid' => 'مدفوع بالكامل',
            'refunded' => 'مسترد',
            default => 'غير محدد'
        };
    }

    public function getPaymentStatusTextAttribute()
{
    $paid = $this->paid_amount;
    $total = $this->total_amount;

    if($paid >= $total) return 'مدفوع بالكامل';
    if($paid > 0) return 'مدفوع جزئياً';
    return 'غير مدفوع';
}*/

    public function getPaymentStatusColorAttribute()
    {
        return match($this->payment_status) {
            'unpaid' => 'danger',
            'partial' => 'warning',
            'paid' => 'success',
            'refunded' => 'info',
            default => 'secondary'
        };
    }

    public function getRemainingAmountAttribute()
    {
        return $this->total_amount - $this->paid_amount;
    }

    public function getPaymentProgressAttribute()
    {
        if (!$this->total_amount || $this->total_amount == 0) {
            return 0;
        }
        return round(($this->paid_amount / $this->total_amount) * 100, 2);
    }

    public function isOverdue()
    {
        return $this->expected_delivery_date &&
               $this->expected_delivery_date < now() &&
               !in_array($this->status, ['delivered', 'archived', 'cancelled']);
    }

    public function getDaysUntilDelivery()
    {
        if (!$this->expected_delivery_date) {
            return null;
        }
        return now()->diffInDays($this->expected_delivery_date, false);
    }

    // Timeline Methods
    public function getTimelineSteps()
    {
        return [
            'design' => [
                'name' => 'التصميم',
                'icon' => 'fas fa-pencil-ruler',
                'color' => 'primary'
            ],
            'manufacturing' => [
                'name' => 'التصنيع',
                'icon' => 'fas fa-industry',
                'color' => 'info'
            ],
            'shipping' => [
                'name' => 'الشحن',
                'icon' => 'fas fa-shipping-fast',
                'color' => 'warning'
            ],
            'first_payment' => [
                'name' => 'الدفعة الأولى',
                'icon' => 'fas fa-credit-card',
                'color' => 'success'
            ],
            'second_payment' => [
                'name' => 'الدفعة الثانية',
                'icon' => 'fas fa-money-check-alt',
                'color' => 'success'
            ],
        ];
    }

    public function updateTimeline($step, $status, $date = null, $notes = null)
    {
        $timeline = $this->timeline_data ?? [];
        $timeline[$step] = [
            'status' => $status,
            'date' => $date ?? now()->toDateTimeString(),
            'notes' => $notes
        ];
        $this->timeline_data = $timeline;
        $this->save();
    }

    public function getTimelineStatus($step)
    {
        return $this->timeline_data[$step]['status'] ?? 'pending';
    }

    // Activity Log Methods
    public function logActivity($action, $description, $user_id = null, $old_data = null, $new_data = null)
    {
        $this->logs()->create([
            'user_id' => $user_id ?? auth()->id(),
            'action' => $action,
            'description' => $description,
            'old_data' => $old_data,
            'new_data' => $new_data,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        $this->last_activity_at = now();
        $this->save();
    }

    // Duplicate Detection
    public function checkForDuplicates()
    {
        return self::where('phone', $this->phone)
                   ->where('email', $this->email)
                   ->where('package_id', $this->package_id)
                   ->where('id', '!=', $this->id)
                   ->where('created_at', '>=', now()->subDays(30))
                   ->exists();
    }

    public function markAsDuplicate($original_order_id)
    {
        $this->is_duplicate = true;
        $this->duplicate_of = $original_order_id;
        $this->save();
    }

    public function campaign()
    {
        return $this->belongsTo(MarketingCampaign::class, 'campaign_id');
    }

    protected static function boot()
{
    parent::boot();

    static::creating(function ($order) {
        // لا تعيد حساب total_amount إذا كان موجود بالفعل (من CartController)
        if (!$order->total_amount && $order->package_id) {
            $package = \App\Models\Package::find($order->package_id);

            if ($package) {
                $basePrice = $package->price;
                $taxRate = config('app.tax_rate', 0.15); // 15%
                $taxAmount = $basePrice * $taxRate;     // صح
                $finalPrice = $basePrice + $taxAmount;  // صح

                $order->total_amount = $finalPrice;     // صح
                $order->paid_amount = 0;
                $order->custom_fields = [
                    'base_price' => $basePrice,
                    'tax' => $taxAmount,
                    'final_price' => $finalPrice,
                ];

            }
        }
    });
}



public function stageStatuses()
{
    return $this->hasMany(OrderStageStatus::class);
}

public function stages()
{
    return $this->belongsToMany(OrderStage::class, 'order_stage_statuses')
                ->withPivot('status', 'completed_at')
                ->withTimestamps();
}






public function getPaymentScheduleAttribute()
{
    return $this->payments()->orderBy('payment_date')->get()->map(function($payment){
        return [
            'amount' => $payment->amount,
            'due_date' => $payment->payment_date?->format('Y-m-d'),
            'status' => $payment->status,
        ];
    })->toArray();
}



// مجموع المبالغ المدفوعة
public function getPaidAmountAttribute()
{
    return $this->paymentSchedules()->where('status', 'paid')->sum('amount');
}



// حالة الدفع بناءً على الدفعات المدفوعة
public function getPaymentStatusTextAttribute()
{
    if ($this->paid_amount == 0) {
        return 'غير مدفوعة';
    } elseif ($this->paid_amount >= $this->total_amount) {
        return 'مدفوعة';
    } else {
        return 'جزئي';
    }
}

public function package()
{
    return $this->belongsTo(Package::class);
}

public function orderItems()
{
    return $this->hasMany(OrderItem::class);
}

public function packages()
{
    return $this->hasManyThrough(
        Package::class,       // النموذج النهائي
        OrderItem::class,     // النموذج الوسيط
        'order_id',           // مفتاح order_items يشير إلى order
        'id',                 // مفتاح packages الرئيسي
        'id',                 // مفتاح order الرئيسي
        'package_id'          // مفتاح order_items يشير إلى package
    );
}

public function getSubtotalAttribute()
{
    // اجمع سعر كل القطع أو الباكج
    if ($this->package) {
        $basePrice = $this->package->price ?? 0;
        return $basePrice;
    }

    return 0;
}


}
