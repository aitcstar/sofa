@extends('admin.layouts.app')

@section('title', 'الحملات التسويقية')

@section('content')
@php
$user = Auth::guard('admin')->user() ?? Auth::guard('employee')->user();
@endphp
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">الحملات التسويقية</h2>
        @if($user && ($user->hasPermission('marketing.campaigns.create') || $user->role === 'admin'))
            <a href="{{ route('admin.marketing.campaigns.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> حملة جديدة
            </a>
        @endif
    </div>


    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.marketing.campaigns.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control"
                               placeholder="بحث بالاسم..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">جميع الحالات</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="paused" {{ request('status') === 'is_active' ? 'selected' : '' }}>متوقف</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="type" class="form-select">
                            <option value="">جميع الأنواع</option>
                            <option value="email" {{ request('type') === 'email' ? 'selected' : '' }}>بريد إلكتروني</option>
                            <option value="sms" {{ request('type') === 'sms' ? 'selected' : '' }}>رسائل نصية</option>
                            <option value="social" {{ request('type') === 'social' ? 'selected' : '' }}>وسائل التواصل</option>
                            <option value="banner" {{ request('type') === 'banner' ? 'selected' : '' }}>بانر</option>
                            <option value="popup" {{ request('type') === 'popup' ? 'selected' : '' }}>نافذة منبثقة</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> بحث
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Campaigns List -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>اسم الحملة</th>
                            <th>النوع</th>
                            <th>الحالة</th>
                            <th>الميزانية</th>
                            <th>تاريخ البدء</th>
                            <th>تاريخ الانتهاء</th>
                            <th>الأداء</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($campaigns as $campaign)
                        <tr>
                            <td>{{ $campaign->id }}</td>
                            <td>
                                <strong>{{ $campaign->name }}</strong>
                                @if($campaign->description)
                                    <small class="text-muted d-block">{{ Str::limit($campaign->description, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $typeIcons = [
                                        'email' => 'envelope',
                                        'sms' => 'sms',
                                        'social' => 'share-alt',
                                        'banner' => 'image',
                                        'popup' => 'window-restore',
                                    ];
                                    $typeLabels = [
                                        'email' => 'بريد',
                                        'sms' => 'SMS',
                                        'social' => 'تواصل',
                                        'banner' => 'بانر',
                                        'popup' => 'منبثقة',
                                    ];
                                @endphp
                                <span class="badge bg-secondary">
                                    <i class="fas fa-{{ $typeIcons[$campaign->type] ?? 'tag' }}"></i>
                                    {{ $typeLabels[$campaign->type] ?? $campaign->type }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'draft' => 'secondary',
                                        'active' => 'success',
                                        'paused' => 'warning',
                                        'completed' => 'info',
                                    ];
                                    $statusLabels = [
                                        'draft' => 'مسودة',
                                        'active' => 'نشط',
                                        'paused' => 'متوقف',
                                        'completed' => 'مكتمل',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$campaign->status] ?? 'secondary' }}">
                                    {{ $statusLabels[$campaign->status] ?? $campaign->status }}
                                </span>
                            </td>
                            <td>
                                @if($campaign->budget)
                                    {{ number_format($campaign->budget, 2) }} ريال
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $campaign->start_date->format('Y-m-d') }}</td>
                            <td>{{ $campaign->end_date->format('Y-m-d') }}</td>
                            <td>
                                @if($campaign->tracking->count() > 0)
                                    @php
                                        $clicks = $campaign->tracking->sum('clicks');
                                        $impressions = $campaign->tracking->sum('impressions');
                                        $conversions = $campaign->tracking->sum('conversions');
                                        $ctr = $impressions > 0 ? round(($clicks / $impressions) * 100, 2) : 0;
                                    @endphp
                                    <small>
                                        <strong>CTR:</strong> {{ $ctr }}%<br>
                                        <strong>تحويلات:</strong> {{ $conversions }}
                                    </small>
                                @else
                                    <small class="text-muted">لا توجد بيانات</small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.marketing.campaigns.show', $campaign) }}"
                                       class="btn btn-sm btn-info" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($user && ($user->hasPermission('marketing.campaigns.edit') || $user->role === 'admin'))
                                    <a href="{{ route('admin.marketing.campaigns.edit', $campaign) }}"
                                       class="btn btn-sm btn-warning" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif



                                    @if($user && ($user->hasPermission('marketing.campaigns.delete') || $user->role === 'admin'))
                                    @if($campaign->status !== 'active')
                                    <form action="{{ route('admin.marketing.campaigns.destroy', $campaign) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذه الحملة؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <p class="text-muted mb-0">لا توجد حملات</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($campaigns->hasPages())
            <div class="mt-3">
                {{ $campaigns->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

