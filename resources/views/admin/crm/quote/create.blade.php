@extends('admin.layouts.app')

@section('title', 'إنشاء عرض سعر جديد')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">إنشاء عرض سعر جديد</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.crm.index') }}">CRM</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.crm.quotes.index') }}">عروض الأسعار</a></li>
                    <li class="breadcrumb-item active">إنشاء جديد</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.crm.quotes.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-2"></i>رجوع
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i>معلومات عرض السعر</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.crm.quotes.store') }}" method="POST">
                @csrf

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>ملاحظة:</strong> سيتم إنشاء عرض سعر أساسي. يمكنك تعديل التفاصيل بعد الإنشاء.
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="lead_id" class="form-label">العميل المحتمل</label>
                            <select class="form-select @error('lead_id') is-invalid @enderror" 
                                    id="lead_id" name="lead_id">
                                <option value="">اختر عميل محتمل</option>
                                @if(isset($leads))
                                    @foreach($leads as $lead)
                                        <option value="{{ $lead->id }}" {{ old('lead_id', request('lead_id')) == $lead->id ? 'selected' : '' }}>
                                            {{ $lead->name }} - {{ $lead->email }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('lead_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="valid_until" class="form-label">صالح حتى</label>
                            <input type="date" class="form-control @error('valid_until') is-invalid @enderror" 
                                   id="valid_until" name="valid_until" value="{{ old('valid_until', now()->addDays(30)->format('Y-m-d')) }}">
                            @error('valid_until')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="description" class="form-label">الوصف</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="subtotal" class="form-label">المبلغ الأساسي (ريال)</label>
                            <input type="number" class="form-control @error('subtotal') is-invalid @enderror" 
                                   id="subtotal" name="subtotal" value="{{ old('subtotal', 0) }}" step="0.01" min="0">
                            @error('subtotal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="tax_rate" class="form-label">نسبة الضريبة (%)</label>
                            <input type="number" class="form-control @error('tax_rate') is-invalid @enderror" 
                                   id="tax_rate" name="tax_rate" value="{{ old('tax_rate', 15) }}" step="0.01" min="0">
                            @error('tax_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="total_amount" class="form-label">المبلغ الإجمالي (ريال)</label>
                            <input type="number" class="form-control @error('total_amount') is-invalid @enderror" 
                                   id="total_amount" name="total_amount" value="{{ old('total_amount', 0) }}" step="0.01" min="0" readonly>
                            @error('total_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="row mt-4">
                    <div class="col-12">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i>إنشاء عرض السعر
                        </button>
                        <a href="{{ route('admin.crm.quotes.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>إلغاء
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Calculate total amount
    function calculateTotal() {
        const subtotal = parseFloat(document.getElementById('subtotal').value) || 0;
        const taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;
        const taxAmount = subtotal * (taxRate / 100);
        const total = subtotal + taxAmount;
        document.getElementById('total_amount').value = total.toFixed(2);
    }

    document.getElementById('subtotal').addEventListener('input', calculateTotal);
    document.getElementById('tax_rate').addEventListener('input', calculateTotal);

    // Calculate on page load
    calculateTotal();
</script>
@endpush
@endsection

