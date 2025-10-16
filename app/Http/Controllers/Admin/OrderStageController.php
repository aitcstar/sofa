<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderStage;
use Illuminate\Http\Request;

class OrderStageController extends Controller
{
    public function index()
    {
        $stages = OrderStage::orderBy('order_number')->get();
        return view('admin.order_stages.index', compact('stages'));
    }

    public function create()
    {
        return view('admin.order_stages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title_ar' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|array',
            'description_en' => 'nullable|array',
            'order_number' => 'required|integer|min:1',
        ]);

        OrderStage::create([
            'title_ar' => $request->title_ar,
            'title_en' => $request->title_en,
            'description_ar' => $request->description_ar,
            'description_en' => $request->description_en,
            'order_number' => $request->order_number,
        ]);

        return redirect()->route('admin.orders.enhanced.order_stages.index')->with('success', 'تمت إضافة المرحلة بنجاح');
    }

    public function edit(OrderStage $orderStage)
    {
        return view('admin.order_stages.edit', compact('orderStage'));
    }

    public function update(Request $request, OrderStage $orderStage)
    {
        $request->validate([
            'title_ar' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|array',
            'description_en' => 'nullable|array',
            'order_number' => 'required|integer|min:1',
        ]);

        $orderStage->update($request->all());
        return redirect()->route('admin.orders.enhanced.order_stages.index')->with('success', 'تم تعديل المرحلة بنجاح');
    }

    public function destroy(OrderStage $orderStage)
    {
        $orderStage->delete();
        return back()->with('success', 'تم حذف المرحلة بنجاح');
    }
}
