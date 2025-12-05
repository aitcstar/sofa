<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Package;
use App\Models\SeoSetting;
use Illuminate\Http\Request;

class CartController extends Controller
{
   public function index()
    {
        $cartItems = Cart::with("package")->where("user_id", auth()->id())->get();
        $seo = SeoSetting::where('page', 'blog')->first();

        return view("frontend.cart.index", compact("cartItems","seo"));
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
}
