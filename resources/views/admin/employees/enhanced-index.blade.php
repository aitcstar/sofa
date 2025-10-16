@extends('admin.layouts.app')

@section('title', 'إدارة الموظفين')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">إدارة الموظفين</h2>
        <a href="{{ route('admin.employees.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة موظف جديد
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.employees.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control"
                               placeholder="بحث بالاسم، البريد، أو الهاتف..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="role_id" class="form-select">
                            <option value="">جميع الأدوار</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->display_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">جميع الحالات</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>معطل</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> بحث
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Employees List -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الموظف</th>
                            <th>الدور</th>
                            <th>الوظيفة</th>
                            <th>الطلبات</th>

                            <th>آخر تسجيل دخول</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                        <tr>
                            <td>{{ $employee->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary text-white me-2">
                                        {{ substr($employee->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <strong>{{ $employee->name }}</strong>
                                        <small class="text-muted d-block">{{ $employee->email }}</small>
                                        <small class="text-muted d-block">{{ $employee->phone }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($employee->role)
                                    <span class="badge bg-info">{{ $employee->role->display_name }}</span>
                                @else
                                    <span class="badge bg-secondary">بدون دور</span>
                                @endif
                            </td>
                            <td>{{ $employee->job_title ?? '-' }}</td>
                            <td>
                                <div>
                                    <small><strong>إجمالي:</strong> {{ $employee->assigned_orders_count }}</small><br>
                                    <small><strong>نشط:</strong> {{ $employee->active_orders_count }}</small><br>
                                    <small><strong>مكتمل:</strong> {{ $employee->completed_orders_count }}</small>
                                </div>
                            </td>

                            <td>
                                @if($employee->last_login_at)
                                    <small>{{ $employee->last_login_at->diffForHumans() }}</small>
                                @else
                                    <small class="text-muted">لم يسجل دخول</small>
                                @endif
                            </td>
                            <td>
                                @if($employee->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">معطل</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.employees.show', $employee) }}"
                                       class="btn btn-sm btn-info" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.employees.edit', $employee) }}"
                                       class="btn btn-sm btn-warning" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('admin.employees.toggle', $employee) }}"
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-secondary"
                                                title="{{ $employee->is_active ? 'تعطيل' : 'تفعيل' }}">
                                            <i class="fas fa-{{ $employee->is_active ? 'ban' : 'check' }}"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <p class="text-muted mb-0">لا يوجد موظفون</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($employees->hasPages())
            <div class="mt-3">
                {{ $employees->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 18px;
}
</style>
@endsection

