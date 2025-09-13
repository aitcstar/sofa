<?php
namespace App\Http\Controllers\Admin\Home;

use App\Http\Controllers\Controller;
use App\Models\HeroSlider;
use App\Models\SeoSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HeroSliderController extends Controller
{
    public function index()
    {
        $sliders = HeroSlider::latest()->paginate(10);
        $page = 'home';
        $seoSettings = SeoSetting::all()->keyBy('page');
        return view('admin.home.hero-sliders.index', compact('sliders','page','seoSettings'));
    }

    public function create()
    {
        return view('admin.home.hero-sliders.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image',
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_active' => 'nullable',
        ]);

        $imagePath = $request->file('image')->store('hero-sliders', 'public');

       // dd($request->all());
        HeroSlider::create([
            'image' => $imagePath,
            'title_ar' => $request->title_ar,
            'title_en' => $request->title_en,
            'description_ar' => $request->description_ar,
            'description_en' => $request->description_en,
            'order' => $request->order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.hero-sliders.index')->with('success', 'تم إضافة السلايد بنجاح');
    }

    public function edit(HeroSlider $heroSlider)
    {
        return view('admin.home.hero-sliders.edit', compact('heroSlider'));
    }

    public function update(Request $request, HeroSlider $heroSlider)
    {

        $request->validate([
            'image' => 'nullable|image',
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_active' => 'nullable',
        ]);
        //dd($request->all());
        $data = $request->only([
            'title_ar', 'title_en', 'description_ar', 'description_en', 'order'
        ]);

        $data['is_active'] = $request->has('is_active');

        // معالجة الصورة إذا تم رفعها
        if ($request->hasFile('image')) {
            if ($heroSlider->image) {
                Storage::disk('public')->delete($heroSlider->image);
            }
            $data['image'] = $request->file('image')->store('hero-sliders', 'public');
        }

        $heroSlider->update($data);

        return redirect()->route('admin.hero-sliders.index')->with('success', 'تم تحديث السلايدر بنجاح');
    }


    public function destroy(HeroSlider $heroSlider)
    {
        Storage::disk('public')->delete($heroSlider->image);
        $heroSlider->delete();

        return redirect()->route('admin.hero-sliders.index')->with('success', 'تم حذف السلايد بنجاح');
    }
}
