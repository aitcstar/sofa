@extends('admin.layouts.app')

@section('title', 'تعديل الخطوة')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">تعديل الخطوة</h1>
        <a href="{{ route('admin.steps.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i> رجوع
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">تعديل محتوى الخطوة</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.steps.update', $step) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">العنوان بالعربية</label>
                        <input type="text" name="title_ar" class="form-control @error('title_ar') is-invalid @enderror"
                               value="{{ old('title_ar', $step->title_ar) }}" required>
                        @error('title_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">العنوان بالإنجليزية</label>
                        <input type="text" name="title_en" class="form-control @error('title_en') is-invalid @enderror"
                               value="{{ old('title_en', $step->title_en) }}">
                        @error('title_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الوصف بالعربية</label>
                        <textarea name="desc_ar" class="form-control @error('desc_ar') is-invalid @enderror" rows="4" required>{{ old('desc_ar', $step->desc_ar) }}</textarea>
                        @error('desc_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الوصف بالإنجليزية</label>
                        <textarea name="desc_en" class="form-control @error('desc_en') is-invalid @enderror" rows="4">{{ old('desc_en', $step->desc_en) }}</textarea>
                        @error('desc_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">الترتيب</label>
                    <input type="number" name="order" class="form-control @error('order') is-invalid @enderror"
                           value="{{ old('order', $step->order) }}" required>
                    @error('order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">الأيقونة / الصورة الحالية</label>
                    @if($step->icon)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $step->icon) }}" width="150" class="rounded border object-fit-cover">
                        </div>
                    @else
                        <div class="text-muted mb-2">لا توجد صورة حالياً</div>
                    @endif
                    <label class="form-label">تغيير الصورة (اختياري)</label>
                    <input type="file" name="icon" class="form-control @error('icon') is-invalid @enderror">
                    @error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-edit me-1"></i> تعديل</button>
                    <a href="{{ route('admin.steps.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times me-1"></i> إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@endpush
