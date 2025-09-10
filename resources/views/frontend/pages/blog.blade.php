@extends('frontend.layouts.pages')

@section('title', 'المدونة - SOFA Experience')
@section('description', 'استكشف نصائح وأفكار تصميم، وتعرف على أسرار تجهيز الوحدات السكنية بأعلى كفاءة وجودة')

@section('content')
<!-- ===== BREADCRUMB ===== -->
<div class="breadcrumb-container container">
    <a href="{{ route('home',['locale' => app()->getLocale()]) }}" class="body-2 text-body">{{ __('site.home') }}</a>
    <span class="body-2 text-body">/</span>
    <a href="{{ route('blog.index',['locale' => app()->getLocale()]) }}" class="body-2 text-primary">{{ __('site.blog') }}</a>
</div>


<!-- ===== BLOG SECTION ===== -->
<section class="blog-section">
    <div class="container d-flex flex-column gap-md">
        <!-- Heading -->
        <div class="blog-heading d-flex flex-column gap-sm-5 align-items-center">
            <h2 class="heading-h7 text-heading">{{ __('site.heading') }}</h2>
            <p class="body-2 text-caption mb-0">{{ __('site.subtitle') }}</p>
        </div>

        <!-- Filter (Tabs) -->

        <div class="blog-filter">
            <div class="blog-filter-item {{ request('category') ? '' : 'active' }}">
                <a href="{{ route('blog.index', ['locale' => app()->getLocale()]) }}" class="sub-heading-5 text-body mb-0">{{ __('site.all') }}</a>
            </div>
            <div class="blog-filter-item {{ request('category') == __('site.tips') ? 'active' : '' }}">
                <a href="{{ route('blog.index', ['locale' => app()->getLocale(), 'category' => __('site.tips')]) }}" class="sub-heading-5 text-body mb-0">{{ __('site.tips') }}</a>
            </div>
            <div class="blog-filter-item {{ request('category') == __('site.offers') ? 'active' : '' }}">
                <a href="{{ route('blog.index', ['locale' => app()->getLocale(), 'category' => __('site.offers')]) }}" class="sub-heading-5 text-body mb-0">{{ __('site.offers') }}</a>
            </div>
            <div class="blog-filter-item {{ request('category') == __('site.designs') ? 'active' : '' }}">
                <a href="{{ route('blog.index', ['locale' => app()->getLocale(), 'category' => __('site.designs')]) }}" class="sub-heading-5 text-body mb-0">{{ __('site.designs') }}</a>
            </div>
            <div class="blog-filter-item {{ request('category') == __('site.reviews') ? 'active' : '' }}">
                <a href="{{ route('blog.index', ['locale' => app()->getLocale(), 'category' => __('site.reviews')]) }}" class="sub-heading-5 text-body mb-0">{{ __('site.reviews') }}</a>
            </div>
        </div>

        <!-- Blog Grid -->
        <div class="blog-grid">
            @forelse($blogs as $post)
            <div class="blog-item d-flex flex-column gap-sm-2">
                <div class="blog-image">
                    <div class="blog-widget">
                        <p class="body-4 text-white mb-0">{{ $post->category }}</p>
                    </div>
                    <img src="{{ $post->image ? asset('storage/'.$post->image) : asset('assets/images/blog/blog-01.jpg') }}" alt="{{ $post->title }}">
                </div>
                <div class="blog-content d-flex flex-column gap-sm-5">
                    <div class="blog-content-item">
                        <p class="body-4 text-secondary mb-0">
                            {{ __('site.published_at', ['date' => $post->created_at->format('d M Y')]) }}
                        </p>
                    </div>
                    <div class="blog-content-item d-flex flex-column gap-sm-6">
                        <p class="sub-heading-3 mb-0">{{ $post->title }}</p>
                        <p class="caption-5 mb-0">{{ $post->excerpt }}</p>
                    </div>
                    <div class="blog-content-item">
                        <a href="{{ route('blog.details' ,['locale' => app()->getLocale(), $post->slug]) }}" class="d-flex align-items-center gap-sm-5 cursor-pointer" style="height: 40px;">
                            <p class="sub-heading-5 mb-0">{{ __('site.read_more') }}</p>
                            <i class="fa-solid fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} text-subheading"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center mt-4">
                <p class="body-2 text-caption">{{ __('site.no_posts') }}</p>
            </div>
        @endforelse

        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $blogs->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
</section>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pages/blog.css') }}">
@endpush
