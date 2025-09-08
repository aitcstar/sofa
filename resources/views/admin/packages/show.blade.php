@extends('admin.layouts.app')

@section('title', 'تفاصيل الباكج / Package Details')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">تفاصيل الباكج: {{ $package->name_ar }} / {{ $package->name_en }}</h1>
        <div>
            <a href="{{ route('admin.packages.edit', $package->id) }}" class="btn btn-warning btn-sm me-2">
                <i class="fas fa-edit"></i> تعديل / Edit
            </a>
            <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-right"></i> العودة / Back
            </a>
        </div>
    </div>

    <div class="row">
        <!-- معلومات الباكج -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">معلومات الباكج / Package Info</h5>
                </div>
                <div class="card-body">
                    <p><strong>السعر / Price:</strong> {{ number_format($package->price, 2) }} ريال</p>
                    <p><strong>عدد الوحدات / Units Count:</strong> {{ $package->units->count() }}</p>
                    <p><strong>عدد القطع الإجمالي / Total Items:</strong> {{ $package->units->sum(fn($unit) => $unit->items->count()) }}</p>
                    <p><strong>الوصف / Description:</strong> {{ $package->description_ar ?? 'غير متوفر' }} / {{ $package->description_en ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- صور الباكج -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">صور الباكج / Package Images</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse($package->images as $image)
                            <div class="col-md-3 mb-3 position-relative">
                                <img src="{{ asset('storage/' . $image->image_path) }}" class="img-fluid rounded" alt="Package Image">
                                @if($image->is_primary)
                                    <span class="badge bg-primary position-absolute top-0 start-0 m-1">رئيسية / Primary</span>
                                @endif
                                <form action="{{ route('admin.package-images.destroy', $image->id) }}" method="POST" class="mt-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger w-100" onclick="return confirm('حذف الصورة؟ / Delete image?')">حذف / Delete</button>
                                </form>
                            </div>
                        @empty
                            <div class="col-12 text-center text-muted">لا توجد صور لهذا الباكج / No images available</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- الوحدات -->
    <div class="mt-4">
        <h4 class="border-bottom pb-2">الوحدات الفرعية / Sub Units</h4>
        @forelse($package->units as $unit)
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $unit->name_ar }} / {{ $unit->name_en }}</h5>
                    <span class="badge bg-light text-dark">{{ $unit->type }}</span>
                </div>
                <div class="card-body">
                    <p><strong>الوصف / Description:</strong> {{ $unit->description_ar ?? 'غير متوفر' }} / {{ $unit->description_en ?? 'N/A' }}</p>

                    <!-- التصاميم -->
                    <h6 class="mt-3">التصاميم المتاحة / Available Designs:</h6>
                    <div class="row">
                        @forelse($unit->designs as $design)
                            <div class="col-md-4 mb-3">
                                <div class="border p-3 rounded">
                                    <h6>{{ $design->name_ar }} / {{ $design->name_en }}</h6>
                                    @if($design->image_path)
                                        <img src="{{ asset('storage/' . $design->image_path) }}" class="img-fluid rounded mb-2" width="100%">
                                    @endif
                                    <p class="small text-muted">{{ $design->description_ar ?? 'لا وصف' }} / {{ $design->description_en ?? 'No description' }}</p>
                                    <p class="mb-0"><strong>عدد القطع / Items Count:</strong> {{ $design->items->count() }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-muted">لا توجد تصاميم لهذا الوحدة / No designs for this unit</div>
                        @endforelse
                    </div>

                    <!-- جدول القطع -->
                    <h6 class="mt-4">جدول القطع / Items Table (All Designs):</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>اسم القطعة / Item Name</th>
                                    <th>الكمية / Quantity</th>
                                    <th>المقاس / Dimensions</th>
                                    <th>الخامة / Material</th>
                                    <th>اللون / Color</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($unit->items as $item)
                                    <tr>
                                        <td>{{ $item->item_name_ar }} / {{ $item->item_name_en }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ $item->dimensions ?? '-' }}</td>
                                        <td>{{ $item->material }}</td>
                                        <td>{{ $item->color }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">لا توجد قطع لهذا الوحدة / No items for this unit</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-warning">لا توجد وحدات لهذا الباكج / No units for this package</div>
        @endforelse
    </div>
</div>
@endsection
