@extends('admin.layouts.app')

@section('title', 'تعديل القسم')

@section('content')
<div class="container">
    <h4 class="mb-4"><i class="bi bi-pencil"></i> تعديل القسم</h4>

    <form action="{{ route('admin.faq-categories.update', $faqCategory) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">الاسم بالعربية</label>
                        <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $faqCategory->name_ar) }}" required>
                        @error('name_ar') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">الاسم بالإنجليزية</label>
                        <input type="text" name="name_en" class="form-control" value="{{ old('name_en', $faqCategory->name_en) }}" required>
                        @error('name_en') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">الترتيب</label>
                        <input type="number" name="sort" class="form-control" value="{{ old('sort', $faqCategory->sort) }}">
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <button class="btn btn-primary"><i class="bi bi-check"></i> تحديث</button>
                <a href="{{ route('admin.faq-categories.index') }}" class="btn btn-outline-secondary">رجوع</a>
            </div>
        </div>
    </form>
</div>
@endsection
