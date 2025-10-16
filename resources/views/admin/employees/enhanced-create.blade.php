@extends('admin.layouts.app')

@section('title', 'إضافة موظف جديد')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">إضافة موظف جديد</h2>
        <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> رجوع
        </a>
    </div>

    <form action="{{ route('admin.employees.store') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">المعلومات الأساسية</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">رقم الجوال <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone') }}"
                                       placeholder="05xxxxxxxx" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="job_title" class="form-label">المسمى الوظيفي <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('job_title') is-invalid @enderror"
                                       id="job_title" name="job_title" value="{{ old('job_title') }}" required>
                                @error('job_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">معلومات الحساب</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">كلمة المرور <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password" required>
                                <small class="text-muted">يجب أن تكون 8 أحرف على الأقل</small>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                                <input type="password" class="form-control"
                                       id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">الدور والصلاحيات</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="role_id" class="form-label">الدور <span class="text-danger">*</span></label>
                            <select class="form-select @error('role_id') is-invalid @enderror"
                                    id="role_id" name="role_id" required>
                                <option value="">اختر الدور</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->display_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="role-permissions" class="mt-3">
                            <!-- سيتم عرض الصلاحيات هنا عند اختيار الدور -->
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">الحالة</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                   id="is_active" name="is_active"
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                تفعيل الحساب
                            </label>
                        </div>
                        <small class="text-muted">
                            إذا كان الحساب معطلاً، لن يتمكن الموظف من تسجيل الدخول
                        </small>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ الموظف
                            </button>
                            <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">
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
// عرض صلاحيات الدور عند الاختيار
document.getElementById('role_id').addEventListener('change', function() {
    const roleId = this.value;
    const permissionsDiv = document.getElementById('role-permissions');

    if (!roleId) {
        permissionsDiv.innerHTML = '';
        return;
    }

    // هنا يمكن عمل AJAX request لجلب صلاحيات الدور
    // أو يمكن تمرير البيانات مباشرة من Controller
    permissionsDiv.innerHTML = `
        <div class="alert alert-info">
            <small><i class="fas fa-info-circle"></i> سيتم منح الموظف جميع صلاحيات الدور المحدد</small>
        </div>
    `;
});
</script>
@endpush

