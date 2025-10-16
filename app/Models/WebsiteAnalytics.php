<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebsiteAnalytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'session_id',
        'user_id',
        'page_url',
        'page_title',
        'referrer_url',
        'visitor_ip',
        'user_agent',
        'referrer',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'event_type',
        'event_data',
        'device_type',
        'browser',
        'os',
        'country',
        'city',
        'ip_address',
        'time_on_page',
        'duration',
        'is_bounce',
        'bounce',
        'is_conversion',
        'conversion',
        'metadata'
    ];

    protected $casts = [
        'date' => 'date',
        'time_on_page' => 'integer',
        'duration' => 'integer',
        'is_bounce' => 'boolean',
        'bounce' => 'boolean',
        'is_conversion' => 'boolean',
        'conversion' => 'boolean',
        'event_data' => 'array',
        'metadata' => 'array'
    ];

    // العلاقات
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeByPage($query, $pageUrl)
    {
        return $query->where('page_url', $pageUrl);
    }

    public function scopeBySource($query, $source)
    {
        return $query->where('utm_source', $source);
    }

    public function scopeByMedium($query, $medium)
    {
        return $query->where('utm_medium', $medium);
    }

    public function scopeByCampaign($query, $campaign)
    {
        return $query->where('utm_campaign', $campaign);
    }

    public function scopeByDevice($query, $deviceType)
    {
        return $query->where('device_type', $deviceType);
    }

    public function scopeBounces($query)
    {
        return $query->where('is_bounce', true);
    }

    public function scopeConversions($query)
    {
        return $query->where('is_conversion', true);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    // Accessors
    public function getDeviceTypeTextAttribute()
    {
        return match($this->device_type) {
            'desktop' => 'سطح المكتب',
            'mobile' => 'جوال',
            'tablet' => 'لوحي',
            default => $this->device_type
        };
    }

    public function getTimeOnPageFormattedAttribute()
    {
        if (!$this->time_on_page) {
            return '0 ثانية';
        }

        $minutes = floor($this->time_on_page / 60);
        $seconds = $this->time_on_page % 60;

        if ($minutes > 0) {
            return "{$minutes} دقيقة {$seconds} ثانية";
        }

        return "{$seconds} ثانية";
    }

    public function getTrafficSourceAttribute()
    {
        if ($this->utm_source) {
            return $this->utm_source;
        }

        if ($this->referrer_url) {
            $domain = parse_url($this->referrer_url, PHP_URL_HOST);
            return $domain ?: 'مصدر خارجي';
        }

        return 'مباشر';
    }

    // Methods
    public function markAsConversion()
    {
        $this->is_conversion = true;
        $this->save();
    }

    public function markAsBounce()
    {
        $this->is_bounce = true;
        $this->save();
    }

    public function updateTimeOnPage($timeSpent)
    {
        $this->time_on_page = $timeSpent;
        $this->save();
    }

    // Static Methods
    public static function track($data)
    {
        return static::create($data);
    }

    public static function getPopularPages($limit = 10)
    {
        return static::selectRaw('page_url, page_title, COUNT(*) as visits')
                     ->groupBy('page_url', 'page_title')
                     ->orderBy('visits', 'desc')
                     ->limit($limit)
                     ->get();
    }

    public static function getTrafficSources($limit = 10)
    {
        return static::selectRaw('
                        CASE
                            WHEN utm_source IS NOT NULL THEN utm_source
                            WHEN referrer_url IS NOT NULL THEN SUBSTRING_INDEX(SUBSTRING_INDEX(referrer_url, "/", 3), "//", -1)
                            ELSE "مباشر"
                        END as source,
                        COUNT(*) as visits
                    ')
                     ->groupBy('source')
                     ->orderBy('visits', 'desc')
                     ->limit($limit)
                     ->get();
    }

    public static function getDeviceStats()
    {
        return static::selectRaw('device_type, COUNT(*) as count')
                     ->groupBy('device_type')
                     ->get();
    }

    public static function getBrowserStats()
    {
        return static::selectRaw('browser, COUNT(*) as count')
                     ->whereNotNull('browser')
                     ->groupBy('browser')
                     ->orderBy('count', 'desc')
                     ->get();
    }

    public static function getCountryStats()
    {
        return static::selectRaw('country, COUNT(*) as count')
                     ->whereNotNull('country')
                     ->groupBy('country')
                     ->orderBy('count', 'desc')
                     ->get();
    }

    public static function getBounceRate()
    {
        $totalVisits = static::count();
        $bounces = static::bounces()->count();

        return $totalVisits > 0 ? ($bounces / $totalVisits) * 100 : 0;
    }

    public static function getConversionRate()
    {
        $totalVisits = static::count();
        $conversions = static::conversions()->count();

        return $totalVisits > 0 ? ($conversions / $totalVisits) * 100 : 0;
    }

    public static function getAverageTimeOnPage()
    {
        return static::whereNotNull('time_on_page')
                     ->avg('time_on_page') ?: 0;
    }

    public static function getUniqueVisitors()
    {
        return static::distinct('session_id')->count('session_id');
    }

    public static function getPageViews()
    {
        return static::count();
    }

    public static function getCampaignPerformance()
    {
        return static::selectRaw('
                        utm_campaign,
                        utm_source,
                        utm_medium,
                        COUNT(*) as visits,
                        COUNT(DISTINCT session_id) as unique_visitors,
                        SUM(CASE WHEN is_conversion = 1 THEN 1 ELSE 0 END) as conversions,
                        AVG(time_on_page) as avg_time_on_page
                    ')
                     ->whereNotNull('utm_campaign')
                     ->groupBy('utm_campaign', 'utm_source', 'utm_medium')
                     ->orderBy('visits', 'desc')
                     ->get();
    }


    // Enhanced Scopes
    public function scopePageViews($query)
    {
        return $query->where('event_type', 'page_view');
    }

    public function scopeClicks($query)
    {
        return $query->where('event_type', 'click');
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeUniqueVisitors($query)
    {
        return $query->distinct('visitor_ip');
    }

    public function scopeUniqueSessions($query)
    {
        return $query->distinct('session_id');
    }

    // Enhanced Accessors
    public function getEventTypeTextAttribute(): string
    {
        return match($this->event_type) {
            'page_view' => 'عرض صفحة',
            'click' => 'نقرة',
            'form_submit' => 'إرسال نموذج',
            'download' => 'تحميل',
            'video_play' => 'تشغيل فيديو',
            'scroll' => 'تمرير',
            'search' => 'بحث',
            'contact' => 'تواصل',
            'quote_request' => 'طلب عرض سعر',
            'package_view' => 'عرض باكج',
            'gallery_view' => 'عرض معرض',
            'questionnaire_start' => 'بدء استبيان',
            'questionnaire_complete' => 'إكمال استبيان',
            default => $this->event_type ?? 'عرض صفحة'
        };
    }

    public function getFormattedDurationAttribute(): string
    {
        $duration = $this->duration ?? $this->time_on_page ?? 0;

        if (!$duration) {
            return '0 ثانية';
        }

        $minutes = floor($duration / 60);
        $seconds = $duration % 60;

        if ($minutes > 0) {
            return "{$minutes} دقيقة {$seconds} ثانية";
        }

        return "{$seconds} ثانية";
    }

    // Enhanced Static Methods
    public static function trackPageView(array $data): self
    {
        return static::create(array_merge($data, [
            'event_type' => 'page_view',
            'date' => now()->toDateString()
        ]));
    }

    public static function trackEvent(string $eventType, array $data): self
    {
        return static::create(array_merge($data, [
            'event_type' => $eventType,
            'date' => now()->toDateString()
        ]));
    }

    public static function getOverviewStats(string $period = 'month'): array
    {
        $startDate = match($period) {
            'today' => today(),
            'yesterday' => yesterday(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'quarter' => now()->startOfQuarter(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth()
        };

        $endDate = now();

        $query = static::byDateRange($startDate, $endDate);

        return [
            'total_page_views' => $query->where('event_type', 'page_view')->orWhereNull('event_type')->count(),
            'unique_visitors' => $query->where('event_type', 'page_view')->orWhereNull('event_type')->distinct('visitor_ip')->count(),
            'unique_sessions' => $query->where('event_type', 'page_view')->orWhereNull('event_type')->distinct('session_id')->count(),
            'total_events' => $query->count(),
            'conversions' => $query->where('conversion', true)->orWhere('is_conversion', true)->count(),
            'bounces' => $query->where('bounce', true)->orWhere('is_bounce', true)->count(),
            'average_session_duration' => $query->where('event_type', 'page_view')->orWhereNull('event_type')->avg('duration') ?? $query->avg('time_on_page') ?? 0,
            'bounce_rate' => static::calculateBounceRate($startDate, $endDate),
            'conversion_rate' => static::calculateConversionRate($startDate, $endDate),
        ];
    }

    public static function calculateBounceRate($startDate, $endDate): float
    {
        $totalSessions = static::byDateRange($startDate, $endDate)
            ->where('event_type', 'page_view')->orWhereNull('event_type')
            ->distinct('session_id')
            ->count();

        $bounceSessions = static::byDateRange($startDate, $endDate)
            ->where(function($query) {
                $query->where('bounce', true)->orWhere('is_bounce', true);
            })
            ->distinct('session_id')
            ->count();

        return $totalSessions > 0 ? round(($bounceSessions / $totalSessions) * 100, 2) : 0;
    }

    public static function calculateConversionRate($startDate, $endDate): float
    {
        $totalSessions = static::byDateRange($startDate, $endDate)
            ->where('event_type', 'page_view')->orWhereNull('event_type')
            ->distinct('session_id')
            ->count();

        $conversionSessions = static::byDateRange($startDate, $endDate)
            ->where(function($query) {
                $query->where('conversion', true)->orWhere('is_conversion', true);
            })
            ->distinct('session_id')
            ->count();

        return $totalSessions > 0 ? round(($conversionSessions / $totalSessions) * 100, 2) : 0;
    }

    public static function getPerformanceMetrics(string $period = 'month'): array
    {
        $startDate = match($period) {
            'today' => today(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'quarter' => now()->startOfQuarter(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth()
        };

        $pageViews = static::byDateRange($startDate, now())
            ->where('event_type', 'page_view')->orWhereNull('event_type');

        return [
            'average_page_load_time' => $pageViews->whereNotNull('metadata->page_load_time')
                ->avg('metadata->page_load_time') ?? 0,
            'pages_with_slow_load' => $pageViews->whereRaw('JSON_EXTRACT(metadata, "$.page_load_time") > 3')
                ->count(),
            'mobile_performance_score' => $pageViews->where('device_type', 'mobile')
                ->whereNotNull('metadata->performance_score')
                ->avg('metadata->performance_score') ?? 0,
            'desktop_performance_score' => $pageViews->where('device_type', 'desktop')
                ->whereNotNull('metadata->performance_score')
                ->avg('metadata->performance_score') ?? 0,
            'core_web_vitals' => [
                'lcp' => $pageViews->whereNotNull('metadata->lcp')->avg('metadata->lcp') ?? 0,
                'fid' => $pageViews->whereNotNull('metadata->fid')->avg('metadata->fid') ?? 0,
                'cls' => $pageViews->whereNotNull('metadata->cls')->avg('metadata->cls') ?? 0,
            ],
            'seo_issues' => static::getSEOIssues($startDate, now()),
        ];
    }

    public static function getSEOIssues($startDate, $endDate): array
    {
        $pageViews = static::byDateRange($startDate, $endDate)
            ->where('event_type', 'page_view')->orWhereNull('event_type');

        return [
            'pages_without_title' => $pageViews->whereNull('page_title')->count(),
            'pages_with_long_title' => $pageViews->whereRaw('LENGTH(page_title) > 60')->count(),
            'pages_without_meta_description' => $pageViews->whereNull('metadata->meta_description')->count(),
            'pages_with_long_meta_description' => $pageViews->whereRaw('LENGTH(JSON_UNQUOTE(JSON_EXTRACT(metadata, "$.meta_description"))) > 160')->count(),
            'pages_without_h1' => $pageViews->whereNull('metadata->h1')->count(),
            'pages_with_multiple_h1' => $pageViews->whereRaw('JSON_EXTRACT(metadata, "$.h1_count") > 1')->count(),
            'pages_without_alt_text' => $pageViews->whereRaw('JSON_EXTRACT(metadata, "$.images_without_alt") > 0')->count(),
            'broken_links' => $pageViews->whereRaw('JSON_EXTRACT(metadata, "$.broken_links") > 0')->count(),
        ];
    }

    public static function generateReport(string $period = 'month'): array
    {
        return [
            'overview' => static::getOverviewStats($period),
            'top_pages' => static::getPopularPages(10),
            'traffic_sources' => static::getTrafficSources(10),
            'device_stats' => static::getDeviceStats(),
            'browser_stats' => static::getBrowserStats(),
            'country_stats' => static::getCountryStats(),
            'performance_metrics' => static::getPerformanceMetrics($period),
            'campaign_performance' => static::getCampaignPerformance(),
        ];
    }

    public static function cleanupOldData(int $daysToKeep = 365): int
    {
        return static::where('created_at', '<', now()->subDays($daysToKeep))->delete();
    }
}
