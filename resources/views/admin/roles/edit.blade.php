@extends('admin.layouts.app')

@section('title', 'تعديل الدور: ' . $role->display_name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">تعديل الدور: {{ $role->display_name }}</h2>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> رجوع
        </a>
    </div>

    <form action="{{ route('admin.roles.update', $role) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">معلومات الدور</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="display_name" class="form-label">اسم الدور <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('display_name') is-invalid @enderror"
                                   id="display_name" name="display_name"
                                   value="{{ old('display_name', $role->display_name) }}" required>
                            @error('display_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">الاسم التقني (بالإنجليزية) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $role->name) }}"
                                   {{ $role->name === 'super_admin' ? 'readonly' : '' }} required>
                            @if($role->name === 'super_admin')
                                <small class="text-muted">لا يمكن تعديل الاسم التقني لدور مدير النظام</small>
                            @endif
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">الوصف</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3">{{ old('description', $role->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">الصلاحيات</h5>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                                تحديد الكل
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">
                                إلغاء التحديد
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @foreach($permissions as $module => $modulePermissions)
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-folder-open"></i>
                                {{ __('modules.' . $module) != 'modules.' . $module ? __('modules.' . $module) : $module }}
                            </h6>
                            <div class="row">
                                @foreach($modulePermissions as $permission)
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input permission-checkbox" type="checkbox"
                                               name="permissions[]" value="{{ $permission->id }}"
                                               id="permission_{{ $permission->id }}"
                                               {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="permission_{{ $permission->id }}">
                                            {{ $permission->display_name }}
                                            <small class="text-muted d-block">&nbsp;</small>
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">الإعدادات</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox"
                                       id="is_active" name="is_active"
                                       {{ old('is_active', $role->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    تفعيل الدور
                                </label>
                            </div>
                        </div>

                        <hr>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ التغييرات
                            </button>
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                                إلغاء
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">إحصائيات</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <strong>عدد الموظفين:</strong>
                            <span class="badge bg-secondary float-end">{{ $role->employees->count() }}</span>
                        </div>
                        <div class="mb-2">
                            <strong>عدد الصلاحيات:</strong>
                            <span class="badge bg-info float-end" id="selected-count">{{ count($rolePermissions) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function selectAll() {
    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
    updateCount();
}

function deselectAll() {
    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    updateCount();
}

function updateCount() {
    const count = document.querySelectorAll('.permission-checkbox:checked').length;
    document.getElementById('selected-count').textContent = count;
}

document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateCount);
});
</script>
@endpush

