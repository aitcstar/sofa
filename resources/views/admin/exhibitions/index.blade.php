@extends('admin.layouts.app')

@section('title', 'المعارض')

@section('content')
@php
$user = Auth::guard('admin')->user() ?? Auth::guard('employee')->user();
@endphp
<div class="container">


    <form action="{{ route('admin.seo.update') }}" method="POST">
        @csrf

            @php $seo = $seoSettings[$page] ?? null; @endphp
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                     إعدادات SEO المعارض
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
            @if($user && ($user->hasPermission('exhibitions.edit') || $user->role === 'admin'))
        <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
        @endif
    </form>
    <br><hr> <br>

    <form action="{{ route('admin.exhibitions.content.update') }}" method="POST">
        @csrf

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                إعدادات محتوى المعرض
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
        @if($user && ($user->hasPermission('exhibitions.edit') || $user->role === 'admin'))
        <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
        @endif
    </form>

        <br><hr> <br>


    <!-- Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">المعارض</h1>
        @if($user && ($user->hasPermission('exhibitions.create') || $user->role === 'admin'))
        <a href="{{ route('admin.exhibitions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> إضافة معرض جديد
        </a>
        @endif
    </div>



    <!-- Exhibitions Table Card -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="exhibitionsTable" class="table table-hover table-bordered align-middle mb-0 text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>العنوان</th>
                            <th>التصنيف</th>
                            <th>الباقة</th>
                            <th>تاريخ التسليم</th>
                            <th>الحالة</th>
                            <th>خيارات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exhibitions as $exhibition)
                        <tr>
                            <td>{{ $exhibition->id }}</td>
                            <td>{{ $exhibition->name_ar }}</td>
                            <td>{{ $exhibition->category->name_ar ?? '-' }}</td>
                            <td>{{ $exhibition->packages->name_ar ?? '-' }}</td>
                            <td>{{ $exhibition->delivery_date }}</td>
                            <td>
                                @if($exhibition->is_active)
                                    <span class="badge bg-success">مفعل</span>
                                @else
                                    <span class="badge bg-danger">غير مفعل</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    @if($user && ($user->hasPermission('exhibitions.edit') || $user->role === 'admin'))
                                    <a href="{{ route('admin.exhibitions.edit', $exhibition) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit me-1"></i> تعديل
                                    </a>
                                    @endif
                                    @if($user && ($user->hasPermission('exhibitions.delete') || $user->role === 'admin'))
                                    <form action="{{ route('admin.exhibitions.destroy', $exhibition) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                            <i class="fas fa-trash me-1"></i> حذف
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-exhibition fa-2x mb-2"></i>
                                <p>لا توجد معارض حتى الآن</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-3">
        {{ $exhibitions->links() }}
    </div>

</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#exhibitionsTable').DataTable({
        responsive: true,
        order: [[0, 'desc']],
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "الكل"]],
        dom: '<"row"<"col-md-6"l><"col-md-6 text-start"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
        columnDefs: [
            { orderable: false, targets: [6] },
            { searchable: false, targets: [6] }
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
            paginate: { first: "الأول", last: "الأخير", next: "التالي", previous: "السابق" },
            aria: { sortAscending: ": تفعيل لترتيب العمود تصاعدياً", sortDescending: ": تفعيل لترتيب العمود تنازلياً" }
        }
    });
});
</script>
@endpush
