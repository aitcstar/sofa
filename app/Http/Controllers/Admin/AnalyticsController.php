<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteAnalytics;
use App\Models\SecurityLog;
use App\Models\Order;
use App\Models\User;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AnalyticsExport;
class AnalyticsController extends Controller
{
    /**
     * Display website analytics dashboard
     */
    public function website(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period);
        $endDate = now();

        // إحصائيات عامة
        $stats = [
            'total_visitors' => WebsiteAnalytics::whereBetween('created_at', [$startDate, $endDate])
                                              ->distinct('session_id')
                                              ->count('session_id'),
            'page_views' => WebsiteAnalytics::whereBetween('created_at', [$startDate, $endDate])->count(),
            'bounce_rate' => WebsiteAnalytics::whereBetween('created_at', [$startDate, $endDate])
                                           ->where('is_bounce', true)
                                           ->count() / max(1, WebsiteAnalytics::whereBetween('created_at', [$startDate, $endDate])->count()) * 100,
            'avg_session_duration' => WebsiteAnalytics::whereBetween('created_at', [$startDate, $endDate])
                                                     ->whereNotNull('time_on_page')
                                                     ->avg('time_on_page'),
            'conversion_rate' => WebsiteAnalytics::whereBetween('created_at', [$startDate, $endDate])
                                                ->where('is_conversion', true)
                                                ->count() / max(1, WebsiteAnalytics::whereBetween('created_at', [$startDate, $endDate])->count()) * 100
        ];

        // الصفحات الأكثر زيارة
        $popularPages = WebsiteAnalytics::selectRaw('page_url, page_title, COUNT(*) as visits')
                                      ->whereBetween('created_at', [$startDate, $endDate])
                                      ->groupBy('page_url', 'page_title')
                                      ->orderBy('visits', 'desc')
                                      ->limit(10)
                                      ->get();

