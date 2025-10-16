@extends('admin.layouts.app')

@section('title', 'استيراد الطلبات')

@section('content')
<div class="container">
    <h1>استيراد الطلبات</h1>
    <form action="{{ route('admin.orders.enhanced.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="file" class="form-label">اختر ملف CSV أو Excel</label>
            <input type="file" name="file" id="file" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">استيراد</button>
    </form>
</div>
@endsection
