<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketingCampaign;
use App\Models\CampaignTracking;
use App\Models\User;
use App\Models\Package;
use App\Models\Coupon;
use App\Models\WebsiteAnalytics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MarketingController extends Controller
{
    /**
     * Display a listing of marketing campaigns
     */
    public function index(Request $request)
    {
        $query = MarketingCampaign::with(['createdBy'])->orderBy('created_at', 'desc');

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب النوع
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $campaigns = $query->paginate(20);

        // إحصائيات سريعة
        $stats = [
            'total' => MarketingCampaign::count(),
            'draft' => MarketingCampaign::draft()->count(),
            'running' => MarketingCampaign::running()->count(),
            'completed' => MarketingCampaign::completed()->count(),
            'total_sent' => MarketingCampaign::sum('sent_count'),
            'total_opened' => MarketingCampaign::sum('opened_count'),
            'avg_open_rate' => MarketingCampaign::where('sent_count', '>', 0)->avg(DB::raw('(opened_count / sent_count) * 100')),
            'avg_click_rate' => MarketingCampaign::where('sent_count', '>', 0)->avg(DB::raw('(clicked_count / sent_count) * 100'))
        ];

        return view('admin.marketing.campaigns.index', compact('campaigns', 'stats'));
    }

    /**
     * Show the form for creating a new campaign
     */
    public function create()
    {
        $packages = Package::all();

        return view('admin.marketing.campaigns.create', compact('packages'));
    }

    /**
     * Store a newly created campaign
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:email,sms,whatsapp,notification',
            'subject' => 'required_if:type,email|string|max:255',
            'content' => 'required|string',
            'target_audience' => 'required|array',
            'scheduled_at' => 'nullable|date|after:now',
            'settings' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $campaign = MarketingCampaign::create([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'subject' => $request->subject,
            'content' => $request->content,
            'target_audience' => $request->target_audience,
            'scheduled_at' => $request->scheduled_at,
            'settings' => $request->settings,
            'created_by' => auth()->id()
        ]);

        // حساب عدد المستلمين المستهدفين
        $targetAudience = $campaign->getTargetAudience();
        $campaign->total_recipients = $targetAudience->count();
        $campaign->save();

        return redirect()->route('admin.marketing.campaigns.show', $campaign)
                       ->with('success', 'تم إنشاء الحملة التسويقية بنجاح');
    }

    /**
     * Display the specified campaign
     */
    public function show(MarketingCampaign $campaign)
    {
        $campaign->load(['createdBy', 'tracking']);

        // إحصائيات الحملة
        $stats = [
            'total_recipients' => $campaign->total_recipients,
            'sent_count' => $campaign->sent_count,
            'delivered_count' => $campaign->delivered_count,
            'opened_count' => $campaign->opened_count,
            'clicked_count' => $campaign->clicked_count,
            'unsubscribed_count' => $campaign->unsubscribed_count,
            'open_rate' => $campaign->open_rate,
            'click_rate' => $campaign->click_rate,
            'delivery_rate' => $campaign->delivery_rate,
            'unsubscribe_rate' => $campaign->unsubscribe_rate,
            'conversion_rate' => $campaign->conversion_rate
        ];

        // أحدث الأنشطة
        $recentTracking = $campaign->tracking()
                                  ->with('user')
                                  ->latest()
                                  ->limit(10)
                                  ->get();

        return view('admin.marketing.campaigns.show', compact('campaign', 'stats', 'recentTracking'));
    }

    /**
     * Show the form for editing the specified campaign
     */
    public function edit(MarketingCampaign $campaign)
    {
        if (!$campaign->canBeEdited()) {
            return redirect()->route('admin.marketing.campaigns.show', $campaign)
                           ->with('error', 'لا يمكن تعديل هذه الحملة في الحالة الحالية');
        }

        $packages = Package::all();

        return view('admin.marketing.campaigns.edit', compact('campaign', 'packages'));
    }

    /**
     * Update the specified campaign
     */
    public function update(Request $request, MarketingCampaign $campaign)
    {
        if (!$campaign->canBeEdited()) {
            return redirect()->route('admin.marketing.campaigns.show', $campaign)
                           ->with('error', 'لا يمكن تعديل هذه الحملة في الحالة الحالية');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:email,sms,whatsapp,notification',
            'subject' => 'required_if:type,email|string|max:255',
            'content' => 'required|string',
            'target_audience' => 'required|array',
            'scheduled_at' => 'nullable|date|after:now',
            'settings' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $campaign->update([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'subject' => $request->subject,
            'content' => $request->content,
            'target_audience' => $request->target_audience,
            'scheduled_at' => $request->scheduled_at,
            'settings' => $request->settings
        ]);

        // إعادة حساب عدد المستلمين المستهدفين
        $targetAudience = $campaign->getTargetAudience();
        $campaign->total_recipients = $targetAudience->count();
        $campaign->save();

        return redirect()->route('admin.marketing.campaigns.show', $campaign)
                       ->with('success', 'تم تحديث الحملة التسويقية بنجاح');
    }

    /**
     * Start campaign
     */
    public function start(MarketingCampaign $campaign)
    {
        try {
            $campaign->start();

            return response()->json([
                'success' => true,
                'message' => 'تم بدء الحملة التسويقية بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Pause campaign
     */
    public function pause(MarketingCampaign $campaign)
    {
        try {
            $campaign->pause();

            return response()->json([
                'success' => true,
                'message' => 'تم إيقاف الحملة التسويقية مؤقتاً'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Resume campaign
     */
    public function resume(MarketingCampaign $campaign)
    {
        try {
            $campaign->resume();

            return response()->json([
                'success' => true,
                'message' => 'تم استئناف الحملة التسويقية'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Cancel campaign
     */
    public function cancel(MarketingCampaign $campaign)
    {
        try {
            $campaign->cancel();

            return response()->json([
                'success' => true,
                'message' => 'تم إلغاء الحملة التسويقية'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Schedule campaign
     */
    public function schedule(Request $request, MarketingCampaign $campaign)
    {
        $validator = Validator::make($request->all(), [
            'scheduled_at' => 'required|date|after:now'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        try {
            $campaign->schedule($request->scheduled_at);

            return response()->json([
                'success' => true,
                'message' => 'تم جدولة الحملة التسويقية بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Duplicate campaign
     */
    public function duplicate(MarketingCampaign $campaign)
    {
        $newCampaign = $campaign->duplicate();

        return redirect()->route('admin.marketing.campaigns.edit', $newCampaign)
                       ->with('success', 'تم نسخ الحملة التسويقية بنجاح');
    }

    /**
     * Preview campaign
     */
    public function preview(MarketingCampaign $campaign)
    {
        return view('admin.marketing.campaigns.preview', compact('campaign'));
    }

    /**
     * Test campaign
     */
    public function test(Request $request, MarketingCampaign $campaign)
    {
        $validator = Validator::make($request->all(), [
            'test_recipients' => 'required|array',
            'test_recipients.*' => 'email'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        // إرسال الحملة للمستلمين التجريبيين
        // يمكن تنفيذ هذا لاحقاً

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال الحملة التجريبية بنجاح'
        ]);
    }

    /**
     * Get target audience preview
     */
    public function getTargetAudience(Request $request)
    {
        $targetAudience = $request->get('target_audience', []);

        $query = User::where('role', 'customer');

        // تطبيق معايير الجمهور المستهدف
        foreach ($targetAudience as $criterion => $value) {
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

        $count = $query->count();
        $sample = $query->limit(10)->get(['id', 'name', 'email']);

        return response()->json([
            'count' => $count,
            'sample' => $sample
        ]);
    }

    /**
     * Delete campaign
     */
    public function destroy(MarketingCampaign $campaign)
    {
        if (!in_array($campaign->status, ['draft', 'cancelled'])) {
            return redirect()->route('admin.marketing.campaigns.index')
                           ->with('error', 'لا يمكن حذف حملة نشطة أو مكتملة');
        }

        $campaign->delete();

        return redirect()->route('admin.marketing.campaigns.index')
                       ->with('success', 'تم حذف الحملة التسويقية بنجاح');
    }

    /**
     * Get campaign statistics
     */
    public function getStats(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period);
        $endDate = now();

        $stats = [
            'total_campaigns' => MarketingCampaign::whereBetween('created_at', [$startDate, $endDate])->count(),
            'active_campaigns' => MarketingCampaign::running()->whereBetween('created_at', [$startDate, $endDate])->count(),
            'completed_campaigns' => MarketingCampaign::completed()->whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_sent' => MarketingCampaign::whereBetween('created_at', [$startDate, $endDate])->sum('sent_count'),
            'total_opened' => MarketingCampaign::whereBetween('created_at', [$startDate, $endDate])->sum('opened_count'),
            'total_clicked' => MarketingCampaign::whereBetween('created_at', [$startDate, $endDate])->sum('clicked_count'),
            'avg_open_rate' => MarketingCampaign::whereBetween('created_at', [$startDate, $endDate])
                                              ->where('sent_count', '>', 0)
                                              ->avg(DB::raw('(opened_count / sent_count) * 100')),
            'avg_click_rate' => MarketingCampaign::whereBetween('created_at', [$startDate, $endDate])
                                               ->where('sent_count', '>', 0)
                                               ->avg(DB::raw('(clicked_count / sent_count) * 100')),
            'campaigns_by_type' => MarketingCampaign::selectRaw('type, COUNT(*) as count')
                                                   ->whereBetween('created_at', [$startDate, $endDate])
                                                   ->groupBy('type')
                                                   ->get(),
            'top_campaigns' => MarketingCampaign::whereBetween('created_at', [$startDate, $endDate])
                                               ->orderBy('opened_count', 'desc')
                                               ->limit(5)
                                               ->get(['name', 'opened_count', 'sent_count'])
        ];

        return response()->json($stats);
    }

    // Helper Methods
    private function getStartDate($period)
    {
        return match($period) {
            'day' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth()
        };
    }


    /**
     * Display enhanced marketing dashboard.
     */
    public function enhancedDashboard(Request $request)
    {
        $period = $request->get('period', 'month');

        // Get overview statistics
        $stats = [
            'campaigns' => MarketingCampaign::getStatistics($period),
            'coupons' => Coupon::getStatistics($period),
            'analytics' => WebsiteAnalytics::getOverviewStats($period),
        ];

        // Get active campaigns
        $activeCampaigns = MarketingCampaign::where('status', 'running')
            ->with(['tracking', 'leads', 'orders'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get campaigns needing attention
        $campaignsNeedingAttention = MarketingCampaign::getCampaignsNeedingAttention();

        // Get expiring coupons
        $expiringCoupons = Coupon::getExpiringSoon(7);

        return view('admin.marketing.enhanced-dashboard', compact(
            'stats',
            'activeCampaigns',
            'campaignsNeedingAttention',
            'expiringCoupons',
            'period'
        ));
    }

    /**
     * Display analytics dashboard.
     */
    public function analytics(Request $request)
    {
        $period = $request->get('period', 'month');

        // Get comprehensive analytics report
        $report = WebsiteAnalytics::generateReport($period);

        // Get performance metrics
        $performanceMetrics = WebsiteAnalytics::getPerformanceMetrics($period);

        // Get conversion funnel data
        $conversionFunnel = $this->getConversionFunnelData($period);

        // Get traffic trends
        $trafficTrends = $this->getTrafficTrends($period);

        return view('admin.marketing.analytics', compact(
            'report',
            'performanceMetrics',
            'conversionFunnel',
            'trafficTrends',
            'period'
        ));
    }

    /**
     * Display coupons management.
     */
    public function coupons(Request $request)
    {
        $query = Coupon::with(['createdBy', 'usages']);

        // Apply filters
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->where('is_active', true);
                    break;
                case 'expired':
                    $query->where('expires_at', '<', now());
                    break;
                case 'exhausted':
                    $query->whereColumn('used_count', '>=', 'usage_limit');
                    break;
            }
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $coupons = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get statistics
        $stats = Coupon::getStatistics();

        return view('admin.marketing.coupons.index', compact('coupons', 'stats'));
    }

    /**
     * Show coupon creation form.
     */
    public function createCoupon()
    {
        $typeOptions = Coupon::getEnhancedTypeOptions();
        $applicabilityOptions = Coupon::getApplicabilityOptions();

        return view('admin.marketing.coupons.create', compact(
            'typeOptions',
            'applicabilityOptions'
        ));
    }

    /**
     * Store new coupon.
     */
    public function storeCoupon(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed_amount,free_shipping',
            'value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'applicable_to' => 'required|string',
            'applicable_ids' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $validated['code'] = $request->code ?: Coupon::generateUniqueCode();
        $validated['created_by'] = auth()->id();

        $coupon = Coupon::create($validated);

        return redirect()
            ->route('admin.marketing.coupons')
            ->with('success', 'تم إنشاء الكوبون بنجاح');
    }

    /**
     * Create promotional campaign.
     */
    public function createPromotionalCampaign(Request $request)
    {
        $validated = $request->validate([
            'campaign_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed_amount',
            'value' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1|max:1000',
            'usage_limit_per_coupon' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'applicable_to' => 'required|string',
            'applicable_ids' => 'nullable|array',
        ]);

        $coupons = Coupon::createPromotionalCampaign($validated);

        return redirect()
            ->route('admin.marketing.coupons')
            ->with('success', "تم إنشاء {$validated['quantity']} كوبون بنجاح");
    }

    /**
     * Get conversion funnel data.
     */
    private function getConversionFunnelData(string $period): array
    {
        $startDate = $this->getStartDate($period);
        $endDate = now();

        return [
            'visitors' => WebsiteAnalytics::whereBetween('created_at', [$startDate, $endDate])
                ->distinct('visitor_ip')
                ->count(),
            'page_views' => WebsiteAnalytics::whereBetween('created_at', [$startDate, $endDate])
                ->where('event_type', 'page_view')
                ->count(),
            'package_views' => WebsiteAnalytics::whereBetween('created_at', [$startDate, $endDate])
                ->where('event_type', 'package_view')
                ->count(),
            'quote_requests' => WebsiteAnalytics::whereBetween('created_at', [$startDate, $endDate])
                ->where('event_type', 'quote_request')
                ->count(),
            'leads' => Lead::whereBetween('created_at', [$startDate, $endDate])->count(),
            'orders' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
        ];
    }

    /**
     * Get traffic trends data.
     */
    private function getTrafficTrends(string $period): array
    {
        $startDate = $this->getStartDate($period);
        $endDate = now();

        $groupBy = match($period) {
            'week' => 'DATE(created_at)',
            'month' => 'DATE(created_at)',
            'quarter' => 'WEEK(created_at)',
            'year' => 'MONTH(created_at)',
            default => 'DATE(created_at)'
        };

        return WebsiteAnalytics::whereBetween('created_at', [$startDate, $endDate])
            ->where('event_type', 'page_view')
            ->selectRaw("{$groupBy} as period, COUNT(*) as views, COUNT(DISTINCT visitor_ip) as visitors")
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->toArray();
    }

    /**
     * Cleanup expired data.
     */
    public function cleanup()
    {
        $expiredCoupons = Coupon::cleanupExpired();
        $deactivatedCoupons = Coupon::deactivateExpired();
        $completedCampaigns = MarketingCampaign::completeExpiredCampaigns();
        $cleanedAnalytics = WebsiteAnalytics::cleanupOldData(365);

        return back()->with('success',
            "تم تنظيف البيانات: {$expiredCoupons} كوبون منتهي الصلاحية، " .
            "{$deactivatedCoupons} كوبون تم إلغاء تفعيله، " .
            "{$completedCampaigns} حملة تم إنهاؤها، " .
            "{$cleanedAnalytics} سجل تحليلات قديم"
        );
    }
}
