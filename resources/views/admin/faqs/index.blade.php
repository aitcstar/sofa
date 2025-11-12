@extends('admin.layouts.app')

@section('title', ' الأسئلة الشائعة')

@section('content')
@php
$user = Auth::guard('admin')->user() ?? Auth::guard('employee')->user();
@endphp
<div class="container">
    <!-- Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"> الأسئلة الشائعة</h1>
        @if($user && ($user->hasPermission('faqs.create') || $user->role === 'admin'))
        <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> إضافة سؤال جديد
        </a>
        @endif
    </div>

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
            @if($user && ($user->hasPermission('faqs.edit') || $user->role === 'admin'))
        <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
        @endif
    </form>
    <br>

    <form action="{{ route('admin.faq.content.update') }}" method="POST">
        @csrf

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                إعدادات محتوى الأسئلة الشائعة
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
        @if($user && ($user->hasPermission('faqs.edit') || $user->role === 'admin'))
        <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
        @endif
    </form>

        <br><hr> <br>

    <!-- Table Card -->
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table id="faqsTable" class="table table-hover w-100 text-end">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>السؤال (عربي)</th>
                            <th>السؤال (إنجليزي)</th>
                            <th>الإجابة (عربي)</th>
                            <th>الإجابة (إنجليزي)</th>
                            <th>القسم</th>
                            <th>الصفحة</th>
                            <th>الترتيب</th>
                            <th width="180">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($faqs as $faq)
                        <tr class="text-center">
                            <td>{{ $faq->question_ar }}</td>
                            <td>{{ $faq->question_en }}</td>
                            <td>{{ $faq->answer_ar }}</td>
                            <td>{{ $faq->answer_en }}</td>
                            <td>{{ $faq->category?->name_ar ?? '-' }}</td>
                            <td>
                                @if($faq->page == 'home')
                                    الصفحة الرئيسية
                                @elseif($faq->page == 'blog')
                                    المدونة
                                @elseif($faq->page == 'category')
                                    التصنيفات
                                @else
                                    {{ $faq->page }}
                                @endif
                            </td>

                            <td>{{ $faq->sort }}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    @if($user && ($user->hasPermission('faqs.edit') || $user->role === 'admin'))
                                    <a href="{{ route('admin.faqs.edit', $faq->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit me-1"></i> تعديل
                                    </a>
                                    @endif
                                    @if($user && ($user->hasPermission('faqs.delete') || $user->role === 'admin'))
                                    <form action="{{ route('admin.faqs.destroy', $faq->id) }}" method="POST" class="d-inline">
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
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script>
   $(document).ready(function() {
    $('#faqsTable').DataTable({
        responsive: true,
        order: [[4, 'desc']],
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "الكل"]],
        dom: '<"row"<"col-md-6"l><"col-md-6 text-end"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
        columnDefs: [
            { orderable: false, targets: [4] },
            { searchable: false, targets: [4] }
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
