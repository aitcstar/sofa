<?php
namespace App\Http\Controllers\Admin\Home;

use App\Http\Controllers\Controller;
use App\Models\ProcessSection;
use App\Models\ProcessStep;
use Illuminate\Http\Request;

class ProcessSectionController extends Controller
{
    public function edit()
    {
        $process = ProcessSection::with('steps')->first();
        return view('admin.home.process.edit', compact('process'));
    }

    public function update(Request $request)
    {
        $process = ProcessSection::firstOrCreate([]);

        $data = $request->only([
            'title_ar','title_en','desc_ar','desc_en','button_text_en','button_text_ar',
            'name','units','status','progress'
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('process', 'public');
        }

        $process->update($data);

        // تحديث الخطوات
        if ($request->has('steps')) {
            foreach ($request->steps as $stepData) {
                if (isset($stepData['id'])) {
                    $step = ProcessStep::find($stepData['id']);
                    if ($step) {
                        if (isset($stepData['icon']) && $stepData['icon'] instanceof \Illuminate\Http\UploadedFile) {
                            $stepData['icon'] = $stepData['icon']->store('process_steps', 'public');
                        }
                        $step->update($stepData);
                    }
                } else {
                    if (isset($stepData['icon']) && $stepData['icon'] instanceof \Illuminate\Http\UploadedFile) {
                        $stepData['icon'] = $stepData['icon']->store('process_steps', 'public');
                    }
                    $process->steps()->create($stepData);
                }
            }
        }

        return redirect()->back()->with('success','تم تحديث بيانات خطوات العملية بنجاح');
    }
}
