<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackageImage;
use App\Models\Unit;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::with(['images', 'units.items', 'units.designs'])->get();
        return view('admin.packages.index', compact('packages'));
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
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'number_of_units' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $package = Package::create([
            'name' => [
                'ar' => $request->name_ar,
                'en' => $request->name_en,
            ],
            'price' => $request->price,
            'description' => [
                'ar' => $request->description_ar,
                'en' => $request->description_en,
            ],
            'number_of_units' => $request->number_of_units,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('packages', 'public');
                PackageImage::create([
                    'package_id' => $package->id,
                    'image_path' => $path,
                    'is_primary' => $index == 0,
                    'sort_order' => $index + 1,
                ]);
            }
        }

        // حفظ الوحدات
        if ($request->has('units')) {
            foreach ($request->units as $unitData) {
                $unit = Unit::create([
                    'package_id' => $package->id,
                    'name' => $unitData['name'],
                    'type' => $unitData['type'],
                    'description' => $unitData['description'] ?? null,
                ]);

                // ربط التصاميم
                if (isset($unitData['design_ids']) && is_array($unitData['design_ids'])) {
                    $unit->designs()->attach($unitData['design_ids']);
                }
            }
        }

        return redirect()->route('admin.packages.index')->with('success', 'تم إنشاء الباكج بنجاح.');
    }

    public function edit(Package $package)
    {
        $package->load(['images', 'units.designs', 'units.items']);
        return view('admin.packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package)
    {
        // التحقق من البيانات
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'price'   => 'required|numeric',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'units'   => 'nullable|array',
            'units.*.name_ar' => 'required|string|max:255',
            'units.*.name_en' => 'required|string|max:255',
            'units.*.type' => 'required|string|in:bedroom,living_room,kitchen,bathroom',
            'units.*.description_ar' => 'nullable|string',
            'units.*.description_en' => 'nullable|string',
        ]);

        // تحديث بيانات الباكج
        $package->update([
            'name_ar'        => $validated['name_ar'],
            'name_en'        => $validated['name_en'],
            'price'          => $validated['price'],
            'description_ar' => $validated['description_ar'] ?? null,
            'description_en' => $validated['description_en'] ?? null,
        ]);

        // تحديث الصورة
        if ($request->hasFile('image')) {
            if ($package->image && \Storage::disk('public')->exists($package->image)) {
                \Storage::disk('public')->delete($package->image);
            }

            $path = $request->file('image')->store('packages', 'public');
            $package->update([
                'image' => $path,
            ]);
        }

        $unitsData = $validated['units'] ?? [];
        $existingUnitIds = [];

        foreach ($unitsData as $unit) {
            $existingItemIds = []; // 👈 لازم تبدأ فاضية لكل وحدة

            if (!empty($unit['id'])) {
                $packageUnit = Unit::find($unit['id']);
                if ($packageUnit) {
                    $packageUnit->update([
                        'name_ar'        => $unit['name_ar'],
                        'name_en'        => $unit['name_en'],
                        'type'           => $unit['type'],
                        'description_ar' => $unit['description_ar'] ?? null,
                        'description_en' => $unit['description_en'] ?? null,
                    ]);

                    $existingUnitIds[] = $packageUnit->id;

                    // ✅ تحديث أو إضافة items الخاصة بالوحدة
                    if (!empty($unit['items'])) {
                        foreach ($unit['items'] as $itemData) {
                            if (!empty($itemData['id'])) {
                                $item = Item::find($itemData['id']);
                                if ($item) {
                                    $item->update([
                                        'item_name_ar' => $itemData['item_name_ar'],
                                        'item_name_en' => $itemData['item_name_en'],
                                        'quantity'     => $itemData['quantity'],
                                        'dimensions'   => $itemData['dimensions'] ?? null,
                                        'material'     => $itemData['material'] ?? null,
                                        'color'        => $itemData['color'] ?? null,
                                    ]);
                                    $existingItemIds[] = $item->id;
                                }
                            } else {
                                $newItem = $packageUnit->items()->create([
                                    'item_name_ar' => $itemData['item_name_ar'],
                                    'item_name_en' => $itemData['item_name_en'],
                                    'quantity'     => $itemData['quantity'],
                                    'dimensions'   => $itemData['dimensions'] ?? null,
                                    'material'     => $itemData['material'] ?? null,
                                    'color'        => $itemData['color'] ?? null,
                                ]);
                                $existingItemIds[] = $newItem->id;
                            }
                        }
                    }

                    // ✅ حذف العناصر الغير موجودة في الفورم
                    //$packageUnit->items()->whereNotIn('id', $existingItemIds)->delete();
                }
            } else {
                // وحدة جديدة
                $newUnit = $package->units()->create([
                    'name_ar'        => $unit['name_ar'],
                    'name_en'        => $unit['name_en'],
                    'type'           => $unit['type'],
                    'description_ar' => $unit['description_ar'] ?? null,
                    'description_en' => $unit['description_en'] ?? null,
                ]);

                $existingUnitIds[] = $newUnit->id;

                // لو فيه عناصر جديدة
                if (!empty($unit['items'])) {
                    foreach ($unit['items'] as $itemData) {
                        $newUnit->items()->create([
                            'item_name_ar' => $itemData['item_name_ar'],
                            'item_name_en' => $itemData['item_name_en'],
                            'quantity'     => $itemData['quantity'],
                            'dimensions'   => $itemData['dimensions'] ?? null,
                            'material'     => $itemData['material'] ?? null,
                            'color'        => $itemData['color'] ?? null,
                        ]);
                    }
                }
            }
        }

        // ✅ حذف الوحدات الغير موجودة
        $package->units()->whereNotIn('id', $existingUnitIds)->delete();

        return redirect()->route('admin.packages.index')->with('success', 'تم تحديث الباكج بنجاح');
    }



    public function destroy(Package $package)
    {
        $package->images()->delete();
        $package->units()->delete(); // ⚠️ انتبه: هذا سيحذف كل الوحدات والتصاميم والقطع المرتبطة!
        $package->delete();
        return redirect()->route('admin.packages.index')->with('success', 'تم حذف الباكج بنجاح.');
    }

    public function destroyImage($imageId)
    {
        $image = PackageImage::findOrFail($imageId);
        \Storage::disk('public')->delete($image->image_path);
        $image->delete();
        return back()->with('success', 'تم حذف الصورة بنجاح.');
    }
}
