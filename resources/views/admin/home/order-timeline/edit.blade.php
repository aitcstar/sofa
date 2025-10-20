@extends('admin.layouts.app')

@section('title', 'تعديل تايم لاين الطلب')

@section('content')
@php
$user = Auth::guard('admin')->user() ?? Auth::guard('employee')->user();
@endphp
<div class="container">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">تعديل تايم لاين الطلب</h1>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">البيانات الأساسية</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.order-timeline.update') }}" method="POST">
                @csrf
                @method('PUT')

                <!-- العناوين -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">العنوان (AR)</label>
                        <input type="text" name="title_ar" class="form-control" value="{{ $section->title_ar ?? '' }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">العنوان (EN)</label>
                        <input type="text" name="title_en" class="form-control" value="{{ $section->title_en ?? '' }}">
                    </div>
                </div>

                <!-- الوصف -->
                <div class="mb-3">
                    <label class="form-label">الوصف (AR)</label>
                    <textarea name="desc_ar" class="form-control" rows="3">{{ $section->desc_ar ?? '' }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">الوصف (EN)</label>
                    <textarea name="desc_en" class="form-control" rows="3">{{ $section->desc_en ?? '' }}</textarea>
                </div>

                <!-- العناصر -->
                <div class="card mt-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">العناصر (Timeline Items)</h5>
                    </div>
                    <div class="card-body">
                        <div id="items-wrapper">
                            @foreach($section->items ?? [] as $index => $item)
                                <div class="row mb-3 item-row align-items-center">
                                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">

                                    <div class="col-md-2">
                                        <input type="text" name="items[{{ $index }}][title_ar]" class="form-control" placeholder="العنوان (AR)" value="{{ $item->title_ar }}">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" name="items[{{ $index }}][title_en]" class="form-control" placeholder="العنوان (EN)" value="{{ $item->title_en }}">
                                    </div>
                                    <div class="col-md-3">
                                        <textarea name="items[{{ $index }}][desc_ar]" class="form-control" rows="2" placeholder="الوصف (AR)">{{ $item->desc_ar }}</textarea>
                                    </div>
                                    <div class="col-md-3">
                                        <textarea name="items[{{ $index }}][desc_en]" class="form-control" rows="2" placeholder="الوصف (EN)">{{ $item->desc_en }}</textarea>
                                    </div>
                                    <div class="col-md-1">
                                        <input type="color" name="items[{{ $index }}][color]" class="form-control form-control-color" value="{{ $item->color ?? '#000000' }}">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger btn-sm remove-item">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($user && ($user->hasPermission('content.create') || $user->role === 'admin'))
                        <button type="button" id="add-item" class="btn btn-secondary">
                            <i class="fas fa-plus"></i> إضافة عنصر
                        </button>
                        @endif
                    </div>
                </div>

                <!-- أزرار الحفظ -->
                <div class="d-flex gap-2 mt-4">
                    @if($user && ($user->hasPermission('content.edit') || $user->role === 'admin'))
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> حفظ التعديلات
                    </button>
                    @endif
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('add-item').addEventListener('click', function () {
    const wrapper = document.getElementById('items-wrapper');
    const index = wrapper.querySelectorAll('.item-row').length;

    const row = document.createElement('div');
    row.classList.add('row', 'mb-3', 'item-row', 'align-items-center');
    row.innerHTML = `
        <div class="col-md-2">
            <input type="text" name="items[${index}][title_ar]" class="form-control" placeholder="العنوان (AR)">
        </div>
        <div class="col-md-2">
            <input type="text" name="items[${index}][title_en]" class="form-control" placeholder="العنوان (EN)">
        </div>
        <div class="col-md-3">
            <textarea name="items[${index}][desc_ar]" class="form-control" rows="2" placeholder="الوصف (AR)"></textarea>
        </div>
        <div class="col-md-3">
            <textarea name="items[${index}][desc_en]" class="form-control" rows="2" placeholder="الوصف (EN)"></textarea>
        </div>
        <div class="col-md-1">
            <input type="color" name="items[${index}][color]" class="form-control form-control-color">
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-danger btn-sm remove-item">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    wrapper.appendChild(row);
});

// حذف عنصر
document.addEventListener('click', function (e) {
    if (e.target.closest('.remove-item')) {
        e.target.closest('.item-row').remove();
    }
});
</script>
@endsection
