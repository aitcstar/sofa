@extends('admin.layouts.app')

@section('title', 'لوحة التحكم المالية')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">لوحة التحكم المالية</h1>

    <div class="row g-3">
        <div class="col-md-3">
            <div class="card text-white bg-primary h-100 p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5>إجمالي الإيرادات</h5>
                        <h3 style="color: #fff">{{ number_format($stats['total_revenue'] ?? 0, 2) }} <small>ج.م</small></h3>
                    </div>
                    <i class="fe fe-dollar-sign fa-2x"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-success h-100 p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6>إيرادات هذا الشهر</h6>
                        <h3>{{ number_format($stats['monthly_revenue'] ?? 0, 2) }} <small>ج.م</small></h3>
                    </div>
                    <i class="fe fe-calendar fa-2x"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-warning h-100 p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6>الفواتير المعلقة</h6>
                        <h3>{{ $stats['pending_invoices'] ?? 0 }}</h3>
                    </div>
                    <i class="fe fe-file-text fa-2x"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-danger h-100 p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6>المدفوعات المعلقة</h6>
                        <h3>{{ number_format($stats['pending_payments'] ?? 0, 2) }}</h3>
                    </div>
                    <i class="fe fe-credit-card fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

    <h3 class="mt-5 mb-3">أحدث الفواتير</h3>
    @include('admin.financial.invoices.partials.table', ['invoices' => $recentInvoices])

    <h3 class="mt-5 mb-3">أحدث المدفوعات</h3>
    @include('admin.financial.payments.partials.table', ['payments' => $recentPayments])
</div>
@endsection
