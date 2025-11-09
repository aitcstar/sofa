@extends('admin.layouts.app')

@section('title', 'تعديل التصميم')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">تعديل التصميم: {{ $design->name_ar }}</h1>
        <a href="{{ route('admin.designs.index') }}"class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> رجوع
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
