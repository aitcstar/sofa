@extends('admin.layouts.app')

@section('title', 'إضافة خطوة جديدة')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">إضافة خطوة جديدة</h1>
        <a href="{{ route('admin.steps.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i> رجوع
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">تفاصيل الخطوة</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.steps.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">العنوان بالعربية</label>
                        <input type="text" name="title_ar" class="form-control @error('title_ar') is-invalid @enderror" value="{{ old('title_ar') }}" required>
                        @error('title_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">العنوان بالإنجليزية</label>
                        <input type="text" name="title_en" class="form-control @error('title_en') is-invalid @enderror" value="{{ old('title_en') }}">
                        @error('title_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الوصف بالعربية</label>
                        <textarea name="desc_ar" class="form-control @error('desc_ar') is-invalid @enderror" rows="4" required>{{ old('desc_ar') }}</textarea>
                        @error('desc_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الوصف بالإنجليزية</label>
                        <textarea name="desc_en" class="form-control @error('desc_en') is-invalid @enderror" rows="4">{{ old('desc_en') }}</textarea>
                        @error('desc_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">الترتيب</label>
                    <input type="number" name="order" class="form-control @error('order') is-invalid @enderror" value="{{ old('order', 1) }}" required>
                    @error('order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">الأيقونة / الصورة</label>
                    <input type="file" name="icon" id="iconUpload" class="form-control @error('icon') is-invalid @enderror">
                    <img id="iconPreview" class="image-preview mt-2" style="max-width: 150px; display: none;">
                    @error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-plus me-1"></i> إضافة</button>
                    <a href="{{ route('admin.steps.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times me-1"></i> إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    // معاينة صورة الأيقونة قبل الرفع
    document.getElementById('iconUpload').addEventListener('change', function(e) {
        const preview = document.getElementById('iconPreview');
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

</script>
@endpush
