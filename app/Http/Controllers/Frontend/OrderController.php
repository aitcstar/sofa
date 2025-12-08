<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SeoSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            "coupon_code" => "nullable|exists:coupons,code",
        ]);

        $cartItems = Cart::with("package")->where("user_id", auth()->id())->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route("cart.index")->with("error", "Your cart is empty!");
        }

        $totalAmount = $cartItems->sum(function ($item) {
            return $item->package->price * $item->quantity;
        });

        $discountAmount = 0;
        $couponId = null;

        if ($request->coupon_code) {
            $coupon = Coupon::where("code", $request->coupon_code)->first();
            if ($coupon) {
                if ($coupon->type == "fixed") {
                    $discountAmount = $coupon->value;
                } else {
                    $discountAmount = ($totalAmount * $coupon->value) / 100;
                }
                $couponId = $coupon->id;
            }
        }

        $finalAmount = $totalAmount - $discountAmount;

        DB::beginTransaction();

        try {
            $order = Order::create([
                "user_id" => auth()->id(),
                "order_number" => "ORD-" . time(),
                "total_amount" => $totalAmount,
                "discount_amount" => $discountAmount,
                "final_amount" => $finalAmount,
                "payment_status" => "pending",
                "order_status" => "pending",
                "coupon_id" => $couponId,
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    "order_id" => $order->id,
                    "package_id" => $item->package_id,
                    "quantity" => $item->quantity,
                    "price" => $item->package->price,
                ]);
            }

            Cart::where("user_id", auth()->id())->delete();

            DB::commit();

            return redirect()->route("orders.show", $order)->with("success", "Order placed successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route("cart.index")->with("error", "Something went wrong!");
        }
    }

    public function show(Order $order)
    {
        $seo = SeoSetting::where('page', 'blog')->first();
        return view("frontend.order.order-details", compact("order",'seo'));
    }

    public function myOrders()
    {
        $seo = SeoSetting::where('page', 'blog')->first();

        $orders = Order::with(['orderItems.package'])
        ->where('user_id', auth()->id())
        ->orderBy('id', 'desc')
        ->paginate(10);


        return view('frontend.order.my-orders', compact('orders','seo'));
    }


}
