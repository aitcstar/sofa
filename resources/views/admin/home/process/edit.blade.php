@extends('admin.layouts.app')

@section('title', 'تعديل قسم خطوات تأثيث وحدتك')

@section('content')
<div class="container">
    <h1 class="h3 mb-4">تعديل خطوات تأثيث وحدتك</h1>

    <form action="{{ route('admin.process.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- بيانات أساسية -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">البيانات الأساسية</div>
            <div class="card-body">
                <div class="mb-3">
                    <label>الصورة (Avatar)</label>
                    <input type="file" name="avatar" class="form-control">
                    @if($process && $process->avatar)
                        <img src="{{ asset('storage/'.$process->avatar) }}" width="120" class="mt-2">
                    @endif
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>الاسم</label>
                        <input type="text" name="name" value="{{ $process->name ?? '' }}" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>عدد الوحدات</label>
                        <input type="number" name="units" value="{{ $process->units ?? '' }}" class="form-control">
                    </div>
                </div>
                <div class="mb-3">
                    <label>الحالة</label>
                    <input type="text" name="status" value="{{ $process->status ?? '' }}" class="form-control">
                </div>
                <div class="mb-3">
                    <label>نسبة التقدم</label>
                    <input type="number" name="progress" value="{{ $process->progress ?? 0 }}" class="form-control" max="100">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">العنوان الفرعي (AR)</label>
                        <input type="text" name="title_ar" class="form-control" value="{{ $process->title_ar ?? '' }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">العنوان الفرعي (EN)</label>
                        <input type="text" name="title_en" class="form-control" value="{{ $process->title_en ?? '' }}">
                    </div>
                </div>

                <!-- الوصف -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الوصف (AR)</label>
                        <textarea name="desc_ar" class="form-control" rows="3">{{ $process->desc_ar ?? '' }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الوصف (EN)</label>
                        <textarea name="desc_en" class="form-control" rows="3">{{ $process->desc_en ?? '' }}</textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">نص الزر (AR)</label>
                        <input type="text" name="button_text_ar" value="{{ $process->button_text_ar  }}" class="form-control" >
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">نص الزر (EN)</label>
                        <input type="text" name="button_text_en" value="{{ $process->button_text_en  }}" class="form-control">
                    </div>
              </div>


            </div>
        </div>

        <!-- الخطوات -->
        <div class="card">
            <div class="card-header bg-secondary text-white">الخطوات</div>
            <div class="card-body" id="steps-wrapper">
                @foreach($process->steps ?? [] as $i => $step)
                    <div class="row mb-3 step-row align-items-center">
                        <input type="hidden" name="steps[{{ $i }}][id]" value="{{ $step->id }}">

                        <div class="col-md-2">
                            <label>الأيقونة</label>
                            <input type="file" name="steps[{{ $i }}][icon]" class="form-control">
                            @if($step->icon)
                                <img src="{{ asset('storage/'.$step->icon) }}" width="60" class="mt-2">
                            @endif
                        </div>

                        <div class="col-md-2">
                            <input type="text" name="steps[{{ $i }}][title_ar]" class="form-control"
                                   placeholder="العنوان AR" value="{{ $step->title_ar }}">
                        </div>

                        <div class="col-md-2">
                            <input type="text" name="steps[{{ $i }}][title_en]" class="form-control"
                                   placeholder="العنوان EN" value="{{ $step->title_en }}">
                        </div>

                        <div class="col-md-2">
                            <textarea name="steps[{{ $i }}][desc_ar]" rows="2" class="form-control"
                                      placeholder="الوصف AR">{{ $step->desc_ar }}</textarea>
                        </div>

                        <div class="col-md-2">
                            <textarea name="steps[{{ $i }}][desc_en]" rows="2" class="form-control"
                                      placeholder="الوصف EN">{{ $step->desc_en }}</textarea>
                        </div>

                        <div class="col-md-1">
                            <input type="number" name="steps[{{ $i }}][order]" class="form-control"
                                   placeholder="ترتيب" value="{{ $step->order }}">
                        </div>

                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger btn-sm remove-step">X</button>
                        </div>
                    </div>
                @endforeach
            </div>
            <button type="button" id="add-step" class="btn btn-secondary mt-2">إضافة خطوة</button>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">حفظ</button>
        </div>
    </form>
</div><script>
    document.getElementById('add-step').addEventListener('click', function () {
        const wrapper = document.getElementById('steps-wrapper');
        const index = wrapper.querySelectorAll('.step-row').length;

        const row = document.createElement('div');
        row.classList.add('row', 'mb-3', 'step-row', 'align-items-center');
        row.innerHTML = `
            <div class="col-md-2">
                <label>الأيقونة</label>
                <input type="file" name="steps[${index}][icon]" class="form-control">
            </div>
            <div class="col-md-2">
                <input type="text" name="steps[${index}][title_ar]" class="form-control" placeholder="العنوان AR">
            </div>
            <div class="col-md-2">
                <input type="text" name="steps[${index}][title_en]" class="form-control" placeholder="العنوان EN">
            </div>
            <div class="col-md-2">
                <textarea name="steps[${index}][desc_ar]" rows="2" class="form-control" placeholder="الوصف AR"></textarea>
            </div>
            <div class="col-md-2">
                <textarea name="steps[${index}][desc_en]" rows="2" class="form-control" placeholder="الوصف EN"></textarea>
            </div>
            <div class="col-md-1">
                <input type="number" name="steps[${index}][order]" class="form-control" placeholder="ترتيب">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-sm remove-step">X</button>
            </div>
        `;
        wrapper.appendChild(row);
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-step')) {
            e.target.closest('.step-row').remove();
        }
    });
    </script>
@endsection
