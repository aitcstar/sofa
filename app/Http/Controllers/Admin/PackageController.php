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
        $units = Unit::whereNull('package_id')->get();
        //$items = Item::all(); // كل القطع
        return view('admin.packages.create', compact('units'));
    }
/*
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
            'sort_order'=> 'nullable|string',
            'service_includes_ar'=> 'nullable|string',
            'service_includes_en'=> 'nullable|string',
            'payment_plan_ar'=> 'nullable|string',
            'payment_plan_en'=> 'nullable|string',
            'decoration_ar'=> 'nullable|string',
            'decoration_en'=> 'nullable|string',
            'units'   => 'nullable|array',
            'units.*.name_ar' => 'required|string|max:255',
            'units.*.name_en' => 'required|string|max:255',
            'units.*.images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'units.*.type' => 'required|string|in:bedroom,living_room,kitchen,bathroom,external',
            'units.*.items' => 'nullable|array',
            'units.*.items.*.item_name_ar' => 'nullable|string',
            'units.*.items.*.item_name_en' => 'nullable|string',
            'units.*.items.*.quantity'     => 'nullable|integer',
            'units.*.items.*.dimensions'   => 'nullable|string',
            'units.*.items.*.material_ar'     => 'nullable|string',
            'units.*.items.*.material_en'     => 'nullable|string',
            'units.*.items.*.color_ar'        => 'nullable|string',
            'units.*.items.*.color_en'        => 'nullable|string',
            'units.*.items.*.background_color'    => 'nullable|string',
            'units.*.items.*.image'        => 'nullable|image',
            'image'  => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp',
        ]);

        // ✅ إنشاء الباكج
        $package = Package::create([
            'name_ar' => $validated['name_ar'],
            'name_en' => $validated['name_en'],
            'price'   => $validated['price'],
            'description_ar' => $validated['description_ar'] ?? null,
            'description_en' => $validated['description_en'] ?? null,
            'period_ar' => $validated['period_ar'] ?? null,
            'period_en' => $validated['period_en'] ?? null,
            'service_includes_ar' => $validated['service_includes_ar'] ?? null,
            'service_includes_en' => $validated['service_includes_en'] ?? null,
            'payment_plan_ar' => $validated['payment_plan_ar'] ?? null,
            'payment_plan_en' => $validated['payment_plan_en'] ?? null,
            'decoration_ar' => $validated['decoration_ar'] ?? null,
            'decoration_en' => $validated['decoration_en'] ?? null,
            'sort_order' => $validated['sort_order'] ?? null,
        ]);

        // ✅ حفظ الصورة الرئيسية
        if ($request->hasFile('image')) {
            $package->update([
                'image' => $request->file('image')->store('packages', 'public'),
            ]);
        }

       // ربط الوحدات والقطع
        foreach ($request->units as $unitInput) {
            // جلب الوحدة الأصلية
            $originalUnit = Unit::with('images')->findOrFail($unitInput['unit_id']);

            // إنشاء نسخة جديدة من الوحدة مرتبطة بالباكج
            $newUnit = $package->units()->create([
                'name_ar' => $originalUnit->name_ar,
                'name_en' => $originalUnit->name_en,
                'type' => $originalUnit->type,
                'description_ar' => $originalUnit->description_ar,
                'description_en' => $originalUnit->description_en,
            ]);

            // نسخ صور الوحدة
            foreach ($originalUnit->images as $image) {
                $newUnit->images()->create([
                    'image_path' => $image->image_path,
                    'alt_text' => $image->alt_text,
                    'sort_order' => $image->sort_order,
                    'is_primary' => $image->is_primary,
                ]);
            }

            // معالجة القطع
            if (!empty($unitInput['items'])) {
                foreach ($unitInput['items'] as $itemInput) {
                    $originalItem = Item::findOrFail($itemInput['item_id']);

                    $newUnit->items()->create([
                        'package_id' => $package->id,
                        'item_name_ar' => $originalItem->item_name_ar,
                        'item_name_en' => $originalItem->item_name_en,
                        'quantity' => $itemInput['quantity'] ?? $originalItem->quantity,
                        'dimensions' => $itemInput['dimensions'] ?? $originalItem->dimensions,
                        'material_ar' => $originalItem->material_ar,
                        'material_en' => $originalItem->material_en,
                        'color_ar' => $originalItem->color_ar,
                        'color_en' => $originalItem->color_en,
                        'background_color' => $originalItem->background_color,
                        'image_path' => $originalItem->image_path, // ← هنا يتم نسخ مسار الصورة
                    ]);
                }
            }
        }


        return redirect()->route('admin.packages.index')->with('success', 'تم إنشاء الباكج بنجاح');
    }
*/

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
        'units.*.unit_id' => 'required|integer|exists:units,id',
        'units.*.items' => 'nullable|array',
        'units.*.items.*.item_id' => 'required|integer|exists:items,id',
    ]);

    $package = Package::create($request->only([
        'name_ar' ,
        'name_en',
        'price'  ,
        'description_ar' ,
        'description_en',
        'period_ar' ,
        'period_en' ,
        'service_includes_ar',
        'service_includes_en' ,
        'payment_plan_ar',
        'payment_plan_en' ,
        'decoration_ar',
        'decoration_en' ,
        'sort_order',
        'meta_title_en',
        'meta_title_ar',
        'meta_description_en',
        'meta_description_ar',
        'slug_en',
        'slug_ar'
    ]));

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
                    ]);
                }
            }
        }
    }

    return redirect()->route('admin.packages.index')->with('success', 'تم إنشاء الباكج بنجاح');
}


    /*public function edit(Package $package)
    {
        $package->load(['units.images', 'units.items']);

        // جلب الوحدات العامة (غير المرتبطة بأي باكج) + الوحدات الخاصة بهذا الباكج
        $units = Unit::where(function($q) use ($package) {
            $q->whereNull('package_id') // وحدات عامة
              ->orWhere('package_id', $package->id); // وحدات هذا الباكج
        })->get();
        Unit::whereNull('package_id')->get();
        return view('admin.packages.edit', compact('package', 'units'));
    }*/

    // تحميل العلاقات الخاصة بالباكج مع الوحدات والقطع والصور
    public function edit(Package $package)
{
    $package->load([
        'packageUnitItems.unit.images',
        'packageUnitItems.item'
    ]);


    // كل الوحدات المتاحة اللي ممكن إضافتها للباكج (مش موجودة مسبقاً)
    $units = Unit::whereNull('package_id')->get();

    return view('admin.packages.edit', compact('package', 'units'));
}



