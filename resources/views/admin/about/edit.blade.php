@extends('admin.layouts.app')

@section('title', 'تعديل القسم')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">تعديل القسم: {{ $section->section }}</h1>
        <a href="{{ route('admin.about.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i> رجوع
        </a>
    </div>

    <!-- Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">تعديل بيانات القسم</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.about.update', $section->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Arabic -->
                    <div class="col-md-6">
                        <h5 class="mb-3 text-primary">العربية</h5>
                        <div class="mb-3">
                            <label class="form-label">العنوان (عربي)</label>
                            <input type="text" name="title_ar" class="form-control" value="{{ old('title_ar', $section->title_ar) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">النص (عربي)</label>
                            <textarea name="text_ar" rows="3" class="form-control">{{ old('text_ar', $section->text_ar) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">العناصر (عربي)</label>
                            <div id="items-ar-wrapper">
                                @forelse($section->items_ar ?? [] as $item)
                                    <div class="d-flex mb-2">
                                        <input type="text" name="items_ar[]" class="form-control" value="{{ $item }}">
                                        <button type="button" class="btn btn-danger btn-sm ms-2 remove-item">X</button>
                                    </div>
                                @empty
                                    <div class="d-flex mb-2">
                                        <input type="text" name="items_ar[]" class="form-control" placeholder="أدخل عنصر">
                                    </div>
                                @endforelse
                            </div>
                            <button type="button" id="add-item-ar" class="btn btn-sm btn-outline-primary mt-2">+ إضافة عنصر</button>
                        </div>
                    </div>

                    <!-- English -->
                    <div class="col-md-6">
                        <h5 class="mb-3 text-primary">English</h5>
                        <div class="mb-3">
                            <label class="form-label">Title (English)</label>
                            <input type="text" name="title_en" class="form-control" value="{{ old('title_en', $section->title_en) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Text (English)</label>
                            <textarea name="text_en" rows="3" class="form-control">{{ old('text_en', $section->text_en) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Items (English)</label>
                            <div id="items-en-wrapper">
                                @forelse($section->items_en ?? [] as $item)
                                    <div class="d-flex mb-2">
                                        <input type="text" name="items_en[]" class="form-control" value="{{ $item }}">
                                        <button type="button" class="btn btn-danger btn-sm ms-2 remove-item">X</button>
                                    </div>
                                @empty
                                    <div class="d-flex mb-2">
                                        <input type="text" name="items_en[]" class="form-control" placeholder="Enter item">
                                    </div>
                                @endforelse
                            </div>
                            <button type="button" id="add-item-en" class="btn btn-sm btn-outline-primary mt-2">+ Add Item</button>
                        </div>
                    </div>
                </div>

                <!-- Image -->
                <div class="mb-3">
                    <label class="form-label">الصورة الحالية</label>
                    <div>
                        @if($section->image)
                            <img src="{{ asset('storage/' . $section->image) }}" class="current-image me-3 border" width="200">
                            <a href="{{ asset('storage/'.$section->image) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye me-1"></i> عرض الصورة
                            </a>
                        @else
                            <span class="text-muted">لا توجد صورة</span>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">تغيير الصورة</label>
                    <input type="file" name="image" id="imageUpload" class="form-control" accept="image/*">
                    <div class="form-text">اتركه فارغاً إذا كنت لا تريد تغيير الصورة</div>
                    <img id="imagePreview" class="image-preview mt-2" style="display:none; max-width:200px;">
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> حفظ التعديلات
                    </button>
                    <a href="{{ route('admin.about.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Preview Image
    const imageInput = document.getElementById('imageUpload');
    const imagePreview = document.getElementById('imagePreview');
    imageInput?.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Add Item (Arabic)
    document.getElementById('add-item-ar')?.addEventListener('click', function() {
        let wrapper = document.getElementById('items-ar-wrapper');
        let div = document.createElement('div');
        div.classList.add('d-flex', 'mb-2');
        div.innerHTML = `<input type="text" name="items_ar[]" class="form-control">
                         <button type="button" class="btn btn-danger btn-sm ms-2 remove-item">X</button>`;
        wrapper.appendChild(div);
    });

    // Add Item (English)
    document.getElementById('add-item-en')?.addEventListener('click', function() {
        let wrapper = document.getElementById('items-en-wrapper');
        let div = document.createElement('div');
        div.classList.add('d-flex', 'mb-2');
        div.innerHTML = `<input type="text" name="items_en[]" class="form-control">
                         <button type="button" class="btn btn-danger btn-sm ms-2 remove-item">X</button>`;
        wrapper.appendChild(div);
    });

    // Remove item
    document.addEventListener('click', function(e) {
        if(e.target.classList.contains('remove-item')) {
            e.target.parentElement.remove();
        }
    });
});
</script>
@endpush
