@extends('admin.layouts.app')

@section('title', 'إدارة القطع')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">إدارة القطع</h1>
        <a href="{{ route('admin.items.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> إضافة قطعة جديدة
        </a>
    </div>

    <!-- Table Card -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="itemsTable" class="table table-hover w-100 text-end">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>الصورة</th>
                            <th>اسم القطعة</th>
                            <th>الوحدة</th>
                            <th>الكمية</th>
                            <th>المقاس</th>
                            <th>الخامة</th>
                            <th>اللون</th>
                            <th width="180">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                        <tr class="text-center">
                            <td><img src="{{ asset('storage/'.$item->image_path) }}" class="img-fluid border rounded" style="max-width: 100px;"></td>
                            <td>{{ $item->item_name_ar }}</td>
                            <td>{{ $item->unit->name_ar ?? 'غير محدد' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->dimensions ?? '-' }}</td>
                            <td>{{ $item->material_ar }}</td>
                            <td>{{ $item->color_ar }}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('admin.items.edit', $item) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit me-1"></i> تعديل
                                    </a>
                                    <form action="{{ route('admin.items.destroy', $item) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('هل تريد حذف القطعة؟')">
                                            <i class="fas fa-trash me-1"></i> حذف
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fas fa-box-open fa-2x mb-3"></i>
                                <p>لا توجد قطع حتى الآن</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- رسالة تأكيد الحذف إن كانت القطعة مرتبطة ببكاج --}}
                @if(session('confirm_delete'))
                    @php $data = session('confirm_delete'); @endphp
                    <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
                        <strong>تنبيه:</strong> {{ $data['message'] }}
                        <form action="{{ route('admin.items.destroy', $data['item_id']) }}" method="POST" class="d-inline ms-2">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="force_delete" value="1">
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash-alt me-1"></i> تأكيد الحذف
                            </button>
                        </form>
                        <button type="button" class="btn btn-secondary btn-sm ms-2" onclick="this.closest('.alert').remove();">
                            <i class="fas fa-times"></i> إلغاء
                        </button>
                    </div>
                @endif

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
    $('#itemsTable').DataTable({
        responsive: true,
        order: [[1, 'desc']],
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "الكل"]],
        dom: '<"row"<"col-md-6"l><"col-md-6 text-start"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
        columnDefs: [
            { orderable: false, targets: [7] },
            { searchable: false, targets: [7] }
        ],
        language: {
            emptyTable: "لا توجد بيانات متاحة في الجدول",
            info: "إظهار _START_ إلى _END_ من أصل _TOTAL_ مدخل",
            infoEmpty: "إظهار 0 إلى 0 من أصل 0 مدخل",
            infoFiltered: "(تمت تصفيته من أصل _MAX_ مدخل)",
            lengthMenu: "إظهار _MENU_ مدخلات",
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
