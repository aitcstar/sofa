<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Package;
use App\Models\OrderLog;
use App\Models\Notification;
use App\Models\ScheduledTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EnhancedDashboardController extends Controller
{
    /**
     * Display enhanced dashboard.
     */
    public function index()
    {
        // Get basic statistics
        $stats = $this->getBasicStats();
        
        // Get recent orders
        $recentOrders = Order::with(['user', 'package', 'assignedEmployee'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get overdue orders
        $overdueOrders = Order::overdue()
            ->with(['user', 'package', 'assignedEmployee'])
            ->orderBy('expected_delivery_date')
            ->limit(5)
            ->get();

        // Get high priority orders
        $highPriorityOrders = Order::where('priority', '>=', 4)
            ->whereNotIn('status', ['delivered', 'archived', 'cancelled'])
            ->with(['user', 'package', 'assignedEmployee'])
            ->orderBy('priority', 'desc')
            ->limit(5)
            ->get();

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

        return view('admin.dashboard.enhanced', compact(
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
    }

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
    private function getOrderStatusDistribution(): array
    {
        return Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status => $item->count];
            })
            ->toArray();
    }

    /**
     * Get top packages.
     */
    private function getTopPackages(): array
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
    }

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
