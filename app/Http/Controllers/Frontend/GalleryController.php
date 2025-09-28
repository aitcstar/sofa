<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SeoSetting;
use App\Models\ExhibitionCategory;
use App\Models\Exhibition;
use App\Models\PageContent;
class GalleryController extends Controller
{
    /**
     * عرض صفحة المعرض
     */
    public function index(Request $request)
    {
        $seo = SeoSetting::where('page','gallery')->first();

        // جلب أقسام المعارض (التصنيفات)
        $categories = ExhibitionCategory::select('id', 'name_ar', 'name_en')->get();

        // جلب المعارض مع التصنيف المرتبط والصور
        $query = Exhibition::with(['category', 'images', 'packages.units.items']);

        // فلترة حسب التصنيف لو موجود في الريكوست
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // ترتيب أحدث الأول + تقسيم صفحات
        $exhibitions = $query->orderBy('created_at', 'desc')->paginate(9);

        // جلب محتوى الصفحة (لو عندك جدول page_contents)
        $content = PageContent::where('page', 'gallery')->first();

        return view('frontend.pages.gallery', compact('seo','categories', 'exhibitions', 'content'));
    }





}
