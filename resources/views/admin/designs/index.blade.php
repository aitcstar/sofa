@extends('admin.layouts.app')

@section('title', 'إدارة التصاميم')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">إدارة التصاميم</h1>
        <a href="{{ route('admin.designs.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> إضافة تصميم جديد
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="designsTable" class="table table-hover w-100 text-end">
                    <thead>
                        <tr>
                            <th>الاسم</th>
                            <th>الفئة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($designs as $design)
                        <tr>
                            <td>{{ $design->name_ar }} </td>
                            <td>
                                @if($design->category  == 'bedroom')
                                غرفة نوم
                                @elseif ($design->category  == 'living_room')
                                معيشة
                                @elseif ($design->category  == 'kitchen')
                                مطبخ
                                @elseif ($design->category  == 'bathroom')
                                حمام
                                @elseif ($design->category  == 'external')
                                الملحقات الخارجية والإضافية
                                @endif

                            </td>

                            <td>
                                <a href="{{ route('admin.designs.edit', $design) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.designs.destroy', $design) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد؟')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
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

@push('scripts')
<script>
$(document).ready(function() {
    $('#designsTable').DataTable({
        responsive: true,
        order: [[0, 'asc']],
        pageLength: 10,
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
