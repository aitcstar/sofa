@extends('admin.layouts.app')

@section('title', 'إدارة العملاء المحتملين')

@section('content')
@php
$user = Auth::guard('admin')->user() ?? Auth::guard('employee')->user();
@endphp
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">إدارة العملاء المحتملين</h1>
        @if($user && ($user->hasPermission('crm.leads.create') || $user->role === 'admin'))
        <a href="{{ route('admin.crm.leads.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> إضافة عميل محتمل
        </a>
        @endif
    </div>

    <!-- فلترة وبحث -->
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-2">
            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="new" {{ old('status') == 'new' ? 'selected' : '' }}>جديد</option>
                                <option value="contacted" {{ old('status') == 'contacted' ? 'selected' : '' }}>تم التواصل</option>
                                <option value="interested" {{ old('status') == 'interested' ? 'selected' : '' }}>مهتم</option>
                                <option value="not_interested" {{ old('status') == 'not_interested' ? 'selected' : '' }}>غير مهتم</option>
                                <option value="converted" {{ old('status') == 'converted' ? 'selected' : '' }}>تم التحويل</option>
                            </select>
        </div>
        <div class="col-md-2">
            <select name="priority" class="form-select">
                <option value="">الأولوية</option>
                @foreach($priorityOptions as $key => $label)
                    <option value="{{ $key }}" {{ request('priority') == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select class="form-select @error('source') is-invalid @enderror"
                                    id="source" name="source">
                                <option value="">اختر المصدر</option>
                                <option value="website" {{ old('source') == 'website' ? 'selected' : '' }}>الموقع الإلكتروني</option>
                                <option value="phone" {{ old('source') == 'phone' ? 'selected' : '' }}>اتصال هاتفي</option>
                                <option value="email" {{ old('source') == 'email' ? 'selected' : '' }}>بريد إلكتروني</option>
                                <option value="social_media" {{ old('source') == 'social_media' ? 'selected' : '' }}>وسائل التواصل</option>
                                <option value="referral" {{ old('source') == 'referral' ? 'selected' : '' }}>إحالة</option>
                                <option value="other" {{ old('source') == 'other' ? 'selected' : '' }}>أخرى</option>
                            </select>
        </div>
        <div class="col-md-2">
            <select name="assigned_to" class="form-select">
                <option value="">الموظف المعين</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ request('assigned_to') == $employee->id ? 'selected' : '' }}>
                        {{ $employee->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="من تاريخ">
        </div>
        <div class="col-md-2">
            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="إلى تاريخ">
        </div>
        <div class="col-md-2">
            <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="بحث">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">تطبيق</button>
        </div>
    </form>

    <!-- الجدول -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="leadsTable" class="table table-striped table-bordered align-middle text-center w-100">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>البريد الإلكتروني</th>
                            <th>الهاتف</th>
                            <th>الشركة</th>
                            <th>الموظف المسؤول</th>

                            <th>المصدر</th>
                            <th>الحالة</th>
                            <th>تاريخ الإنشاء</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leads as $lead)
                        <tr>
                            <td>{{ $lead->id }}</td>
                            <td>{{ $lead->name }}</td>
                            <td>{{ $lead->email }}</td>
                            <td>{{ $lead->phone }}</td>
                            <td>{{ $lead->company }}</td>
                            <td>{{ $lead->assignedTo->name ?? '-' }}</td>
                            <td>
                                @php
                                    $sources = [
                                        'website' => 'الموقع',
                                        'phone' => 'هاتف',
                                        'email' => 'بريد',
                                        'social_media' => 'سوشيال ميديا',
                                        'referral' => 'إحالة',
                                        'other' => 'أخرى'
                                    ];
                                @endphp
                                <span class="badge bg-secondary">{{ $sources[$lead->source] ?? $lead->source }}</span>
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'new' => 'info',
                                        'contacted' => 'warning',
                                        'interested' => 'success',
                                        'not_interested' => 'danger',
                                        'converted' => 'dark'
                                    ];
                                    $statusTexts = [
                                        'new' => 'جديد',
                                        'contacted' => 'تم التواصل',
                                        'interested' => 'مهتم',
                                        'not_interested' => 'غير مهتم',
                                        'converted' => 'تم التحويل'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$lead->status] ?? 'secondary' }}">
                                    {{ $statusTexts[$lead->status] ?? $lead->status }}
                                </span>
                            </td>
                            <td>{{ $lead->created_at->format('Y-m-d') }}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">

                                    @if($user && ($user->hasPermission('crm.leads.view') || $user->role === 'admin'))
                                        <a href="{{ route('admin.crm.leads.show', $lead) }}"
                                           class="btn btn-sm btn-primary">
                                            عرض
                                        </a>
                                    @endif

                                    @if($user && ($user->hasPermission('crm.leads.edit') || $user->role === 'admin'))
                                        <a href="{{ route('admin.crm.leads.edit', $lead) }}"
                                           class="btn btn-sm btn-warning">
                                            تعديل
                                        </a>
                                    @endif

                                    @if($user && ($user->hasPermission('crm.leads.delete') || $user->role === 'admin'))
                                        <form action="{{ route('admin.crm.leads.destroy', $lead) }}"
                                              method="POST"
                                              onsubmit="return confirm('هل أنت متأكد من حذف العميل المحتمل؟ سيتم حذف كل البيانات المرتبطة به');">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-danger">
                                                حذف
                                            </button>
                                        </form>
                                    @endif

                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="py-4 text-muted">لا توجد بيانات</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#leadsTable').DataTable({
        responsive: true,
        order: [[0, 'desc']],
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "الكل"]],
        dom: '<"row"<"col-md-6"l><"col-md-6 text-start"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
        columnDefs: [
            { orderable: false, targets: [9] },
            { searchable: false, targets: [9] }
        ],
        language: {
            emptyTable: "لا توجد بيانات متاحة في الجدول",
            info: "إظهار _START_ إلى _END_ من أصل _TOTAL_ عميل",
            infoEmpty: "إظهار 0 إلى 0 من أصل 0 عميل",
            infoFiltered: "(تمت تصفيته من أصل _MAX_ عميل)",
            lengthMenu: "إظهار _MENU_ مدخلات",
            loadingRecords: "جارٍ التحميل...",
            processing: "جارٍ المعالجة...",
            search: "بحث:",
            zeroRecords: "لم يتم العثور على سجلات مطابقة",
            paginate: {
                first: "الأول",
                last: "الأخير",
                next: "التالي",
                previous: "السابق"
            },
            aria: {
                sortAscending: ": تفعيل لترتيب العمود تصاعدياً",
                sortDescending: ": تفعيل لترتيب العمود تنازلياً"
            }
        }
    });
});
</script>
@endpush
