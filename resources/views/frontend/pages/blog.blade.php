@extends('frontend.layouts.pages')

@section('title', 'المدونة - SOFA Experience')
@section('description', 'استكشف نصائح وأفكار تصميم، وتعرف على أسرار تجهيز الوحدات السكنية بأعلى كفاءة وجودة')

@section('content')
<!-- ===== BREADCRUMB ===== -->
<div class="breadcrumb-container container">
    <a href="{{ route('home') }}" class="body-2 text-body">الرئيسية</a>
    <span class="body-2 text-body">/</span>
    <a href="{{ route('blog.index') }}" class="body-2 text-primary">المدونة</a>
</div>

<!-- ===== BLOG SECTION ===== -->
<section class="blog-section">
    <div class="container d-flex flex-column gap-md">
        <!-- Heading -->
        <div class="blog-heading d-flex flex-column gap-sm-5 align-items-center">
            <h2 class="heading-h7 text-heading">دليلك الذكي للتأثيث الفندقي والجاهز</h2>
            <p class="body-2 text-caption mb-0">استكشف نصائح وأفكار تصميم، وتعرف على أسرار تجهيز الوحدات السكنية بأعلى كفاءة
                وجودة</p>
        </div>

        <!-- Filter (Tabs) -->
        <div class="blog-filter">
            <div class="blog-filter-item active" data-category="all">
                <p class="sub-heading-5 text-body mb-0">الكل</p>
            </div>
            <div class="blog-filter-item" data-category="furnishing-tips">
                <p class="sub-heading-5 text-body mb-0">نصائح التأثيث</p>
            </div>
            <div class="blog-filter-item" data-category="offers-services">
                <p class="sub-heading-5 text-body mb-0">العروض والخدمات</p>
            </div>
            <div class="blog-filter-item" data-category="color-decoration">
                <p class="sub-heading-5 text-body mb-0">تنسيقات الألوان والديكور</p>
            </div>
            <div class="blog-filter-item" data-category="product-comparisons">
                <p class="sub-heading-5 text-body mb-0">مقارنات وتجارب المنتجات</p>
            </div>
        </div>

        <!-- Blog -->
        <div class="blog-grid">
            <!-- سيتم ملء هذه المنطقة ديناميكيًا via JavaScript -->
        </div>

        <!-- Loading Spinner -->
        <div class="text-center mt-4 d-none" id="loadingSpinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">جاري التحميل...</span>
            </div>
        </div>

        <!-- No Results Message -->
        <div class="text-center mt-4 d-none" id="noResults">
            <p class="body-2 text-caption">لا توجد منشورات متاحة في هذا التصنيف</p>
        </div>
    </div>
