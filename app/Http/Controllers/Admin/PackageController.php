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

use Illuminate\Support\Facades\Storage;

class PackageController extends Controller
{
    public function index()
    {
        $page = 'category';
        $seoSettings = SeoSetting::all()->keyBy('page');
        $content = PageContent::where('page', 'package')->first();


        $packages = Package::with(['images', 'units.items', 'units.designs'])->get();
        return view('admin.packages.index', compact('packages','page','seoSettings','content'));
    }

    public function show(Package $package)
    {
        $package->load(['images', 'units.designs', 'units.items']);
        return view('admin.packages.show', compact('package'));
    }

    public function create()
    {
        return view('admin.packages.create');
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

        // ✅ حفظ صور إضافية
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $package->images()->create([
                    'image_path' => $img->store('packages', 'public'),
                ]);
            }
        }

        // ✅ حفظ الوحدات والعناصر
        if (!empty($validated['units'])) {
            foreach ($validated['units'] as $unitData) {
                $unit = $package->units()->create([
                    'name_ar' => $unitData['name_ar'],
                    'name_en' => $unitData['name_en'],
                    'type'    => $unitData['type'],
                ]);

                if (!empty($unitData['items'])) {
                    foreach ($unitData['items'] as $itemData) {
                        $item = $unit->items()->create([
                            'item_name_ar' => $itemData['item_name_ar'] ?? null,
                            'item_name_en' => $itemData['item_name_en'] ?? null,
                            'quantity'     => $itemData['quantity'] ?? null,
                            'dimensions'   => $itemData['dimensions'] ?? null,
                            'material_ar'  => $itemData['material_ar'] ?? null,
                            'material_en'  => $itemData['material_en'] ?? null,
                            'color_ar'     => $itemData['color_ar'] ?? null,
                            'color_en'     => $itemData['color_en'] ?? null,
                            'background_color' => $itemData['background_color'] ?? null,
                        ]);

                        if (isset($itemData['image'])) {
                            $item->update([
                                'image_path' => $itemData['image']->store('items', 'public'),
                            ]);
                        }
                    }
                }
            }
        }

        return redirect()->route('admin.packages.index')->with('success', 'تم إنشاء الباكج بنجاح');
    }


    public function edit(Package $package)
    {
        $package->load(['images', 'units.designs', 'units.items']);
        return view('admin.packages.edit', compact('package'));
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
        'units'   => 'nullable|array',
        'units.*.id' => 'nullable|integer|exists:units,id',
        'units.*.name_ar' => 'required|string|max:255',
        'units.*.name_en' => 'required|string|max:255',
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

    // ✅ إضافة صور جديدة
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $file) {
            $path = $file->store('packages/gallery', 'public');
            $package->images()->create(['image_path' => $path]);
        }
    }

    // ✅ Sync للوحدات + العناصر
    $unitsData = $validated['units'] ?? [];
    $existingUnitIds = [];

    foreach ($unitsData as $unit) {
        if (!empty($unit['id'])) {
            // ✏️ تحديث وحدة موجودة
            $packageUnit = Unit::find($unit['id']);
            if ($packageUnit) {
                $packageUnit->update([
                    'name_ar' => $unit['name_ar'],
                    'name_en' => $unit['name_en'],
                    'type'    => $unit['type'],
                    'description_ar' => $unit['description_ar'] ?? null,
                    'description_en' => $unit['description_en'] ?? null,
                ]);
                $existingUnitIds[] = $packageUnit->id;

                // ✏️ تحديث العناصر
                $existingItemIds = [];
                if (!empty($unit['items'])) {
                    foreach ($unit['items'] as $itemData) {
                        if (!empty($itemData['id'])) {
                            // عنصر موجود → تحديث
                            $item = Item::find($itemData['id']);
                            if ($item) {
                                $updateData = [
                                    'item_name_ar' => $itemData['item_name_ar'] ?? $item->item_name_ar,
                                    'item_name_en' => $itemData['item_name_en'] ?? $item->item_name_en,
                                    'quantity'     => $itemData['quantity'] ?? $item->quantity,
                                    'dimensions'   => $itemData['dimensions'] ?? $item->dimensions,
                                    'material_ar'     => $itemData['material_ar'] ?? $item->material_ar,
                                    'material_en'     => $itemData['material_en'] ?? $item->material_en,
                                    'color_ar'        => $itemData['color_ar'] ?? $item->color_ar,
                                    'color_en'        => $itemData['color_en'] ?? $item->color_en,
                                    'background_color' => $itemData['background_color'] ?? $item->background_color,
                                    'design_id'    => $unit['design_id'] ?? $item->design_id,
                                ];

                                // صورة جديدة؟
                                if (isset($itemData['image']) && $itemData['image'] instanceof \Illuminate\Http\UploadedFile) {
                                    if ($item->image_path && \Storage::disk('public')->exists($item->image_path)) {
                                        \Storage::disk('public')->delete($item->image_path);
                                    }
                                    $updateData['image_path'] = $itemData['image']->store('items', 'public');
                                }

                                $item->update($updateData);
                                $existingItemIds[] = $item->id;
                            }
                        } else {
                            // عنصر جديد
                            $defaultDesignId = \App\Models\UnitDesign::where('unit_id', $packageUnit->id)
                                ->where('is_default', 1)
                                ->value('design_id');

                            $createData = [
                                'item_name_ar' => $itemData['item_name_ar'] ?? null,
                                'item_name_en' => $itemData['item_name_en'] ?? null,
                                'quantity'     => $itemData['quantity'] ?? 0,
                                'dimensions'   => $itemData['dimensions'] ?? null,
                                'material_ar'     => $itemData['material_ar'] ?? $item->material_ar,
                                'material_en'     => $itemData['material_en'] ?? $item->material_en,
                                'color_ar'        => $itemData['color_ar'] ?? $item->color_ar,
                                'color_en'        => $itemData['color_en'] ?? $item->color_en,
                                'background_color' => $itemData['background_color'] ?? $item->background_color,
                                'design_id'    => $defaultDesignId,
                            ];

                            if (isset($itemData['image']) && $itemData['image'] instanceof \Illuminate\Http\UploadedFile) {
                                $createData['image_path'] = $itemData['image']->store('items', 'public');
                            }

                            $newItem = $packageUnit->items()->create($createData);
                            $existingItemIds[] = $newItem->id;
                        }
                    }
                }

                // ❌ حذف العناصر اللي مش راجعة من الفورم
                $packageUnit->items()->whereNotIn('id', $existingItemIds)->delete();
            }
        } else {
            // ➕ وحدة جديدة
            $newUnit = $package->units()->create([
                'name_ar' => $unit['name_ar'],
                'name_en' => $unit['name_en'],
                'type'    => $unit['type'],
                'description_ar' => $unit['description_ar'] ?? null,
                'description_en' => $unit['description_en'] ?? null,
            ]);
            $existingUnitIds[] = $newUnit->id;

            $defaultDesignId = \App\Models\UnitDesign::where('unit_id', $newUnit->id)
                ->where('is_default', 1)
                ->value('design_id');

            if (!empty($unit['items'])) {
                foreach ($unit['items'] as $itemData) {
                    $createData = [
                        'item_name_ar' => $itemData['item_name_ar'] ?? null,
                        'item_name_en' => $itemData['item_name_en'] ?? null,
                        'quantity'     => $itemData['quantity'] ?? 0,
                        'dimensions'   => $itemData['dimensions'] ?? null,
                        'material_ar'     => $itemData['material_ar'] ?? $item->material_ar,
                        'material_en'     => $itemData['material_en'] ?? $item->material_en,
                        'color_ar'        => $itemData['color_ar'] ?? $item->color_ar,
                        'color_en'        => $itemData['color_en'] ?? $item->color_en,
                        'background_color' => $itemData['background_color'] ?? $item->background_color,
                        'design_id'    => $defaultDesignId,
                    ];

                    if (isset($itemData['image']) && $itemData['image'] instanceof \Illuminate\Http\UploadedFile) {
                        $createData['image_path'] = $itemData['image']->store('items', 'public');
                    }

                    $newUnit->items()->create($createData);
                }
            }
        }
    }

    // ❌ حذف الوحدات اللي مش راجعة من الفورم
    $package->units()->whereNotIn('id', $existingUnitIds)->delete();

    return redirect()->route('admin.packages.index')->with('success', 'تم تحديث الباكج بنجاح');
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


}
