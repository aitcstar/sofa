<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Testimonial;
use App\Models\Faq;
use App\Models\HeroSlider;
use App\Models\Step;
use App\Models\HomeAboutSection;
use App\Models\ProcessSection;
use App\Models\ProcessStep;
use App\Models\WhyChooseSection;
use App\Models\ReadyToFurnishSection;
use App\Models\SeoSetting;
use App\Models\OrderTimelineSection;
use App\Models\Package;
use App\Models\Exhibition;
use App\Models\SurveyQuestion;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $seo = SeoSetting::where('page','home')->first();
        //$packages = Package::with(['images', 'units.designs','units.items'])->take(4)->get();
        $packages = Package::with([
            'images',
            'packageUnitItems.unit',
            'packageUnitItems.item'
        ])->active()->ordered()->take(4)->get();

        $featured_products = Product::active()->featured()->take(8)->get();
        $testimonials = Testimonial::latest()->take(10)->get();


        if (app()->getLocale() == 'ar') {
            $faqs = Faq::where('page', 'home')
                    ->orderBy('sort', 'asc')
                    ->get();
        } else {
            $faqs = Faq::where('page', 'home')
                    ->orderBy('sort', 'asc')
                    ->get();
        }


        //$faqs = Faq::latest()->take(10)->get();
        $sliders = HeroSlider::where('is_active', true)->orderBy('order')->get();
        $steps = Step::orderBy('order')->get();
        $about = HomeAboutSection::with('icons')->first();
        $process = ProcessSection::first(); // البيانات الأساسية
        $processsteps = ProcessStep::orderBy('order')->get(); // الخطوات بالترتيب
        $whyChoose = WhyChooseSection::with('items')->first();
        $timelines = OrderTimelineSection::with('items')->first();
        $readyToFurnish = ReadyToFurnishSection::first();

        $exhibitions = Exhibition::with(['images'])->where('is_active', 1)->get();
        $questions = SurveyQuestion::with('options')->orderBy('order')->get();

        return view('frontend.home', compact('seo','packages', 'featured_products','testimonials','faqs','sliders'
                    ,'steps','about','process','processsteps','whyChoose','timelines','readyToFurnish','exhibitions','questions'));
    }
}
