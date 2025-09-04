@extends('admin.layouts.app')

@section('title', 'إضافة سلايدر جديد')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">إضافة سلايدر جديد</h1>
        <a href="{{ route('admin.hero-sliders.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i> رجوع
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">معلومات السلايدر</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.hero-sliders.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الصورة <span class="text-danger">*</span></label>
                        <input type="file" name="image" class="form-control" accept="image/*" required>
                        <div class="form-text">يُفضل صورة بحجم 1200x600 بكسل</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">الترتيب</label>
                        <input type="number" name="order" class="form-control" value="0" min="0">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">العنوان بالعربي <span class="text-danger">*</span></label>
                        <input type="text" name="title_ar" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">العنوان بالإنجليزي <span class="text-danger">*</span></label>
                        <input type="text" name="title_en" class="form-control" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الوصف بالعربي</label>
                        <textarea name="description_ar" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">الوصف بالإنجليزي</label>
                        <textarea name="description_en" class="form-control" rows="3"></textarea>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked>
                        <label class="form-check-label" for="is_active">مفعل</label>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> حفظ السلايدر
                    </button>
                    <a href="{{ route('admin.hero-sliders.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

