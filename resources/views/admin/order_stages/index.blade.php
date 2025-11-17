@extends('admin.layouts.app')

@section('title', 'مراحل الطلب')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">مراحل الطلب</h1>
        <a href="{{ route('admin.order_stages.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> إضافة مرحلة جديدة
        </a>
    </div>

    <!-- Table Card -->
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table id="orderStagesTable" class="table table-hover w-100 text-end align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>#</th>
                            <th>العنوان (عربي)</th>
                            <th>العنوان (إنجليزي)</th>

                            <th>الترتيب</th>
                            <th width="180">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stages as $stage)
                            <tr class="text-center">
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $stage->title_ar }}</strong></td>
                                <td>{{ $stage->title_en }}</td>
                                <td>{{ $stage->order_number }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('admin.order_stages.edit', $stage->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit me-1"></i> تعديل
                                        </a>
                                        <form action="{{ route('admin.order_stages.destroy', $stage->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                                <i class="fas fa-trash me-1"></i> حذف
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @foreach($stage->children as $sub)
                                <tr class="text-center table-light">
                                    <td>—</td>
                                    <td>{{ $sub->title_ar }}</td>
                                    <td>{{ $sub->title_en }}</td>
                                    <td>{{ $sub->order_number }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('admin.order_stages.edit', $sub->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit me-1"></i> تعديل
                                            </a>
                                            <form action="{{ route('admin.order_stages.destroy', $sub->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                                    <i class="fas fa-trash me-1"></i> حذف
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
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
    $('#orderStagesTable').DataTable({
        responsive: true,
        order: [[5, 'asc']],
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "الكل"]],
        dom: '<"row"<"col-md-6"l><"col-md-6 text-end"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
        columnDefs: [
            { orderable: false, targets: [4] },
            { searchable: false, targets: [4] }
        ],
        language: {
            emptyTable: "لا توجد مراحل متاحة",
            info: "إظهار _START_ إلى _END_ من أصل _TOTAL_ مرحلة",
            infoEmpty: "إظهار 0 إلى 0 من أصل 0 مرحلة",
            infoFiltered: "(تمت تصفيته من أصل _MAX_ مرحلة)",
            lengthMenu: "إظهار _MENU_ مراحل",
            loadingRecords: "جارٍ التحميل...",
            processing: "جارٍ المعالجة...",
            search: "بحث:",
            zeroRecords: "لم يتم العثور على نتائج",
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
