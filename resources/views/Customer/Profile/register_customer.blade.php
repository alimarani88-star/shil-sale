@extends('Customer.Layout.master')

@section('content')
    <style>
        .is-invalid {
            border: 2px solid #6f42c1 !important;

        }
    </style>

    <!-- main -->
    <main class="profile-user-page default">
        <div class="container">
            <div class="row">
                <div class="profile-page col-xl-9 col-lg-8 col-md-12 order-2">
                    <div class="row">
                        <div class="col-12">
                            <div class="col-12">
                                <h1 class="title-tab-content">ثبت اطلاعات مشتریان</h1>
                            </div>
                            <div class="content-section default">

                                <form class="form-account" action="{{ route('s_register_customer') }}" method="post"
                                      enctype="multipart/form-data" id="register_form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-sm-12 col-md-6">
                                            <div class="form-account-title">نام <span class="text-danger">*</span></div>
                                            <div class="form-account-row">
                                                <input class="input-field text-right" type="text"
                                                       name="first_name"
                                                       id="first_name"
                                                       value="{{old('first_name')}}"
                                                       placeholder="نام خود را وارد نمایید">

                                                @error('first_name')
                                                <span
                                                    class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                                    role="alert">
                                            <strong>{{ $message }}</strong>
                                              </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            <div class="form-account-title">نام خانوادگی <span
                                                    class="text-danger">*</span></div>
                                            <div class="form-account-row">
                                                <input class="input-field text-right" type="text"
                                                       name="last_name"
                                                       id="last_name"
                                                       value="{{old('last_name')}}"
                                                       placeholder="نام خانوادگی خود را وارد نمایید">

                                                @error('last_name')
                                                <span
                                                    class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                                    role="alert">
                                            <strong>{{ $message }}</strong>
                                              </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-sm-12 col-md-6">
                                            <div class="form-account-title">شماره موبایل <span
                                                    class="text-danger">*</span></div>
                                            <div class="form-account-row">
                                                <input class="input-field"
                                                       type="tel"
                                                       inputmode="numeric"
                                                       pattern="[0-9]*"
                                                       name="mobile"
                                                       id="mobile"
                                                       value="{{ old('mobile') }}"
                                                       placeholder="شماره موبایل خود را وارد نمایید">
                                                @error('mobile')
                                                <span
                                                    class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                                    role="alert">
                                            <strong>{{ $message }}</strong>
                                              </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            <div class="form-account-title">درخواست اخذ نمایندگی</div>
                                            <div class="form-account-row">
                                                <div class="d-flex align-items-center border rounded p-2"
                                                     style="height: 45px; background-color: #fff;">
                                                    <input type="checkbox"
                                                           name="request_agency"
                                                           id="request_agency"
                                                           value="1"
                                                           {{ old('request_agency') ? 'checked' : '' }}
                                                           style="width: 22px; height: 22px; accent-color: #6f42c1; cursor: pointer;">
                                                    <label for="request_agency"
                                                           class="mb-0 ms-3 fw-semibold"
                                                           style="font-size: 1rem; cursor: pointer;margin-right: 12px;">
                                                        درخواست اخذ نمایندگی فروش را دارم
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            <div class="form-account-title">شهر <span
                                                    class="text-danger">*</span></div>
                                            <div class="form-account-row">
                                                <select class="form-control form-control-sm  select2"
                                                        name="city"
                                                        id="city"
                                                        onchange="load_cities()">
                                                    <option value="">-- انتخاب کنید --</option>
                                                    @foreach($cities as $city)
                                                        <option
                                                            value="{{ $city['id'] }}" {{ old('city') == $city['id'] ? 'selected' : '' }}>
                                                            {{ $city['title'] }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                @error('province')
                                                <span
                                                    class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                                    role="alert">
                                            <strong>{{ $message }}</strong>
                                              </span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{--                                        <div class="col-sm-12 col-md-6">--}}
                                        {{--                                            <div class="form-account-title">شهر</div>--}}
                                        {{--                                            <div class="form-account-row">--}}
                                        {{--                                                <select class="form-control form-control-sm  select2"--}}
                                        {{--                                                        name="city"--}}
                                        {{--                                                        id="city">--}}
                                        {{--                                                    <option value="">-- انتخاب کنید --</option>--}}

                                        {{--                                                </select>--}}

                                        {{--                                                @error('city')--}}
                                        {{--                                                <span--}}
                                        {{--                                                    class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"--}}
                                        {{--                                                    role="alert">--}}
                                        {{--                                            <strong>{{ $message }}</strong>--}}
                                        {{--                                              </span>--}}
                                        {{--                                                @enderror--}}
                                        {{--                                            </div>--}}
                                        {{--                                        </div>--}}
                                        <div class="col-sm-12 col-md-6">
                                            <div class="form-account-title">نام شرکت / مغازه</div>
                                            <div class="form-account-row">
                                                <input class="input-field text-right" type="text"
                                                       name="company_name"
                                                       id="company_name"
                                                       value="{{old('company_name')}}"
                                                       placeholder=" نام شرکت یا مغازه را وارد کنید">
                                                @error('company_name')
                                                <span
                                                    class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                                    role="alert">
                                                  <strong>{{ $message }}</strong>
                                              </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-12">
                                            <div class="form-account-title">توضیحات ( اختیاری )</div>
                                            <div class="form-account-row">
                                               <textarea class="input-field text-right"
                                                         name="description"
                                                         id="description"
                                                         placeholder="توضیحات"
                                                         rows="3">{{ old('description') }}</textarea>
                                                @error('description')
                                                <span
                                                    class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                                    role="alert">
                                                  <strong>{{ $message }}</strong>
                                              </span>
                                                @enderror
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">

                                        <button type="submit" class="btn btn-default btn-lg custom-primary mr-2"
                                                id="btn_submit">ذخیره
                                        </button>

                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>
    <!-- main -->

@endsection

@section('script')

    <script>
        $(document).ready(function () {
            const navigationType = performance.getEntriesByType("navigation")[0]?.type || '';

            if (navigationType === 'reload') {

                $('form#register_form :input').not(':button, :submit, :reset, :hidden').val('').trigger('change.select2');
            }

            let oldProvince = "{{ old('province') }}";
            if (oldProvince && navigationType !== 'reload') {
                $('#province').val(oldProvince).trigger('change.select2');
                load_cities();
            }
        });


        $('.select2').select2(
            {
                placeholder: "انتخاب کنید",
                width: '100%',
                allowClear: true,
                dir: "rtl"
            });


        function load_cities() {
            const val = $('#province').val();
            if (val) {
                get_cities();
            }
        }

        function get_cities() {
            const province_id = $("#province").val();
            if (!province_id) return;
            $.ajax({
                url: "/ajax_register_customer", type: "GET", data: {province_id}, success: function (response) {
                    console.log(response);
                    if (response.status === 'success') {
                        add_cities(response.data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'SHILIRAN',
                            text: response.message || 'خطایی رخ داده است',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                }, error: function () {
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

        function add_cities(dataArray) {
            const $select = $("#city");
            $select.empty().append(new Option("انتخاب شهر", ""));
            if (Array.isArray(dataArray)) {
                dataArray.forEach(item => {
                    $select.append(new Option(item.title, item.id));
                });
            }
            const oldCities = "{{ old('city') }}";
            if (oldCities) {
                $select.val(oldCities).trigger('change.select2');
            } else {
                $select.trigger('change.select2');
            }
        }

        $('#btn_submit').on('click', function (e) {
            if ($('#request_agency').is(':checked')) {

                const city = $('#city').val().trim();
                const company = $('#company_name').val().trim();
                const description = $('#description').val().trim();

                let hasError = false;

                if (city === '') {
                    $('#city').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                    hasError = true;
                } else {
                    $('#city').next('.select2-container').find('.select2-selection').removeClass('is-invalid');
                }

                if (company === '') {
                    $('#company_name').addClass('is-invalid');
                    hasError = true;
                } else {
                    $('#company_name').removeClass('is-invalid');
                }

                if (description === '') {
                    $('#description').addClass('is-invalid');
                    hasError = true;
                } else {
                    $('#description').removeClass('is-invalid');
                }

                if (hasError) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'تکمیل اطلاعات لازم است',
                        text: ' در صورت انتخاب گزینه اخذ نمایندگی لطفاً فیلدهای شهر، شرکت و توضیحات را پر نمایید',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#6f42c1'
                    });
                }
            }
        });


    </script>

@endsection
