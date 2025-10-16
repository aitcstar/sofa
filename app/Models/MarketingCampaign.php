<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketingCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'status',
        'target_audience',
        'subject',
        'content',
        'settings',
        'budget',
        'spent_amount',
        'start_date',
        'end_date',
        'goals',
        'channels',
        'tracking_parameters',
        'scheduled_at',
        'started_at',
        'completed_at',
        'total_recipients',
        'sent_count',
        'delivered_count',
        'opened_count',
        'clicked_count',
        'unsubscribed_count',
        'conversion_rate',
        'created_by',
        'metadata'
    ];

    protected $casts = [
        'target_audience' => 'array',
        'settings' => 'array',
        'budget' => 'decimal:2',
        'spent_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'goals' => 'array',
        'channels' => 'array',
        'tracking_parameters' => 'array',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'total_recipients' => 'integer',
        'sent_count' => 'integer',
        'delivered_count' => 'integer',
        'opened_count' => 'integer',
        'clicked_count' => 'integer',
        'unsubscribed_count' => 'integer',
        'conversion_rate' => 'decimal:2',
        'metadata' => 'array'
    ];

    // العلاقات
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tracking(): HasMany
    {
        return $this->hasMany(CampaignTracking::class, 'campaign_id');
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeRunning($query)
    {
        return $query->where('status', 'running');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Accessors
    public function getTypeTextAttribute()
    {
        return match($this->type) {
            'email' => 'بريد إلكتروني',
            'sms' => 'رسائل نصية',
            'whatsapp' => 'واتساب',
            'notification' => 'إشعارات',
            default => $this->type
        };
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'draft' => 'مسودة',
            'scheduled' => 'مجدولة',
            'running' => 'قيد التشغيل',
            'completed' => 'مكتملة',
            'paused' => 'متوقفة',
            'cancelled' => 'ملغية',
            default => $this->status
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'draft' => 'gray',
            'scheduled' => 'blue',
            'running' => 'yellow',
            'completed' => 'green',
            'paused' => 'orange',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    public function getOpenRateAttribute()
    {
        return $this->sent_count > 0 ? ($this->opened_count / $this->sent_count) * 100 : 0;
    }

    public function getClickRateAttribute()
    {
        return $this->sent_count > 0 ? ($this->clicked_count / $this->sent_count) * 100 : 0;
    }

    public function getDeliveryRateAttribute()
    {
        return $this->sent_count > 0 ? ($this->delivered_count / $this->sent_count) * 100 : 0;
    }

    public function getUnsubscribeRateAttribute()
    {
        return $this->sent_count > 0 ? ($this->unsubscribed_count / $this->sent_count) * 100 : 0;
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->total_recipients == 0) {
            return 0;
        }

        return ($this->sent_count / $this->total_recipients) * 100;
    }

    // Methods
    public function canBeEdited(): bool
    {
        return in_array($this->status, ['draft', 'scheduled']);
    }

    public function canBeStarted(): bool
    {
        return in_array($this->status, ['draft', 'scheduled']);
    }

    public function canBePaused(): bool
    {
        return $this->status === 'running';
    }

    public function canBeResumed(): bool
    {
        return $this->status === 'paused';
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['draft', 'scheduled', 'running', 'paused']);
    }

    public function start()
    {
        if (!$this->canBeStarted()) {
            throw new \Exception('لا يمكن بدء هذه الحملة في الحالة الحالية');
        }

        $this->status = 'running';
        $this->started_at = now();
        $this->save();
    }

    public function pause()
    {
        if (!$this->canBePaused()) {
            throw new \Exception('لا يمكن إيقاف هذه الحملة في الحالة الحالية');
        }

        $this->status = 'paused';
        $this->save();
    }

    public function resume()
    {
        if (!$this->canBeResumed()) {
            throw new \Exception('لا يمكن استئناف هذه الحملة في الحالة الحالية');
        }

        $this->status = 'running';
        $this->save();
    }

    public function complete()
    {
        $this->status = 'completed';
        $this->completed_at = now();
        $this->save();
    }

    public function cancel()
    {
        if (!$this->canBeCancelled()) {
            throw new \Exception('لا يمكن إلغاء هذه الحملة في الحالة الحالية');
        }

        $this->status = 'cancelled';
        $this->save();
    }

    public function schedule($dateTime)
    {
        $this->status = 'scheduled';
        $this->scheduled_at = $dateTime;
        $this->save();
    }

    public function getTargetAudience()
    {
        $query = User::where('role', 'customer');

        if (!$this->target_audience) {
            return $query;
        }

        // تطبيق معايير الجمهور المستهدف
        foreach ($this->target_audience as $criterion => $value) {
            switch ($criterion) {
                case 'age_min':
                    $query->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= ?', [$value]);
                    break;
                case 'age_max':
                    $query->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) <= ?', [$value]);
                    break;
                case 'city':
                    $query->where('city', $value);
                    break;
                case 'has_orders':
                    if ($value) {
                        $query->has('orders');
                    } else {
                        $query->doesntHave('orders');
                    }
                    break;
                case 'order_count_min':
                    $query->withCount('orders')->having('orders_count', '>=', $value);
                    break;
                case 'order_count_max':
                    $query->withCount('orders')->having('orders_count', '<=', $value);
                    break;
                case 'total_spent_min':
                    $query->withSum('orders', 'total_amount')->having('orders_sum_total_amount', '>=', $value);
                    break;
                case 'total_spent_max':
                    $query->withSum('orders', 'total_amount')->having('orders_sum_total_amount', '<=', $value);
                    break;
                case 'last_order_days':
                    $query->whereHas('orders', function($q) use ($value) {
                        $q->where('created_at', '>=', now()->subDays($value));
                    });
                    break;
                case 'preferred_packages':
                    $query->whereHas('orders', function($q) use ($value) {
                        $q->whereIn('package_id', $value);
                    });
                    break;
            }
        }

        return $query;
    }

    public function updateStats()
    {
        $this->sent_count = $this->tracking()->where('action', 'sent')->count();
        $this->delivered_count = $this->tracking()->where('action', 'delivered')->count();
        $this->opened_count = $this->tracking()->where('action', 'opened')->count();
        $this->clicked_count = $this->tracking()->where('action', 'clicked')->count();
        $this->unsubscribed_count = $this->tracking()->where('action', 'unsubscribed')->count();

        $conversions = $this->tracking()->where('action', 'converted')->count();
        $this->conversion_rate = $this->sent_count > 0 ? ($conversions / $this->sent_count) * 100 : 0;

        $this->save();
    }

    public function duplicate()
    {
        $newCampaign = $this->replicate();
        $newCampaign->name = $this->name . ' (نسخة)';
        $newCampaign->status = 'draft';
        $newCampaign->scheduled_at = null;
        $newCampaign->started_at = null;
        $newCampaign->completed_at = null;
        $newCampaign->total_recipients = 0;
        $newCampaign->sent_count = 0;
        $newCampaign->delivered_count = 0;
        $newCampaign->opened_count = 0;
        $newCampaign->clicked_count = 0;
        $newCampaign->unsubscribed_count = 0;
        $newCampaign->conversion_rate = 0;
        $newCampaign->save();

        return $newCampaign;
    }

    // Static Methods
    public static function getTypeOptions()
    {
        return [
            'email' => 'بريد إلكتروني',
            'sms' => 'رسائل نصية',
            'whatsapp' => 'واتساب',
            'notification' => 'إشعارات'
        ];
    }

    public static function getStatusOptions()
    {
        return [
            'draft' => 'مسودة',
            'scheduled' => 'مجدولة',
            'running' => 'قيد التشغيل',
            'completed' => 'مكتملة',
            'paused' => 'متوقفة',
            'cancelled' => 'ملغية'
        ];
    }


    // Enhanced relationships
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'campaign_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'campaign_id');
    }

    // Enhanced Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('start_date', '<=', now())
                    ->where(function ($q) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', now());
                    });
    }

    public function scopeByChannel($query, $channel)
    {
        return $query->whereJsonContains('channels', $channel);
    }

    // Enhanced Accessors
    public function getBudgetUtilizationAttribute(): float
    {
        return $this->budget > 0 ?
            round(($this->spent_amount / $this->budget) * 100, 2) : 0;
    }

    public function getRemainingBudgetAttribute(): float
    {
        return max(0, $this->budget - $this->spent_amount);
    }

    public function getDurationDaysAttribute(): int
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }
        return $this->start_date->diffInDays($this->end_date);
    }

    public function getDaysRemainingAttribute(): int
    {
        if (!$this->end_date || $this->end_date < now()) {
            return 0;
        }
        return now()->diffInDays($this->end_date);
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active' &&
               $this->start_date <= now() &&
               (!$this->end_date || $this->end_date >= now());
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->end_date && $this->end_date < now();
    }

    // Enhanced Methods
    public function addSpending(float $amount, string $description = null): bool
    {
        $newSpentAmount = $this->spent_amount + $amount;

        // Check budget limit
        if ($this->budget && $newSpentAmount > $this->budget) {
            return false;
        }

        $updated = $this->update(['spent_amount' => $newSpentAmount]);

        if ($updated && $description) {
            $this->tracking()->create([
                'action' => 'spending',
                'value' => $amount,
                'description' => $description,
                'tracked_at' => now()
            ]);
        }

        return $updated;
    }

    public function trackEvent(string $type, $value = null, string $description = null): CampaignTracking
    {
        return $this->tracking()->create([
            'action' => $type,
            'value' => $value,
            'description' => $description,
            'tracked_at' => now()
        ]);
    }

    public function getPerformanceMetrics(): array
    {
        $tracking = $this->tracking;

        return [
            'impressions' => $tracking->where('action', 'impression')->sum('value'),
            'clicks' => $tracking->where('action', 'click')->sum('value'),
            'conversions' => $tracking->where('action', 'conversion')->sum('value'),
            'leads_generated' => $this->leads()->count(),
            'orders_generated' => $this->orders()->count(),
            'revenue_generated' => $this->orders()->sum('total_amount'),
            'cost_per_click' => $this->calculateCostPerClick(),
            'cost_per_conversion' => $this->calculateCostPerConversion(),
            'roi' => $this->calculateROI(),
            'conversion_rate' => $this->calculateConversionRate(),
        ];
    }

    private function calculateCostPerClick(): float
    {
        $clicks = $this->tracking->where('action', 'click')->sum('value');
        return $clicks > 0 ? round($this->spent_amount / $clicks, 2) : 0;
    }

    private function calculateCostPerConversion(): float
    {
        $conversions = $this->tracking->where('action', 'conversion')->sum('value');
        return $conversions > 0 ? round($this->spent_amount / $conversions, 2) : 0;
    }

    private function calculateROI(): float
    {
        $revenue = $this->orders()->sum('total_amount');
        return $this->spent_amount > 0 ?
            round((($revenue - $this->spent_amount) / $this->spent_amount) * 100, 2) : 0;
    }

    private function calculateConversionRate(): float
    {
        $clicks = $this->tracking->where('action', 'click')->sum('value');
        $conversions = $this->tracking->where('action', 'conversion')->sum('value');
        return $clicks > 0 ? round(($conversions / $clicks) * 100, 2) : 0;
    }

    public function generateTrackingUrl(string $baseUrl, array $additionalParams = []): string
    {
        $params = array_merge([
            'utm_source' => $this->tracking_parameters['utm_source'] ?? 'sofa',
            'utm_medium' => $this->tracking_parameters['utm_medium'] ?? 'campaign',
            'utm_campaign' => $this->tracking_parameters['utm_campaign'] ?? $this->id,
            'utm_content' => $this->tracking_parameters['utm_content'] ?? $this->name,
        ], $additionalParams);

        return $baseUrl . '?' . http_build_query($params);
    }

    // Enhanced Static Methods
    public static function getEnhancedTypeOptions(): array
    {
        return [
            'email' => 'حملة بريد إلكتروني',
            'sms' => 'رسائل نصية',
            'whatsapp' => 'واتساب',
            'notification' => 'إشعارات',
            'social_media' => 'وسائل التواصل الاجتماعي',
            'google_ads' => 'إعلانات جوجل',
            'facebook_ads' => 'إعلانات فيسبوك',
            'content_marketing' => 'تسويق المحتوى',
            'seo' => 'تحسين محركات البحث',
            'influencer' => 'التسويق عبر المؤثرين',
            'referral' => 'برنامج الإحالة',
            'event' => 'فعالية أو معرض',
            'print' => 'إعلانات مطبوعة',
            'radio' => 'إعلانات إذاعية',
            'tv' => 'إعلانات تلفزيونية',
            'outdoor' => 'إعلانات خارجية',
            'other' => 'أخرى'
        ];
    }

    public static function getChannelOptions(): array
    {
        return [
            'website' => 'الموقع الإلكتروني',
            'facebook' => 'فيسبوك',
            'instagram' => 'إنستغرام',
            'twitter' => 'تويتر',
            'linkedin' => 'لينكد إن',
            'youtube' => 'يوتيوب',
            'tiktok' => 'تيك توك',
            'snapchat' => 'سناب شات',
            'google' => 'جوجل',
            'email' => 'البريد الإلكتروني',
            'sms' => 'الرسائل النصية',
            'whatsapp' => 'واتساب',
            'print' => 'المطبوعات',
            'radio' => 'الراديو',
            'tv' => 'التلفزيون',
            'outdoor' => 'الإعلانات الخارجية',
            'events' => 'الفعاليات',
            'referral' => 'الإحالات',
            'other' => 'أخرى'
        ];
    }

    /*public static function getStatistics(string $period = 'month'): array
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
            'total_campaigns' => static::count(),
            'active_campaigns' => static::where('status', 'running')->count(),
            'completed_campaigns' => static::completed()->count(),
            'total_budget' => static::sum('budget'),
            'total_spent' => static::sum('spent_amount'),
            'total_leads_generated' => Lead::whereHas('campaign')->whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_orders_generated' => Order::whereHas('campaign')->whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_revenue_generated' => Order::whereHas('campaign')->whereBetween('created_at', [$startDate, $endDate])->sum('total_amount'),
            'campaigns_by_type' => static::groupBy('type')
                ->selectRaw('type, COUNT(*) as count, SUM(spent_amount) as total_spent')
                ->get()
                ->keyBy('type')
                ->toArray(),
            'top_performing_campaigns' => static::withCount(['orders', 'leads'])
                ->with(['orders' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }])
                ->orderBy('orders_count', 'desc')
                ->limit(10)
                ->get(),
        ];
    }
*/

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

    $stats = [
        'total_campaigns' => static::whereBetween('created_at', [$startDate, $endDate])->count(),
        'active_campaigns' => static::where('status', 'running')->whereBetween('created_at', [$startDate, $endDate])->count(),
        'completed_campaigns' => static::where('status', 'completed')->whereBetween('created_at', [$startDate, $endDate])->count(),
        'total_sent' => static::whereBetween('created_at', [$startDate, $endDate])->sum('sent_count'),
        'total_opened' => static::whereBetween('created_at', [$startDate, $endDate])->sum('opened_count'),
        'total_clicked' => static::whereBetween('created_at', [$startDate, $endDate])->sum('clicked_count'),
        'avg_open_rate' => round(static::whereBetween('created_at', [$startDate, $endDate])
            ->where('sent_count', '>', 0)
            ->avg(\DB::raw('(opened_count / sent_count) * 100')), 2),
        'avg_click_rate' => round(static::whereBetween('created_at', [$startDate, $endDate])
            ->where('sent_count', '>', 0)
            ->avg(\DB::raw('(clicked_count / sent_count) * 100')), 2),
    ];

    return $stats;
}

    public static function completeExpiredCampaigns(): int
    {
        return static::where('status', 'running')
            ->where('end_date', '<', now())
            ->update(['status' => 'completed']);
    }

    public static function getCampaignsNeedingAttention(): \Illuminate\Database\Eloquent\Collection
    {
        return static::where(function ($query) {
            $query->where('status', 'running')
                  ->where(function ($q) {
                      // Budget almost exhausted
                      $q->whereRaw('spent_amount >= budget * 0.9')
                        // Or ending soon
                        ->orWhere('end_date', '<=', now()->addDays(3));
                  });
        })->get();
    }
}
