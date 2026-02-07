@extends('Admin.Layout.master')

@section('head-tag')
    <title>ویرایش تخفیف</title>
@endsection

@section('content')

    <div class="container-fluid pt-2">
        <nav aria-label="breadcrumb" class="mt-0 px-3">
            <ol class="breadcrumb p-2">
                <li class="breadcrumb-item font-size-12"><a href="{{route('A_home')}}">خانه</a></li>
                <li class="breadcrumb-item font-size-12 active" aria-current="page">تخفیف ها</li>
                <li class="breadcrumb-item font-size-12 active" aria-current="page">تخفیف ها</li>
                <li class="breadcrumb-item font-size-12 active" aria-current="page">ویرایش تخفیف</li>
            </ol>
        </nav>

        <section class="row">
            <div class="col-12">
                <section class="main-body-container">
                    <section class="main-body-container-header">
                        <h5>ایجاد تخفیف</h5>
                    </section>

                    <div class="d-flex justify-content-between align-items-center mt-2 mb-4 border-bottom pb-2">
                        <a href="{{ route('A_show_discount') }}" class="btn btn-info btn-sm">بازگشت</a>
                    </div>

                    <div>
                        <form action="{{ route('A_s_edit_discount',$discountdata['id']) }}" method="post"
                              enctype="multipart/form-data" id="form">
                            @csrf
                            @method('PUT')
                            <div class="row">

                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label>نام تخفیف</label>
                                        <input type="text" name="discount_name" id="discount_name"
                                               value="{{ old('discount_name',$discountdata['discount_name']) }}"
                                               class="form-control form-control-sm">

                                        @error('discount_name')
                                        <span
                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                            role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label for="discount_type">نوع تخفیف</label>
                                        <select name="discount_type" id="discount_type" class="form-control form-control-sm select2">
                                            <option value="amazingsale" @if(old('discount_type',$discountdata['discount_type']) == 'amazingsale') selected @endif>تخفیف شگفت انگیز</option>
                                            <option value="common" @if(old('discount_type',$discountdata['discount_type']) == 'common') selected @endif>تخفیف عمومی</option>
                                        </select>
                                        @error('discount_type')
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
                                            <option value="1" @if(old('status',$discountdata['status']) == 1) selected @endif>فعال</option>
                                            <option value="0" @if(old('status',$discountdata['status']) == 0) selected @endif>غیرفعال</option>
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
                                        <label>تاریخ شروع تخفیف</label>
                                        <input type="text" name="start_date" id="start_date"
                                               value="{{ old('start_date',$discountdata['start_date']) }}"
                                               class="form-control form-control-sm">

                                        @error('start_date')
                                        <span
                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                            role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label>تاریخ پایان تخفیف</label>
                                        <input type="text" name="end_date" id="end_date"
                                               value="{{ old('end_date',$discountdata['end_date']) }}"
                                               class="form-control form-control-sm">

                                        @error('end_date')
                                        <span
                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                            role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-8">
                                    <div class="form-group">
                                        <label>توضیحات</label>
                                        <textarea name="description" class="form-control form-control-sm"
                                                  rows="6">{{ old('description',$discountdata['description']) }}</textarea>
                                        @error('description')
                                        <span
                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                            role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
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
            $('#start_date, #end_date').persianDatepicker({
                format: 'YYYY/MM/DD'
            });

            $('.select2').select2({
                placeholder: "انتخاب کنید",
                allowClear: true,
                dir: "rtl"
            });

        });


    </script>
@endsection


