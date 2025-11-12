@extends('admin.layouts.app')

@section('title', 'رسائل اتصل بنا')

@section('content')
@php
$user = Auth::guard('admin')->user() ?? Auth::guard('employee')->user();
@endphp
<div class="container">
    <!-- Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">رسائل اتصل بنا</h1>
    </div>

    <div class="mb-3 text-start">
        <a href="{{ route('admin.contact.export') }}" class="btn btn-secondary">
            <i class="fas fa-file-export"></i> تصدير البيانات
        </a>
    </div>

    <!-- Table Card -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="contactsTable" class="table table-hover w-100 text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>البريد</th>
                            <th>الجوال</th>
                            <th>الحالة</th>
                            <th width="180">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($messages as $msg)
                        <tr>
                            <td>{{ $msg->id }}</td>
                            <td>{{ $msg->name }}</td>
                            <td>{{ $msg->email }}</td>
                            <td style="direction: ltr;">{{ '+'. $msg->country_code . $msg->phone  }}</td>
                            <td>

                                @php
                                $statusClasses = [
                                    'new' => 'warning',
                                    'contacted' => 'success',
                                    'no_response' => 'secondary',
                                    'in_progress' => 'primary',
                                ];

                                $statusLabels = [
                                    'new' => 'جديدة',
                                    'contacted' => 'تم التواصل',
                                    'no_response' => 'لم يتم الرد',
                                    'in_progress' => 'قيد المتابعة',
                                ];
                            @endphp

                            <span class="badge bg-{{ $statusClasses[$msg->status] ?? 'secondary' }}">
                                {{ $statusLabels[$msg->status] ?? 'غير محددة' }}
                            </span>



                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    @if($user && ($user->hasPermission('contacts.show') || $user->role === 'admin'))
                                    <a href="{{ route('admin.contacts.show', $msg) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye me-1"></i> عرض
                                    </a>
                                    @endif
                                    @if($user && ($user->hasPermission('contacts.delete') || $user->role === 'admin'))
                                    <form action="{{ route('admin.contacts.destroy', $msg) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"  onclick="return confirm('هل أنت متأكد من الحذف؟')">
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
                                <i class="fas fa-envelope-open-text fa-2x mb-3"></i>
                                <p>لا توجد رسائل حتى الآن</p>
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
    $('#contactsTable').DataTable({
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
