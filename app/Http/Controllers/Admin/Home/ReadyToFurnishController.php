<?php
namespace App\Http\Controllers\Admin\Home;

use App\Http\Controllers\Controller;
use App\Models\ReadyToFurnishSection;
use Illuminate\Http\Request;

class ReadyToFurnishController extends Controller
{
    public function edit()
    {
        $section = ReadyToFurnishSection::first();
        return view('admin.home.ready_to_furnish.edit', compact('section'));
    }

    public function update(Request $request)
    {
        $section = ReadyToFurnishSection::first();
        $data = $request->only(['title_en','title_ar','desc_en','desc_ar','whatsapp','start_order_link']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('ready_to_furnish','public');
        }

        $section->update($data);

        return redirect()->back()->with('success','تم التحديث بنجاح');
    }
}