public function update(Request $request, Package $package)
{
    $validated = $request->validate([
        'name_ar' => 'required|string|max:255',
        'name_en' => 'required|string|max:255',
        'price'   => 'required|numeric',
        'description_ar' => 'nullable|string',
        'description_en' => 'nullable|string',
        'period_ar'=> 'nullable|string',
        'period_en'=> 'nullable|string',
        'sort_order'=> 'nullable|string',
        'service_includes_ar'=> 'nullable|string',
        'service_includes_en'=> 'nullable|string',
        'payment_plan_ar'=> 'nullable|string',
        'payment_plan_en'=> 'nullable|string',
        'decoration_ar'=> 'nullable|string',
        'decoration_en'=> 'nullable|string',
        'meta_title_en'=> 'nullable|string',
        'meta_title_ar'=> 'nullable|string',
        'meta_description_en'=> 'nullable|string',
        'meta_description_ar'=> 'nullable|string',
        'slug_en'=> 'nullable|string',
        'slug_ar'=> 'nullable|string',
        'units'   => 'nullable|array',

    ]);


    $package->update([
        'name_ar' => $validated['name_ar'],
        'name_en' => $validated['name_en'],
        'price'   => $validated['price'],
        'description_ar' => $validated['description_ar'] ?? null,
        'description_en' => $validated['description_en'] ?? null,
        'period_ar' => $validated['period_ar'] ?? null,
        'period_en' => $validated['period_en'] ?? null,
        'service_includes_ar' => $validated['service_includes_ar'] ?? null,
        'service_includes_en' => $validated['service_includes_en'] ?? null,
        'payment_plan_ar' => $validated['payment_plan_ar'] ?? null,
        'payment_plan_en' => $validated['payment_plan_en'] ?? null,
        'decoration_ar' => $validated['decoration_ar'] ?? null,
        'decoration_en' => $validated['decoration_en'] ?? null,
        'sort_order' => $validated['sort_order'] ?? null,
        'meta_title_en' => $validated['meta_title_en'] ?? null,
        'meta_title_ar' => $validated['meta_title_ar'] ?? null,
        'meta_description_en' => $validated['meta_description_en'] ?? null,
        'meta_description_ar' => $validated['meta_description_ar'] ?? null,
        'slug_en' => $validated['slug_en'] ?? null,
        'slug_ar' => $validated['slug_ar'] ?? null,
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

            if (!empty($unitData['items'])) {
                foreach ($unitData['items'] as $itemData) {
                    PackageUnitItem::create([
                        'package_id' => $package->id,
                        'unit_id'    => $unitId,
                        'item_id'    => $itemData['item_id'],
                    ]);
                }
            }
        }
    }

    return redirect()->route('admin.packages.index')
        ->with('success', 'تم تحديث الباكج بنجاح');
}


