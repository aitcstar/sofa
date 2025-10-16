<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Package;
use App\Models\Product;
use App\Models\Contact;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\OrderLog;
use App\Models\ScheduledTask;

class DashboardController extends Controller
{
    private $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display the admin dashboard
     */
    public function index(Request $request)
    {
        $period = $request->get('period', 'month'); // day, week, month, year
        $startDate = $this->getStartDate($period);
        $endDate = now();

        // الإحصائيات الأساسية
        $stats = $this->getBasicStats($startDate, $endDate);

        // إحصائيات الطلبات
        $orderStats = $this->getOrderStats($startDate, $endDate);

        // الإحصائيات المالية
        $financialStats = $this->getFinancialStats($startDate, $endDate);

        // إحصائيات الأداء
        $performanceStats = $this->getPerformanceStats($startDate, $endDate);

        // الطلبات الحديثة
        $recentOrders = Order::with(['user', 'package', 'assignedEmployee'])
                            ->orderBy('created_at', 'desc')
                            ->limit(10)
                            ->get();

        // الطلبات المتأخرة
        $overdueOrders = Order::overdue()
                             ->with(['user', 'package', 'assignedEmployee'])
                             ->orderBy('expected_delivery_date', 'asc')
                             ->limit(5)
                             ->get();

        // الطلبات عالية الأولوية
        $highPriorityOrders = Order::highPriority()
                                  ->whereNotIn('status', ['delivered', 'archived'])
                                  ->with(['user', 'package', 'assignedEmployee'])
                                  ->orderBy('priority', 'desc')
                                  ->limit(5)
                                  ->get();

          // استرجاع أحدث الطلبات
          $recent_orders = Order::latest()->take(5)->get();


        // الإشعارات غير المقروءة
        $unreadNotifications = $this->notificationService->getUnreadNotifications(auth()->user(), 5);

        // بيانات الرسوم البيانية
        $chartData = $this->getChartData($period);


        // أحدث العملاء
        $recent_users = User::latest()->take(5)->get();

        // عدد الطلبات (اليوم/الشهر/السنة)
        $today_orders = Order::whereDate('created_at', Carbon::today())->count();
        $month_orders = Order::whereMonth('created_at', Carbon::now()->month)->count();
        $year_orders = Order::whereYear('created_at', Carbon::now()->year)->count();

        // أكثر الباكجات مبيعاً
        $top_packages = Package::withCount('orders')
            ->orderByDesc('orders_count')
            ->take(5)
            ->get();

        // عدد العملاء الجدد (آخر 7 أيام)
        $new_users = User::where('created_at', '>=', Carbon::now()->subDays(7))->count();

        // نسبة الإنجاز
        $completion_rate = $this->getOrderCompletionRate($startDate, $endDate);

        // حالة الطلبات
        $orders_status = [
            'in_progress' => Order::where('status', 'processing')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
        ];

        // بيانات المبيعات للرسم البياني
        $salesMonths = ['يناير','فبراير','مارس','أبريل','مايو','يونيو'];
        $salesData = $this->getSalesData();

        // حالات الطلبات للرسم البياني
        $ordersStatusData = [
            'pending' => Order::where('status', 'pending')->count(),
            'confirmed' => Order::where('status', 'confirmed')->count(),
            'in_progress' => Order::where('status', 'processing')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'archived' => Order::where('status', 'archived')->count(),
        ];






        // Get recent notifications
        $recentNotifications = Notification::with('notifiable')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get pending tasks
        $pendingTasks = ScheduledTask::where('status', 'pending')
            ->where('scheduled_at', '<=', now())
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();

        // Get revenue data for chart
        $revenueData = $this->getRevenueData();

        // Get order status distribution
        $statusDistribution = $this->getOrderStatusDistribution();

        // Get top packages
        $topPackages = $this->getTopPackages();

        // Get employee performance
        $employeePerformance = $this->getEmployeePerformance();



        return view('admin.dashboard', compact(
            'stats',
            'orderStats',
            'financialStats',
            'performanceStats',
            'recentOrders',
            'overdueOrders',
            'highPriorityOrders',
            'unreadNotifications',
            'chartData',
            'period',
            'recent_users',
            'today_orders',
            'month_orders',
            'year_orders',
            'top_packages',
            'orders_status',
            'new_users',
            'completion_rate',
            'salesMonths',
            'salesData',
            'ordersStatusData',
            'recent_orders',
            'stats',
            'recentOrders',
            'overdueOrders',
            'highPriorityOrders',
            'recentNotifications',
            'pendingTasks',
            'revenueData',
            'statusDistribution',
            'topPackages',
            'employeePerformance'
        ));
    }

