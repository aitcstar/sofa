@extends('admin.layouts.app')

@section('title', 'إدارة الفواتير')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">إدارة الفواتير</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.financial.index') }}">المالية</a></li>
                    <li class="breadcrumb-item active">الفواتير</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.financial.invoices.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>إنشاء فاتورة جديدة
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>تصفية الفواتير</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.financial.invoices.index') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-select">
                            <option value="">الكل</option>
                            <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>غير مدفوعه</option>
                            <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>دفعة أولى</option>
                            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>مدفوع</option>
                            <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>متأخر</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>ملغي</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">من تاريخ</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">بحث</label>
                        <input type="text" name="search" class="form-control" placeholder="رقم الفاتورة أو اسم العميل" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>بحث
                        </button>
                        <a href="{{ route('admin.financial.invoices.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo me-2"></i>إعادة تعيين
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">إجمالي الفواتير</h6>
                            <h3 class="mb-0">{{ $invoices->total() }}</h3>
                        </div>
                        <div class="fs-2 text-primary">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">فواتير مدفوعة</h6>
                            <h3 class="mb-0">{{ $invoices->where('status', 'paid')->count() }}</h3>
                        </div>
                        <div class="fs-2 text-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">فواتير غير مدفوعه</h6>
                            <h3 class="mb-0">{{ $invoices->where('status', 'unpaid')->count() }}</h3>
                        </div>
                        <div class="fs-2 text-warning">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">فواتير متأخرة</h6>
                            <h3 class="mb-0">{{ $invoices->where('status', 'overdue')->count() }}</h3>
                        </div>
                        <div class="fs-2 text-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>قائمة الفواتير</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>رقم الفاتورة</th>
                            <th>الطلب</th>
                            <th>العميل</th>
                            <th>تاريخ الإصدار</th>
                            <th>تاريخ الاستحقاق</th>
                            <th>المبلغ الإجمالي</th>
                            <th>المبلغ المدفوع</th>
                            <th>المتبقي</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                        <tr>
                            <td>
                                <strong>{{ $invoice->invoice_number }}</strong>
                            </td>
                            <td>
                                <a href="{{ route('admin.orders.enhanced.show', $invoice->order_id) }}">
                                    {{ $invoice->order->order_number ?? '-' }}
                                </a>
                            </td>
                            <td>{{ $invoice->order->name ?? '-' }}</td>
                            <td>{{ $invoice->issue_date ? $invoice->issue_date->format('Y-m-d') : '-' }}</td>
                            <td>
                                @if($invoice->due_date)
                                    @if($invoice->due_date->isPast() && $invoice->status !== 'paid')
                                        <span class="text-danger">
                                            <i class="fas fa-exclamation-circle me-1"></i>
                                            {{ $invoice->due_date->format('Y-m-d') }}
                                        </span>
                                    @else
                                        {{ $invoice->due_date->format('Y-m-d') }}
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td><strong>{{ number_format($invoice->total_amount, 2) }} ريال</strong></td>
                            <td><span class="text-success">{{ number_format($invoice->paid_amount ?? 0, 2) }} ريال</span></td>
                            <td><span class="text-danger">{{ number_format($invoice->total_amount - ($invoice->paid_amount ?? 0), 2) }} ريال</span></td>
                            <td>
                                @php
                                    $statusColors = [
                                        'unpaid' => 'warning',
                                        'partial' => 'info',
                                        'paid' => 'success',
                                        'overdue' => 'danger',
                                        'cancelled' => 'secondary'
                                    ];
                                    $statusTexts = [
                                        'unpaid' => 'غير مدفوعه',
                                        'partial' => 'دفعة أولى',
                                        'paid' => 'مدفوع',
                                        'overdue' => 'متأخر',
                                        'cancelled' => 'ملغي'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$invoice->status] ?? 'secondary' }}">
                                    {{ $statusTexts[$invoice->status] ?? $invoice->status }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.financial.invoices.show', $invoice->id) }}" class="btn btn-info" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                 {{-- @if($invoice->status !== 'paid')
                                    <a href="{{ route('admin.financial.invoices.edit', $invoice->id) }}" class="btn btn-warning" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif--}}
                                    <a href="{{ route('admin.financial.invoices.download', $invoice->id) }}" class="btn btn-secondary" title="تحميل PDF">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fs-1 d-block mb-2"></i>
                                لا توجد فواتير
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($invoices->hasPages())
        <div class="card-footer">
            {{ $invoices->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush

