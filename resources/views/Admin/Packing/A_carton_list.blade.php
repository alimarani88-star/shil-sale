@extends('Admin.Layout.master')

@section('head-tag')
    <title>مدیریت جعبه ها</title>
    <style>
        .card-body::after, .card-footer::after, .card-header::after{
            content: unset !important;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid pt-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">خانه</a></li>
                <li class="breadcrumb-item">بسته بندی</li>
                <li class="breadcrumb-item active">مدیریت جعبه ها</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">لیست جعبه‌ها</h4>
                <button class="btn btn-success" id="btnAddBox">افزودن جعبه</button>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead>
                        <tr class="text-center">
                            <th>#</th>
                            <th>نام</th>
                            <th>طول</th>
                            <th>عرض</th>
                            <th>ارتفاع</th>
                            <th>شناسه پستی</th>
                            <th>وزن جعبه</th>
                            <th>نوع</th>
                            <th>عملیات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($cartons as $i => $c)
                            <tr class="text-center">
                                <td>{{ $i+1 }}</td>
                                <td class="text-start">{{ $c->name }}</td>
                                <td>{{ $c->length }}</td>
                                <td>{{ $c->width }}</td>
                                <td>{{ $c->height }}</td>
                                <td>{{ $c->id_post }}</td>
                                <td>{{ $c->box_weight }}</td>
                                <td>{{ $c->type_fa }}</td>
                                <td>
                                    @if($c->type == 'company')
                                    <button
                                        class="btn btn-sm btn-outline-primary btnEditBox"
                                        data-id="{{ $c->id }}"
                                        data-name="{{ $c->name }}"
                                        data-length="{{ $c->length }}"
                                        data-width="{{ $c->width }}"
                                        data-height="{{ $c->height }}"
                                        data-id_post="{{ $c->id_post }}"
                                        data-box_weight="{{ $c->box_weight }}"
                                        data-type="{{ $c->type }}"
                                    >ویرایش</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="boxModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="boxModalTitle">افزودن جعبه</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div id="boxModalAlert"></div>

                    <input type="hidden" id="box_id">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">نام</label>
                            <input type="text" id="box_name" class="form-control">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">طول</label>
                            <input type="number" id="box_length" class="form-control" min="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">عرض</label>
                            <input type="number" id="box_width" class="form-control" min="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">ارتفاع</label>
                            <input type="number" id="box_height" class="form-control" min="0">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">id_post</label>
                            <input type="number" id="box_id_post" class="form-control" min="0">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">وزن جعبه</label>
                            <input type="number" id="box_weight" class="form-control" min="0">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">نوع</label>
                            <select id="box_type" class="form-select">
                                <option value="company">شرکتی</option>
                                <option disabled value="post">پستی</option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">بستن</button>
                    <button class="btn btn-success" id="btnSaveBox">
                        <span class="save-text">ذخیره</span>
                        <span class="save-loading d-none">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            در حال ذخیره...
                        </span>
                    </button>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function openBoxModal(mode, data = {}) {
            $('#boxModalAlert').html('');
            $('#box_id').val(data.id || '');

            $('#box_name').val(data.name || '');
            $('#box_length').val(data.length ?? 0);
            $('#box_width').val(data.width ?? 0);
            $('#box_height').val(data.height ?? 0);
            $('#box_id_post').val(data.id_post ?? 0);
            $('#box_weight').val(data.box_weight ?? 0);
            $('#box_type').val(data.type || 'company');

            $('#boxModalTitle').text(mode === 'edit' ? 'ویرایش جعبه' : 'افزودن جعبه');

            new bootstrap.Modal(document.getElementById('boxModal')).show();
        }

        $(document).on('click', '#btnAddBox', function () {
            openBoxModal('create');
        });

        $(document).on('click', '.btnEditBox', function () {
            openBoxModal('edit', {
                id: $(this).data('id'),
                name: $(this).data('name'),
                length: $(this).data('length'),
                width: $(this).data('width'),
                height: $(this).data('height'),
                id_post: $(this).data('id_post'),
                box_weight: $(this).data('box_weight'),
                type: $(this).data('type'),
            });
        });

        $(document).on('click', '#btnSaveBox', function () {

            const payload = {
                _token: $('meta[name="csrf-token"]').attr('content'),
                id: $('#box_id').val(),
                name: $('#box_name').val(),
                length: $('#box_length').val(),
                width: $('#box_width').val(),
                height: $('#box_height').val(),
                id_post: $('#box_id_post').val(),
                box_weight: $('#box_weight').val(),
                type: $('#box_type').val(),
            };

            $('#btnSaveBox').prop('disabled', true);
            $('#btnSaveBox .save-text').addClass('d-none');
            $('#btnSaveBox .save-loading').removeClass('d-none');

            $.ajax({
                url: '/ajax_carton_list',
                type: 'POST',
                data: payload,
                success: function (res) {
                    if (res.status === 'success') {
                        $('#boxModalAlert').html(`<div class="alert alert-success mb-0">ذخیره شد ✅</div>`);
                        setTimeout(() => location.reload(), 250);
                    } else {
                        $('#boxModalAlert').html(`<div class="alert alert-danger mb-0">${res.message || 'خطا'}</div>`);
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        const errs = xhr.responseJSON.errors;
                        let html = '<div class="alert alert-danger mb-0"><ul class="mb-0">';
                        Object.keys(errs).forEach(k => html += `<li>${errs[k][0]}</li>`);
                        html += '</ul></div>';
                        $('#boxModalAlert').html(html);
                    } else {
                        $('#boxModalAlert').html(`<div class="alert alert-danger mb-0">${xhr.responseJSON?.message || 'خطا در ارتباط'}</div>`);
                    }
                },
                complete: function () {
                    $('#btnSaveBox').prop('disabled', false);
                    $('#btnSaveBox .save-text').removeClass('d-none');
                    $('#btnSaveBox .save-loading').addClass('d-none');
                }
            });
        });
    </script>
@endsection
