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
            <div class="mt-3">
                <strong>الحالة:</strong>
                <select id="contactStatus" class="form-select w-auto d-inline-block"
                    data-id="{{ $contact->id }}">
                    <option value="new" {{ $contact->status == 'new' ? 'selected' : '' }}>جديدة</option>
                    <option value="contacted" {{ $contact->status == 'contacted' ? 'selected' : '' }}>تم التواصل</option>
                    <option value="no_response" {{ $contact->status == 'no_response' ? 'selected' : '' }}>لم يتم الرد</option>
                    <option value="in_progress" {{ $contact->status == 'in_progress' ? 'selected' : '' }}>قيد المتابعة</option>
                </select>
            </div>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('admin.contacts.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> رجوع
            </a>
        </div>
    </div>
</div>
<script>
    document.getElementById('contactStatus').addEventListener('change', function() {
        const id = this.getAttribute('data-id');
        const status = this.value;

        fetch(`/admin/contacts/${id}/status`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({status})
        }).then(res => res.json())
          .then(data => {
              if (data.success) {
                  alert('تم تحديث الحالة بنجاح');
              }
          });
    });
    </script>
@endsection
