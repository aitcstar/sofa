<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SeoSetting;
use App\Models\AboutPage;

class CartController extends Controller
{
    public function index()
    {
        $seo = SeoSetting::where('page','about')->first();
        $sections = AboutPage::all();


        return view('frontend.pages.cart', compact('seo','sections'));
    }
}