    /**
     * Get basic statistics
     */
    private function getBasicStats($startDate, $endDate)
    {
        return [
            'total_orders' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_customers' => User::where('role', 'customer')
                                   ->whereBetween('created_at', [$startDate, $endDate])
                                   ->count(),
            'total_revenue' => Payment::where('status', 'completed')
                                    ->whereBetween('payment_date', [$startDate, $endDate])
                                    ->sum('amount'),
            'active_employees' => User::where('role', 'employee')
                                    ->where('is_active', true)
                                    ->count(),
            'total_users' => User::count(),
            'total_packages' => Package::count(),
            'total_products' => Product::count(),
            'total_sales' => Order::where('status', 'delivered')->sum('total_amount'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'new_contacts' => Contact::where('status', 'new')->count(),
        ];
    }

    /**
     * Get order statistics
     */
    private function getOrderStats($startDate, $endDate)
    {
        $baseQuery = Order::whereBetween('created_at', [$startDate, $endDate]);

        return [
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
            'confirmed' => (clone $baseQuery)->where('status', 'confirmed')->count(),
            'in_progress' => (clone $baseQuery)->where('status', 'processing')->count(),
            'shipped' => (clone $baseQuery)->where('status', 'shipped')->count(),
            'delivered' => (clone $baseQuery)->where('status', 'delivered')->count(),
            'cancelled' => (clone $baseQuery)->where('status', 'cancelled')->count(),
            'overdue' => Order::overdue()->count(),
            'high_priority' => Order::highPriority()
                                   ->whereNotIn('status', ['delivered', 'archived'])
                                   ->count()
        ];
    }

    /**
     * Get financial statistics
     */
    private function getFinancialStats($startDate, $endDate)
    {
        $totalRevenue = Payment::where('status', 'completed')
                              ->whereBetween('payment_date', [$startDate, $endDate])
                              ->sum('amount');

        $pendingPayments = Order::where('payment_status', '!=', 'paid')
                               ->whereBetween('created_at', [$startDate, $endDate])
                               ->sum('total_amount');

        $paidOrders = Order::where('payment_status', 'paid')
                          ->whereBetween('created_at', [$startDate, $endDate])
                          ->count();

        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();

        return [
            'total_revenue' => $totalRevenue ?: 0,
            'pending_payments' => $pendingPayments ?: 0,
            'average_order_value' => $totalOrders > 0 ? $totalRevenue / $totalOrders : 0,
            'payment_completion_rate' => $totalOrders > 0 ? ($paidOrders / $totalOrders) * 100 : 0,
            'outstanding_invoices' => Invoice::where('status', 'sent')
                                            ->whereBetween('issue_date', [$startDate->toDateString(), $endDate->toDateString()])
                                            ->count(),
            'overdue_invoices' => Invoice::where('status', 'overdue')
                                        ->whereBetween('issue_date', [$startDate->toDateString(), $endDate->toDateString()])
                                        ->count()
        ];
    }

    /**
     * Get performance statistics
     */
    private function getPerformanceStats($startDate, $endDate)
    {
        $deliveredOrders = Order::where('status', 'delivered')
                               ->whereBetween('delivered_at', [$startDate, $endDate])
                               ->get();

        $avgDeliveryTime = 0;
        $onTimeDeliveries = 0;

        if ($deliveredOrders->count() > 0) {
            $totalDeliveryTime = 0;

            foreach ($deliveredOrders as $order) {
                if ($order->delivered_at && $order->created_at) {
                    $deliveryTime = $order->created_at->diffInDays($order->delivered_at);
                    $totalDeliveryTime += $deliveryTime;

                    // فحص التسليم في الوقت المحدد
                    if ($order->expected_delivery_date && $order->delivered_at <= $order->expected_delivery_date) {
                        $onTimeDeliveries++;
                    }
                }
            }

            $avgDeliveryTime = $totalDeliveryTime / $deliveredOrders->count();
        }

        $onTimeRate = $deliveredOrders->count() > 0 ? ($onTimeDeliveries / $deliveredOrders->count()) * 100 : 0;

        // أداء الموظفين
        $employeePerformance = User::where('role', 'employee')
                                 ->withCount(['assignedOrders as completed_orders' => function($query) use ($startDate, $endDate) {
                                     $query->where('status', 'delivered')
                                           ->whereBetween('delivered_at', [$startDate, $endDate]);
                                 }])
                                 ->orderBy('completed_orders', 'desc')
                                 ->limit(5)
                                 ->get();

        return [
            'avg_delivery_time' => round($avgDeliveryTime, 1),
            'on_time_delivery_rate' => round($onTimeRate, 1),
            'customer_satisfaction' => 85, // يمكن حسابها من التقييمات
            'order_completion_rate' => $this->getOrderCompletionRate($startDate, $endDate),
            'top_employees' => $employeePerformance
        ];
    }

    /**
     * Get chart data for visualizations
     */
    private function getChartData($period)
    {
        $data = [];

        // بيانات الطلبات حسب الوقت
        $data['orders_timeline'] = $this->getOrdersTimelineData($period);

        // بيانات الإيرادات حسب الوقت
        $data['revenue_timeline'] = $this->getRevenueTimelineData($period);

        // توزيع حالات الطلبات
        $data['order_status_distribution'] = $this->getOrderStatusDistribution();

        // أكثر الباكجات مبيعاً
        $data['top_packages'] = $this->getTopPackages();

        // توزيع طرق الدفع
        $data['payment_methods'] = $this->getPaymentMethodsDistribution();

        // نمو العملاء
        $data['customer_growth'] = $this->getCustomerGrowthData($period);

        return $data;
    }

    /**
     * Get sales data for chart
     */
    private function getSalesData()
    {
        $months = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M');

            $sales = Payment::where('status', 'completed')
                           ->whereYear('payment_date', $date->year)
                           ->whereMonth('payment_date', $date->month)
                           ->sum('amount');

            $data[] = (float) $sales;
        }

        return $data;
    }

