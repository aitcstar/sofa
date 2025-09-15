@extends('admin.layouts.app')

@section('title', 'تعديل المدونة')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">تعديل المدونة</h1>
        <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i> رجوع
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">تفاصيل المدونة</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.blogs.update', $blog->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">العنوان (عربي) <span class="text-danger">*</span></label>
                        <input type="text" name="title_ar" class="form-control" value="{{ $blog->title_ar }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">العنوان (إنجليزي) <span class="text-danger">*</span></label>
                        <input type="text" name="title_en" class="form-control" value="{{ $blog->title_en }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Slug (AR) <span class="text-danger">*</span></label>
                        <input type="text" name="slug_ar" class="form-control" value="{{ $blog->slug_ar }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Slug (EN) <span class="text-danger">*</span></label>
                        <input type="text" name="slug_en" class="form-control" value="{{ $blog->slug_en }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">الفئة <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-select" required>
                        <option value="" disabled>اختر الفئة</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $blog->category_id) == $category->id ? 'selected' : '' }}>
                                {{ app()->getLocale() === 'ar' ? $category->name_ar : $category->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">المؤلف (عربي)</label>
                        <input type="text" name="author_ar" class="form-control" value="{{ $blog->author_ar }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">المؤلف (إنجليزي)</label>
                        <input type="text" name="author_en" class="form-control" value="{{ $blog->author_en }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">ملخص (عربي)</label>
                        <textarea name="excerpt_ar" class="form-control" rows="3">{{ $blog->excerpt_ar }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">ملخص (إنجليزي)</label>
                        <textarea name="excerpt_en" class="form-control" rows="3">{{ $blog->excerpt_en }}</textarea>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">المحتوى (عربي)</label>
                    <textarea name="content_ar" class="form-control" rows="5">{{ $blog->content_ar }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">المحتوى (إنجليزي)</label>
                    <textarea name="content_en" class="form-control" rows="5">{{ $blog->content_en }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">الصورة الحالية</label>
                    <div>
                        @if($blog->image)
                            <img src="{{ asset('storage/'.$blog->image) }}" class="current-image me-3 border" width="200">
                            <a href="{{ asset('storage/'.$blog->image) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye me-1"></i> عرض الصورة
                            </a>
                        @else
                            <span class="text-muted">لا توجد صورة</span>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">تغيير الصورة</label>
                    <input type="file" name="image" id="imageUpload" class="form-control" accept="image/*">
                    <div class="form-text">اتركه فارغاً إذا كنت لا تريد تغيير الصورة</div>
                    <img id="imagePreview" class="image-preview mt-2" style="max-width: 200px; display:none;">
                </div>

                <div class="card mt-4">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">الأسئلة الشائعة</h5>
                        <button type="button" id="add-faq" class="btn btn-sm btn-light">إضافة سؤال</button>
                    </div>
                    <div class="card-body" id="faq-wrapper">
                        @foreach($blog->faqs as $index => $faq)
                            <div class="faq-item border p-3 mb-3 rounded">
                                <input type="hidden" name="faqs[{{ $index }}][id]" value="{{ $faq->id }}">
                                <div class="mb-2">
                                    <label>السؤال (AR)</label>
                                    <input type="text" name="faqs[{{ $index }}][question_ar]" class="form-control" value="{{ $faq->question_ar }}">
                                </div>
                                <div class="mb-2">
                                    <label>السؤال (EN)</label>
                                    <input type="text" name="faqs[{{ $index }}][question_en]" class="form-control" value="{{ $faq->question_en }}">
                                </div>
                                <div class="mb-2">
                                    <label>الإجابة (AR)</label>
                                    <textarea name="faqs[{{ $index }}][answer_ar]" class="form-control">{{ $faq->answer_ar }}</textarea>
                                </div>
                                <div class="mb-2">
                                    <label>الإجابة (EN)</label>
                                    <textarea name="faqs[{{ $index }}][answer_en]" class="form-control">{{ $faq->answer_en }}</textarea>
                                </div>
                                <button type="button" class="btn btn-danger btn-sm mt-2" onclick="removeFaqRow(this)">حذف</button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> تحديث المدونة
                    </button>
                    <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> إلغاء
                    </a>
                </div>
            </form>
            <hr><br>

            <div class="card mt-4 border-0 shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">التعليقات على المدونة</h5>
                    <span class="badge bg-light text-dark">
                        {{ $blog->comments->count() }} تعليق
                    </span>
                </div>

                <div class="card-body">
                    @if($blog->comments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>الاسم</th>
                                        <th>البريد الإلكتروني</th>
                                        <th>التعليق</th>
                                        <th>الحالة</th>
                                        <th class="text-center">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($blog->comments as $comment)
                                        <tr>
                                            <td>{{ $comment->name }}</td>
                                            <td>{{ $comment->email }}</td>
                                            <td>{{ $comment->message }}</td>
                                            <td>
                                                @if($comment->status == 'pending')
                                                    <span class="badge bg-warning text-dark">قيد المراجعة</span>
                                                @elseif($comment->status == 'approved')
                                                    <span class="badge bg-success">مقبول</span>
                                                @else
                                                    <span class="badge bg-danger">مرفوض</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex gap-2 justify-content-center">
                                                    @if($comment->status != 'approved')
                                                        <form action="{{ route('admin.comments.approve', $comment->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-sm btn-outline-success">
                                                                <i class="fas fa-check"></i> قبول
                                                            </button>
                                                        </form>
                                                    @endif

                                                    @if($comment->status != 'rejected')
                                                        <form action="{{ route('admin.comments.reject', $comment->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                <i class="fas fa-times"></i> رفض
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">لا توجد تعليقات بعد.</p>
                    @endif
                </div>
            </div>


        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    // معاينة الصورة قبل الرفع
    document.getElementById('imageUpload').addEventListener('change', function(e) {
        const preview = document.getElementById('imagePreview');
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    // CKEditor
    ClassicEditor.create(document.querySelector('textarea[name="content_ar"]'), { language: 'ar' })
        .catch(error => console.error(error));
    ClassicEditor.create(document.querySelector('textarea[name="content_en"]'))
        .catch(error => console.error(error));

    // FAQs
    let faqIndex = {{ $blog->faqs->count() }};
    document.getElementById('add-faq').addEventListener('click', function () {
        const wrapper = document.getElementById('faq-wrapper');
        const html = `
        <div class="faq-item border p-3 mb-3 rounded">
            <div class="mb-2">
                <label>السؤال (AR)</label>
                <input type="text" name="faqs[${faqIndex}][question_ar]" class="form-control">
            </div>
            <div class="mb-2">
                <label>السؤال (EN)</label>
                <input type="text" name="faqs[${faqIndex}][question_en]" class="form-control">
            </div>
            <div class="mb-2">
                <label>الإجابة (AR)</label>
                <textarea name="faqs[${faqIndex}][answer_ar]" class="form-control"></textarea>
            </div>
            <div class="mb-2">
                <label>الإجابة (EN)</label>
                <textarea name="faqs[${faqIndex}][answer_en]" class="form-control"></textarea>
            </div>
            <button type="button" class="btn btn-danger btn-sm mt-2" onclick="removeFaqRow(this)">حذف</button>
        </div>`;
        wrapper.insertAdjacentHTML('beforeend', html);
        faqIndex++;
    });

    function removeFaqRow(button) {
        button.closest('.faq-item').remove();
    }
</script>
@endpush
