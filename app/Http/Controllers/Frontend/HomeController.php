<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Testimonial;
use App\Models\Faq;
use App\Models\HeroSlider;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::active()->ordered()->take(4)->get();
        $featured_products = Product::active()->featured()->take(8)->get();
        $testimonials = Testimonial::latest()->take(10)->get();
        $faqs = Faq::latest()->take(10)->get();
        $sliders = HeroSlider::where('is_active', true)->orderBy('order')->get();

        return view('frontend.home', compact('categories', 'featured_products','testimonials','faqs','sliders'));
    }
}
