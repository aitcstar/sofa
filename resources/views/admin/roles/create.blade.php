@extends('admin.layouts.app')

@section('title', 'إضافة دور جديد')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">إضافة دور جديد</h2>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> رجوع
        </a>
    </div>

    <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf

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
                                   id="display_name" name="display_name" value="{{ old('display_name') }}" required>
                            @error('display_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">الاسم التقني (بالإنجليزية) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}"
                                   placeholder="مثال: sales_manager" required>
                            <small class="text-muted">استخدم أحرف صغيرة وشرطة سفلية فقط</small>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">الوصف</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">الصلاحيات</h5>
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
                                        <input class="form-check-input" type="checkbox"
                                               name="permissions[]" value="{{ $permission->id }}"
                                               id="permission_{{ $permission->id }}"
                                               {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="permission_{{ $permission->id }}">
                                            {{ $permission->display_name }}
                                            <small class="text-muted d-block">{{ $permission->name }}</small>
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
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">الإعدادات</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox"
                                       id="is_active" name="is_active"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    تفعيل الدور
                                </label>
                            </div>
                        </div>

                        <hr>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ الدور
                            </button>
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                                إلغاء
                            </a>
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
// Auto-generate technical name from display name
document.getElementById('display_name').addEventListener('input', function(e) {
    const nameInput = document.getElementById('name');
    if (!nameInput.value || nameInput.dataset.autoGenerated) {
        const technicalName = e.target.value
            .toLowerCase()
            .replace(/\s+/g, '_')
            .replace(/[^a-z0-9_]/g, '');
        nameInput.value = technicalName;
        nameInput.dataset.autoGenerated = 'true';
    }
});

document.getElementById('name').addEventListener('input', function() {
    delete this.dataset.autoGenerated;
});
</script>
@endpush

