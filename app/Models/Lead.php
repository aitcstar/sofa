<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'position',
        'source',
        'status',
        'priority',
        'project_type',
        'units_count',
        'budget_range',
        'estimated_value',
        'expected_close_date',
        'expected_start_date',
        'description',
        'notes',
        'custom_fields',
        'assigned_to',
        'converted_to_customer_at',
        'converted_customer_id',
        'last_contact_at',
        'next_follow_up_at',
        'converted_to_order_at',
        'lead_score',
        'metadata',
        'tags'
    ];

    protected $casts = [
        'custom_fields' => 'array',
        'metadata' => 'array',
        'tags' => 'array',
        'estimated_value' => 'decimal:2',
        'expected_close_date' => 'date',
        'expected_start_date' => 'date',
        'converted_to_customer_at' => 'datetime',
        'last_contact_at' => 'datetime',
        'next_follow_up_at' => 'datetime',
        'converted_to_order_at' => 'datetime'


    ];

    // العلاقات
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // Scopes
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeContacted($query)
    {
        return $query->where('status', 'contacted');
    }

    public function scopeQualified($query)
    {
        return $query->where('status', 'qualified');
    }

    public function scopeWon($query)
    {
        return $query->where('status', 'won');
    }

    public function scopeLost($query)
    {
        return $query->where('status', 'lost');
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    public function scopeUrgent($query)
    {
        return $query->where('priority', 'urgent');
    }

    public function scopeOverdue($query)
    {
        return $query->where('next_follow_up_at', '<', now())
                    ->whereNotIn('status', ['won', 'lost']);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeBySource($query, $source)
    {
        return $query->where('source', $source);
    }

    // Accessors
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'new' => 'جديد',
            'contacted' => 'تم التواصل',
            'qualified' => 'مؤهل',
            'proposal_sent' => 'تم إرسال العرض',
            'negotiation' => 'تفاوض',
            'won' => 'تم الإغلاق',
            'lost' => 'مفقود',
            default => $this->status
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'new' => 'blue',
            'contacted' => 'yellow',
            'qualified' => 'green',
            'proposal_sent' => 'purple',
            'negotiation' => 'orange',
            'won' => 'green',
            'lost' => 'red',
            default => 'gray'
        };
    }

    public function getPriorityTextAttribute()
    {
        return match($this->priority) {
            'low' => 'منخفضة',
            'medium' => 'متوسطة',
            'high' => 'عالية',
            'urgent' => 'عاجلة',
            default => $this->priority
        };
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'low' => 'gray',
            'medium' => 'blue',
            'high' => 'orange',
            'urgent' => 'red',
            default => 'gray'
        };
    }

    public function getSourceTextAttribute()
    {
        return match($this->source) {
            'website' => 'الموقع الإلكتروني',
            'phone' => 'مكالمة هاتفية',
            'referral' => 'إحالة',
            'social_media' => 'وسائل التواصل',
            'advertisement' => 'إعلان',
            default => $this->source
        };
    }

    public function getDaysOverdueAttribute()
    {
        if (!$this->next_follow_up_at || in_array($this->status, ['won', 'lost'])) {
            return 0;
        }

        return max(0, now()->diffInDays($this->next_follow_up_at, false));
    }

    public function getIsOverdueAttribute()
    {
        return $this->days_overdue > 0;
    }

    public function getConversionRateAttribute()
    {
        // حساب معدل التحويل للعميل المحتمل
        $totalActivities = $this->activities()->count();
        $completedActivities = $this->activities()->where('status', 'completed')->count();

        return $totalActivities > 0 ? ($completedActivities / $totalActivities) * 100 : 0;
    }

    // Methods
    /*public function updateStatus($newStatus, $userId = null)
    {
        $oldStatus = $this->status;
        $this->status = $newStatus;
        $this->save();

        // تسجيل النشاط
        $this->logActivity('status_changed', "تم تغيير الحالة من {$oldStatus} إلى {$newStatus}", $userId);

        // إذا تم إغلاق العميل المحتمل، قم بتحديث التاريخ
        if ($newStatus === 'won') {
            $this->expected_close_date = now()->toDateString();
            $this->save();
        }
    }*/

    public function assignTo($userId)
    {
        $oldAssignee = $this->assigned_to;
        $this->assigned_to = $userId;
        $this->save();

        // تسجيل النشاط
        $newAssignee = User::find($userId);
        $this->logActivity('assigned', "تم تعيين العميل المحتمل إلى {$newAssignee->name}", $userId);
    }

    /*public function scheduleFollowUp($date, $userId = null)
    {
        $this->next_follow_up_at = $date;
        $this->save();

        // تسجيل النشاط
        $this->logActivity('follow_up_scheduled', "تم جدولة متابعة في {$date->format('Y-m-d H:i')}", $userId);
    }*/

    public function logActivity($type, $description, $userId = null)
    {
        return $this->activities()->create([
            'user_id' => $userId ?: auth()->id(),
            'type' => $type,
            'subject' => $description,
            'description' => $description,
            'completed_at' => now()
        ]);
    }

    /*public function convertToCustomer()
    {
        // تحويل العميل المحتمل إلى عميل
        $customer = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => 'customer',
            'password' => bcrypt('temporary_password'),
            'email_verified_at' => now()
        ]);

        // تحديث حالة العميل المحتمل
        $this->updateStatus('won');

        // تسجيل النشاط
        $this->logActivity('converted_to_customer', "تم تحويل العميل المحتمل إلى عميل", auth()->id());

        return $customer;
    }*/

    public function convertToOrder($orderData = [])
    {
        // تحويل العميل المحتمل إلى طلب
        $customer = $this->convertToCustomer();

        $order = Order::create(array_merge([
            'user_id' => $customer->id,
            'lead_id' => $this->id,
            'order_number' => Order::generateOrderNumber(),
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => 'pending',
            'total_amount' => $this->estimated_value ?? 0
        ], $orderData));

        // تسجيل النشاط
        $this->logActivity('converted_to_order', "تم تحويل العميل المحتمل إلى طلب رقم {$order->order_number}", auth()->id());

        return $order;
    }

    public function getLastActivity()
    {
        return $this->activities()->latest()->first();
    }

    public function getNextActivity()
    {
        return $this->activities()
                   ->where('status', 'pending')
                   ->where('scheduled_at', '>', now())
                   ->orderBy('scheduled_at')
                   ->first();
    }

    public function updateLastContact()
    {
        $this->last_contact_at = now();
        $this->save();
    }

    // Static Methods
    public static function getStatusOptions()
    {
        return [
            'new' => 'جديد',
            'contacted' => 'تم التواصل',
            'interested' => 'مهتم',
            'not_interested' => 'غير مهتم',
            'converted' => 'تم التحويل',
        ];
    }

    public static function getPriorityOptions()
    {
        return [
            'low' => 'منخفضة',
            'medium' => 'متوسطة',
            'high' => 'عالية',
            'urgent' => 'عاجلة'
        ];
    }

    public static function getSourceOptions()
    {
        return [
            'website' => 'الموقع الإلكتروني',
            'phone' => 'مكالمة هاتفية',
            'referral' => 'إحالة',
            'social_media' => 'وسائل التواصل',
            'advertisement' => 'إعلان'
        ];
    }


    public function customer()
    {
        return $this->belongsTo(User::class, 'converted_customer_id');
    }

    // Enhanced Scopes
    public function scopeProposal($query)
    {
        return $query->where('status', 'proposal');
    }

    public function scopeNegotiation($query)
    {
        return $query->where('status', 'negotiation');
    }

    public function scopeConverted($query)
    {
        return $query->where('status', 'converted');
    }

    public function scopeHot($query)
    {
        return $query->where('priority', 'hot');
    }

    public function scopeWarm($query)
    {
        return $query->where('priority', 'warm');
    }

    public function scopeCold($query)
    {
        return $query->where('priority', 'cold');
    }

    public function scopeRequiresFollowUp($query)
    {
        return $query->where('next_follow_up_at', '<=', now())
                    ->whereNotIn('status', ['converted', 'lost']);
    }

    // Enhanced Accessors
    public function getProjectTypeTextAttribute(): string
    {
        return match($this->project_type) {
            'building' => 'عمارة',
            'compound' => 'كمبوند',
            'hotel_apartments' => 'شقق فندقية',
            'villa' => 'فيلا',
            'commercial' => 'تجاري',
            'other' => 'أخرى',
            default => 'غير محدد'
        };
    }

    public function getBudgetRangeTextAttribute(): string
    {
        return match($this->budget_range) {
            'under_50k' => 'أقل من 50,000 ريال',
            '50k_100k' => '50,000 - 100,000 ريال',
            '100k_250k' => '100,000 - 250,000 ريال',
            '250k_500k' => '250,000 - 500,000 ريال',
            '500k_1m' => '500,000 - 1,000,000 ريال',
            'over_1m' => 'أكثر من 1,000,000 ريال',
            default => 'غير محدد'
        };
    }

    public function getLeadAgeAttribute(): int
    {
        return $this->created_at->diffInDays(now());
    }

    // Enhanced Methods
    public static function generateLeadNumber(): string
    {
        $year = now()->year;
        $month = now()->format('m');

        $lastLead = static::whereYear('created_at', $year)
            ->whereMonth('created_at', now()->month)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastLead ?
            (int) substr($lastLead->lead_number ?? '', -4) + 1 : 1;

        return "LEAD-{$year}{$month}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function updateStatus($newStatus, $userId = null)
    {
        $oldStatus = $this->status;
        $this->status = $newStatus;

        // Update conversion date if converted
        if ($newStatus === 'converted') {
            $this->converted_to_customer_at = now();
        }

        $this->save();

        // Log activity
        $this->logActivity('status_changed', "تم تغيير الحالة من {$oldStatus} إلى {$newStatus}", $userId);

        // Update lead score
        $this->updateLeadScore();
    }

    public function convertToCustomer($customerData = [])
    {
        try {
            \DB::beginTransaction();

            // Create customer if doesn't exist
            $customer = User::where('email', $this->email)->first();

            if (!$customer) {
                $customer = User::create(array_merge([
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'role' => 'customer',
                    'company' => $this->company,
                    'position' => $this->position,
                    'is_active' => true,
                    'password' => bcrypt('temporary_password'),
                    'email_verified_at' => now(),
                    'metadata' => [
                        'converted_from_lead' => $this->id,
                        'lead_source' => $this->source,
                        'project_type' => $this->project_type,
                        'budget_range' => $this->budget_range,
                    ]
                ], $customerData));
            }

            // Update lead status
            $this->update([
                'status' => 'converted',
                'converted_to_customer_at' => now(),
                'converted_customer_id' => $customer->id
            ]);

            // Log the conversion
            $this->logActivity('converted_to_customer', "تم تحويل العميل المحتمل إلى عميل", auth()->id());

            \DB::commit();
            return $customer;

        } catch (\Exception $e) {
            \DB::rollBack();
            return null;
        }
    }

    public function markAsLost($reason, $userId = null)
    {
        $this->updateStatus('lost', $userId);
        $this->logActivity('lost', "تم تحديد العميل المحتمل كمفقود: {$reason}", $userId);
    }

    public function updateLeadScore(): void
    {
        $score = 0;

        // Score based on status
        $score += match($this->status) {
            'new' => 10,
            'contacted' => 20,
            'qualified' => 40,
            'proposal_sent' => 60,
            'negotiation' => 80,
            default => 0
        };

        // Score based on priority
        $score += match($this->priority) {
            'urgent' => 30,
            'high' => 25,
            'medium' => 15,
            'low' => 5,
            default => 0
        };

        // Score based on budget
        if ($this->budget_range) {
            $score += match($this->budget_range) {
                'over_1m' => 30,
                '500k_1m' => 25,
                '250k_500k' => 20,
                '100k_250k' => 15,
                '50k_100k' => 10,
                'under_50k' => 5,
                default => 0
            };
        }

        // Score based on project type
        if ($this->project_type) {
            $score += match($this->project_type) {
                'compound' => 25,
                'building' => 20,
                'hotel_apartments' => 20,
                'villa' => 15,
                'commercial' => 15,
                default => 10
            };
        }

        // Deduct points for age
        $ageInDays = $this->lead_age;
        if ($ageInDays > 30) {
            $score -= min(20, ($ageInDays - 30) * 0.5);
        }

        // Ensure score is between 0 and 100
        $score = max(0, min(100, $score));

        $this->update(['lead_score' => $score]);
    }

    public function addNote($note, $userId = null)
    {
        return $this->logActivity('note', $note, $userId);
    }

    public function scheduleFollowUp($date, $userId = null)
    {
        $this->next_follow_up_at = $date;
        $this->save();

        // Log activity
        $this->logActivity('follow_up_scheduled', "تم جدولة متابعة في {$date->format('Y-m-d H:i')}", $userId);
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
            'total_leads' => static::whereBetween('created_at', [$startDate, $endDate])->count(),
            'new_leads' => static::new()->whereBetween('created_at', [$startDate, $endDate])->count(),
            'qualified_leads' => static::qualified()->whereBetween('created_at', [$startDate, $endDate])->count(),
            'converted_leads' => static::where('status', 'converted')->whereBetween('converted_to_customer_at', [$startDate, $endDate])->count(),
            'lost_leads' => static::lost()->whereBetween('updated_at', [$startDate, $endDate])->count(),
            'hot_leads' => static::where('priority', 'urgent')->whereNotIn('status', ['converted', 'lost'])->count(),
            'overdue_leads' => static::overdue()->count(),
            'conversion_rate' => static::getConversionRate($startDate, $endDate),
            'average_lead_score' => static::whereNotIn('status', ['converted', 'lost'])->avg('lead_score') ?? 0,
        ];
    }

    public static function getConversionRate($startDate = null, $endDate = null): float
    {
        $query = static::query();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $total = $query->count();
        $converted = $query->where('status', 'converted')->count();

        return $total > 0 ? round(($converted / $total) * 100, 2) : 0;
    }

    public static function getLeadsBySource(): array
    {
        return static::selectRaw('source, COUNT(*) as count')
            ->groupBy('source')
            ->orderBy('count', 'desc')
            ->get()
            ->pluck('count', 'source')
            ->toArray();
    }

    public static function getPipeline(): array
    {
        $statuses = ['new', 'contacted', 'qualified', 'proposal_sent', 'negotiation'];
        $pipeline = [];

        foreach ($statuses as $status) {
            $leads = static::where('status', $status)->get();
            $pipeline[$status] = [
                'count' => $leads->count(),
                'value' => $leads->sum('estimated_value')
            ];
        }

        return $pipeline;
    }

    public function getEstimatedValue(): float
    {
        if ($this->estimated_value) {
            return $this->estimated_value;
        }

        return match($this->budget_range) {
            'under_50k' => 25000,
            '50k_100k' => 75000,
            '100k_250k' => 175000,
            '250k_500k' => 375000,
            '500k_1m' => 750000,
            'over_1m' => 1500000,
            default => 0
        };
    }

    // Enhanced Options
    public static function getProjectTypeOptions()
    {
        return [
            'building' => 'عمارة',
            'compound' => 'كمبوند',
            'hotel_apartments' => 'شقق فندقية',
            'villa' => 'فيلا',
            'commercial' => 'تجاري',
            'other' => 'أخرى'
        ];
    }

    public static function getBudgetRangeOptions()
    {
        return [
            'under_50k' => 'أقل من 50,000 ريال',
            '50k_100k' => '50,000 - 100,000 ريال',
            '100k_250k' => '100,000 - 250,000 ريال',
            '250k_500k' => '250,000 - 500,000 ريال',
            '500k_1m' => '500,000 - 1,000,000 ريال',
            'over_1m' => 'أكثر من 1,000,000 ريال'
        ];
    }

    public function campaign()
{
    return $this->belongsTo(MarketingCampaign::class, 'campaign_id');
}
public function quoteItems()
{
    return $this->hasManyThrough(
        QuoteItem::class,  // الجدول النهائي اللي عايز تجيبه
        Quote::class,      // الجدول الوسيط (quotes)
        'lead_id',         // مفتاح الـ Quote اللي يشير للـ Lead
        'quote_id',        // مفتاح الـ QuoteItem اللي يشير للـ Quote
        'id',              // مفتاح الـ Lead
        'id'               // مفتاح الـ Quote
    );
}

public function quote()
{
    return $this->hasOne(Quote::class);
}


}
