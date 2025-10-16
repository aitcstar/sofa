@extends('admin.layouts.app')

@section('title', 'تعديل العميل المحتمل - ' . $lead->name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">تعديل العميل المحتمل</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.crm.index') }}">CRM</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.crm.leads.index') }}">العملاء المحتملون</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.crm.leads.show', $lead->id) }}">{{ $lead->name }}</a></li>
                    <li class="breadcrumb-item active">تعديل</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.crm.leads.show', $lead->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-2"></i>رجوع
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card shadow">
        <div class="card-header bg-warning">
            <h5 class="mb-0"><i class="fas fa-edit me-2"></i>تعديل معلومات العميل المحتمل</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.crm.leads.update', $lead->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Basic Information -->
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">المعلومات الأساسية</h6>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">الاسم <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $lead->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $lead->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone', $lead->phone) }}" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="company" class="form-label">الشركة</label>
                            <input type="text" class="form-control @error('company') is-invalid @enderror" 
                                   id="company" name="company" value="{{ old('company', $lead->company) }}">
                            @error('company')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Lead Details -->
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">تفاصيل العميل</h6>

                        <div class="mb-3">
                            <label for="source" class="form-label">المصدر <span class="text-danger">*</span></label>
                            <select class="form-select @error('source') is-invalid @enderror" 
                                    id="source" name="source" required>
                                <option value="">اختر المصدر</option>
                                <option value="website" {{ old('source', $lead->source) == 'website' ? 'selected' : '' }}>الموقع الإلكتروني</option>
                                <option value="phone" {{ old('source', $lead->source) == 'phone' ? 'selected' : '' }}>اتصال هاتفي</option>
                                <option value="email" {{ old('source', $lead->source) == 'email' ? 'selected' : '' }}>بريد إلكتروني</option>
                                <option value="social_media" {{ old('source', $lead->source) == 'social_media' ? 'selected' : '' }}>وسائل التواصل</option>
                                <option value="referral" {{ old('source', $lead->source) == 'referral' ? 'selected' : '' }}>إحالة</option>
                                <option value="other" {{ old('source', $lead->source) == 'other' ? 'selected' : '' }}>أخرى</option>
                            </select>
                            @error('source')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">الحالة <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="new" {{ old('status', $lead->status) == 'new' ? 'selected' : '' }}>جديد</option>
                                <option value="contacted" {{ old('status', $lead->status) == 'contacted' ? 'selected' : '' }}>تم التواصل</option>
                                <option value="interested" {{ old('status', $lead->status) == 'interested' ? 'selected' : '' }}>مهتم</option>
                                <option value="not_interested" {{ old('status', $lead->status) == 'not_interested' ? 'selected' : '' }}>غير مهتم</option>
                                <option value="converted" {{ old('status', $lead->status) == 'converted' ? 'selected' : '' }}>تم التحويل</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="assigned_to" class="form-label">تعيين إلى</label>
                            <select class="form-select @error('assigned_to') is-invalid @enderror" 
                                    id="assigned_to" name="assigned_to">
                                <option value="">اختر موظف</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('assigned_to', $lead->assigned_to) == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="project_type" class="form-label">نوع المشروع</label>
                            <select class="form-select @error('project_type') is-invalid @enderror" 
                                    id="project_type" name="project_type">
                                <option value="">اختر نوع المشروع</option>
                                <option value="villa" {{ old('project_type', $lead->project_type) == 'villa' ? 'selected' : '' }}>فيلا</option>
                                <option value="apartment" {{ old('project_type', $lead->project_type) == 'apartment' ? 'selected' : '' }}>شقة</option>
                                <option value="office" {{ old('project_type', $lead->project_type) == 'office' ? 'selected' : '' }}>مكتب</option>
                                <option value="commercial" {{ old('project_type', $lead->project_type) == 'commercial' ? 'selected' : '' }}>تجاري</option>
                                <option value="other" {{ old('project_type', $lead->project_type) == 'other' ? 'selected' : '' }}>أخرى</option>
                            </select>
                            @error('project_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Budget and Notes -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="budget" class="form-label">الميزانية المتوقعة (ريال)</label>
                            <input type="number" class="form-control @error('budget') is-invalid @enderror" 
                                   id="budget" name="budget" value="{{ old('budget', $lead->estimated_value) }}" step="0.01" min="0">
                            @error('budget')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="notes" class="form-label">ملاحظات</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="4">{{ old('notes', $lead->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="row mt-4">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>حفظ التغييرات
                        </button>
                        <a href="{{ route('admin.crm.leads.show', $lead->id) }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>إلغاء
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

