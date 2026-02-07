@extends('Customer.Layout.master')

@section('content')

    <main class="profile-user-page default">
        <div class="container">
            <div class="row">
                <div class="profile-page col-xl-9 col-lg-8 col-md-12 order-2">
                    <div class="row">
                        <div class="col-lg-6">
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
                                            <span>{{$userProfileInfo->national_code ?? '' }}</span>
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
                        <div class="col-lg-6">
                            <div class="col-12">
                                <h1 class="title-tab-content">لیست آخرین علاقمندی ها</h1>
                            </div>
                            <div class="content-section default">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="profile-recent-fav-row">
                                            <a href="#" class="profile-recent-fav-col profile-recent-fav-col-thumb">
                                                <img src="assets/img/cart/4560621.jpg"></a>
                                            <div class="profile-recent-fav-col profile-recent-fav-col-title">
                                                <a href="#">
                                                    <h4 class="profile-recent-fav-name">
                                                        گوشی موبایل اپل مدل iPhone XR دو سیم کارت ظرفیت 256 گیگابایت
                                                    </h4>
                                                </a>
                                                <div class="profile-recent-fav-price">ناموجود</div>
                                            </div>
                                            <div class="profile-recent-fav-col profile-recent-fav-col-actions">
                                                <button class="btn-action btn-action-remove">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="profile-recent-fav-row">
                                            <a href="#" class="profile-recent-fav-col profile-recent-fav-col-thumb">
                                                <img src="assets/img/cart/3794614.jpg"></a>
                                            <div class="profile-recent-fav-col profile-recent-fav-col-title">
                                                <a href="#">
                                                    <h4 class="profile-recent-fav-name">
                                                        گوشی موبایل اپل مدل iPhone XR دو سیم کارت ظرفیت 256 گیگابایت
                                                    </h4>
                                                </a>
                                                <div class="profile-recent-fav-price">ناموجود</div>
                                            </div>
                                            <div class="profile-recent-fav-col profile-recent-fav-col-actions">
                                                <button class="btn-action btn-action-remove">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 text-center">
                                        <a href="#" class="btn-link-border form-account-link">
                                            مشاهده و ویرایش لیست مورد علاقه
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <h1 class="title-tab-content">آخرین سفارش ها</h1>
                        </div>
                        <div class="col-12 text-center">
                            <div class="content-section pt-5 pb-5">
                                <div class="icon-empty">
                                    <i class="now-ui-icons travel_info"></i>
                                </div>
                                <h1 class="text-empty">موردی برای نمایش وجود ندارد!</h1>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="profile-page-aside col-xl-3 col-lg-4 col-md-6 center-section order-1">
                    <div class="profile-box">
                        <div class="profile-box-header">
                            <div class="profile-box-avatar">
                                <img src="assets/img/svg/user.svg" alt="">
                            </div>
                            <button data-toggle="modal" data-target="#myModal" class="profile-box-btn-edit">
                                <i class="fa fa-pencil"></i>
                            </button>

                        </div>
                        <div class="profile-box-username">{{$user->name}}</div>
                        <div class="profile-box-tabs">
                            <a href="{{route('reset_password')}}" class="profile-box-tab profile-box-tab-access">
                                <i class="now-ui-icons ui-1_lock-circle-open"></i>
                                تغییر رمز
                            </a>
                            <a href="#" class="profile-box-tab profile-box-tab--sign-out"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="now-ui-icons media-1_button-power"></i>
                                خروج از حساب
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                    <div class="responsive-profile-menu show-md">
                        <div class="btn-group">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                <i class="fad fa-bars"></i>
                                حساب کاربری شما
                            </button>
                            <div class="dropdown-menu dropdown-menu-right text-right">
                                <a href="{{route('profile_personal_info')}}" class="dropdown-item active-menu">
                                    <i class="now-ui-icons users_single-02"></i>
                                    پروفایل
                                </a>
                                <a href="{{route('profile_orders')}}" class="dropdown-item">
                                    <i class="now-ui-icons shopping_basket"></i>
                                    همه سفارش ها
                                </a>
                                <a href="{{route('profile_orders_return')}}" class="dropdown-item">
                                    <i class="now-ui-icons files_single-copy-04"></i>
                                    درخواست مرجوعی
                                </a>
                                <a href="{{route('profile_favorites')}}" class="dropdown-item">
                                    <i class="now-ui-icons ui-2_favourite-28"></i>
                                    لیست علاقمندی ها
                                </a>

                            </div>
                        </div>
                    </div>
                    <div class="profile-menu hidden-md">
                        <div class="profile-menu-header">حساب کاربری شما</div>
                        <ul class="profile-menu-items">
                            <li>
                                <a href="{{route('profile_personal_info')}}" class="active">
                                    <i class="now-ui-icons users_single-02"></i>
                                    پروفایل
                                </a>
                            </li>
                            <li>
                                <a href="{{route('profile_orders')}}">
                                    <i class="now-ui-icons shopping_basket"></i>
                                    همه سفارش ها
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('profile_addresses') }}">
                                    <i class="now-ui-icons location_pin"></i>
                                    آدرس‌ها
                                </a>
                            </li>
                            <li>
                                <a href="{{route('profile_orders_return')}}">
                                    <i class="now-ui-icons files_single-copy-04"></i>
                                    درخواست مرجوعی
                                </a>
                            </li>
                            <li>
                                <a href="{{route('profile_favorites')}}">
                                    <i class="now-ui-icons ui-2_favourite-28"></i>
                                    لیست علاقمندی ها
                                </a>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>



@endsection
