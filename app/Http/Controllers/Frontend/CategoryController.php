<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        // جلب التصنيفات النشطة مع العلاقات إذا لزم الأمر
        $categories = Category::active()->ordered()->get();

        // إذا لم تكن هناك تصنيفات في قاعدة البيانات، استخدم بيانات افتراضية
        if ($categories->isEmpty()) {
            $categories = collect([
                (object)[
                    'id' => 1,
                    'name' => 'باكج غرفة نوم واحدة',
                    'description' => 'مثالي للمساحات الصغيرة، يوفر الراحة والأناقة',
                    'image' => 'assets/images/category/category-01.jpg',
                    'items_count' => 30,
                    'price' => 1205500,
                    'features' => [
                        ['icon' => 'assets/images/icons/caricone.png', 'name' => 'غرفة نوم'],
                        ['icon' => 'assets/images/icons/sofa.png', 'name' => 'مجلس'],
                        ['icon' => 'assets/images/icons/foot.png', 'name' => 'طاولة طعام']
                    ],
                    'colors' => ['#f5f1e6', '#aaaaaa', '#a1866f', '#8b5e3c'],
                    'duration' => '15–20 يوم عمل'
                ],
                (object)[
                    'id' => 2,
                    'name' => 'باكج غرفتين نوم',
                    'description' => 'مثالي للعائلات الصغيرة، يوفر مساحة كافية للجميع',
                    'image' => 'assets/images/category/category-02.jpg',
                    'items_count' => 45,
                    'price' => 1800000,
                    'features' => [
                        ['icon' => 'assets/images/icons/caricone.png', 'name' => 'غرفتي نوم'],
                        ['icon' => 'assets/images/icons/sofa.png', 'name' => 'مجلس'],
                        ['icon' => 'assets/images/icons/foot.png', 'name' => 'طاولة طعام']
                    ],
                    'colors' => ['#f5f1e6', '#aaaaaa', '#a1866f'],
                    'duration' => '20–25 يوم عمل'
                ],
                (object)[
                    'id' => 3,
                    'name' => 'باكج ثلاث غرف نوم',
                    'description' => 'مثالي للعائلات الكبيرة، يوفر الراحة والخصوصية',
                    'image' => 'assets/images/category/category-03.png',
                    'items_count' => 60,
                    'price' => 2500000,
                    'features' => [
                        ['icon' => 'assets/images/icons/caricone.png', 'name' => 'ثلاث غرف نوم'],
                        ['icon' => 'assets/images/icons/sofa.png', 'name' => 'مجلس'],
                        ['icon' => 'assets/images/icons/foot.png', 'name' => 'طاولة طعام']
                    ],
                    'colors' => ['#f5f1e6', '#aaaaaa'],
                    'duration' => '25–30 يوم عمل'
                ],
                (object)[
                    'id' => 4,
                    'name' => 'باكج استوديو',
                    'description' => 'مثالي للمساحات الصغيرة، تصميم أنيق وعملي',
                    'image' => 'assets/images/category/category-04.jpg',
                    'items_count' => 25,
                    'price' => 900000,
                    'features' => [
                        ['icon' => 'assets/images/icons/caricone.png', 'name' => 'استوديو'],
                        ['icon' => 'assets/images/icons/sofa.png', 'name' => 'مجلس'],
                        ['icon' => 'assets/images/icons/foot.png', 'name' => 'طاولة طعام']
                    ],
                    'colors' => ['#f5f1e6', '#aaaaaa', '#a1866f', '#8b5e3c'],
                    'duration' => '10–15 يوم عمل'
                ]
            ]);
        }

        // التأكد من أن كل تصنيف يحتوي على مصفوفة features
        $categories = $categories->map(function($category) {
            if (!isset($category->features) || !is_array($category->features)) {
                $category->features = [];
            }
            if (!isset($category->colors) || !is_array($category->colors)) {
                $category->colors = [];
            }
            return $category;
        });

        return view('frontend.categories.index', compact('categories'));
    }

    public function show(Category $category)
{
    // Load products and category images
    $products = $category->products()->active()->ordered()->paginate(12);

    // Check if category has images, otherwise use default images
    if ($category->images && count($category->images) > 0) {
        $images = $category->images;
    } else {
        // Default images if none exist
        $images = [
            'assets/images/category/category-01.jpg',
            'assets/images/category/category-02.jpg',
            'assets/images/category/category-03.png',
            'assets/images/category/category-04.jpg'
        ];
    }

    // For the testimonials and FAQs, you need to define these or get from database
    $testimonials = [
        [
            'comment' => 'تجربة رائعة مع SOFA، الجودة كانت ممتازة والتسليم في الوقت المحدد.',
            'avatar' => 'assets/images/testimonials/avatar-1.jpg',
            'name' => 'أحمد السعدي',
            'location' => 'الرياض'
        ],
        // Add more testimonials as needed
    ];

    $faqs = [
        [
            'question' => 'كم تستغرق مدة التوصيل؟',
            'answer' => 'مدة التوصيل تتراوح بين 15-30 يوم عمل حسب الباكج المختار.'
        ],
        // Add more FAQs as needed
    ];

    return view('frontend.categories.show', compact('category', 'products', 'images', 'testimonials', 'faqs'));
}
}
