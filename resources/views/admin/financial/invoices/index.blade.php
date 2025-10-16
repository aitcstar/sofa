@extends('admin.layouts.app')

@section('title', 'إدارة الفواتير')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">إدارة الفواتير</h1>

    <!-- جدول الفواتير -->
    @include('admin.financial.invoices.partials.table', ['invoices' => $invoices])

    <!-- Pagination -->
    {{ $invoices->links() }}
</div>
@endsection
