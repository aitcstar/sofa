@extends('admin.layouts.app')

@section('content')
<h3>{{ $lead->name }}</h3>
<p><strong>البريد:</strong> {{ $lead->email }}</p>
<p><strong>الهاتف:</strong> {{ $lead->phone }}</p>
<p><strong>الشركة:</strong> {{ $lead->company }}</p>
<p><strong>الحالة:</strong> {{ $lead->status }}</p>
<p><strong>الأولوية:</strong> {{ $lead->priority }}</p>
<p><strong>الموظف المعين:</strong> {{ $lead->assignedTo?->name ?? '-' }}</p>

<h4>الأنشطة الأخيرة</h4>
<ul>
    @foreach($lead->activities as $activity)
        <li>{{ $activity->description }} - {{ $activity->created_at->format('Y-m-d H:i') }}</li>
    @endforeach
</ul>
@endsection
