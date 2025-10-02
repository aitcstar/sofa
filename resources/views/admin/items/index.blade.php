@extends('admin.layouts.app')

@section('title', 'إدارة القطع ')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">إدارة القطع </h1>
        <a href="{{ route('admin.items.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> إضافة قطعة جديدة
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="itemsTable" class="table table-hover w-100 text-end">
                    <thead>
                        <tr>
                            <th>الصوره </th>
                            <th>اسم القطعة</th>
                            <th>الوحدة</th>
                            <th>الكمية</th>
                            <th>المقاس</th>
                            <th>الخامة</th>
                            <th>اللون</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                        <tr data-id="{{ $item->id }}">
                            <td><img src="{{ asset('storage/'.$item->image_path) }}" class="img-fluid border rounded" style="max-width: 100px;"></td>
                            <td>{{ $item->item_name_ar }}</td>
                            <td>{{ $item->unit->name_ar ?? 'غير محدد' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->dimensions ?? '-' }}</td>
                            <td>{{ $item->material_ar }}</td>
                            <td>{{ $item->color_ar }}</td>
                            <td>
                                <a href="{{ route('admin.items.edit', $item) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger delete-item" data-item="{{ $item->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
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
    $('#itemsTable').DataTable({
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

    // حذف القطعة باستخدام AJAX
    $('#itemsTable').on('click', '.delete-item', function() {
        if (!confirm('هل أنت متأكد من حذف هذه القطعة؟')) return;

        let button = $(this);
        let itemId = button.data('item');

        $.ajax({
            url: '/admin/items/' + itemId,
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function(res) {
                if (res.success) {
                    button.closest('tr').fadeOut(300, function() { $(this).remove(); });
                    alert('تم حذف القطعة بنجاح');
                } else {
                    alert('حدث خطأ أثناء الحذف');
                }
            },
            error: function() {
                alert('حدث خطأ أثناء الحذف');
            }
        });
    });
});
</script>
@endpush
