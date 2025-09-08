@extends('admin.layouts.app')

@section('title', 'تعديل التصميم')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">تعديل التصميم: {{ $design->name_ar }}</h1>
        <a href="{{ route('admin.designs.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> العودة
        </a>
    </div>

    <form action="{{ route('admin.designs.update', $design) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="name_ar" class="form-label">اسم التصميم (عربي)</label>
                    <input type="text" name="name_ar" id="name_ar" class="form-control" value="{{ old('name_ar', $design->name_ar) }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="name_en" class="form-label">اسم التصميم (إنجليزي)</label>
                    <input type="text" name="name_en" id="name_en" class="form-control" value="{{ old('name_en', $design->name_en) }}" required>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="description_ar" class="form-label">الوصف (عربي)</label>
                    <textarea name="description_ar" id="description_ar" class="form-control" rows="3">{{ old('description_ar', $design->description_ar) }}</textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="description_en" class="form-label">الوصف (إنجليزي)</label>
                    <textarea name="description_en" id="description_en" class="form-control" rows="3">{{ old('description_en', $design->description_en) }}</textarea>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="mb-3">
                    <label for="category" class="form-label">الفئة</label>
                    <select name="category" id="category" class="form-control">
                        <option value="">اختر الفئة</option>
                        <option value="bedroom" {{ $design->category == 'bedroom' ? 'selected' : '' }}>غرفة نوم</option>
                        <option value="living_room" {{ $design->category == 'living_room' ? 'selected' : '' }}>معيشة</option>
                        <option value="kitchen" {{ $design->category == 'kitchen' ? 'selected' : '' }}>مطبخ</option>
                        <option value="bathroom" {{ $design->category == 'bathroom' ? 'selected' : '' }}>حمام</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="mb-3">
                    <label for="image" class="form-label">صورة التصميم</label>
                    <input type="file" name="image" id="image" class="form-control" accept="image/*">
                </div>
                @if($design->image_path)
                    <div class="mb-3">
                        <label>الصورة الحالية</label>
                        <div>
                            <img src="{{ asset('storage/' . $design->image_path) }}" width="150" class="rounded">
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> تحديث التصميم
            </button>
            <a href="{{ route('admin.designs.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> إلغاء
            </a>
        </div>
    </form>
</div>
@endsection
