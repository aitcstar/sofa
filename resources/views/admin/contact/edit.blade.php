@extends('admin.layouts.app')

@section('title', 'إدارة صفحة اتصل بنا')

@section('content')
@php
$user = Auth::guard('admin')->user() ?? Auth::guard('employee')->user();
@endphp
<div class="container">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">إدارة صفحة اتصل بنا</h1>
    </div>


    <form action="{{ route('admin.seo.update') }}" method="POST">
        @csrf

            @php $seo = $seoSettings[$page] ?? null; @endphp
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                     إعدادات SEO
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

                    <div class="mb-3">
                        <label class="form-label">Index Status</label>
                        <select name="seo[{{ $page }}][index_status]" class="form-select">
                            <option value="index" {{ ($seo->index_status ?? '') == 'index' ? 'selected' : '' }}>Index</option>
                            <option value="noindex" {{ ($seo->index_status ?? '') == 'noindex' ? 'selected' : '' }}>No Index</option>
                        </select>
                    </div>
                </div>
            </div>
            @if($user && ($user->hasPermission('content.edit') || $user->role === 'admin'))
                <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
            @endif
    </form>
    <br>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">البيانات الأساسية</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.contact.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- العنوان -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">العنوان (AR)</label>
                        <input type="text" name="title_ar" class="form-control"
                               value="{{ old('title_ar', $section->title_ar ?? '') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Title (EN)</label>
                        <input type="text" name="title_en" class="form-control"
                               value="{{ old('title_en', $section->title_en ?? '') }}">
                    </div>

                    <!-- الوصف -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الوصف (AR)</label>
                        <textarea name="desc_ar" class="form-control" rows="3">{{ old('desc_ar', $section->desc_ar ?? '') }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Description (EN)</label>
                        <textarea name="desc_en" class="form-control" rows="3">{{ old('desc_en', $section->desc_en ?? '') }}</textarea>
                    </div>

                    <br>
                    <hr>
                    <label style="text-align: center;
                    font-weight: bold;
                    background-color: #ad996f;
                    color: white;
                    padding: 9px 6px;
                ">للتواصل</label>
                    <br>
                    <hr>
                    <br>
                     <!-- CTA Text -->
                     <div class="col-md-6 mb-3">
                        <label class="form-label">نص الـ CTA (AR)</label>
                        <textarea name="cta_text_ar" class="form-control" rows="3">{{ old('cta_text_ar', $section->cta_text_ar ?? '') }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">CTA Text (EN)</label>
                        <textarea name="cta_text_en" class="form-control" rows="3">{{ old('cta_text_en', $section->cta_text_en ?? '') }}</textarea>
                    </div>

                    <!-- المعرض الرئيسي -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">المعرض الرئيسي (AR)</label>
                        <input type="text" name="main_showroom_ar" class="form-control"
                               value="{{ old('main_showroom_ar', $section->main_showroom_ar ?? '') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Main Showroom (EN)</label>
                        <input type="text" name="main_showroom_en" class="form-control"
                               value="{{ old('main_showroom_en', $section->main_showroom_en ?? '') }}">
                    </div>

                    <!-- ساعات العمل -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">ساعات العمل (AR)</label>
                        <input type="text" name="work_hours_ar" class="form-control"
                               value="{{ old('work_hours_ar', $section->work_hours_ar ?? '') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Work Hours (EN)</label>
                        <input type="text" name="work_hours_en" class="form-control"
                               value="{{ old('work_hours_en', $section->work_hours_en ?? '') }}">
                    </div>


                    <br>
                    <hr>
                    <label style="text-align: center;
                    font-weight: bold;
                    background-color: #ad996f;
                    color: white;
                    padding: 9px 6px;
                ">الجزء الثاني</label>
                    <br>
                    <hr>
                    <br>

                    <!-- CTA Heading -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">عنوان الـ CTA (AR)</label>
                        <input type="text" name="cta_heading_ar" class="form-control"
                               value="{{ old('cta_heading_ar', $section->cta_heading_ar ?? '') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">CTA Heading (EN)</label>
                        <input type="text" name="cta_heading_en" class="form-control"
                               value="{{ old('cta_heading_en', $section->cta_heading_en ?? '') }}">
                    </div>


                    <br>
                    <hr>
                    <label style="text-align: center;
                    font-weight: bold;
                    background-color: #ad996f;
                    color: white;
                    padding: 9px 6px;
                "> العنوان </label>
                    <br>
                    <hr>
                    <br>
                    <!-- المدينة -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">المدينة (AR)</label>
                        <input type="text" name="city_ar" class="form-control"
                               value="{{ old('city_ar', $section->city_ar ?? '') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">City (EN)</label>
                        <input type="text" name="city_en" class="form-control"
                               value="{{ old('city_en', $section->city_en ?? '') }}">
                    </div>

                    <!-- العنوان -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">العنوان (AR)</label>
                        <textarea name="address_ar" class="form-control" rows="2">{{ old('address_ar', $section->address_ar ?? '') }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Address (EN)</label>
                        <textarea name="address_en" class="form-control" rows="2">{{ old('address_en', $section->address_en ?? '') }}</textarea>
                    </div>

                    <br>
                    <hr>
                    <label style="text-align: center;
                    font-weight: bold;
                    background-color: #ad996f;
                    color: white;
                    padding: 9px 6px;
                ">  الخريطه</label>
                    <br>
                    <hr>
                    <br>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">خط العرض</label>
                        <input type="text" name="lat" class="form-control"
                               value="{{ old('lat', $section->lat ?? '') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">خط الطول</label>
                        <input type="text" name="lng" class="form-control"
                               value="{{ old('lng', $section->lng ?? '') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">العنوان </label>
                        <input type="text" name="maptitle_ar" class="form-control"
                               value="{{ old('maptitle_ar', $section->maptitle_ar ?? '') }}">
                    </div>
                    <!--
                    <div class="col-md-6 mb-3">
                        <label class="form-label">العنوان (EN)</label>
                        <input type="text" name="maptitle_en" class="form-control"
                               value="{{ old('maptitle_en', $section->maptitle_en ?? '') }}">
                    </div>-->

                    <div class="col-md-6 mb-3">
                        <label class="form-label">الموقع </label>
                        <textarea name="mapaddress_ar" class="form-control" rows="2">{{ old('mapaddress_ar', $section->mapaddress_ar ?? '') }}</textarea>
                    </div>
                    <!--
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Address (EN)</label>
                        <textarea name="mapaddress_en" class="form-control" rows="2">{{ old('mapaddress_en', $section->mapaddress_en ?? '') }}</textarea>
                    </div>-->
                </div>

                <!-- أزرار الحفظ -->
                <div class="d-flex gap-2 mt-4">
                    @if($user && ($user->hasPermission('content.edit') || $user->role === 'admin'))
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> حفظ التعديلات
                        </button>
                    @endif
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
