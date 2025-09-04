@extends('frontend.layouts.pages')

@section('title', 'تفاصيل المقال - SOFA Experience')
@section('description', 'استكشف نصائح وأفكار تصميم، وتعرف على أسرار تجهيز الوحدات السكنية بأعلى كفاءة وجودة')

@section('content')
<!-- ===== BREADCRUMB ===== -->
<div class="breadcrumb-container container">
    <a href="{{ route('home') }}" class="body-2 text-body">الرئيسية</a>
    <span class="body-2 text-body">/</span>
    <a href="{{ route('blog.index') }}" class="body-2 text-body">المدونة</a>
    <span class="body-2 text-body">/</span>
    <a href="#" class="body-2 text-primary">تفاصيل المقال</a>
</div>

<!-- ===== BLOG DETAILS SECTION ===== -->
<section class="blog-details">
    <div class="container">
        <div class="blog-details-container">
            <!-- سيتم ملء هذه المنطقة ديناميكيًا via JavaScript -->
            <div id="blogDetailsContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">جاري التحميل...</span>
                    </div>
                    <p class="mt-3 body-2 text-caption">جاري تحميل المحتوى...</p>
                </div>
            </div>
        </div>

        <!-- Related Posts -->
        <div class="blog-details-related-posts d-flex flex-column gap-sm-3 mt-5">
            <h6 class="heading-h6 mb-0">مقالات ذات صلة</h6>
            <div class="blog-grid" id="relatedPosts">
                <!-- سيتم ملء هذه المنطقة ديناميكيًا via JavaScript -->
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pages/blog-details.css') }}">
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
    // بيانات المقالات (ستأتي من قاعدة البيانات في الواقع)
    const blogPosts = [
        {
            id: 1,
            title: 'تجهيزات تكنولوجية لراحة الضيوف',
            excerpt: 'التكنولوجيا الحديثة تسهم في تحسين تجربة الإقامة. تعرف على أحدث التجهيزات التي يمكن إضافتها.',
            content: [
                {
                    type: 'paragraph',
                    text: 'في عالم الضيافة الحديث، أصبحت التكنولوجيا جزءًا لا يتجزأ من تجربة النزيل، ووسيلة لتعزيز الراحة ورفع مستوى التميز في الخدمة. لم تعد التجهيزات التكنولوجية رفاهية، بل ضرورة تسهم في تقديم إقامة متكاملة تليق بتوقعات الضيوف العصريين.'
                },
                {
                    type: 'heading',
                    text: 'إضاءة ذكية ومزاجية'
                },
                {
                    type: 'paragraph',
                    text: 'من أبرز التقنيات التي تضيف لمسة مميزة للوحدة الفندقية هي أنظمة الإضاءة الذكية، التي يمكن التحكم فيها عبر الهاتف أو الأوامر الصوتية. تسمح للنزيل بتعديل درجة الإضاءة أو اختيار أنماط مختلفة (هادئة، للقراءة، رومانسية... إلخ) بما يتناسب مع حالته المزاجية.'
                },
                {
                    type: 'heading',
                    text: 'مفاتيح ذكية وأجهزة تحكم مركزية'
                },
                {
                    type: 'paragraph',
                    text: 'توفر أنظمة التحكم الذكي سهولة تشغيل جميع التجهيزات من مكان واحد: الإضاءة، الستائر، التكييف، وحتى التلفاز. هذا النوع من الراحة التلقائية يترك انطباعًا عالي المستوى عن الاهتمام بتفاصيل إقامة الضيف.'
                },
                {
                    type: 'heading',
                    text: 'أنظمة صوت مدمجة'
                },
                {
                    type: 'paragraph',
                    text: 'تُعد أنظمة الصوت المدمجة داخل الأثاث أو الأسقف إضافة فاخرة تضيف شعورًا منزليًا راقيًا. يمكن للضيف تشغيل موسيقاه المفضلة بجودة عالية من خلال ربط جهازه بسهولة عبر البلوتوث.'
                },
                {
                    type: 'heading',
                    text: 'أثاث يدعم التقنية'
                },
                {
                    type: 'paragraph',
                    text: 'حتى قطع الأثاث أصبحت ذكية! فمثلاً، الأرائك المزوّدة بمخارج USB أو الشواحن اللاسلكية المدمجة تتيح للضيوف استخدام أجهزتهم دون عناء.'
                },
                {
                    type: 'heading',
                    text: 'حلول رقمية داخل الغرف'
                },
                {
                    type: 'paragraph',
                    text: 'مثل الشاشات التفاعلية التي تعرض معلومات الفندق والخدمات، أو أجهزة اللوحية التي تتيح طلب الطعام أو خدمة الغرف بنقرة واحدة، كل ذلك يُترجم إلى تجربة ضيافة سلسة وفعالة.'
                }
            ],
            image: "{{ asset('assets/images/blog/blog-01.jpg') }}",
            category: 'تنسيقات الألوان والديكور',
            author: 'مصطفي خالد',
            publishDate: '29 يونيو , 2025 , 10:45 ص',
            updatedDate: '2 يوليو 2025',
            faqs: [
                {
                    question: 'كم تستغرق مدة التوصيل؟',
                    answer: 'نعم، جميع أسعار الباكجات تشمل خدمة التركيب الكامل في الموقع المحدد.'
                },
                {
                    question: 'هل تشمل الأسعار التركيب؟',
                    answer: 'نعم، جميع أسعار الباكجات تشمل خدمة التركيب الكامل في الموقع المحدد.'
                },
                {
                    question: 'هل يمكن جدولة موعد الشحن حسب رغبة العميل؟',
                    answer: 'نعم، جميع أسعار الباكجات تشمل خدمة التركيب الكامل في الموقع المحدد.'
                },
                {
                    question: 'هل توفرون التوصيل لجميع مدن المملكة؟',
                    answer: 'نعم، جميع أسعار الباكجات تشمل خدمة التركيب الكامل في الموقع المحدد.'
                },
                {
                    question: 'هل يتم التواصل مع العميل قبل موعد التوصيل؟',
                    answer: 'نعم، جميع أسعار الباكجات تشمل خدمة التركيب الكامل في الموقع المحدد.'
                },
                {
                    question: 'ماذا يحدث إذا لم أكن متواجدًا أثناء التوصيل؟',
                    answer: 'نعم، جميع أسعار الباكجات تشمل خدمة التركيب الكامل في الموقع المحدد.'
                },
                {
                    question: 'هل يمكن تغيير عنوان التوصيل بعد تأكيد الطلب؟',
                    answer: 'نعم، جميع أسعار الباكجات تشمل خدمة التركيب الكامل في الموقع المحدد.'
                },
                {
                    question: 'كم مدة التركيب في الموقع؟',
                    answer: 'نعم، جميع أسعار الباكجات تشمل خدمة التركيب الكامل في الموقع المحدد.'
                }
            ]
        },
        {
            id: 2,
            title: 'أفكار مبتكرة لديكور غرف النوم',
            excerpt: 'اكتشف أحدث الاتجاهات في ديكور غرف النوم الفندقية وكيفية تحقيق أقصى درجات الراحة للضيوف.',
            image: "{{ asset('assets/images/blog/blog-01.jpg') }}",
            category: 'تنسيقات الألوان والديكور',
            author: 'أحمد محمد',
            publishDate: '8 يونيو 2025',
            updatedDate: '15 يونيو 2025'
        },
        {
            id: 3,
            title: 'عروض خاصة لتجهيز الشقق المفروشة',
            excerpt: 'استفد من العروض الحصرية لتجهيز شقتك المفروشة بأعلى جودة وأفضل الأسعار.',
            image: "{{ asset('assets/images/blog/blog-01.jpg') }}",
            category: 'العروض والخدمات',
            author: 'سارة عبدالله',
            publishDate: '5 يونيو 2025',
            updatedDate: '12 يونيو 2025'
        },
        {
            id: 4,
            title: 'مقارنة بين أنواع الأسرّة الفندقية',
            excerpt: 'دليل مفصل للمساعدة في اختيار أفضل أنواع الأسرّة المناسبة للوحدات الفندقية والسكنية.',
            image: "{{ asset('assets/images/blog/blog-01.jpg') }}",
            category: 'مقارنات وتجارب المنتجات',
            author: 'خالد إبراهيم',
            publishDate: '3 يونيو 2025',
            updatedDate: '10 يونيو 2025'
        }
    ];

    // الحصول على معرف المقال من URL
    const postId = {{ $id }};

    // البحث عن المقال الحالي
    const currentPost = blogPosts.find(post => post.id == postId);

    // إذا وجد المقال، عرضه
    if (currentPost) {
        loadPostDetails(currentPost);
        loadRelatedPosts(currentPost);
    } else {
        document.getElementById('blogDetailsContent').innerHTML = `
            <div class="text-center py-5">
                <p class="body-2 text-caption">المقال غير موجود</p>
                <a href="{{ route('blog.index') }}" class="btn btn-custom-primary mt-3">العودة إلى المدونة</a>
            </div>
        `;
    }

    // وظيفة تحميل تفاصيل المقال
    function loadPostDetails(post) {
        const blogDetailsContent = document.getElementById('blogDetailsContent');

        let contentHTML = `
            <!-- image -->
            <div class="blog-details-image">
                <img src="${post.image}" alt="${post.title}" />
            </div>

            <!-- details -->
            <div class="blog-details-details">
                <!-- item 1 -->
                <div class="blog-details-details-item">
                    <div class="d-flex gap-sm-4">
                        <div class="blog-details-tag d-flex gap-sm-6">
                            <img src="{{ asset('assets/images/icons/user.svg') }}" alt="user" />
                            <p class="mb-0" style="white-space: nowrap;">${post.author}</p>
                        </div>
                        <div class="blog-details-tag d-flex gap-sm-6">
                            <img src="{{ asset('assets/images/icons/book-mark.svg') }}" alt="tag" />
                            <p class="mb-0" style="white-space: nowrap;">${post.category}</p>
                        </div>
                    </div>
                    <p class="heading-h6 mb-0">${post.title}</p>
                    <div class="d-flex align-items-center gap-sm-6">
                        <img src="{{ asset('assets/images/icons/date.svg') }}" alt="date" />
                        <p class="mb-0">تاريخ النشر ${post.publishDate}</p>
                    </div>
        `;

        // إضافة المحتوى
        if (post.content) {
            post.content.forEach(item => {
                if (item.type === 'paragraph') {
                    contentHTML += `<p class="body-2 mb-0">${item.text}</p>`;
                } else if (item.type === 'heading') {
                    contentHTML += `<p class="sub-heading-3 mb-0 mt-4">${item.text}</p>`;
                }
            });
        }

        contentHTML += `
                    <p class="body-3 mb-0 mt-3">آخر تحديث: ${post.updatedDate}</p>
                </div>
        `;

        // إضافة CTA
        contentHTML += `
                <!-- cta button -->
                <div class="position-relative">
                    <div class="cta-button-container d-flex flex-column align-items-center gap-md">
                        <!-- overlay -->
                        <div class="cta-button-overlay"></div>

                        <!-- heading -->
                        <div class="cta-button-heading d-flex flex-column gap-sm-3">
                            <h2 class="heading-h7 mb-0 text-white">هل ترغب بتجهيز وحدتك الفندقية بأناقة وبأسرع وقت؟
                            </h2>
                            <p class="caption-5 mb-0 text-white" style="max-width: 592px; opacity: 0.8;">
                                في SOFA، نوفّر لك باكجات جاهزة بتصاميم مدروسة تناسب مختلف أنماط التشطيب. اختصر الوقت
                                والجهد، ودعنا نهتم
                                بالتفاصيل من التصميم حتى التسليم. </p>
                        </div>

                        <!-- buttons -->
                        <div class="cta-button-buttons d-flex gap-sm-3">
                            <button class="btn btn-custom-secondary">
                                <p class="mb-0">تحدث معنا عبر واتساب</p>
                                <i class="fa-brands fa-whatsapp" style="font-size: 18px;"></i>
                            </button>
                            <button class="btn btn-custom-outline">
                                <p class="mb-0">اطلب الان</p>
                                <i class="fa-solid fa-arrow-left" style="font-size: 18px;"></i>
                            </button>
                        </div>
                    </div>
                </div>
        `;

        // إضافة الأسئلة الشائعة إذا كانت موجودة
        if (post.faqs && post.faqs.length > 0) {
            contentHTML += `
                <!-- divider -->
                <hr class="divider" />

                <!-- item 7 -->
                <div class="blog-details-details-item">
                    <p class="sub-heading-3 mb-0">الأسئلة الشائعة الخاصة بمقال ${post.title}</p>

                    <!-- accordion -->
                    <div class="accordion" id="faqAccordion">
            `;

            post.faqs.forEach((faq, index) => {
                const isFirst = index === 0;
                contentHTML += `
                        <!-- Item ${index + 1} -->
                        <div class="accordion-item">
                            <div class="accordion-header">
                                <button class="accordion-button ${isFirst ? '' : 'collapsed'}" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse${index}" aria-expanded="${isFirst ? 'true' : 'false'}" aria-controls="collapse${index}">
                                    <p class="sub-heading-3 mb-0">${faq.question}</p>
                                </button>
                            </div>
                            <div id="collapse${index}" class="accordion-collapse collapse ${isFirst ? 'show' : ''}" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p class="body-2 text-body mb-0">${faq.answer}</p>
                                </div>
                            </div>
                        </div>
                `;
            });

            contentHTML += `
                    </div>
                </div>
            `;
        }

        // إضافة مشاركة المقال والتعليقات
        contentHTML += `
                <!-- divider -->
                <hr class="divider" />

                <!-- Share -->
                <div class="blog-details-share d-flex align-items-center gap-sm-3">
                    <p class="sub-heading-3 mb-0">شارك المقالة</p>

                    <div class="blog-details-share-icons d-flex gap-sm-4">
                        <div class="blog-details-share-icon" style="background-color: #363636;">
                            <i class="fa-brands fa-x-twitter" style="font-size: 18px;"></i>
                        </div>
                        <div class="blog-details-share-icon" style="background-color: #10658F;">
                            <i class="fa-brands fa-linkedin" style="font-size: 18px;"></i>
                        </div>

                        <div class="blog-details-share-icon" style="background-color: #23B7EC;">
                            <i class="fa-brands fa-telegram" style="font-size: 18px;"></i>
                        </div>

                        <div class="blog-details-share-icon" style="background-color: #000000;">
                            <i class="fa-solid fa-envelope" style="font-size: 18px;"></i>
                        </div>

                        <div class="blog-details-share-icon" style="background-color: #4CAF50;">
                            <i class="fa-brands fa-whatsapp" style="font-size: 18px;"></i>
                        </div>

                        <div class="blog-details-share-icon" style="background-color: #1877F2;">
                            <i class="fa-brands fa-facebook" style="font-size: 18px;"></i>
                        </div>
                    </div>
                </div>

                <!-- divider -->
                <hr class="divider" />

                <!-- Item 8 -->
                <div class="blog-details-details-item">
                    <p class="sub-heading-3 mb-0">شاركنا رأيك</p>
                    <p class="caption-5">نحن نهتم برأيك! اترك تعليقك حول المقال أو التجربة وسنكون سعداء بقراءته
                    </p>

                    <form action="#" method="POST" class="d-flex flex-column gap-sm-3" id="commentForm">
                        <div class="d-flex gap-sm-3" style="width: 100%;">
                            <!-- name -->
                            <div class="form-group" style="flex: 0.5;">
                                <input type="text" class="form-control" id="name" name="name" placeholder="الاسم الكامل" required />
                            </div>

                            <!-- email -->
                            <div class="form-group" style="flex: 0.5;">
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="البريد الإلكتروني" required />
                            </div>
                        </div>

                        <!-- message -->
                        <div class="form-group">
                            <textarea class="form-control" id="message" name="message" placeholder="اكتب تعليقك هنا..."
                                rows="4" required></textarea>
                        </div>

                        <!-- submit -->
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-custom-primary">إرسال التعليق</button>
                        </div>
                    </form>
                </div>
            </div>
        `;

        blogDetailsContent.innerHTML = contentHTML;

        // إضافة event listeners للمشاركة والتعليقات
        addEventListeners();
    }

    // وظيفة تحميل المقالات ذات الصلة
    function loadRelatedPosts(currentPost) {
        const relatedPostsContainer = document.getElementById('relatedPosts');
        const relatedPosts = blogPosts.filter(post => post.id !== currentPost.id && post.category === currentPost.category);

        if (relatedPosts.length === 0) {
            relatedPostsContainer.innerHTML = '<p class="body-2 text-caption">لا توجد مقالات ذات صلة</p>';
            return;
        }

        let relatedHTML = '';

        relatedPosts.slice(0, 3).forEach(post => {
            relatedHTML += `
                <div class="blog-item d-flex flex-column gap-sm-2">
                    <!-- image & widget -->
                    <div class="blog-image">
                        <div class="blog-widget">
                            <p class="body-4 text-white mb-0">${post.category}</p>
                        </div>
                        <img src="${post.image}" alt="${post.title}" />
                    </div>

                    <!-- content -->
                    <div class="blog-content d-flex flex-column gap-sm-5">
                        <!-- content item -->
                        <div class="blog-content-item">
                            <p class="body-4 text-secondary mb-0">تاريخ النشر: ${post.publishDate}</p>
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
                </div>
            `;
        });

        relatedPostsContainer.innerHTML = relatedHTML;
    }

    // وظيفة إضافة event listeners
    function addEventListeners() {
        // مشاركة المقال على وسائل التواصل الاجتماعي
        const shareIcons = document.querySelectorAll('.blog-details-share-icon');

        shareIcons.forEach(icon => {
            icon.addEventListener('click', function() {
                const platform = this.querySelector('i').classList[1];
                shareOnPlatform(platform);
            });
        });

        // إرسال التعليق
        const commentForm = document.getElementById('commentForm');
        if (commentForm) {
            commentForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // هنا يمكن إضافة التحقق من صحة البيانات وإرسالها
                alert('شكراً لك على تعليقك! سيتم مراجعته قبل النشر.');
                this.reset();
            });
        }
    }

    // وظيفة المشاركة على وسائل التواصل الاجتماعي
    function shareOnPlatform(platform) {
        const title = document.querySelector('.heading-h6').textContent;
        const url = window.location.href;

        let shareUrl = '';

        switch(platform) {
            case 'fa-x-twitter':
                shareUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(title)}&url=${encodeURIComponent(url)}`;
                break;
            case 'fa-linkedin':
                shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`;
                break;
            case 'fa-telegram':
                shareUrl = `https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`;
                break;
            case 'fa-envelope':
                shareUrl = `mailto:?subject=${encodeURIComponent(title)}&body=${encodeURIComponent(url)}`;
                break;
            case 'fa-whatsapp':
                shareUrl = `https://wa.me/?text=${encodeURIComponent(title + ' ' + url)}`;
                break;
            case 'fa-facebook':
                shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
                break;
        }

        if (shareUrl) {
            window.open(shareUrl, '_blank');
        }
    }
});
</script>
@endpush
