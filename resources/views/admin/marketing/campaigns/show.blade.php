@extends('admin.layouts.app')

@section('title', 'تفاصيل الحملة - ' . $campaign->name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">{{ $campaign->name }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.marketing.index') }}">التسويق</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.marketing.campaigns.index') }}">الحملات</a></li>
                    <li class="breadcrumb-item active">{{ $campaign->name }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.marketing.campaigns.edit', $campaign) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>تعديل
            </a>
            <a href="{{ route('admin.marketing.campaigns.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-2"></i>رجوع
            </a>
        </div>
    </div>

    <!-- Campaign Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-primary">{{ number_format($stats['impressions'] ?? 0) }}</h3>
                    <p class="mb-0">مرات الظهور</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-success">{{ number_format($stats['clicks'] ?? 0) }}</h3>
                    <p class="mb-0">النقرات</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-info">{{ $stats['ctr'] ?? 0 }}%</h3>
                    <p class="mb-0">معدل النقر</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-warning">{{ number_format($stats['conversions'] ?? 0) }}</h3>
                    <p class="mb-0">التحويلات</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Campaign Details -->
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>تفاصيل الحملة</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width: 40%;">نوع الحملة:</th>
                            <td>
                                @switch($campaign->type)
                                    @case('email')
                                        <span class="badge bg-primary">بريد إلكتروني</span>
                                        @break
                                    @case('sms')
                                        <span class="badge bg-success">رسائل نصية</span>
                                        @break
                                    @case('social')
                                        <span class="badge bg-info">وسائل التواصل</span>
                                        @break
                                    @case('banner')
                                        <span class="badge bg-warning">إعلانات بانر</span>
                                        @break
                                    @case('popup')
                                        <span class="badge bg-danger">نوافذ منبثقة</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $campaign->type }}</span>
                                @endswitch
                            </td>
                        </tr>
                        <tr>
                            <th>الحالة:</th>
                            <td>
                                @switch($campaign->status)
                                    @case('draft')
                                        <span class="badge bg-secondary">مسودة</span>
                                        @break
                                    @case('active')
                                        <span class="badge bg-success">نشط</span>
                                        @break
                                    @case('paused')
                                        <span class="badge bg-warning">متوقف مؤقتاً</span>
                                        @break
                                    @case('completed')
                                        <span class="badge bg-info">مكتمل</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $campaign->status }}</span>
                                @endswitch
                            </td>
                        </tr>
                        <tr>
                            <th>الجمهور المستهدف:</th>
                            <td>{{ $campaign->target_audience ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>الميزانية:</th>
                            <td>{{ $campaign->budget ? number_format($campaign->budget, 2) . ' ريال' : '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width: 40%;">تاريخ البدء:</th>
                            <td>{{ $campaign->start_date ? $campaign->start_date->format('Y-m-d') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>تاريخ الانتهاء:</th>
                            <td>{{ $campaign->end_date ? $campaign->end_date->format('Y-m-d') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>تاريخ الإنشاء:</th>
                            <td>{{ $campaign->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        <tr>
                            <th>آخر تحديث:</th>
                            <td>{{ $campaign->updated_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($campaign->description)
            <div class="row mt-3">
                <div class="col-12">
                    <h6>الوصف:</h6>
                    <p>{{ $campaign->description }}</p>
                </div>
            </div>
            @endif

            @if($campaign->goals)
            <div class="row mt-3">
                <div class="col-12">
                    <h6>الأهداف:</h6>
                    <p>{{ $campaign->goals }}</p>
                </div>
            </div>
            @endif

            @if($campaign->content)
            <div class="row mt-3">
                <div class="col-12">
                    <h6>المحتوى:</h6>
                    <div class="border p-3 bg-light">
                        {{ $campaign->content }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

