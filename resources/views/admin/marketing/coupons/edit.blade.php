@extends('admin.layouts.app')

@section('title', 'تعديل الكوبون - ' . $coupon->code)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">تعديل الكوبون: {{ $coupon->code }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.marketing.index') }}">التسويق</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.marketing.coupons.index') }}">الكوبونات</a></li>
                    <li class="breadcrumb-item active">تعديل</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.marketing.coupons.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-2"></i>رجوع
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-edit me-2"></i>تعديل معلومات الكوبون</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.marketing.coupons.update', $coupon) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="code" class="form-label">كود الكوبون <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                   id="code" name="code" value="{{ old('code', $coupon->code) }}" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">اسم الكوبون</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $coupon->name) }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="type" class="form-label">نوع الخصم <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror"
                                    id="type" name="type" required>
                                <option value="">اختر نوع الخصم</option>
                                <option value="percentage" {{ old('type', $coupon->type) == 'percentage' ? 'selected' : '' }}>نسبة مئوية</option>
                                <option value="fixed_amount" {{ old('type', $coupon->type) == 'fixed_amount' ? 'selected' : '' }}>مبلغ ثابت</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="value" class="form-label">قيمة الخصم <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('value') is-invalid @enderror"
                                   id="value" name="value" value="{{ old('value', $coupon->value) }}" step="0.01" min="0" required>
                            @error('value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="mb-3">
                            <label for="description" class="form-label">الوصف</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3">{{ old('description', $coupon->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="valid_from" class="form-label">صالح من <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('starts_at') is-invalid @enderror"
                                   id="valid_from" name="starts_at" value="{{ old('starts_at', $coupon->starts_at ? $coupon->starts_at->format('Y-m-d') : '') }}" required>
                            @error('valid_from')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="expires_at" class="form-label">صالح حتى <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('expires_at') is-invalid @enderror"
                                   id="valid_until" name="expires_at" value="{{ old('expires_at', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : '') }}" required>
                            @error('valid_until')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="max_uses" class="form-label">الحد الأقصى للاستخدام</label>
                            <input type="number" class="form-control @error('max_uses') is-invalid @enderror"
                                   id="max_uses" name="max_uses" value="{{ old('max_uses', $coupon->max_uses) }}" min="1">
                            @error('max_uses')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="max_uses_per_user" class="form-label">الحد الأقصى للاستخدام لكل مستخدم</label>
                            <input type="number" class="form-control @error('max_uses_per_user') is-invalid @enderror"
                                   id="max_uses_per_user" name="max_uses_per_user" value="{{ old('max_uses_per_user', $coupon->max_uses_per_user) }}" min="1">
                            @error('max_uses_per_user')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="min_purchase_amount" class="form-label">الحد الأدنى لقيمة الشراء (ريال)</label>
                            <input type="number" class="form-control @error('min_purchase_amount') is-invalid @enderror"
                                   id="min_purchase_amount" name="min_purchase_amount" value="{{ old('min_purchase_amount', $coupon->min_purchase_amount) }}" step="0.01" min="0">
                            @error('min_purchase_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="max_discount_amount" class="form-label">الحد الأقصى للخصم (ريال)</label>
                            <input type="number" class="form-control @error('max_discount_amount') is-invalid @enderror"
                                   id="max_discount_amount" name="max_discount_amount" value="{{ old('max_discount_amount', $coupon->max_discount_amount) }}" step="0.01" min="0">
                            @error('max_discount_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    الكوبون نشط
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="row mt-4">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>حفظ التعديلات
                        </button>
                        <a href="{{ route('admin.marketing.coupons.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>إلغاء
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

