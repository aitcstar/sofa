<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\Quote;
use App\Models\Order;
use App\Models\User;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Auth;
use Illuminate\Support\Str;
use App\Models\OrderItem;


class EnhancedCRMController extends Controller
{
    /**
     * Display CRM dashboard.
     */
    public function index(Request $request)
    {
        // Calculate statistics
        $stats = [
            'total_leads' => Lead::count(),
            'new_leads' => Lead::where('status', 'new')->count(),
            'contacted_leads' => Lead::where('status', 'contacted')->count(),
            'interested_leads' => Lead::where('status', 'interested')->count(),
            'converted_leads' => Lead::where('status', 'converted')->count(),
            'conversion_rate' => Lead::count() > 0 ? (Lead::where('status', 'converted')->count() / Lead::count()) * 100 : 0,
            'total_quotes' => Quote::count(),
            'pending_quotes' => Quote::where('status', 'pending')->count(),
        ];

        // Get recent leads
        $recentLeads = Lead::with('assignedTo')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get chart data
        $chartData = $this->prepareCRMChartData();

        return view('admin.crm.enhanced.index', compact('stats', 'recentLeads', 'chartData'));
    }

    /**
     * Display leads list.
     */
    public function leadsIndex(Request $request)
    {
        $query = Lead::with(['assignedTo', 'activities']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            });
        }

        $leads = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get filter options
        $employees = User::where('role', 'employee')->orWhere('role', 'admin')->get();
        $statusOptions = Lead::getStatusOptions();
        $priorityOptions = Lead::getPriorityOptions();
        $sourceOptions = Lead::getSourceOptions();
        $projectTypeOptions = Lead::getProjectTypeOptions();

      return view('admin.crm.leads.index', compact(
            'leads',
            'employees',
            'statusOptions',
            'priorityOptions',
            'sourceOptions',
            'projectTypeOptions'
        ));
    }

    /**
     * Show create lead form.
     */
    public function createLead()
    {
        $employees = User::where('role', 'employee')->orWhere('role', 'admin')->get();

        return view('admin.crm.enhanced.lead-create', compact('employees'));
    }

    /**
     * Store new lead.
     */
    public function storeLead(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'company' => 'nullable|string|max:255',
            'source' => 'required',
            'status' => 'required',
            'assigned_to' => 'nullable|exists:users,id',
            'project_type' => 'nullable|string',
            'budget' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        //DB::beginTransaction();
        //dd($request->all());
        try {
            $lead = Lead::create($validated);

            // Create initial activity
            LeadActivity::create([
                'lead_id' => $lead->id,
                'user_id' => auth()->id(),
                'activity_type' => 'created',
                'description' => 'تم إنشاء العميل المحتمل',
            ]);

            DB::commit();

            return redirect()->route('admin.crm.leads.show', $lead)
                ->with('success', 'تم إضافة العميل المحتمل بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show lead details.
     */
    public function showLead(Lead $lead)
    {
        $lead->load(['assignedTo', 'activities.user', 'quotes']);

        $employees = User::where('role', 'employee')->orWhere('role', 'admin')->get();

        return view('admin.crm.enhanced.lead-show', compact('lead', 'employees'));
    }

    /**
     * Show edit lead form.
     */
    public function editLead(Lead $lead)
    {
        $employees = User::where('role', 'employee')->orWhere('role', 'admin')->get();

        return view('admin.crm.enhanced.lead-edit', compact('lead', 'employees'));
    }

    /**
     * Update lead.
     */
    public function updateLead(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'company' => 'nullable|string|max:255',
            'source' => 'required',
            'status' => 'required',
            'assigned_to' => 'nullable|exists:users,id',
            'project_type' => 'nullable|string',
            'budget' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $oldStatus = $lead->status;
            $lead->update($validated);

            // Log status change
            if ($oldStatus !== $lead->status) {
                LeadActivity::create([
                    'lead_id' => $lead->id,
                    'user_id' => auth()->id(),
                    'activity_type' => 'status_changed',
                    'description' => "تم تغيير الحالة من {$oldStatus} إلى {$lead->status}",
                ]);
            }

            DB::commit();

            return redirect()->route('admin.crm.leads.show', $lead)
                ->with('success', 'تم تحديث بيانات العميل المحتمل بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Add activity to lead.
     */
    public function addActivity(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'activity_type' => 'required|in:call,email,meeting,note,follow_up',
            'description' => 'required|string',
            'scheduled_at' => 'nullable|date',
        ]);

        LeadActivity::create([
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'activity_type' => $request->activity_type,
            'description' => $request->description,
            'scheduled_at' => $request->scheduled_at,
        ]);

        return redirect()->back()->with('success', 'تم إضافة النشاط بنجاح');
    }

    /**
     * Convert lead to order.
     */

     /*
    public function convertToOrder(Lead $lead)
    {
        if ($lead->status === 'converted') {
            return redirect()->back()->with('error', 'تم تحويل هذا العميل المحتمل مسبقاً');
        }

       //DB::beginTransaction();

        try {
            // Find or create customer
            $customer = User::firstOrCreate(
                ['email' => $lead->email],
                [
                    'name' => $lead->name,
                    'phone' => $lead->phone,
                    'role' => 'customer',
                    'password' => bcrypt(Str::random(16)),
                ]
            );

            // Generate order number
            $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(Order::count() + 1, 4, '0', STR_PAD_LEFT);

            // Create order
            $order = Order::create([
                'user_id' => $customer->id,
                'package_id' => $request->package_id,
                'order_number' => $orderNumber,
                'name' => $lead->name,
                'email' => $lead->email,
                'phone' => $lead->phone,
                'project_type' => $lead->project_type,
                'total_amount' => $lead->total_amount,
                'discount_amount' => $lead->discount_amount,
                'tax_amount' => $lead->tax_amount,
                'client_type' => 'individual',
                'customer_type' => 'individual',
                'status' => 'pending',
                'payment_status' => 'pending',
                'internal_notes' => "تم التحويل من العميل المحتمل: {$lead->name}\n\n" . $lead->notes,
            ]);

            // Update lead status
            $lead->status = 'converted';
            $lead->converted_to_order_id = $order->id;
            $lead->converted_at = now();
            $lead->save();

            // Log activity
            LeadActivity::create([
                'lead_id' => $lead->id,
                'user_id' => auth()->id(),
                'activity_type' => 'converted',
                'description' => "تم تحويل العميل المحتمل إلى طلب رقم: {$order->order_number}",
            ]);

            DB::commit();

            return redirect()->route('admin.orders.enhanced.show', $order)
                ->with('success', 'تم تحويل العميل المحتمل إلى طلب بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }
*/

public function convertToOrder(Request $request, Lead $lead)
{
    if ($lead->status === 'converted') {
        return redirect()->back()->with('error', 'تم تحويل هذا العميل المحتمل مسبقاً');
    }

    DB::beginTransaction();

    try {
        // Find or create customer
        $customer = User::firstOrCreate(
            ['email' => $lead->email],
            [
                'name' => $lead->name,
                'phone' => $lead->phone,
                'role' => 'customer',
                'password' => bcrypt(Str::random(16)),
            ]
        );

        // Generate order number
        $orderNumber = 'ORD-' . now()->format('Ymd') . '-' . str_pad(Order::count() + 1, 4, '0', STR_PAD_LEFT);

        // Create order
        $baseAmount = $lead->total_amount - $lead->tax_amount + ($lead->discount_amount ?? 0);

        $packageId = $lead->quoteItems()->first()?->package_id;

        if (!$packageId) {
            return redirect()->back()->with('error', 'لا يوجد Package مرتبط بهذا العميل المحتمل.');
        }


        $projectTypeMapping = [
            'building' => 'large',
            'compound' => 'large',
            'hotel_apartments' => 'medium',
            'villa' => 'medium',
            'commercial' => 'small',
            'other' => 'small',
        ];

        $projectType = $projectTypeMapping[$this->project_type] ?? 'small';


        $order = Order::create([
            'user_id' => $customer->id,
            'package_id' => $packageId,
            'order_number' => $orderNumber,
            'name' => $lead->name,
            'email' => $lead->email,
            'phone' => $lead->phone,
            'project_type' =>  $projectType,
            'base_amount' => $baseAmount,
            'total_amount' => $lead->total_amount,
            'discount_amount' => $lead->discount_amount ?? 0,
            'tax_amount' => $lead->tax_amount,
            'client_type' => 'individual',
            'country_code' => $lead->country_code ?? '+966',
            'status' => 'pending',
            'payment_status' => 'pending',
            'internal_notes' => "تم التحويل من العميل المحتمل: {$lead->name}\n\n" . $lead->notes,
        ]);





        // Update lead status
        $lead->update([
            'status' => 'converted',
            'converted_to_order_id' => $order->id,
            'converted_at' => now(),
        ]);

        // Log activity
        LeadActivity::create([
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'activity_type' => 'converted',
            'description' => "تم تحويل العميل المحتمل إلى طلب رقم: {$order->order_number}",
        ]);

        DB::commit();

        return redirect()->route('admin.orders.enhanced.show', $order)
            ->with('success', 'تم تحويل العميل المحتمل إلى طلب بنجاح');

    } catch (\Exception $e) {
        DB::rollBack();

        return redirect()->back()
            ->with('error', 'حدث خطأ: ' . $e->getMessage());
    }
 }

    /**
     * Display quotes list.
     */
    public function quotesIndex(Request $request)
    {
            $query = Quote::with(['lead', 'customer', 'createdBy']);

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('created_by')) {
                $query->where('created_by', $request->created_by);
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
                    $q->where('quote_number', 'like', "%{$search}%")
                      ->orWhere('customer_name', 'like', "%{$search}%")
                      ->orWhere('customer_email', 'like', "%{$search}%")
                      ->orWhereHas('lead', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
                });
            }

            $quotes = $query->orderBy('created_at', 'desc')->paginate(20);

            $employees = User::where('role', 'employee')->orderBy('name')->get();
            $statusOptions = Quote::getStatusOptions();

            return view('admin.crm.quotes.index', compact('quotes', 'employees', 'statusOptions'));
    }

    /**
     * Show create quote form.
     */

    /* public function createQuote()
    {
        $leads = Lead::whereIn('status', ['contacted', 'interested'])->get();

        return view('admin.crm.quotes.create', compact('leads'));
    }*/

    public function createQuote()
    {
        $leads = Lead::whereIn('status', ['contacted', 'interested'])->get();

        // تحميل packageUnitItems مع العلاقة item()
        $packages = Package::with('packageUnitItems.item')->get()->map(function ($pkg) {
            return [
                'id' => $pkg->id,
                'name_ar' => $pkg->name_ar,
                'items' => $pkg->packageUnitItems->map(function ($packageUnitItem) {
                    // $packageUnitItem هو PackageUnitItem
                    // $packageUnitItem->item هو نموذج Item
                    return [
                        'name' => $packageUnitItem->item->item_name_ar ?? 'بدون اسم',
                        'description' => $packageUnitItem->item->description ?? '',
                        'default_price' => (float) ($packageUnitItem->item->default_price ?? 0),
                        'default_quantity' => (int) ($packageUnitItem->item->default_quantity ?? 1),
                    ];
                })
            ];
        });

        // dd($packages); // ← الآن سيعرض البيانات بشكل صحيح

        return view('admin.crm.quotes.create', compact('leads', 'packages'));
    }

    /**
     * Store new quote.
     */
    /*
    public function storeQuote(Request $request)
    {
        $validated = $request->validate([
            'lead_id' => 'required|exists:leads,id',
            //'title' => 'required|string|max:255',
            //'description' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'valid_until' => 'required|date',
            'terms' => 'nullable|string',
        ]);
        //dd($request->all());
        //DB::beginTransaction();

        try {
            // حساب المجموعات
            $subtotal = 0;
            foreach ($request->items as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }

            $taxAmount = round($subtotal * ($request->tax_rate / 100), 2);
            $discountAmount = $request->discount_amount ?? 0;
            $totalAmount = $subtotal + $taxAmount - $discountAmount;

            // إنشاء رقم عرض السعر
            $quoteNumber = 'QUO-' . date('Ymd') . '-' . str_pad(Quote::count() + 1, 4, '0', STR_PAD_LEFT);

            // إنشاء عرض السعر
            $quote = Quote::create([
                'lead_id' => $request->lead_id,
                'quote_number' => $quoteNumber,
                //'title' => $request->title,
                //'description' => $request->description,
                'subtotal' => $subtotal,
                'tax_rate' => $request->tax_rate,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'valid_until' => $request->valid_until,
                'terms_conditions' => $request->terms,
                'status' => 'pending',
                'created_by' => auth()->id(),
            ]);

            // حفظ العناصر المرتبطة
            foreach ($request->items as $item) {
                $quote->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            // تسجيل النشاط في العميل المحتمل
            LeadActivity::create([
                'lead_id' => $request->lead_id,
                'user_id' => auth()->id(),
                'activity_type' => 'quote_sent',
                'description' => "تم إنشاء عرض سعر رقم: {$quote->quote_number}",
            ]);

            DB::commit();

            return redirect()->route('admin.crm.quotes.show', $quote)
                ->with('success', 'تم إنشاء عرض السعر بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage())
                ->withInput();
        }
    }*/

    /**
 * Store new quote.
 */
public function storeQuote(Request $request)
{
    //dd('dddd');
    // التحقق من البيانات
    $validated = $request->validate([
        'lead_id' => 'required|exists:leads,id',
        'customer_name' => 'required|string|max:255',
        'customer_email' => 'nullable|email',
        'customer_phone' => 'nullable|string',
        'customer_company' => 'nullable|string',
        'issue_date' => 'required|date',
        'valid_until' => 'required|date|after_or_equal:issue_date',
        'packages' => 'required|array|min:1',
        'packages.*.items' => 'required|array|min:1',
        'packages.*.items.*.name' => 'required|string',
        'packages.*.items.*.description' => 'nullable|string',
        'packages.*.items.*.quantity' => 'required|numeric|min:1',
        'packages.*.items.*.unit_price' => 'required|numeric|min:0',
        'discount_amount' => 'nullable|numeric|min:0',
        'tax_rate' => 'required|numeric|min:0|max:100',
        'terms_conditions' => 'nullable|string',
        'notes' => 'nullable|string',
    ]);



    DB::beginTransaction();
    //try {
        // حساب المبالغ
        $subtotal = 0;
        foreach ($request->packages as $pkg) {
            foreach ($pkg['items'] as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }
        }

        $discountAmount = $request->discount_amount ?? 0;
        $taxRate = $request->tax_rate ?? 0;
        $afterDiscount = $subtotal - $discountAmount;
        $taxAmount = round($afterDiscount * ($taxRate / 100), 2);
        $totalAmount = $afterDiscount + $taxAmount;

        // توليد رقم عرض السعر
        $quoteNumber = 'QUO-' . now()->format('Ymd') . '-' . str_pad(Quote::count() + 1, 4, '0', STR_PAD_LEFT);

        // إنشاء عرض السعر
        $quote = Quote::create([
            'lead_id' => $request->lead_id,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'customer_company' => $request->customer_company,
            'quote_number' => $quoteNumber,
            'issue_date' => $request->issue_date,
            'valid_until' => $request->valid_until,
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'terms_conditions' => $request->terms_conditions,
            'notes' => $request->notes,
            'status' => 'draft',
            'created_by' => auth()->id() ?? 1,
        ]);

       // dd($request->packages);

        // حفظ جميع القطع (Items) تحت الباكجات
        // بما أن كل باكج مُعبّأ بالقطع عند الاختيار، نقوم بحفظها كعناصر مستقلة في جدول `quote_items`
        foreach ($request->packages as $pkgData) {
            $packageId = $pkgData['package_id'] ?? null;

            foreach ($pkgData['items'] as $itemData) {
                $quote->items()->create([
                    'item_name' => $itemData['name'],
                    'description' => $itemData['description'] ?? null,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'total_price' => $itemData['quantity'] * $itemData['unit_price'],
                    'package_id' => $packageId, // ✅ هذا هو المفتاح
                ]);
            }
        }

        // تسجيل نشاط في العميل المحتمل
        LeadActivity::create([
            'lead_id' => $request->lead_id,
            'user_id' => auth()->id() ?? 1,
            'activity_type' => 'quote_created',
            'description' => "تم إنشاء عرض سعر جديد رقم: {$quote->quote_number}",
        ]);

        DB::commit();

        // تحديد الإجراء: مسودة أم إرسال؟
        if ($request->has('action') && $request->action === 'send') {
            // يمكنك هنا إرسال بريد إلكتروني أو تغيير الحالة لـ 'sent'
            $quote->update(['status' => 'sent']);
            // إرسال بريد (اختياري)
        }

        return redirect()->route('admin.crm.quotes.show', $quote)
            ->with('success', 'تم إنشاء عرض السعر بنجاح');

   /* } catch (\Exception $e) {
        DB::rollback();
        \Log::error('Error creating quote: ' . $e->getMessage());
        return redirect()->back()
            ->with('error', 'حدث خطأ أثناء حفظ عرض السعر. يُرجى المحاولة لاحقًا.')
            ->withInput();
    }*/
}


    /**
     * Show quote details.
     */
    public function showQuote(Quote $quote)
    {
        $quote->load(['lead', 'createdBy', 'items']); // ✅ أضف items هنا
        return view('admin.crm.quotes.show', compact('quote'));
    }

    /**
     * Display sales funnel.
     */
    public function funnel()
    {
        $stats = [
            'total_leads' => Lead::count(),
            'new_leads' => Lead::where('status', 'new')->count(),
            'contacted_leads' => Lead::where('status', 'contacted')->count(),
            'interested_leads' => Lead::where('status', 'interested')->count(),
            'quoted_leads' => Quote::where('status', 'sent')->count(),
            'converted_leads' => Lead::where('status', 'converted')->count(),
            'conversion_rate' => Lead::count() > 0 ? (Lead::where('status', 'converted')->count() / Lead::count()) * 100 : 0,
        ];

        $funnelData = [
            'new' => $stats['new_leads'],
            'contacted' => $stats['contacted_leads'],
            'interested' => $stats['interested_leads'],
            'quoted' => $stats['quoted_leads'],
            'converted' => $stats['converted_leads'],
        ];

        $conversionRates = [
            'contacted_rate' => $funnelData['new'] > 0 ? ($funnelData['contacted'] / $funnelData['new']) * 100 : 0,
            'interested_rate' => $funnelData['contacted'] > 0 ? ($funnelData['interested'] / $funnelData['contacted']) * 100 : 0,
            'quoted_rate' => $funnelData['interested'] > 0 ? ($funnelData['quoted'] / $funnelData['interested']) * 100 : 0,
            'converted_rate' => $funnelData['quoted'] > 0 ? ($funnelData['converted'] / $funnelData['quoted']) * 100 : 0,
        ];

        return view('admin.crm.enhanced.funnel', compact('stats', 'funnelData', 'conversionRates'));
    }

    /**
     * Display activities timeline.
     */
    public function activities(Request $request)
    {
        $query = LeadActivity::with(['lead', 'user']);

        // Apply filters
        if ($request->filled('activity_type')) {
            $query->where('activity_type', $request->activity_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $activities = $query->orderBy('created_at', 'desc')->paginate(50);

        // Calculate statistics
        $stats = [
            'total_activities' => LeadActivity::count(),
            'calls' => LeadActivity::where('activity_type', 'call')->count(),
            'meetings' => LeadActivity::where('activity_type', 'meeting')->count(),
            'emails' => LeadActivity::where('activity_type', 'email')->count(),
        ];

        return view('admin.crm.enhanced.activities', compact('activities', 'stats'));
    }

    /**
     * Prepare CRM chart data.
     */
    private function prepareCRMChartData()
    {
        // Lead status distribution
        $statusData = [
            'new' => Lead::where('status', 'new')->count(),
            'contacted' => Lead::where('status', 'contacted')->count(),
            'interested' => Lead::where('status', 'interested')->count(),
            'not_interested' => Lead::where('status', 'not_interested')->count(),
            'converted' => Lead::where('status', 'converted')->count(),
        ];

        // Lead source distribution
        $sourceData = Lead::select('source', DB::raw('count(*) as count'))
            ->groupBy('source')
            ->pluck('count', 'source')
            ->toArray();

        // Monthly leads
        $monthlyLabels = [];
        $monthlyLeads = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyLabels[] = $date->format('M Y');

            $monthLeads = Lead::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $monthlyLeads[] = $monthLeads;
        }

        return [
            'status' => $statusData,
            'source' => $sourceData,
            'monthly' => [
                'labels' => $monthlyLabels,
                'leads' => $monthlyLeads,
            ],
        ];
    }
}

