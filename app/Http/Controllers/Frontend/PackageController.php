<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\SeoSetting;
use App\Models\Package;
use App\Models\Testimonial;
use App\Models\Faq;

class PackageController extends Controller
{
    public function index()
    {
        $seo = SeoSetting::where('page','category')->first();

        $packages = Package::with(['images', 'units.designs','units.items'])->get();

        // أنواع الباكجات (حسب الاسم)
        $packageTypes = $packages->pluck('name_'.app()->getLocale())->unique();

        // الألوان من كل العناصر

        $locale = app()->getLocale();
        $uniqueKey = $locale === 'ar' ? 'color_ar' : 'color_en';

        $colors = $packages
            ->flatMap(fn($package) => $package->units)
            ->flatMap(fn($unit) => $unit->items)
            ->filter(fn($item) =>
                !empty($item->color_ar) &&
                !empty($item->color_en) &&
                !empty($item->background_color)
            )
            ->map(fn($item) => [
                'color_ar' => $item->color_ar,
                'color_en' => $item->color_en,
                'background_color' => $item->background_color,
            ])
            ->keyBy(fn($item) => $item[$uniqueKey]) // 👈 هذا هو الحل السحري
            ->values(); // 👈 بيحوله لـ indexed array تاني — بدون keys

            $mobileColors = $colors;

        return view('frontend.categories.index', compact('seo','packages','packageTypes','colors','mobileColors'));
    }

    public function show($id)
{
    $seo = SeoSetting::where('page','category')->first();

    //dd($id);
    $package = Package::where('id',$id)->with(['images', 'units.designs','units.items'])->first();

    //$package->load(['images', 'units.designs', 'units.items']);

    // For the testimonials and FAQs, you need to define these or get from database
    $testimonials = Testimonial::latest()->take(10)->get();

    if (app()->getLocale() == 'ar') {
        $faqs = Faq::where('page', 'category')
                ->orderBy('sort', 'asc')
                ->get();
    } else {
        $faqs = Faq::where('page', 'category')
                ->orderBy('sort', 'asc')
                ->get();
    }
    return view('frontend.categories.show', compact('seo','package', 'testimonials', 'faqs'));
}
}
