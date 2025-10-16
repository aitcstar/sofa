@extends('admin.layouts.app')

@section('title', 'إنشاء حملة تسويقية جديدة')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">إنشاء حملة تسويقية جديدة</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.marketing.index') }}">التسويق</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.marketing.campaigns.index') }}">الحملات</a></li>
                    <li class="breadcrumb-item active">إنشاء جديد</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.marketing.campaigns.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-2"></i>رجوع
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-bullhorn me-2"></i>معلومات الحملة</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.marketing.campaigns.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">اسم الحملة <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="type" class="form-label">نوع الحملة <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" 
                                    id="type" name="type" required>
                                <option value="">اختر نوع الحملة</option>
                                <option value="email" {{ old('type') == 'email' ? 'selected' : '' }}>بريد إلكتروني</option>
                                <option value="sms" {{ old('type') == 'sms' ? 'selected' : '' }}>رسائل نصية</option>
                                <option value="social" {{ old('type') == 'social' ? 'selected' : '' }}>وسائل التواصل الاجتماعي</option>
                                <option value="banner" {{ old('type') == 'banner' ? 'selected' : '' }}>إعلانات بانر</option>
                                <option value="popup" {{ old('type') == 'popup' ? 'selected' : '' }}>نوافذ منبثقة</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="mb-3">
                            <label for="description" class="form-label">الوصف</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="target_audience" class="form-label">الجمهور المستهدف</label>
                            <input type="text" class="form-control @error('target_audience') is-invalid @enderror" 
                                   id="target_audience" name="target_audience" value="{{ old('target_audience') }}"
                                   placeholder="مثال: عملاء جدد، عملاء حاليين، إلخ">
                            @error('target_audience')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="budget" class="form-label">الميزانية (ريال)</label>
                            <input type="number" class="form-control @error('budget') is-invalid @enderror" 
                                   id="budget" name="budget" value="{{ old('budget') }}" step="0.01" min="0">
                            @error('budget')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="start_date" class="form-label">تاريخ البدء <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                   id="start_date" name="start_date" value="{{ old('start_date', now()->format('Y-m-d')) }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="end_date" class="form-label">تاريخ الانتهاء <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                   id="end_date" name="end_date" value="{{ old('end_date', now()->addDays(30)->format('Y-m-d')) }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="mb-3">
                            <label for="goals" class="form-label">أهداف الحملة</label>
                            <textarea class="form-control @error('goals') is-invalid @enderror" 
                                      id="goals" name="goals" rows="3" 
                                      placeholder="مثال: زيادة المبيعات بنسبة 20%، جذب 100 عميل جديد، إلخ">{{ old('goals') }}</textarea>
                            @error('goals')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="mb-3">
                            <label for="content" class="form-label">محتوى الحملة</label>
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                      id="content" name="content" rows="5">{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">محتوى الرسالة أو الإعلان الذي سيتم إرساله</small>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="row mt-4">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>إنشاء الحملة
                        </button>
                        <a href="{{ route('admin.marketing.campaigns.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>إلغاء
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

