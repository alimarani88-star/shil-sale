@extends('Admin.Layout.master')

@section('head-tag')
    <title>ایجاد کالا</title>
@endsection

@section('content')

    <div class="container-fluid pt-2">
        <nav aria-label="breadcrumb" class="mt-0 px-3">
            <ol class="breadcrumb p-2">
                <li class="breadcrumb-item font-size-12"><a href="#">خانه</a></li>
                <li class="breadcrumb-item font-size-12"><a href="#">کالاها</a></li>
                <li class="breadcrumb-item font-size-12 active" aria-current="page">ایجاد کالای جدید</li>
            </ol>
        </nav>

        <section class="row">
            <div class="col-12">
                <section class="main-body-container">
                    <section class="main-body-container-header">
                        <h4>ایجاد کالا</h4>
                    </section>

                    <div class="d-flex justify-content-between align-items-center mt-2 mb-4 border-bottom pb-2">
                        <a href="{{ route('A_show_product') }}" class="btn btn-info btn-sm">بازگشت</a>
                    </div>

                    <div>
                        <form action="{{ route('A_s_create_product') }}" method="post"
                              enctype="multipart/form-data" id="form">
                            @csrf
                            <div class="row">

                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label>گروه کالا</label>
                                        <select class="form-control form-control-sm select2"
                                                name="product_group_id_in_app"
                                                id="product_group_id_in_app"
                                                onchange="load_articles()">
                                            <option value="">-- انتخاب کنید --</option>
                                            @foreach($groups['data'] as $group)
                                                <option
                                                    value="{{ $group['id'] }}" {{ old('product_group_id_in_app') == $group['id'] ? 'selected' : '' }}>
                                                    {{ $group['title'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('product_group_id_in_app')
                                        <span
                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                            role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label>نام کالا در اپ شیل ایران</label>
                                        <select class="form-control form-control-sm select2"
                                                name="product_name_id_in_app"
                                                id="product_name_id_in_app">
                                            <option value="">نام کالا را انتخاب کنید...</option>

                                        </select>

                                        @error('product_name_id_in_app')
                                        <span
                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                            role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label>نام کالا در سایت شیل ایران</label>
                                        <input type="text" name="product_name" value="{{ old('product_name') }}"
                                               id="product_name"
                                               class="form-control form-control-sm">

                                        @error('product_name')
                                        <span
                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                            role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label>قیمت کالا</label>
                                        <input type="text" name="price" id="price" value="{{ old('price') }}"
                                               class="form-control form-control-sm">
                                        @error('price')
                                        <span
                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                            role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label>واحد پولی</label>
                                        <select name="price_unit" id="price_unit" class="form-control form-control-sm">
                                            <option value="ریال" @if(old('price_unit') == 'ریال') selected @endif>ریال
                                            </option>
                                            <option value="تومان" @if(old('price_unit') == 'تومان') selected @endif>
                                                تومان
                                            </option>
                                            <option value="دلار" @if(old('price_unit') == 'دلار') selected @endif>دلار
                                            </option>
                                            <option value="درهم" @if(old('price_unit') == 'درهم') selected @endif>درهم
                                            </option>
                                        </select>
                                        @error('price_unit')
                                        <span
                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                            role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label for="status">وضعیت</label>
                                        <select name="status" id="status" class="form-control form-control-sm">
                                            <option value="1" @if(old('status') == 1) selected @endif>فعال</option>
                                            <option value="0" @if(old('status') == 0) selected @endif>غیرفعال</option>
                                        </select>
                                        @error('status')
                                        <span
                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                            role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label for="marketable">قابل فروش بودن</label>
                                        <select name="marketable" id="marketable" class="form-control form-control-sm">
                                            <option value="1" @if(old('marketable') == 1) selected @endif>فعال</option>
                                            <option value="0" @if(old('marketable') == 0) selected @endif>غیرفعال
                                            </option>
                                        </select>
                                        @error('marketable')
                                        <span
                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                            role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label>تاریخ شروع فروش</label>
                                        <input type="text" name="sales_start_date" id="sales_start_date"
                                               {{ old('sales_start_date') }}
                                               class="form-control form-control-sm">

                                        @error('sales_start_date')
                                        <span
                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                            role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label>تاریخ پایان فروش</label>
                                        <input type="text" name="sales_end_date" id="sales_end_date"
                                               {{ old('sales_end_date') }}
                                               class="form-control form-control-sm">

                                        @error('sales_end_date')
                                        <span
                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                            role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12 col-12 mt-3">
                                    <div class="card">
                                        <div class="card-header bg-success text-white fw-bold">
                                            عکس‌های زیر را با اندازه حداکثر 800x800 و حجم 100KB آپلود کنید .
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <label>تصویر یک</label>
                                                        <input type="file" name="image1" id="image1"
                                                               class="form-control form-control-sm">
                                                        <div id="preview1" class="mt-2 d-flex flex-wrap gap-3"></div>

                                                        @error('image1')
                                                        <span
                                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                                            role="alert"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>


                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <label>تصویر دو</label>
                                                        <input type="file" name="image2" id="image2"
                                                               class="form-control form-control-sm">
                                                        <div id="preview2" class="mt-2 d-flex flex-wrap gap-3"></div>

                                                        @error('image2')
                                                        <span
                                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                                            role="alert"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>


                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <label>تصویر سه</label>
                                                        <input type="file" name="image3" id="image3"
                                                               class="form-control form-control-sm">
                                                        <div id="preview3" class="mt-2 d-flex flex-wrap gap-3"></div>

                                                        @error('image3')
                                                        <span
                                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                                            role="alert"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>


                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <label>تصویر چهار</label>
                                                        <input type="file" name="image4" id="image4"
                                                               class="form-control form-control-sm">
                                                        <div id="preview4" class="mt-2 d-flex flex-wrap gap-3"></div>
                                                        @error('image4')
                                                        <span
                                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                                            role="alert"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-12 mt-3">
                                    <div class="card">
                                        <div class="card-header bg-success text-white fw-bold">
                                            در فیلد زیر میتوانید یک عکس با کیفیت برای محصول آپلود کنید .
                                        </div>
                                        <div class="card-body bg-light">
                                            <div class="row">
                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <label>تصویر پنج</label>
                                                        <input type="file" name="image5" id="image5"
                                                               class="form-control form-control-sm">
                                                        <div id="preview4" class="mt-2 d-flex flex-wrap gap-3"></div>
                                                        @error('image5')
                                                        <span
                                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                                            role="alert"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-8">
                                    <div class="form-group">
                                        <label>توضیحات</label>
                                        <textarea name="description" class="form-control form-control-sm"
                                                  rows="6">{{ old('description') }}</textarea>
                                        @error('description')
                                        <span
                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                            role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="col-12 border-top border-bottom py-3 mb-3">
                                    <div id="attribute-container">

                                        @php
                                            $old_meta_keys = old('meta_key', []);
                                            $old_meta_values = old('meta_value', []);
                                        @endphp

                                        @if(count($old_meta_keys) > 0)

                                            @foreach($old_meta_keys as $index => $oldKey)
                                                <div class="row attribute-row mb-2 meta-row">
                                                    <div class="col-6 col-md-3">
                                                        <select name="meta_key[]" class="form-control form-control-sm">
                                                            <option value="">انتخاب ویژگی</option>
                                                            @foreach($attributes as $attribute)
                                                                <option value="{{ $attribute->id }}"
                                                                    {{ $oldKey == $attribute->id ? 'selected' : '' }}>
                                                                    {{ $attribute->attribute }}
                                                                </option>
                                                            @endforeach
                                                        </select>

                                                        @error("meta_key.$index")
                                                        <span
                                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                                            role="alert">
                                                        <strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>

                                                    <div class="col-6 col-md-3">
                                                        <input type="text" name="meta_value[]"
                                                               value="{{ $old_meta_values[$index] ?? '' }}"
                                                               class="form-control form-control-sm"
                                                               placeholder="مقدار ...">

                                                        @error("meta_value.$index")
                                                        <span
                                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                                            role="alert">
                                                             <strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>

                                                    <div class="col-12 col-md-2 mt-2 mt-md-0">
                                                        <button type="button" class="btn btn-danger btn-sm remove-row">
                                                            حذف
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            {{-- اولین بار --}}
                                            <div class="row attribute-row mb-2">
                                                <div class="col-6 col-md-3">
                                                    <select name="meta_key[]" class="form-control form-control-sm">
                                                        <option value="">انتخاب ویژگی</option>
                                                        @foreach($attributes as $attribute)
                                                            <option
                                                                value="{{ $attribute->id }}">{{ $attribute->attribute }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-6 col-md-3">
                                                    <input type="text" name="meta_value[]"
                                                           class="form-control form-control-sm"
                                                           placeholder="مقدار ...">
                                                </div>

                                                <div class="col-12 col-md-2 mt-2 mt-md-0">
                                                    <button type="button" class="btn btn-danger btn-sm remove-row">حذف
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <button type="button" class="btn btn-sm btn-primary" id="btn-copy">افزودن ویژگی
                                    </button>
                                </div>


                                <div class="col-12">
                                    <button class="btn btn-success btn-lg">ثبت</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </section>
    </div>
@endsection

@section('script')

    <script src="admin-assets/jquery/jquery-image-upload-resizer-complete.js"></script>
    <script>
        $(document).ready(function () {
            $('#product_group_id_in_app').val('')
            $('#product_name_id_in_app').val('')

            $('#image1, #image2, #image3, #image4').val('');


            $('#sales_start_date, #sales_end_date').persianDatepicker({
                format: 'YYYY/MM/DD',
                altField: '#published_at'
            });


            $('#image1, #image2, #image3, #image4').imageUploadResizer({
                max_width: 1000,
                max_height: 1000,
                quality: 0.9,
                min_quality: 0.4,
                max_size_kb: 100
            });


            $('.select2').select2({
                placeholder: "انتخاب کنید",
                allowClear: true,
                dir: "rtl"
            });

            // ---------- Helper Functions ----------
            function toggleRemoveButtons() {
                if ($('.attribute-row').length <= 1) {
                    $('.remove-row').hide();
                } else {
                    $('.remove-row').show();
                }
            }

            toggleRemoveButtons();

            $("#btn-copy").on('click', function () {
                var ele = $(".attribute-row").last().clone(false);
                ele.find('select, input').val("");
                $("#attribute-container").append(ele);
                toggleRemoveButtons();
            });

            $(document).on('click', '.remove-row', function () {
                if ($('.attribute-row').length > 1) {
                    $(this).closest('.attribute-row').remove();
                    toggleRemoveButtons();
                }
            });

            let oldGroup = "{{ old('product_group_id_in_app') }}";
            if (oldGroup) {
                $('#product_group_id_in_app').val(oldGroup).trigger('change.select2');
                load_articles();
            }

        });

        function load_articles() {
            let val = $('#product_group_id_in_app').val();
            if (val) get_article();
        }

        function get_article() {
            let group_id = $("#product_group_id_in_app").val();
            if (!group_id) return;

            $.ajax({
                url: "/ajax_A_create_product",
                type: "get",
                data: {group_id: group_id},
                success: function (response) {
                    console.log(response);
                    if (response.status === 'success') {
                        add_articles(response.data.data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'SHILIRAN',
                            text: response.message || 'خطایی رخ داده است',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                },
                error: function () {
                    $("#table_product > tbody").empty();
                    Swal.fire({
                        icon: 'error',
                        title: 'SHILIRAN',
                        text: 'خطا در ارسال!',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            });
        }

        function add_articles(dataArray) {
            let $select = $("#product_name_id_in_app");
            $select.empty().append(new Option(""));
            dataArray.forEach(item => {
                $select.append(new Option(item.TITLE2, item.id));
            });
            let oldProduct = "{{ old('product_name_id_in_app') }}";
            if (oldProduct) {
                $select.val(oldProduct).trigger('change.select2');
                $('#product_name').val($select.find('option:selected').text());
            } else {
                $select.trigger('change.select2');
            }
        }


        $('#price').on('input', function (e) {
            let value = e.target.value.replace(/,/g, '');
            if (!isNaN(value) && value !== '') {
                e.target.value = Number(value).toLocaleString('en-US');
            }
        });


        $('#form').on('submit', function () {
            let priceInput = $('#price');
            priceInput.val(priceInput.val().replace(/,/g, ''));
        });

        $('#product_name_id_in_app').on('change', function () {
            let selectedText = $(this).find('option:selected').text();
            $('#product_name').val(selectedText);
        });

        $('#product_group_id_in_app').on('select2:clear', function (e) {
            $('#product_name_id_in_app').val('').trigger('change');
        });


    </script>

@endsection