    /**
     * Get orders timeline data
     */
    private function getOrdersTimelineData($period)
    {
        $startDate = $this->getStartDate($period);
        $format = $this->getDateFormat($period);

        return Order::selectRaw("DATE_FORMAT(created_at, '{$format}') as date, COUNT(*) as count")
                   ->where('created_at', '>=', $startDate)
                   ->groupBy('date')
                   ->orderBy('date')
                   ->get()
                   ->map(function($item) {
                       return [
                           'date' => $item->date,
                           'count' => $item->count
                       ];
                   });
    }

    /**
     * Get revenue timeline data
     */
    private function getRevenueTimelineData($period)
    {
        $startDate = $this->getStartDate($period);
        $format = $this->getDateFormat($period);

        return Payment::selectRaw("DATE_FORMAT(payment_date, '{$format}') as date, SUM(amount) as total")
                     ->where('status', 'completed')
                     ->where('payment_date', '>=', $startDate)
                     ->groupBy('date')
                     ->orderBy('date')
                     ->get()
                     ->map(function($item) {
                         return [
                             'date' => $item->date,
                             'total' => (float) $item->total
                         ];
                     });
    }

    /**
     * Get order status distribution
     */
    private function getOrderStatusDistribution()
    {
        return Order::selectRaw('status, COUNT(*) as count')
                   ->groupBy('status')
                   ->get()
                   ->map(function($item) {
                       return [
                           'status' => $item->status,
                           'status_text' => (new Order(['status' => $item->status]))->status_text,
                           'count' => $item->count,
                           'color' => (new Order(['status' => $item->status]))->status_color
                       ];
                   });
    }

