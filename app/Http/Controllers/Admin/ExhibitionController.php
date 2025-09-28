<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exhibition;
use App\Models\ExhibitionCategory;
use App\Models\Package;
use App\Models\SeoSetting;
use App\Models\PageContent;
use App\Models\ExhibitionImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExhibitionController extends Controller
{
    public function index()
    {
        $page = 'gallery';
        $seoSettings = SeoSetting::all()->keyBy('page');

        $exhibitions = Exhibition::with('category', 'packages')->latest()->paginate(10);
        $content = PageContent::where('page', 'gallery')->first();

        return view('admin.exhibitions.index', compact('exhibitions','page','seoSettings','content'));
    }

    public function create()
    {
        $categories = ExhibitionCategory::all();
        $packages = Package::all();
        return view('admin.exhibitions.create', compact('categories', 'packages'));
    }

    public function store(Request $request)
    {
       //
        $data = $request->validate([
            'category_id' => 'required|exists:exhibition_categories,id',
            'package_id' => 'nullable|exists:packages,id',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'summary_ar' => 'nullable|string',
            'summary_en' => 'nullable|string',
            //'description_ar' => 'nullable|string',
            //'description_en' => 'nullable|string',
            'delivery_date' => 'nullable|date',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg',
            'steps' => 'nullable|array',
            'steps.*.title_ar' => 'required_with:steps|string',
            'steps.*.title_en' => 'required_with:steps|string',
            'steps.*.sort_order' => 'nullable|string',
            'steps.*.icon' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg',
        ]);
        //dd($data);
        $exhibition = Exhibition::create($data);

        // الصور
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('exhibitions', 'public');
                $exhibition->images()->create([
                    'image' => $path,
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);
            }
        }

        // الخطوات
        if (!empty($data['steps'])) {
            foreach ($data['steps'] as $i => $step) {
                $iconPath = null;
                if (isset($step['icon']) && $request->file("steps.$i.icon")) {
                    $iconPath = $request->file("steps.$i.icon")->store('exhibition_steps', 'public');
                }

                $exhibition->steps()->create([
                    'title_ar' => $step['title_ar'],
                    'title_en' => $step['title_en'],
                    'icon' => $iconPath,
                    //'step_order' => $i,
                ]);
            }
        }


        return redirect()->route('admin.exhibitions.index')->with('success', 'تم إضافة المعرض بنجاح');
    }


    public function edit(Exhibition $exhibition)
    {
        $categories = ExhibitionCategory::all();
        $packages = Package::all();
        return view('admin.exhibitions.edit', compact('exhibition', 'categories', 'packages'));
    }

    public function update(Request $request, Exhibition $exhibition)
{
    $data = $request->validate([
        'category_id' => 'required|exists:exhibition_categories,id',
        'package_id' => 'nullable|exists:packages,id',
        'name_ar' => 'required|string|max:255',
        'name_en' => 'required|string|max:255',
        'summary_ar' => 'nullable|string',
        'summary_en' => 'nullable|string',
        'delivery_date' => 'nullable|date',
        'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg',
        'steps' => 'nullable|array',
        'steps.*.id' => 'nullable|exists:exhibition_steps,id',
        'steps.*.title_ar' => 'required|string',
        'steps.*.title_en' => 'required|string',
        'steps.*.sort_order' => 'nullable|integer',
        'steps.*.icon' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg',
        'steps.*._delete' => 'sometimes|in:0,1'
    ]);

    //dd($request->all());
    // تحديث بيانات المعرض الأساسية
    $exhibition->update($request->only([
        'category_id', 'package_id', 'name_ar', 'name_en',
        'summary_ar', 'summary_en', 'delivery_date'
    ]));

    // معالجة الصور الجديدة
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $file) {
            $path = $file->store('exhibitions', 'public');
            $exhibition->images()->create(['image' => $path]);
        }
    }

    // معالجة الخطوات
    if (!empty($data['steps'])) {
        foreach ($data['steps'] as $stepData) {
            // تخطي الخطوات المحددة للحذف
            if (isset($stepData['_delete']) && $stepData['_delete'] == '1') {
                if (!empty($stepData['id'])) {
                    $existingStep = $exhibition->steps()->find($stepData['id']);
                    if ($existingStep) {
                        // حذف الأيقونة من التخزين
                        if ($existingStep->icon) {
                            \Storage::disk('public')->delete($existingStep->icon);
                        }
                        $existingStep->delete();
                    }
                }
                continue;
            }

            // معالجة رفع الأيقونة
            $iconPath = null;
            if (isset($stepData['icon']) && $stepData['icon'] instanceof \Illuminate\Http\UploadedFile) {
                $iconPath = $stepData['icon']->store('exhibition_steps', 'public');
            }

            if (!empty($stepData['id'])) {
                // تحديث خطوة موجودة
                $existingStep = $exhibition->steps()->find($stepData['id']);
                if ($existingStep) {
                    $updateData = [
                        'title_ar' => $stepData['title_ar'],
                        'title_en' => $stepData['title_en'],
                        'sort_order' => $stepData['sort_order'] ?? 0,
                    ];

                    if ($iconPath) {
                        // حذف الأيقونة القديمة إذا كانت موجودة
                        if ($existingStep->icon) {
                            \Storage::disk('public')->delete($existingStep->icon);
                        }
                        $updateData['icon'] = $iconPath;
                    }

                    $existingStep->update($updateData);
                }
            } else {
                // إنشاء خطوة جديدة
                $exhibition->steps()->create([
                    'title_ar' => $stepData['title_ar'],
                    'title_en' => $stepData['title_en'],
                    'sort_order' => $stepData['sort_order'] ?? 0,
                    'icon' => $iconPath,
                ]);
            }
        }
    }

    return back()->with('success', 'تم تحديث المعرض والخطوات بنجاح');
}


    public function destroy(Exhibition $exhibition)
    {
        foreach ($exhibition->images as $image) {
            Storage::disk('public')->delete($image->image);
        }

        $exhibition->delete();
        return redirect()->route('admin.exhibitions.index')->with('success', 'تم حذف المعرض بنجاح');
    }

    public function setPrimaryImage(Exhibition $exhibition, ExhibitionImage $image)
{
    //dd('dd');
    // إعادة تعيين جميع الصور كـ غير رئيسية
    $exhibition->images()->update(['is_primary' => false]);

    // تعيين الصورة المختارة كرئيسية
    $image->update(['is_primary' => true]);
    return response()->json(['success' => true]);

    //return back()->with('success', 'تم تعيين الصورة الرئيسية بنجاح');
}

public function deleteImage(Exhibition $exhibition, ExhibitionImage $image)
{
    Storage::disk('public')->delete($image->image);
    $image->delete();
    return response()->json(['success' => true]);

    //return back()->with('success', 'تم حذف الصورة بنجاح');
}

public function updateExhibitions(Request $request)
    {
        $request->validate([
            'title_ar' => 'required',
            'title_en' => 'required',
            'text_ar'  => 'required',
            'text_en'  => 'required',
        ]);

        $content = PageContent::where('page', 'gallery')->first();
        $content->update($request->all());

        return redirect()->back()->with('success', 'تم تحديث بيانات صفحة المعرض بنجاح');
    }

}
