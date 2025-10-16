@extends('admin.layouts.app')

@section('title', 'تفاصيل الدور: ' . $role->display_name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">تفاصيل الدور: {{ $role->display_name }}</h2>
        <div>
            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> تعديل
            </a>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> رجوع
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">معلومات الدور</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">اسم الدور</th>
                            <td>{{ $role->display_name }}</td>
                        </tr>
                        <tr>
                            <th>الاسم التقني</th>
                            <td><code>{{ $role->name }}</code></td>
                        </tr>
                        <tr>
                            <th>الوصف</th>
                            <td>{{ $role->description ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>الحالة</th>
                            <td>
                                @if($role->is_active)
                                    <span class="badge bg-success">مفعل</span>
                                @else
                                    <span class="badge bg-danger">معطل</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>تاريخ الإنشاء</th>
                            <td>{{ $role->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        <tr>
                            <th>آخر تحديث</th>
                            <td>{{ $role->updated_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">الصلاحيات ({{ $role->permissions->count() }})</h5>
                </div>
                <div class="card-body">
                    @php
                        $groupedPermissions = $role->permissions->groupBy('module');
                    @endphp

                    @foreach($groupedPermissions as $module => $modulePermissions)
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-folder-open"></i>
                            {{ __('modules.' . $module) != 'modules.' . $module ? __('modules.' . $module) : $module }}
                            <span class="badge bg-secondary">{{ $modulePermissions->count() }}</span>
                        </h6>
                        <div class="row">
                            @foreach($modulePermissions as $permission)
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <div>
                                        {{ $permission->display_name }}
                                        <small class="text-muted d-block">{{ $permission->name }}</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach

                    @if($role->permissions->count() === 0)
                    <p class="text-muted text-center mb-0">لا توجد صلاحيات مرتبطة بهذا الدور</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">إحصائيات</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span>عدد الصلاحيات</span>
                        <span class="badge bg-info">{{ $role->permissions->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>عدد الموظفين</span>
                        <span class="badge bg-secondary">{{ $role->employees->count() }}</span>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">الموظفون ({{ $role->employees->count() }})</h5>
                </div>
                <div class="card-body">
                    @if($role->employees->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($role->employees as $employee)
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $employee->name }}</strong>
                                    <small class="text-muted d-block">{{ $employee->email }}</small>
                                </div>
                                @if($employee->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">معطل</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-muted text-center mb-0">لا يوجد موظفون بهذا الدور</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

