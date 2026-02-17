@extends('Admin.Layout.master')

@section('head-tag')

    <title>بسته بندی مرسولات پستی</title>
    <style>
        .patterns {
            gap: 20px
        }

        .pattern {
            width: clamp(250px, 20%, 300px);
        }
    </style>
    <style>
        #editPatternModal table {
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
        }

        #editPatternModal thead th {
            background-color: #f1f3f5;
            color: #333;
            font-weight: 600;
            text-align: center;
            vertical-align: middle;
        }

        #editPatternModal tbody td {
            vertical-align: middle;
            padding: 8px;
        }

        #editPatternModal tbody tr {
            transition: background-color 0.2s ease;
        }

        #editPatternModal tbody tr:hover {
            background-color: #f8f9fa;
        }

        #editPatternModal .carton-select {
            min-width: 220px;
        }

        #editPatternModal .qty-input {
            text-align: center;
            max-width: 120px;
            margin: auto;
        }

        #editPatternModal .btnRemoveRow {
            padding: 4px 10px;
        }

        #editPatternModal .btnRemoveRow i {
            pointer-events: none;
        }

        #editPatternModal .is-invalid {
            border-color: #dc3545;
        }

        #editPatternModal .empty-row td {
            text-align: center;
            color: #999;
            font-style: italic;
        }

        #editPatternModal .modal-header {
            background-color: #198754;
            color: #fff;
        }

        #editPatternModal .modal-header .btn-close {
            filter: invert(1);
        }
    </style>

@endsection

