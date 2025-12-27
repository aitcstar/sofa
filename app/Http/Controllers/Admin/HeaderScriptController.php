<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HeaderScript;

class HeaderScriptController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'script' => 'required|string',
        ]);

        HeaderScript::create($request->only('title', 'script'));

        return redirect()->back()->with('success', 'تم إضافة الكود بنجاح!');
    }

    public function destroy($id)
    {
        HeaderScript::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'تم حذف الكود بنجاح!');
    }
}
