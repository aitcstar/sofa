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

        $countries = [
            ['code' => 'sa', 'name_ar' => 'السعودية', 'name_en' => 'Saudi Arabia', 'dial_code' => '+966'],
            ['code' => 'ae', 'name_ar' => 'الإمارات', 'name_en' => 'United Arab Emirates', 'dial_code' => '+971'],
            ['code' => 'kw', 'name_ar' => 'الكويت', 'name_en' => 'Kuwait', 'dial_code' => '+965'],
            ['code' => 'qa', 'name_ar' => 'قطر', 'name_en' => 'Qatar', 'dial_code' => '+974'],
            ['code' => 'bh', 'name_ar' => 'البحرين', 'name_en' => 'Bahrain', 'dial_code' => '+973'],
            ['code' => 'om', 'name_ar' => 'عمان', 'name_en' => 'Oman', 'dial_code' => '+968'],
            ['code' => 'jo', 'name_ar' => 'الأردن', 'name_en' => 'Jordan', 'dial_code' => '+962'],
            ['code' => 'lb', 'name_ar' => 'لبنان', 'name_en' => 'Lebanon', 'dial_code' => '+961'],
            ['code' => 'eg', 'name_ar' => 'مصر', 'name_en' => 'Egypt', 'dial_code' => '+20'],
            ['code' => 'ma', 'name_ar' => 'المغرب', 'name_en' => 'Morocco', 'dial_code' => '+212'],
            ['code' => 'dz', 'name_ar' => 'الجزائر', 'name_en' => 'Algeria', 'dial_code' => '+213'],
            ['code' => 'tn', 'name_ar' => 'تونس', 'name_en' => 'Tunisia', 'dial_code' => '+216'],
            ['code' => 'sd', 'name_ar' => 'السودان', 'name_en' => 'Sudan', 'dial_code' => '+249'],
            ['code' => 'iq', 'name_ar' => 'العراق', 'name_en' => 'Iraq', 'dial_code' => '+964'],
            ['code' => 'sy', 'name_ar' => 'سوريا', 'name_en' => 'Syria', 'dial_code' => '+963'],
            ['code' => 'ye', 'name_ar' => 'اليمن', 'name_en' => 'Yemen', 'dial_code' => '+967'],
            ['code' => 'ps', 'name_ar' => 'فلسطين', 'name_en' => 'Palestine', 'dial_code' => '+970'],
            ['code' => 'us', 'name_ar' => 'الولايات المتحدة', 'name_en' => 'United States', 'dial_code' => '+1'],
            ['code' => 'gb', 'name_ar' => 'المملكة المتحدة', 'name_en' => 'United Kingdom', 'dial_code' => '+44'],
            ['code' => 'de', 'name_ar' => 'ألمانيا', 'name_en' => 'Germany', 'dial_code' => '+49'],
            ['code' => 'fr', 'name_ar' => 'فرنسا', 'name_en' => 'France', 'dial_code' => '+33'],
            ['code' => 'it', 'name_ar' => 'إيطاليا', 'name_en' => 'Italy', 'dial_code' => '+39'],
            ['code' => 'es', 'name_ar' => 'إسبانيا', 'name_en' => 'Spain', 'dial_code' => '+34'],
            ['code' => 'ca', 'name_ar' => 'كندا', 'name_en' => 'Canada', 'dial_code' => '+1'],
            ['code' => 'au', 'name_ar' => 'أستراليا', 'name_en' => 'Australia', 'dial_code' => '+61'],
            ['code' => 'in', 'name_ar' => 'الهند', 'name_en' => 'India', 'dial_code' => '+91'],
            ['code' => 'pk', 'name_ar' => 'باكستان', 'name_en' => 'Pakistan', 'dial_code' => '+92'],
            ['code' => 'tr', 'name_ar' => 'تركيا', 'name_en' => 'Turkey', 'dial_code' => '+90'],
            ['code' => 'ir', 'name_ar' => 'إيران', 'name_en' => 'Iran', 'dial_code' => '+98'],
        ];


        return view('frontend.home', compact('seo','packages', 'featured_products','testimonials','faqs','sliders'
                    ,'steps','about','process','processsteps','whyChoose','timelines','readyToFurnish','exhibitions','questions','countries'));
    }
}
