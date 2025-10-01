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

        // 👈 استخراج أنواع الوحدات بدون تكرار
        $unitTypes = $packages
        ->flatMap(fn($package) => $package->units->map(fn($unit) => [
            'name_ar' => $unit->name_ar,
            'name_en' => $unit->name_en,
        ]))
        ->unique(function ($item) {
            return $item['name_ar'] . '-' . $item['name_en'];
        })
        ->values();


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
            ->keyBy(fn($item) => $item[$uniqueKey]) // يضمن التميّز
            ->values();

        $mobileColors = $colors;

        return view('frontend.categories.index', compact('seo','packages','unitTypes','colors','mobileColors'));
    }


    public function show($id)
{
    $seo = SeoSetting::where('page','category')->first();

    //dd($id);
    $package = Package::where('id',$id)->with(['images', 'units.designs','units.images','units.items'])->first();

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
public function filter(Request $request)
{
    $query = Package::query()->with('units.items');

    $answers = $request->answers ?? [];

    // فلترة حسب اسم الوحدة
    if (!empty($answers[5][0])) {
        $unitName = $answers[5][0];
        $query->whereHas('units', function ($q) use ($unitName) {
            $q->where('name_ar', 'LIKE', "%{$unitName}%")
              ->orWhere('name_en', 'LIKE', "%{$unitName}%");
        });
    }

    // فلترة حسب الألوان
    if (!empty($answers[6][0])) {
        $color = $answers[6][0];
        $query->whereHas('units.items', function ($q) use ($color) {
            $q->where('background_color', $color)
              ->orWhere('color_ar', 'LIKE', "%{$color}%")
              ->orWhere('color_en', 'LIKE', "%{$color}%");
        });
    }

    // فلترة حسب عدد القطع
    if (!empty($answers[7][0])) {
        $pieces = $answers[7][0];
        $query->whereHas('units.items', function ($q) use ($pieces) {
            $q->where('quantity', $pieces);
        });
    }

    // فلترة بالسعر
    if (!empty($request->price_min) && !empty($request->price_max)) {
        $query->whereBetween('price', [$request->price_min, $request->price_max]);
    }

    // إذا لم يكن هناك فلتر (أي عند أول تحميل الصفحة) نجيب أول 4 باكجات فقط
    if (empty($request->all())) {
        $packages = $query->take(4)->get();
    } else {
        $packages = $query->get();
    }

    return view('frontend.categories._section', compact('packages'));
}







}
