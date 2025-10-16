<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialReportsController extends Controller
{
    /**
     * Display financial reports dashboard
     */
    public function index()
    {
        $currentMonth = now()->startOfMonth();
        $previousMonth = now()->subMonth()->startOfMonth();
        
        // الإحصائيات الشهرية الحالية
        $currentStats = $this->getMonthlyStats($currentMonth, now());
        
        // الإحصائيات الشهرية السابقة
        $previousStats = $this->getMonthlyStats($previousMonth, $previousMonth->copy()->endOfMonth());
        
        // حساب النمو
        $growth = $this->calculateGrowth($currentStats, $previousStats);
        
        // بيانات الرسوم البيانية
        $chartData = $this->getFinancialChartData();
        
        return view('admin.financial-reports.index', compact(
            'currentStats', 'previousStats', 'growth', 'chartData'
        ));
    }

    /**
     * Revenue Report
     */
    public function revenue(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        $groupBy = $request->get('group_by', 'day'); // day, week, month

        // إجمالي الإيرادات
        $totalRevenue = Payment::where('status', 'completed')
                              ->whereBetween('payment_date', [$startDate, $endDate])
                              ->sum('amount');

        // الإيرادات حسب الفترة
        $revenueData = $this->getRevenueByPeriod($startDate, $endDate, $groupBy);
        
        // الإيرادات حسب طريقة الدفع
        $revenueByMethod = Payment::where('status', 'completed')
                                 ->whereBetween('payment_date', [$startDate, $endDate])
                                 ->groupBy('payment_method')
                                 ->selectRaw('payment_method, SUM(amount) as total, COUNT(*) as count')
                                 ->get();

        // الإيرادات حسب الباكج
        $revenueByPackage = DB::table('payments')
                             ->join('orders', 'payments.order_id', '=', 'orders.id')
                             ->join('packages', 'orders.package_id', '=', 'packages.id')
                             ->where('payments.status', 'completed')
                             ->whereBetween('payments.payment_date', [$startDate, $endDate])
                             ->groupBy('packages.id', 'packages.name')
                             ->selectRaw('packages.name, SUM(payments.amount) as total, COUNT(payments.id) as count')
                             ->orderBy('total', 'desc')
                             ->get();

        // أفضل العملاء (حسب الإيرادات)
        $topCustomers = DB::table('payments')
                         ->join('orders', 'payments.order_id', '=', 'orders.id')
                         ->join('users', 'orders.user_id', '=', 'users.id')
                         ->where('payments.status', 'completed')
                         ->whereBetween('payments.payment_date', [$startDate, $endDate])
                         ->groupBy('users.id', 'users.name')
                         ->selectRaw('users.name, SUM(payments.amount) as total, COUNT(payments.id) as count')
                         ->orderBy('total', 'desc')
                         ->limit(10)
                         ->get();

        // الإحصائيات
        $stats = [
            'total_revenue' => $totalRevenue,
            'average_daily' => $this->getAverageDailyRevenue($startDate, $endDate),
            'growth_rate' => $this->getRevenueGrowthRate($startDate, $endDate),
            'payment_count' => Payment::where('status', 'completed')
                                     ->whereBetween('payment_date', [$startDate, $endDate])
                                     ->count(),
            'average_payment' => $totalRevenue > 0 ? $totalRevenue / Payment::where('status', 'completed')
                                                                           ->whereBetween('payment_date', [$startDate, $endDate])
                                                                           ->count() : 0
        ];

        return view('admin.financial-reports.revenue', compact(
            'stats', 'revenueData', 'revenueByMethod', 'revenueByPackage', 
            'topCustomers', 'startDate', 'endDate', 'groupBy'
        ));
    }

    /**
     * Profit & Loss Report
     */
    public function profitLoss(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        // الإيرادات
        $revenue = Payment::where('status', 'completed')
                         ->whereBetween('payment_date', [$startDate, $endDate])
                         ->sum('amount');

        // التكاليف (يمكن إضافة جدول للتكاليف لاحقاً)
        $costs = $this->calculateCosts($startDate, $endDate);
        
        // الضرائب
        $taxes = Invoice::whereBetween('issue_date', [$startDate, $endDate])
                       ->sum('tax_amount');

        // صافي الربح
        $netProfit = $revenue - $costs['total'] - $taxes;
        
        // هامش الربح
        $profitMargin = $revenue > 0 ? ($netProfit / $revenue) * 100 : 0;

        // بيانات الرسم البياني
        $chartData = $this->getProfitLossChartData($startDate, $endDate);

        $data = [
            'revenue' => $revenue,
            'costs' => $costs,
            'taxes' => $taxes,
            'net_profit' => $netProfit,
            'profit_margin' => $profitMargin,
            'chart_data' => $chartData
        ];

        return view('admin.financial-reports.profit-loss', compact(
            'data', 'startDate', 'endDate'
        ));
    }

    /**
     * Cash Flow Report
     */
    public function cashFlow(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        // التدفق النقدي الداخل (المدفوعات المستلمة)
        $cashInflow = Payment::where('status', 'completed')
                            ->whereBetween('payment_date', [$startDate, $endDate])
                            ->selectRaw('DATE(payment_date) as date, SUM(amount) as total')
                            ->groupBy('date')
                            ->orderBy('date')
                            ->get();

        // التدفق النقدي الخارج (المصروفات - يمكن إضافة جدول للمصروفات)
        $cashOutflow = $this->getCashOutflow($startDate, $endDate);

        // صافي التدفق النقدي
        $netCashFlow = $this->calculateNetCashFlow($cashInflow, $cashOutflow);

        // الرصيد النقدي التراكمي
        $cumulativeBalance = $this->calculateCumulativeBalance($netCashFlow);

        $data = [
            'cash_inflow' => $cashInflow,
            'cash_outflow' => $cashOutflow,
            'net_cash_flow' => $netCashFlow,
            'cumulative_balance' => $cumulativeBalance
        ];

        return view('admin.financial-reports.cash-flow', compact(
            'data', 'startDate', 'endDate'
        ));
    }

    /**
     * Outstanding Payments Report
     */
    public function outstandingPayments(Request $request)
    {
        // الطلبات غير المدفوعة بالكامل
        $unpaidOrders = Order::where('payment_status', '!=', 'paid')
                            ->where('total_amount', '>', 0)
                            ->with(['user', 'package', 'payments'])
                            ->get()
                            ->map(function($order) {
                                $order->outstanding_amount = $order->total_amount - $order->paid_amount;
                                $order->days_overdue = $order->expected_delivery_date 
                                    ? max(0, now()->diffInDays($order->expected_delivery_date, false))
                                    : 0;
                                return $order;
                            });

        // الفواتير المتأخرة
        $overdueInvoices = Invoice::where('status', 'overdue')
                                 ->with(['order.user'])
                                 ->get()
                                 ->map(function($invoice) {
                                     $invoice->days_overdue = now()->diffInDays($invoice->due_date, false);
                                     return $invoice;
                                 });

        // إحصائيات المدفوعات المعلقة
        $stats = [
            'total_outstanding' => $unpaidOrders->sum('outstanding_amount'),
            'overdue_invoices_count' => $overdueInvoices->count(),
            'overdue_invoices_amount' => $overdueInvoices->sum('total_amount'),
            'partial_payments' => $unpaidOrders->where('payment_status', 'partial')->count(),
            'unpaid_orders' => $unpaidOrders->where('payment_status', 'unpaid')->count()
        ];

        // تقسيم حسب فترات التأخير
        $agingAnalysis = [
            '0-30' => $unpaidOrders->where('days_overdue', '<=', 30)->sum('outstanding_amount'),
            '31-60' => $unpaidOrders->whereBetween('days_overdue', [31, 60])->sum('outstanding_amount'),
            '61-90' => $unpaidOrders->whereBetween('days_overdue', [61, 90])->sum('outstanding_amount'),
            '90+' => $unpaidOrders->where('days_overdue', '>', 90)->sum('outstanding_amount')
        ];

        return view('admin.financial-reports.outstanding-payments', compact(
            'unpaidOrders', 'overdueInvoices', 'stats', 'agingAnalysis'
        ));
    }

    /**
     * Tax Report
     */
    public function taxReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        // إجمالي الضرائب
        $totalTax = Invoice::whereBetween('issue_date', [$startDate, $endDate])
                          ->sum('tax_amount');

        // الضرائب حسب الشهر
        $taxByMonth = Invoice::whereBetween('issue_date', [$startDate, $endDate])
                            ->selectRaw('MONTH(issue_date) as month, YEAR(issue_date) as year, SUM(tax_amount) as total')
                            ->groupBy('year', 'month')
                            ->orderBy('year')
                            ->orderBy('month')
                            ->get();

        // الضرائب حسب معدل الضريبة
        $taxByRate = Invoice::whereBetween('issue_date', [$startDate, $endDate])
                           ->groupBy('tax_rate')
                           ->selectRaw('tax_rate, SUM(tax_amount) as total, COUNT(*) as count')
                           ->get();

        // الفواتير الخاضعة للضريبة
        $taxableInvoices = Invoice::whereBetween('issue_date', [$startDate, $endDate])
                                 ->where('tax_amount', '>', 0)
                                 ->with(['order.user'])
                                 ->get();

        $data = [
            'total_tax' => $totalTax,
            'tax_by_month' => $taxByMonth,
            'tax_by_rate' => $taxByRate,
            'taxable_invoices' => $taxableInvoices,
            'average_tax_rate' => $taxableInvoices->avg('tax_rate')
        ];

        return view('admin.financial-reports.tax-report', compact(
            'data', 'startDate', 'endDate'
        ));
    }

    /**
     * Export financial report
     */
    public function export(Request $request)
    {
        $type = $request->get('type');
        $format = $request->get('format', 'excel');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        switch ($type) {
            case 'revenue':
                return $this->exportRevenueReport($format, $startDate, $endDate);
            case 'profit-loss':
                return $this->exportProfitLossReport($format, $startDate, $endDate);
            case 'cash-flow':
                return $this->exportCashFlowReport($format, $startDate, $endDate);
            case 'outstanding':
                return $this->exportOutstandingPaymentsReport($format);
            case 'tax':
                return $this->exportTaxReport($format, $startDate, $endDate);
            default:
                abort(404);
        }
    }

    // Helper Methods

    private function getMonthlyStats($startDate, $endDate)
    {
        return [
            'revenue' => Payment::where('status', 'completed')
                               ->whereBetween('payment_date', [$startDate, $endDate])
                               ->sum('amount'),
            'orders' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
            'invoices' => Invoice::whereBetween('issue_date', [$startDate->toDateString(), $endDate->toDateString()])->count(),
            'payments' => Payment::where('status', 'completed')
                                ->whereBetween('payment_date', [$startDate, $endDate])
                                ->count()
        ];
    }

    private function calculateGrowth($current, $previous)
    {
        $growth = [];
        
        foreach ($current as $key => $value) {
            if ($previous[$key] > 0) {
                $growth[$key] = round((($value - $previous[$key]) / $previous[$key]) * 100, 2);
            } else {
                $growth[$key] = $value > 0 ? 100 : 0;
            }
        }
        
        return $growth;
    }

    private function getFinancialChartData()
    {
        // بيانات الإيرادات الشهرية للسنة الحالية
        $monthlyRevenue = Payment::where('status', 'completed')
                                ->whereYear('payment_date', now()->year)
                                ->selectRaw('MONTH(payment_date) as month, SUM(amount) as total')
                                ->groupBy('month')
                                ->orderBy('month')
                                ->pluck('total', 'month');

        // بيانات الطلبات الشهرية
        $monthlyOrders = Order::whereYear('created_at', now()->year)
                             ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                             ->groupBy('month')
                             ->orderBy('month')
                             ->pluck('count', 'month');

        return [
            'monthly_revenue' => $monthlyRevenue,
            'monthly_orders' => $monthlyOrders
        ];
    }

    private function getRevenueByPeriod($startDate, $endDate, $groupBy)
    {
        $format = match($groupBy) {
            'day' => '%Y-%m-%d',
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            default => '%Y-%m-%d'
        };

        return Payment::where('status', 'completed')
                     ->whereBetween('payment_date', [$startDate, $endDate])
                     ->selectRaw("DATE_FORMAT(payment_date, '{$format}') as period, SUM(amount) as total")
                     ->groupBy('period')
                     ->orderBy('period')
                     ->get();
    }

    private function getAverageDailyRevenue($startDate, $endDate)
    {
        $days = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        $totalRevenue = Payment::where('status', 'completed')
                              ->whereBetween('payment_date', [$startDate, $endDate])
                              ->sum('amount');
        
        return $days > 0 ? $totalRevenue / $days : 0;
    }

    private function getRevenueGrowthRate($startDate, $endDate)
    {
        $currentPeriod = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));
        $previousStartDate = Carbon::parse($startDate)->subDays($currentPeriod + 1);
        $previousEndDate = Carbon::parse($startDate)->subDay();

        $currentRevenue = Payment::where('status', 'completed')
                                ->whereBetween('payment_date', [$startDate, $endDate])
                                ->sum('amount');

        $previousRevenue = Payment::where('status', 'completed')
                                 ->whereBetween('payment_date', [$previousStartDate, $previousEndDate])
                                 ->sum('amount');

        return $previousRevenue > 0 ? (($currentRevenue - $previousRevenue) / $previousRevenue) * 100 : 0;
    }

    private function calculateCosts($startDate, $endDate)
    {
        // هذه دالة مؤقتة - يمكن إضافة جدول للتكاليف لاحقاً
        return [
            'materials' => 0,
            'labor' => 0,
            'overhead' => 0,
            'total' => 0
        ];
    }

    private function getProfitLossChartData($startDate, $endDate)
    {
        // بيانات الربح والخسارة للرسم البياني
        return [];
    }

    private function getCashOutflow($startDate, $endDate)
    {
        // التدفق النقدي الخارج - يمكن إضافة جدول للمصروفات
        return collect();
    }

    private function calculateNetCashFlow($inflow, $outflow)
    {
        // حساب صافي التدفق النقدي
        return collect();
    }

    private function calculateCumulativeBalance($netCashFlow)
    {
        // حساب الرصيد التراكمي
        return collect();
    }

    // Export Methods
    private function exportRevenueReport($format, $startDate, $endDate) {}
    private function exportProfitLossReport($format, $startDate, $endDate) {}
    private function exportCashFlowReport($format, $startDate, $endDate) {}
    private function exportOutstandingPaymentsReport($format) {}
    private function exportTaxReport($format, $startDate, $endDate) {}
}
