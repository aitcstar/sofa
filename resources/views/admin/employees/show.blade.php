@extends('admin.layouts.app')

@section('title', 'تفاصيل الموظف - ' . $employee->name)

@section('content')
@php
$user = Auth::guard('admin')->user() ?? Auth::guard('employee')->user();
@endphp
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.employees.index') }}">الموظفين</a></li>
                    <li class="breadcrumb-item active">{{ $employee->name }}</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user"></i>
                تفاصيل الموظف
            </h1>
        </div>
        <div class="d-flex gap-2">
            @if($user && ($user->hasPermission('employees.edit') || $user->role === 'admin'))
            <a href="{{ route('admin.employees.edit', $employee) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> تعديل
            </a>
            @endif
            @if($user && ($user->hasPermission('employees.permissions') || $user->role === 'admin'))
            <a href="{{ route('admin.employees.permissions.matrix', $employee) }}" class="btn btn-info">
                <i class="fas fa-key"></i> الصلاحيات
            </a>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Employee Info -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات الموظف</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">

                        <h4 class="mb-1">{{ $employee->name }}</h4>
                        <p class="text-muted mb-2">{{ $employee->job_title ?? 'غير محدد' }}</p>
                        <span class="badge badge-{{ $employee->is_active ? 'success' : 'danger' }} badge-lg">
                            {{ $employee->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tr>
                                <td class="font-weight-bold">رقم الموظف:</td>
                                <td>{{ $employee->id ?? 'غير محدد' }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">البريد الإلكتروني:</td>
                                <td>{{ $employee->email }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">الهاتف:</td>
                                <td>{{ $employee->phone ?? 'غير محدد' }}</td>
                            </tr>
                            {{--
                            <tr>
                                <td class="font-weight-bold">القسم:</td>
                                <td>{{ $employee->department ?? 'غير محدد' }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">تاريخ التوظيف:</td>
                                <td>{{ $employee->hire_date ? $employee->hire_date->format('Y-m-d') : 'غير محدد' }}</td>
                            </tr>
                            @if($employee->salary)
                            <tr>
                                <td class="font-weight-bold">الراتب:</td>
                                <td>{{ number_format($employee->salary, 2) }} ريال</td>
                            </tr>
                            @endif--}}
                        </table>
                    </div>
                </div>
            </div>

            <!-- Performance Summary -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">ملخص الأداء</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-right">
                                <h4 class="text-primary">{{ $performance['completion_rate'] }}%</h4>
                                <small class="text-muted">معدل الإنجاز</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success">{{ $performance['on_time_delivery_rate'] }}%</h4>
                            <small class="text-muted">التسليم في الوقت</small>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-right">
                                <h4 class="text-info">{{ $performance['average_completion_days'] }}</h4>
                                <small class="text-muted">متوسط أيام الإنجاز</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-warning">{{ $performance['customer_satisfaction'] }}%</h4>
                            <small class="text-muted">رضا العملاء</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics and Orders -->
        <div class="col-xl-8 col-lg-7">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        إجمالي الطلبات
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_orders'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
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
                                        طلبات نشطة
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_orders'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        طلبات مكتملة
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['completed_orders'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                        طلبات متأخرة
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['overdue_orders'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">الطلبات الحديثة</h6>
                   <!-- <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#assignOrdersModal">
                        <i class="fas fa-plus"></i> تعيين طلبات
                    </button>-->
                </div>
                <div class="card-body">
                    @if($employee->assignedOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>رقم الطلب</th>
                                        <th>العميل</th>
                                        <th>الباكج</th>
                                        <th>الحالة</th>
                                        <th>تاريخ الإنشاء</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employee->assignedOrders->where('is_active',1) as $order)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.orders.enhanced.show', $order) }}" class="text-primary">
                                                    {{ $order->order->order_number }}
                                                </a>
                                            </td>
                                            <td>{{ $order->user->name }}</td>
                                            <td>{{ $order->order->package->name_ar ?? 'غير محدد' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $order->order->getStatusColorAttribute() }}">
                                                    {{ $order->order->getStatusTextAttribute() }}
                                                </span>
                                            </td>
                                            <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                <a href="{{ route('admin.orders.enhanced.show', $order) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-clipboard-list fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-600">لا توجد طلبات مُعيَّنة</h5>
                            <p class="text-gray-500">لم يتم تعيين أي طلبات لهذا الموظف بعد.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">النشاطات الأخيرة</h6>
                </div>
                <div class="card-body">
                    @if(count($recentActivities) > 0)
                        <div class="timeline">
                            @foreach($recentActivities as $activity)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        {{--<h6 class="timeline-title">{{ $activity['action'] ?? 'نشاط' }}</h6>--}}
                                        <p class="timeline-text">{{ $activity['description'] ?? 'وصف النشاط' }}</p>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($activity['created_at'])->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-history fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-600">لا توجد نشاطات</h5>
                            <p class="text-gray-500">لم يتم تسجيل أي نشاطات لهذا الموظف بعد.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Orders Modal -->
<div class="modal fade" id="assignOrdersModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تعيين طلبات للموظف</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.employees.assignments', $employee) }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>الطلبات المتاحة للتعيين:</label>
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" id="selectAll">
                                        </th>
                                        <th>رقم الطلب</th>
                                        <th>العميل</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $availableOrders = \App\Models\Order::whereNull('assigned_to')
                                            ->with(['user', 'package'])
                                            ->orderBy('created_at', 'desc')
                                            ->limit(20)
                                            ->get();
                                    @endphp
                                    @foreach($availableOrders as $order)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="order_ids[]" value="{{ $order->id }}" class="order-checkbox">
                                            </td>
                                            <td>{{ $order->order_number }}</td>
                                            <td>{{ $order->user->name }}</td>
                                            <td>
                                                <span class="badge badge-{{ $order->status_color }}">
                                                    {{ $order->status_text }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">تعيين الطلبات</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -30px;
    top: 17px;
    width: 2px;
    height: calc(100% + 5px);
    background-color: #e3e6f0;
}

.timeline-title {
    margin-bottom: 5px;
    font-size: 14px;
}

.timeline-text {
    margin-bottom: 5px;
    font-size: 13px;
    color: #6c757d;
}

.avatar-lg {
    width: 80px;
    height: 80px;
}

.avatar-title {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
}

.badge-lg {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}
</style>
@endsection

@section('scripts')
<script>
// Select all checkbox functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.order-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Update select all when individual checkboxes change
document.querySelectorAll('.order-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const allCheckboxes = document.querySelectorAll('.order-checkbox');
        const checkedCheckboxes = document.querySelectorAll('.order-checkbox:checked');
        const selectAllCheckbox = document.getElementById('selectAll');

        selectAllCheckbox.checked = allCheckboxes.length === checkedCheckboxes.length;
        selectAllCheckbox.indeterminate = checkedCheckboxes.length > 0 && checkedCheckboxes.length < allCheckboxes.length;
    });
});
</script>
@endsection
