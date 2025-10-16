@extends('admin.layouts.app')

@section('title', 'إدارة الموظفين')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-users"></i>
            إدارة الموظفين
        </h1>
        @permission('create_employees')
        <a href="{{ route('admin.employees.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة موظف جديد
        </a>
        @endpermission
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">البحث والفلترة</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.employees.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search">البحث</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="اسم، إيميل، هاتف، رقم الموظف...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="status">الحالة</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">جميع الحالات</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="department">القسم</label>
                            <select class="form-control" id="department" name="department">
                                <option value="">جميع الأقسام</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department }}" {{ request('department') === $department ? 'selected' : '' }}>
                                        {{ $department }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="d-flex">
                                <button type="submit" class="btn btn-primary mr-2">
                                    <i class="fas fa-search"></i> بحث
                                </button>
                                <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> إعادة تعيين
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Employees Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">قائمة الموظفين ({{ $employees->total() }})</h6>
            <div class="d-flex gap-2">
                @permission('view_employee_performance')
                <a href="{{ route('admin.employees.workload') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-chart-bar"></i> عبء العمل
                </a>
                @endpermission
                @permission('manage_permissions')
                <a href="{{ route('admin.permissions.index') }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-key"></i> الصلاحيات
                </a>
                @endpermission
            </div>
        </div>
        <div class="card-body">
            @if($employees->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>الموظف</th>
                                <th>القسم</th>
                                <th>المنصب</th>
                                <th>الطلبات</th>
                                <th>الأداء</th>
                                <th>الحالة</th>
                                <th>تاريخ التوظيف</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm mr-3">
                                                <div class="avatar-title bg-primary text-white rounded-circle">
                                                    {{ substr($employee->name, 0, 1) }}
                                                </div>
                                            </div>
                                            <div>
                                                <strong>{{ $employee->name }}</strong><br>
                                                <small class="text-muted">{{ $employee->email }}</small><br>
                                                @if($employee->employee_id)
                                                    <small class="text-muted">ID: {{ $employee->employee_id }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $employee->department ?? 'غير محدد' }}</td>
                                    <td>{{ $employee->position ?? 'غير محدد' }}</td>
                                    <td>
                                        <div class="text-center">
                                            <div class="text-primary font-weight-bold">{{ $employee->active_orders_count }}</div>
                                            <small class="text-muted">نشط</small>
                                        </div>
                                        <div class="text-center mt-1">
                                            <div class="text-success">{{ $employee->completed_orders_count }}</div>
                                            <small class="text-muted">مكتمل</small>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $completionRate = $employee->assigned_orders_count > 0 
                                                ? round(($employee->completed_orders_count / $employee->assigned_orders_count) * 100, 1)
                                                : 0;
                                            $performanceColor = $completionRate >= 80 ? 'success' : ($completionRate >= 60 ? 'warning' : 'danger');
                                        @endphp
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-{{ $performanceColor }}" 
                                                 style="width: {{ $completionRate }}%">
                                                {{ $completionRate }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $employee->is_active ? 'success' : 'danger' }}">
                                            {{ $employee->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $employee->hire_date ? $employee->hire_date->format('Y-m-d') : 'غير محدد' }}
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @permission('view_employees')
                                            <a href="{{ route('admin.employees.show', $employee) }}" 
                                               class="btn btn-sm btn-outline-primary" title="عرض التفاصيل">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endpermission
                                            
                                            @permission('edit_employees')
                                            <a href="{{ route('admin.employees.edit', $employee) }}" 
                                               class="btn btn-sm btn-outline-warning" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endpermission
                                            
                                            @permission('manage_permissions')
                                            <a href="{{ route('admin.permissions.show', $employee) }}" 
                                               class="btn btn-sm btn-outline-info" title="الصلاحيات">
                                                <i class="fas fa-key"></i>
                                            </a>
                                            @endpermission
                                            
                                            @permission('delete_employees')
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="confirmDelete({{ $employee->id }})" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endpermission
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $employees->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">لا توجد موظفين</h5>
                    <p class="text-gray-500">لم يتم العثور على أي موظفين مطابقين لمعايير البحث.</p>
                    @permission('create_employees')
                    <a href="{{ route('admin.employees.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إضافة موظف جديد
                    </a>
                    @endpermission
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تأكيد الحذف</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                هل أنت متأكد من رغبتك في حذف هذا الموظف؟ هذا الإجراء لا يمكن التراجع عنه.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function confirmDelete(employeeId) {
    const form = document.getElementById('deleteForm');
    form.action = `{{ route('admin.employees.index') }}/${employeeId}`;
    $('#deleteModal').modal('show');
}

// Auto-submit form on filter change
document.getElementById('status').addEventListener('change', function() {
    this.form.submit();
});

document.getElementById('department').addEventListener('change', function() {
    this.form.submit();
});
</script>
@endsection