        // مصادر الزيارات
        $trafficSources = WebsiteAnalytics::selectRaw('
                                CASE
                                    WHEN utm_source IS NOT NULL THEN utm_source
                                    WHEN referrer_url IS NOT NULL THEN SUBSTRING_INDEX(SUBSTRING_INDEX(referrer_url, "/", 3), "//", -1)
                                    ELSE "مباشر"
                                END as source,
                                COUNT(*) as visits
                            ')
                                         ->whereBetween('created_at', [$startDate, $endDate])
                                         ->groupBy('source')
                                         ->orderBy('visits', 'desc')
                                         ->limit(10)
                                         ->get();

        // إحصائيات الأجهزة
        $deviceStats = WebsiteAnalytics::selectRaw('device_type, COUNT(*) as count')
                                     ->whereBetween('created_at', [$startDate, $endDate])
                                     ->groupBy('device_type')
                                     ->get();

        // إحصائيات المتصفحات
        $browserStats = WebsiteAnalytics::selectRaw('browser, COUNT(*) as count')
                                      ->whereBetween('created_at', [$startDate, $endDate])
                                      ->whereNotNull('browser')
                                      ->groupBy('browser')
                                      ->orderBy('count', 'desc')
                                      ->limit(10)
                                      ->get();

        // إحصائيات الدول
        $countryStats = WebsiteAnalytics::selectRaw('country, COUNT(*) as count')
                                      ->whereBetween('created_at', [$startDate, $endDate])
                                      ->whereNotNull('country')
                                      ->groupBy('country')
                                      ->orderBy('count', 'desc')
                                      ->limit(10)
                                      ->get();

        // أداء الحملات
        $campaignPerformance = WebsiteAnalytics::selectRaw('
                                    utm_campaign,
                                    utm_source,
                                    utm_medium,
                                    COUNT(*) as visits,
                                    COUNT(DISTINCT session_id) as unique_visitors,
                                    SUM(CASE WHEN is_conversion = 1 THEN 1 ELSE 0 END) as conversions,
                                    AVG(time_on_page) as avg_time_on_page
                                ')
                                              ->whereBetween('created_at', [$startDate, $endDate])
                                              ->whereNotNull('utm_campaign')
                                              ->groupBy('utm_campaign', 'utm_source', 'utm_medium')
                                              ->orderBy('visits', 'desc')
                                              ->get();

        // الزيارات اليومية (للرسم البياني)
        $dailyVisits = WebsiteAnalytics::selectRaw('DATE(created_at) as date, COUNT(DISTINCT session_id) as visitors, COUNT(*) as page_views')
                                     ->whereBetween('created_at', [$startDate, $endDate])
                                     ->groupBy('date')
                                     ->orderBy('date')
                                     ->get();

        return view('admin.analytics.website', compact(
            'stats', 'popularPages', 'trafficSources', 'deviceStats',
            'browserStats', 'countryStats', 'campaignPerformance', 'dailyVisits'
        ));
    }

    public function orders()
{
    // نطاق التاريخ (آخر 30 يوم مثلاً)
    $startDate = now()->subDays(30);
    $endDate = now();

    // إجمالي الطلبات
    $totalOrders = \App\Models\Order::count();

    // الطلبات المعلقة
    $pendingOrders = \App\Models\Order::where('status', 'pending')->count();

    // الطلبات المكتملة
    $completedOrders = \App\Models\Order::where('status', 'delivered')->count();

    // الطلبات الملغاة
    $canceledOrders = \App\Models\Order::where('status', 'cancelled')->count();

    // توزيع الطلبات حسب الحالة
    $orderStatuses = \App\Models\Order::select('status', \DB::raw('COUNT(*) as count'))
        ->groupBy('status')
        ->get();

    // أفضل العملاء حسب عدد الطلبات
    $topCustomers = \App\Models\User::whereHas('orders')
        ->withCount('orders')
        ->orderByDesc('orders_count')
        ->limit(5)
        ->get();

    // تمرير البيانات للعرض
    return view('admin.analytics.orders', [
        'stats' => [
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'completed_orders' => $completedOrders,
            'canceled_orders' => $canceledOrders,
        ],
        'orderStatuses' => $orderStatuses,
        'topCustomers' => $topCustomers,
    ]);
}



public function sales()
{
    // نطاق التاريخ (آخر 30 يوم)
    $startDate = now()->subDays(30);
    $endDate = now();

    // إجمالي المبيعات (المبالغ الإجمالية للطلبات المكتملة)
    $totalSales = \App\Models\Order::where('status', 'delivered')
        ->sum('total_amount');

    // متوسط قيمة الطلب الواحد
    $averageOrderValue = \App\Models\Order::where('status', 'delivered')
        ->avg('total_amount');

    // عدد الطلبات المكتملة
    $completedOrders = \App\Models\Order::where('status', 'delivered')->count();

    // المبيعات اليومية لآخر 30 يوم (لرسم بياني مثلاً)
    $dailySales = \App\Models\Order::where('status', 'delivered')
        ->whereBetween('delivered_at', [$startDate, $endDate])
        ->selectRaw('DATE(delivered_at) as date, SUM(total_amount) as total_sales')
        ->groupBy('date')
        ->orderBy('date')
        ->get();

    // أفضل العملاء من حيث إجمالي المبيعات
    $topCustomers = \App\Models\User::whereHas('orders', function ($q) {
            $q->where('status', 'delivered');
        })
        ->withSum(['orders as total_spent' => function ($q) {
            $q->where('status', 'delivered');
        }], 'total_amount')
        ->orderByDesc('total_spent')
        ->limit(5)
        ->get();

    // تمرير البيانات إلى الصفحة
    return view('admin.analytics.sales', [
        'totalSales' => $totalSales,
        'averageOrderValue' => $averageOrderValue,
        'completedOrders' => $completedOrders,
        'dailySales' => $dailySales,
        'topCustomers' => $topCustomers,
    ]);
}



public function customers()
{
    $startDate = now()->subDays(30);
    $endDate = now();

    // عدد العملاء الكلي
    $totalCustomers = \App\Models\User::where('role', 'customer')->count();

    // العملاء الجدد خلال آخر 30 يوم
    $newCustomers = \App\Models\User::where('role', 'customer')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count();

    // العملاء النشطين (الذين أجروا طلبات خلال الفترة)
    $activeCustomers = \App\Models\User::where('role', 'customer')
        ->whereHas('orders', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        })
        ->count();

    // أفضل العملاء من حيث الإنفاق الكلي
    $topCustomers = \App\Models\User::where('role', 'customer')
        ->withSum(['orders as total_spent' => function ($q) {
            $q->where('status', 'delivered');
        }], 'total_amount')
        ->orderByDesc('total_spent')
        ->limit(10)
        ->get();

    // العملاء الذين لم يطلبوا شيئًا منذ فترة طويلة (غير نشطين)
    $inactiveCustomers = \App\Models\User::where('role', 'customer')
        ->whereDoesntHave('orders', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        })
        ->orderBy('created_at', 'asc')
        ->limit(10)
        ->get();

