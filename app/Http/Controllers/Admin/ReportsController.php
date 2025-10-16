<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    /**
     * Display reports dashboard
     */
    public function index()
    {
        return view('admin.reports.index');
    }

    /**
     * Orders Report
     */
    public function orders(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        $status = $request->get('status');
        $employee = $request->get('employee');
        $package = $request->get('package');

        $query = Order::with(['user', 'package', 'assignedEmployee'])
                     ->whereBetween('created_at', [$startDate, $endDate]);

        if ($status) {
            $query->where('status', $status);
        }

        if ($employee) {
            $query->where('assigned_to', $employee);
        }

        if ($package) {
            $query->where('package_id', $package);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        // إحصائيات الطلبات
        $stats = [
            'total_orders' => $orders->count(),
            'total_value' => $orders->sum('total_amount'),
            'avg_order_value' => $orders->count() > 0 ? $orders->avg('total_amount') : 0,
            'status_breakdown' => $orders->groupBy('status')->map->count(),
            'package_breakdown' => $orders->groupBy('package.name')->map->count(),
            'employee_breakdown' => $orders->groupBy('assignedEmployee.name')->map->count()
        ];

        // بيانات الرسم البياني
        $chartData = $this->getOrdersChartData($orders);

        $employees = User::where('role', 'employee')->get();
        $packages = Package::all();

        return view('admin.reports.orders', compact(
            'orders', 'stats', 'chartData', 'employees', 'packages',
            'startDate', 'endDate', 'status', 'employee', 'package'
        ));
    }

    /**
     * Financial Report
     */
    public function financial(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        $paymentMethod = $request->get('payment_method');

        // الإيرادات
        $revenueQuery = Payment::where('status', 'completed')
                              ->whereBetween('payment_date', [$startDate, $endDate]);

        if ($paymentMethod) {
            $revenueQuery->where('payment_method', $paymentMethod);
        }

        $payments = $revenueQuery->with('order')->get();

        // إحصائيات مالية
        $stats = [
            'total_revenue' => $payments->sum('amount'),
            'total_payments' => $payments->count(),
            'avg_payment' => $payments->count() > 0 ? $payments->avg('amount') : 0,
            'payment_methods' => $payments->groupBy('payment_method')->map->count(),
            'monthly_revenue' => $this->getMonthlyRevenue($startDate, $endDate),
            'pending_payments' => Order::where('payment_status', '!=', 'paid')
                                      ->whereBetween('created_at', [$startDate, $endDate])
                                      ->sum('total_amount'),
            'overdue_invoices' => Invoice::where('status', 'overdue')
                                        ->whereBetween('issue_date', [$startDate, $endDate])
                                        ->count()
        ];

        // بيانات الرسم البياني
        $chartData = $this->getFinancialChartData($payments, $startDate, $endDate);

        return view('admin.reports.financial', compact(
            'payments', 'stats', 'chartData', 'startDate', 'endDate', 'paymentMethod'
        ));
    }

    /**
     * Performance Report
     */
    public function performance(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        // أداء الموظفين
        $employeePerformance = User::where('role', 'employee')
                                 ->withCount([
                                     'assignedOrders as total_orders' => function($query) use ($startDate, $endDate) {
                                         $query->whereBetween('created_at', [$startDate, $endDate]);
                                     },
                                     'assignedOrders as completed_orders' => function($query) use ($startDate, $endDate) {
                                         $query->where('status', 'delivered')
                                               ->whereBetween('delivered_at', [$startDate, $endDate]);
                                     },
                                     'assignedOrders as overdue_orders' => function($query) {
                                         $query->where('expected_delivery_date', '<', now())
                                               ->whereNotIn('status', ['delivered', 'archived', 'cancelled']);
                                     }
                                 ])
                                 ->get()
                                 ->map(function($employee) {
                                     $employee->completion_rate = $employee->total_orders > 0 
                                         ? ($employee->completed_orders / $employee->total_orders) * 100 
                                         : 0;
                                     return $employee;
                                 });

        // إحصائيات الأداء العامة
        $stats = [
            'avg_delivery_time' => $this->getAverageDeliveryTime($startDate, $endDate),
            'on_time_delivery_rate' => $this->getOnTimeDeliveryRate($startDate, $endDate),
            'order_completion_rate' => $this->getOrderCompletionRate($startDate, $endDate),
            'customer_satisfaction' => 85, // يمكن حسابها من التقييمات
            'total_employees' => $employeePerformance->count(),
            'active_employees' => $employeePerformance->where('is_active', true)->count()
        ];

        // بيانات الرسم البياني
        $chartData = $this->getPerformanceChartData($employeePerformance, $startDate, $endDate);

        return view('admin.reports.performance', compact(
            'employeePerformance', 'stats', 'chartData', 'startDate', 'endDate'
        ));
    }

    /**
     * Customers Report
     */
    public function customers(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        // العملاء الجدد
        $newCustomers = User::where('role', 'customer')
                           ->whereBetween('created_at', [$startDate, $endDate])
                           ->withCount('orders')
                           ->with(['orders' => function($query) {
                               $query->latest()->limit(1);
                           }])
                           ->get();

        // أفضل العملاء (حسب قيمة الطلبات)
        $topCustomers = User::where('role', 'customer')
                           ->withSum('orders', 'total_amount')
                           ->withCount('orders')
                           ->orderBy('orders_sum_total_amount', 'desc')
                           ->limit(20)
                           ->get();

        // إحصائيات العملاء
        $stats = [
            'new_customers' => $newCustomers->count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'avg_orders_per_customer' => $topCustomers->avg('orders_count'),
            'avg_customer_value' => $topCustomers->avg('orders_sum_total_amount'),
            'repeat_customers' => User::where('role', 'customer')
                                    ->withCount('orders')
                                    ->having('orders_count', '>', 1)
                                    ->count(),
            'customer_growth' => $this->getCustomerGrowth($startDate, $endDate)
        ];

        // بيانات الرسم البياني
        $chartData = $this->getCustomersChartData($newCustomers, $startDate, $endDate);

        return view('admin.reports.customers', compact(
            'newCustomers', 'topCustomers', 'stats', 'chartData', 'startDate', 'endDate'
        ));
    }

    /**
     * Packages Report
     */
    public function packages(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        // أداء الباكجات
        $packagePerformance = Package::withCount([
                                        'orders as total_orders' => function($query) use ($startDate, $endDate) {
                                            $query->whereBetween('created_at', [$startDate, $endDate]);
                                        }
                                    ])
                                    ->withSum([
                                        'orders as total_revenue' => function($query) use ($startDate, $endDate) {
                                            $query->whereBetween('created_at', [$startDate, $endDate]);
                                        }
                                    ], 'total_amount')
                                    ->orderBy('total_orders', 'desc')
                                    ->get()
                                    ->map(function($package) {
                                        $package->avg_order_value = $package->total_orders > 0 
                                            ? $package->total_revenue / $package->total_orders 
                                            : 0;
                                        return $package;
                                    });

        // إحصائيات الباكجات
        $stats = [
            'total_packages' => $packagePerformance->count(),
            'best_selling_package' => $packagePerformance->first()?->name,
            'total_package_revenue' => $packagePerformance->sum('total_revenue'),
            'avg_package_orders' => $packagePerformance->avg('total_orders'),
            'most_profitable' => $packagePerformance->sortByDesc('total_revenue')->first()?->name
        ];

        // بيانات الرسم البياني
        $chartData = $this->getPackagesChartData($packagePerformance);

        return view('admin.reports.packages', compact(
            'packagePerformance', 'stats', 'chartData', 'startDate', 'endDate'
        ));
    }

    /**
     * Export report to PDF
     */
    public function exportPdf(Request $request)
    {
        $type = $request->get('type');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // تنفيذ تصدير PDF حسب نوع التقرير
        switch ($type) {
            case 'orders':
                return $this->exportOrdersPdf($startDate, $endDate);
            case 'financial':
                return $this->exportFinancialPdf($startDate, $endDate);
            case 'performance':
                return $this->exportPerformancePdf($startDate, $endDate);
            case 'customers':
                return $this->exportCustomersPdf($startDate, $endDate);
            case 'packages':
                return $this->exportPackagesPdf($startDate, $endDate);
            default:
                abort(404);
        }
    }

    /**
     * Export report to Excel
     */
    public function exportExcel(Request $request)
    {
        $type = $request->get('type');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // تنفيذ تصدير Excel حسب نوع التقرير
        switch ($type) {
            case 'orders':
                return $this->exportOrdersExcel($startDate, $endDate);
            case 'financial':
                return $this->exportFinancialExcel($startDate, $endDate);
            case 'performance':
                return $this->exportPerformanceExcel($startDate, $endDate);
            case 'customers':
                return $this->exportCustomersExcel($startDate, $endDate);
            case 'packages':
                return $this->exportPackagesExcel($startDate, $endDate);
            default:
                abort(404);
        }
    }

    // Helper Methods

    private function getOrdersChartData($orders)
    {
        return [
            'daily_orders' => $orders->groupBy(function($order) {
                return $order->created_at->format('Y-m-d');
            })->map->count(),
            'status_distribution' => $orders->groupBy('status')->map->count(),
            'package_distribution' => $orders->groupBy('package.name')->map->count()
        ];
    }

    private function getFinancialChartData($payments, $startDate, $endDate)
    {
        return [
            'daily_revenue' => $payments->groupBy(function($payment) {
                return $payment->payment_date->format('Y-m-d');
            })->map(function($group) {
                return $group->sum('amount');
            }),
            'payment_methods' => $payments->groupBy('payment_method')->map(function($group) {
                return $group->sum('amount');
            }),
            'monthly_trend' => $this->getMonthlyRevenue($startDate, $endDate)
        ];
    }

    private function getPerformanceChartData($employees, $startDate, $endDate)
    {
        return [
            'employee_performance' => $employees->map(function($employee) {
                return [
                    'name' => $employee->name,
                    'completion_rate' => $employee->completion_rate,
                    'total_orders' => $employee->total_orders,
                    'completed_orders' => $employee->completed_orders
                ];
            }),
            'delivery_trends' => $this->getDeliveryTrends($startDate, $endDate)
        ];
    }

    private function getCustomersChartData($customers, $startDate, $endDate)
    {
        return [
            'customer_growth' => $customers->groupBy(function($customer) {
                return $customer->created_at->format('Y-m-d');
            })->map->count(),
            'customer_segments' => $this->getCustomerSegments()
        ];
    }

    private function getPackagesChartData($packages)
    {
        return [
            'package_sales' => $packages->map(function($package) {
                return [
                    'name' => $package->name,
                    'orders' => $package->total_orders,
                    'revenue' => $package->total_revenue
                ];
            }),
            'revenue_distribution' => $packages->pluck('total_revenue', 'name')
        ];
    }

    private function getMonthlyRevenue($startDate, $endDate)
    {
        return Payment::selectRaw('MONTH(payment_date) as month, SUM(amount) as total')
                     ->where('status', 'completed')
                     ->whereBetween('payment_date', [$startDate, $endDate])
                     ->groupBy('month')
                     ->pluck('total', 'month');
    }

    private function getAverageDeliveryTime($startDate, $endDate)
    {
        $orders = Order::where('status', 'delivered')
                      ->whereBetween('delivered_at', [$startDate, $endDate])
                      ->get();

        if ($orders->count() == 0) return 0;

        $totalDays = 0;
        foreach ($orders as $order) {
            if ($order->delivered_at && $order->created_at) {
                $totalDays += $order->created_at->diffInDays($order->delivered_at);
            }
        }

        return round($totalDays / $orders->count(), 1);
    }

    private function getOnTimeDeliveryRate($startDate, $endDate)
    {
        $deliveredOrders = Order::where('status', 'delivered')
                               ->whereBetween('delivered_at', [$startDate, $endDate])
                               ->whereNotNull('expected_delivery_date')
                               ->get();

        if ($deliveredOrders->count() == 0) return 0;

        $onTimeCount = $deliveredOrders->filter(function($order) {
            return $order->delivered_at <= $order->expected_delivery_date;
        })->count();

        return round(($onTimeCount / $deliveredOrders->count()) * 100, 1);
    }

    private function getOrderCompletionRate($startDate, $endDate)
    {
        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $completedOrders = Order::where('status', 'delivered')
                               ->whereBetween('created_at', [$startDate, $endDate])
                               ->count();

        return $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 1) : 0;
    }

    private function getCustomerGrowth($startDate, $endDate)
    {
        $previousPeriod = Carbon::parse($startDate)->subDays(
            Carbon::parse($endDate)->diffInDays(Carbon::parse($startDate))
        );

        $currentPeriodCustomers = User::where('role', 'customer')
                                    ->whereBetween('created_at', [$startDate, $endDate])
                                    ->count();

        $previousPeriodCustomers = User::where('role', 'customer')
                                     ->whereBetween('created_at', [$previousPeriod, $startDate])
                                     ->count();

        if ($previousPeriodCustomers == 0) return 100;

        return round((($currentPeriodCustomers - $previousPeriodCustomers) / $previousPeriodCustomers) * 100, 1);
    }

    private function getDeliveryTrends($startDate, $endDate)
    {
        // تنفيذ حساب اتجاهات التسليم
        return [];
    }

    private function getCustomerSegments()
    {
        // تنفيذ تقسيم العملاء
        return [];
    }

    // Export Methods (يمكن تنفيذها لاحقاً)
    private function exportOrdersPdf($startDate, $endDate) {}
    private function exportFinancialPdf($startDate, $endDate) {}
    private function exportPerformancePdf($startDate, $endDate) {}
    private function exportCustomersPdf($startDate, $endDate) {}
    private function exportPackagesPdf($startDate, $endDate) {}
    
    private function exportOrdersExcel($startDate, $endDate) {}
    private function exportFinancialExcel($startDate, $endDate) {}
    private function exportPerformanceExcel($startDate, $endDate) {}
    private function exportCustomersExcel($startDate, $endDate) {}
    private function exportPackagesExcel($startDate, $endDate) {}
}
