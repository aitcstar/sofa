<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faq;
use App\Models\SeoSetting;
use App\Models\PageContent;

class FaqController extends Controller
{
    public function index()
    {
        $page = 'faq';
        $seoSettings = SeoSetting::all()->keyBy('page');

        $faqs = Faq::orderBy('sort', 'asc')->get();
        $content = PageContent::where('page', 'faq')->first();

        return view('admin.faqs.index', compact('faqs','page','seoSettings','content'));
    }

    public function create()
    {
        return view('admin.faqs.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'category_ar' => 'required|string|max:255',
        'category_en' => 'required|string|max:255',
        'question_ar' => 'required|string|max:255',
        'question_en' => 'required|string|max:255',
        'answer_ar' => 'required|string',
        'answer_en' => 'required|string',
        'sort' => 'required|string',
        'page'        => 'required|string',
    ]);

    Faq::create([
        'category_ar' => $request->category_ar,
        'category_en' => $request->category_en,
        'question_ar' => $request->question_ar,
        'question_en' => $request->question_en,
        'answer_ar' => $request->answer_ar,
        'answer_en' => $request->answer_en,
        'sort' => $request->sort,
        'page'        => $request->page,


    ]);

    return redirect()->route('admin.faqs.index')->with('success', 'تمت إضافة السؤال بنجاح');
}


    public function edit(Faq $faq)
    {
        return view('admin.faqs.edit', compact('faq'));
    }

    public function update(Request $request, Faq $faq)
    {
        $request->validate([
            'category_ar' => 'required|string|max:255',
            'category_en' => 'required|string|max:255',
            'question_ar' => 'required|string|max:255',
            'question_en' => 'required|string|max:255',
            'answer_ar' => 'required|string',
            'answer_en' => 'required|string',
            'sort' => 'required|string',
            'page'        => 'required|string',
        ]);

        // تحديث الأعمدة مباشرة
        $faq->update([
            'category_ar' => $request->category_ar,
            'category_en' => $request->category_en,
            'question_ar' => $request->question_ar,
            'question_en' => $request->question_en,
            'answer_ar' => $request->answer_ar,
            'answer_en' => $request->answer_en,
            'sort' => $request->sort,
            'page'        => $request->page,
        ]);

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
