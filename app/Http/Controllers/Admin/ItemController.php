<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Design;
use App\Models\Unit;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function allItems()
{
    // تجيب كل العناصر
    $items = Item::with('unit')->get();

    // ترسلهم للـ view
    return view('admin.items.all', compact('items'));
}


    public function index(Design $design)
    {
        $items = $design->items;
        return view('admin.items.index', compact('design', 'items'));
    }

    public function create(Design $design)
    {
        return view('admin.items.create', compact('design'));
    }

    public function store(Request $request, Design $design)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'item_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'dimensions' => 'nullable|string|max:100',
            'material' => 'required|string|max:255',
            'color' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('items', 'public');
        }

        $data['design_id'] = $design->id;

        Item::create($data);

        return redirect()->route('admin.designs.items.index', $design)->with('success', 'تم إضافة القطعة بنجاح.');
    }

    public function edit(Design $design, Item $item)
    {
        return view('admin.items.edit', compact('design', 'item'));
    }

    public function update(Request $request, Design $design, Item $item)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'item_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'dimensions' => 'nullable|string|max:100',
            'material' => 'required|string|max:255',
            'color' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            if ($item->image_path) {
                \Storage::disk('public')->delete($item->image_path);
            }
            $data['image_path'] = $request->file('image')->store('items', 'public');
        }

        $item->update($data);

        return redirect()->route('admin.designs.items.index', $design)->with('success', 'تم تحديث القطعة بنجاح.');
    }

    public function destroy(Design $design, Item $item)
    {
        if ($item->image_path) {
            \Storage::disk('public')->delete($item->image_path);
        }
        $item->delete();
        return redirect()->route('admin.designs.items.index', $design)->with('success', 'تم حذف القطعة بنجاح.');
    }
}
