<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    /**
     * Display a listing of coupons
     */
    public function index(Request $request)
    {
        $query = Coupon::with(['createdBy', 'usages'])->orderBy('created_at', 'desc');

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->valid();
                    break;
                case 'expired':
                    $query->expired();
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
            }
        }

        // فلترة حسب النوع
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $coupons = $query->paginate(20);

        // إحصائيات سريعة
        $stats = [
            'total' => Coupon::count(),
            'active' => Coupon::valid()->count(),
            'expired' => Coupon::expired()->count(),
            'inactive' => Coupon::where('is_active', false)->count(),
            'total_usage' => Coupon::sum('used_count'),
            'total_discount' => Coupon::join('coupon_usages', 'coupons.id', '=', 'coupon_usages.coupon_id')
                                     ->sum('coupon_usages.discount_amount')
        ];

        return view('admin.coupons.index', compact('coupons', 'stats'));
    }

    /**
     * Show the form for creating a new coupon
     */
    public function create()
    {
        $packages = Package::all();
        $customers = User::where('role', 'customer')->get();
        
        return view('admin.coupons.create', compact('packages', 'customers'));
    }

    /**
     * Store a newly created coupon
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'nullable|string|max:50|unique:coupons,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed_amount',
            'value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_customer' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'applicable_packages' => 'nullable|array',
            'applicable_packages.*' => 'exists:packages,id',
            'applicable_customers' => 'nullable|array',
            'applicable_customers.*' => 'exists:users,id'
        ]);

        // التحقق من صحة النسبة المئوية
        if ($request->type === 'percentage' && $request->value > 100) {
            $validator->after(function ($validator) {
                $validator->errors()->add('value', 'النسبة المئوية يجب أن تكون أقل من أو تساوي 100%');
            });
        }

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $coupon = Coupon::create([
            'code' => $request->code ?: Coupon::generateUniqueCode(),
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'value' => $request->value,
            'minimum_amount' => $request->minimum_amount,
            'maximum_discount' => $request->maximum_discount,
            'usage_limit' => $request->usage_limit,
            'usage_limit_per_customer' => $request->usage_limit_per_customer,
            'starts_at' => $request->starts_at,
            'expires_at' => $request->expires_at,
            'applicable_packages' => $request->applicable_packages,
            'applicable_customers' => $request->applicable_customers,
            'created_by' => auth()->id()
        ]);

        return redirect()->route('admin.coupons.show', $coupon)
                       ->with('success', 'تم إنشاء الكوبون بنجاح');
    }

    /**
     * Display the specified coupon
     */
    public function show(Coupon $coupon)
    {
        $coupon->load(['createdBy', 'usages.user', 'usages.order']);
        
        // إحصائيات الكوبون
        $stats = [
            'total_usage' => $coupon->used_count,
            'remaining_usage' => $coupon->remaining_uses,
            'total_discount' => $coupon->usages->sum('discount_amount'),
            'average_discount' => $coupon->usages->avg('discount_amount'),
            'unique_customers' => $coupon->usages->unique('user_id')->count()
        ];

        return view('admin.coupons.show', compact('coupon', 'stats'));
    }

    /**
     * Show the form for editing the specified coupon
     */
    public function edit(Coupon $coupon)
    {
        $packages = Package::all();
        $customers = User::where('role', 'customer')->get();
        
        return view('admin.coupons.edit', compact('coupon', 'packages', 'customers'));
    }

    /**
     * Update the specified coupon
     */
    public function update(Request $request, Coupon $coupon)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed_amount',
            'value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_customer' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'applicable_packages' => 'nullable|array',
            'applicable_packages.*' => 'exists:packages,id',
            'applicable_customers' => 'nullable|array',
            'applicable_customers.*' => 'exists:users,id'
        ]);

        // التحقق من صحة النسبة المئوية
        if ($request->type === 'percentage' && $request->value > 100) {
            $validator->after(function ($validator) {
                $validator->errors()->add('value', 'النسبة المئوية يجب أن تكون أقل من أو تساوي 100%');
            });
        }

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $coupon->update([
            'code' => $request->code,
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'value' => $request->value,
            'minimum_amount' => $request->minimum_amount,
            'maximum_discount' => $request->maximum_discount,
            'usage_limit' => $request->usage_limit,
            'usage_limit_per_customer' => $request->usage_limit_per_customer,
            'starts_at' => $request->starts_at,
            'expires_at' => $request->expires_at,
            'applicable_packages' => $request->applicable_packages,
            'applicable_customers' => $request->applicable_customers
        ]);

        return redirect()->route('admin.coupons.show', $coupon)
                       ->with('success', 'تم تحديث الكوبون بنجاح');
    }

    /**
     * Activate/Deactivate coupon
     */
    public function toggleStatus(Coupon $coupon)
    {
        if ($coupon->is_active) {
            $coupon->deactivate();
            $message = 'تم إلغاء تفعيل الكوبون';
        } else {
            $coupon->activate();
            $message = 'تم تفعيل الكوبون';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'status' => $coupon->status
        ]);
    }

    /**
     * Duplicate coupon
     */
    public function duplicate(Coupon $coupon)
    {
        $newCoupon = $coupon->duplicate();

        return redirect()->route('admin.coupons.edit', $newCoupon)
                       ->with('success', 'تم نسخ الكوبون بنجاح');
    }

    /**
     * Validate coupon code
     */
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'order_amount' => 'required|numeric|min:0',
            'customer_id' => 'required|exists:users,id',
            'package_id' => 'nullable|exists:packages,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $coupon = Coupon::findByCode($request->code);

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'كود الكوبون غير صحيح'
            ]);
        }

        if (!$coupon->canBeUsedBy($request->customer_id)) {
            return response()->json([
                'success' => false,
                'message' => 'هذا الكوبون غير متاح للاستخدام'
            ]);
        }

        $discount = $coupon->calculateDiscount($request->order_amount, $request->package_id);

        if ($discount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'هذا الكوبون غير قابل للتطبيق على هذا الطلب'
            ]);
        }

        return response()->json([
            'success' => true,
            'coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'name' => $coupon->name,
                'type' => $coupon->type,
                'value' => $coupon->value,
                'discount_amount' => $discount
            ]
        ]);
    }

    /**
     * Delete coupon
     */
    public function destroy(Coupon $coupon)
    {
        if ($coupon->used_count > 0) {
            return redirect()->route('admin.coupons.index')
                           ->with('error', 'لا يمكن حذف كوبون تم استخدامه');
        }

        $coupon->delete();

        return redirect()->route('admin.coupons.index')
                       ->with('success', 'تم حذف الكوبون بنجاح');
    }

    /**
     * Get coupon statistics
     */
    public function getStats(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period);
        $endDate = now();

        $stats = [
            'total_coupons' => Coupon::whereBetween('created_at', [$startDate, $endDate])->count(),
            'active_coupons' => Coupon::valid()->whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_usage' => Coupon::join('coupon_usages', 'coupons.id', '=', 'coupon_usages.coupon_id')
                                  ->whereBetween('coupon_usages.created_at', [$startDate, $endDate])
                                  ->count(),
            'total_discount' => Coupon::join('coupon_usages', 'coupons.id', '=', 'coupon_usages.coupon_id')
                                     ->whereBetween('coupon_usages.created_at', [$startDate, $endDate])
                                     ->sum('coupon_usages.discount_amount'),
            'top_coupons' => Coupon::withCount(['usages' => function($query) use ($startDate, $endDate) {
                                        $query->whereBetween('created_at', [$startDate, $endDate]);
                                    }])
                                   ->orderBy('usages_count', 'desc')
                                   ->limit(5)
                                   ->get(),
            'coupon_types' => Coupon::selectRaw('type, COUNT(*) as count')
                                   ->whereBetween('created_at', [$startDate, $endDate])
                                   ->groupBy('type')
                                   ->get()
        ];

        return response()->json($stats);
    }

    /**
     * Export coupons
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel');
        $status = $request->get('status');
        $type = $request->get('type');

        $query = Coupon::with(['createdBy']);

        if ($status) {
            switch ($status) {
                case 'active':
                    $query->valid();
                    break;
                case 'expired':
                    $query->expired();
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
            }
        }

        if ($type) {
            $query->byType($type);
        }

        $coupons = $query->get();

        if ($format === 'pdf') {
            return $this->exportToPdf($coupons);
        } else {
            return $this->exportToExcel($coupons);
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

    private function exportToPdf($coupons)
    {
        // تنفيذ تصدير PDF للكوبونات
    }

    private function exportToExcel($coupons)
    {
        // تنفيذ تصدير Excel للكوبونات
    }
}
