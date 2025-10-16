@extends('admin.layouts.app')

@section('title', 'لوحة التحكم المالية')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">لوحة التحكم المالية</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item active">المالية</li>
                </ol>
            </nav>
        </div>
        <div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#dateRangeModal">
                <i class="fas fa-calendar me-2"></i>تحديد الفترة
            </button>
            <button type="button" class="btn btn-success" onclick="exportFinancialReport()">
                <i class="fas fa-file-excel me-2"></i>تصدير التقرير
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">إجمالي الإيرادات</h6>
                            <h2 class="mb-0">{{ number_format($stats['total_revenue'] ?? 0, 0) }}</h2>
                            <small>ريال سعودي</small>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-white-50">
                            <i class="fas fa-arrow-up me-1"></i>
                            {{ number_format($stats['revenue_growth'] ?? 0, 1) }}% عن الشهر الماضي
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">المدفوعات المستلمة</h6>
                            <h2 class="mb-0">{{ number_format($stats['paid_amount'] ?? 0, 0) }}</h2>
                            <small>ريال سعودي</small>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-white-50">
                            {{ $stats['paid_invoices'] ?? 0 }} فاتورة مدفوعة
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">المدفوعات المؤجلة</h6>
                            <h2 class="mb-0">{{ number_format($stats['unpaid_amount'] ?? 0, 0) }}</h2>
                            <small>ريال سعودي</small>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-white-50">
                            {{ $stats['pending_invoices'] ?? 0 }} فاتورة معلقة
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">المدفوعات المتأخرة</h6>
                            <h2 class="mb-0">{{ number_format($stats['overdue_amount'] ?? 0, 0) }}</h2>
                            <small>ريال سعودي</small>
                        </div>
                        <div class="fs-1">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-white-50">
                            {{ $stats['overdue_invoices'] ?? 0 }} فاتورة متأخرة
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">الضرائب المحصلة</h6>
                            <h4 class="mb-0">{{ number_format($stats['total_tax'] ?? 0, 0) }}</h4>
                            <small class="text-muted">ريال سعودي</small>
                        </div>
                        <div class="fs-2 text-primary">
                            <i class="fas fa-receipt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">الدفعات الأولى</h6>
                            <h4 class="mb-0">{{ number_format($stats['partial_payments'] ?? 0, 0) }}</h4>
                            <small class="text-muted">ريال سعودي</small>
                        </div>
                        <div class="fs-2 text-info">
                            <i class="fas fa-coins"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">متوسط قيمة الفاتورة</h6>
                            <h4 class="mb-0">{{ number_format($stats['avg_invoice_value'] ?? 0, 0) }}</h4>
                            <small class="text-muted">ريال سعودي</small>
                        </div>
                        <div class="fs-2 text-success">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">إجمالي الفواتير</h6>
                            <h4 class="mb-0">{{ $stats['total_invoices'] ?? 0 }}</h4>
                            <small class="text-muted">فاتورة</small>
                        </div>
                        <div class="fs-2 text-secondary">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0" style="color: #585c5f;"><i class="fas fa-chart-line me-2"></i>الإيرادات الشهرية</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyRevenueChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0" style="color: #585c5f;"><i class="fas fa-chart-pie me-2"></i>توزيع حالة الدفع</h5>
                </div>
                <div class="card-body">
                    <canvas id="paymentStatusChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0" style="color: #585c5f;"><i class="fas fa-chart-bar me-2"></i>الإيرادات السنوية</h5>
                </div>
                <div class="card-body">
                    <canvas id="yearlyRevenueChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0" style="color: #585c5f;"><i class="fas fa-chart-area me-2"></i>المدفوعات مقابل المعلق</h5>
                </div>
                <div class="card-body">
                    <canvas id="paidVsPendingChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0" ><i class="fas fa-check-circle me-2"></i>آخر المدفوعات</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>الفاتورة</th>
                                    <th>العميل</th>
                                    <th>المبلغ</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPayments ?? [] as $payment)
                                <tr>
                                    <td><a href="{{ route('admin.financial.invoices.show', $payment->invoice_id) }}">#{{ $payment->invoice->invoice_number ?? '-' }}</a></td>
                                    <td>{{ $payment->invoice->order->name ?? '-' }}</td>
                                    <td><strong class="text-success">{{ number_format($payment->amount, 2) }} ريال</strong></td>
                                    <td>{{ $payment->created_at->format('Y-m-d') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">لا توجد مدفوعات حديثة</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('admin.financial.payments.index') }}" class="btn btn-sm btn-outline-success">
                        عرض جميع المدفوعات <i class="fas fa-arrow-left ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-exclamation-circle me-2"></i>الفواتير المعلقة</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>الفاتورة</th>
                                    <th>العميل</th>
                                    <th>المبلغ</th>
                                    <th>الاستحقاق</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingInvoices ?? [] as $invoice)
                                <tr>
                                    <td><a href="{{ route('admin.financial.invoices.show', $invoice->id) }}">#{{ $invoice->invoice_number }}</a></td>
                                    <td>{{ $invoice->order->name ?? '-' }}</td>
                                    <td><strong class="text-warning">{{ number_format($invoice->total_amount, 2) }} ريال</strong></td>
                                    <td>
                                        @if($invoice->due_date && $invoice->due_date->isPast())
                                            <span class="badge bg-danger">متأخر</span>
                                        @else
                                            {{ $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '-' }}
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">لا توجد فواتير معلقة</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('admin.financial.invoices.index') }}?status=pending" class="btn btn-sm btn-outline-warning">
                        عرض جميع الفواتير المعلقة <i class="fas fa-arrow-left ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>إجراءات سريعة</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{ route('admin.financial.invoices.create') }}" class="btn btn-lg btn-outline-primary w-100 mb-2">
                                <i class="fas fa-plus-circle d-block fs-2 mb-2"></i>
                                إنشاء فاتورة جديدة
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.financial.payments.create') }}" class="btn btn-lg btn-outline-success w-100 mb-2">
                                <i class="fas fa-money-check-alt d-block fs-2 mb-2"></i>
                                تسجيل دفعة
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.financial.reports') }}" class="btn btn-lg btn-outline-info w-100 mb-2">
                                <i class="fas fa-chart-bar d-block fs-2 mb-2"></i>
                                التقارير المالية
                            </a>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-lg btn-outline-secondary w-100 mb-2" onclick="calculateTax()">
                                <i class="fas fa-calculator d-block fs-2 mb-2"></i>
                                حاسبة الضريبة
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
</div>

<!-- Date Range Modal -->
<div class="modal fade" id="dateRangeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.financial.index') }}" method="GET">
                <div class="modal-header">
                    <h5 class="modal-title">تحديد الفترة الزمنية</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">من تاريخ</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from', now()->startOfMonth()->format('Y-m-d')) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to', now()->format('Y-m-d')) }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">تطبيق</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Chart colors
    const chartColors = {
        primary: '#0d6efd',
        success: '#198754',
        warning: '#ffc107',
        danger: '#dc3545',
        info: '#0dcaf0',
        secondary: '#6c757d'
    };

    // Monthly Revenue Chart
    const monthlyRevenueCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
    new Chart(monthlyRevenueCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['monthly']['labels'] ?? []) !!},
            datasets: [{
                label: 'الإيرادات',
                data: {!! json_encode($chartData['monthly']['revenue'] ?? []) !!},
                borderColor: chartColors.success,
                backgroundColor: 'rgba(25, 135, 84, 0.1)',
                fill: true,
                tension: 0.4
            }, {
                label: 'المدفوعات',
                data: {!! json_encode($chartData['monthly']['paid'] ?? []) !!},
                borderColor: chartColors.info,
                backgroundColor: 'rgba(13, 202, 240, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Payment Status Chart
    const paymentStatusCtx = document.getElementById('paymentStatusChart').getContext('2d');
    new Chart(paymentStatusCtx, {
        type: 'doughnut',
        data: {
            labels: ['مدفوع', 'غير مدفوع', 'دفعة أولى', 'متأخر'],
            datasets: [{
                data: [
                    {{ $chartData['payment_status']['paid'] ?? 0 }},
                    {{ $chartData['payment_status']['unpaid'] ?? 0 }},
                    {{ $chartData['payment_status']['partial'] ?? 0 }},
                    {{ $chartData['payment_status']['overdue'] ?? 0 }}
                ],
                backgroundColor: [
                    chartColors.success,
                    chartColors.warning,
                    chartColors.info,
                    chartColors.danger
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Yearly Revenue Chart
    const yearlyRevenueCtx = document.getElementById('yearlyRevenueChart').getContext('2d');
    new Chart(yearlyRevenueCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartData['yearly']['labels'] ?? []) !!},
            datasets: [{
                label: 'الإيرادات السنوية',
                data: {!! json_encode($chartData['yearly']['revenue'] ?? []) !!},
                backgroundColor: chartColors.primary
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Paid vs Pending Chart
    const paidVsPendingCtx = document.getElementById('paidVsPendingChart').getContext('2d');
    new Chart(paidVsPendingCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['monthly']['labels'] ?? []) !!},
            datasets: [{
                label: 'مدفوع',
                data: {!! json_encode($chartData['monthly']['paid'] ?? []) !!},
                borderColor: chartColors.success,
                backgroundColor: 'rgba(25, 135, 84, 0.2)',
                fill: true
            }, {
                label: 'معلق',
                data: {!! json_encode($chartData['monthly']['unpaid'] ?? []) !!},
                borderColor: chartColors.warning,
                backgroundColor: 'rgba(255, 193, 7, 0.2)',
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    function exportFinancialReport() {
        window.location.href = '{{ route("admin.financial.export") }}';
    }

    function calculateTax() {
        const amount = prompt('أدخل المبلغ الأساسي:');
        if (amount) {
            const tax = parseFloat(amount) * 0.15;
            const total = parseFloat(amount) + tax;
            alert(`المبلغ الأساسي: ${parseFloat(amount).toFixed(2)} ريال\nالضريبة (15%): ${tax.toFixed(2)} ريال\nالإجمالي: ${total.toFixed(2)} ريال`);
        }
    }
</script>
@endpush

