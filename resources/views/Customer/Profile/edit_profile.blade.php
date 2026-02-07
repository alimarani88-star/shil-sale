@extends('Customer.Layout.master')

@section('content')

    <!-- main -->
    <main class="profile-user-page default">
        <div class="container">
            <div class="row">
                <div class="profile-page col-xl-9 col-lg-8 col-md-12 order-2">
                    <div class="row">
                        <div class="col-12">
                            <div class="col-12">
                                <h1 class="title-tab-content">ویرایش اطلاعات شخصی</h1>
                            </div>
                            <div class="content-section default">
                                <div class="row">
                                    <div class="col-12">
                                        <h1 class="title-tab-content">حساب شخصی</h1>
                                    </div>
                                </div>

                                <form class="form-account" action="{{ route('s_edit_profile') }}" method="post"
                                      enctype="multipart/form-data" id="form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-sm-12 col-md-6">
                                            <div class="form-account-title">نام</div>
                                            <div class="form-account-row">
                                                <input class="input-field text-right" type="text"
                                                       name="first_name"
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
                                            <div class="form-account-title">نام خانوادگی</div>
                                            <div class="form-account-row">
                                                <input class="input-field text-right" type="text"
                                                       name="last_name"
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
                                            <div class="form-account-title">کد ملی</div>
                                            <div class="form-account-row">
                                                <input class="input-field" type="text"
                                                       name="national_code"
                                                       value="{{old('national_code')}}"
                                                       placeholder="کد ملی خود را وارد نمایید">

                                                @error('national_code')
                                                <span
                                                    class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                                    role="alert">
                                            <strong>{{ $message }}</strong>
                                              </span>
                                                @enderror
                                            </div>

                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            <div class="form-account-title">شماره موبایل</div>
                                            <div class="form-account-row">
                                                <input class="input-field" type="text"
                                                       value="{{$user->username}}"
                                                       readonly
                                                       placeholder="شماره موبایل خود را وارد نمایید">

                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            <div class="form-account-title">شماره کارت</div>
                                            <div class="form-account-row">
                                                <input class="input-field" type="text"
                                                       name="card_number"
                                                       value="{{old('card_number')}}"
                                                       placeholder=" شماره کارت خود را وارد نمایید">

                                                @error('card_number')
                                                <span
                                                    class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                                    role="alert">
                                            <strong>{{ $message }}</strong>
                                              </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            <div class="form-account-title">آدرس ایمیل</div>
                                            <div class="form-account-row">
                                                <input class="input-field" type="email"
                                                       name="email"
                                                       value="{{old('email')}}"
                                                       placeholder=" آدرس ایمیل خود را وارد نمایید">

                                                @error('email')
                                                   <span
                                                    class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                                    role="alert">
                                                  <strong>{{ $message }}</strong>
                                              </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            <div class="form-account-title">شناسه ملی</div>
                                            <div class="form-account-row">
                                                <input class="input-field" type="text"
                                                       name="company_national_id"
                                                       value="{{old('company_national_id')}}"
                                                       placeholder=" شناسه ملی خود را وارد نمایید">

                                                @error('company_national_id')
                                                <span
                                                    class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                                    role="alert">
                                                  <strong>{{ $message }}</strong>
                                              </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            <div class="form-account-title">کد اقتصادی</div>
                                            <div class="form-account-row">
                                                <input class="input-field" type="text"
                                                       name="economic_number"
                                                       value="{{old('economic_number')}}"
                                                       placeholder=" کد اقتصادی خود را وارد نمایید">

                                                @error('economic_number')
                                                <span
                                                    class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                                    role="alert">
                                                  <strong>{{ $message }}</strong>
                                              </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <button type="submit" class="btn btn-default btn-lg">ذخیره</button>

                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <x-profile-sidebar :user="$user" />
            </div>
        </div>
    </main>
    <!-- main -->

@endsection
