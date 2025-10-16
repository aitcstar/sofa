@extends('admin.layouts.app')

@section('title', 'تعديل المرحلة')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">تعديل المرحلة</h1>
        <a href="{{ route('admin.order_stages.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i> رجوع
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">تعديل بيانات المرحلة</h5>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.order_stages.update', $orderStage->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- العنوان --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">العنوان (عربي) <span class="text-danger">*</span></label>
                        <input type="text" name="title_ar" class="form-control"
                               value="{{ old('title_ar', $orderStage->title_ar) }}" required>
                        @error('title_ar')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">العنوان (إنجليزي)</label>
                        <input type="text" name="title_en" class="form-control"
                               value="{{ old('title_en', $orderStage->title_en) }}">
                        @error('title_en')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                {{-- الوصف --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الوصف (عربي)</label>
                        <textarea name="description_ar[]" class="form-control" rows="4">{{ implode("\n", $orderStage->description_ar ?? []) }}</textarea>
                        <small class="text-muted">أدخل كل سطر كوصف منفصل.</small>
                        @error('description_ar')
                            <small class="text-danger d-block">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">الوصف (إنجليزي)</label>
                        <textarea name="description_en[]" class="form-control" rows="4">{{ implode("\n", $orderStage->description_en ?? []) }}</textarea>
                        <small class="text-muted">Each line will be treated as a separate point.</small>
                        @error('description_en')
                            <small class="text-danger d-block">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                {{-- ترتيب المرحلة --}}
                <div class="col-md-4 mb-3">
                    <label class="form-label">ترتيب المرحلة <span class="text-danger">*</span></label>
                    <input type="number" name="order_number" class="form-control"
                           value="{{ old('order_number', $orderStage->order_number) }}" required>
                    @error('order_number')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- الأزرار --}}
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> تحديث المرحلة
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
