<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoSetting;
use Illuminate\Http\Request;

class SEOSettingController extends Controller
{
    public function index()
    {
        $pages = ['home', 'category', 'about', 'gallery' ,'blog' ,'contact' ,'help' ,'faq'];
        $seoSettings = SeoSetting::all()->keyBy('page');

        return view('admin.seo.index', compact('pages', 'seoSettings'));
    }

    public function update(Request $request)
    {
        foreach ($request->seo as $page => $data) {
            SeoSetting::updateOrCreate(
                ['page' => $page],
                $data
            );
        }

        return redirect()->back()->with('success', 'تم تحديث إعدادات SEO بنجاح');
    }
}
