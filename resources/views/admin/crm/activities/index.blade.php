@extends('admin.layouts.app')

@section('content')
<h1 class="mb-4">الأنشطة والمتابعات</h1>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-3">
        <select name="type" class="form-select">
            <option value="">نوع النشاط</option>
            @foreach($types as $key => $value)
                <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <select name="user_id" class="form-select">
            <option value="">الموظف</option>
            @foreach($employees as $emp)
                <option value="{{ $emp->id }}" {{ request('user_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
    </div>
    <div class="col-md-2">
        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
    </div>
    <div class="col-md-2">
        <button class="btn btn-primary w-100">تطبيق</button>
    </div>
</form>

<table class="table table-hover">
    <thead>
        <tr>
            <th>التاريخ</th>
            <th>العميل المحتمل</th>
            <th>الموظف</th>
            <th>نوع النشاط</th>
            <th>الوصف</th>
        </tr>
    </thead>
    <tbody>
        @foreach($activities as $activity)
        <tr>
            <td>{{ $activity->created_at->format('Y-m-d H:i') }}</td>
            <td>{{ $activity->lead?->name ?? '-' }}</td>
            <td>{{ $activity->user?->name ?? '-' }}</td>
            <td>{{ $activity->type }}</td>
            <td>{{ $activity->description }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $activities->links() }}
@endsection
