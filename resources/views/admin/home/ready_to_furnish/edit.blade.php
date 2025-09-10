@extends('admin.layouts.app')

@section('title', 'تعديل جاهز لتأثيث وحدتك')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">تعديل جاهز لتأثيث وحدتك</h1>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">البيانات الأساسية</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.ready-to-furnish.update') }}" method="POST" enctype="multipart/form-data">
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

                <!-- واتساب -->
                <div class="mb-3">
                    <label class="form-label">رقم الواتساب</label>
                    <input type="text" name="whatsapp" class="form-control" value="{{ $section->whatsapp ?? '' }}">
                </div>

                <!-- رابط البدء -->
               {{-- <div class="mb-3">
                    <label class="form-label">رابط البدء في الطلب</label>
                    <input type="text" name="start_order_link" class="form-control" value="{{ $section->start_order_link ?? '' }}">
                </div>--}}

                <!-- الصورة -->
                <div class="mb-3">
                    <label class="form-label">الصورة</label>
                    <input type="file" name="image" class="form-control">
                    @if(!empty($section->image))
                        <div class="mt-2">
                            <img src="{{ asset('storage/'.$section->image) }}" alt="Ready Image" class="img-thumbnail" style="max-height: 150px;">
                        </div>
                    @endif
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
@endsection
