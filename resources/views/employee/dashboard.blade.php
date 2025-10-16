@extends('employee.layouts.app')

@section('title', 'لوحة الموظف')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">

            <div class="card shadow-sm">
                <div class="card-header">
                    <h4>👋 مرحباً {{ $employee->name }}</h4>
                </div>
                <div class="card-body">
                    <p>أهلاً بك في لوحة تحكم الموظف الخاصة بك.</p>

                    <ul>
                        <li>📧 البريد الإلكتروني: {{ $employee->email }}</li>
                        <li>📱 الهاتف: {{ $employee->phone }}</li>
                        <li>🟢 الحالة: {{ $employee->is_active ? 'نشط' : 'غير نشط' }}</li>
                    </ul>

                    <form action="{{ route('employee.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger">تسجيل خروج</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
