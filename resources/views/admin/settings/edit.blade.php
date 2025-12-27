@extends('admin.layouts.app')

@section('title', 'إعدادات الموقع')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">إعدادات الموقع</h1>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf

        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-primary text-white">معلومات أساسية</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">اسم الموقع</label>
                    <input type="text" name="site_name" class="form-control" value="{{ old('site_name', $setting->site_name ?? '') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $setting->email ?? '') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">رقم الهاتف</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $setting->phone ?? '') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">رقم الواتساب</label>
                    <input type="text" name="whatsapp" class="form-control" value="{{ old('whatsapp', $setting->whatsapp ?? '') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">موقع المعرض الرئيسي</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address', $setting->address ?? '') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">ساعات العمل</label>
                    <input type="text" name="worktime" class="form-control" value="{{ old('worktime', $setting->worktime ?? '') }}">
                </div>


            </div>

            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-primary text-white">إعدادات الطلب</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">أقل عدد متاح من الوحدات في الطلب</label>
                        <input type="number" min="1" name="min_units" class="form-control"
                            value="{{ old('min_units', $setting->min_units ?? '') }}">
                    </div>
                </div>
            </div>


        </div>

        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-primary text-white">روابط السوشيال ميديا</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Snapchat</label>
                    <input type="url" name="snapchat" class="form-control" value="{{ old('snapchat', $setting->snapchat ?? '') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Tiktok</label>
                    <input type="url" name="tiktok" class="form-control" value="{{ old('tiktok', $setting->tiktok ?? '') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Instagram</label>
                    <input type="url" name="instagram" class="form-control" value="{{ old('instagram', $setting->instagram ?? '') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">YouTube</label>
                    <input type="url" name="youtube" class="form-control" value="{{ old('youtube', $setting->youtube ?? '') }}">
                </div>
            </div>
        </div>




        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-primary text-white">إعدادات SEO العامه</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Meta Title (AR)</label>
                    <input type="text" name="seo_title" class="form-control" value="{{ old('seo_title', $setting->seo_title ?? '') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Meta Description (AR)</label>
                    <textarea name="seo_description" class="form-control" rows="3">{{ old('seo_description', $setting->seo_description ?? '') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Meta Title (EN)</label>
                    <input type="text" name="seo_title_en" class="form-control" value="{{ old('seo_title_en', $setting->seo_title_en ?? '') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Meta Description (EN)</label>
                    <textarea name="seo_description_en" class="form-control" rows="3">{{ old('seo_description_en', $setting->seo_description_en ?? '') }}</textarea>
                </div>
            {{--<div class="mb-3">
                    <label class="form-label">Meta Keywords</label>
                    <textarea name="seo_keywords" class="form-control" rows="3">{{ old('seo_keywords', $setting->seo_keywords ?? '') }}</textarea>
                </div>--}}
            </div>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i> حفظ الإعدادات
        </button>
    </form>



    <!-- Form أكواد الهيدر -->
<div class="card mb-4 border-0 shadow-sm mt-4">
    <div class="card-header bg-primary text-white">أكواد الهيدر</div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.header-scripts.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">عنوان الكود</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">الكود</label>
                <textarea name="script" class="form-control" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-success">حفظ الكود</button>
        </form>

        <!-- الأكواد الموجودة -->
        <div class="mt-4">
            <h5>الأكواد الموجودة</h5>
            <ul>
                @foreach($scripts as $script)
                    <li>
                        {{ $script->title }}
                        <form action="{{ route('admin.header-scripts.destroy', $script->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">حذف</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

</div>

@endsection
