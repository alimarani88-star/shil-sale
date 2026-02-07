@extends('Admin.Layout.master')

@section('head-tag')
    <title>ویرایش اطلاعات سوالات متداول</title>
@endsection

@section('content')
    <div class="container-fluid pt-2">
        <nav aria-label="breadcrumb" class="mt-0 px-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item font-size-12"><a href="#">خانه</a></li>
                <li class="breadcrumb-item font-size-12"><a href="#">تنظیمات</a></li>
                <li class="breadcrumb-item font-size-12 active" aria-current="page">ویرایش اطلاعات سوالات متداول</li>
            </ol>
        </nav>

        <section class="row">
            <div class="col-12">
                <section class="main-body-container">
                    <section class="card shadow-sm border-0 rounded-3">

                        <row class="card-header bg-light d-flex justify-content-between align-items-center">
                            <div class="col-md-11">
                                <h5 class="mb-0">مدیریت سوالات متداول</h5>
                            </div>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#addFaqForm">
                                <i class="fa fa-plus"></i> افزودن سوال جدید
                            </button>
                        </row>

                        {{-- فرم افزودن سوال --}}
                        <div class="collapse border-top" id="addFaqForm">
                            <div class="card-body">
                                <form action="{{ route('A_s_edit_frequently_asked_questions') }}" method="POST">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label class="form-label">سوال</label>
                                            <input type="text" name="question" class="form-control"
                                                placeholder="متن سوال را وارد کنید">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                        <label class="form-label">پاسخ</label>
                                        <textarea type="text" name="answer" class="form-control" autocomplete="off"
                                            placeholder="پاسخ سوال را وارد کنید"></textarea>
                                    </div>
                                    </div>
                                    <div class="mt-3 text-end">
                                        <button type="submit" class="btn btn-primary px-4">
                                            <i class="fa fa-save"></i> ذخیره
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- جدول سوالات --}}
                        <div class="card-body">
                            <table class="table table-bordered align-middle text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">#</th>
                                        <th>سوال</th>
                                        <th>پاسخ</th>
                                        <th width="180">عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($questions_answers as $faq)
                                        <tr id="row-{{ $faq['id'] }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td class="text-start">
                                                <span class="text-view">{{ $faq['question'] }}</span>
                                                <input type="text" name="question" class="form-control d-none text-edit"
                                                    value="{{ $faq['question'] }}">
                                            </td>
                                            <td class="text-start">
                                                <span class="text-view">{{ $faq['answer'] }}</span>
                                                <input type="text" name="answer" class="form-control d-none text-edit"
                                                    value="{{ $faq['answer'] }}">
                                            </td>
                                            <td>
                                                {{-- دکمه‌ها در حالت مشاهده --}}
                                                <div class="btn-group btn-view">
                                                    <button class="btn btn-sm btn-warning ml-3 rounded"
                                                        onclick="editRow({{ $faq['id'] }})">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm delete-btn"
                                                        data-id="{{ $faq['id'] }}">
                                                        <i class="fa fa-trash"></i>
                                                    </button>

                                                </div>

                                                {{-- دکمه‌ها در حالت ویرایش --}}
                                                <div class="btn-group btn-edit d-none" style="margin-right: 1.2rem;">
                                                    <form
                                                        action="{{ route('A_update_frequently_asked_questions', ['id' => $faq['id']]) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="question" class="input-question">
                                                        <input type="hidden" name="answer" class="input-answer">
                                                        <button type="submit" class="btn btn-sm btn-success"
                                                            onclick="saveRow({{ $faq['id'] }})">
                                                            <i class="fa fa-check"></i>
                                                        </button>
                                                    </form>
                                                    <button class="btn btn-sm btn-secondary rounded"
                                                        style="margin-left: 1rem;margin-right: 1rem;height: 1.6rem;"
                                                        onclick="cancelEdit({{ $faq['id'] }})">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-muted">هیچ سوالی ثبت نشده است.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </section>
                </section>
            </div>
        </section>
    </div>

    @extends('Admin.Layout.script')
    {{-- اسکریپت ویرایش درجا --}}
    <script>
        function editRow(id) {
            const row = document.getElementById('row-' + id);
            row.querySelectorAll('.text-view').forEach(el => el.classList.add('d-none'));
            row.querySelectorAll('.text-edit').forEach(el => el.classList.remove('d-none'));
            row.querySelector('.btn-view').classList.add('d-none');
            row.querySelector('.btn-edit').classList.remove('d-none');
        }

        function cancelEdit(id) {
            const row = document.getElementById('row-' + id);
            row.querySelectorAll('.text-view').forEach(el => el.classList.remove('d-none'));
            row.querySelectorAll('.text-edit').forEach(el => el.classList.add('d-none'));
            row.querySelector('.btn-view').classList.remove('d-none');
            row.querySelector('.btn-edit').classList.add('d-none');
        }

        function saveRow(id) {
            const row = document.getElementById('row-' + id);
            const question = row.querySelector('input[name="question"]').value;
            const answer = row.querySelector('input[name="answer"]').value;
            row.querySelector('.input-question').value = question;
            row.querySelector('.input-answer').value = answer;
        }

        $(document).on('click', '.delete-btn', function () {
            let id = $(this).data('id');


            Swal.fire({
                title: 'آیا مطمئن هستید؟',
                text: "بعد از حذف، امکان بازگردانی وجود ندارد!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'بله، حذف شود',
                cancelButtonText: 'انصراف'
            }).then((result) => {
                if (result.value == true) {

                    $.ajax({
                        url: "{{ route('A_delete_frequently_asked_questions') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: id
                        },
                        success: function (response) {

                            Swal.fire({
                                title: 'حذف شد!',
                                text: 'سوال با موفقیت حذف شد.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // حذف ردیف از جدول بدون رفرش صفحه
                            $(`button[data-id='${id}']`).closest('tr').fadeOut(400, function () {
                                $(this).remove();
                            });
                        },
                        error: function (xhr) {
                            Swal.fire({
                                title: 'خطا!',
                                text: 'مشکلی در حذف داده پیش آمد.',
                                icon: 'error',
                                confirmButtonText: 'باشه'
                            });
                        }
                    });
                }
            });
        });
    </script>
@endsection