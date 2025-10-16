@extends('admin.layouts.app')

@section('content')
<a href="{{ route('admin.crm.quotes.create') }}" class="btn btn-success">
    <i class="fas fa-file-invoice me-2"></i>إنشاء عرض سعر
</a>
<form method="GET" class="row g-2 mb-3">
    <div class="col-md-3">
        <input type="text" name="search" class="form-control" placeholder="بحث عن رقم أو عميل" value="{{ request('search') }}">
    </div>
    <div class="col-md-2">
        <select name="status" class="form-select">
            <option value="">الحالة</option>
            @foreach($statusOptions as $key => $value)
                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <select name="created_by" class="form-select">
            <option value="">الموظف</option>
            @foreach($employees as $emp)
                <option value="{{ $emp->id }}" {{ request('created_by') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <button class="btn btn-primary">تطبيق</button>
    </div>
</form>

<table class="table table-hover">
    <thead>
        <tr>
            <th>رقم العرض</th>
            <th>العميل</th>
            <th>الحالة</th>
            <th>تاريخ الإنشاء</th>
            <th>الموظف</th>
        </tr>
    </thead>
    <tbody>
        @foreach($quotes as $quote)
        <tr>
            <td><a href="{{ route('admin.crm.quotes.show', $quote) }}">{{ $quote->quote_number }}</a></td>
            <td>{{ $quote->customer_name }}</td>
            <td>{{ $quote->status }}</td>
            <td>{{ $quote->created_at->format('Y-m-d') }}</td>
            <td>{{ $quote->createdBy?->name ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $quotes->links() }}
@endsection
