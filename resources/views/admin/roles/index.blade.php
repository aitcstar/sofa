@extends('admin.layouts.app')

@section('title', 'إدارة الأدوار والصلاحيات')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">إدارة الأدوار والصلاحيات</h2>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة دور جديد
        </a>
    </div>



    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>اسم الدور</th>
                            <th>الاسم التقني</th>
                            <th>الوصف</th>
                            <th>عدد الصلاحيات</th>
                            <th>عدد الموظفين</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                        <tr>
                            <td>{{ $role->id }}</td>
                            <td>
                                <strong>{{ $role->display_name }}</strong>
                            </td>
                            <td>
                                <code>{{ $role->name }}</code>
                            </td>
                            <td>{{ $role->description ?? '-' }}</td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $role->permissions_count }} صلاحية
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ $role->employees_count }} موظف
                                </span>
                            </td>
                            <td>
                                @if($role->is_active)
                                    <span class="badge bg-success">مفعل</span>
                                @else
                                    <span class="badge bg-danger">معطل</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.roles.show', $role) }}"
                                       class="btn btn-sm btn-info" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.roles.edit', $role) }}"
                                       class="btn btn-sm btn-warning" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($role->name !== 'super_admin')
                                    <form action="{{ route('admin.roles.toggle-status', $role) }}"
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-secondary"
                                                title="{{ $role->is_active ? 'تعطيل' : 'تفعيل' }}">
                                            <i class="fas fa-{{ $role->is_active ? 'ban' : 'check' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.roles.duplicate', $role) }}"
                                          method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary" title="نسخ">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </form>
                                    @if($role->employees_count == 0)
                                    <form action="{{ route('admin.roles.destroy', $role) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا الدور؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <p class="text-muted mb-0">لا توجد أدوار</p>
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

