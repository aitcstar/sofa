<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\UnitImage;
use App\Models\PackageUnitItem;
use App\Models\Design;
class UnitController extends Controller
{

    public function index()
    {

        $units = Unit::whereNull('package_id')->get();
        return view('admin.units.index', compact('units'));
    }

    public function create()
    {
        $designs = Design::all(); // كل التصميمات
        return view('admin.units.create', compact('designs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'package_id' => 'nullable|exists:packages,id', // الآن يمكن تركها فارغة
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'design_ids' => 'array',
        ]);

        $unit = Unit::create($request->all());

        if ($request->has('design_ids')) {
            $unit->designs()->sync($request->design_ids);
        }

        if($request->hasFile('images')) {
            foreach($request->file('images') as $file) {
                $path = $file->store('units', 'public');
                $unit->images()->create([
                    'image_path' => $path,
                    'alt_text' => $unit->name_en,
                ]);
            }
        }

        return redirect()->route('admin.units.index')->with('success', 'تم إنشاء الوحدة بنجاح');
    }


    public function edit(Unit $unit)
    {
        $designs = Design::all();
        $images = $unit->images()->orderBy('sort_order')->get();
        $selectedDesigns = $unit->designs->pluck('id')->toArray(); // <-- دي أهم سطر

        return view('admin.units.edit', compact('unit', 'images','selectedDesigns','designs'));
    }

    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'design_ids' => 'array',
        ]);

        $unit->update($request->all());

        if ($request->has('design_ids')) {
            $unit->designs()->sync($request->design_ids);
        }

        if($request->hasFile('images')) {
            foreach($request->file('images') as $file) {
                $path = $file->store('units', 'public');
                $unit->images()->create([
                    'image_path' => $path,
                    'alt_text' => $unit->name_en,
                ]);
            }
        }

        return redirect()->route('admin.units.index')->with('success', 'تم تحديث الوحدة بنجاح');
    }

    /*public function destroy(Unit $unit)
    {
        foreach($unit->images as $image) {
            \Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }

        $unit->delete();
        return redirect()->route('admin.units.index')->with('success', 'تم حذف الوحدة بنجاح');
    }*/

    public function destroy(Request $request, Unit $unit)
    {
        // تحقق أولاً إن كان هناك باكجات مرتبطة
        $hasPackages = PackageUnitItem::where('unit_id', $unit->id)->exists();

        // لو فيه باكجات و المستخدم لم يؤكد الحذف بعد
        if ($hasPackages && !$request->has('force_delete')) {
            return redirect()->route('admin.units.index')
                ->with('confirm_delete', [
                    'unit_id' => $unit->id,
                    'message' => 'هذه الوحدة مرتبطة ببكاجات. هل تريد حذفها وجميع العلاقات المرتبطة بها؟'
                ]);
        }

        // حذف الصور
        foreach ($unit->images as $image) {
            \Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }

        // حذف العلاقات مع PackageUnitItem
        PackageUnitItem::where('unit_id', $unit->id)->delete();

        // حذف الوحدة نفسها
        $unit->delete();

        return redirect()->route('admin.units.index')->with('success', 'تم حذف الوحدة بنجاح');
    }


   /* public function destroyimage($unitId, $imageId)
    {
        $image = UnitImage::findOrFail($imageId);

        // حذف الصورة من التخزين
        if (\Storage::disk('public')->exists($image->image_path)) {
            \Storage::disk('public')->delete($image->image_path);
        }

        $image->delete();

        return response()->json(['success' => true]);
    }*/

    public function destroyImage($unitId, $imageId)
    {
        $unit = Unit::with('images')->findOrFail($unitId);
        $image = $unit->images->firstWhere('id', $imageId);

        if(!$image){
            return response()->json(['success' => false, 'message' => 'الصورة غير موجودة']);
        }

        if (\Storage::disk('public')->exists($image->image_path)) {
            \Storage::disk('public')->delete($image->image_path);
        }

        $image->delete();

        return response()->json(['success' => true]);
    }


    public function details(Unit $unit)
    {
        // تحميل الصور والبيانات المطلوبة
        $unit->load('images');

        return response()->json([
            'id' => $unit->id,
            'name_ar' => $unit->name_ar,
            'name_en' => $unit->name_en,
            'type' => $unit->type,
            'description_ar' => $unit->description_ar,
            'description_en' => $unit->description_en,
            'images' => $unit->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image_path' => asset('storage/' . $image->image_path),
                    'alt_text' => $image->alt_text,
                    'is_primary' => $image->is_primary,
                ];
            }),
        ]);
    }

}
