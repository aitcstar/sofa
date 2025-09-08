<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Package;
use App\Models\Product;
use App\Models\Order;
use App\Models\Contact;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /*
    public function index()
    {
        // إحصائيات عامة
        $stats = [
            'total_users'      => User::count(),
            'total_categories' => Category::count(),
            'total_products'   => Product::count(),
            'new_contacts'     => Contact::where('status', 'new')->count(),
        ];

        // أحدث المستخدمين
        $recent_users = User::latest()
            ->take(5)
            ->get();

        // بيانات المبيعات (Dummy)
        $salesMonths = ['يناير','فبراير','مارس','أبريل','مايو','يونيو'];
        $salesData   = [1200, 1500, 900, 2000, 1800, 2200];

        // حالات الطلبات (Dummy)
        $ordersStatusData = [
            'new'         => 5,
            'confirmed'   => 1,
            'in_progress' => 0,
            'shipped'     => 2,
            'delivered'   => 1,
            'archived'    => 1,
        ];

        return view('admin.dashboard', compact(
            'stats',
            'salesMonths',
            'salesData',
            'ordersStatusData',
            'recent_users'
        ));
    }
    */
    public function index()
{
    // إحصائيات عامة
    $stats = [
        'total_users'      => User::count(),
        'total_packages'   => Package::count(),
        'total_products'   => Product::count(),
        'total_orders'     => Order::count(),
        'total_sales'      => Order::where('status', 'delivered')->sum('total_amount'),
        'pending_orders'   => Order::where('status', 'pending')->count(),
        'new_contacts'     => Contact::where('status', 'new')->count(),
    ];

    // أحدث الطلبات
    $recent_orders = Order::with('user')
        ->latest()
        ->take(5)
        ->get();

    // أحدث العملاء
    $recent_users = User::latest()
        ->take(5)
        ->get();

    // بيانات المبيعات (Dummy → هتربط بعدين بتقارير فعلية)
    $salesMonths = ['يناير','فبراير','مارس','أبريل','مايو','يونيو'];
    $salesData   = [1200, 1500, 900, 2000, 1800, 2200];

    // حالات الطلبات
    /*$ordersStatusData = [
        'new'         => Order::where('status', 'new')->count(),
        'confirmed'   => Order::where('status', 'confirmed')->count(),
        'in_progress' => Order::where('status', 'in_progress')->count(),
        'shipped'     => Order::where('status', 'shipped')->count(),
        'delivered'   => Order::where('status', 'delivered')->count(),
        'archived'    => Order::where('status', 'archived')->count(),
    ];*/

    $ordersStatusData = [
        'new'         => 1,
        'confirmed'   => 2,
        'in_progress' => 3,
        'shipped'     => 6,
        'delivered'   => 3,
        'archived'    => 1,
    ];

    // عدد الطلبات (اليوم/الشهر/السنة)
    $today_orders   = Order::whereDate('created_at', Carbon::today())->count();
    $month_orders   = Order::whereMonth('created_at', Carbon::now()->month)->count();
    $year_orders    = Order::whereYear('created_at', Carbon::now()->year)->count();

    // أكثر الباكجات مبيعاً
    $top_packages = Product::withCount('orders')
        ->orderByDesc('orders_count')
        ->take(5)
        ->get();


        // عدد العملاء الجدد (آخر 7 أيام)
        $new_users = User::where('created_at', '>=', Carbon::now()->subDays(7))->count();

        // نسبة الإنجاز = متوسط (المدة الفعلية ÷ المدة المتوقعة)
        $avg_completion = Order::whereNotNull('delivered_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, delivered_at) / expected_duration) as ratio')
            ->value('ratio');
        $completion_rate = round($avg_completion * 100, 1);

    // حالة الطلبات
    $orders_status = [
        'in_progress' => Order::where('status', 'in_progress')->count(),
        'delivered'   => Order::where('status', 'delivered')->count(),
    ];

    return view('admin.dashboard', compact(
        'stats',
        'salesMonths',
        'salesData',
        'ordersStatusData',
        'recent_users',
        'recent_orders',
        'today_orders',
            'month_orders',
            'year_orders',
            'top_packages',
            'orders_status',
            'new_users',
            'completion_rate'
    ));
}

}