/*
    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'price'   => 'required|numeric',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'period_ar'=> 'nullable|string',
            'period_en'=> 'nullable|string',
            'sort_order'=> 'nullable|string',
            'service_includes_ar'=> 'nullable|string',
            'service_includes_en'=> 'nullable|string',
            'payment_plan_ar'=> 'nullable|string',
            'payment_plan_en'=> 'nullable|string',
            'decoration_ar'=> 'nullable|string',
            'decoration_en'=> 'nullable|string',
            'units'   => 'nullable|array',
            'units.*.id' => 'nullable|integer|exists:units,id',
            'units.*.name_ar' => 'required|string|max:255',
            'units.*.name_en' => 'required|string|max:255',
            'units.*.images' => 'nullable|array', // مصفوفة صور الوحدة
            'units.*.images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp',

            'units.*.type' => 'required|string|in:bedroom,living_room,kitchen,bathroom,external',
            'units.*.items' => 'nullable|array',
            'units.*.items.*.id' => 'nullable|integer|exists:items,id',
            'units.*.items.*.item_name_ar' => 'nullable|string',
            'units.*.items.*.item_name_en' => 'nullable|string',
            'units.*.items.*.quantity'     => 'nullable|integer',
            'units.*.items.*.dimensions'   => 'nullable|string',
            'units.*.items.*.material_ar'     => 'nullable|string',
            'units.*.items.*.material_en'     => 'nullable|string',
            'units.*.items.*.color_ar'        => 'nullable|string',
            'units.*.items.*.color_en'        => 'nullable|string',
            'units.*.items.*.background_color'    => 'nullable|string',
            'units.*.items.*.image'        => 'nullable|image',
            'image'  => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp',
        ]);

        // ✅ تحديث بيانات الباكج
        $package->update([
            'name_ar' => $validated['name_ar'],
            'name_en' => $validated['name_en'],
            'price'   => $validated['price'],
            'description_ar' => $validated['description_ar'] ?? null,
            'description_en' => $validated['description_en'] ?? null,
            'period_ar' => $validated['period_ar'] ?? null,
            'period_en' => $validated['period_en'] ?? null,
            'service_includes_ar' => $validated['service_includes_ar'] ?? null,
            'service_includes_en' => $validated['service_includes_en'] ?? null,
            'payment_plan_ar' => $validated['payment_plan_ar'] ?? null,
            'payment_plan_en' => $validated['payment_plan_en'] ?? null,
            'decoration_ar' => $validated['decoration_ar'] ?? null,
            'decoration_en' => $validated['decoration_en'] ?? null,
            'sort_order' => $validated['sort_order'] ?? null,
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


            // ✅ ربط الوحدات والقطع
            $unitsData = $request->units ?? [];
            $existingUnitIds = [];

            foreach ($unitsData as $unitData) {
                $unitId = $unitData['unit_id'] ?? null;
                if (!$unitId) continue;

                // جلب الوحدة الأصلية
                $originalUnit = Unit::with('images')->findOrFail($unitId);

                // إذا كانت الوحدة مرتبطة بباكج آخر (غير هذا)، أنشئ نسخة جديدة
                if ($originalUnit->package_id && $originalUnit->package_id != $package->id) {
                    $newUnit = $package->units()->create([
                        'name_ar' => $originalUnit->name_ar,
                        'name_en' => $originalUnit->name_en,
                        'type' => $originalUnit->type,
                        'description_ar' => $originalUnit->description_ar,
                        'description_en' => $originalUnit->description_en,
                    ]);

                    // نسخ الصور
                    foreach ($originalUnit->images as $img) {
                        $newUnit->images()->create([
                            'image_path' => $img->image_path,
                            'alt_text' => $img->alt_text,
                            'sort_order' => $img->sort_order,
                            'is_primary' => $img->is_primary,
                        ]);
                    }

                    $targetUnit = $newUnit;
                } else {
                    // ربط الوحدة الحالية بالباكج
                    $originalUnit->package_id = $package->id;
                    $originalUnit->save();
                    $targetUnit = $originalUnit;
                }

                $existingUnitIds[] = $targetUnit->id;

                // معالجة القطع
                if (!empty($unitData['items'])) {
                    foreach ($unitData['items'] as $itemData) {
                        $itemId = $itemData['item_id'] ?? null;
                        if (!$itemId) continue;

                        $originalItem = Item::findOrFail($itemId);

                        // إنشاء نسخة من القطعة
                        $targetUnit->items()->create([
                            'package_id' => $package->id,
                            'item_name_ar' => $originalItem->item_name_ar,
                            'item_name_en' => $originalItem->item_name_en,
                            'quantity' => $itemData['quantity'] ?? $originalItem->quantity,
                            'dimensions' => $itemData['dimensions'] ?? $originalItem->dimensions,
                            'material_ar' => $originalItem->material_ar,
                            'material_en' => $originalItem->material_en,
                            'color_ar' => $originalItem->color_ar,
                            'color_en' => $originalItem->color_en,
                            'background_color' => $originalItem->background_color,
                            'image_path' => $originalItem->image_path,
                        ]);
                    }
                }
            }

            // حذف الوحدات غير المختارة
            $package->units()->whereNotIn('id', $existingUnitIds)->delete();


        return redirect()->route('admin.packages.index')->with('success', 'تم تحديث الباكج بنجاح');
    }
*/





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
