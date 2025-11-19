@extends('admin.layouts.app')

@section('title', 'آراء العملاء')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">آراء العملاء</h1>
       <!-- <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> إضافة توصية جديدة
        </a>-->
    </div>

    <!-- Table Card -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="testimonialsTable" class="table table-hover w-100 text-end">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>الاسم</th>
                           <!-- <th>المكان</th>-->
                            <th>التقييم</th>
                            <th>الباكج</th>
                            <th>الحاله</th>
                            <th width="180">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($testimonials as $testimonial)
                        <tr class="text-center">
                            <td>{{ $testimonial->name }}</td>
                            <!--<td>{{ $testimonial->location }}</td>-->
                            <td>
                                <div class="d-flex justify-content-center align-items-center">
                                    <span class="text-warning me-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $testimonial->rating)
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </span>
                                    <span>({{ $testimonial->rating }})</span>
                                </div>
                            </td>
                            <td>{{ $testimonial->package->name_ar ?? '-' }}</td>
                            <td>
                                @if ($testimonial->status == "pending")
                                    بانتظار الموافقه
                                @else
                                        تم الموافقه
                                @endif
                            </td>
                             <!--
                            <td>
                                @if($testimonial->image)
                                    <img src="{{ asset('storage/' . $testimonial->image) }}" width="60" height="60" class="rounded-circle object-fit-cover">
                                @else
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <i class="fas fa-user text-muted"></i>
                                    </div>
                                @endif
                            </td>
                        -->
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('admin.testimonials.edit', $testimonial) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit me-1"></i> تعديل
                                    </a>
                                    <form action="{{ route('admin.testimonials.destroy', $testimonial) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                            <i class="fas fa-trash me-1"></i> حذف
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="fas fa-comment-slash fa-2x mb-3"></i>
                                <p>لا توجد توصيات حتى الآن</p>
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
    $('#testimonialsTable').DataTable({
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
