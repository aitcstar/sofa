@extends('frontend.layouts.pages')

@section('title', $post->title . ' - SOFA Experience')
@section('description', $post->excerpt)

@section('content')

<div class="breadcrumb-container container">
    <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="body-2 text-body">{{ __('site.home') }}</a>
    <span class="body-2 text-body">/</span>
    <a href="{{ route('blog.index', ['locale' => app()->getLocale()]) }}" class="body-2 text-primary">{{ __('site.blog') }}</a>
    <span class="body-2 text-body">/</span>
    <span class="body-2 text-primary">{{ $post->title }}</span>
</div>

<section class="blog-details">
    <div class="container">
        <div class="blog-details-container">
            <!-- صورة المقال -->
            <div class="blog-details-image">
                <img src="{{ $post->image ? asset('storage/'.$post->image) : asset('assets/images/blog/blog-01.jpg') }}" alt="{{ $post->title }}">
            </div>

            <!-- تفاصيل المقال -->
            <div class="blog-details-details">
                <div class="blog-details-details-item">
                    <div class="d-flex gap-sm-4">
                        <div class="blog-details-tag d-flex gap-sm-6">
                            <img src="{{ asset('assets/images/icons/user.svg') }}" alt="user" />
                            <p class="mb-0" style="white-space: nowrap;">{{ $post->author }}</p>
                        </div>
                        <div class="blog-details-tag d-flex gap-sm-6">
                            <img src="{{ asset('assets/images/icons/book-mark.svg') }}" alt="tag" />
                            <p class="mb-0" style="white-space: nowrap;">{{ $post->category }}</p>
                        </div>
                    </div>
                    <p class="heading-h6 mb-0">{{ $post->title }}</p>
                    <div class="d-flex align-items-center gap-sm-6">
                        <img src="{{ asset('assets/images/icons/date.svg') }}" alt="date" />
                        <p class="mb-0">{{ __('site.published_at', ['date' => $post->created_at->format('d M Y')]) }}
                        </p>

                    </div>

                    <p class="body-2 mb-0 mt-3">{!! $post->content !!}</p>
                    {{ __('site.last_update', ['date' => $post->updated_at->format('d M Y')]) }}
                </div>

                <!-- CTA -->
                <div class="position-relative">
                    <div class="cta-button-container d-flex flex-column align-items-center gap-md">
                        <div class="cta-button-overlay"></div>
                        <div class="cta-button-heading d-flex flex-column gap-sm-3">
                            <h2 class="heading-h7 mb-0 text-white">{{ __('site.heading') }}</h2>
                            <p class="caption-5 mb-0 text-white" style="max-width: 592px; opacity: 0.8;">
                                {{ __('site.text') }}
                            </p>
                        </div>
                        <div class="cta-button-buttons d-flex gap-sm-3">
                            <button class="btn btn-custom-secondary">
                                <p class="mb-0">{{ __('site.whatsapp') }}</p>
                                <i class="fa-brands fa-whatsapp" style="font-size: 18px;"></i>
                            </button>
                            <button class="btn btn-custom-outline">
                                <p class="mb-0">{{ __('site.order_now') }}</p>
                                <i class="fa-solid fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} action-icon" style="font-size: 18px;"></i>
                            </button>
                        </div>
                    </div>
                </div>



                    <!-- divider -->
                    <hr class="divider" />

                    <!-- item 7 -->
                    <div class="blog-details-details-item">
                        <p class="sub-heading-3 mb-0">{{ __('site.faq_post', ['title' => $post->title]) }}</p>

                        <!-- accordion -->
                                    <!-- Accordion -->
                                    <div class="faq-section-content">
                                        <div class="accordion" id="accordionFaq">
                                            @foreach($faqs as $index => $faq)
                                            <div class="accordion-item">
                                                <div class="accordion-header">
                                                    <button class="accordion-button @if($index != 0) collapsed @endif" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}"
                                                        aria-expanded="{{ $index == 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $index }}">
                                                        <p class="sub-heading-3 mb-0">
                                                            {{ app()->getLocale() == 'ar' ? $faq->question_ar : $faq->question_en }}
                                                        </p>
                                                    </button>
                                                </div>
                                                <div id="collapse{{ $index }}" class="accordion-collapse collapse @if($index == 0) show @endif" aria-labelledby="heading{{ $index }}" data-bs-parent="#accordionFaq">
                                                    <div class="accordion-body">
                                                        <p class="body-2 text-body mb-0 tit">
                                                            {{ app()->getLocale() == 'ar' ? $faq->answer_ar : $faq->answer_en }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>

                    </div>

                    <!-- divider -->
                    <hr class="divider" />

                    <!-- Share -->
                    @php
                        $url   = urlencode(request()->fullUrl());
                        $title = urlencode($post->title);
                    @endphp
                    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

                    <div class="blog-details-share d-flex align-items-center gap-sm-3">
                        <p class="sub-heading-3 mb-0">شارك المقالة</p>

                        <div class="blog-details-share-icons d-flex gap-sm-4">
                            <!-- Twitter (X) -->
                            <a href="https://twitter.com/intent/tweet?url={{ $url }}&text={{ $title }}"
                            target="_blank" class="blog-details-share-icon" style="background-color: #363636;">
                                <i class="fa-brands fa-x-twitter" style="font-size: 18px;"></i>
                            </a>

                            <!-- LinkedIn -->
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $url }}"
                            target="_blank" class="blog-details-share-icon" style="background-color: #10658F;">
                                <i class="fa-brands fa-linkedin" style="font-size: 18px;"></i>
                            </a>

                            <!-- Telegram -->
                            <a href="https://t.me/share/url?url={{ $url }}&text={{ $title }}"
                            target="_blank" class="blog-details-share-icon" style="background-color: #23B7EC;">
                                <i class="fa-brands fa-telegram" style="font-size: 18px;"></i>
                            </a>

                            <!-- Email -->
                            <a href="mailto:?subject={{ $title }}&body={{ $url }}"
                            class="blog-details-share-icon" style="background-color: #000000;">
                                <i class="fa-solid fa-envelope" style="font-size: 18px;"></i>
                            </a>

                            <!-- WhatsApp -->
                            <a href="https://wa.me/?text={{ $title }}%20{{ $url }}"
                            target="_blank" class="blog-details-share-icon" style="background-color: #4CAF50;">
                                <i class="fa-brands fa-whatsapp" style="font-size: 18px;"></i>
                            </a>

                            <!-- Facebook -->
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ $url }}"
                            target="_blank" class="blog-details-share-icon" style="background-color: #1877F2;">
                                <i class="fa-brands fa-facebook" style="font-size: 18px;"></i>
                            </a>
                        </div>
                    </div>



                    <!-- divider -->
                    <hr class="divider" />

                    <!-- Item 8 -->
                    <div class="blog-details-details-item">
                        <p class="sub-heading-3 mb-0">شاركنا رأيك</p>
                        <p class="caption-5">نحن نهتم برأيك! اترك تعليقك حول المقال أو التجربة وسنكون سعداء بقراءته
                        </p>

                        <form action="" class="d-flex flex-column gap-sm-3">
                            <div class="d-flex gap-sm-3" style="width: 100%;">
                                <!-- name -->
                                <div class="form-group" style="flex: 0.5;">
                                    <input type="text" class="form-control" id="name" placeholder="الاسم الكامل" />
                                </div>

                                <!-- email -->
                                <div class="form-group" style="flex: 0.5;">
                                    <input type="email" class="form-control" id="email"
                                        placeholder="البريد الإلكتروني" />
                                </div>
                            </div>

                            <!-- message -->
                            <div class="form-group">
                                <textarea class="form-control" id="message" placeholder="اكتب تعليقك هنا..."
                                    rows="4"></textarea>
                            </div>

                            <!-- submit -->
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-custom-primary">إرسال التعليق</button>
                            </div>
                        </form>
                    </div>


            </div>
        </div>

        <!-- المقالات ذات صلة -->
        <div class="blog-details-related-posts d-flex flex-column gap-sm-3 mt-5">
            <h6 class="heading-h6 mb-0">مقالات ذات صلة</h6>
            <div class="blog-grid">
                @forelse($relatedPosts as $related)
                    <div class="blog-item d-flex flex-column gap-sm-2">
                        <div class="blog-image">
                            <div class="blog-widget">
                                <p class="body-4 text-white mb-0">{{ $related->category }}</p>
                            </div>
                            <img src="{{ $related->image ? asset('storage/'.$related->image) : asset('assets/images/blog/blog-01.jpg') }}" alt="{{ $related->title }}">
                        </div>
                        <div class="blog-content d-flex flex-column gap-sm-5">
                            <div class="blog-content-item">
                                <p class="body-4 text-secondary mb-0">تاريخ النشر: {{ $related->created_at->format('d M Y') }}</p>
                            </div>
                            <div class="blog-content-item d-flex flex-column gap-sm-6">
                                <p class="sub-heading-3 mb-0">{{ $related->title }}</p>
                                <p class="caption-5 mb-0">{{ $related->excerpt }}</p>
                            </div>
                            <div class="blog-content-item">
                                <A href="{{ route('blog.details', ['locale' => app()->getLocale(), $related->slug]) }}" class="d-flex align-items-center gap-sm-5 cursor-pointer" style="height: 40px;">
                                    <p class="sub-heading-5 mb-0">إقرأ المزيد</p>
                                    <i class="fa-solid fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} text-subheading"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="body-2 text-caption">لا توجد مقالات ذات صلة</p>
                @endforelse
            </div>
        </div>

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
