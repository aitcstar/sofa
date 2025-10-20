@extends('admin.layouts.app')

@section('title', 'أسئلة الاستبيان')

@section('content')
@php
$user = Auth::guard('admin')->user() ?? Auth::guard('employee')->user();
@endphp
<div class="container">

    <!-- Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">أسئلة الاستبيان</h1>
        @if($user && ($user->hasPermission('surveys.create') || $user->role === 'admin'))
        <a href="{{ route('admin.survey-questions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> إضافة سؤال جديد
        </a>
        @endif
    </div>

    <!-- Questions Table Card -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="questionsTable" class="table table-hover table-bordered align-middle mb-0 text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>السؤال (AR)</th>
                            <th>النوع</th>
                            <th>مطلوب؟</th>
                            <th>خيارات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($questions as $question)
                            <tr>
                                <td>{{ $question->id }}</td>
                                <td>{{ $question->title_ar }}</td>
                                <td>{{ $question->type }}</td>
                                <td>
                                    @if($question->is_required)
                                        <span class="badge bg-success">نعم</span>
                                    @else
                                        <span class="badge bg-secondary">لا</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        @if($user && ($user->hasPermission('surveys.edit') || $user->role === 'admin'))
                                        <a href="{{ route('admin.survey-questions.edit', $question) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit me-1"></i> تعديل
                                        </a>
                                        @endif
                                        @if($user && ($user->hasPermission('surveys.delete') || $user->role === 'admin'))
                                        <form action="{{ route('admin.survey-questions.destroy', $question) }}" method="POST" class="d-inline">
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
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fas fa-question-circle fa-2x mb-2"></i>
                                    <p>لا توجد أسئلة حتى الآن</p>
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
        {{ $questions->links() }}
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
    $('#questionsTable').DataTable({
        responsive: true,
        order: [[0, 'desc']],
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "الكل"]],
        dom: '<"row"<"col-md-6"l><"col-md-6 text-start"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
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
            paginate: { first: "الأول", last: "الأخير", next: "التالي", previous: "السابق" },
            aria: { sortAscending: ": تفعيل لترتيب العمود تصاعدياً", sortDescending: ": تفعيل لترتيب العمود تنازلياً" }
        }
    });
});
</script>
@endpush
