<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackageImage;
use App\Models\Unit;
use App\Models\Item;
use Illuminate\Http\Request;
use App\Models\SeoSetting;
use App\Models\PageContent;
use App\Models\PackageUnitItem;
use Illuminate\Support\Facades\Storage;

class PackageController extends Controller
{
    public function index()
    {
        $page = 'category';
        $seoSettings = SeoSetting::all()->keyBy('page');
        $content = PageContent::where('page', 'package')->first();


        $packages = Package::with(['images', 'packageUnitItems.unit', 'packageUnitItems.item'])->get();
        return view('admin.packages.index', compact('packages','page','seoSettings','content'));
    }

    public function show(Package $package)
    {
        $package->load(['images', 'units.designs', 'units.items']);
        return view('admin.packages.show', compact('package'));
    }

    public function create()
    {
        //$units = Unit::whereNull('package_id')->get();
        $units = Unit::get();
        //$items = Item::all(); // كل القطع
        return view('admin.packages.create', compact('units'));
    }


public function store(Request $request)
{
    $validated = $request->validate([
        'name_ar' => 'required|string|max:255',
        'name_en' => 'required|string|max:255',
        'price'   => 'required|numeric',
        'description_ar' => 'nullable|string',
        'description_en' => 'nullable|string',
        'period_ar'=> 'nullable|string',
        'period_en'=> 'nullable|string',
        'service_includes_ar'=> 'nullable|string',
        'service_includes_en'=> 'nullable|string',
        'payment_plan_ar'=> 'nullable|string',
        'payment_plan_en'=> 'nullable|string',
        'decoration_ar'=> 'nullable|string',
        'decoration_en'=> 'nullable|string',
        'sort_order'=> 'nullable|integer',
        'units'   => 'nullable|array',
        'title_ar'  => 'nullable|string',
        'title_en'  => 'nullable|string',
        'units.*.unit_id' => 'required|integer|exists:units,id',
        'units.*.items' => 'nullable|array',
        'units.*.items.*.item_id' => 'required|integer|exists:items,id',
        'available_colors' => 'nullable|array',
        'available_colors.*.name_ar' => 'required_with:available_colors|string',
        'available_colors.*.name_en' => 'required_with:available_colors|string',
        'available_colors.*.color_code' => 'required_with:available_colors|string',
    ]);

    $package = Package::create($request->only([
        'name_ar', 'name_en', 'price', 'description_ar', 'description_en',
        'period_ar', 'period_en', 'service_includes_ar', 'service_includes_en',
        'payment_plan_ar', 'payment_plan_en', 'decoration_ar', 'decoration_en',
        'sort_order', 'meta_title_en', 'meta_title_ar', 'meta_description_en',
        'meta_description_ar', 'slug_en', 'slug_ar','title_ar','title_en'
    ]) + [
        'available_colors' => $validated['available_colors'] ?? [],
    ]);

     // ✅ حفظ الصورة الرئيسية
     if ($request->hasFile('image')) {
        $package->update([
            'image' => $request->file('image')->store('packages', 'public'),
        ]);
    }

    // أضف الوحدات الجديدة
    if (!empty($validated['units'])) {
        foreach ($validated['units'] as $unitData) {
            $unitId = $unitData['unit_id'];

            if (!empty($unitData['items'])) {
                foreach ($unitData['items'] as $itemData) {
                    PackageUnitItem::create([
                        'package_id' => $package->id,
                        'unit_id'    => $unitId,
                        'item_id'    => $itemData['item_id'],
                        'sort_order' => $itemData['sort_order'] ?? 0,
                    ]);
                }
            }
        }

        Unit::where('id', $unitId)->update([
            'sort_order' => $unitData['sort_order'] ?? 0
        ]);

    }

    return redirect()->route('admin.packages.index')->with('success', 'تم إنشاء الباكج بنجاح');
}


