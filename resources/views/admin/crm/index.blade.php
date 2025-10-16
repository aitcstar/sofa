@extends('admin.layouts.app')

@section('title', 'لوحة تحكم CRM')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">لوحة تحكم CRM</h1>

    <!-- إحصائيات سريعة -->
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card p-3 text-center">
                <h6>إجمالي العملاء</h6>
                <h3>{{ $stats['total'] ?? 0 }}</h3>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card p-3 text-center">
                <h6>العملاء الجدد</h6>
                <h3>{{ $stats['new'] ?? 0 }}</h3>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card p-3 text-center">
                <h6>العملاء الساخنين</h6>
                <h3>{{ $hotLeads->count() ?? 0 }}</h3>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card p-3 text-center">
                <h6>العملاء المتأخرين</h6>
                <h3>{{ $overdueLeads->count() ?? 0 }}</h3>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card p-3 text-center">
                <h6>القيمة الإجمالية</h6>
                <h3>{{ number_format($stats['total_value'] ?? 0, 2) }}</h3>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card p-3 text-center">
                <h6>متوسط القيمة</h6>
                <h3>{{ number_format($stats['avg_value'] ?? 0, 2) }}</h3>
            </div>
        </div>
    </div>

    <!-- Pipeline العملاء -->
    <h3 class="mt-4">Pipeline العملاء</h3>
    <div class="card p-3 mb-4">
        <canvas id="pipelineChart" height="100"></canvas>
    </div>

    <!-- أحدث العملاء -->
    <h3 class="mt-4">أحدث العملاء</h3>
    <div class="card p-3 mb-4">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>الاسم</th>
                    <th>البريد الإلكتروني</th>
                    <th>الهاتف</th>
                    <th>الموظف المسؤول</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentLeads as $lead)
                    <tr>
                        <td>{{ $lead->name }}</td>
                        <td>{{ $lead->email }}</td>
                        <td>{{ $lead->phone }}</td>
                        <td>{{ $lead->assignedTo->name ?? '-' }}</td>
                        <td>{{ ucfirst($lead->status) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">لا توجد بيانات</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- أحدث العروض -->
    <h3 class="mt-4">أحدث العروض</h3>
    <div class="card p-3 mb-4">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>العميل</th>
                    <th>العرض</th>
                    <th>الحالة</th>
                    <th>تم الإنشاء بواسطة</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentQuotes as $quote)
                    <tr>
                        <td>{{ $quote->customer->name ?? $quote->lead->name }}</td>
                        <td>{{ $quote->title }}</td>
                        <td>{{ ucfirst($quote->status) }}</td>
                        <td>{{ $quote->createdBy->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">لا توجد بيانات</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('pipelineChart').getContext('2d');
    const pipelineData = @json($pipeline); // مصفوفة تحتوي على الحالات وعدد كل حالة
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: Object.keys(pipelineData),
            datasets: [{
                label: 'عدد العملاء',
                data: Object.values(pipelineData),
                backgroundColor: '#4e73df'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { enabled: true }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endsection
