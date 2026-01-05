<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faq;
use App\Models\SeoSetting;
use App\Models\PageContent;
use App\Models\FaqCategory;
use App\Models\Package;

class FaqController extends Controller
{
    public function index()
    {
        $page = 'faq';
        $seoSettings = SeoSetting::all()->keyBy('page');
        $faqs = Faq::with('category','package')->orderBy('sort')->get();
        $content = PageContent::where('page', 'faq')->first();

        return view('admin.faqs.index', compact('faqs','page','seoSettings','content'));
    }

    public function create()
    {
        $categories = FaqCategory::orderBy('sort')->get();
        $packages = Package::active()->ordered()->get();

        return view('admin.faqs.create', compact('categories','packages'));
    }

    public function store(Request $request)
{
    $request->validate([
        'category_id' => 'required|exists:faq_categories,id',
        'question_ar' => 'required|string|max:255',
        'question_en' => 'required|string|max:255',
        'answer_ar' => 'required|string',
        'answer_en' => 'required|string',
        'sort'       => 'required|numeric',
        'page'       => 'required|string',
        'package_id'  => 'nullable|exists:packages,id', // اختياري
    ]);

    Faq::create($request->only([
        'category_id', 'package_id','question_ar', 'question_en', 'answer_ar', 'answer_en', 'sort', 'page'
    ]));

    return redirect()->route('admin.faqs.index')->with('success', 'تمت إضافة السؤال بنجاح');
}


    public function edit(Faq $faq)
    {
        $categories = FaqCategory::orderBy('sort')->get();
        $packages = Package::active()->ordered()->get();

        return view('admin.faqs.edit', compact('faq', 'categories','packages'));
    }

    public function update(Request $request, Faq $faq)
    {
        $request->validate([
            'category_id' => 'required|exists:faq_categories,id',
            'question_ar' => 'required|string|max:255',
            'question_en' => 'required|string|max:255',
            'answer_ar' => 'required|string',
            'answer_en' => 'required|string',
            'sort'       => 'required|numeric',
            'page'       => 'required|string',
            'package_id'  => 'nullable|exists:packages,id', // اختياري
        ]);

        $faq->update($request->only([
            'category_id', 'package_id','question_ar', 'question_en', 'answer_ar', 'answer_en', 'sort', 'page'
        ]));

        return redirect()->route('admin.faqs.index')->with('success', 'تم تحديث السؤال بنجاح');
    }


    public function destroy(Faq $faq)
    {
        $faq->delete();
        return redirect()->route('admin.faqs.index')->with('success', 'تم حذف السؤال بنجاح');
    }

    public function updatefaq(Request $request)
    {
        $request->validate([
            'title_ar' => 'required',
            'title_en' => 'required',
            'text_ar'  => 'required',
            'text_en'  => 'required',
        ]);

        $content = PageContent::where('page', 'faq')->first();
        $content->update($request->all());

        return redirect()->back()->with('success', 'تم تحديث بيانات صفحة المدونة بنجاح');
    }

}