    // تحميل العلاقات الخاصة بالباكج مع الوحدات والقطع والصور
    public function edit(Package $package)
{
    $package->load([
        'packageUnitItems.unit.images',
        'packageUnitItems.item'
    ]);


    // كل الوحدات المتاحة اللي ممكن إضافتها للباكج (مش موجودة مسبقاً)
    //$units = Unit::whereNull('package_id')->get();
    //$units = Unit::all();
 // الوحدات المرتبطة بالباكج حالياً
 $units = $package->units()->get(); // جلب الوحدات المرتبطة بالباكج فقط
    return view('admin.packages.edit', compact('package', 'units'));
}



public function update(Request $request, Package $package)
{
    // ✅ إضافة قاعدة التحقق لـ unit_id و unit_sort_order و items بشكل صريح
    $validated = $request->validate([
        'name_ar' => 'required|string|max:255',
        'name_en' => 'required|string|max:255',
        'price'   => 'required|numeric',
        'description_ar' => 'nullable|string',
        'description_en' => 'nullable|string',
        'period_ar'=> 'nullable|string',
        'period_en'=> 'nullable|string',
        'sort_order'=> 'nullable|integer', // ✅ تم تغييره من string إلى integer
        'service_includes_ar'=> 'nullable|string',
        'service_includes_en'=> 'nullable|string',
        'payment_plan_ar'=> 'nullable|string',
        'payment_plan_en'=> 'nullable|string',
        'decoration_ar'=> 'nullable|string',
        'decoration_en'=> 'nullable|string',
        'meta_title_en'=> 'nullable|string',
        'meta_title_ar'=> 'nullable|string',
        'title_ar'  => 'nullable|string',
        'title_en'  => 'nullable|string',
        'meta_description_en'=> 'nullable|string',
        'meta_description_ar'=> 'nullable|string',
        'slug_en'=> 'nullable|string',
        'slug_ar'=> 'nullable|string',
        'units'   => 'nullable|array',
        'units.*.unit_id' => 'required_with:units|exists:units,id', // ✅ إضافة
        'units.*.sort_order' => 'nullable|integer',
        'units.*.unit_sort_order' => 'nullable|integer', // ✅ إضافة
        'units.*.items' => 'nullable|array',
        'units.*.items.*.item_id' => 'required_with:units.*.items|exists:items,id', // ✅ إضافة
        'units.*.items.*.sort_order' => 'nullable|integer',
        'available_colors' => 'nullable|array',
        'available_colors.*.name_ar' => 'required_with:available_colors|string',
        'available_colors.*.name_en' => 'required_with:available_colors|string',
        'available_colors.*.color_code' => 'required_with:available_colors|string',
    ]);


    $package->update($request->only([
        'name_ar', 'name_en', 'price', 'description_ar', 'description_en',
        'period_ar', 'period_en', 'service_includes_ar', 'service_includes_en',
        'payment_plan_ar', 'payment_plan_en', 'decoration_ar', 'decoration_en',
        'sort_order', 'meta_title_en', 'meta_title_ar', 'meta_description_en',
        'meta_description_ar', 'slug_en', 'slug_ar','title_ar','title_en'
    ]) + [
        'available_colors' => $validated['available_colors'] ?? [],
    ]);


    // ✅ تحديث الصورة الرئيسية
    if ($request->hasFile('image')) {
        if ($package->image && \Storage::disk('public')->exists($package->image)) {
            \Storage::disk('public')->delete($package->image);
        }
        $package->update([
            'image' => $request->file('image')->store('packages', 'public'),
        ]);
    }


    // احذف الوحدات القديمة
    $package->packageUnitItems()->delete();

    // أضف الوحدات الجديدة
    if (!empty($validated['units'])) {
        foreach ($validated['units'] as $unitData) {
            $unitId = $unitData['unit_id'];

            // ✅ 1. تحديث sort_order في جدول units (لعرض جدول الكميات)
            $unitSortOrder = $unitData['unit_sort_order'] ?? 0;
            Unit::where('id', $unitId)->update([
                'sort_order' => $unitSortOrder
            ]);

            // ✅ 2. ترتيب الوحدة داخل هذا الباكج (يُستخدم لترتيب الوحدات عند عرض الباكج)
            $packageUnitSortOrder = $unitData['sort_order'] ?? 0;

            if (!empty($unitData['items'])) {
                foreach ($unitData['items'] as $itemData) {
                    PackageUnitItem::create([
                        'package_id' => $package->id,
                        'unit_id'    => $unitId,
                        'item_id'    => $itemData['item_id'],
                        // ✅ هنا نستخدم ترتيب الوحدة في الباكج (ليس ترتيب العنصر داخل الوحدة)
                        'sort_order' => $packageUnitSortOrder,
                    ]);
                }
            }
        }
    }
    return redirect()->route('admin.packages.index')
        ->with('success', 'تم تحديث الباكج بنجاح');
}







    public function destroy(Package $package)
    {
        // حذف الصور من التخزين
        foreach ($package->images as $image) {
            if ($image->image_path && \Storage::disk('public')->exists($image->image_path)) {
                \Storage::disk('public')->delete($image->image_path);
            }
        }

        // حذف الصور من قاعدة البيانات
        $package->images()->delete();

        // حذف الوحدات (ولو عايز مايحذفهاش ممكن تشيل السطر ده)
        $package->units()->delete();

        // حذف الباكدج نفسه
        $package->delete();

        return redirect()->route('admin.packages.index')->with('success', 'تم حذف الباكج بنجاح.');
    }


    public function designsFromUnits(Package $package)
    {
        $designs = \App\Models\Design::where('package_id', $package->id)
            ->select('id','name_ar','name_en')
            ->get();

        return response()->json($designs);
    }




    public function deleteImage(Package $package, PackageImage $image)
    {
        // نتأكد أن الصورة تنتمي للبـاكدج
        if ($image->package_id != $package->id) {
            return redirect()->back()->with('error', 'الصورة لا تنتمي لهذا الباكدج');
        }

        // حذف من التخزين
        if ($image->image_path && \Storage::disk('public')->exists($image->image_path)) {
            \Storage::disk('public')->delete($image->image_path);
        }

        // حذف من قاعدة البيانات
        $image->delete();

        return redirect()->back()->with('success', 'تم حذف الصورة بنجاح');
    }

    public function updatepackage(Request $request)
    {
        $request->validate([
            'title_ar' => 'required',
            'title_en' => 'required',
            'text_ar'  => 'required',
            'text_en'  => 'required',
        ]);

        $content = PageContent::where('page', 'package')->first();
        $content->update($request->all());

        return redirect()->back()->with('success', 'تم تحديث بيانات صفحة الباكدج بنجاح');
    }


public function toggleHome(Request $request, Package $package)
{
    $package->show_in_home = $request->show_in_home;
    $package->save();

    return response()->json(['success' => true]);
}


}
