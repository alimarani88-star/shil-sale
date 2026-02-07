@extends('Admin.Layout.master')

@section('head-tag')
    <title>ایجاد تخفیف عمومی</title>
@endsection

@section('content')

    <div class="container-fluid pt-2">
        <nav aria-label="breadcrumb" class="mt-0 px-3">
            <ol class="breadcrumb p-2">
                <li class="breadcrumb-item font-size-12"><a href="{{route('A_home')}}">خانه</a></li>
                <li class="breadcrumb-item font-size-12 active" aria-current="page">تخفیف ها</li>
                <li class="breadcrumb-item font-size-12 active" aria-current="page">تخفیف عمومی</li>
                <li class="breadcrumb-item font-size-12 active" aria-current="page">ایجاد تخفیف عمومی</li>
            </ol>
        </nav>

        <section class="row">
            <div class="col-12">
                <section class="main-body-container">
                    <section class="main-body-container-header">
                        <h5>ایجاد تخفیف عمومی</h5>
                    </section>

                    <div class="d-flex justify-content-between align-items-center mt-2 mb-4 border-bottom pb-2">
                        <a href="{{ route('A_show_common_discount') }}" class="btn btn-info btn-sm">بازگشت</a>
                    </div>

                    <div>
                        <form action="{{ route('A_s_create_common_discount') }}" method="post"
                              enctype="multipart/form-data" id="form">
                            @csrf
                            <div class="row mb-4">
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label>نوع تخفیف</label>
                                        <select id="discount_type" name="discount_type"
                                                class="form-control form-control-sm select2">
                                            <option value="">-- انتخاب کنید --</option>
                                            <option value="maingroup"
                                                    @if(old('discount_type') == 'maingroup') selected @endif>
                                                تخفیف گروه اصلی کالا
                                            </option>
                                            <option value="group" @if(old('discount_type') == 'group') selected @endif>
                                                تخفیف گروه کالا
                                            </option>
                                            <option value="product"
                                                    @if(old('discount_type') == 'Product') selected @endif>تخفیف کالا
                                            </option>
                                        </select>
                                        @error('discount_type')
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
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label>گروه اصلی کالا</label>
                                        <select class="form-control form-control-sm select2"
                                                name="main_group_id"
                                                id="main_group_id" disabled>
                                            <option value="">-- انتخاب کنید --</option>
                                            @foreach($maingroups['data'] as $maingroup)
                                                <option
                                                    value="{{ $maingroup['id'] }}" {{ old('main_group_id') == $maingroup['id'] ? 'selected' : '' }}>
                                                    {{ $maingroup['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('main_group_id')
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
                                        <label>گروه کالا</label>
                                        <select class="form-control form-control-sm select2"
                                                name="group_id"
                                                id="group_id" disabled>
                                            <option value="">-- انتخاب کنید --</option>
                                            @foreach($groups['data'] as $group)
                                                <option
                                                    value="{{ $group['id'] }}" {{ old('group_name') == $group['id'] ? 'selected' : '' }}>
                                                    {{ $group['title'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('group_id')
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
                                        <label>نام کالا</label>
                                        <select class="form-control form-control-sm select2"
                                                name="product_id"
                                                id="product_id" disabled>
                                            <option value="">نام کالا را انتخاب کنید...</option>
                                            @foreach($products as $product)
                                                <option
                                                    value="{{ $product['id'] }}" {{ old('product_name') == $product['id'] ? 'selected' : '' }}>
                                                    {{ $product['product_name'] }}
                                                </option>
                                            @endforeach

                                        </select>

                                        @error('product_id')
                                        <span
                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                            role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label>نام تخفیف</label>
                                        <select class="form-control form-control-sm select2"
                                                name="discount_id"
                                                id="discount_id">
                                            <option value="">نام تخفیف را انتخاب کنید...</option>
                                            @foreach($discounts as $discount)
                                                <option
                                                    value="{{ $discount['id'] }}" {{ old('discount_id') == $discount['id'] ? 'selected' : '' }}>
                                                    {{ $discount['discount_name'] }}
                                                </option>
                                            @endforeach

                                        </select>

                                        @error('discount_id')
                                        <span
                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                            role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label>درصد تخفیف</label>
                                        <input type="text" name="percentage_discount" id="percentage_discount"
                                               value="{{ old('percentage_discount') }}"
                                               class="form-control form-control-sm">
                                        @error('percentage_discount')
                                        <span
                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                            role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
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

    <script>

        $(document).ready(function () {
            $('#percentage_start_date, #percentage_end_date').persianDatepicker({
                format: 'YYYY/MM/DD'
            });

            $('.select2').select2({
                placeholder: "انتخاب کنید",
                allowClear: true,
                dir: "rtl"
            });


            toggleDiscountInputs($('#discount_type').val());


            $('#discount_type').on('change', function () {
                toggleDiscountInputs(this.value);
            });

            function toggleDiscountInputs(type) {
                $('#main_group_id').prop('disabled', true);
                $('#group_id').prop('disabled', true);
                $('#product_id').prop('disabled', true);

                if (type === 'group') {
                    $('#group_id').prop('disabled', false);
                } else if (type === 'product') {
                    $('#product_id').prop('disabled', false);
                } else if (type === 'maingroup')
                {
                    $('#main_group_id').prop('disabled', false);
                }
            }
        });


    </script>
@endsection


