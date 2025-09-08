<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Design;
use Illuminate\Http\Request;

class DesignController extends Controller
{
    public function index()
    {
        $designs = Design::all();
        return view('admin.designs.index', compact('designs'));
    }

    public function create()
    {
        return view('admin.designs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('designs', 'public');
        }

        Design::create($data);

        return redirect()->route('admin.designs.index')->with('success', 'تم إنشاء التصميم بنجاح.');
    }

    public function edit(Design $design)
    {
        return view('admin.designs.edit', compact('design'));
    }

    public function update(Request $request, Design $design)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إذا وجدت
            if ($design->image_path) {
                \Storage::disk('public')->delete($design->image_path);
            }
            $data['image_path'] = $request->file('image')->store('designs', 'public');
        }

        $design->update($data);

        return redirect()->route('admin.designs.index')->with('success', 'تم تحديث التصميم بنجاح.');
    }

    public function destroy(Design $design)
    {
        if ($design->image_path) {
            \Storage::disk('public')->delete($design->image_path);
        }
        $design->delete();
        return redirect()->route('admin.designs.index')->with('success', 'تم حذف التصميم بنجاح.');
    }
}