</section>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pages/blog.css') }}">
<style>
.spinner-border {
    width: 3rem;
    height: 3rem;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // عناصر DOM
    const blogGrid = document.querySelector('.blog-grid');
    const filterItems = document.querySelectorAll('.blog-filter-item');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const noResults = document.getElementById('noResults');

    // المنشورات الافتراضية (يمكن جلبها من الخادم)
    const samplePosts = [
        {
            id: 1,
            title: 'تجهيزات تكنولوجية لراحة الضيوف',
            excerpt: 'التكنولوجيا الحديثة تسهم في تحسين تجربة الإقامة. تعرف على أحدث التجهيزات التي يمكن إضافتها.',
            image: "{{ asset('assets/images/blog/blog-01.jpg') }}",
            category: 'نصائح التأثيث',
            date: '10 يونيو 2025',
            slug: 'tech-preparations-for-guest-comfort'
        },
        {
            id: 2,
            title: 'أفكار مبتكرة لديكور غرف النوم',
            excerpt: 'اكتشف أحدث الاتجاهات في ديكور غرف النوم الفندقية وكيفية تحقيق أقصى درجات الراحة للضيوف.',
            image: "{{ asset('assets/images/blog/blog-01.jpg') }}",
            category: 'تنسيقات الألوان والديكور',
            date: '8 يونيو 2025',
            slug: 'innovative-ideas-for-bedroom-decor'
        },
        {
            id: 3,
            title: 'عروض خاصة لتجهيز الشقق المفروشة',
            excerpt: 'استفد من العروض الحصرية لتجهيز شقتك المفروشة بأعلى جودة وأفضل الأسعار.',
            image: "{{ asset('assets/images/blog/blog-01.jpg') }}",
            category: 'العروض والخدمات',
            date: '5 يونيو 2025',
            slug: 'special-offers-for-furnished-apartments'
        },
        {
            id: 4,
            title: 'مقارنة بين أنواع الأسرّة الفندقية',
            excerpt: 'دليل مفصل للمساعدة في اختيار أفضل أنواع الأسرّة المناسبة للوحدات الفندقية والسكنية.',
            image: "{{ asset('assets/images/blog/blog-01.jpg') }}",
            category: 'مقارنات وتجارب المنتجات',
            date: '3 يونيو 2025',
            slug: 'comparison-between-hotel-bed-types'
        },
        {
            id: 5,
            title: 'نصائح لاختيار الأثاث المناسب للمساحات الصغيرة',
            excerpt: 'كيفية استغلال المساحات الصغيرة بشكل أمثل مع الحفاظ على الأناقة والراحة.',
            image: "{{ asset('assets/images/blog/blog-01.jpg') }}",
            category: 'نصائح التأثيث',
            date: '1 يونيو 2025',
            slug: 'tips-for-choosing-furniture-for-small-spaces'
        }
    ];

    // تحميل المنشورات الأولى
    loadPosts(samplePosts);

    // فلترة المدونة حسب التصنيف
    filterItems.forEach(item => {
        item.addEventListener('click', function() {
            // إزالة النشاط من جميع العناصر
            filterItems.forEach(i => i.classList.remove('active'));
            // إضافة النشاط للعنصر الحالي
            this.classList.add('active');

            // عرض مؤشر التحميل
            showLoading();

            // محاكاة جلب البيانات من الخادم
            setTimeout(() => {
                const category = this.getAttribute('data-category');
                if (category === 'all') {
                    loadPosts(samplePosts);
                } else {
                    const filteredPosts = samplePosts.filter(post =>
                        post.category === this.querySelector('p').textContent.trim()
                    );
                    loadPosts(filteredPosts);
                }
                hideLoading();
            }, 500);
        });
    });

    // وظيفة تحميل المنشورات
    function loadPosts(posts) {
        blogGrid.innerHTML = '';

        if (posts.length === 0) {
            noResults.classList.remove('d-none');
            return;
        }

        noResults.classList.add('d-none');

        posts.forEach(post => {
            const postElement = createPostElement(post);
            blogGrid.appendChild(postElement);
        });
    }

    // وظيفة إنشاء عنصر منشور
    function createPostElement(post) {
        const div = document.createElement('div');
        div.className = 'blog-item d-flex flex-column gap-sm-2';
        div.innerHTML = `
            <div class="blog-image">
                <div class="blog-widget">
                    <p class="body-4 text-white mb-0">${post.category}</p>
                </div>
                <img src="${post.image}" alt="${post.title}" />
            </div>
            <div class="blog-content d-flex flex-column gap-sm-5">
                <div class="blog-content-item">
                    <p class="body-4 text-secondary mb-0">تاريخ النشر: ${post.date}</p>
                </div>
                <div class="blog-content-item d-flex flex-column gap-sm-6">
                    <p class="sub-heading-3 mb-0">${post.title}</p>
                    <p class="caption-5 mb-0">${post.excerpt}</p>
                </div>
                <div class="blog-content-item">
                    <a href="{{ url('blog') }}/${post.id}" class="d-flex align-items-center gap-sm-5 cursor-pointer" style="height: 40px;">
                        <p class="sub-heading-5 mb-0">إقرأ المزيد</p>
                        <i class="fa-solid fa-arrow-left text-subheading"></i>
                    </a>
                </div>
            </div>
        `;
        return div;
    }

    // وظائف إظهار وإخفاء مؤشر التحميل
    function showLoading() {
        loadingSpinner.classList.remove('d-none');
    }

    function hideLoading() {
        loadingSpinner.classList.add('d-none');
    }
});
</script>
@endpush
