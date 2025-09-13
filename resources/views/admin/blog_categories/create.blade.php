@extends('admin.layouts.app')

@section('title', 'إضافة قسم جديد')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">إضافة قسم جديد</h1>
        <a href="{{ route('admin.blog_categories.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i> رجوع
        </a>
    </div>

    <!-- Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">تفاصيل القسم</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.blog_categories.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">الاسم بالعربية</label>
                        <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">الاسم بالإنجليزية</label>
                        <input type="text" name="name_en" class="form-control" value="{{ old('name_en') }}" required>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> حفظ القسم
                    </button>
                    <a href="{{ route('admin.blog_categories.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
