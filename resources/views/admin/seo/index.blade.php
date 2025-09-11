@extends('admin.layouts.app')

@section('title', 'إعدادات SEO')

@section('content')
<div class="container">
    <h1 class="h2 mb-4">إعدادات SEO للصفحات</h1>

    <form action="{{ route('admin.seo.update') }}" method="POST">
        @csrf
        @foreach($pages as $page)
            @php $seo = $seoSettings[$page] ?? null; @endphp
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    {{ __('site.' . ($page === 'gallery' ? 'mgallery' : $page)) }}
                </div>
                <div class="card-body">
                    {{-- العنوان --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Meta Title (AR)</label>
                            <input type="text" name="seo[{{ $page }}][meta_title_ar]" class="form-control"
                                   value="{{ old("seo.$page.meta_title_ar", $seo->meta_title_ar ?? '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Meta Title (EN)</label>
                            <input type="text" name="seo[{{ $page }}][meta_title_en]" class="form-control"
                                   value="{{ old("seo.$page.meta_title_en", $seo->meta_title_en ?? '') }}">
                        </div>
                    </div>

                    {{-- الوصف --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Meta Description (AR)</label>
                            <textarea name="seo[{{ $page }}][meta_description_ar]" class="form-control">{{ old("seo.$page.meta_description_ar", $seo->meta_description_ar ?? '') }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Meta Description (EN)</label>
                            <textarea name="seo[{{ $page }}][meta_description_en]" class="form-control">{{ old("seo.$page.meta_description_en", $seo->meta_description_en ?? '') }}</textarea>
                        </div>
                    </div>

                    {{-- Slug --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Slug (AR)</label>
                            <input type="text" name="seo[{{ $page }}][slug_ar]" class="form-control"
                                   value="{{ old("seo.$page.slug_ar", $seo->slug_ar ?? '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Slug (EN)</label>
                            <input type="text" name="seo[{{ $page }}][slug_en]" class="form-control"
                                   value="{{ old("seo.$page.slug_en", $seo->slug_en ?? '') }}">
                        </div>
                    </div>

                    {{-- Canonical
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Canonical (AR)</label>
                            <input type="text" name="seo[{{ $page }}][canonical_ar]" class="form-control"
                                   value="{{ old("seo.$page.canonical_ar", $seo->canonical_ar ?? '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Canonical (EN)</label>
                            <input type="text" name="seo[{{ $page }}][canonical_en]" class="form-control"
                                   value="{{ old("seo.$page.canonical_en", $seo->canonical_en ?? '') }}">
                        </div>
                    </div>--}}

                    {{-- Index Status --}}
                    <div class="mb-3">
                        <label class="form-label">Index Status</label>
                        <select name="seo[{{ $page }}][index_status]" class="form-select">
                            <option value="index" {{ ($seo->index_status ?? '') == 'index' ? 'selected' : '' }}>Index</option>
                            <option value="noindex" {{ ($seo->index_status ?? '') == 'noindex' ? 'selected' : '' }}>No Index</option>
                        </select>
                    </div>
                </div>
            </div>
        @endforeach

        <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
    </form>
</div>
@endsection
