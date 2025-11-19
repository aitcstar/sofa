<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SeoSetting;
use App\Models\PageContent;
use App\Models\Testimonial;

class ProfileController extends Controller
{

    // عرض صفحة الملف الشخصي
public function index(Request $request)
{
    $seo = SeoSetting::where('page', 'blog')->first();
    $content = PageContent::where('page','blog')->first();

    $testimonials = Testimonial::where('status', 'approved')->where('user_id',auth()->user()->id)->latest()->take(10)->get();

    return view('frontend.pages.profile', compact('seo','content','testimonials'));
}

// تحديث بيانات المستخدم
public function update(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'email' => 'required|email|unique:users,email,' . auth()->id(),
    ]);

    auth()->user()->update($request->only(['name', 'phone', 'email']));

    return back()->with('success', __('site.profile_updated_successfully'));
}


public function destroy($id)
{
    $testimonial = Testimonial::where('id', $id)
        ->where('user_id', auth()->id()) // علشان ما يحذفش غير تعليقه
        ->firstOrFail();

    $testimonial->delete();

    return back()->with('success', 'تم حذف التقييم بنجاح');
}



}
