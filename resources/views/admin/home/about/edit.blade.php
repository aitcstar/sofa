@extends('admin.layouts.app')

@section('title', 'تعديل قسم من نحن')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">تعديل قسم من نحن</h1>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">البيانات الأساسية</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.home-about.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- الصورة -->
                <div class="mb-3">
                    <label class="form-label">الصورة الحالية</label>
                    @if($about && $about->image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/'.$about->image) }}" width="200" class="rounded object-fit-cover border">
                            <a href="{{ asset('storage/'.$about->image) }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    @endif
                    <label class="form-label">تغيير الصورة</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>

                <!-- العناوين -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">العنوان (AR)</label>
                        <input type="text" name="title_ar" class="form-control" value="{{ $about->title_ar ?? '' }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">العنوان (EN)</label>
                        <input type="text" name="title_en" class="form-control" value="{{ $about->title_en ?? '' }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">العنوان الفرعي (AR)</label>
                        <input type="text" name="sub_title_ar" class="form-control" value="{{ $about->sub_title_ar ?? '' }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">العنوان الفرعي (EN)</label>
                        <input type="text" name="sub_title_en" class="form-control" value="{{ $about->sub_title_en ?? '' }}">
                    </div>
                </div>

                <!-- الوصف -->
                <div class="mb-3">
                    <label class="form-label">الوصف (AR)</label>
                    <textarea name="desc_ar" class="form-control" rows="3">{{ $about->desc_ar ?? '' }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">الوصف (EN)</label>
                    <textarea name="desc_en" class="form-control" rows="3">{{ $about->desc_en ?? '' }}</textarea>
                </div>

                <!-- زر -->
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">نص الزر (AR)</label>
                        <input type="text" name="button_text_ar" class="form-control" value="{{ $about->button_text_ar ?? '' }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">نص الزر (EN)</label>
                        <input type="text" name="button_text_en" class="form-control" value="{{ $about->button_text_en ?? '' }}">
                    </div>
                    <!--<div class="col-md-4 mb-3">
                        <label class="form-label">رابط الزر</label>
                        <input type="url" name="button_link" class="form-control" value="{{ $about->button_link ?? '' }}">
                    </div>-->
                </div>

                <!-- الأيقونات -->
                <div class="card mt-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">الأيقونات</h5>
                    </div>
                    <div class="card-body">
                        <div id="icons-wrapper">
                            @foreach($icons as $index => $icon)
                                <div class="row mb-3 icon-row align-items-center">
                                    <input type="hidden" name="icons[{{ $index }}][id]" value="{{ $icon->id }}">
                                    <div class="col-md-3">
                                        <input type="file" name="icons[{{ $index }}][icon]" class="form-control">
                                        @if($icon->icon)
                                            <img src="{{ asset('storage/'.$icon->icon) }}" alt="icon" class="img-thumbnail mt-2" width="60">
                                        @endif
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="icons[{{ $index }}][title_ar]" class="form-control" placeholder="العنوان (AR)" value="{{ $icon->title_ar }}">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="icons[{{ $index }}][title_en]" class="form-control" placeholder="العنوان (EN)" value="{{ $icon->title_en }}">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="icons[{{ $index }}][order]" class="form-control" placeholder="الترتيب" value="{{ $icon->order }}">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger btn-sm remove-icon">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" id="add-icon" class="btn btn-secondary">
                            <i class="fas fa-plus"></i> إضافة أيقونة
                        </button>
                    </div>
                </div>

                <!-- أزرار الحفظ -->
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> حفظ التعديلات
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('add-icon').addEventListener('click', function () {
    const wrapper = document.getElementById('icons-wrapper');
    const index = wrapper.querySelectorAll('.icon-row').length;

    const row = document.createElement('div');
    row.classList.add('row', 'mb-3', 'icon-row', 'align-items-center');
    row.innerHTML = `
        <div class="col-md-3">
            <input type="file" name="icons[${index}][icon]" class="form-control">
        </div>
        <div class="col-md-3">
            <input type="text" name="icons[${index}][title_ar]" class="form-control" placeholder="العنوان (AR)">
        </div>
        <div class="col-md-3">
            <input type="text" name="icons[${index}][title_en]" class="form-control" placeholder="العنوان (EN)">
        </div>
        <div class="col-md-2">
            <input type="number" name="icons[${index}][order]" class="form-control" placeholder="الترتيب" value="1">
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-danger btn-sm remove-icon">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    wrapper.appendChild(row);
});

// حذف أيقونة
document.addEventListener('click', function (e) {
    if (e.target.closest('.remove-icon')) {
        e.target.closest('.icon-row').remove();
    }
});
</script>
@endsection
