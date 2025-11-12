@extends('admin.layouts.app')

@section('title', 'إدارة أقسام الأسئلة الشائعة')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="bi bi-tags"></i> أقسام الأسئلة الشائعة</h4>
        <a href="{{ route('admin.faq-categories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> قسم جديد
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>الاسم (عربي)</th>
                        <th>الاسم (إنجليزي)</th>
                        <th>الترتيب</th>
                        <th>عدد الأسئلة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                    <tr>
                        <td>{{ $cat->id }}</td>
                        <td>{{ $cat->name_ar }}</td>
                        <td>{{ $cat->name_en }}</td>
                        <td>{{ $cat->sort }}</td>
                        <td>{{ $cat->faqs()->count() }}</td>
                        <td>
                            <a href="{{ route('admin.faq-categories.edit', $cat) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i> تعديل
                            </a>
                            <form action="{{ route('admin.faq-categories.destroy', $cat) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> حذف</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-muted py-4">لا توجد أقسام بعد</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $categories->links() }}
    </div>
</div>
@endsection
