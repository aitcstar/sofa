@extends('admin.layouts.app')

@section('title', 'الوحدات')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">الوحدات</h1>
        <a href="{{ route('admin.units.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> إنشاء وحدة جديدة
        </a>
    </div>

    <!-- Table Card -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="unitsTable" class="table table-hover w-100 text-end">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>الاسم (AR)</th>
                            <th>الاسم (EN)</th>
                            <th>النوع</th>
                            <th width="180">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($units as $unit)
                        <tr class="text-center">
                            <td>{{ $unit->name_ar }}</td>
                            <td>{{ $unit->name_en }}</td>
                            <td>
                                @if($unit->type == 'bedroom')
                                     غرفة نوم
                                @elseif ($unit->type == 'living_room')
                                     غرفة معيشة
                                @elseif ($unit->type == 'kitchen')
                                مطبخ
                                @elseif ($unit->type == 'bathroom')
                                حمام
                                @elseif ($unit->type == 'external')
                                الملحقات الخارجية والإضافية
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('admin.units.edit', $unit) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit me-1"></i> تعديل
                                    </a>
                                    <form action="{{ route('admin.units.destroy', $unit) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('هل تريد حذف الوحدة؟')">
                                            <i class="fas fa-trash me-1"></i> حذف
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                <i class="fas fa-box-open fa-2x mb-3"></i>
                                <p>لا توجد وحدات حتى الآن</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- رسالة تأكيد الحذف --}}
                @if(session('confirm_delete'))
                    @php $data = session('confirm_delete'); @endphp
                    <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
                        <strong>تنبيه:</strong> {{ $data['message'] }}
                        <form action="{{ route('admin.units.destroy', $data['unit_id']) }}" method="POST" class="d-inline ms-2">
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
    $('#unitsTable').DataTable({
        responsive: true,
        order: [[0, 'desc']],
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "الكل"]],
        dom: '<"row"<"col-md-6"l><"col-md-6 text-start"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
        columnDefs: [
            { orderable: false, targets: [3] },
            { searchable: false, targets: [3] }
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
