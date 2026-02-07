@extends('Admin.Layout.master')

@section('head-tag')
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/select2/select2.min.css') }}">
    <title>بسته بندی محصولات</title>

    <style>
        /*استایل فلش زدن */
        @keyframes flash {
            0% {
                background-color: transparent;
                color: #374151;
            }
            50% {
                background-color: #bababa;
                color: #b45309;
            }
            100% {
                background-color: transparent;
                color: #9ca3af;
            }
        }

        .flash-animation {
            animation: flash 0.6s ease-in-out;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid pt-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">خانه</a></li>
                <li class="breadcrumb-item ">بسته بندی</li>
                <li class="breadcrumb-item active">بسته بندی محصولات</li>
            </ol>
        </nav>

        <section class="row">
            <div class="col-12">
                <section class="main-body-container">
                    <section class="d-flex justify-content-between align-items-center mt-4 mb-3 border-bottom pb-2">
                        <button id="open-modal" class="btn btn-info btn-sm" type="button" data-bs-toggle="modal"
                                data-bs-target="#packing-add">
                            <i class="fa fa-plus"></i> ایجاد بسته جدید
                        </button>
                    </section>

                    <section class="table-responsive px-3">
                        <table id="posts-table" class="table table-striped table-hover">
                            <thead>
                            <tr class="text-center">
                                <th>#</th>
                                <th>محصول</th>
                                <th>گروه محصول</th>
                                <th>کارتن</th>
                                <th>حداکثر تعداد</th>
                                <th>عملیات</th>
                            </tr>
                            <tr id="filter-row">
                                <th></th>
                                <th><input type="text" placeholder="جستجو محصول" class="form-control form-control-sm" /></th>
                                <th><input type="text" placeholder="جستجو گروه" class="form-control form-control-sm" /></th>
                                <th><input type="text" placeholder="جستجو کارتن" class="form-control form-control-sm" /></th>
                                <th><input type="text" placeholder="جستجو تعداد" class="form-control form-control-sm" /></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </section>
                </section>
            </div>
        </section>
    </div>
    <div class="modal fade" style="z-index: 9998" id="packing-add" aria-hidden="true" aria-labelledby="packing-add"
         tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="packing-add-title">تعریف بسته بندی </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col d-flex flex-column pb-3 rounded">
                            <label>انتخاب محصول</label>
                            <select class="single-select-product " id="product" name="product">
                                <option value="0">انتخاب محصول</option>
                                @foreach($products as $product )
                                    <option value="{{$product->product_id_in_app}}">{{$product->product_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col d-flex flex-column pb-3 rounded">
                            <label>انتخاب گروه محصول</label>
                            <select class="single-select-group form-select" id="group">
                                <option value="0">انتخاب گروه محصول</option>
                                @foreach($groups as $group)
                                    <option value="{{$group['id']}}">{{$group['title']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col d-flex flex-column pb-3 rounded">
                            <label>انتخاب کارتن</label>
                            <select class="single-select-carton form-select" id="carton">
                                <option value="0">انتخاب کارتن</option>
                                @foreach($cartons as $carton)
                                    <option value="{{$carton->id}}">{{$carton->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col d-flex flex-column pb-3 rounded">
                            <label>حداکثر گنجایش</label>
                            <input class="form-control text-center" type="number" id="max_number" required>
                            <span class="input-loader input-loader-max-number position-absolute "
                                  style="left: 12px;top: 36px ; display: none"></span>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <ul class="alert w-100" style="display: none">
                    </ul>
                    <button style="width: 62.42px ; height: 38px ; padding: 3px" class="btn btn-primary"
                            id="save-package">
                        ذخیره
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')

    <script src="{{ asset('admin-assets/vendor/select2/select2.min.js') }}"></script>
    <script>

        $(document).ready(function() {

            let table = $('#posts-table').DataTable({
                ajax: "{{ route('ajax_product_packing_list') }}",
                columns: [
                    { data: null },
                    { data: 'product_name' },
                    { data: 'group_name' },
                    { data: 'carton_name' },
                    { data: 'max_number' },
                    {
                        data: 'id',
                        render: function(data, type, row ) {
                            return `
                        <a href="#" class="text-info package-edit" data-id="${data}"  data-product_id="${row.product_id}" data-group_id="${row.group_id}" data-carton_id="${row.carton_id}"><i class="fa fa-pencil"></i></a>
                        <a href="#" class="text-danger package-delete" data-id="${data}" data-product_name="${row.product_name}" data-group_name="${row.group_name}" data-carton_name="${row.carton_name}"><i class="fa fa-trash"></i></a>
                    `;
                        }
                    },
                ],
                columnDefs: [
                    { targets: '_all', className: 'text-center' },
                    { targets: 0, render: function (data, type, row, meta) { return meta.row + 1; } }
                ],
                initComplete: function () {
                    this.api().columns().every(function (index) {
                        let column = this;
                        let input = $('#filter-row th').eq(index).find('input');
                        if (input.length > 0) {
                            input.on('keyup change clear', function () {
                                if (column.search() !== this.value) {
                                    column.search(this.value).draw();
                                }
                            });
                        }
                    });
                }
            });



            $('[class*=single-select-]').on('change', function() {
                let carton_id = parseInt($('#carton').val()) || 0;
                let product_id = 0;
                let group_id = 0;
                if ($(this).val()) {
                    if ($(this).attr('id') === 'product') {
                        product_id = parseInt($('#product').val()) || 0;
                        group_id = 0;
                    } else if ($(this).attr('id') === 'group') {
                        product_id = 0;
                        group_id = parseInt($('#group').val()) || 0;
                    } else {
                        product_id = parseInt($('#product').val()) || 0;
                        group_id = parseInt($('#group').val()) || 0;
                    }
                    if (carton_id && (product_id || group_id)) {
                        $('.input-loader-max-number').show();
                        $.ajax({
                            url: '{{route('ajax_fetch_packing')}}',
                            type: 'GET',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                product_id,
                                group_id,
                                carton_id
                            },
                            success: function(response) {
                                $('.input-loader-max-number').hide();
                                if (response.status === 'success') {
                                    if (response.data != null) {
                                        $('#max_number').val(response.data.max_number);
                                    }
                                }
                            }
                        });
                    }
                }
            });

            $('#save-package').on('click', function(e) {
                const btn = $(this);
                btn.html('<span class="input-loader" style="left: 12px;top: 36px ; "></span>');

                $('.alert').html('').hide(); // اول خطاهای قبلی را پاک کن

                const carton_id = parseInt($('#carton').val()) || 0;
                const product_id = parseInt($('#product').val()) || 0;
                const group_id = parseInt($('#group').val()) || 0;
                const max_number = parseInt($('#max_number').val()) || 0;
                let isErr = 0;
                if (!product_id && !group_id) {
                    $('.alert')
                        .append('<li class="text-danger">یکی از فیلدهای محصول یا گروه محصول الزامی است.</li>')
                        .show();
                    isErr = 1;
                }
                if (!carton_id) {
                    $('.alert')
                        .append('<li class="text-danger">انتخاب کارتن الزامی است.</li>')
                        .show();
                    isErr = 1;
                }
                if (!max_number) {
                    $('.alert')
                        .append('<li class="text-danger">حداکثر گنجایش الزامی است.</li>')
                        .show();
                    isErr = 1;
                }
                if (isErr === 0) {
                    $.ajax({
                        url: '{{route('ajax_save_packing')}}',
                        type: 'GET',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            product_id,
                            group_id,
                            carton_id,
                            max_number
                        },
                        success: function(response) {
                            btn.html('ذخیره');
                            if (response.status === 'success') {
                                var myModalEl = document.getElementById('packing-add');
                                var modal = bootstrap.Modal.getInstance(myModalEl);
                                modal.hide();
                                table.ajax.reload(null, false);
                                Swal.fire({
                                    title: 'موفق',
                                    text: response.message,
                                    icon: response.status,
                                    showConfirmButton: false,
                                    timer: 1000
                                });
                            } else {
                                $('.alert')
                                    .append('<li class="text-danger">' + response.message + '</li>')
                                    .show();
                            }
                        }
                    });
                } else {
                    setTimeout(function() {
                        btn.html('ذخیره');
                    }, 300);
                }

            });

            $(document).on('click', '.package-delete', function(e) {
                e.preventDefault();
                const product_name = $(this).attr('data-product_name');
                const group_name = $(this).attr('data-group_name');
                const carton_name = $(this).attr('data-carton_name');
                const package_id = $(this).attr('data-id');
                let text ='' ;
                if(product_name.length > 1){
                    text = `بسته مربوط به محصول ${product_name} و کارتن ${carton_name} حذف شود؟`;
                }else {
                    text = `بسته مربوط به گروه ${group_name} و کارتن ${carton_name} حذف شود؟`;
                }

                Swal.fire({
                    title: 'اطمینان دارید!',
                    text: text,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'بله، حذف شود',
                    cancelButtonText: 'انصراف'
                }).then((result) => {

                    if(result.value){
                        location.href= 'A_delete_packing/'+ package_id
                    }
                });
            });

            $(document).on('click', '.package-edit', function(e) {
                e.preventDefault();
                $('.modal li').remove();
                $('#open-modal').click();
                const cartonId = $(this).data('carton_id');
                const productId = $(this).data('product_id');
                const groupId = $(this).data('group_id');

                $('#carton').val(cartonId).trigger('change');
                $('#product').val(productId).trigger('change');
                $('#group').val(groupId).trigger('change');
                $('#packing-add-title').text('ویرایش بسته بندی')
            });
            $('#open-modal').on('click' , function() {
                $('#packing-add-title').text('تعریف بسته بندی')
            })
        });
        $(document).ready(function() {
            // ─────────────────────────────────────────────────────────
            //  Select2 با allowClear -- اجرا دوباره select2 به دلیل نیاز به پاک کردن دیگری
            // ─────────────────────────────────────────────────────────
            $('.single-select-carton').select2({
                allowClear: true,
                language: 'fa',
                dir: 'rtl',
                placeholder: 'انتخاب کارتن',
                dropdownParent: $('#packing-add')
            });
            $('.single-select-product').select2({
                language: 'fa',
                dir: 'rtl',
                allowClear: true,
                placeholder: 'انتخاب محصول',
                width: '100%',
                dropdownParent: $('#packing-add')
            });

            $('.single-select-group').select2({
                language: 'fa',
                dir: 'rtl',
                allowClear: true,
                placeholder: 'انتخاب گروه محصول',
                width: '100%',
                dropdownParent: $('#packing-add')
            });
            const $productSelect = $('.single-select-product');
            const $groupSelect = $('.single-select-group');
            const initialproductValue = $productSelect.val() || '';
            const initialGroupValue = $groupSelect.val() || '';
            let isProgrammaticChange = false;
            // ─────────────────────────────────────────────────────────
            // تغییر Main Select
            // ─────────────────────────────────────────────────────────
            $productSelect.on('select2:select', function(e) {
                let currentValue = e.params.data.id;
                if (currentValue && currentValue !== initialproductValue) {
                    isProgrammaticChange = true;
                    $groupSelect.val(null).trigger('change');
                    showFlash($groupSelect);
                    setTimeout(() => {
                        isProgrammaticChange = false;
                    }, 50);
                }
            });
            // ─────────────────────────────────────────────────────────
            // تغییر Group Select
            // ─────────────────────────────────────────────────────────
            $groupSelect.on('select2:select', function(e) {
                let currentValue = e.params.data.id;
                if (currentValue && currentValue !== initialGroupValue) {
                    isProgrammaticChange = true;
                    $productSelect.val(null).trigger('change');
                    showFlash($productSelect);
                    setTimeout(() => {
                        isProgrammaticChange = false;
                    }, 50);
                }
            });
            // ─────────────────────────────────────────────────────────
            // تابع نمایش فلش
            // ─────────────────────────────────────────────────────────
            function showFlash($element) {
                let $box = $element.closest('[class*="single-select-"]');
                if ($box.length) {
                    $box.parent().addClass('flash-animation');
                    setTimeout(() => {
                        $box.parent().removeClass('flash-animation');
                    }, 600);
                }
            }
        });




    </script>

@endsection
