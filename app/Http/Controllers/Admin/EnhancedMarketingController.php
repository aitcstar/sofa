<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketingCampaign;
use App\Models\Coupon;
use App\Models\CampaignTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EnhancedMarketingController extends Controller
{
    /**
     * Marketing dashboard.
     */
    public function index()
    {
        $stats = [
            'total_campaigns' => MarketingCampaign::count(),
            'active_campaigns' => MarketingCampaign::where('status', 'active')->count(),
            'total_coupons' => Coupon::count(),
            'active_coupons' => Coupon::where('is_active', true)
                ->where('starts_at', '<=', now())
                ->where('expires_at', '>=', now())
                ->count(),
        ];

        $recentCampaigns = MarketingCampaign::with('tracking')
            ->latest()
            ->limit(5)
            ->get();

        $topCoupons = Coupon::withCount('usages')
            ->orderBy('usages_count', 'desc')
            ->limit(5)
            ->get();

        return view('admin.marketing.enhanced-index', compact('stats', 'recentCampaigns', 'topCoupons'));
    }

    /**
     * Display campaigns list.
     */
    public function campaigns(Request $request)
    {
        $query = MarketingCampaign::with('tracking');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $campaigns = $query->latest()->paginate(15);

        return view('admin.marketing.campaigns.index', compact('campaigns'));
    }

    /**
     * Show create campaign form.
     */
    public function createCampaign()
    {
        return view('admin.marketing.campaigns.create');
    }

    /**
     * Store a new campaign.
     */
    public function storeCampaign(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:email,sms,social,banner,popup',
            'description' => 'nullable|string',
            'target_audience' => 'nullable|string',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'content' => 'nullable|string',
            'goals' => 'nullable|string',
        ]);

        $campaign = MarketingCampaign::create([
            ...$validated,
            'status' => 'draft',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.marketing.campaigns.show', $campaign)
            ->with('success', 'تم إنشاء الحملة بنجاح');
    }

    /**
     * Show campaign details.
     */
    public function showCampaign(MarketingCampaign $campaign)
    {
        $campaign->load('tracking');

        $stats = [
            'impressions' => $campaign->tracking->sum('impressions'),
            'clicks' => $campaign->tracking->sum('clicks'),
            'conversions' => $campaign->tracking->sum('conversions'),
            'revenue' => $campaign->tracking->sum('revenue'),
        ];

        $stats['ctr'] = $stats['impressions'] > 0
            ? round(($stats['clicks'] / $stats['impressions']) * 100, 2)
            : 0;

        $stats['conversion_rate'] = $stats['clicks'] > 0
            ? round(($stats['conversions'] / $stats['clicks']) * 100, 2)
            : 0;

        return view('admin.marketing.campaigns.show', compact('campaign', 'stats'));
    }

    /**
     * Show edit campaign form.
     */
    public function editCampaign(MarketingCampaign $campaign)
    {
        return view('admin.marketing.campaigns.edit', compact('campaign'));
    }

    /**
     * Update campaign.
     */
    public function updateCampaign(Request $request, MarketingCampaign $campaign)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:email,sms,social,banner,popup',
            'description' => 'nullable|string',
            'target_audience' => 'nullable|string',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'content' => 'nullable|string',
            'goals' => 'nullable|string',
        ]);

        $campaign->update($validated);

        return redirect()->route('admin.marketing.campaigns.show', $campaign)
            ->with('success', 'تم تحديث الحملة بنجاح');
    }

    /**
     * Delete campaign.
     */
    public function destroyCampaign(MarketingCampaign $campaign)
    {
        if ($campaign->status === 'active') {
            return back()->with('error', 'لا يمكن حذف حملة نشطة');
        }

        $campaign->delete();

        return redirect()->route('admin.marketing.campaigns.index')
            ->with('success', 'تم حذف الحملة بنجاح');
    }

    /**
     * Start campaign.
     */
    public function startCampaign(MarketingCampaign $campaign)
    {
        $campaign->update(['status' => 'active']);

        return back()->with('success', 'تم تفعيل الحملة بنجاح');
    }

    /**
     * Pause campaign.
     */
    public function pauseCampaign(MarketingCampaign $campaign)
    {
        $campaign->update(['status' => 'paused']);

        return back()->with('success', 'تم إيقاف الحملة مؤقتاً');
    }

    /**
     * Resume campaign.
     */
    public function resumeCampaign(MarketingCampaign $campaign)
    {
        $campaign->update(['status' => 'active']);

        return back()->with('success', 'تم استئناف الحملة بنجاح');
    }

    /**
     * Complete campaign.
     */
    public function completeCampaign(MarketingCampaign $campaign)
    {
        $campaign->update(['status' => 'completed']);

        return back()->with('success', 'تم إكمال الحملة بنجاح');
    }

    /**
     * Display coupons list.
     */
    public function coupons(Request $request)
    {
        $query = Coupon::withCount('usages');

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true)
                    ->where('starts_at', '<=', now())
                    ->where('expires_at', '>=', now());
            } else {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        $coupons = $query->latest()->paginate(15);

        return view('admin.marketing.coupons.index', compact('coupons'));
    }

    /**
     * Show create coupon form.
     */
    public function createCoupon()
    {
        return view('admin.marketing.coupons.create');
    }

    /**
     * Store a new coupon.
     */
    public function storeCoupon(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:coupons,code|max:50',
            'type' => 'required|in:percentage,fixed_amount',
            'value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'starts_at' => 'required|date',
            'expires_at' => 'required|date|after:starts_at',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $coupon = Coupon::create([
            ...$validated,
            'code' => strtoupper($validated['code']),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.marketing.coupons.index')
            ->with('success', 'تم إنشاء الكوبون بنجاح');
    }

    /**
     * Show edit coupon form.
     */
    public function editCoupon(Coupon $coupon)
    {
        return view('admin.marketing.coupons.edit', compact('coupon'));
    }

    /**
     * Update coupon.
     */
    public function updateCoupon(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'type' => 'required|in:percentage,fixed_amount',
            'value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'starts_at' => 'required|date',
            'expires_at' => 'required|date|after:starts_at',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $coupon->update([
            ...$validated,
            'code' => strtoupper($validated['code']),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.marketing.coupons.index')
            ->with('success', 'تم تحديث الكوبون بنجاح');
    }

    /**
     * Delete coupon.
     */
    public function destroyCoupon(Coupon $coupon)
    {
        if ($coupon->usages()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف كوبون تم استخدامه');
        }

        $coupon->delete();

        return redirect()->route('admin.marketing.coupons.index')
            ->with('success', 'تم حذف الكوبون بنجاح');
    }

    /**
     * Marketing analytics.
     */
    public function analytics()
    {
        $stats = [
            'total_campaigns' => MarketingCampaign::count(),
            'active_campaigns' => MarketingCampaign::where('status', 'active')->count(),
            'total_coupons' => Coupon::count(),
            'active_coupons' => Coupon::where('is_active', true)
                ->where('starts_at', '<=', now())
                ->where('expires_at', '>=', now())
                ->count(),
        ];

        $campaignStats = MarketingCampaign::selectRaw('
            status,
            COUNT(*) as count,
            SUM(budget) as total_budget
        ')
        ->groupBy('status')
        ->get();

        $couponStats = Coupon::selectRaw('
            type,
            COUNT(*) as count,
            SUM(value) as total_value
        ')
        ->groupBy('type')
        ->get();

        return view('admin.marketing.analytics', compact(
            'stats',
            'campaignStats',
            'couponStats'
        ));
    }
}

