@extends('admin.layouts.app')

@section('title', 'إدارة المدفوعات')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">إدارة المدفوعات</h1>

    <!-- جدول المدفوعات -->
    @include('admin.financial.payments.partials.table', ['payments' => $payments])

    <!-- Pagination -->
    {{ $payments->links() }}
</div>
@endsection
