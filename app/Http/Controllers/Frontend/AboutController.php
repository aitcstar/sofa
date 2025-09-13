<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SeoSetting;
use App\Models\AboutPage;
class AboutController extends Controller
{
    public function index()
    {
        $seo = SeoSetting::where('page','about')->first();
        $sections = AboutPage::all();


        return view('frontend.pages.about', compact('seo','sections'));
    }
}
