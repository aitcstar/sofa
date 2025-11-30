@extends('admin.layouts.app')

@section('title', 'إنشاء تصميم جديد')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">إنشاء تصميم جديد </h1>
        <a href="{{ route('admin.designs.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> العودة
        </a>
    </div>

    <form action="{{ route('admin.designs.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row mb-4">
            <!-- اسم التصميم عربي -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="name_ar" class="form-label">اسم التصميم (عربي)</label>
                    <input type="text" name="name_ar" id="name_ar" class="form-control" required>
                </div>
            </div>

            <!-- اسم التصميم انجليزي -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="name_en" class="form-label">اسم التصميم (English)</label>
                    <input type="text" name="name_en" id="name_en" class="form-control" required>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label>الباكج</label>
                <select name="package_id" class="form-select">
                    @foreach($packages as $package)
                        <option value="{{ $package->id }}">{{ $package->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> حفظ التصميم
            </button>
            <a href="{{ route('admin.designs.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> إلغاء
            </a>
        </div>
    </form>
</div>
@endsection
