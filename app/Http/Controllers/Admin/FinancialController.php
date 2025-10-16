<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialController extends Controller
{
    /**
     * Display financial dashboard.
     */
    public function index()
    {
        $stats = $this->getFinancialStats();
        $recentInvoices = Invoice::with(['order', 'customer'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        $recentPayments = Payment::with(['order', 'customer', 'paymentMethod'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $overdueInvoices = Invoice::overdue()
            ->with(['order', 'customer'])
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        $pendingPayments = Payment::pending()
            ->with(['order', 'customer'])
            ->orderBy('created_at')
            ->limit(5)
            ->get();

        return view('admin.financial.index', compact(
            'stats',
            'recentInvoices',
            'recentPayments',
            'overdueInvoices',
            'pendingPayments'
        ));
    }

    /**
     * Display invoices management.
     */
    public function invoices(Request $request)
    {
        $query = Invoice::with(['order', 'customer', 'createdBy']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('issue_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('issue_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('order', function ($q) use ($search) {
                      $q->where('order_number', 'like', "%{$search}%");
                  });
            });
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(20);

        $customers = User::where('role', 'customer')->orderBy('name')->get();

        return view('admin.financial.invoices.index', compact('invoices', 'customers'));
    }

    /**
     * Show invoice details.
     */
    public function showInvoice(Invoice $invoice)
    {
        $invoice->load(['order', 'customer', 'createdBy', 'payments']);
        
        return view('admin.financial.invoices.show', compact('invoice'));
    }

    /**
     * Create invoice from order.
     */
    public function createInvoiceFromOrder(Order $order)
    {
        try {
            DB::beginTransaction();

            $invoice = Invoice::createFromOrder($order, auth()->user());

            DB::commit();

            return redirect()
                ->route('admin.financial.invoices.show', $invoice)
                ->with('success', 'تم إنشاء الفاتورة بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إنشاء الفاتورة: ' . $e->getMessage());
        }
    }

    /**
     * Send invoice by email.
     */
    public function sendInvoice(Invoice $invoice)
    {
        try {
            if ($invoice->sendByEmail()) {
                return back()->with('success', 'تم إرسال الفاتورة بنجاح');
            } else {
                return back()->with('error', 'فشل في إرسال الفاتورة');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء إرسال الفاتورة: ' . $e->getMessage());
        }
    }

    /**
     * Mark invoice as paid.
     */
    public function markInvoiceAsPaid(Invoice $invoice)
    {
        try {
            $invoice->markAsPaid();
            return back()->with('success', 'تم تحديث حالة الفاتورة إلى مدفوعة');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء تحديث الفاتورة: ' . $e->getMessage());
        }
    }

    /**
     * Cancel invoice.
     */
    public function cancelInvoice(Invoice $invoice)
    {
        try {
            $invoice->markAsCancelled();
            return back()->with('success', 'تم إلغاء الفاتورة');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء إلغاء الفاتورة: ' . $e->getMessage());
        }
    }

    /**
     * Display payments management.
     */
    public function payments(Request $request)
    {
        $query = Payment::with(['order', 'invoice', 'customer', 'paymentMethod', 'processedBy']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('payment_number', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        $customers = User::where('role', 'customer')->orderBy('name')->get();
        $paymentMethods = PaymentMethod::active()->ordered()->get();

        return view('admin.financial.payments.index', compact('payments', 'customers', 'paymentMethods'));
    }

    /**
     * Show payment details.
     */
    public function showPayment(Payment $payment)
    {
        $payment->load(['order', 'invoice', 'customer', 'paymentMethod', 'processedBy']);
        
        return view('admin.financial.payments.show', compact('payment'));
    }

    /**
     * Create new payment.
     */
    public function createPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'nullable|exists:orders,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'customer_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $payment = Payment::createPayment([
                'order_id' => $request->order_id,
                'invoice_id' => $request->invoice_id,
                'customer_id' => $request->customer_id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
                'processed_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()
                ->route('admin.financial.payments.show', $payment)
                ->with('success', 'تم إنشاء الدفعة بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إنشاء الدفعة: ' . $e->getMessage());
        }
    }

    /**
     * Confirm payment.
     */
    public function confirmPayment(Payment $payment)
    {
        try {
            if ($payment->markAsCompleted(auth()->user())) {
                return back()->with('success', 'تم تأكيد الدفعة بنجاح');
            } else {
                return back()->with('error', 'فشل في تأكيد الدفعة');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء تأكيد الدفعة: ' . $e->getMessage());
        }
    }

    /**
     * Fail payment.
     */
    public function failPayment(Payment $payment, Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:255'
        ]);

        try {
            if ($payment->markAsFailed($request->reason)) {
                return back()->with('success', 'تم تحديث حالة الدفعة إلى فاشلة');
            } else {
                return back()->with('error', 'فشل في تحديث حالة الدفعة');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء تحديث الدفعة: ' . $e->getMessage());
        }
    }

    /**
     * Refund payment.
     */
    public function refundPayment(Payment $payment, Request $request)
    {
        $request->validate([
            'refund_amount' => 'required|numeric|min:0.01|max:' . $payment->amount,
            'reason' => 'required|string|max:255'
        ]);

        try {
            if ($payment->refund($request->refund_amount, $request->reason)) {
                return back()->with('success', 'تم استرداد الدفعة بنجاح');
            } else {
                return back()->with('error', 'فشل في استرداد الدفعة');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء استرداد الدفعة: ' . $e->getMessage());
        }
    }

    /**
     * Display payment methods management.
     */
    public function paymentMethods()
    {
        $paymentMethods = PaymentMethod::ordered()->get();
        
        return view('admin.financial.payment-methods.index', compact('paymentMethods'));
    }

    /**
     * Store new payment method.
     */
    public function storePaymentMethod(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_online' => 'boolean',
            'processing_fee' => 'nullable|numeric|min:0',
            'processing_fee_type' => 'nullable|in:amount,percentage',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
        ]);

        try {
            PaymentMethod::create($request->all());
            return back()->with('success', 'تم إضافة طريقة الدفع بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء إضافة طريقة الدفع: ' . $e->getMessage());
        }
    }

    /**
     * Update payment method.
     */
    public function updatePaymentMethod(PaymentMethod $paymentMethod, Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_online' => 'boolean',
            'processing_fee' => 'nullable|numeric|min:0',
            'processing_fee_type' => 'nullable|in:amount,percentage',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
        ]);

        try {
            $paymentMethod->update($request->all());
            return back()->with('success', 'تم تحديث طريقة الدفع بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء تحديث طريقة الدفع: ' . $e->getMessage());
        }
    }

    /**
     * Delete payment method.
     */
    public function deletePaymentMethod(PaymentMethod $paymentMethod)
    {
        try {
            $paymentMethod->delete();
            return back()->with('success', 'تم حذف طريقة الدفع بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء حذف طريقة الدفع: ' . $e->getMessage());
        }
    }

    /**
     * Get financial reports.
     */
    public function reports(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period);
        $endDate = now();

        $reports = [
            'revenue' => $this->getRevenueReport($startDate, $endDate),
            'invoices' => $this->getInvoicesReport($startDate, $endDate),
            'payments' => $this->getPaymentsReport($startDate, $endDate),
            'outstanding' => $this->getOutstandingReport(),
        ];

        return view('admin.financial.reports', compact('reports', 'period'));
    }

    /**
     * Export financial data.
     */
    public function export(Request $request, string $type)
    {
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period);
        $endDate = now();

        switch ($type) {
            case 'invoices':
                return $this->exportInvoices($startDate, $endDate);
            case 'payments':
                return $this->exportPayments($startDate, $endDate);
            case 'revenue':
                return $this->exportRevenue($startDate, $endDate);
            default:
                return back()->with('error', 'نوع التصدير غير مدعوم');
        }
    }

    /**
     * Get financial statistics.
     */
    private function getFinancialStats(): array
    {
        $today = today();
        $thisMonth = [now()->startOfMonth(), now()->endOfMonth()];
        $lastMonth = [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()];

        return [
            'total_revenue' => Payment::completed()->sum('amount'),
            'monthly_revenue' => Payment::completed()
                ->whereBetween('payment_date', $thisMonth)
                ->sum('amount'),
            'daily_revenue' => Payment::completed()
                ->whereDate('payment_date', $today)
                ->sum('amount'),
            'pending_invoices' => Invoice::pending()->count(),
            'overdue_invoices' => Invoice::overdue()->count(),
            'pending_payments' => Payment::pending()->sum('amount'),
            'outstanding_amount' => Invoice::pending()->sum('total_amount'),
            'revenue_growth' => $this->calculateRevenueGrowth($thisMonth, $lastMonth),
        ];
    }

    /**
     * Calculate revenue growth.
     */
    private function calculateRevenueGrowth(array $currentPeriod, array $previousPeriod): float
    {
        $currentRevenue = Payment::completed()
            ->whereBetween('payment_date', $currentPeriod)
            ->sum('amount');

        $previousRevenue = Payment::completed()
            ->whereBetween('payment_date', $previousPeriod)
            ->sum('amount');

        if ($previousRevenue == 0) {
            return 0;
        }

        return round((($currentRevenue - $previousRevenue) / $previousRevenue) * 100, 2);
    }

    /**
     * Get start date based on period.
     */
    private function getStartDate(string $period): Carbon
    {
        return match($period) {
            'today' => today(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'quarter' => now()->startOfQuarter(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth()
        };
    }

    // Additional helper methods for reports would go here...
    private function getRevenueReport($startDate, $endDate): array
    {
        return [
            'total' => Payment::completed()
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->sum('amount'),
            'daily_breakdown' => Payment::completed()
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->selectRaw('DATE(payment_date) as date, SUM(amount) as total')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];
    }

    private function getInvoicesReport($startDate, $endDate): array
    {
        return [
            'total_issued' => Invoice::whereBetween('issue_date', [$startDate, $endDate])->count(),
            'total_paid' => Invoice::paid()
                ->whereBetween('issue_date', [$startDate, $endDate])
                ->count(),
            'total_amount' => Invoice::whereBetween('issue_date', [$startDate, $endDate])
                ->sum('total_amount'),
        ];
    }

    private function getPaymentsReport($startDate, $endDate): array
    {
        return Payment::getStatistics('custom');
    }

    private function getOutstandingReport(): array
    {
        return [
            'overdue_invoices' => Invoice::overdue()->sum('total_amount'),
            'pending_invoices' => Invoice::pending()->sum('total_amount'),
            'total_outstanding' => Invoice::whereIn('payment_status', ['pending', 'partial'])
                ->sum('total_amount'),
        ];
    }
}
