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
use App\Models\PackageSlug;

class PackageController extends Controller
{



public function index()
{
    $seo = SeoSetting::where('page','category')->first();

    $packages = Package::with([
        'images',
        'units.designs',
        'packageUnitItems.unit',
        'packageUnitItems.item'
    ])->get();

    // أسماء الباقات
    $packageNames = Package::select('name_ar', 'name_en')
        ->distinct()
        ->get();

    // الألوان من available_colors
    $locale = app()->getLocale();
    $uniqueKey = $locale === 'ar' ? 'name_ar' : 'name_en';

   /* $colors = Package::pluck('available_colors') // جلب كل الألوان
        ->flatten(1)                               // دمج المصفوفات
        ->filter(fn($color) => !empty($color['name_' . $locale])) // إزالة الفارغة
        ->unique(fn($color) => $locale === 'ar' ? $color['name_ar'] : $color['name_en']) // إزالة التكرار
        ->values();*/

        $colors = Package::pluck('available_colors')
    ->flatten(1)
    ->filter(fn($color) => !empty($color['name_' . $locale]))
    ->unique(fn($color) => $locale === 'ar' ? $color['name_ar'] : $color['name_en'])
    ->values();

    $mobileColors = $colors;

    return view('frontend.categories.index', compact('seo','packages','packageNames','colors','mobileColors'));
}


/*
public function show($slug)
{
    // حول slug للحروف الصغيرة فورًا
    $slugLower = strtolower($slug);

    $slugColumn = app()->getLocale() == 'ar' ? 'slug_ar' : 'slug_en';
    $routeName = app()->getLocale() == 'ar' ? 'packages.show' : 'packages.show.en';

    // إذا slug الحالي ليس lowercase، أعد التوجيه للـ lowercase
    if ($slug !== $slugLower) {
        return redirect()->route($routeName, $slugLower, 301);
    }

    // جلب الباقة حسب slug
    $package = Package::with([
        'images',
        'packageUnitItems.unit.images',
        'packageUnitItems.unit.designs',
        'packageUnitItems.item','faqs'
    ])->where($slugColumn, $slugLower)->first();

    // إذا لم توجد الباقة، تحقق من أي slug قديم
    if (!$package) {
        $oldSlug = PackageSlug::where('slug', $slugLower)->first();
        if ($oldSlug) {
            return redirect()->route($routeName, $oldSlug->package->$slugColumn, 301);
        }
        abort(404);
    }

    // تحميل بيانات إضافية
    $seo = SeoSetting::where('page', 'category')->first();

    $testimonials = Testimonial::where('status', 'approved')
        ->where('package_id', $package->id)
        ->get();

    $faqs = Faq::where('page', 'category')
                ->orderBy('sort', 'asc')
                ->get();

    $unitTypes = PackageUnitItem::with('unit:id,type,name_ar,name_en')
        ->where('package_id', $package->id)
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
}*/

public function show($slug)
{
    // حول slug للحروف الصغيرة فورًا
    $slugLower = strtolower($slug);

    $slugColumn = app()->getLocale() == 'ar' ? 'slug_ar' : 'slug_en';
    $routeName = app()->getLocale() == 'ar' ? 'packages.show' : 'packages.show.en';

    // إذا slug الحالي ليس lowercase، أعد التوجيه للـ lowercase
    if ($slug !== $slugLower) {
        return redirect()->route($routeName, $slugLower, 301);
    }

    // جلب الباقة
    $package = Package::with([
        'images',
        'packageUnitItems.unit.images',
        'packageUnitItems.unit.designs',
        'packageUnitItems.item',
        'faqs'
    ])->where($slugColumn, $slugLower)->first();

    // إذا لم توجد الباقة، تحقق من أي slug قديم
    if (!$package) {
        $oldSlug = PackageSlug::where('slug', $slugLower)->first();
        if ($oldSlug) {
            return redirect()->route($routeName, $oldSlug->package->$slugColumn, 301);
        }
        abort(404);
    }

    // ✅ إعداد البيانات المرتبة للـ Accordion (حسب units.sort_order)
    $groupedForAccordion = $package->packageUnitItems
        ->groupBy('unit_id')
        ->sortBy(function ($items) {
            return $items->first()->unit->sort_order ?? 9999;
        });

    // ✅ إعداد البيانات المرتبة لجدول الكميات (حسب package_unit_items.sort_order)
    $groupedForTable = $package->packageUnitItems
        ->groupBy('unit_id')
        ->sortBy(function ($items) {
            return $items->first()->sort_order ?? 9999;
        });

    // تحميل بيانات إضافية
    $seo = SeoSetting::where('page', 'category')->first();

    $testimonials = Testimonial::where('status', 'approved')
        ->where('package_id', $package->id)
        ->get();

    $faqs = Faq::where('page', 'category')
                ->orderBy('sort', 'asc')
                ->get();

    $unitTypes = PackageUnitItem::with('unit:id,type,name_ar,name_en')
        ->where('package_id', $package->id)
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

    return view('frontend.categories.show', compact(
        'seo', 'package', 'testimonials', 'faqs', 'unitTypes',
        'groupedForAccordion', 'groupedForTable'
    ));
}



public function filter(Request $request)
{
    $locale = app()->getLocale();
    $answers = $request->answers ?? [];

    $query = Package::query();

    // أولاً فلترة حسب اسم الباكج
    if (!empty($answers[5])) {
        $packageName = $answers[5];
        $query->where($locale === 'ar' ? 'name_ar' : 'name_en', $packageName);
    }

    // فلترة بالسعر
    if (!empty($request->price_min) && !empty($request->price_max)) {
        $query->whereBetween('price', [$request->price_min, $request->price_max]);
    }

    // جلب النتائج المؤقتة
    $packages = $query->get();

    // فلترة حسب اللون على مستوى الـ collection
    if (!empty($answers[6])) {
        $selectedColor = $answers[6];
        $packages = $packages->filter(function($package) use ($selectedColor, $locale) {
            return collect($package->available_colors)
                ->pluck('name_' . $locale)
                ->contains($selectedColor);
        });
    }

    // لو أول تحميل للصفحة
    if (empty($request->all())) {
        $packages = $query->where('show_in_home', 1)->take(4)->get();
    }

    return view('frontend.categories._section', compact('packages'));
}

public function testimonialsstore(Request $request)
{
    $request->validate([
        'package_id' => 'required|exists:packages,id',
        'name'       => 'required|string',
        'message'    => 'required|string',
        'rating'     => 'required|integer|min:1|max:5',
    ]);

    Testimonial::create([
        'package_id' => $request->package_id,
        'name'       => $request->name,
        'message'    => $request->message,
        'rating'     => $request->rating,
        'location'   => auth()->user()->country->name ?? '-------',
        'image'      => auth()->user()->image ?? null,
        'status'     => 'pending',
    ]);

    return back()->with('success', 'تم إرسال تقييمك وسيظهر بعد الموافقة عليه');
}




}
