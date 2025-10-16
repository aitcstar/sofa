@extends('admin.layouts.app')

@section('title', 'تعديل الموظف - ' . $employee->name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">تعديل الموظف</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.employees.index') }}">الموظفون</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.employees.show', $employee->id) }}">{{ $employee->name }}</a></li>
                    <li class="breadcrumb-item active">تعديل</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.employees.show', $employee->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-2"></i>رجوع
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card shadow">
        <div class="card-header bg-warning">
            <h5 class="mb-0"><i class="fas fa-edit me-2"></i>تعديل معلومات الموظف</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.employees.update', $employee->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Basic Information -->
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">المعلومات الأساسية</h6>

                        <div class="mb-3">
                            <label for="name" class="form-label">الاسم <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $employee->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email', $employee->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                   id="phone" name="phone" value="{{ old('phone', $employee->phone) }}" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">كلمة المرور الجديدة</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password">
                            <small class="text-muted">اتركه فارغاً إذا كنت لا تريد تغيير كلمة المرور</small>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                            <input type="password" class="form-control"
                                   id="password_confirmation" name="password_confirmation">
                        </div>
                    </div>

                    <!-- Role and Status -->
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">الدور والحالة</h6>

                        <div class="mb-3">
                            <label for="role_id" class="form-label">الدور <span class="text-danger">*</span></label>
                            <select class="form-select @error('role_id') is-invalid @enderror"
                                    id="role_id" name="role_id" required>
                                <option value="">اختر الدور</option>
                                @if(isset($roles))
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id', $employee->role_id) == $role->id ? 'selected' : '' }}>
                                            {{ $role->display_name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('role_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{--<div class="mb-3">
                            <label for="department" class="form-label">القسم</label>
                            <input type="text" class="form-control @error('department') is-invalid @enderror"
                                   id="department" name="department" value="{{ old('department', $employee->department) }}">
                            @error('department')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>--}}

                        <div class="mb-3">
                            <label for="position" class="form-label">المنصب</label>
                            <input type="text" class="form-control @error('position') is-invalid @enderror"
                                   id="position" name="job_title" value="{{ old('position', $employee->job_title) }}">
                            @error('position')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                       value="1" {{ old('is_active', $employee->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    الموظف نشط
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="row mt-4">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>حفظ التغييرات
                        </button>
                        <a href="{{ route('admin.employees.show', $employee->id) }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>إلغاء
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

