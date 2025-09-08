@extends('admin.layouts.app')

@section('title', 'عرض الرسالة')

@section('content')
<div class="container">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-envelope-open me-2"></i> تفاصيل الرسالة</h5>
        </div>
        <div class="card-body">
            <p><strong>الاسم:</strong> {{ $contact->name }}</p>
            <p><strong>البريد:</strong> {{ $contact->email }}</p>
            <p><strong>الجوال:</strong> {{ $contact->country_code . $contact->phone . '+'}}</p>
            <p><strong>الرسالة:</strong></p>
            <div class="p-3 border rounded bg-light">
                {{ $contact->message }}
            </div>
            <p class="mt-3">
                <strong>الحالة:</strong>
                <span class="badge bg-{{ $contact->status == 'new' ? 'warning' : 'info' }}">
                    {{ $contact->status == 'new' ? 'جديدة' : 'مقروءة' }}
                </span>
            </p>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('admin.contacts.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> رجوع
            </a>
        </div>
    </div>
</div>
@endsection
