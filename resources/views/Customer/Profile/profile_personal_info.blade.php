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
                                <h1 class="title-tab-content">اطلاعات شخصی</h1>
                            </div>
                            <div class="content-section default">
                                <div class="row">
                                    <div class="col-sm-12 col-md-6">
                                        <p>
                                            <span class="title">نام و نام خانوادگی :</span>
                                            <span>{{ ($userProfileInfo->first_name ?? '') . ' ' . ($userProfileInfo->last_name ?? '') }}</span>

                                        </p>
                                    </div>
                                    <div class="col-sm-12 col-md-6">
                                        <p>
                                            <span class="title">پست الکترونیک :</span>
                                            <span>{{$userProfileInfo->email ?? ''}}</span>
                                        </p>
                                    </div>
                                    <div class="col-sm-12 col-md-6">
                                        <p>
                                            <span class="title">شماره تلفن همراه:</span>
                                            <span>{{$user->username ?? ''}}</span>
                                        </p>
                                    </div>
                                    <div class="col-sm-12 col-md-6">
                                        <p>
                                            <span class="title">کد ملی :</span>
                                            <span>{{$userProfileInfo->national_code ?? ''}}</span>
                                        </p>
                                    </div>

                                    <div class="col-sm-12 col-md-6">
                                        <p>
                                            <span class="title">شماره کارت :</span>
                                            <span>{{$userProfileInfo->card_number ?? ''}}</span>
                                        </p>
                                    </div>
                                    <div class="col-12 text-center">
                                        <a href="{{route('edit_profile')}}"
                                           class="btn-link-border form-account-link">
                                            ویرایش اطلاعات شخصی
                                        </a>
                                    </div>
                                </div>
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
