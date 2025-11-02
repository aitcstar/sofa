<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\SeoSetting;
use App\Models\Package;
use App\Models\Testimonial;
use App\Models\Faq;
use App\Models\PackageUnitItem;
use App\Models\Unit;
class PackageController extends Controller
{
    /*public function index()
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

    public function index()
{
    $seo = SeoSetting::where('page','category')->first();

    $packages = Package::with([
        'images',
        'units.designs',
        'packageUnitItems.unit',
        'packageUnitItems.item'
    ])->get();

    $unitTypes = $packages
        ->flatMap(fn($package) => $package->units->map(fn($unit) => [
            'name_ar' => $unit->name_ar,
            'name_en' => $unit->name_en,
        ]))
        ->unique(fn($item) => $item['name_ar'].'-'.$item['name_en'])
        ->values();

    $locale = app()->getLocale();
    $uniqueKey = $locale === 'ar' ? 'color_ar' : 'color_en';

    $colors = $package->packageUnitItems
    ->pluck('item.background_color')
    ->filter()
    ->unique()
    ->take(4);


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
*/


public function index()
{
    $seo = SeoSetting::where('page','category')->first();

    $packages = Package::with([
        'images',
        'units.designs',
        'packageUnitItems.unit',
        'packageUnitItems.item'
    ])->get();

    $unitTypes = Unit::select('name_ar', 'name_en')
    ->distinct()
    ->get();


    // الألوان
    $locale = app()->getLocale();
    $uniqueKey = $locale === 'ar' ? 'color_ar' : 'color_en';

    $colors = $packages
        ->flatMap(fn($package) => $package->packageUnitItems)
        ->map(fn($pui) => [
            'color_ar' => $pui->item->color_ar,
            'color_en' => $pui->item->color_en,
            'background_color' => $pui->item->background_color,
        ])
        ->filter(fn($item) => !empty($item['background_color']))
        ->keyBy(fn($item) => $item[$uniqueKey])
        ->values();

    $mobileColors = $colors;

    return view('frontend.categories.index', compact('seo','packages','unitTypes','colors','mobileColors'));
}
/*
public function show($id)
{
    $seo = SeoSetting::where('page','category')->first();

    $package = Package::where('id',$id)
        ->with(['images', 'units.designs','units.images','packageUnitItems.unit','packageUnitItems.item'])
        ->first();

    $testimonials = Testimonial::latest()->take(10)->get();

    $faqs = Faq::where('page', 'category')
                ->orderBy('sort', 'asc')
                ->get();
// استخراج أنواع الوحدات بدون تكرار من جدول الوحدات مباشرة
$unitTypes = Unit::select('name_ar', 'name_en')
->distinct()
->get();

    return view('frontend.categories.show', compact('seo','package', 'testimonials', 'faqs','unitTypes'));
}*/

public function show($id)
{
    $seo = SeoSetting::where('page', 'category')->first();

    // تحميل الباكج مع العلاقات عبر الجدول الوسيط
    $package = Package::with([
        'images',
        'packageUnitItems.unit.images', // صور الوحدات
        'packageUnitItems.item'         // القطع
    ])->findOrFail($id);

    $testimonials = Testimonial::latest()->take(10)->get();

    $faqs = Faq::where('page', 'category')
                ->orderBy('sort', 'asc')
                ->get();

    // استخراج أنواع الوحدات الفريدة من الجدول الوسيط
    $unitTypes = PackageUnitItem::with('unit:id,type,name_ar,name_en')
    ->where('package_id', $id)
    ->get()
    ->pluck('unit')
    ->unique('id')
    ->values()
    ->map(function ($unit) {
        return [
            'type' => $unit->type,
            'name_ar' => $unit->name_ar,
            'name_en' => $unit->name_en,
        ];
    });

    return view('frontend.categories.show', compact('seo', 'package', 'testimonials', 'faqs', 'unitTypes'));
}
public function filter(Request $request)
{
    //dd($request->all());
    $query = Package::query()->with(['packageUnitItems.unit','packageUnitItems.item']);

    $answers = $request->answers ?? [];

    // فلترة حسب اسم الباقة (عربي أو إنجليزي)
    if (!empty($answers[5])) {
        $packageName = $answers[5];
        //dd($packageName);
        $query->where(function ($q) use ($packageName) {
            $q->where('name_ar', 'LIKE', "%{$packageName}%")
            ->orWhere('name_en', 'LIKE', "%{$packageName}%");
        });
    }


    // فلترة حسب اسم الوحدة
    /*if (!empty($answers[5][0])) {
        $unitName = $answers[5][0];
        $query->whereHas('packageUnitItems.unit', function ($q) use ($unitName) {
            $q->where('name_ar', 'LIKE', "%{$unitName}%")
              ->orWhere('name_en', 'LIKE', "%{$unitName}%");
        });
    }*/

    // فلترة حسب الألوان
    if (!empty($answers[6])) {
        $color = $answers[6];
        $query->whereHas('packageUnitItems.item', function ($q) use ($color) {
            $q->where('background_color', $color)
              ->orWhere('color_ar', 'LIKE', "%{$color}%")
              ->orWhere('color_en', 'LIKE', "%{$color}%");
        });
    }

    // فلترة حسب عدد القطع
    if (!empty($answers[7])) {
        $pieces = $answers[7];
        $query->whereHas('packageUnitItems.item', function ($q) use ($pieces) {
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
