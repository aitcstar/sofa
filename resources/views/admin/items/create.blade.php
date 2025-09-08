@extends('admin.layouts.app')

@section('title', 'إضافة قطعة جديدة')

@section('content')
<div class="container">
    <h1 class="h3 mb-4">إضافة قطعة جديدة - {{ $design->name }}</h1>

    <a href="{{ route('admin.designs.items.index', $design) }}" class="btn btn-secondary mb-4">
        <i class="fas fa-arrow-left"></i> العودة
    </a>

    <form action="{{ route('admin.designs.items.store', $design) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="unit_id" class="form-label">الوحدة</label>
            <select name="unit_id" id="unit_id" class="form-control" required>
                <option value="">اختر الوحدة</option>
                @foreach($design->units as $unit)
                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="item_name" class="form-label">اسم القطعة</label>
            <input type="text" name="item_name" id="item_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">الكمية</label>
            <input type="number" name="quantity" id="quantity" class="form-control" min="1" required>
        </div>

        <div class="mb-3">
            <label for="dimensions" class="form-label">المقاس</label>
            <input type="text" name="dimensions" id="dimensions" class="form-control" placeholder="مثال: 200x100x50">
        </div>

        <div class="mb-3">
            <label for="material" class="form-label">الخامة</label>
            <input type="text" name="material" id="material" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="color" class="form-label">اللون</label>
            <input type="text" name="color" id="color" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">صورة القطعة</label>
            <input type="file" name="image" id="image" class="form-control" accept="image/*">
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> حفظ القطعة
            </button>
            <a href="{{ route('admin.designs.items.index', $design) }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> إلغاء
            </a>
        </div>
    </form>
</div>
@endsection
