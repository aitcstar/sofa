@extends('admin.layouts.app')

@section('title', 'طرق الدفع')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">طرق الدفع</h1>

    <a href="{{ route('admin.financial.payment-methods.create') }}" class="btn btn-success mb-3">إضافة طريقة دفع جديدة</a>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>الاسم</th>
                <th>النوع</th>
                <th>الحالة</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($paymentMethods as $method)
            <tr>
                <td>{{ $method->name }}</td>
                <td>{{ $method->type }}</td>
                <td>{{ $method->is_active ? 'نشط' : 'غير نشط' }}</td>
                <td>
                    <a href="{{ route('admin.financial.payment-methods.edit', $method) }}" class="btn btn-sm btn-warning">تعديل</a>
                    <form action="{{ route('admin.financial.payment-methods.destroy', $method) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد؟')">حذف</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
