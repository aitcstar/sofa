@extends('admin.layouts.app')

@section('title', 'قمع المبيعات')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"> المبيعات</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.crm.index') }}">CRM</a></li>
                    <li class="breadcrumb-item active">المبيعات </li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Funnel Visualization -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-filter me-2"></i>مراحل المبيعات</h5>
                </div>
                <div class="card-body">
                    <div class="funnel-container">
                        @php
                            $funnelData = [
                                ['stage' => 'جديد', 'count' => $stats['new_leads'] ?? 0, 'color' => '#4e73df', 'width' => 100],
                                ['stage' => 'تم التواصل', 'count' => $stats['contacted_leads'] ?? 0, 'color' => '#1cc88a', 'width' => 80],
                                ['stage' => 'مهتم', 'count' => $stats['interested_leads'] ?? 0, 'color' => '#36b9cc', 'width' => 60],
                                ['stage' => 'عرض سعر', 'count' => $stats['quoted_leads'] ?? 0, 'color' => '#f6c23e', 'width' => 40],
                                ['stage' => 'تم التحويل', 'count' => $stats['converted_leads'] ?? 0, 'color' => '#e74a3b', 'width' => 20],
                            ];
                        @endphp

                        @foreach($funnelData as $stage)
                        <div class="funnel-stage" style="width: {{ $stage['width'] }}%; background-color: {{ $stage['color'] }};">
                            <div class="funnel-stage-content">
                                <h4>{{ $stage['stage'] }}</h4>
                                <p class="mb-0">{{ $stage['count'] }} عميل</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h6 class="text-white-50">إجمالي العملاء</h6>
                    <h2 style="color: #84909f;">{{ $stats['total_leads'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h6 class="text-white-50">تم التواصل</h6>
                    <h2>{{ $stats['contacted_leads'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h6 class="text-white-50">مهتم</h6>
                    <h2>{{ $stats['interested_leads'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h6 class="text-white-50">معدل التحويل</h6>
                    <h2>{{ number_format($stats['conversion_rate'] ?? 0, 1) }}%</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Leads by Stage -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>العملاء حسب المرحلة</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>المرحلة</th>
                                    <th>عدد العملاء</th>
                                    <th>النسبة المئوية</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge bg-primary">جديد</span></td>
                                    <td>{{ $stats['new_leads'] ?? 0 }}</td>
                                    <td>
                                        @php
                                            $percentage = $stats['total_leads'] > 0 ? ($stats['new_leads'] / $stats['total_leads']) * 100 : 0;
                                        @endphp
                                        <div class="progress">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $percentage }}%"
                                                 aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ number_format($percentage, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.crm.leads.index', ['status' => 'new']) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> عرض
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-success">تم التواصل</span></td>
                                    <td>{{ $stats['contacted_leads'] ?? 0 }}</td>
                                    <td>
                                        @php
                                            $percentage = $stats['total_leads'] > 0 ? ($stats['contacted_leads'] / $stats['total_leads']) * 100 : 0;
                                        @endphp
                                        <div class="progress">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%"
                                                 aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ number_format($percentage, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.crm.leads.index', ['status' => 'contacted']) }}" class="btn btn-sm btn-success">
                                            <i class="fas fa-eye"></i> عرض
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-info">مهتم</span></td>
                                    <td>{{ $stats['interested_leads'] ?? 0 }}</td>
                                    <td>
                                        @php
                                            $percentage = $stats['total_leads'] > 0 ? ($stats['interested_leads'] / $stats['total_leads']) * 100 : 0;
                                        @endphp
                                        <div class="progress">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $percentage }}%"
                                                 aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ number_format($percentage, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.crm.leads.index', ['status' => 'interested']) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> عرض
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-danger">تم التحويل</span></td>
                                    <td>{{ $stats['converted_leads'] ?? 0 }}</td>
                                    <td>
                                        @php
                                            $percentage = $stats['total_leads'] > 0 ? ($stats['converted_leads'] / $stats['total_leads']) * 100 : 0;
                                        @endphp
                                        <div class="progress">
                                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $percentage }}%"
                                                 aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ number_format($percentage, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.crm.leads.index', ['status' => 'converted']) }}" class="btn btn-sm btn-danger">
                                            <i class="fas fa-eye"></i> عرض
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .funnel-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px 0;
    }

    .funnel-stage {
        margin: 10px 0;
        padding: 20px;
        text-align: center;
        color: white;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }

    .funnel-stage:hover {
        transform: scale(1.05);
    }

    .funnel-stage-content h4 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: bold;
    }

    .funnel-stage-content p {
        margin: 5px 0 0 0;
        font-size: 1.2rem;
    }
</style>
@endpush
@endsection

