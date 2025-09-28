@extends('admin.layouts.app')

@section('title', 'تعديل القسم')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">تعديل القسم</h1>
        <a href="{{ route('admin.exhibition-categories.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i> رجوع
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">تفاصيل القسم</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.exhibition-categories.update', $exhibitionCategory->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الاسم (AR) <span class="text-danger">*</span></label>
                        <input type="text" name="name_ar" class="form-control"
                               value="{{ old('name_ar', $exhibitionCategory->name_ar) }}" required>
                        @error('name_ar') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الاسم (EN) <span class="text-danger">*</span></label>
                        <input type="text" name="name_en" class="form-control"
                               value="{{ old('name_en', $exhibitionCategory->name_en) }}" required>
                        @error('name_en') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Slug (AR) <span class="text-danger">*</span></label>
                        <input type="text" name="slug_ar" class="form-control"
                               value="{{ old('slug_ar', $exhibitionCategory->slug_ar) }}" required>
                        @error('slug_ar') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Slug (EN) <span class="text-danger">*</span></label>
                        <input type="text" name="slug_en" class="form-control"
                               value="{{ old('slug_en', $exhibitionCategory->slug_en) }}" required>
                        @error('slug_en') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> تحديث القسم
                    </button>
                    <a href="{{ route('admin.exhibition-categories.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
