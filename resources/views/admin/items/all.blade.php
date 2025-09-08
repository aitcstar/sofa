@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h2>جميع العناصر</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>اسم العنصر عربي</th>
                <th>اسم العنصر انجليزي</th>
                <th>الوحدة</th>
                <th>الكمية</th>
                <th>الأبعاد</th>
                <th>المادة</th>
                <th>اللون</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->item_name_ar }}</td>
                <td>{{ $item->item_name_en }}</td>
                <td>{{ $item->unit->name_ar ?? '' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->dimensions }}</td>
                <td>{{ $item->material }}</td>
                <td>{{ $item->color }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
