@extends('admin.layouts.app')

@section('title', 'تعديل السلايدر')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">تعديل السلايدر</h1>
        <a href="{{ route('admin.hero-sliders.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i> رجوع
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">تعديل معلومات السلايدر</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.hero-sliders.update', $heroSlider->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الصورة الحالية</label>
                        @if($heroSlider->image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $heroSlider->image) }}" width="200" class="rounded object-fit-cover border">
                                <a href="{{ asset('storage/' . $heroSlider->image) }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        @endif
                        <label class="form-label">تغيير الصورة</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <div class="form-text">يُفضل صورة بحجم 1200x600 بكسل</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">الترتيب</label>
                        <input type="number" name="order" class="form-control" value="{{ old('order', $heroSlider->order) }}" min="0">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">العنوان بالعربي <span class="text-danger">*</span></label>
                        <input type="text" name="title_ar" class="form-control" value="{{ old('title_ar', $heroSlider->title_ar) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">العنوان بالإنجليزي <span class="text-danger">*</span></label>
                        <input type="text" name="title_en" class="form-control" value="{{ old('title_en', $heroSlider->title_en) }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الوصف بالعربي</label>
                        <textarea name="description_ar" class="form-control" rows="3">{{ old('description_ar', $heroSlider->description_ar) }}</textarea>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">الوصف بالإنجليزي</label>
                        <textarea name="description_en" class="form-control" rows="3">{{ old('description_en', $heroSlider->description_en) }}</textarea>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" {{ $heroSlider->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">مفعل</label>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> تحديث السلايدر
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

