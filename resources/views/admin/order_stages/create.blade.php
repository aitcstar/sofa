@extends('admin.layouts.app')

@section('title', 'إضافة مرحلة جديدة')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">إضافة مرحلة جديدة</h1>
        <a href="{{ route('admin.order_stages.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i> رجوع
        </a>
    </div>

    <!-- Form Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">معلومات المرحلة</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.order_stages.store') }}" method="POST">
                @csrf

                <div class="col-md-6 mb-3">
                    <label class="form-label">تابعة لمرحلة (اختياري)</label>
                    <select name="parent_id" class="form-select">
                        <option value="">مرحلة رئيسية</option>
                        @foreach($stages as $stage)
                            <option value="{{ $stage->id }}">{{ $stage->title_ar }}</option>
                        @endforeach
                    </select>

                </div>


                <!-- Arabic Title -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">العنوان (عربي) <span class="text-danger">*</span></label>
                        <input type="text" name="title_ar" class="form-control" value="{{ old('title_ar') }}" placeholder="أدخل العنوان باللغة العربية" required>
                        @error('title_ar')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- English Title -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">العنوان (إنجليزي)</label>
                        <input type="text" name="title_en" class="form-control" value="{{ old('title_en') }}" placeholder="Enter the title in English">
                        @error('title_en')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Arabic Description -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الوصف (عربي)</label>
                        <textarea name="description_ar[]" class="form-control" rows="4" placeholder="يمكنك كتابة أكثر من سطر كوصف لكل خطوة">{{ old('description_ar.0') }}</textarea>
                        @error('description_ar')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- English Description -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الوصف (إنجليزي)</label>
                        <textarea name="description_en[]" class="form-control" rows="4" placeholder="You can write multiple lines as a description">{{ old('description_en.0') }}</textarea>
                        @error('description_en')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Order Number -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">ترتيب المرحلة <span class="text-danger">*</span></label>
                        <input type="number" name="order_number" class="form-control" value="{{ old('order_number') }}" placeholder="مثلاً: 1 للتصميم" required>
                        @error('order_number')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Actions -->
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> حفظ المرحلة
                    </button>
                    <a href="{{ route('admin.order_stages.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
