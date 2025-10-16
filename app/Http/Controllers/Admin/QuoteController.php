<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Lead;
use App\Models\User;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class QuoteController extends Controller
{
    /**
     * Display a listing of quotes
     */
    public function index(Request $request)
    {
        $query = Quote::with(['lead', 'customer', 'createdBy', 'items'])
                     ->orderBy('created_at', 'desc');

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب التاريخ
        if ($request->filled('date_from')) {
            $query->whereDate('issue_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('issue_date', '<=', $request->date_to);
        }

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('quote_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('customer_company', 'like', "%{$search}%");
            });
        }

        // عروض الأسعار المنتهية الصلاحية
        if ($request->filled('expired') && $request->expired == '1') {
            $query->expired();
        }

        $quotes = $query->paginate(20);

        // تحديث عروض الأسعار المنتهية الصلاحية
        $this->updateExpiredQuotes();

        // إحصائيات سريعة
        $stats = [
            'total' => Quote::count(),
            'draft' => Quote::draft()->count(),
            'sent' => Quote::sent()->count(),
            'accepted' => Quote::accepted()->count(),
            'rejected' => Quote::rejected()->count(),
            'expired' => Quote::expired()->count(),
            'total_value' => Quote::sum('total_amount'),
            'accepted_value' => Quote::accepted()->sum('total_amount'),
            'conversion_rate' => Quote::count() > 0 ? (Quote::accepted()->count() / Quote::count()) * 100 : 0
        ];

        return view('admin.quotes.index', compact('quotes', 'stats'));
    }

    /**
     * Show the form for creating a new quote
     */
    public function create(Request $request)
    {
        $leadId = $request->get('lead_id');
        $customerId = $request->get('customer_id');

        $lead = null;
        $customer = null;

        if ($leadId) {
            $lead = Lead::findOrFail($leadId);
        }

        if ($customerId) {
            $customer = User::findOrFail($customerId);
        }

        $leads = Lead::whereNotIn('status', ['won', 'lost'])->get();
        $customers = User::where('role', 'customer')->get();
        $packages = Package::all();

        return view('admin.quotes.create', compact('lead', 'customer', 'leads', 'customers', 'packages'));
    }

    /**
     * Store a newly created quote
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_id' => 'nullable|exists:leads,id',
            'user_id' => 'nullable|exists:users,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_company' => 'nullable|string|max:255',
            'issue_date' => 'required|date',
            'valid_until' => 'required|date|after:issue_date',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'terms_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.description' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        DB::beginTransaction();
        try {
            $quote = Quote::create([
                'lead_id' => $request->lead_id,
                'user_id' => $request->user_id,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'customer_company' => $request->customer_company,
                'issue_date' => $request->issue_date,
                'valid_until' => $request->valid_until,
                'tax_rate' => $request->tax_rate,
                'discount_amount' => $request->discount_amount ?? 0,
                'terms_conditions' => $request->terms_conditions,
                'notes' => $request->notes,
                'created_by' => auth()->id()
            ]);

            // إضافة العناصر
            foreach ($request->items as $index => $itemData) {
                QuoteItem::create([
                    'quote_id' => $quote->id,
                    'package_id' => $itemData['package_id'] ?? null,
                    'item_name' => $itemData['item_name'],
                    'description' => $itemData['description'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'sort_order' => $index
                ]);
            }

            // حساب الإجماليات
            $quote->calculateTotals();

            // تسجيل النشاط في العميل المحتمل
            if ($quote->lead) {
                $quote->lead->logActivity('quote_created', "تم إنشاء عرض سعر رقم {$quote->quote_number}", auth()->id());
            }

            DB::commit();

            return redirect()->route('admin.quotes.show', $quote)
                           ->with('success', 'تم إنشاء عرض السعر بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->with('error', 'حدث خطأ أثناء إنشاء عرض السعر: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Display the specified quote
     */
    public function show(Quote $quote)
    {
        $quote->load(['lead', 'customer', 'createdBy', 'items.package']);

        return view('admin.quotes.show', compact('quote'));
    }

    /**
     * Show the form for editing the specified quote
     */
    public function edit(Quote $quote)
    {
        if ($quote->status !== 'draft') {
            return redirect()->route('admin.quotes.show', $quote)
                           ->with('error', 'لا يمكن تعديل عرض سعر مرسل أو مقبول');
        }

        $quote->load('items');
        $leads = Lead::whereNotIn('status', ['won', 'lost'])->get();
        $customers = User::where('role', 'customer')->get();
        $packages = Package::all();

        return view('admin.quotes.edit', compact('quote', 'leads', 'customers', 'packages'));
    }

    /**
     * Update the specified quote
     */
    public function update(Request $request, Quote $quote)
    {
        if ($quote->status !== 'draft') {
            return redirect()->route('admin.quotes.show', $quote)
                           ->with('error', 'لا يمكن تعديل عرض سعر مرسل أو مقبول');
        }

        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_company' => 'nullable|string|max:255',
            'issue_date' => 'required|date',
            'valid_until' => 'required|date|after:issue_date',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'terms_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.description' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        DB::beginTransaction();
        try {
            $quote->update([
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'customer_company' => $request->customer_company,
                'issue_date' => $request->issue_date,
                'valid_until' => $request->valid_until,
                'tax_rate' => $request->tax_rate,
                'discount_amount' => $request->discount_amount ?? 0,
                'terms_conditions' => $request->terms_conditions,
                'notes' => $request->notes
            ]);

            // حذف العناصر القديمة
            $quote->items()->delete();

            // إضافة العناصر الجديدة
            foreach ($request->items as $index => $itemData) {
                QuoteItem::create([
                    'quote_id' => $quote->id,
                    'package_id' => $itemData['package_id'] ?? null,
                    'item_name' => $itemData['item_name'],
                    'description' => $itemData['description'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'sort_order' => $index
                ]);
            }

            // إعادة حساب الإجماليات
            $quote->calculateTotals();

            // تسجيل النشاط في العميل المحتمل
            if ($quote->lead) {
                $quote->lead->logActivity('quote_updated', "تم تحديث عرض سعر رقم {$quote->quote_number}", auth()->id());
            }

            DB::commit();

            return redirect()->route('admin.quotes.show', $quote)
                           ->with('success', 'تم تحديث عرض السعر بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->with('error', 'حدث خطأ أثناء تحديث عرض السعر: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Send quote to customer
     */
    public function send(Quote $quote)
    {
        if ($quote->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن إرسال عرض سعر مرسل مسبقاً'
            ]);
        }

        try {
            $quote->send();

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال عرض السعر بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إرسال عرض السعر: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Accept quote
     */
    public function accept(Quote $quote)
    {
        try {
            $quote->accept();

            return response()->json([
                'success' => true,
                'message' => 'تم قبول عرض السعر وإنشاء الطلب بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء قبول عرض السعر: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Reject quote
     */
    public function reject(Request $request, Quote $quote)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        try {
            $quote->reject($request->reason);

            return response()->json([
                'success' => true,
                'message' => 'تم رفض عرض السعر'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء رفض عرض السعر: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Duplicate quote
     */
    public function duplicate(Quote $quote)
    {
        try {
            $newQuote = $quote->duplicate();

            return redirect()->route('admin.quotes.edit', $newQuote)
                           ->with('success', 'تم نسخ عرض السعر بنجاح');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'حدث خطأ أثناء نسخ عرض السعر: ' . $e->getMessage());
        }
    }

    /**
     * Generate PDF quote
     */
    public function generatePdf(Quote $quote)
    {
        $quote->load(['lead', 'customer', 'createdBy', 'items.package']);

        // تنفيذ إنشاء PDF لعرض السعر
        // يمكن استخدام مكتبة مثل DomPDF أو wkhtmltopdf

        return response()->download($pdfPath);
    }

    /**
     * Print quote
     */
    public function print(Quote $quote)
    {
        $quote->load(['lead', 'customer', 'createdBy', 'items.package']);

        return view('admin.quotes.print', compact('quote'));
    }

    /**
     * Delete quote
     */
    public function destroy(Quote $quote)
    {
        if ($quote->status !== 'draft') {
            return redirect()->route('admin.quotes.index')
                           ->with('error', 'لا يمكن حذف عرض سعر مرسل أو مقبول');
        }

        // تسجيل النشاط في العميل المحتمل قبل الحذف
        if ($quote->lead) {
            $quote->lead->logActivity('quote_deleted', "تم حذف عرض سعر رقم {$quote->quote_number}", auth()->id());
        }

        $quote->delete();

        return redirect()->route('admin.quotes.index')
                       ->with('success', 'تم حذف عرض السعر بنجاح');
    }

    /**
     * Get quote statistics
     */
    public function getStats(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period);
        $endDate = now();

        $stats = [
            'total_quotes' => Quote::whereBetween('created_at', [$startDate, $endDate])->count(),
            'sent_quotes' => Quote::sent()->whereBetween('created_at', [$startDate, $endDate])->count(),
            'accepted_quotes' => Quote::accepted()->whereBetween('created_at', [$startDate, $endDate])->count(),
            'rejected_quotes' => Quote::rejected()->whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_value' => Quote::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount'),
            'accepted_value' => Quote::accepted()->whereBetween('created_at', [$startDate, $endDate])->sum('total_amount'),
            'conversion_rate' => 0,
            'avg_quote_value' => Quote::whereBetween('created_at', [$startDate, $endDate])->avg('total_amount')
        ];

        // حساب معدل التحويل
        if ($stats['total_quotes'] > 0) {
            $stats['conversion_rate'] = ($stats['accepted_quotes'] / $stats['total_quotes']) * 100;
        }

        return response()->json($stats);
    }

    /**
     * Update expired quotes
     */
    private function updateExpiredQuotes()
    {
        $expiredQuotes = Quote::where('status', 'sent')
                             ->where('valid_until', '<', now()->toDateString())
                             ->get();

        foreach ($expiredQuotes as $quote) {
            $quote->markAsExpired();
        }
    }

    /**
     * Export quotes
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel');
        $status = $request->get('status');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = Quote::with(['lead', 'customer', 'createdBy']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('issue_date', [$startDate, $endDate]);
        }

        $quotes = $query->get();

        if ($format === 'pdf') {
            return $this->exportToPdf($quotes);
        } else {
            return $this->exportToExcel($quotes);
        }
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

    private function exportToPdf($quotes)
    {
        // تنفيذ تصدير PDF لعروض الأسعار
    }

    private function exportToExcel($quotes)
    {
        // تنفيذ تصدير Excel لعروض الأسعار
    }
}
