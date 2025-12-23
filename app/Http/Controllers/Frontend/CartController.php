<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use App\Models\Package;
use App\Models\SeoSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::with("package")->where("user_id", auth()->id())->get();

        $seo = new \stdClass();
        $seo->title = app()->getLocale() == 'ar' ? 'السلة' : 'Cart';
        $seo->index_status = 'noindex';
        $seo->slug_en = 'cart'; // أو أي slug ديناميكي حسب الصفحة
        $seo->slug_ar = 'السلة';
        $seo->meta_title_ar = 'السلة';
        $seo->meta_title_en = 'cart';
        return view("frontend.cart.index", compact("cartItems","seo"));
    }

    public function checkout()
    {
        $seo = new \stdClass();
        $seo->title = aapp()->getLocale() == 'ar' ? 'الدفع' : 'checkout';
        $seo->index_status = 'noindex';
        $seo->slug_en = 'checkout'; // أو أي slug ديناميكي حسب الصفحة
        $seo->slug_ar = 'الدفع';
        $seo->meta_title_ar = 'الدفع';
        $seo->meta_title_en = 'checkout';
        return view("frontend.cart.checkout", compact("seo"));
    }

    public function add(Request $request)
    {
        $request->validate([
            "package_id" => "required|exists:packages,id",
            "quantity" => "required|integer|min:1",
        ]);

        $cartItem = Cart::where("user_id", auth()->id())
            ->where("package_id", $request->package_id)
            ->first();

        if ($cartItem) {
            $cartItem->increment("quantity", $request->quantity);
        } else {
            Cart::create([
                "user_id" => auth()->id(),
                "package_id" => $request->package_id,
                "quantity" => $request->quantity,
            ]);
        }

        return redirect()->route("cart.index")->with("success", "Package added to cart!");
    }

    public function update(Request $request, Cart $cart)
    {
        $request->validate([
            "quantity" => "required|integer|min:1",
        ]);

        $cart->update(["quantity" => $request->quantity]);

        return redirect()->route("cart.index")->with("success", "Cart updated!");
    }

    public function remove(Cart $cart)
    {
        $cart->delete();

        return redirect()->route("cart.index")->with("success", "Package removed from cart!");
    }

    public function applyCoupon(Request $request)
    {
        try {
            $code = $request->input('code');
            $subtotal = $request->input('subtotal');

            $coupon = Coupon::where('code', $code)->first();

            if (!$coupon) {
                return response()->json([
                    'success' => false,
                    'message' => 'كود الخصم غير صحيح'
                ]);
            }

            if (!$coupon->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'كود الخصم غير صالح أو منتهي الصلاحية'
                ]);
            }

            // Check if user can use this coupon
            if (auth()->check() && !$coupon->canBeUsedBy(auth()->id())) {
                return response()->json([
                    'success' => false,
                    'message' => 'لقد استخدمت هذا الكود من قبل'
                ]);
            }

            $discount = $coupon->calculateDiscount($subtotal);

            return response()->json([
                'success' => true,
                'message' => 'تم تطبيق كود الخصم بنجاح',
                'discount_amount' => $discount,
                'coupon_id' => $coupon->id
            ]);

        } catch (\Exception $e) {
            Log::error('Error applying coupon: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تطبيق الكود'
            ]);
        }
    }

    public function placeOrder(Request $request)
    {
        if (!auth()->check()) {
            session()->put('checkout_form_data', $request->all());
            return redirect()->route('cart.checkout')
                             ->with('open_login_tab', true);
        }



        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'country_code' => 'required|string|max:10',
            'units_count' => 'required|integer|min:1',
            'project_type' => 'required|in:small,medium,large',
            'current_stage' => 'required|in:design,execution,operation',
            'cart_data' => 'required|json',
        ]);

        try {
            DB::beginTransaction();

            $cartData = json_decode($request->cart_data, true);

            if (empty($cartData)) {
                return redirect()->back()->with('error', 'السلة فارغة');
            }

            // حساب المبالغ
            $subtotal = 0;
            foreach ($cartData as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }

            $discount = 0;
            $couponId = null;

            if ($request->coupon_code) {
                $coupon = Coupon::where('code', $request->coupon_code)->first();
                if ($coupon && $coupon->isValid()) {
                    $discount = $coupon->calculateDiscount($subtotal);
                    $couponId = $coupon->id;
                }
            }

            $afterDiscount = $subtotal - $discount;
            $taxRate = 0.15; // 15% VAT
            $taxAmount = $afterDiscount * $taxRate;
            $totalAmount = $afterDiscount + $taxAmount;

            // إنشاء الطلب
            $order = Order::create([
                'user_id' => auth()->check() ? auth()->id() : null,
                'coupon_id' => $couponId,
                'package_id' => $cartData[0]['id'], // أول باكج في السلة
                'order_number' => $this->generateOrderNumber(),
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'country_code' => $request->country_code,
                'units_count' => $request->units_count,
                'project_type' => $request->project_type,
                'current_stage' => $request->current_stage,
                'has_interior_design' => $request->has('has_interior_design'),
                'needs_finishing_help' => $request->has('needs_finishing_help'),
                'needs_color_help' => $request->has('needs_color_help'),
                'internal_notes' => $request->internal_notes,
                'base_amount' => $subtotal,
                'discount_amount' => $discount,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
                'payment_status' => 'unpaid',
                'status' => 'pending',
                'priority' => 2,
            ]);

            // إضافة عناصر الطلب
            foreach ($cartData as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'package_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            // تسجيل استخدام الكوبون
            if ($couponId) {
                $coupon->use($order->id, auth()->id() ?? null, $discount, $subtotal);
            }

            // تسجيل النشاط
            $order->logActivity(
                'order_created',
                'تم إنشاء الطلب',
                auth()->id() ?? null
            );

            DB::commit();

            return redirect()
                ->route(app()->getLocale() == 'ar' ? 'order.success' : 'order.success.en', ['order' => $order->id])
                ->with('success', 'تم إنشاء طلبك بنجاح! رقم الطلب: ' . $order->order_number);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating order: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الطلب. الرجاء المحاولة مرة أخرى.');
        }
    }

    private function generateOrderNumber()
    {
        $year = date('Y');
        $lastOrder = Order::whereYear('created_at', $year)->orderBy('id', 'desc')->first();

        if ($lastOrder) {
            $lastNumber = intval(substr($lastOrder->order_number, -6));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'ORD-' . $year . '-' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    public function orderSuccess($orderId)
    {
        $order = Order::with('items.package')->findOrFail($orderId);
        $seo = SeoSetting::where('page', 'blog')->first();
        return view('frontend.cart.success', compact('order', 'seo'));
    }
}