    /**
     * Get top selling packages
     */
    private function getTopPackages()
    {
        return Package::withCount('orders')
                     ->orderBy('orders_count', 'desc')
                     ->limit(10)
                     ->get()
                     ->map(function($package) {
                         return [
                             'name' => $package->name,
                             'count' => $package->orders_count
                         ];
                     });
    }

    /**
     * Get payment methods distribution
     */
    private function getPaymentMethodsDistribution()
    {
        return Payment::selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total')
                     ->where('status', 'completed')
                     ->groupBy('payment_method')
                     ->get()
                     ->map(function($item) {
                         return [
                             'method' => $item->payment_method,
                             'method_text' => (new Payment(['payment_method' => $item->payment_method]))->payment_method_text,
                             'count' => $item->count,
                             'total' => (float) $item->total
                         ];
                     });
    }

    /**
     * Get customer growth data
     */
    private function getCustomerGrowthData($period)
    {
        $startDate = $this->getStartDate($period);
        $format = $this->getDateFormat($period);

        return User::selectRaw("DATE_FORMAT(created_at, '{$format}') as date, COUNT(*) as count")
                  ->where('role', 'customer')
                  ->where('created_at', '>=', $startDate)
                  ->groupBy('date')
                  ->orderBy('date')
                  ->get()
                  ->map(function($item) {
                      return [
                          'date' => $item->date,
                          'count' => $item->count
                      ];
                  });
    }

