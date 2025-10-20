<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Order;
use App\Models\SeoSetting;
use App\Models\AboutPage;
use App\Models\OrderStage;
use App\Models\OrderLog;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function confirm($id)
    {
        $seo = SeoSetting::where('page','about')->first();
        $sections = AboutPage::all();
        $package = Package::with([
            'packageUnitItems.unit.images',
            'packageUnitItems.item'
        ])->findOrFail($id);

        $countries = [
            ['code' => 'sa', 'name_ar' => 'السعودية', 'name_en' => 'Saudi Arabia', 'dial_code' => '+966'],
            ['code' => 'ae', 'name_ar' => 'الإمارات', 'name_en' => 'United Arab Emirates', 'dial_code' => '+971'],
            ['code' => 'kw', 'name_ar' => 'الكويت', 'name_en' => 'Kuwait', 'dial_code' => '+965'],
            ['code' => 'qa', 'name_ar' => 'قطر', 'name_en' => 'Qatar', 'dial_code' => '+974'],
            ['code' => 'bh', 'name_ar' => 'البحرين', 'name_en' => 'Bahrain', 'dial_code' => '+973'],
            ['code' => 'om', 'name_ar' => 'عمان', 'name_en' => 'Oman', 'dial_code' => '+968'],
            ['code' => 'jo', 'name_ar' => 'الأردن', 'name_en' => 'Jordan', 'dial_code' => '+962'],
            ['code' => 'lb', 'name_ar' => 'لبنان', 'name_en' => 'Lebanon', 'dial_code' => '+961'],
            ['code' => 'eg', 'name_ar' => 'مصر', 'name_en' => 'Egypt', 'dial_code' => '+20'],
            ['code' => 'ma', 'name_ar' => 'المغرب', 'name_en' => 'Morocco', 'dial_code' => '+212'],
            ['code' => 'dz', 'name_ar' => 'الجزائر', 'name_en' => 'Algeria', 'dial_code' => '+213'],
            ['code' => 'tn', 'name_ar' => 'تونس', 'name_en' => 'Tunisia', 'dial_code' => '+216'],
            ['code' => 'sd', 'name_ar' => 'السودان', 'name_en' => 'Sudan', 'dial_code' => '+249'],
            ['code' => 'iq', 'name_ar' => 'العراق', 'name_en' => 'Iraq', 'dial_code' => '+964'],
            ['code' => 'sy', 'name_ar' => 'سوريا', 'name_en' => 'Syria', 'dial_code' => '+963'],
            ['code' => 'ye', 'name_ar' => 'اليمن', 'name_en' => 'Yemen', 'dial_code' => '+967'],
            ['code' => 'ps', 'name_ar' => 'فلسطين', 'name_en' => 'Palestine', 'dial_code' => '+970'],
            ['code' => 'us', 'name_ar' => 'الولايات المتحدة', 'name_en' => 'United States', 'dial_code' => '+1'],
            ['code' => 'gb', 'name_ar' => 'المملكة المتحدة', 'name_en' => 'United Kingdom', 'dial_code' => '+44'],
            ['code' => 'de', 'name_ar' => 'ألمانيا', 'name_en' => 'Germany', 'dial_code' => '+49'],
            ['code' => 'fr', 'name_ar' => 'فرنسا', 'name_en' => 'France', 'dial_code' => '+33'],
            ['code' => 'it', 'name_ar' => 'إيطاليا', 'name_en' => 'Italy', 'dial_code' => '+39'],
            ['code' => 'es', 'name_ar' => 'إسبانيا', 'name_en' => 'Spain', 'dial_code' => '+34'],
            ['code' => 'ca', 'name_ar' => 'كندا', 'name_en' => 'Canada', 'dial_code' => '+1'],
            ['code' => 'au', 'name_ar' => 'أستراليا', 'name_en' => 'Australia', 'dial_code' => '+61'],
            ['code' => 'in', 'name_ar' => 'الهند', 'name_en' => 'India', 'dial_code' => '+91'],
            ['code' => 'pk', 'name_ar' => 'باكستان', 'name_en' => 'Pakistan', 'dial_code' => '+92'],
            ['code' => 'tr', 'name_ar' => 'تركيا', 'name_en' => 'Turkey', 'dial_code' => '+90'],
            ['code' => 'ir', 'name_ar' => 'إيران', 'name_en' => 'Iran', 'dial_code' => '+98'],
        ];

        return view('frontend.pages.confirm-order', compact('package','seo','sections','countries'));
    }


    /*public function store(Request $request, $id)
    {
        // التحقق من صحة البيانات
        $request->validate([
            'phone' => 'required|string',
            'country_code' => 'required|string',
            'units_count' => 'required|integer|min:1',
            'project_type' => 'required|in:large,medium,small',
            'current_stage' => 'required|in:design,execution,operation',
            'diagrams_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB
            'commercial_register' => 'required_if:client_type,company|string|nullable',
            'tax_number' => 'required_if:client_type,company|string|nullable',
            'total_price' => 'nullable|numeric|min:0',
        ]);

        // Get package to calculate price
        $package = Package::findOrFail($id);

        // Calculate total price
        $basePrice = $package->price ?? 0;
        $unitsCount = $request->units_count;
        $totalPrice = $basePrice * $unitsCount;

        // Apply discounts or additional fees based on project type
        $priceMultiplier = 1;
        switch ($request->project_type) {
            case 'large':
                $priceMultiplier = 1.2; // 20% increase for large projects
                break;
            case 'medium':
                $priceMultiplier = 1.0; // No change
                break;
            case 'small':
                $priceMultiplier = 0.9; // 10% discount for small projects
                break;
        }

        $totalPrice = $totalPrice * $priceMultiplier;

        // Calculate tax (15% VAT for Saudi Arabia)
        $taxRate = 0.15;
        $taxAmount = $totalPrice * $taxRate;
        $finalPrice = $totalPrice + $taxAmount;

        // جمع بيانات الطلب
        $orderData = [
            'user_id' => auth()->id(),
            'package_id' => $id,
            'name' => auth()->check() ? auth()->user()->name : $request->name,
            'phone' => $request->phone,
            'country_code' => $request->country_code,
            'email' => $request->email,
            'units_count' => $request->units_count,
            'project_type' => $request->project_type,
            'current_stage' => $request->current_stage,
            'has_interior_design' => $request->has_interior_design == '1',
            'needs_finishing_help' => $request->needs_finishing_help == '1',
            'needs_color_help' => $request->needs_color_help == '1',
            'colors' => $request->colors,
            'order_number' => 'TEMP-' . time(),
            'client_type' => $request->client_type,
            'commercial_register' => $request->commercial_register,
            'tax_number' => $request->tax_number,
            // Price fields
            'total_price' => $totalPrice,
            'discount_amount' => 0,
            'tax_amount' => $taxAmount,
            'final_price' => $finalPrice,
            'currency' => 'SAR',
        ];

        // رفع الملف إذا وُجد
        if ($request->hasFile('diagrams_file')) {
            $filePath = $request->file('diagrams_file')->store('order_diagrams', 'public');
            $orderData['diagrams_path'] = $filePath;
        }

        // إنشاء الطلب
        $order = Order::create($orderData);

        // توليد رقم الطلب النهائي
        $order->order_number = $order->generateOrderNumber();
        $order->save();

        return redirect()->route('order.success', ['order_id' => $order->id])
                     ->with('success', __('site.order_submitted'));
        //return redirect()->route('order.success')->with('success', __('site.order_submitted'));
    }*/


    public function store(Request $request, $id)
    {
        $user = auth()->user();
        $package = Package::findOrFail($id);

        // التحقق من البيانات الأساسية
        $validated = $request->validate([
            'phone' => 'required|string|max:20',
            'country_code' => 'required|string|max:10',
            'email' => 'nullable|email',
            'units_count' => 'required|integer|min:1',
            'project_type' => 'required|string',
            'current_stage' => 'required|string',
            'has_interior_design' => 'required|boolean',
            'needs_finishing_help' => 'required|boolean',
            'needs_color_help' => 'required|boolean',
            'client_type' => 'required|string|in:individual,company',
            'commercial_register' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:255',
            'diagrams_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        // ✅ حساب السعر والضريبة والإجمالي
        $baseAmount = $package->price * $validated['units_count'];
        $taxAmount = $baseAmount * 0.15;
        $totalAmount = $baseAmount + $taxAmount;

        // ✅ حفظ ملف الرسومات إن وجد
        $diagramPath = null;
        if ($request->hasFile('diagrams_file')) {
            $diagramPath = $request->file('diagrams_file')->store('diagrams', 'public');
        }

        // ✅ إنشاء الطلب
        $order = Order::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'name' => $user->name ?? 'عميل جديد',
            'phone' => $validated['phone'],
            'country_code' => $validated['country_code'],
            'email' => $validated['email'] ?? $user->email,
            'units_count' => $validated['units_count'],
            'project_type' => $validated['project_type'],
            'current_stage' => $validated['current_stage'],
            'has_interior_design' => $validated['has_interior_design'],
            'needs_finishing_help' => $validated['needs_finishing_help'],
            'needs_color_help' => $validated['needs_color_help'],
            'diagrams_path' => $diagramPath,
            'client_type' => $validated['client_type'],
            'commercial_register' => $validated['commercial_register'] ?? null,
            'tax_number' => $validated['tax_number'] ?? null,
            'base_amount' => $baseAmount,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        // ربط المراحل بالطلب الجديد تلقائيًا
        $stages = OrderStage::orderBy('order_number')->get();

        foreach ($stages as $stage) {
            $order->stageStatuses()->create([
                'order_stage_id' => $stage->id,
                'status' => 'not_started',
            ]);
        }

         // Log order creation
         OrderLog::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'action' => 'created',
            'description' => 'تم إنشاء الطلب من لوحة التحكم',
        ]);


        // ✅ توجيه المستخدم إلى صفحة النجاح
        return redirect()->route('order.success', ['order_id' => $order->id])
        ->with('success', __('site.order_submitted'));


    }


    public function success(Request $request, $order_id = null)
    {
        $seo = SeoSetting::where('page','about')->first();
        $sections = AboutPage::all();

        return view('frontend.pages.success', compact('seo', 'sections', 'order_id'));
    }

    public function show(Order $order)
    {
        // تأكد أن الطلب يخص المستخدم الحالي
        if (auth()->id() !== $order->user_id && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $seo = SeoSetting::where('page','about')->first();
        $sections = AboutPage::all();
        $order->load(['package', 'assignedEmployee', 'timeline.assignedUser']);

        return view('frontend.pages.order-details', compact('seo', 'sections','order'));
    }


    public function myOrders()
{
    $seo = SeoSetting::where('page','about')->first();
        $sections = AboutPage::all();

    $orders = Order::with(['package'])
        ->where('user_id', auth()->id())
        ->latest()
        ->paginate(10);

    return view('frontend.pages.my-orders', compact('seo', 'sections','orders'));
}

public function showInvoice(Request $request,Order $order)
{
    // تأكد أن الطلب يخص المستخدم الحالي
    if (auth()->id() !== $order->user_id && !auth()->user()->isAdmin()) {
        abort(403);
    }

    //$order->load(['package', 'package.packageUnitItems.unit.images', 'package.packageUnitItems.item']);

    $invoice = Invoice::with([
        'customer',
        'assignedEmployee',
        'payments',
        'package.packageUnitItems.unit',
        'package.packageUnitItems.item',
        'package.images',
    ])->where('order_id', $order->id)->first();


    return view('frontend.pages.order-invoice', compact('invoice'));
}
}
