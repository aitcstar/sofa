@extends('admin.layouts.app')

@section('title', 'إدارة الباكجات')

@section('content')
@php
$user = Auth::guard('admin')->user() ?? Auth::guard('employee')->user();
@endphp
<div class="container">
    <!-- Header -->

    <form action="{{ route('admin.seo.update') }}" method="POST">
        @csrf

            @php $seo = $seoSettings[$page] ?? null; @endphp
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                     إعدادات SEO
                </div>
                <div class="card-body">
                    {{-- العنوان --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Meta Title (AR)</label>
                            <input type="text" name="seo[{{ $page }}][meta_title_ar]" class="form-control"
                                   value="{{ old("seo.$page.meta_title_ar", $seo->meta_title_ar ?? '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Meta Title (EN)</label>
                            <input type="text" name="seo[{{ $page }}][meta_title_en]" class="form-control"
                                   value="{{ old("seo.$page.meta_title_en", $seo->meta_title_en ?? '') }}">
                        </div>
                    </div>

                    {{-- الوصف --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Meta Description (AR)</label>
                            <textarea name="seo[{{ $page }}][meta_description_ar]" class="form-control">{{ old("seo.$page.meta_description_ar", $seo->meta_description_ar ?? '') }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Meta Description (EN)</label>
                            <textarea name="seo[{{ $page }}][meta_description_en]" class="form-control">{{ old("seo.$page.meta_description_en", $seo->meta_description_en ?? '') }}</textarea>
                        </div>
                    </div>

                    {{-- Slug --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Slug (AR)</label>
                            <input type="text" name="seo[{{ $page }}][slug_ar]" class="form-control"
                                   value="{{ old("seo.$page.slug_ar", $seo->slug_ar ?? '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Slug (EN)</label>
                            <input type="text" name="seo[{{ $page }}][slug_en]" class="form-control"
                                   value="{{ old("seo.$page.slug_en", $seo->slug_en ?? '') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Index Status</label>
                        <select name="seo[{{ $page }}][index_status]" class="form-select">
                            <option value="index" {{ ($seo->index_status ?? '') == 'index' ? 'selected' : '' }}>Index</option>
                            <option value="noindex" {{ ($seo->index_status ?? '') == 'noindex' ? 'selected' : '' }}>No Index</option>
                        </select>
                    </div>
                </div>
            </div>
            @if($user && ($user->hasPermission('packages.edit') || $user->role === 'admin'))
                <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
            @endif
    </form>

    <hr><br>
    <form action="{{ route('admin.package.content.update') }}" method="POST">
        @csrf

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                إعدادات محتوى  الباكدجات
            </div>
            <div class="card-body">
                {{-- العنوان --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">العنوان (AR)</label>
                        <input type="text" name="title_ar" value="{{ old('title_ar', $content->title_ar ?? '') }}" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">العنوان (EN)</label>
                        <input type="text" name="title_en" value="{{ old('title_en', $content->title_en ?? '') }}" class="form-control">
                    </div>
                </div>

                {{-- النص --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">النص (AR)</label>
                        <textarea name="text_ar" class="form-control" rows="4">{{ old('text_ar', $content->text_ar ?? '') }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">النص (EN)</label>
                        <textarea name="text_en" class="form-control" rows="4">{{ old('text_en', $content->text_en ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        @if($user && ($user->hasPermission('packages.edit') || $user->role === 'admin'))
            <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
        @endif
    </form>

        <br><hr> <br>
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">إدارة الباكجات</h1>
            @if($user && ($user->hasPermission('packages.create') || $user->role === 'admin'))
            <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> إضافة باكج جديد
            </a>
            @endif
        </div>
    <!-- Table Card -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="packagesTable" class="table table-hover w-100 text-end">
                    <thead>
                        <tr>
                            <th>الصورة</th>
                            <th>الاسم</th>
                            <th>السعر</th>
                            <th>عدد الوحدات</th>
                            <th>عدد القطع</th>
                            <th>الوان الباكج</th>
                            <th>الترتيب</th>
                            <th>تظهر في الرئيسية</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($packages as $package)
                        <tr>
                            <td>
                                <img src="{{ asset('storage/' . $package->image) }}" width="50" height="50" class="rounded">
                            </td>
                            <td>{{ $package->name }}</td>
                            <td>{{ number_format($package->price, 2) }} ريال</td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $package->packageUnitItems->groupBy('unit_id')->count() }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ $package->packageUnitItems->count() }}
                                </span>
                            </td>
                            <td>
                                @if(!empty($package->available_colors))
                                    @foreach($package->available_colors as $color)
                                        <div class="d-inline-block me-1 mb-1 text-center" >
                                            <span class="d-block" style="width:20px; height:20px; border-radius:50%; background-color: {{ $color['color_code'] }}; border:1px solid #ccc; margin:auto;"></span>
                                        </div>
                                    @endforeach
                                @else
                                    <span class="text-muted">لا توجد ألوان</span>
                                @endif
                            </td>
                            <td>{{ $package->sort_order }}</td>
                            <td>
                                <input type="checkbox"
                                       class="form-check-input toggle-home"
                                       data-id="{{ $package->id }}"
                                       {{ $package->show_in_home ? 'checked' : '' }}>
                            </td>
                            <td class="d-flex gap-1">
                                @if($user && ($user->hasPermission('packages.edit') || $user->role === 'admin'))
                                <a href="{{ route('admin.packages.edit', $package->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit me-1"></i> تعديل
                                </a>
                                @endif
                                @if($user && ($user->hasPermission('packages.delete') || $user->role === 'admin'))
                                    <form action="{{ route('admin.packages.destroy', $package->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"  class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>حذف
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.toggle-home').forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const packageId = this.dataset.id;
                const showInHome = this.checked ? 1 : 0;

                fetch(`/admin/packages/${packageId}/toggle-home`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ show_in_home: showInHome })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('تم التحديث بنجاح');
                    } else {
                        alert('حدث خطأ أثناء الحفظ');
                    }
                })
                .catch(() => alert('تعذر الاتصال بالخادم'));
            });
        });
    });
    </script>

<script>
$(document).ready(function() {

    $('#packagesTable').DataTable({
        responsive: true,
        order: [[5, 'asc']],
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "الكل"]],
        dom: '<"row"<"col-md-6"l><"col-md-6 text-start"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
        columnDefs: [
            { orderable: false, targets: [1, 6] },
            { searchable: false, targets: [1, 6] }
        ],
        language: {
            emptyTable: "لا توجد بيانات متاحة في الجدول",
            info: "إظهار _START_ إلى _END_ من أصل _TOTAL_ مدخل",
            infoEmpty: "إظهار 0 إلى 0 من أصل 0 مدخل",
            infoFiltered: "(تمت تصفيته من أصل _MAX_ مدخل)",
            lengthMenu: "إظهار _MENU_ مدخلات",
            loadingRecords: "جارٍ التحميل...",
            processing: "جارٍ المعالجة...",
            search: "بحث:",
            zeroRecords: "لم يتم العثور على سجلات مطابقة",
            paginate: {
                first: "الأول",
                last: "الأخير",
                next: "التالي",
                previous: "السابق"
            },
            aria: {
                sortAscending: ": تفعيل لترتيب العمود تصاعدياً",
                sortDescending: ": تفعيل لترتيب العمود تنازلياً"
            }
        }
    });
});
</script>
@endpush
