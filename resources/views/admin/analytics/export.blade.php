@extends('admin.layouts.app')

@section('title', 'تصدير التقارير')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm p-4 rounded-4">
        <h3 class="mb-3"><i class="fas fa-file-export me-2 text-primary"></i> تصدير التقارير</h3>
        <p class="text-muted mb-4">اختر نوع التقرير وطريقة التصدير ثم اضغط <strong>تصدير الآن</strong>.</p>

        <form action="{{ route('admin.analytics.export') }}" method="GET" class="row g-3">
            <div class="col-md-6">
                <label for="type" class="form-label">نوع التقرير</label>
                <select name="type" id="type" class="form-select" required>
                    <option value="sales">تقرير المبيعات</option>
                    <option value="orders">تقرير الطلبات</option>
                    <option value="customers">تقرير العملاء</option>
                </select>
            </div>

            <div class="col-md-6">
                <label for="format" class="form-label">صيغة الملف</label>
                <select name="format" id="format" class="form-select" required>
                    <option value="excel">Excel (.xlsx)</option>
                    <option value="csv">CSV (.csv)</option>
                </select>
            </div>

            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-download me-2"></i> تصدير الآن
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