@section('content')
    <div class="container-fluid pt-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">خانه</a></li>
                <li class="breadcrumb-item ">بسته بندی</li>
                <li class="breadcrumb-item active">بسته بندی پستی</li>
            </ol>
        </nav>

        <div class="card ">
            <div class="card-header">
                <h3>الگو های بسته بندی کارتن های پستی</h3>
            </div>
            <div class="card-body">
                <div class="row py-3">
                    <div class="col-12 col-sm-6 col-md-3">
                        <label>انتخاب کارتن پست</label>
                        <select id="cartons-list" class="single-select form-select">
                            <option value="0"> کارتن پست(طول * عرض * ارتفاع)</option>
                            @foreach($post_cartons as $carton)
                                <option value="{{$carton->id}}">{{$carton->name}} ({{$carton->length}}
                                    * {{$carton->width}} * {{$carton->height}})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div id="patterns" class="patterns d-flex flex-row flex-wrap mt-4">
                </div>

            </div>
            <div class="card-footer"></div>
        </div>
    </div>
    <!-- Edit Pattern Modal -->
    <div class="modal fade" id="editPatternModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">ویرایش الگو</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-muted">می‌تونی کارتن اضافه/حذف کنی و تعدادها رو تغییر بدی</div>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="btnAddRow">
                            <i class="fa fa-plus me-1"></i> افزودن کارتن
                        </button>
                    </div>
                    <div id="editPatternAlert"></div>

                    <input type="hidden" id="edit_pattern_id">
                    <input type="hidden" id="edit_carton_parent_id">

                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-hover align-middle mb-0">
                            <thead>
                            <tr>
                                <th>کارتن</th>
                                <th style="width: 180px;">تعداد</th>
                                <th style="width: 90px;">حذف</th>
                            </tr>
                            </thead>
                            <tbody id="editPatternTbody"></tbody>

                            <!-- rows by js -->
                            </tbody>
                        </table>
                    </div>


                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">بستن</button>
                    <button type="button" id="btnSavePattern" class="btn btn-success">
                        <span class="save-text">ذخیره تغییرات</span>
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
        const CARTONS = @json($company_cartons->map(fn($c)=>['id'=>$c->id,'name'=>$c->name])->values());

        function cartonsOptionsHtml(selectedId = null) {
            let html = `<option value="">انتخاب کارتن...</option>`;
            CARTONS.forEach(c => {
                const sel = (c.id == selectedId) ? 'selected' : '';
                html += `<option value="${c.id}" ${sel}>${c.name}</option>`;
            });
            return html;
        }

        function makeRowHtml({ pattern_details_id = null, carton_id = null, carton_name = null, quantity = 0 } = {}) {
            return `
    <tr data-pattern-details-id="${pattern_details_id ?? ''}">
        <td>
            <select class="form-select carton-select">
                ${cartonsOptionsHtml(carton_id)}
            </select>
        </td>
        <td>
            <input type="number" class="form-control qty-input" min="0" value="${quantity ?? 0}">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger btnRemoveRow">
                <i class="fa fa-trash"></i>
            </button>
        </td>
    </tr>`;
        }

        $(document).ready(function() {

            function showLoading() {
                $('#patterns').html(`
                <div class="w-100 d-flex justify-content-center align-items-center py-5">
                    <div class="text-center">
                        <div class="spinner-border" role="status" aria-hidden="true"></div>
                        <div class="mt-2">در حال دریافت اطلاعات...</div>
                    </div>
                </div>
            `);
            }

            function showError(msg) {
                $('#patterns').html(`
                <div class="alert alert-danger mb-0 w-100">
                    ${msg || 'خطا در دریافت اطلاعات'}
                </div>
            `);
            }

            $('#cartons-list').on('change', function() {
                const carton_id = $(this).val();

                if (!carton_id || carton_id === '0') {
                    $('#patterns').empty();
                    return;
                }

                $.ajax({
                    url: '/ajax_post_packing_list',
                    type: 'GET',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        carton_id: carton_id
                    },

                    beforeSend: function() {
                        $('#cartons-list').prop('disabled', true);
                        showLoading();
                    },

                    success: function(response) {
                        if (response.status !== 'success') {
                            showError(response.message || 'خطایی رخ داد');
                            return;
                        }

                        const $parent = $('#patterns');
                        $parent.empty();

                        const data = response.data || {};
                        const patternIds = Object.keys(data);

                        if (patternIds.length === 0) {
                            $parent.html(`
                            <div class="alert alert-warning mb-0 w-100">
                                هیچ الگویی برای این کارتن پیدا نشد.
                            </div>
                        `);
                            return;
                        }

                        patternIds.forEach((patternId, index) => {
                            const rows = data[patternId] || [];

                            let bodyRowsHtml = '';
                            rows.forEach(r => {
                                bodyRowsHtml += `
                                <p class="card-text mb-0">
                                    ${r.carton_name} : <b class="float-left">${r.quantity} عدد</b>
                                </p>
                            `;
                            });

                            const rowsJson = encodeURIComponent(JSON.stringify(rows));
                            const cardHtml = `
                            <div class="pattern" data-pattern-id="${patternId}" data-rows="${rowsJson}">
                                <div class="card text-bg-light mb-3" style="min-height:160px;">
                                    <div class="card-header bg-success">
                                        <div class="d-flex flex-row justify-content-between w-100">
                                            <h3 class="text-white">الگو ${index + 1}</h3>

                                            <div class="dropdown">
                                                <button class="btn float-left dropdown-toggle text-white" type="button"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                    عملیات
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item edit-pattern" style="color: #0a6aa1 !important;" href="#"
                                                           data-pattern-id="${patternId}">
                                                            <i class="fa fa-pencil mx-1" aria-hidden="true"></i>ویرایش
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item delete-pattern " style="color: #e41653 !important;" href="#"
                                                           data-pattern-id="${patternId}">
                                                            <i class="fa fa-trash mx-1" aria-hidden="true"></i>حذف
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="card-body">
                                        ${bodyRowsHtml || '<div class="text-muted">جزئیاتی ثبت نشده</div>'}
                                    </div>
                                </div>
                            </div>
                        `;

                            $parent.append(cardHtml);
                        });
                        $parent.append(`
                          <div class="pattern">
                            <div class="card text-bg-light mb-3 h-100" style="min-height:160px; cursor:pointer;" id="btnCreatePattern">
                              <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <div style="font-size:60px; line-height:60px;">+</div>
                                <div class="mt-2 text-muted">ایجاد الگوی جدید</div>
                              </div>
                            </div>
                          </div>
                        `);

                    },

                    error: function(xhr) {
                        showError(xhr.responseJSON?.message || xhr.responseText || 'خطا در ارتباط با سرور');
                    },

                    complete: function() {
                        // پایان لودینگ
                        $('#cartons-list').prop('disabled', false);
                    }
                });
            });

            $('.single-select').select2({
                allowClear: true,
                language: 'fa',
                dir: 'rtl',
                placeholder: 'انتخاب کارتن'
            });

            $(document).on('click', '#btnCreatePattern', function () {
                const parentCartonId = $('#cartons-list').val();

                $('#edit_pattern_id').val('');
                $('#edit_carton_parent_id').val(parentCartonId);

                $('#editPatternAlert').html('');
                const $tbody = $('#editPatternTbody');
                $tbody.empty();
                $tbody.append(makeRowHtml({quantity: 0}));
                $('#editPatternModal .modal-title').text('ایجاد الگوی جدید');

                new bootstrap.Modal(document.getElementById('editPatternModal')).show();
            });


        });

        $(document).on('click', '.edit-pattern', function(e) {
            e.preventDefault();

            const patternId = $(this).data('pattern-id');
            const $patternEl = $(`.pattern[data-pattern-id="${patternId}"]`);

            const rows = JSON.parse(decodeURIComponent($patternEl.attr('data-rows') || '[]'));
            const parentCartonId = $('#cartons-list').val();

            $('#edit_pattern_id').val(patternId);
            $('#edit_carton_parent_id').val(parentCartonId);
            $('#editPatternAlert').html('');

            const $tbody = $('#editPatternTbody');
            $tbody.empty();

            if (!rows.length) {
                $tbody.append(makeRowHtml({ quantity: 0 }));
            } else {
                rows.forEach(r => {
                    $tbody.append(makeRowHtml({
                        pattern_details_id: r.pattern_details_id,
                        carton_id: r.carton_id,
                        quantity: r.quantity
                    }));
                });
            }

            $('#editPatternModal .modal-title').text('ویرایش الگو');


            new bootstrap.Modal(document.getElementById('editPatternModal')).show();
        });

        $('#btnSavePattern').on('click', function() {

            const pattern_id = $('#edit_pattern_id').val();
            const parent_carton_id = $('#edit_carton_parent_id').val();

            // جمع‌آوری وضعیت نهایی
            const items = [];
            let hasError = false;

            $('#editPatternTbody tr').each(function() {
                const pattern_details_id = $(this).attr('data-pattern-details-id') || null;
                const carton_id = $(this).find('.carton-select').val();
                const quantity = $(this).find('.qty-input').val();

                if (!carton_id) {
                    hasError = true;
                    $(this).find('.carton-select').addClass('is-invalid');
                } else {
                    $(this).find('.carton-select').removeClass('is-invalid');
                }

                items.push({
                    pattern_details_id: pattern_details_id ? Number(pattern_details_id) : null,
                    carton_id: Number(carton_id || 0),
                    quantity: Number(quantity || 0)
                });
            });

            if (hasError) {
                $('#editPatternAlert').html(`<div class="alert alert-danger">لطفاً همه ردیف‌ها کارتن داشته باشند.</div>`);
                return;
            }

            const cartonIds = items.map(x => x.carton_id).filter(Boolean);
            const dup = cartonIds.find((v, i) => cartonIds.indexOf(v) !== i);
            if (dup) {
                $('#editPatternAlert').html(`<div class="alert alert-danger">یک کارتن را دوبار انتخاب شده لطفاً تکراری‌ها را حذف کنید.</div>`);
                return;
            }

            $('#btnSavePattern').prop('disabled', true);
            $('#btnSavePattern .save-text').addClass('d-none');
            $('#btnSavePattern .save-loading').removeClass('d-none');
            $('#editPatternAlert').html('');


            if (!pattern_id) {
                $.ajax({
                    url: '/ajax_post_packing_create_pattern',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        carton_id: parent_carton_id
                    },
                    success: function (r) {
                        if (r.status !== 'success' || !r.pattern_id) {
                            $('#editPatternAlert').html(`<div class="alert alert-danger">${r.message || 'خطا در ساخت الگو'}</div>`);
                            return;
                        }

                        savePatternDetails(r.pattern_id, parent_carton_id, items);
                    },
                    error: function (xhr) {
                        $('#editPatternAlert').html(`<div class="alert alert-danger">${xhr.responseJSON?.message || 'خطا در ساخت الگو'}</div>`);
                    }
                });

                return;
            }
            savePatternDetails(pattern_id, parent_carton_id, items);
        });
        function savePatternDetails(pattern_id, parent_carton_id, items) {
            $.ajax({
                url: '/ajax_post_packing_update',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    pattern_id: pattern_id,
                    carton_id: parent_carton_id,
                    items: items
                },
                success: function (res) {
                    if (res.status === 'success') {
                        $('#editPatternAlert').html(`<div class="alert alert-success">ذخیره شد ✅</div>`);

                        setTimeout(() => {
                            bootstrap.Modal.getInstance(document.getElementById('editPatternModal')).hide();
                            $('#cartons-list').trigger('change');
                        }, 400);
                    } else {
                        $('#editPatternAlert').html(`<div class="alert alert-danger">${res.message || 'خطا در ذخیره'}</div>`);
                    }
                },
                error: function (xhr) {
                    $('#editPatternAlert').html(`
                <div class="alert alert-danger">
                    ${xhr.responseJSON?.message || xhr.responseText || 'خطا در ارتباط با سرور'}
                </div>
            `);
                },
                complete: function () {
                    $('#btnSavePattern').prop('disabled', false);
                    $('#btnSavePattern .save-text').removeClass('d-none');
                    $('#btnSavePattern .save-loading').addClass('d-none');
                }
            });
        }

        $(document).on('click', '#btnAddRow', function() {
            $('#editPatternTbody').append(makeRowHtml({ quantity: 0 }));
        });

        $(document).on('click', '.btnRemoveRow', function() {
            $(this).closest('tr').remove();
        });
        $(document).on('click', '.btnRemoveRow', function() {
            $(this).closest('tr').fadeOut(200, function() {
                $(this).remove();
            });
        });


    </script>

@endsection
