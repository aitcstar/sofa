@extends('admin.layouts.app')

@section('title', 'إضافة توصية')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">إضافة توصية جديدة</h1>
        <a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i> رجوع
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">معلومات التوصية</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.testimonials.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الاسم <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">المكان</label>
                        <input type="text" name="location" class="form-control">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">الرسالة <span class="text-danger">*</span></label>
                    <textarea name="message" class="form-control" rows="4" required></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">التقييم <span class="text-danger">*</span></label>
                        <select name="rating" class="form-select" required>
                            <option value="">اختر التقييم</option>
                            @for($i=1; $i<=5; $i++)
                                <option value="{{ $i }}">{{ $i }} نجوم</option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">الصورة</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <div class="form-text">يُفضل صورة بحجم 100x100 بكسل</div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> حفظ التوصية
                    </button>
                    <a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
