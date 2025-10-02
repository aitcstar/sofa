@extends('admin.layouts.app')

@section('title', 'تفاصيل الباكج')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">تفاصيل الباكج: {{ $package->name_ar }}</h1>
        <div>
            <a href="{{ route('admin.packages.edit', $package->id) }}" class="btn btn-warning btn-sm me-2">
                <i class="fas fa-edit"></i> تعديل
            </a>
            <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-right"></i> العودة
            </a>
        </div>
    </div>

    <div class="row">
        <!-- معلومات الباكج -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">معلومات الباكج </h5>
                </div>
                <div class="card-body">
                    <p><strong> السعر :</strong> {{ number_format($package->price, 2) }} ريال</p>
                    <p><strong>عدد الوحدات :</strong> {{ $package->units->count() }}</p>
                    <p><strong>عدد القطع الإجمالي :</strong> {{ $package->units->sum(fn($unit) => $unit->items->count()) }}</p>
                    <p><strong>الوصف :</strong> {{ $package->description_ar ?? 'غير متوفر' }}</p>
                    <p><strong> مدة التنفيذ  :</strong> {{ $package->period_ar ?? 'غير متوفر' }}</p>
                    <p><strong>الخدمة :</strong> {{ $package->service_includes_ar ?? 'غير متوفر' }}</p>
                    <p><strong>خطة الدفع  :</strong> {{ $package->payment_plan_ar ?? 'غير متوفر' }}</p>
                    <p><strong>الديكور :</strong> {{ $package->decoration_ar ?? 'غير متوفر' }}</p>
                </div>
            </div>
        </div>

        <!-- صور الباكج -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">صور الباكج</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($package->image)
                            <div class="col-md-12 mb-3">
                                <img src="{{ asset('storage/' . $package->image) }}" class="img-fluid rounded" alt="Package Main Image">
                            </div>
                        @endif

                        @forelse($package->images as $image)
                            <div class="col-md-3 mb-3 position-relative" id="img-{{ $image->id }}">
                                <img src="{{ asset('storage/' . $image->image_path) }}" class="img-fluid rounded shadow-sm" alt="Package Image">

                                <!-- زرار الحذف Ajax -->
                                <!--<button type="button"
                                        class="btn btn-sm btn-danger rounded-circle position-absolute top-0 end-0 m-1 delete-image"
                                        style="width:28px; height:28px;"
                                        data-url="{{ route('admin.packages.images.destroy', [$package->id, $image->id]) }}"
                                        data-id="{{ $image->id }}">
                                    <i class="fas fa-times small"></i>
                                </button>-->
                            </div>
                        @empty
                            <div class="col-12 text-center text-muted">لا توجد صور لهذا الباكج</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- الوحدات -->
    <div class="mt-4">
        <h4 class="border-bottom pb-2">الوحدات الفرعية</h4>
        @forelse($package->units as $unit)
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $unit->name_ar }}</h5>
                    <!--<span class="badge bg-light text-dark">{{ $unit->type }}</span>-->
                </div>
                <div class="card-body">
                    <p><strong>نوع الوحدة :</strong>
                    @if($unit->type == 'bedroom')
                        غرفة نوم
                    @elseif ($unit->type == 'living_room')
                    معيشة
                    @elseif ($unit->type == 'kitchen')
                    مطبخ
                    @elseif ($unit->type == 'bathroom')
                    حمام
                    @elseif ($unit->type == 'external')
                    الملحقات الخارجية والإضافية
                    @endif


                    </p>
                    {{-- <p><strong>الوصف :</strong> {{ $unit->description_ar ?? 'غير متوفر' }}</p>

                    التصاميم
                    <h6 class="mt-3">التصاميم المتاحة :</h6>
                    <div class="row">
                        @forelse($unit->designs as $design)
                            <div class="col-md-4 mb-3">
                                <div class="border p-3 rounded shadow-sm">
                                    <h6>{{ $design->name_ar }}</h6>
                                    @if($design->image_path)
                                        <img src="{{ asset('storage/' . $design->image_path) }}" class="img-fluid rounded mb-2">
                                    @endif
                                    <p class="small text-muted">{{ $design->description_ar ?? 'لا وصف' }}</p>
                                    <p class="mb-0"><strong>عدد القطع :</strong> {{ $design->items->count() }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-muted">لا توجد تصاميم لهذا الوحدة</div>
                        @endforelse
                    </div>--}}

                    <!-- جدول القطع -->
                    <h6 class="mt-4"> جدول الكميات :</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>اسم القطعة</th>
                                    <th>الكمية</th>
                                    <th>المقاس</th>
                                    <th>الخامة</th>
                                    <th>اللون</th>
                                    <th>الصورة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($unit->items as $item)
                                    <tr>
                                        <td>{{ $item->item_name_ar }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ $item->dimensions ?? '-' }}</td>
                                        <td>{{ $item->material }}</td>
                                        <td><span class="badge " style="background-color:{{$item->background_color}};color:white"> {{$item->color }} </span></td>
                                        <td><img src="{{ asset('storage/' . $item->image_path) }}" class="img-thumbnail" width="60"></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">لا توجد قطع لهذا الوحدة</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-warning">لا توجد وحدات لهذا الباكج </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.delete-image').forEach(button => {
        button.addEventListener('click', function () {
            if (!confirm("هل تريد حذف الصورة؟")) return;

            let url = this.dataset.url;
            let id = this.dataset.id;

            fetch(url, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ _method: "DELETE" })
            })
            .then(response => {
                if (response.ok) {
                    document.getElementById("img-" + id).remove();
                } else {
                    alert("فشل الحذف، حاول مرة أخرى");
                }
            })
            .catch(() => alert("حدث خطأ في الاتصال بالسيرفر"));
        });
    });
});
</script>
@endpush
