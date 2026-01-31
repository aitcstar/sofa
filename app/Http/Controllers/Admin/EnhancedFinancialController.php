<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;

class EnhancedFinancialController extends Controller
{
    /**
     * Display financial dashboard.
     */
    public function index(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        // Calculate statistics
        $stats = $this->calculateFinancialStats($dateFrom, $dateTo);

        // Get chart data
        $chartData = $this->prepareChartData($dateFrom, $dateTo);

        // Get recent payments
        $recentPayments = Payment::with(['invoice.order'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get pending invoices
        $pendingInvoices = Invoice::with(['order'])
            ->where('status', 'unpaid')
            ->orderBy('due_date', 'asc')
            ->limit(5)
            ->get();

        return view('admin.financial.enhanced.index', compact(
            'stats',
            'chartData',
            'recentPayments',
            'pendingInvoices'
        ));
    }

    /**
     * Display invoices list.
     */
    public function invoicesIndex(Request $request)
    {
        $query = Invoice::with(['order.user', 'payments']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('order', function ($oq) use ($search) {
                      $oq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.financial.enhanced.invoices-index', compact('invoices'));
    }

    /**
     * Show create invoice form.
     */
    public function createInvoice(Request $request)
    {
        $orders = Order::whereDoesntHave('invoices')
            ->orWhereHas('invoices', function ($q) {
                $q->where('status', '!=', 'paid');
            })
            ->with('user')
            ->get();

        $selectedOrder = null;
        if ($request->has('order_id')) {
            $selectedOrder = Order::find($request->order_id);
        }

        return view('admin.financial.enhanced.invoice-create', compact('orders', 'selectedOrder'));
    }

    /**
     * Store new invoice.
     */

     /*
    public function storeInvoice(Request $request)
    {

        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            //'due_date' => 'required|date',
            //'base_amount' => 'required|numeric|min:0',
            //'tax_rate' => 'required|numeric|min:0|max:100',
            //'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

       // DB::beginTransaction();

        try {
            $order = Order::findOrFail($request->order_id);

            // حساب المبالغ
            $baseAmount = $order->total_amount * 0.87;
            $taxAmount = $baseAmount * 0.15;

           // $invoice->subtotal = $baseAmount;
            //$invoice->tax_amount = $taxAmount;
            //$invoice->total_amount = $baseAmount + $taxAmount;
            $totalAmount= $baseAmount + $taxAmount;
            // توليد رقم الفاتورة
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad(Invoice::count() + 1, 4, '0', STR_PAD_LEFT);

            $paymentStatuses = ['unpaid', 'partial', 'paid', 'refunded'];

            $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];
            $paidAmount = $paymentStatus === 'paid' ? $totalAmount : ($paymentStatus === 'partial' ? $totalAmount * 0.5 : 0);


            // إنشاء الفاتورة
            $paidAmount = $request->paid_amount ?? 0;

            // إنشاء الفاتورة
            $invoice = Invoice::create([
                'order_id' => $order->id,
                'customer_id' => $order->user_id,
                'invoice_number' => $invoiceNumber,
                'issue_date' => Carbon::parse($order->created_at),
                'due_date' => Carbon::parse($order->created_at)->addDays(30),
                'base_amount' => $baseAmount,
                'paid_amount' => $paidAmount,
                'subtotal' => $baseAmount,
                'tax_rate' => 15,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount ?? 0,
                'total_amount' => $totalAmount,
                'status' => $paidAmount >= $totalAmount ? 'paid' : ($paidAmount > 0 ? 'partial' : 'pending'),
                'notes' => $request->notes,
            ]);

            // تحديث المبلغ الإجمالي في الطلب
            $order->total_amount = $totalAmount;
            $order->save();

            // تسجيل الدفع الجزئي فقط إذا المستخدم حدد مبلغ
            if ($paidAmount > 0) {
                $invoice->payments()->create([
                    'order_id' => $order->id,
                    'invoice_id' => $invoice->id,
                    'customer_id' => $order->user_id,
                    'amount' => $paidAmount,
                    'payment_number' => 'PAY-' . rand(100000, 999999),
                    'payment_method' => 'cash', // أو اختر الطريقة حسب الفورم
                    'payment_date' => now(),
                    'status' => 'completed',
                ]);
            }


            DB::commit();

            return redirect()->route('admin.financial.invoices.show', $invoice)
                             ->with('success', 'تم إنشاء الفاتورة بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                             ->with('error', 'حدث خطأ: ' . $e->getMessage())
                             ->withInput();
        }
    }
*/

public function storeInvoice(Request $request)
{
    $validated = $request->validate([
        'order_id' => 'required|exists:orders,id',
        'notes' => 'nullable|string',
    ]);

    DB::beginTransaction();

    try {
        $order = Order::findOrFail($request->order_id);

        $quote = $order->quote ?? null;

        $baseAmount = $quote ? $quote->subtotal : ($order->total_amount * 0.87);
        $taxAmount = $baseAmount * 0.15;
        $totalAmount = $baseAmount + $taxAmount;

        $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad(Invoice::count() + 1, 4, '0', STR_PAD_LEFT);
        $paidAmount = $request->paid_amount ?? 0;

        $invoice = Invoice::create([
            'order_id' => $order->id,
            'customer_id' => $order->user_id,
            'quote_id' => $quote->id ?? null, // حفظ رقم عرض السعر
            'invoice_number' => $invoiceNumber,
            'issue_date' => Carbon::parse($order->created_at),
            'due_date' => Carbon::parse($order->created_at)->addDays(30),
            'base_amount' => $baseAmount,
            'paid_amount' => $paidAmount,
            'subtotal' => $baseAmount,
            'tax_rate' => 15,
            'tax_amount' => $taxAmount,
            'discount_amount' => 0,
            'total_amount' => $totalAmount,
            'status' => $paidAmount >= $totalAmount ? 'paid' : ($paidAmount > 0 ? 'partial' : 'pending'),
            'notes' => $request->notes,
        ]);

        // نسخ عناصر عرض السعر للفاتورة
        if ($quote) {
            foreach ($quote->items as $item) {
                $invoice->items()->create([
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total' => $item->total_price,
                ]);
            }
        }

        $order->total_amount = $totalAmount;
        $order->save();

        if ($paidAmount > 0) {
            $invoice->payments()->create([
                'order_id' => $order->id,
                'invoice_id' => $invoice->id,
                'customer_id' => $order->user_id,
                'amount' => $paidAmount,
                'payment_number' => 'PAY-' . rand(100000, 999999),
                'payment_method' => 'cash',
                'payment_date' => now(),
                'status' => 'completed',
            ]);
        }

        DB::commit();

        return redirect()->route('admin.financial.invoices.show', $invoice)
                         ->with('success', 'تم إنشاء الفاتورة بنجاح');

    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()
                         ->with('error', 'حدث خطأ: ' . $e->getMessage())
                         ->withInput();
    }
}



    /**
     * Show invoice details.
     */
    /*
    public function showInvoice(Invoice $invoice)
    {
        //$invoice->load(['order.user', 'payments']);

        $invoice->load([
            'customer',
            'assignedEmployee',
            'payments',
            'package.packageUnitItems.unit',
            'package.packageUnitItems.item',
            'package.images',
        ]);

        return view('admin.orders.invoice', compact('invoice'));


        //return view('admin.financial.invoices.show', compact('invoice'));
    }*/

    /*
    public function showInvoice(Invoice $invoice)
{
    // 1. تحديد اللغة من الرابط
    $lang = request()->get('lang', 'ar');
    if (!in_array($lang, ['ar', 'en'])) {
        $lang = 'ar';
    }
    $dir = ($lang === 'ar') ? 'rtl' : 'ltr';

    // 2. تعيين لغة التطبيق مؤقتًا
    app()->setLocale($lang);

    // 3. تحميل العلاقات
    $invoice->load([
        'customer',
        'assignedEmployee',
        'payments',
        'package.packageUnitItems.unit',
        'package.packageUnitItems.item',
        'package.images',
    ]);

    // 4. إعداد إعدادات الموقع
    $siteSettings = (object)[
        'site_name' => config('app.name', 'SOFA Experience'),
        'address' => 'عنوان الشركة',
        'phone' => '1234567890'
    ];

    // 5. تمرير المتغيرات إلى الـ view
    return view('admin.orders.invoice', compact('invoice', 'siteSettings', 'lang', 'dir'));
}
*/

public function showInvoice(Invoice $invoice)
{
    $lang = request()->get('lang', 'ar');
    if (!in_array($lang, ['ar', 'en'])) {
        $lang = 'ar';
    }
    $dir = ($lang === 'ar') ? 'rtl' : 'ltr';
    app()->setLocale($lang);

    // نجيب البيانات الأساسية
    $invoice->load([
        'customer',
        'assignedEmployee',
        'payments',
        'order', // عناصر الطلب لو مش مرتبط بعرض سعر
        'package.images',
    ]);

    // تحديد العناصر اللي هتظهر في الفاتورة
    $invoiceItems = collect();

    if ($invoice->quote_id) {
        // لو مرتبط بعرض سعر
        $invoice->load(['quote.quoteItems.item', 'quote.quoteItems.unit']);
        $invoiceItems = $invoice->quote->quoteItems ?? collect();
    } else {
        // لو مش مرتبط بعرض سعر
        if ($invoice->order && $invoice->order->units_count > 0) {
            // استخدم عناصر الطلب أو الباكدج الحالي
            $invoiceItems = $invoice->order->items()->get()->map(function($item){
                return (object)[
                    'item' => $item,
                    'quantity' => $item->quantity ?? 1,
                    'unit_price' => $item->price ?? 0,
                    'description' => $item->description ?? '-',
                    'unit' => (object)['name_ar' => 'عام', 'name_en' => 'General'], // اسم وحدة افتراضي
                ];
            });
        }
    }

    $siteSettings = (object)[
        'site_name' => config('app.name', 'SOFA Experience'),
        'address' => 'عنوان الشركة',
        'phone' => '1234567890'
    ];

    return view('admin.orders.invoice', compact('invoice', 'invoiceItems', 'siteSettings', 'lang', 'dir'));
}



    /**
     * Show edit invoice form.
     */
    public function editInvoice(Invoice $invoice)
    {
        return view('admin.financial.enhanced.invoice-edit', compact('invoice'));
    }

    /**
     * Update invoice.
     */
    public function updateInvoice(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'due_date' => 'required|date',
            'base_amount' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Calculate amounts
            $baseAmount = $request->base_amount;
            $taxAmount = $baseAmount * ($request->tax_rate / 100);
            $discountAmount = $request->discount_amount ?? 0;
            $totalAmount = $baseAmount + $taxAmount - $discountAmount;

            $invoice->update([
                'due_date' => $request->due_date,
                'base_amount' => $baseAmount,
                'tax_rate' => $request->tax_rate,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'notes' => $request->notes,
            ]);

            DB::commit();

            return redirect()->route('admin.financial.invoices.show', $invoice)
                ->with('success', 'تم تحديث الفاتورة بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display payments list.
     */
    public function paymentsIndex(Request $request)
    {
        $query = Payment::with(['invoice.order']);

        // Apply filters
        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(20);

        return view('admin.financial.payments.index', compact('payments'));
    }

    /**
     * Show create payment form.
     */
    public function createPayment()
    {
        $invoices = Invoice::where('status', '!=', 'paid')
            ->with('order')
            ->get();

        return view('admin.financial.enhanced.payment-create', compact('invoices'));
    }

    /**
     * Store new payment.
     */
    public function storePayment(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank_transfer,credit_card,check',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $invoice = Invoice::findOrFail($request->invoice_id);

            // Check if payment amount is valid
            $remainingAmount = $invoice->total_amount - $invoice->paid_amount;
            if ($request->amount > $remainingAmount) {
                return redirect()->back()
                    ->with('error', 'المبلغ المدخل أكبر من المبلغ المتبقي')
                    ->withInput();
            }

            // Create payment
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'payment_date' => $request->payment_date,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
                'status' => 'completed',
            ]);

            // Update invoice
            $invoice->paid_amount += $request->amount;

            if ($invoice->paid_amount >= $invoice->total_amount) {
                $invoice->status = 'paid';
                $invoice->paid_at = now();
            } elseif ($invoice->paid_amount > 0) {
                $invoice->status = 'partial';
            }

            $invoice->save();

            // Update order payment status
            $order = $invoice->order;
            $order->paid_amount += $request->amount;

            if ($order->paid_amount >= $order->total_amount) {
                $order->payment_status = 'paid';
            } elseif ($order->paid_amount > 0) {
                $order->payment_status = 'partial';
            }

            $order->save();

            DB::commit();

            return redirect()->route('admin.financial.invoices.show', $invoice)
                ->with('success', 'تم تسجيل الدفعة بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display financial reports.
     */
    public function reports(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        $stats = $this->calculateFinancialStats($dateFrom, $dateTo);
        $chartData = $this->prepareChartData($dateFrom, $dateTo);

        // Get detailed data
        $invoices = Invoice::with(['order', 'payments'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->get();

        $payments = Payment::with(['invoice.order'])
            ->whereBetween('payment_date', [$dateFrom, $dateTo])
            ->get();

        return view('admin.financial.reports', compact(
            'dateFrom',
            'dateTo',
            'stats',
            'chartData',
            'invoices',
            'payments'
        ));
    }

    /**
     * Export financial report.
     */
    public function export(Request $request)
    {
        $format = $request->input('format', 'pdf');
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        $stats = $this->calculateFinancialStats($dateFrom, $dateTo);
        /*$invoices = Invoice::with(['order', 'payments'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->get();*/


        // Get recent payments
        $recentPayments = Payment::with(['invoice.order'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get pending invoices
        $pendingInvoices = Invoice::with(['order'])
            ->where('status', 'unpaid')
            ->orderBy('due_date', 'asc')
            ->limit(5)
            ->get();



        /*if ($format === 'pdf') {
            $pdf = PDF::loadView('admin.financial.export', compact('stats', 'invoices', 'dateFrom', 'dateTo')) ->setPaper('a4', 'portrait');
            return $pdf->download('financial_report_' . date('Y-m-d') . '.pdf');
        }

        // Excel export would go here
        return redirect()->back()->with('error', 'صيغة التصدير غير مدعومة');*/
        if ($format === 'pdf') {
                $html = view('admin.financial.export', compact('stats', 'pendingInvoices', 'dateFrom', 'dateTo','recentPayments'))->render();

            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'default_font' => 'dejavusans',
                'default_font_size' => 12,
            ]);

            $mpdf->WriteHTML('<style>body{direction:rtl;text-align:right;font-family:dejavusans;}</style>' . $html);
            return $mpdf->Output('financial_report_' . date('Y-m-d') . '.pdf', 'I');
        }

         // Excel export would go here
         return redirect()->back()->with('error', 'صيغة التصدير غير مدعومة');

    }

    /**
     * Calculate financial statistics.
     */
    private function calculateFinancialStats($dateFrom, $dateTo)
    {
        $invoices = Invoice::whereBetween('created_at', [$dateFrom, $dateTo])->get();
        $payments = Payment::whereBetween('payment_date', [$dateFrom, $dateTo])->get();

        // Previous period for comparison
        $previousDateFrom = Carbon::parse($dateFrom)->subDays(Carbon::parse($dateFrom)->diffInDays($dateTo));
        $previousInvoices = Invoice::whereBetween('created_at', [$previousDateFrom, $dateFrom])->get();

        $totalRevenue = $invoices->sum('total_amount');
        $previousRevenue = $previousInvoices->sum('total_amount');
        $revenueGrowth = $previousRevenue > 0 ? (($totalRevenue - $previousRevenue) / $previousRevenue) * 100 : 0;

       // dd($invoices->where('status', 'draft')->count());
        return [
            'total_revenue' => $totalRevenue,
            'revenue_growth' => $revenueGrowth,
            'paid_amount' => $payments->sum('amount'),
            'paid_invoices' => $invoices->where('status', 'paid')->count(),
            'unpaid_amount' => $invoices->where('status', 'unpaid')->sum('total_amount'),
            'pending_invoices' => $invoices->where('status', 'unpaid')->count(),
            'overdue_amount' => $invoices->where('status', 'overdue')->sum('total_amount'),
            'overdue_invoices' => $invoices->where('status', 'overdue')->count(),
            'total_tax' => $invoices->sum('tax_amount'),
            'partial_payments' => $invoices->where('status', 'partial')->sum('paid_amount'),
            'avg_invoice_value' => $invoices->count() > 0 ? $invoices->avg('total_amount') : 0,
            'total_invoices' => $invoices->count(),
        ];
    }

    /**
     * Prepare chart data.
     */
    private function prepareChartData($dateFrom, $dateTo)
    {
        // Monthly data for the last 6 months
        $monthlyLabels = [];
        $monthlyRevenue = [];
        $monthlyPaid = [];
        $monthlyPending = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyLabels[] = $date->format('M Y');

            $monthInvoices = Invoice::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->get();

            $monthPayments = Payment::whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->get();

            $monthlyRevenue[] = $monthInvoices->sum('total_amount');
            $monthlyPaid[] = $monthPayments->sum('amount');
            $monthlyPending[] = $monthInvoices->where('status', 'pending')->sum('total_amount');
        }

        // Yearly data for the last 3 years
        $yearlyLabels = [];
        $yearlyRevenue = [];

        for ($i = 2; $i >= 0; $i--) {
            $year = now()->subYears($i)->year;
            $yearlyLabels[] = $year;

            $yearInvoices = Invoice::whereYear('created_at', $year)->get();
            $yearlyRevenue[] = $yearInvoices->sum('total_amount');
        }

        // Payment status distribution
        $allInvoices = Invoice::whereBetween('created_at', [$dateFrom, $dateTo])->get();
        $paymentStatus = [
            'paid' => $allInvoices->where('status', 'paid')->count(),
            'pending' => $allInvoices->where('status', 'pending')->count(),
            'partial' => $allInvoices->where('status', 'partial')->count(),
            'overdue' => $allInvoices->where('status', 'overdue')->count(),
        ];

        return [
            'monthly' => [
                'labels' => $monthlyLabels,
                'revenue' => $monthlyRevenue,
                'paid' => $monthlyPaid,
                'pending' => $monthlyPending,
            ],
            'yearly' => [
                'labels' => $yearlyLabels,
                'revenue' => $yearlyRevenue,
            ],
            'payment_status' => $paymentStatus,
        ];
    }

    /**
     * Download invoice as PDF.
     */
    public function downloadInvoice(Invoice $invoice)
    {
        $invoice->load(['order.user', 'order.package', 'payments']);

        // Generate PDF using DomPDF
        $pdf = Pdf::loadView('admin.financial.invoices.pdf', compact('invoice'));

        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }
}