    // معدل الاحتفاظ بالعملاء (Retention Rate)
    $previousPeriod = [$startDate->copy()->subDays(30), $startDate];
    $previousCustomers = \App\Models\User::where('role', 'customer')
        ->whereBetween('created_at', $previousPeriod)
        ->count();

    $retentionRate = $previousCustomers > 0
        ? ($activeCustomers / $previousCustomers) * 100
        : 0;

    // تمرير البيانات للواجهة
    return view('admin.analytics.customers', [
        'totalCustomers' => $totalCustomers,
        'newCustomers' => $newCustomers,
        'activeCustomers' => $activeCustomers,
        'inactiveCustomers' => $inactiveCustomers,
        'topCustomers' => $topCustomers,
        'retentionRate' => $retentionRate,
    ]);
}


public function performance(Request $request)
    {
        // تحديد نطاق التاريخ (من وإلى)
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth());

        // أداء الموظفين بناءً على عدد الطلبات المكتملة خلال الفترة المحددة
        $employeePerformance = User::where('role', 'employee')
            ->withCount(['assignedOrders as completed_orders' => function ($query) use ($startDate, $endDate) {
                $query->where('status', 'delivered')
                    ->whereBetween('delivered_at', [$startDate, $endDate]);
            }])
            ->orderBy('completed_orders', 'desc')
            ->limit(10)
            ->get();

        return view('admin.analytics.performance', compact('employeePerformance', 'startDate', 'endDate'));
    }

    public function trends(Request $request)
    {
        // تحديد نطاق السنة (افتراضي السنة الحالية)
        $year = $request->input('year', Carbon::now()->year);

        // عدد الطلبات لكل شهر في السنة المحددة
        $ordersTrend = Order::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total_orders')
            )
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // إجمالي المبيعات لكل شهر
        $salesTrend = Order::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_amount) as total_sales')
            )
            ->whereYear('created_at', $year)
            ->where('status', 'delivered')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // تجهيز بيانات للرسم البياني
        $months = range(1, 12);
        $ordersData = [];
        $salesData = [];

        foreach ($months as $m) {
            $ordersData[] = $ordersTrend->firstWhere('month', $m)->total_orders ?? 0;
            $salesData[] = $salesTrend->firstWhere('month', $m)->total_sales ?? 0;
        }

        return view('admin.analytics.trends', compact('year', 'months', 'ordersData', 'salesData'));
    }


   // صفحة عرض الفورم
   public function exportPage()
   {
       return view('admin.analytics.export');
   }

   // تحميل الملف مباشرة بعد اختيار النوع
   public function export(Request $request)
   {
       $type = $request->input('type', 'sales');
       $format = $request->input('format', 'excel'); // excel أو csv

       $fileName = '';
       $data = [];

       switch ($type) {
           case 'orders':
               $fileName = 'orders_report_' . now()->format('Y-m-d') . '.xlsx';
               $data = Order::select('id', 'user_id', 'status', 'total_amount', 'created_at')->get();
               break;

           case 'customers':
               $fileName = 'customers_report_' . now()->format('Y-m-d') . '.xlsx';
               $data = User::where('role', 'customer')
                   ->select('id', 'name', 'email', 'phone', 'created_at')
                   ->get();
               break;

           default:
               $fileName = 'sales_report_' . now()->format('Y-m-d') . '.xlsx';
               $data = Order::where('status', 'delivered')
                   ->select('id', 'user_id', 'total_amount', 'delivered_at')
                   ->get();
               break;
       }

       // تصدير الملف
       return Excel::download(new AnalyticsExport($data, $type), $fileName);
   }


    /**
     * Display security analytics dashboard
     */
    public function security(Request $request)
    {
        $period = $request->get('period', 'week');
        $startDate = $this->getStartDate($period);
        $endDate = now();

        // إحصائيات الأمان
        $stats = [
            'total_events' => SecurityLog::whereBetween('occurred_at', [$startDate, $endDate])->count(),
            'suspicious_events' => SecurityLog::suspicious()->whereBetween('occurred_at', [$startDate, $endDate])->count(),
            'failed_logins' => SecurityLog::byEventType('failed_login')->whereBetween('occurred_at', [$startDate, $endDate])->count(),
            'successful_logins' => SecurityLog::byEventType('login')->whereBetween('occurred_at', [$startDate, $endDate])->count(),
            'high_risk_events' => SecurityLog::byRiskLevel('high')->whereBetween('occurred_at', [$startDate, $endDate])->count(),
            'critical_events' => SecurityLog::byRiskLevel('critical')->whereBetween('occurred_at', [$startDate, $endDate])->count(),
            'unique_ips' => SecurityLog::whereBetween('occurred_at', [$startDate, $endDate])->distinct('ip_address')->count(),
            'blocked_attempts' => SecurityLog::where('is_suspicious', true)->whereBetween('occurred_at', [$startDate, $endDate])->count()
        ];

        // أنواع الأحداث الأمنية
        $eventTypes = SecurityLog::selectRaw('event_type, COUNT(*) as count')
                                ->whereBetween('occurred_at', [$startDate, $endDate])
                                ->groupBy('event_type')
                                ->orderBy('count', 'desc')
                                ->get();

        // مستويات المخاطر
        $riskLevels = SecurityLog::selectRaw('risk_level, COUNT(*) as count')
                                ->whereBetween('occurred_at', [$startDate, $endDate])
                                ->groupBy('risk_level')
                                ->get();

        // عناوين IP المشبوهة
        $suspiciousIps = SecurityLog::selectRaw('ip_address, COUNT(*) as suspicious_count, location')
                                   ->where('is_suspicious', true)
                                   ->whereBetween('occurred_at', [$startDate, $endDate])
                                   ->groupBy('ip_address', 'location')
                                   ->orderBy('suspicious_count', 'desc')
                                   ->limit(10)
                                   ->get();

        // الأحداث الأمنية الأخيرة
        $recentEvents = SecurityLog::with('user')
                                  ->whereBetween('occurred_at', [$startDate, $endDate])
                                  ->orderBy('occurred_at', 'desc')
                                  ->limit(20)
                                  ->get();

        // محاولات تسجيل الدخول الفاشلة حسب اليوم
        $dailyFailedLogins = SecurityLog::selectRaw('DATE(occurred_at) as date, COUNT(*) as failed_attempts')
                                       ->where('event_type', 'failed_login')
                                       ->whereBetween('occurred_at', [$startDate, $endDate])
                                       ->groupBy('date')
                                       ->orderBy('date')
                                       ->get();

        // المستخدمون الأكثر نشاطاً أمنياً
        $activeUsers = SecurityLog::selectRaw('user_id, users.name, COUNT(*) as event_count')
                                 ->join('users', 'security_logs.user_id', '=', 'users.id')
                                 ->whereBetween('occurred_at', [$startDate, $endDate])
                                 ->whereNotNull('user_id')
                                 ->groupBy('user_id', 'users.name')
                                 ->orderBy('event_count', 'desc')
                                 ->limit(10)
                                 ->get();

        return view('admin.analytics.security', compact(
            'stats', 'eventTypes', 'riskLevels', 'suspiciousIps',
            'recentEvents', 'dailyFailedLogins', 'activeUsers'
        ));
    }

    /**
     * Display business analytics dashboard
     */
    public function business(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period);
        $endDate = now();

        // إحصائيات الأعمال
        $stats = [
            'total_revenue' => Order::whereBetween('created_at', [$startDate, $endDate])
                                  ->where('payment_status', 'paid')
                                  ->sum('total_amount'),
            'total_orders' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
            'avg_order_value' => Order::whereBetween('created_at', [$startDate, $endDate])
                                    ->avg('total_amount'),
            'new_customers' => User::where('role', 'customer')
                                 ->whereBetween('created_at', [$startDate, $endDate])
                                 ->count(),
            'total_leads' => Lead::whereBetween('created_at', [$startDate, $endDate])->count(),
            'converted_leads' => Lead::won()->whereBetween('created_at', [$startDate, $endDate])->count(),
            'conversion_rate' => Lead::whereBetween('created_at', [$startDate, $endDate])->count() > 0
                               ? (Lead::won()->whereBetween('created_at', [$startDate, $endDate])->count() / Lead::whereBetween('created_at', [$startDate, $endDate])->count()) * 100
                               : 0,
            'pending_orders' => Order::where('status', 'pending')->count()
        ];

        // الإيرادات اليومية
        $dailyRevenue = Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue, COUNT(*) as orders')
                            ->whereBetween('created_at', [$startDate, $endDate])
                            ->where('payment_status', 'paid')
                            ->groupBy('date')
                            ->orderBy('date')
                            ->get();

        // أفضل الباكجات مبيعاً
        $topPackages = Order::selectRaw('packages.name, COUNT(*) as orders_count, SUM(orders.total_amount) as total_revenue')
                           ->join('packages', 'orders.package_id', '=', 'packages.id')
                           ->whereBetween('orders.created_at', [$startDate, $endDate])
                           ->groupBy('packages.id', 'packages.name')
                           ->orderBy('orders_count', 'desc')
                           ->limit(10)
                           ->get();

        // حالات الطلبات
        $orderStatuses = Order::selectRaw('status, COUNT(*) as count')
                             ->whereBetween('created_at', [$startDate, $endDate])
                             ->groupBy('status')
                             ->get();

        // أفضل العملاء
        $topCustomers = Order::selectRaw('users.name, users.email, COUNT(*) as orders_count, SUM(orders.total_amount) as total_spent')
                            ->join('users', 'orders.user_id', '=', 'users.id')
                            ->whereBetween('orders.created_at', [$startDate, $endDate])
                            ->groupBy('users.id', 'users.name', 'users.email')
                            ->orderBy('total_spent', 'desc')
                            ->limit(10)
                            ->get();

        // مصادر العملاء المحتملين
        $leadSources = Lead::selectRaw('source, COUNT(*) as count')
                          ->whereBetween('created_at', [$startDate, $endDate])
                          ->groupBy('source')
                          ->get();

        // أداء المبيعات الشهري
        $monthlySales = Order::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total_amount) as revenue, COUNT(*) as orders')
                            ->where('payment_status', 'paid')
                            ->whereBetween('created_at', [now()->subMonths(12), $endDate])
                            ->groupBy('year', 'month')
                            ->orderBy('year', 'desc')
                            ->orderBy('month', 'desc')
                            ->get();

        return view('admin.analytics.business', compact(
            'stats', 'dailyRevenue', 'topPackages', 'orderStatuses',
            'topCustomers', 'leadSources', 'monthlySales'
        ));
    }

    /**
     * Get analytics data for API
     */
    public function getWebsiteData(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period);
        $endDate = now();

        $data = [
            'visitors' => WebsiteAnalytics::whereBetween('created_at', [$startDate, $endDate])
                                        ->distinct('session_id')
                                        ->count('session_id'),
            'page_views' => WebsiteAnalytics::whereBetween('created_at', [$startDate, $endDate])->count(),
            'bounce_rate' => WebsiteAnalytics::getBounceRate(),
            'conversion_rate' => WebsiteAnalytics::getConversionRate(),
            'avg_time_on_page' => WebsiteAnalytics::getAverageTimeOnPage(),
            'popular_pages' => WebsiteAnalytics::getPopularPages(5),
            'traffic_sources' => WebsiteAnalytics::getTrafficSources(5),
            'device_stats' => WebsiteAnalytics::getDeviceStats(),
            'daily_visits' => WebsiteAnalytics::selectRaw('DATE(created_at) as date, COUNT(DISTINCT session_id) as visitors')
                                            ->whereBetween('created_at', [$startDate, $endDate])
                                            ->groupBy('date')
                                            ->orderBy('date')
                                            ->get()
        ];

        return response()->json($data);
    }

    /**
     * Get security data for API
     */
    public function getSecurityData(Request $request)
    {
        $period = $request->get('period', 'week');
        $startDate = $this->getStartDate($period);
        $endDate = now();

        $data = SecurityLog::getSecuritySummary(7);

        $data['daily_events'] = SecurityLog::selectRaw('DATE(occurred_at) as date, COUNT(*) as events')
                                          ->whereBetween('occurred_at', [$startDate, $endDate])
                                          ->groupBy('date')
                                          ->orderBy('date')
                                          ->get();

        return response()->json($data);
    }

    /**
     * Get business data for API
     */
    public function getBusinessData(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period);
        $endDate = now();

        $data = [
            'revenue' => Order::whereBetween('created_at', [$startDate, $endDate])
                             ->where('payment_status', 'paid')
                             ->sum('total_amount'),
            'orders' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
            'customers' => User::where('role', 'customer')
                              ->whereBetween('created_at', [$startDate, $endDate])
                              ->count(),
            'leads' => Lead::whereBetween('created_at', [$startDate, $endDate])->count(),
            'daily_revenue' => Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue')
                                   ->whereBetween('created_at', [$startDate, $endDate])
                                   ->where('payment_status', 'paid')
                                   ->groupBy('date')
                                   ->orderBy('date')
                                   ->get(),
            'order_statuses' => Order::selectRaw('status, COUNT(*) as count')
                                    ->whereBetween('created_at', [$startDate, $endDate])
                                    ->groupBy('status')
                                    ->get()
        ];

        return response()->json($data);
    }

    /**
     * Track website analytics
     */
    public function track(Request $request)
    {
        $data = [
            'session_id' => $request->get('session_id'),
            'user_id' => auth()->id(),
            'page_url' => $request->get('page_url'),
            'page_title' => $request->get('page_title'),
            'referrer_url' => $request->get('referrer_url'),
            'utm_source' => $request->get('utm_source'),
            'utm_medium' => $request->get('utm_medium'),
            'utm_campaign' => $request->get('utm_campaign'),
            'utm_term' => $request->get('utm_term'),
            'utm_content' => $request->get('utm_content'),
            'device_type' => $this->getDeviceType($request->userAgent()),
            'browser' => $this->getBrowser($request->userAgent()),
            'os' => $this->getOS($request->userAgent()),
            'ip_address' => $request->ip(),
            'time_on_page' => $request->get('time_on_page'),
            'is_bounce' => $request->get('is_bounce', false),
            'is_conversion' => $request->get('is_conversion', false)
        ];

        WebsiteAnalytics::track($data);

        return response()->json(['success' => true]);
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

    private function getDeviceType($userAgent)
    {
        if (strpos($userAgent, 'Mobile') !== false) {
            return 'mobile';
        } elseif (strpos($userAgent, 'Tablet') !== false) {
            return 'tablet';
        }
        return 'desktop';
    }

    private function getBrowser($userAgent)
    {
        if (strpos($userAgent, 'Chrome') !== false) {
            return 'Chrome';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            return 'Firefox';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            return 'Safari';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            return 'Edge';
        } elseif (strpos($userAgent, 'Opera') !== false) {
            return 'Opera';
        }
        return 'Unknown';
    }

    private function getOS($userAgent)
    {
        if (strpos($userAgent, 'Windows') !== false) {
            return 'Windows';
        } elseif (strpos($userAgent, 'Mac') !== false) {
            return 'macOS';
        } elseif (strpos($userAgent, 'Linux') !== false) {
            return 'Linux';
        } elseif (strpos($userAgent, 'Android') !== false) {
            return 'Android';
        } elseif (strpos($userAgent, 'iOS') !== false) {
            return 'iOS';
        }
        return 'Unknown';
    }
}