    /**
     * Get order completion rate
     */
    private function getOrderCompletionRate($startDate, $endDate)
    {
        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $completedOrders = Order::where('status', 'delivered')
                               ->whereBetween('created_at', [$startDate, $endDate])
                               ->count();

        return $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 1) : 0;
    }

    /**
     * Get start date based on period
     */
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
     * Get date format for grouping
     */
    private function getDateFormat($period)
    {
        return match($period) {
            'day' => '%Y-%m-%d %H:00',
            'week' => '%Y-%m-%d',
            'month' => '%Y-%m-%d',
            'year' => '%Y-%m',
            default => '%Y-%m-%d'
        };
    }

    /**
     * Export dashboard data
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'pdf');
        $period = $request->get('period', 'month');

        $startDate = $this->getStartDate($period);
        $endDate = now();

        $data = [
            'stats' => $this->getBasicStats($startDate, $endDate),
            'orderStats' => $this->getOrderStats($startDate, $endDate),
            'financialStats' => $this->getFinancialStats($startDate, $endDate),
            'performanceStats' => $this->getPerformanceStats($startDate, $endDate),
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        if ($format === 'pdf') {
            return $this->exportToPdf($data);
        } else {
            return $this->exportToExcel($data);
        }
    }

    /**
     * Get real-time statistics (AJAX)
     */
    public function getRealTimeStats()
    {
        return response()->json([
            'pending_orders' => Order::pending()->count(),
            'overdue_orders' => Order::overdue()->count(),
            'unread_notifications' => $this->notificationService->getUnreadCount(auth()->user()),
            'today_revenue' => Payment::where('status', 'completed')
                                    ->whereDate('payment_date', today())
                                    ->sum('amount'),
            'today_orders' => Order::whereDate('created_at', today())->count()
        ]);
    }

    /**
     * Get notifications for dashboard
     */
    public function getNotifications()
    {
        $notifications = $this->notificationService->getUnreadNotifications(auth()->user(), 10);

        return response()->json([
            'notifications' => $notifications,
            'count' => $notifications->count()
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead(Request $request, $id)
    {
        $notification = Notification::findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead()
    {
        $this->notificationService->markAllAsRead(auth()->user());

        return response()->json(['success' => true]);
    }

    // Helper methods for export
    private function exportToPdf($data)
    {
        // تنفيذ تصدير PDF للتقرير
    }

    private function exportToExcel($data)
    {
        // تنفيذ تصدير Excel للتقرير
    }



     /**
     * Get basic statistics.
     */
    public function getStats()
    {
        $stats = $this->getBasicStats();
        return response()->json($stats);
    }

    /**
     * Get chart data.
     */
    public function getCharts(Request $request)
    {
        $period = $request->get('period', '30'); // days

        $charts = [
            'revenue' => $this->getRevenueChart($period),
            'orders' => $this->getOrdersChart($period),
            'status_distribution' => $this->getOrderStatusDistribution(),
            'package_performance' => $this->getPackagePerformanceChart($period),
            'employee_workload' => $this->getEmployeeWorkloadChart(),
        ];

        return response()->json($charts);
    }

    /**
     * Get widgets data for AJAX updates.
     */
    public function widgets()
    {
        return response()->json([
            'stats' => $this->getBasicStats(),
            'recent_orders_count' => Order::whereDate('created_at', today())->count(),
            'overdue_orders_count' => Order::overdue()->count(),
            'pending_notifications' => Notification::where('status', 'pending')->count(),
            'active_tasks' => ScheduledTask::where('status', 'active')->count(),
        ]);
    }

    /**
     * Get basic statistics.
     */
    /*
    private function getBasicStats(): array
    {
        $today = today();
        $thisWeek = [now()->startOfWeek(), now()->endOfWeek()];
        $thisMonth = [now()->startOfMonth(), now()->endOfMonth()];
        $lastMonth = [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()];

        // Orders statistics
        $totalOrders = Order::count();
        $todayOrders = Order::whereDate('created_at', $today)->count();
        $weekOrders = Order::whereBetween('created_at', $thisWeek)->count();
        $monthOrders = Order::whereBetween('created_at', $thisMonth)->count();
        $lastMonthOrders = Order::whereBetween('created_at', $lastMonth)->count();

        // Revenue statistics
        $totalRevenue = Order::where('status', 'delivered')->sum('total_amount');
        $todayRevenue = Order::where('status', 'delivered')
            ->whereDate('delivered_at', $today)
            ->sum('total_amount');
        $monthRevenue = Order::where('status', 'delivered')
            ->whereBetween('delivered_at', $thisMonth)
            ->sum('total_amount');
        $lastMonthRevenue = Order::where('status', 'delivered')
            ->whereBetween('delivered_at', $lastMonth)
            ->sum('total_amount');

        // Status counts
        $pendingOrders = Order::where('status', 'pending')->count();
        $confirmedOrders = Order::where('status', 'confirmed')->count();
        $processingOrders = Order::where('status', 'processing')->count();
        $shippedOrders = Order::where('status', 'shipped')->count();
        $deliveredOrders = Order::where('status', 'delivered')->count();
        $overdueOrders = Order::overdue()->count();

        // Customer statistics
        $totalCustomers = User::where('role', 'customer')->count();
        $newCustomersThisMonth = User::where('role', 'customer')
            ->whereBetween('created_at', $thisMonth)
            ->count();

        // Employee statistics
        $totalEmployees = User::where('role', 'employee')->count();
        $activeEmployees = User::where('role', 'employee')
            ->where('is_active', true)
            ->count();

        // Calculate growth percentages
        $orderGrowth = $lastMonthOrders > 0
            ? round((($monthOrders - $lastMonthOrders) / $lastMonthOrders) * 100, 1)
            : 0;

        $revenueGrowth = $lastMonthRevenue > 0
            ? round((($monthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : 0;

        return [
            'orders' => [
                'total' => $totalOrders,
                'today' => $todayOrders,
                'week' => $weekOrders,
                'month' => $monthOrders,
                'growth' => $orderGrowth,
                'pending' => $pendingOrders,
                'confirmed' => $confirmedOrders,
                'processing' => $processingOrders,
                'shipped' => $shippedOrders,
                'delivered' => $deliveredOrders,
                'overdue' => $overdueOrders,
            ],
            'revenue' => [
                'total' => $totalRevenue,
                'today' => $todayRevenue,
                'month' => $monthRevenue,
                'growth' => $revenueGrowth,
                'average_order' => $deliveredOrders > 0 ? round($totalRevenue / $deliveredOrders, 2) : 0,
            ],
            'customers' => [
                'total' => $totalCustomers,
                'new_this_month' => $newCustomersThisMonth,
            ],
            'employees' => [
                'total' => $totalEmployees,
                'active' => $activeEmployees,
            ],
            'performance' => [
                'completion_rate' => $totalOrders > 0 ? round(($deliveredOrders / $totalOrders) * 100, 1) : 0,
                'on_time_delivery' => $this->getOnTimeDeliveryRate(),
                'customer_satisfaction' => 95, // Placeholder - would come from reviews/feedback
            ],
        ];
    }*/

    /**
     * Get revenue data for charts.
     */
    private function getRevenueData(): array
    {
        $last30Days = collect();

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $revenue = Order::where('status', 'delivered')
                ->whereDate('delivered_at', $date)
                ->sum('total_amount');

            $last30Days->push([
                'date' => $date->format('Y-m-d'),
                'revenue' => $revenue,
            ]);
        }

        return $last30Days->toArray();
    }

    /**
     * Get order status distribution.
     */
   /* private function getOrderStatusDistribution(): array
    {
        return Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status => $item->count];
            })
            ->toArray();
    }*/

    /**
     * Get top packages.
     */
    /*private function getTopPackages(): array
    {
        return Package::withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($package) {
                return [
                    'name' => $package->name,
                    'orders_count' => $package->orders_count,
                    'revenue' => Order::where('package_id', $package->id)
                        ->where('status', 'delivered')
                        ->sum('total_amount'),
                ];
            })
            ->toArray();
    }*/

    /**
     * Get employee performance.
     */
    private function getEmployeePerformance(): array
    {
        return User::where('role', 'employee')
            ->withCount([
                'assignedOrders',
                'assignedOrders as completed_orders_count' => function ($query) {
                    $query->where('status', 'delivered');
                },
                'assignedOrders as overdue_orders_count' => function ($query) {
                    $query->where('expected_delivery_date', '<', now())
                          ->whereNotIn('status', ['delivered', 'cancelled', 'archived']);
                }
            ])
            ->get()
            ->map(function ($employee) {
                $completionRate = $employee->assigned_orders_count > 0
                    ? round(($employee->completed_orders_count / $employee->assigned_orders_count) * 100, 1)
                    : 0;

                return [
                    'name' => $employee->name,
                    'assigned_orders' => $employee->assigned_orders_count,
                    'completed_orders' => $employee->completed_orders_count,
                    'overdue_orders' => $employee->overdue_orders_count,
                    'completion_rate' => $completionRate,
                ];
            })
            ->toArray();
    }

    /**
     * Get revenue chart data.
     */
    private function getRevenueChart(int $days): array
    {
        $data = collect();

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $revenue = Order::where('status', 'delivered')
                ->whereDate('delivered_at', $date)
                ->sum('total_amount');

            $data->push([
                'date' => $date->format('M d'),
                'revenue' => $revenue,
            ]);
        }

        return $data->toArray();
    }

    /**
     * Get orders chart data.
     */
    private function getOrdersChart(int $days): array
    {
        $data = collect();

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $orders = Order::whereDate('created_at', $date)->count();

            $data->push([
                'date' => $date->format('M d'),
                'orders' => $orders,
            ]);
        }

        return $data->toArray();
    }

    /**
     * Get package performance chart.
     */
    private function getPackagePerformanceChart(int $days): array
    {
        $startDate = now()->subDays($days);

        return Package::withCount([
            'orders as recent_orders_count' => function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }
        ])
        ->orderBy('recent_orders_count', 'desc')
        ->limit(10)
        ->get()
        ->map(function ($package) {
            return [
                'name' => $package->name,
                'orders' => $package->recent_orders_count,
            ];
        })
        ->toArray();
    }

    /**
     * Get employee workload chart.
     */
    private function getEmployeeWorkloadChart(): array
    {
        return User::where('role', 'employee')
            ->withCount([
                'assignedOrders as active_orders_count' => function ($query) {
                    $query->whereNotIn('status', ['delivered', 'cancelled', 'archived']);
                }
            ])
            ->get()
            ->map(function ($employee) {
                return [
                    'name' => $employee->name,
                    'workload' => $employee->active_orders_count,
                ];
            })
            ->toArray();
    }

    /**
     * Get on-time delivery rate.
     */
    private function getOnTimeDeliveryRate(): float
    {
        $deliveredOrders = Order::where('status', 'delivered')
            ->whereNotNull('expected_delivery_date')
            ->whereNotNull('delivered_at')
            ->get();

        if ($deliveredOrders->isEmpty()) {
            return 0;
        }

        $onTimeOrders = $deliveredOrders->filter(function ($order) {
            return $order->delivered_at <= $order->expected_delivery_date;
        });

        return round(($onTimeOrders->count() / $deliveredOrders->count()) * 100, 1);
    }

    /**
     * Get system health status.
     */
    public function getSystemHealth(): array
    {
        return [
            'database' => $this->checkDatabaseHealth(),
            'storage' => $this->checkStorageHealth(),
            'notifications' => $this->checkNotificationHealth(),
            'tasks' => $this->checkTasksHealth(),
        ];
    }

    /**
     * Check database health.
     */
    private function checkDatabaseHealth(): array
    {
        try {
            DB::connection()->getPdo();
            $orderCount = Order::count();

            return [
                'status' => 'healthy',
                'message' => 'Database connection is working',
                'orders_count' => $orderCount,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Database connection failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check storage health.
     */
    private function checkStorageHealth(): array
    {
        $storagePath = storage_path();
        $freeBytes = disk_free_space($storagePath);
        $totalBytes = disk_total_space($storagePath);

        $freeGB = round($freeBytes / 1024 / 1024 / 1024, 2);
        $totalGB = round($totalBytes / 1024 / 1024 / 1024, 2);
        $usedPercent = round((($totalBytes - $freeBytes) / $totalBytes) * 100, 1);

        $status = $usedPercent > 90 ? 'warning' : ($usedPercent > 95 ? 'error' : 'healthy');

        return [
            'status' => $status,
            'free_space' => $freeGB . ' GB',
            'total_space' => $totalGB . ' GB',
            'used_percent' => $usedPercent,
        ];
    }

    /**
     * Check notification health.
     */
    private function checkNotificationHealth(): array
    {
        $pendingCount = Notification::where('status', 'pending')->count();
        $failedCount = Notification::where('status', 'failed')->count();

        $status = $failedCount > 10 ? 'warning' : ($failedCount > 50 ? 'error' : 'healthy');

        return [
            'status' => $status,
            'pending_notifications' => $pendingCount,
            'failed_notifications' => $failedCount,
        ];
    }

    /**
     * Check scheduled tasks health.
     */
    private function checkTasksHealth(): array
    {
        $overdueCount = ScheduledTask::where('status', 'active')
            ->where('next_run_at', '<', now()->subHour())
            ->count();

        $failedCount = ScheduledTask::where('status', 'failed')->count();

        $status = $overdueCount > 5 || $failedCount > 10 ? 'warning' : 'healthy';

        return [
            'status' => $status,
            'overdue_tasks' => $overdueCount,
            'failed_tasks' => $failedCount,
        ];
    }
}
