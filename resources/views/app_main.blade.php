@extends('Customer.Layout.master')

@section('content')

    <!-- main -->

    <main class="main default">

        <!-- banner -->
        <div class="banner-ads d-md-block d-none">
            <section class="banner">
                <a href="#">
                    <img src="assets/img/banner/baner_01.jpg" alt="" class="banner-img">
                </a>
            </section>
        </div>
        <!-- banner -->


        <section id="main-slider" class="carousel slide carousel-fade card main-slider-st d-md-block d-none"
                 data-ride="carousel">
            <ol class="carousel-indicators">
                <li data-target="#main-slider" data-slide-to="0" class="active"></li>
                <li data-target="#main-slider" data-slide-to="1"></li>
                <li data-target="#main-slider" data-slide-to="2"></li>
                <li data-target="#main-slider" data-slide-to="3"></li>
            </ol>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <a class="d-block" href="#">
                        <img src="assets/img/slider/slider-1.jpg" class="d-block w-100" alt="">
                    </a>
                </div>
                <div class="carousel-item">
                    <a class="d-block" href="#">
                        <img src="assets/img/slider/slider-2.jpg" class="d-block w-100" alt="">
                    </a>
                </div>
                <div class="carousel-item">
                    <a class="d-block" href="#">
                        <img src="assets/img/slider/slider-3.jpg" class="d-block w-100" alt="">
                    </a>
                </div>
                <div class="carousel-item">
                    <a class="d-block" href="#">
                        <img src="assets/img/slider/slider-4.jpg" class="d-block w-100" alt="">
                    </a>
                </div>
            </div>
            <a class="carousel-control-prev" href="#main-slider" role="button" data-slide="prev">
                <i class="now-ui-icons arrows-1_minimal-right"></i>
            </a>
            <a class="carousel-control-next" href="#main-slider" data-slide="next">
                <i class="now-ui-icons arrows-1_minimal-left"></i>
            </a>
        </section>
        <div class="container">
            <section id="main-slider-mb" class="carousel slide carousel-fade card main-slider-st d-md-none"
                     data-ride="carousel">
                <ol class="carousel-indicators">
                    <li data-target="#main-slider-mb" data-slide-to="0" class="active"></li>
                    <li data-target="#main-slider-mb" data-slide-to="1"></li>
                    <li data-target="#main-slider-mb" data-slide-to="2"></li>
                    <li data-target="#main-slider-mb" data-slide-to="3"></li>
                </ol>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <a class="d-block" href="#">
                            <img src="assets/img/slider/001.jpg" class="d-block w-100" alt="">
                        </a>
                    </div>
                    <div class="carousel-item">
                        <a class="d-block" href="#">
                            <img src="assets/img/slider/002.jpg" class="d-block w-100" alt="">
                        </a>
                    </div>
                    <div class="carousel-item">
                        <a class="d-block" href="#">
                            <img src="assets/img/slider/003.jpg" class="d-block w-100" alt="">
                        </a>
                    </div>
                    <div class="carousel-item">
                        <a class="d-block" href="#">
                            <img src="assets/img/slider/004.jpg" class="d-block w-100" alt="">
                        </a>
                    </div>
                </div>
                <a class="carousel-control-prev" href="#main-slider-mb" role="button" data-slide="prev">
                    <i class="now-ui-icons arrows-1_minimal-right"></i>
                </a>
                <a class="carousel-control-next" href="#main-slider-mb" data-slide="next">
                    <i class="now-ui-icons arrows-1_minimal-left"></i>
                </a>
            </section>

            @isset($offerProducts)
                @if($offerProducts->count())
                    <section id="amazing-slider" class="carousel slide carousel-fade card" data-ride="carousel">
                        <div class="row m-0">
                            <!-- Indicators -->
                            <ol class="carousel-indicators pr-0 d-flex flex-column col-lg-3">
                                @foreach($offerProducts as $index => $offerProduct)
                                    <li class="{{ $index == 0 ? 'active' : '' }}"
                                        data-target="#amazing-slider"
                                        data-slide-to="{{ $index }}">
                                        <span>{{ $offerProduct->target_name }}</span>
                                    </li>
                                @endforeach
                                <li class="view-all mt-2">
                                    <a href="#" class="btn btn-primary btn-block hvr-sweep-to-left custom-primary">
                                        <i class="fa fa-arrow-left"></i>مشاهده همه شگفت انگیزها
                                    </a>
                                </li>
                            </ol>

                            <!-- Carousel items -->
                            <div class="carousel-inner p-0 col-12 col-lg-9">
                                <img class="amazing-title "
                                     src="assets/img/amazing-slider/banner_220x48_AE75DA_round_bottom_r16.png" alt="">

                                @foreach($offerProducts as $index => $offerProduct)
                                    <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                        <div class="row m-0">
                                            <div class="right-col col-5 d-flex align-items-center">
                                                <a class="w-100 text-center"
                                                   href="show_product_by_id/{{ $offerProduct->products->id }}">
                                                    @if($offerProduct->products)
                                                        <img
                                                            src="{{ url('get_image_by_id/' . $offerProduct->products->images->first()?->id) }}"
                                                            class="img-fluid"
                                                            style="width: 400px; height: 300px; object-fit: contain;"
                                                            alt="{{ $offerProduct->product_name }}">
                                                    @endif
                                                </a>
                                            </div>
                                            <div class="left-col col-7">
                                                <div class="price mb-2">
                                                    @php
                                                        $old_price = $offerProduct->products->price;
                                                        $discounted_price = round($old_price * (1 - ($offerProduct->percentage / 100))); // محاسبه قیمت جدید
                                                    @endphp
                                                    <del
                                                        style="color: #999; font-size: 0.9rem;">{{ number_format($old_price) }}
                                                        <span>تومان</span></del>
                                                    <ins
                                                        style="display:block; font-size: 1.2rem; font-weight: bold; color: #e74c3c;">{{ number_format($discounted_price) }}
                                                        <span>تومان</span></ins>
                                                    @if($offerProduct->percentage)
                                                        <span
                                                            class="discount-percent">{{ $offerProduct->percentage }} % تخفیف</span>
                                                    @endif
                                                </div>

                                                <h2 class="product-title">
                                                    <a href="#">{{ $offerProduct->products->product_name }}</a>
                                                </h2>


                                                <div class="countdown-timer" countdown
                                                     data-date="{{ \Carbon\Carbon::parse($amazingSaleDiscount->end_date)->format('m d Y H:i:s') }}">
                                                    <span data-days>0</span>:
                                                    <span data-hours>0</span>:
                                                    <span data-minutes>0</span>:
                                                    <span data-seconds>0</span>
                                                </div>
                                                <div class="timer-title">زمان باقی مانده تا پایان سفارش</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>


                    <div class="row" id="amazing-slider-responsive">
                        <div class="col-12">
                            <div class="widget widget-product card">
                                <header class="card-header">
                                    <img src="assets/img/amazing-slider/banner_220x48_AE75DA_round_bottom_r16.png"
                                         width="150px" alt="">
                                    <a href="#" class="view-all">مشاهده همه</a>
                                </header>
                                <div class="product-carousel owl-carousel owl-theme">
                                    @foreach($offerProducts as $index => $offerProduct)
                                        <div class="item">
                                            <div class="el-product-card">
                                                <div class="el-product-thumbnail">
                                                    <a href="show_product_by_id/{{ $offerProduct->products->id }}">

                                                        <img
                                                            src="{{ url('get_image_by_id/' . $offerProduct->products->images->first()?->id) }}"
                                                            class="img-fluid"
                                                            style="object-fit: contain;"
                                                            alt="{{ $offerProduct->product_name }}">
                                                    </a>

                                                </div>
                                                <div class="el-product-card-body">
                                                    <div class="el-product-title">
                                                        <h6><a href="#">{{ $offerProduct->product_name }}</a></h6>
                                                    </div>
                                                    <div class="el-product-info">
                                                        <div class="el-product-status"><i class="fad fa-box-check"></i>
                                                            موجود
                                                            در انبار
                                                        </div>
                                                        <div class="el-product-rating"><i class="fas fa-stars star"></i>
                                                            <strong></strong>
                                                            <span></span>
                                                        </div>
                                                    </div>
                                                    <div class="el-product-price">
                                                <span
                                                    class="el-price-value">{{number_format($offerProduct->products->price)}}</span>
                                                        <span
                                                            class="el-price-currency">{{$offerProduct->products->price_unit}}</span>
                                                    </div>
                                                </div>
                                                <div class="el-product-card-footer">
                                                    <div class="el-product-seller-details">
                                                        <i class="fad fa-store-alt"></i>
                                                        <span class="el-product-seller-details-label">فروشنده:</span>
                                                        <span class="el-product-seller-details--name">شیل ایران</span>
                                                        <img src="{{ asset('assets/img/logo-icon.png') }}"
                                                             class="el-product-seller-details--logo-small"
                                                             alt="seller-details--logo-small">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                @endif
            @endisset



            <div class="row banner-ads">
                @foreach($category_banners as $banner)
                    <div class="col-6 col-lg-3">
                        <div class="widget-banner card">
                            <a href="{{ route('products_category' , ["slug"=>$banner['slug']]) }}" target="_blank">
                                <img class="img-fluid" src="{{$banner['src']}}" alt="{{$banner['title']}}" title="{{$banner['title']}}">
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>



            {{--جدید ترین ها--}}
            @if($lastProducts->count())
                <div class="row">
                    <div class="col-12">
                        <div class="widget widget-product card">
                            <header class="card-header">
                                <h3 class="card-title">
                                    <span>جدید ترین ها در شیل ایران</span>
                                </h3>
                                <a href="#" class="view-all">مشاهده همه</a>
                            </header>
                            <div class="product-carousel owl-carousel owl-theme">
                                @foreach ($lastProducts as $lastProduct)
                                    <div class="item">
                                        <div class="el-product-card">

                                            <div class="el-product-thumbnail">
                                                <a href="show_product_by_id/{{ $lastProduct->id }}">
                                                    <img
                                                        src="{{ url('get_image_by_id/' . $lastProduct->images->first()?->id) }}"
                                                        class="img-fluid"
                                                        style="object-fit: contain;"
                                                        alt="{{ $lastProduct->product_name }}">
                                                </a>


                                            </div>


                                            <div class="el-product-card-body">
                                                <div class="el-product-title">
                                                    <h6>
                                                        <a href="show_product_by_id/{{ $lastProduct->id }}">
                                                            {{ $lastProduct->product_name }}
                                                        </a>
                                                    </h6>
                                                </div>

                                                <div class="el-product-info">
                                                    <div class="el-product-status">
                                                        <i class="fad fa-box-check"></i> موجود در انبار
                                                    </div>
                                                    <div class="el-product-rating">
                                                        <i class="fas fa-stars star"></i>
                                                        <strong></strong>
                                                        <span>{{ $lastProduct->inventory }}</span>
                                                    </div>
                                                </div>

                                                <div class="el-product-price">
                                                <span
                                                    class="el-price-value">{{ number_format($lastProduct->price) }}</span>
                                                    <span class="el-price-currency">{{ $lastProduct->price_unit}}</span>
                                                </div>
                                            </div>


                                            <div class="el-product-card-footer">
                                                <div class="el-product-seller-details">
                                                    <i class="fad fa-store-alt"></i>
                                                    <span class="el-product-seller-details-label">فروشنده:</span>
                                                    <span class="el-product-seller-details--name">شیل ایران</span>
                                                    <img src="{{ asset('assets/img/logo-icon.png') }}"
                                                         class="el-product-seller-details--logo-small"
                                                         alt="seller-details--logo-small">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>
                </div>
            @endif

            {{--تبلیغات--}}
            <div class="row banner-ads">
                <div class="col-12">
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <div class="widget-banner card">
                                <a href="#" target="_blank">
                                    <img class="img-fluid" src="assets/img/banner/banner-11.jpg" alt="">
                                </a>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="widget-banner card">
                                <a href="#" target="_top">
                                    <img class="img-fluid" src="assets/img/banner/banner-12.jpg" alt="">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{--            بیشترین بازدید ها--}}
            @if($topProducts->count())
                <div class="row">
                    <div class="col-12">
                        <div class="widget widget-product card">
                            <header class="card-header">
                                <h3 class="card-title">
                                    <span>بیشترین بازدید ها</span>
                                </h3>
                                <a href="#" class="view-all">مشاهده همه</a>
                            </header>
                            <div class="product-carousel owl-carousel owl-theme">
                                @foreach ($topProducts as $topProduct)
                                    <div class="item">
                                        <div class="el-product-card">

                                            <div class="el-product-thumbnail">
                                                <a href="show_product_by_id/{{ $topProduct->id }}">

                                                    <img
                                                        src="{{ url('get_image_by_id/' . $topProduct->images->first()?->id) }}"
                                                        class="img-fluid"
                                                        style="object-fit: contain;"
                                                        alt="{{ $topProduct->product_name }}">
                                                </a>


                                            </div>


                                            <div class="el-product-card-body">
                                                <div class="el-product-title">
                                                    <h6>
                                                        <a href="show_product_by_id/{{ $topProduct->id }}">
                                                            {{ $topProduct->product_name }}
                                                        </a>
                                                    </h6>
                                                </div>

                                                <div class="el-product-info">
                                                    <div class="el-product-status">
                                                        <i class="fad fa-box-check"></i> موجود در انبار
                                                    </div>
                                                    <div class="el-product-rating">
                                                        <i class="fas fa-stars star"></i>
                                                        <strong></strong>
                                                        <span>{{ $topProduct->inventory }}</span>
                                                    </div>
                                                </div>

                                                <div class="el-product-price">
                                                <span
                                                    class="el-price-value">{{ number_format($topProduct->price) }}</span>
                                                    <span class="el-price-currency">{{ $topProduct->price_unit }}</span>
                                                </div>
                                            </div>


                                            <div class="el-product-card-footer">
                                                <div class="el-product-seller-details">
                                                    <i class="fad fa-store-alt"></i>
                                                    <span class="el-product-seller-details-label">فروشنده:</span>
                                                    <span class="el-product-seller-details--name">شیل ایران</span>
                                                    <img src="{{ asset('assets/img/logo-icon.png') }}"
                                                         class="el-product-seller-details--logo-small"
                                                         alt="seller-details--logo-small">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>
                </div>
            @endif
            <div class="row banner-ads">
                <div class="col-12">
                    <div class="row">
                        <div class="col-12">
                            <div class="widget widget-banner card">
                                <a href="#" target="_blank">
                                    <img class="img-fluid" src="assets/img/banner/baner-22.jpg" alt="">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!--
                        <div class="row">
                            <div class="col-12">
                                <div class="brand-slider card">
                                    <header class="card-header">
                                        <h3 class="card-title"><span>برندهای ویژه</span></h3>
                                    </header>
                                    <div class="owl-carousel">
                                        <div class="item">
                                            <a href="#">
                                                <img src="assets/img/brand/1076.png" alt="">
                                            </a>
                                        </div>
                                        <div class="item">
                                            <a href="#">
                                                <img src="assets/img/brand/1078.png" alt="">
                                            </a>
                                        </div>
                                        <div class="item">
                                            <a href="#">
                                                <img src="assets/img/brand/1080.png" alt="">
                                            </a>
                                        </div>
                                        <div class="item">
                                            <a href="#">
                                                <img src="assets/img/brand/2315.png" alt="">
                                            </a>
                                        </div>
                                        <div class="item">
                                            <a href="#">
                                                <img src="assets/img/brand/5189.png" alt="">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->


        </div>
    </main>

@endsection
