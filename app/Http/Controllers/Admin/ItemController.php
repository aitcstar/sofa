<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Design;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function allItems()
{
    // تجيب كل العناصر
    $items = Item::whereNull('package_id')->with('unit')->get();

    // ترسلهم للـ view
    return view('admin.items.all', compact('items'));
}


    public function index()
    {
        //$items = $design->items;
        $items = Item::whereNull('package_id')->with('unit')->get();

        return view('admin.items.index', compact('items'));
    }

    public function create()
    {
        $units = Unit::whereNull('package_id')->get();
        return view('admin.items.create', compact('units'));
    }

    public function store(Request $request, Design $design)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'item_name_ar' => 'required|string|max:255',
            'item_name_en' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'dimensions' => 'nullable|string|max:100',
            'material_ar' => 'required|string|max:255',
            'material_en' => 'required|string|max:255',
            'color_ar' => 'required|string|max:100',
            'color_en' => 'required|string|max:100',
            'background_color' => 'required|string|max:100',
            'image_path' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        // تحضير البيانات
        $data = $request->only([
            'unit_id',
            'item_name_ar', 'item_name_en',
            'quantity',
            'dimensions',
            'material_ar', 'material_en',
            'color_ar', 'color_en',
            'background_color',
        ]);


        // رفع الصورة إذا كانت موجودة
        if ($request->hasFile('image_path')) {
            $data['image_path'] = $request->file('image_path')->store('items', 'public');
        }

        // إنشاء القطعة
        Item::create($data);

        return redirect()->route('admin.items.index', $design)
                         ->with('success', 'تم إضافة القطعة بنجاح.');
    }



    public function edit(Item $item)
    {
        $units = Unit::whereNull('package_id')
        ->orWhere('id', $item->unit_id)
        ->get();
        return view('admin.items.edit', compact('item', 'units'));
    }

public function update(Request $request, Item $item)
{
    $request->validate([
        'unit_id' => 'required|exists:units,id',
        'item_name_ar' => 'required|string|max:255',
        'item_name_en' => 'required|string|max:255',
        'quantity' => 'required|integer|min:1',
        'dimensions' => 'nullable|string|max:100',
        'material_ar' => 'required|string|max:255',
        'material_en' => 'required|string|max:255',
        'color_ar' => 'required|string|max:100',
        'color_en' => 'required|string|max:100',
        'background_color' => 'required|string|max:100',
        'image_path' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
    ]);

    // تحضير البيانات
    $data = $request->only([
        'unit_id',
        'item_name_ar', 'item_name_en',
        'quantity',
        'dimensions',
        'material_ar', 'material_en',
        'color_ar', 'color_en',
        'background_color',
    ]);

    // رفع الصورة الجديدة إذا تم اختيارها وحذف القديمة
    if ($request->hasFile('image_path')) {
        if ($item->image_path) {
            \Storage::disk('public')->delete($item->image_path);
        }
        $data['image_path'] = $request->file('image_path')->store('items', 'public');
    }

    // تحديث القطعة
    $item->update($data);

    return redirect()->route('admin.items.index', $item->design)
                     ->with('success', 'تم تحديث القطعة بنجاح.');
}


    public function destroy(Item $item)
    {
        //dd($item);
        if ($item->image_path) {
            \Storage::disk('public')->delete($item->image_path);
        }
        $item->delete();
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'تم الحذف بنجاح']);
        }
        return redirect()->route('admin.items.index')->with('success', 'تم حذف القطعة بنجاح.');
    }


    public function destroyImage(Item $item)
    {

        if ($item->image_path && \Storage::disk('public')->exists($item->image_path)) {
            \Storage::disk('public')->delete($item->image_path);
        }

        $item->update(['image_path' => null]);

        return response()->json(['success' => true]);
    }


/*public function destroy(Item $item)
{
    if ($item->image_path) {
        \Storage::disk('public')->delete($item->image_path);
    }
    $item->delete();

    return response()->json(['success' => true]);
}*/

public function getByUnit($unitId)
{
    // تأكد أن الوحدة موجودة ومتاحة
    $unit = Unit::whereNull('package_id')->findOrFail($unitId);

    $items = Item::where('unit_id', $unitId)->get([
        'id', 'item_name_ar', 'item_name_en', 'quantity', 'dimensions',
        'material_ar', 'material_en', 'color_ar', 'color_en','background_color', 'image_path'
    ]);

    return response()->json($items);
}



}
