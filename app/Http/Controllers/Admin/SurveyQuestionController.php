<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SurveyQuestion;

class SurveyQuestionController extends Controller
{
    public function index()
    {
        $questions = SurveyQuestion::with('options')->orderBy('order')->paginate(10);
        $section = SurveyQuestion::first();
        return view('admin.survey-questions.index', compact('questions','section'));
    }

    public function create()
    {
        return view('admin.survey-questions.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'title_ar' => 'required|string|max:255',
        'title_en' => 'required|string|max:255',
        'type' => 'required|in:radio,checkbox,select,text,number',
        'is_required' => 'boolean',
        'options.*.label_ar' => 'required_if:type,radio,checkbox,select',
        'options.*.label_en' => 'required_if:type,radio,checkbox,select',
        'options.*.value_ar' => 'required_if:type,radio,checkbox,select',
        'options.*.value_en' => 'required_if:type,radio,checkbox,select',
    ]);

    $question = SurveyQuestion::create([
        'title_ar' => $request->title_ar,
        'title_en' => $request->title_en,
        'type' => $request->type,
        'is_required' => $request->boolean('is_required'),
        'order' => $request->order ?? 0,
    ]);

    if (in_array($request->type, ['radio', 'checkbox', 'select']) && $request->has('options')) {
        foreach ($request->options as $index => $opt) {
            $question->options()->create([
                'survey_question_id' => $question->id,
                'label_ar' => $opt['label_ar'],
                'value_ar' => $opt['value_ar'],
                'label_en' => $opt['label_en'],
                'value_en' => $opt['value_en'],
                'order' => $index,
            ]);
        }
    }

    return redirect()->route('admin.survey-questions.index')->with('success', 'تم إضافة السؤال بنجاح');
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function edit(SurveyQuestion $survey_question)
{
    $survey_question->load('options');
    return view('admin.survey-questions.edit', compact('survey_question'));
}

public function update(Request $request, SurveyQuestion $survey_question)
{
    $request->validate([
        'title_ar' => 'required|string|max:255',
        'title_en' => 'required|string|max:255',
        'type' => 'required|in:radio,checkbox,select,text,number',
        'is_required' => 'boolean',
        'options.*.label_ar' => 'nullable|string',
        'options.*.label_en' => 'nullable|string',
        'options.*.value_ar' => 'nullable|string',
        'options.*.value_en' => 'nullable|string',
    ]);

    $survey_question->update([
        'title_ar' => $request->title_ar,
        'title_en' => $request->title_en,
        'type' => $request->type,
        'is_required' => $request->boolean('is_required'),
    ]);

    if (in_array($request->type, ['radio','checkbox','select'])) {
        $survey_question->options()->delete(); // نحذف القديم
        foreach ($request->options ?? [] as $index => $opt) {
            $survey_question->options()->create([
                'survey_question_id' => $survey_question->id,
                'label_ar' => $opt['label_ar'] ?? '',
                'label_en' => $opt['label_en'] ?? '',
                'value_ar' => $opt['value_ar'] ?? '',
                'value_en' => $opt['value_en'] ?? '',
                'order' => $index,
            ]);
        }
    }

    return redirect()->route('admin.survey-questions.index')->with('success', 'تم التحديث بنجاح');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

       // dd($id);
        $question = SurveyQuestion::findOrFail($id); // سيُظهر 404 إذا لم يوجد
        $question->delete();

        return redirect()->back()->with('success', 'تم حذف السؤال بنجاح.');
    }

    public function choosepackeg(Request $request, SurveyQuestion $survey_question)
    {
        $request->validate([
            'title_en' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'desc_en'  => 'required|string',
            'desc_ar'  => 'required|string',
        ]);


        $survey_question->where('id',5)->update([
            'title_ar' => $request->title_ar,
            'title_en' => $request->title_en,
            'desc_en' => $request->desc_en,
            'desc_ar' =>  $request->desc_ar,
        ]);
        return redirect()->route('admin.survey-questions.index')->with('success', 'تم التحديث بنجاح');
    }

}
