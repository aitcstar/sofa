<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faq;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::orderBy('sort', 'asc')->get();
        return view('admin.faqs.index', compact('faqs'));
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
    ]);

    Faq::create([
        'category_ar' => $request->category_ar,
        'category_en' => $request->category_en,
        'question_ar' => $request->question_ar,
        'question_en' => $request->question_en,
        'answer_ar' => $request->answer_ar,
        'answer_en' => $request->answer_en,
        'sort' => $request->sort,

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
        ]);

        return redirect()->route('admin.faqs.index')->with('success', 'تم تحديث السؤال بنجاح');
    }


    public function destroy(Faq $faq)
    {
        $faq->delete();
        return redirect()->route('admin.faqs.index')->with('success', 'تم حذف السؤال بنجاح');
    }
}
