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
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
        ]);


        $data = $request->all();

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
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
        ]);

        $data = $request->all();


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
