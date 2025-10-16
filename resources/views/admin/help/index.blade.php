@extends('admin.layouts.app')

@section('title', 'طلبات المساعدة')

@section('content')
<div class="container">

    <form action="{{ route('admin.seo.update') }}" method="POST">
        @csrf

            @php $seo = $seoSettings[$page] ?? null; @endphp
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                     إعدادات SEO طلبات المساعدة
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
        <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
    </form>
    <br><hr> <br>

    <form action="{{ route('admin.help.content.update') }}" method="POST">
        @csrf

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                إعدادات محتوى طلبات المساعدة
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

        <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
    </form>

        <br><hr> <br>


    <!-- Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">طلبات المساعدة</h1>
    </div>

    <!-- Table Card -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="helpRequestsTable" class="table table-hover w-100 text-end">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>الشركة</th>
                            <th>البريد الإلكتروني</th>
                            <th>الهاتف</th>
                            <th>عدد الوحدات</th>
                            <th>الرسالة</th>
                            <th>تاريخ الإرسال</th>
                            <th width="120">الإجراءات</th>

                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $req)
                        <tr class="text-center">
                            <td>{{ $req->id }}</td>
                            <td>{{ $req->name }}</td>
                            <td>{{ $req->company ?? '-' }}</td>
                            <td>{{ $req->email }}</td>
                            <td>{{ $req->country_code }} {{ $req->phone }}</td>
                            <td>{{ $req->units ?? '-' }}</td>
                            <td>{{ $req->message ?? '-' }}</td>
                            <td>{{ $req->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <form action="{{ route('admin.requests.destroy', $req) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                        <i class="fas fa-trash me-1"></i> حذف
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-3"></i>
                                <p>لا توجد طلبات حتى الآن</p>
                            </td>
                        </tr>
                        @endforelse
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
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#helpRequestsTable').DataTable({
        responsive: true,
        order: [[0, 'desc']],
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "الكل"]],
        dom: '<"row"<"col-md-6"l><"col-md-6 text-start"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
        columnDefs: [
            { orderable: false, targets: [6] },
            { searchable: false, targets: [] }
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
