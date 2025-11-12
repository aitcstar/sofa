<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SeoSetting;
use App\Models\PageContent;
use App\Models\HelpRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HelpRequestsExport;
use Illuminate\Support\Facades\Storage;

class HelpController extends Controller
{
    public function index()
    {
        $page = 'help';
        $seoSettings = SeoSetting::all()->keyBy('page');
        $content = PageContent::where('page', 'help')->first();
        $requests = HelpRequest::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.help.index', compact('page','seoSettings','content','requests'));
    }


    public function updatehelp(Request $request)
    {
        $request->validate([
            'title_ar' => 'required',
            'title_en' => 'required',
            'text_ar'  => 'required',
            'text_en'  => 'required',
        ]);

        $content = PageContent::where('page', 'help')->first();
        $content->update($request->all());

        return redirect()->back()->with('success', 'تم تحديث بيانات صفحة طلبات المساعدة بنجاح');
    }

    public function destroy(HelpRequest $request)
{
    $request->delete();
    return back()->with('success', 'تم حذف الطلب بنجاح');
}

public function export()
{
    return Excel::download(new HelpRequestsExport, 'help_requests.xlsx');
}

public function updateStatus(Request $request, $id)
{
    $request->validate(['status' => 'required|string']);
    $help = HelpRequest::findOrFail($id);
    $help->update(['status' => $request->status]);
    return response()->json(['success' => true]);
}


}
