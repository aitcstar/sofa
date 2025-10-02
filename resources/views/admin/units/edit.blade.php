@extends('admin.layouts.app')

@section('title', isset($unit) ? 'تعديل الوحدة' : 'إنشاء وحدة')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">{{ isset($unit) ? 'تعديل الوحدة' : 'إنشاء وحدة جديدة' }}</h1>
        <a href="{{ route('admin.units.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i> رجوع
        </a>
    </div>

    <!-- Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">معلومات الوحدة</h5>
        </div>
        <div class="card-body">
            <form action="{{ isset($unit) ? route('admin.units.update', $unit) : route('admin.units.store') }}"
                  method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($unit)) @method('PUT') @endif



                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>الاسم (AR)</label>
                        <input type="text" name="name_ar" class="form-control" value="{{ $unit->name_ar ?? old('name_ar') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>الاسم (EN)</label>
                        <input type="text" name="name_en" class="form-control" value="{{ $unit->name_en ?? old('name_en') }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label>النوع</label>
                    <select name="type" class="form-select" required>
                        <option value="bedroom" {{ isset($unit) && $unit->type == 'bedroom' ? 'selected' : '' }}>غرفة نوم</option>
                        <option value="living_room" {{ isset($unit) && $unit->type == 'living_room' ? 'selected' : '' }}>غرفة معيشة</option>
                        <option value="kitchen" {{ isset($unit) && $unit->type == 'kitchen' ? 'selected' : '' }}>مطبخ</option>
                        <option value="bathroom" {{ isset($unit) && $unit->type == 'bathroom' ? 'selected' : '' }}>حمام</option>
                        <option value="external" {{ isset($unit) && $unit->type == 'external' ? 'selected' : '' }}>الملحقات الخارجية والإضافية</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>الوصف (AR)</label>
                    <textarea name="description_ar" class="form-control">{{ $unit->description_ar ?? old('description_ar') }}</textarea>
                </div>

                <div class="mb-3">
                    <label>الوصف (EN)</label>
                    <textarea name="description_en" class="form-control">{{ $unit->description_en ?? old('description_en') }}</textarea>
                </div>

                <div class="mb-3">
                    <label>رفع صور الوحدة</label>
                    <input type="file" name="images[]" class="form-control" multiple>
                </div>

                @if(isset($images) && $images->count())
                    <div class="mb-3">
                        <label>صور الوحدة الحالية</label>
                        <div class="row">
                            @foreach($images as $image)
                            <div class="col-md-3 mb-2 position-relative">
                                <img src="{{ asset('storage/'.$image->image_path) }}" class="img-fluid border rounded">
                                <button class="btn btn-sm btn-danger delete-image"
                                        data-unit="{{ $unit->id }}"
                                        data-image="{{ $image->id }}"
                                        style="position:absolute; top:5px; right:5px;">
                                    ✕
                                </button>
                            </div>
                            @endforeach

                        </div>
                    </div>
                @endif

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> {{ isset($unit) ? 'تحديث الوحدة' : 'إنشاء الوحدة' }}
                    </button>
                    <a href="{{ route('admin.units.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.delete-image').click(function(e){
        e.preventDefault();

        if(!confirm('هل تريد حذف الصورة؟')) return;

        var button = $(this);
        var unitId = button.data('unit');
        var imageId = button.data('image');

        $.ajax({
            url: '/admin/units/' + unitId + '/images/' + imageId,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response){
                if(response.success){
                    button.closest('.col-md-3').remove();
                } else {
                    alert('حدث خطأ أثناء الحذف');
                }
            },
            error: function(xhr){
                alert('حدث خطأ أثناء الحذف');
            }
        });
    });
});
</script>

@endsection
