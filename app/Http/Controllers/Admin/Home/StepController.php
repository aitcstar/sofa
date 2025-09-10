<?php

namespace App\Http\Controllers\Admin\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Step;
use Illuminate\Support\Facades\Storage;

class StepController extends Controller
{
    public function index()
{
    $steps = Step::orderBy('order')->get();
    return view('admin.home.steps.index', compact('steps'));
}

public function create()
{
    return view('admin.home.steps.create');
}

public function store(Request $request)
{
    $request->validate([
        'icon' => 'required|image',
        'title_en' => 'required',
        'title_ar' => 'required',
        'desc_en' => 'required',
        'desc_ar' => 'required',
        'order' => 'required|integer',
    ]);

    $icon = $request->file('icon')->store('steps', 'public');

    Step::create([
        'icon' => $icon,
        'title_en' => $request->title_en,
        'title_ar' => $request->title_ar,
        'desc_en' => $request->desc_en,
        'desc_ar' => $request->desc_ar,
        'order' => $request->order,
    ]);

    return redirect()->route('admin.steps.index')->with('success', 'Step created!');
}

public function edit(Step $step)
    {
        return view('admin.home.steps.edit', compact('step'));
    }

public function update(Request $request, Step $step)
    {
        $data = $request->validate([
            'title_ar' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'desc_ar' => 'required|string',
            'desc_en' => 'nullable|string',
            'order' => 'required|integer',
            'icon' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('icon')) {
            // حذف الصورة القديمة
            if ($step->icon) {
                Storage::disk('public')->delete($step->icon);
            }
            $data['icon'] = $request->file('icon')->store('steps', 'public');
        }

        $step->update($data);
        return redirect()->route('admin.steps.index')->with('success', 'تم تعديل الخطوة بنجاح');
    }

    public function destroy(Step $step)
    {
        if ($step->icon) {
            Storage::disk('public')->delete($step->icon);
        }
        $step->delete();
        return redirect()->route('admin.steps.index')->with('success', 'تم حذف الخطوة بنجاح');
    }
}
