@extends('admin.layouts.app')

@section('title', 'إدارة الطلبات')

@section('content')
@php
$user = Auth::guard('admin')->user() ?? Auth::guard('employee')->user();
@endphp
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-shopping-cart"></i>
            إدارة الطلبات
        </h1>
        @if($user && ($user->hasPermission('orders.create') || $user->role === 'admin'))
            <div class="d-flex gap-2">
                <a href="{{ route('admin.orders.enhanced.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> طلب جديد
                </a>
            </div>
        @endif

    </div>

    <!-- Statistics Cards -->
    @if($user && ($user->hasPermission('orders.create') || $user->role === 'admin'))
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    إجمالي الطلبات
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    في الانتظار
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    قيد التنفيذ
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['processing'] ?? 0}}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-cogs fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    متأخرة
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['overdue'] ?? 0}}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter"></i> فلاتر البحث
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.orders.enhanced.index') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search">البحث</label>
                            <input type="text" class="form-control" id="search" name="search"
                                   value="{{ request('search') }}" placeholder="رقم الطلب، الاسم، البريد، الهاتف">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="status">الحالة</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">جميع الحالات</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>قيد التنفيذ</option>
                                <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>تم الشحن</option>
                                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>تم التسليم</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                                <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>مؤرشف</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="priority">الأولوية</label>
                            <select class="form-control" id="priority" name="priority">
                                <option value="">جميع الأولويات</option>
                                <option value="1" {{ request('priority') == '1' ? 'selected' : '' }}>منخفضة</option>
                                <option value="2" {{ request('priority') == '2' ? 'selected' : '' }}>عادية</option>
                                <option value="3" {{ request('priority') == '3' ? 'selected' : '' }}>متوسطة</option>
                                <option value="4" {{ request('priority') == '4' ? 'selected' : '' }}>عالية</option>
                                <option value="5" {{ request('priority') == '5' ? 'selected' : '' }}>عاجلة</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="assigned_to">المسؤول</label>
                            <select class="form-control" id="assigned_to" name="assigned_to">
                                <option value="">جميع الموظفين</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ request('assigned_to') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="payment_status">حالة الدفع</label>
                            <select class="form-control" id="payment_status" name="payment_status">
                                <option value="">جميع حالات الدفع</option>
                                <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>غير مدفوع</option>
                                <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>مدفوع جزئياً</option>
                                <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>مدفوع بالكامل</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_from">من تاريخ</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_to">إلى تاريخ</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" id="overdue" name="overdue" value="1"
                                       {{ request('overdue') ? 'checked' : '' }}>
                                <label class="form-check-label" for="overdue">
                                    الطلبات المتأخرة فقط
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" id="duplicates" name="duplicates" value="1"
                                       {{ request('duplicates') ? 'checked' : '' }}>
                                <label class="form-check-label" for="duplicates">
                                    الطلبات المكررة فقط
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.orders.enhanced.index') }}" class="btn btn-secondary mt-4">
                            <i class="fas fa-times"></i> مسح الفلاتر
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">قائمة الطلبات</h6>

        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover w-100 text-end" id="ordersTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>رقم الطلب</th>
                            <th>العميل</th>
                            <th>الباكج</th>
                            <th>الحالة</th>
                            <th>الأولوية</th>
                            <th>المسؤول</th>
                            <th>حالة الدفع</th>
                            <th>المبلغ</th>
                            <th>تاريخ الإنشاء</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr class="{{ $order->isOverdue() ? 'table-warning' : '' }} {{ $order->is_duplicate ? 'table-info' : '' }}">

                                <td>
                                    <strong>{{ $order->order_number }}</strong>
                                    @if($order->is_duplicate)
                                        <span class="badge badge-info badge-sm">مكرر</span>
                                    @endif
                                    @if($order->isOverdue())
                                        <span class="badge badge-warning badge-sm">متأخر</span>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $order->name }}</strong><br>
                                        <small class="text-muted">{{ $order->email }}</small><br>
                                        <small class="text-muted">{{ $order->phone }}</small>
                                    </div>
                                </td>
                                <td>
                                    @forelse($order->packages as $package)
                                        - {{ $package->{'name_'.app()->getLocale()} ?? $package->name ?? 'غير محدد' }}<br>
                                    @empty
                                        غير محدد
                                    @endforelse
                                    <small class="text-muted">{{ $order->units_count }} وحدة</small>
                                </td>

                                <td>
                                    <span class="badge bg-{{ $order->status_color }}">
                                        {{ $order->status_text }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $order->priority_color }}">
                                        {{ $order->priority_text }}
                                    </span>
                                </td>
                                <td>
                                    @if($order->assignedEmployee)
                                        <div>
                                            <strong>{{ $order->assignedEmployee->name }}</strong>
                                        </div>
                                    @endif
                                    @if($order->activeAssignments->count() > 0)
                                        <div class="mt-1">
                                            @foreach($order->activeAssignments as $assignment)
                                                <span class="badge bg-{{ $assignment->role_color }} badge-sm">
                                                    {{ $assignment->user->name }} ({{ $assignment->formatted_role }})
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $order->payment_status_color }}">
                                        {{ $order->payment_status_text }}
                                    </span>
                                    @if($order->payment_status == 'partial')
                                        <div class="progress mt-1" style="height: 5px;">
                                            <div class="progress-bar" role="progressbar"
                                                 style="width: {{ $order->payment_progress }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ $order->payment_progress }}%</small>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ number_format($order->total_amount, 2) }} ر.س</strong>
                                    @if($order->paid_amount > 0)
                                        <br><small class="text-success">مدفوع: {{ number_format($order->paid_amount, 2) }} ر.س</small>
                                    @endif
                                </td>
                                <td>
                                    {{ $order->created_at->format('Y-m-d') }}<br>
                                    <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.orders.enhanced.show', $order) }}"
                                           class="btn btn-sm btn-outline-primary" title="عرض التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                       <!-- <button type="button" class="btn btn-sm btn-outline-success"
                                                onclick="quickStatusUpdate({{ $order->id }})" title="تحديث الحالة">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-info"
                                                onclick="quickAssign({{ $order->id }})" title="تعيين موظف">
                                            <i class="fas fa-user-tag"></i>
                                        </button>-->
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                                    <p class="text-muted">لا توجد طلبات مطابقة للفلاتر المحددة</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>


        </div>
        @if($orders->hasPages())
        <div class="card-footer">
            {{ $orders->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>
</div>

<!-- Quick Status Update Modal -->
<div class="modal fade" id="quickStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تحديث حالة الطلب</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="quickStatusForm">
                <div class="modal-body">
                    <input type="hidden" id="statusOrderId">
                    <div class="form-group">
                        <label for="newStatus">الحالة الجديدة</label>
                        <select class="form-control" id="newStatus" required>
                            <option value="pending">في الانتظار</option>
                            <option value="confirmed">مؤكد</option>
                            <option value="processing">قيد التنفيذ</option>
                            <option value="shipped">تم الشحن</option>
                            <option value="delivered">تم التسليم</option>
                            <option value="cancelled">ملغي</option>
                            <option value="archived">مؤرشف</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="statusNotes">ملاحظات (اختياري)</label>
                        <textarea class="form-control" id="statusNotes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">تحديث</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Assign Modal -->
<div class="modal fade" id="quickAssignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تعيين موظف للطلب</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="quickAssignForm">
                <div class="modal-body">
                    <input type="hidden" id="assignOrderId">
                    <div class="form-group">
                        <label for="assignEmployee">الموظف</label>
                        <select class="form-control" id="assignEmployee" required>
                            <option value="">اختر موظف</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="assignRole">الدور</label>
                        <select class="form-control" id="assignRole" required>
                            <option value="primary">مسؤول رئيسي</option>
                            <option value="secondary">مسؤول مساعد</option>
                            <option value="designer">مصمم</option>
                            <option value="manufacturer">مصنع</option>
                            <option value="quality_controller">مراقب جودة</option>
                            <option value="shipping_coordinator">منسق شحن</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="assignNotes">ملاحظات (اختياري)</label>
                        <textarea class="form-control" id="assignNotes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">تعيين</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')


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
    $('#ordersTable').DataTable({
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


// Quick Status Update
function quickStatusUpdate(orderId) {
    $('#statusOrderId').val(orderId);
    $('#quickStatusModal').modal('show');
}

$('#quickStatusForm').on('submit', function(e) {
    e.preventDefault();

    const orderId = $('#statusOrderId').val();
    const status = $('#newStatus').val();
    const notes = $('#statusNotes').val();

    $.ajax({
        url: `/admin/orders/enhanced/${orderId}/status`,
        method: 'PUT',
        data: {
            status: status,
            notes: notes,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                location.reload();
            }
        },
        error: function(xhr) {
            const error = xhr.responseJSON?.error || 'حدث خطأ غير متوقع';
            toastr.error(error);
        }
    });

    $('#quickStatusModal').modal('hide');
});

// Quick Assign
function quickAssign(orderId) {
    $('#assignOrderId').val(orderId);
    $('#quickAssignModal').modal('show');
}

$('#quickAssignForm').on('submit', function(e) {
    e.preventDefault();

    const orderId = $('#assignOrderId').val();
    const userId = $('#assignEmployee').val();
    const role = $('#assignRole').val();
    const notes = $('#assignNotes').val();

    $.ajax({
        url: `/admin/orders/enhanced/${orderId}/assign`,
        method: 'POST',
        data: {
            user_id: userId,
            role: role,
            notes: notes,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                location.reload();
            }
        },
        error: function(xhr) {
            const error = xhr.responseJSON?.error || 'حدث خطأ غير متوقع';
            toastr.error(error);
        }
    });

    $('#quickAssignModal').modal('hide');
});

// Select All functionality
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAllCheckbox');
    const checkboxes = document.querySelectorAll('.order-checkbox');

    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

function selectAll() {
    const checkboxes = document.querySelectorAll('.order-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
    document.getElementById('selectAllCheckbox').checked = true;
}

// Bulk Actions
function bulkAction(action) {
    const selectedOrders = getSelectedOrders();

    if (selectedOrders.length === 0) {
        toastr.warning('يرجى تحديد طلب واحد على الأقل');
        return;
    }

    if (!confirm(`هل أنت متأكد من تنفيذ هذا الإجراء على ${selectedOrders.length} طلب؟`)) {
        return;
    }

    $.ajax({
        url: '{{ route("admin.orders.enhanced.bulk-action") }}',
        method: 'POST',
        data: {
            action: action,
            order_ids: selectedOrders,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                location.reload();
            }
        },
        error: function(xhr) {
            const error = xhr.responseJSON?.error || 'حدث خطأ غير متوقع';
            toastr.error(error);
        }
    });
}

function getSelectedOrders() {
    const checkboxes = document.querySelectorAll('.order-checkbox:checked');
    return Array.from(checkboxes).map(checkbox => checkbox.value);
}

// Export Orders
function exportOrders() {
    const params = new URLSearchParams(window.location.search);
    window.open(`{{ route('admin.orders.enhanced.export') }}?${params.toString()}`);
}

// Auto-submit form on filter change
$('#filterForm select, #filterForm input[type="checkbox"]').on('change', function() {
    $('#filterForm').submit();
});
</script>



@endsection
