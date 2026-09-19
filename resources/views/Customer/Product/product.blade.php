@extends('Customer.Layout.master')

@php
    $seoProductName = trim((string) $product->product_name);
    $seoCanonical = 'https://www.shil.ir/product/' . $product->slug;
    $seoDescriptionRaw = trim(preg_replace('/\s+/u', ' ', strip_tags((string) ($product->description ?? ''))));
    $seoDescription = $seoDescriptionRaw !== ''
        ? \Illuminate\Support\Str::limit($seoDescriptionRaw, 160, '...')
        : 'خرید ' . $seoProductName . ' شیل ایران با مشاهده قیمت، مشخصات فنی و امکان ثبت سفارش آنلاین در فروشگاه شیل ایران.';
    $seoImage = $product->images->isNotEmpty()
        ? 'https://www.shil.ir/get_image_by_id/' . $product->images->first()->id
        : 'https://www.shil.ir/assets/img/logo-icon.png';
    $seoPrice = (float) $product->price;
    if (!empty($product->productPercentage) && (float) $product->productPercentage > 0) {
        $seoPrice = round($seoPrice * (1 - ((float) $product->productPercentage / 100)));
    }
    $seoInStock = ((int) $product->marketable > 0 && (int) ($inventory ?? 0) > 0);
    $seoTitle = 'خرید ' . $seoProductName . ' | قیمت و مشخصات | شیل ایران';
@endphp

@section('title', $seoTitle)
@section('canonical', $seoCanonical)

@section('meta')
    <meta name="description" content="{{ $seoDescription }}">

    <meta property="og:type" content="product">
    <meta property="og:locale" content="fa_IR">
    <meta property="og:site_name" content="شیل ایران">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ $seoCanonical }}">
    <meta property="og:image" content="{{ $seoImage }}">
    <meta property="product:price:amount" content="{{ $seoPrice }}">
    <meta property="product:price:currency" content="IRR">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $seoImage }}">

    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $seoProductName,
            'description' => $seoDescription,
            'image' => [$seoImage],
            'sku' => (string) ($product->product_id_in_app ?? $product->id),
            'brand' => [
                '@type' => 'Brand',
                'name' => 'شیل ایران',
            ],
            'offers' => [
                '@type' => 'Offer',
                'url' => $seoCanonical,
                'priceCurrency' => 'IRR',
                'price' => (string) $seoPrice,
                'availability' => $seoInStock
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
                'seller' => [
                    '@type' => 'Organization',
                    'name' => 'شیل ایران',
                ],
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}
    </script>
@endsection

@section('content')

    <style>
        .d-none {
            display: none !important;
        }
        .owl-item{
            width: 81px !important;
        }
        .owl-item.active{
            margin-left: 0px !important;
        }
        .product-image-column {
            position: relative;
        }
        main.single-product .product .product-image-frame {
            border: 1px solid #ac4fea;
            border-radius: 8px;
            padding: 12px;
            position: relative;
            background: #fff;
            overflow: hidden;
        }
        main.single-product .product .product-image-frame .product-gallery {
            position: relative;
            z-index: 1;
            min-height: 410px;
        }
        main.single-product .product .product-image-frame .zoomWrapper {
            float: none !important;
            margin-bottom: 0 !important;
            width: 100% !important;
            height: 410px !important;
        }
        main.single-product .product .product-image-frame ul.gallery-options {
            top: 18px;
            right: 18px;
            z-index: 2;
        }
        main.single-product .product .product-image-column #gallery_01f {
            float: none !important;
            width: 100% !important;
            margin-top: 12px;
        }
    </style>

    <div class="wrapper default">

        <main class="single-product default">
            <div class="container">
                <div class="row ">

                    <div class="col-lg-8 col-12">
                        <article class="product ">
                            <div class="row">
                                <div class="col-lg-5 col-md-6 col-sm-12 product-image-column">

                                    <div class="product-image-frame">
                                        <div class="product-gallery default">

                                            @if($product->images->count() > 0)
                                                <img class="zoom-img" id="img-product-zoom"
                                                     src="{{ url('get_image_by_id/' . $product->images->first()->id) }}"
                                                     data-zoom-image="{{ url('get_image_by_id/' . $product->images->first()->id) }}" />
                                            @endif


                                        </div>


                                        <ul class="gallery-options">
                                            <li>
                                                <section class="product-add-to-favorite position-relative" style="top: 0">
                                                    <button type="button" class="btn btn-light btn-sm text-decoration-none"
                                                            data-url="{{ route('add_to_favorites', $product) }}"
                                                            data-bs-toggle="tooltip" data-bs-placement="left">
                                                        <i
                                                            class="fa fa-heart {{ $product->favoritedBy->contains(auth()->id()) ? 'text-danger' : '' }}"></i>
                                                        <span
                                                            class="tooltip-option">{{ $product->favoritedBy->contains(auth()->id()) ? 'حذف از علاقه‌مندی' : 'افزودن به علاقه‌مندی' }}</span>
                                                    </button>
                                                </section>
                                            </li>
                                            <li>
                                                <button data-toggle="modal" data-target="#myModal"><i
                                                        class="fa fa-share-alt"></i></button>
                                                <span class="tooltip-option">اشتراک گذاری</span>
                                            </li>
                                        </ul>
                                    </div>
                                    <!-- Modal Core -->
                                    <div class="modal-share modal fade" id="myModal" tabindex="-1" role="dialog"
                                         aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            aria-hidden="true">&times;
                                                    </button>
                                                    <h4 class="modal-title" id="myModalLabel">اشتراک گذاری</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <form class="form-share">
                                                        <div class="form-share-title">اشتراک گذاری در شبکه های اجتماعی
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <ul class="btn-group-share">
                                                                    <li><a href="#" class="btn-share btn-share-twitter"
                                                                           target="_blank"><i
                                                                                class="fa fa-twitter"></i></a></li>
                                                                    <li><a href="#" class="btn-share btn-share-facebook"
                                                                           target="_blank"><i
                                                                                class="fa fa-facebook"></i></a></li>
                                                                    <li><a href="#" class="btn-share btn-share-google-plus"
                                                                           target="_blank"><i
                                                                                class="fa fa-google-plus"></i></a></li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <div class="form-share-title">ارسال به ایمیل</div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <label class="ui-input ui-input-send-to-email">
                                                                    <input class="ui-input-field" type="email"
                                                                           placeholder="آدرس ایمیل را وارد نمایید.">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <button class="btn-primary">ارسال</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <form class="form-share-url default">
                                                        <div class="form-share-url-title">آدرس صفحه</div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <label class="ui-url">
                                                                    <input class="ui-url-field" value="">
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="gallery_01f">
                                        <ul class="gallery-items owl-carousel owl-theme" id="gallery-slider">
                                            @foreach($product->images as $key => $image)
                                                <li class="item">
                                                    <a href="#" class="elevatezoom-gallery {{ $key == 0 ? 'active' : '' }}"
                                                       data-image="{{ url('get_image_by_id/' . $image->id) }}"
                                                       data-zoom-image="{{ url('get_image_by_id/' . $image->id) }}">
                                                        <img src="{{ url('get_image_by_id/' . $image->id) }}" width="100" />
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                </div>

                                <div class="col-lg-7 col-md-6 col-sm-12">
                                    <div class="product-title default">
                                        <h1>
                                            {{$product->product_name}}
                                            <span></span>
                                        </h1>
                                    </div>
                                    <div class="product-directory default">
                                        <ul>
                                            <li>
                                                <span>برند</span> :
                                                <span class="product-brand-title">شیل ایران</span>
                                            </li>
                                            @if(!empty($mainGroupName))
                                                <li>
                                                    <span>دسته‌بندی</span> :
                                                    <a href="#" class="btn-link-border">
                                                        {{ $mainGroupName }}
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>

                                    @if($product_Meta != null)
                                        <div class="product-variants default">
                                            <span>{{$product_Meta->meta_name}} :</span>
                                            <div class="radio">
                                                <input type="radio" name="radio1" id="radio1" checked>
                                                <label for="radio1">
                                                    {{$product_Meta->meta_value}}
                                                </label>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="product-guarantee default">
                                        <i class="fa fa-check-circle"></i>
                                        <p class="product-guarantee-text">
                                            @if(!empty($guaranteeDuration))
                                                گارانتی {{ $guaranteeDuration }} ماهه
                                            @else
                                                گارانتی اصالت و سلامت فیزیکی کالا
                                            @endif
                                        </p>
                                    </div>
                                    <div class="product-delivery-seller default">
                                        <p>
                                            <i class="now-ui-icons shopping_shop"></i>
                                            <span>فروشنده:‌</span>
                                            <a href="#" class="btn-link-border">شیل ایران</a>
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        @if($product->productPercentage && $product->productPercentage > 0)
                                            <div class="price-discount mt-3" data-title="تخفیف">
                                                <span>{{ $product->productPercentage }}</span><span>%</span>
                                            </div>
                                        @else
                                            <span class="badge badge-secondary">بدون تخفیف</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>


                    <div class="col-lg-4 col-12">
                        <aside class="product-box-cart card p-3" style="box-shadow: none; border-color: #ac4fea;">
                            <div class="price-product defualt mb-3">

                                @if($product->productPercentage == null)
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <div class="price-value d-flex align-items-baseline">
                                            <!-- <p class="mb-0 me-2">قیمت</p> -->
                                        </div>
                                        <div>
                                            <span
                                                class="price-currency small text-muted">{{number_format($product->price)}}</span>
                                            <span class="fs-5 fw-bold">{{$product->price_unit}}</span>
                                        </div>
                                    </div>
                                @else
                                    @php
                                        $old_price = $product->price;
                                        $discounted_price = round($old_price * (1 - ($product->productPercentage / 100)));
                                    @endphp
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <div class="price-value d-flex align-items-baseline"></div>
                                        <div>
                                            <div class="price mb-2">

                                                <del style="color: #999; font-size: 0.9rem;">{{ number_format($old_price) }}
                                                    <span>{{$product->price_unit}}</span></del>
                                                <ins
                                                    style="display:block; font-size: 1.2rem; font-weight: bold; color: #e74c3c;text-decoration:none">{{ number_format($discounted_price) }}
                                                    <span>{{$product->price_unit}}</span></ins>

                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <hr>


                            <div class="product-quantity default my-3">
                                <div class="d-flex align-items-center">
                                    <label class="d-block mb-1" style="margin-left:8rem">تعداد:</label>

                                    <button type="button" class="btn btn-sm btn-outline-secondary qty-btn"
                                            id="qty-decrease">−</button>
                                    <input id="product-qty" min="1"
                                           @if(!empty($maximumOrderLimit)) max="{{ (int) $maximumOrderLimit }}" @endif
                                           value="{{ $cart_items->count ?? 1 }}"
                                           class="form-control text-center mx-2" style="width:80px;">
                                    <button type="button" class="btn btn-sm btn-outline-secondary qty-btn"
                                            id="qty-increase">+</button>
                                </div>
                            </div>


                            <div class="product-add default mt-3">
                                @if(auth()->check())
                                    @if($product->marketable > 0 and $inventory > 0)
                                        @if($existsInCart)
                                            <button class="btn custom-danger d-block w-100 " id="remove_from_cart">
                                                حذف از سبد خرید
                                            </button>
                                            <button class="btn custom-primary d-none w-100" id="add_product_to_cart"
                                                style="display: block;">افزودن به سبد
                                                خرید
                                            </button>
                                        @else
                                            <button class="btn custom-primary d-block w-100" id="add_product_to_cart"
                                                style="display: block;">افزودن به سبد
                                                خرید
                                            </button>
                                            <button class="btn custom-danger d-block w-100 d-none" id="remove_from_cart">
                                                حذف از سبد خرید
                                            </button>
                                        @endif
                                    @else
                                        <button id="next-level" class="btn btn-secondary disabled d-block w-100">محصول
                                            نا
                                            موجود
                                            میباشد
                                        </button>
                                    @endif
                                @else
                                    <a id="next-level" class="btn btn-secondary d-block w-100 btn-warning btn-danger"
                                       style="color: white;" href="{{ route('login') }}">برای ثبت سفارش وارد حساب کاربر خود
                                        شوید</a>
                                @endif
                            </div>

                        </aside>
                    </div>
                </div>



                <div class="row">
                    <div class="container">
                        <div class="col-12 default no-padding">
                            <div class="product-tabs default">
                                <div class="box-tabs default">
                                    <ul class="nav" role="tablist">
                                        <li class="box-tabs-tab">
                                            <a class="active" data-toggle="tab" href="#desc" role="tab"
                                               aria-expanded="true">
                                                <i class="now-ui-icons education_glasses"></i> معرفی
                                            </a>
                                        </li>
                                        <li class="box-tabs-tab">
                                            <a data-toggle="tab" href="#params" role="tab" aria-expanded="false">
                                                <i class="now-ui-icons design_bullet-list-67"></i> مشخصات
                                            </a>
                                        </li>
                                        <li class="box-tabs-tab">
                                            <a data-toggle="tab" href="#comments" role="tab" aria-expanded="false">
                                                <i class="now-ui-icons ui-2_chat-round"></i> دیدگاها
                                            </a>
                                        </li>
                                        <li class="box-tabs-tab">
                                            <a data-toggle="tab" href="#questions" role="tab" aria-expanded="false">
                                                <i class="now-ui-icons travel_info"></i> افزودن دیدگاه
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="card-body default">
                                        <!-- Tab panes -->
                                        <div class="tab-content">
                                            <div class="tab-pane fade show active" id="desc" role="tabpanel"
                                                 aria-expanded="true">
                                                <article>
                                                    <h2 class="param-title">
                                                        <span>{{$product->product_name}}</span>
                                                    </h2>
                                                    <div class="parent-expert default">
                                                        <div class="content-expert">
                                                            <p>
                                                                @if($postStatus)
                                                                    {!! $postStatus->content !!}
                                                                @endif
                                                            </p>
                                                        </div>
                                                        <div class="sum-more">
                                                            <span class="show-more btn-link-border">
                                                                بستن
                                                            </span>
                                                            <span class="show-less btn-link-border">
                                                                نمایش بیشتر
                                                            </span>
                                                        </div>
                                                        <div class="shadow-box"></div>
                                                    </div>

                                                </article>
                                            </div>
                                            <div class="tab-pane fade params" id="params" role="tabpanel"
                                                 aria-expanded="false">
                                                <article>
                                                    <section>
                                                        <h2 class="param-title mb-3">
                                                            سایر توضیحات
                                                        </h2>
                                                        <span>{{$product->description}}</span>
                                                    </section>

                                                    <section>
                                                        <h3 class="params-title">مشخصات </h3>
                                                        <ul class="params-list">
                                                            @foreach($productAttributes as $attribute)
                                                                <li>
                                                                    <div class="params-list-key">
                                                                        <span class="block">{{ $attribute->meta_name }}</span>
                                                                    </div>
                                                                    <div class="params-list-value">
                                                                        <span class="block">
                                                                            {{ $attribute->meta_value }}
                                                                        </span>
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </section>

                                                </article>
                                            </div>
                                            <div class="tab-pane fade" id="comments" role="tabpanel" aria-expanded="false">
                                                <article>
                                                    <h2 class="param-title">
                                                        نظرات کاربران
                                                        <span>{{ count($comments) }} نظر</span>
                                                    </h2>
                                                    <div class="comments-area default">
                                                        <ol class="comment-list">
                                                            @foreach($comments as $comment)
                                                                <li>
                                                                    <div class="comment-body">
                                                                        <div class="row">
                                                                            <div class="col-md-3 col-sm-12">
                                                                                <div
                                                                                    class="message-light message-light--purchased">
                                                                                    خریدار این محصول
                                                                                </div>
                                                                                <ul class="comments-user-shopping">

                                                                                    <li>
                                                                                        <div class="cell">خریداری شده
                                                                                            از:
                                                                                        </div>
                                                                                        <div class="cell seller-cell">
                                                                                            <span class="o-text-blue">شیل
                                                                                                ایران</span>
                                                                                        </div>
                                                                                    </li>
                                                                                </ul>
                                                                                <div
                                                                                    class="message-light message-light--opinion-positive">
                                                                                    خرید این محصول را توصیه می‌کنم
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-9 col-sm-12 comment-content">
                                                                                <div class="comment-title">
                                                                                    {{$product->product_name}}
                                                                                </div>
                                                                                <div class="comment-author">
                                                                                    توسط {{$comment->user_name}} در
                                                                                    تاریخ {{$comment->created_at_jalali}}

                                                                                </div>

                                                                                <p>{{$comment->content}}</p>


                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            @endforeach

                                                        </ol>
                                                    </div>
                                                </article>
                                            </div>


                                            <div class="tab-pane fade form-comment" id="questions" role="tabpanel"
                                                 aria-expanded="false">
                                                @guest
                                                    <section class="modal-body">
                                                        <p>کاربر گرامی لطفا برای ثبت نظر ابتدا وارد حساب کاربری خود
                                                            شوید </p>
                                                        <p>
                                                            لینک ثبت نام و یا ورود
                                                            <a href="{{ route('login') }}" class="custom-primary">کلیک
                                                                کنید</a>
                                                        </p>
                                                    </section>
                                                @endguest
                                                @auth
                                                    <article>
                                                        <h2 class="param-title mb-3">
                                                            افزودن نظر
                                                            <h6 class="d-block mb-3">نظر خود را در مورد محصول مطرح
                                                                نمایید</h6>
                                                        </h2>

                                                        <form action="{{ route('C_add_comment', ['id' => $product->id]) }}"
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            <div class="mb-3">
                                                                <textarea
                                                                    class="form-control shadow-sm border border-2 border-secondary rounded-3"
                                                                    placeholder="نظر خود را اینجا بنویسید..." rows="4" required
                                                                    name="comment"></textarea>
                                                            </div>
                                                            <button type="submit" class="btn custom-primary px-4 rounded-pill">
                                                                ارسال نظر
                                                            </button>
                                                        </form>
                                                    </article>
                                                @endauth
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

    </div>

@endsection



@section('script')
    <script>
        $(document).ready(function () {
            const $qtyInput = $('#product-qty');
            const $increase = $('#qty-increase');
            const $decrease = $('#qty-decrease');
            const $remove_from_cart = $('#remove_from_cart');
            const maxOrderLimit = @json($maximumOrderLimit ?? null);

            function clampQty(qty) {
                qty = parseInt(qty) || 1;
                if (qty < 1) qty = 1;
                if (maxOrderLimit !== null && qty > maxOrderLimit) {
                    qty = maxOrderLimit;
                }
                return qty;
            }

            function showLimitError() {
                if (maxOrderLimit === null) return;
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'warning',
                    title: 'حداکثر تعداد مجاز سفارش این محصول ' + maxOrderLimit + ' عدد هست . لطفا برای سفارش بیشتر با واحد فروش به شماره 03133122 تماس حاصل فرمایید',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            }

            $('#add_product_to_cart').click(function (e) {
                addToCart();
            });

            $increase.on('click', function () {
                let current = parseInt($qtyInput.val()) || 1;
                if (maxOrderLimit !== null && current >= maxOrderLimit) {
                    $qtyInput.val(maxOrderLimit);
                    showLimitError();
                    return;
                }
                const next = clampQty(current + 1);
                $qtyInput.val(next);

                if (!$('#remove_from_cart').hasClass('d-none')) {
                    updateCartQuantity(next);
                }
            });

            $decrease.on('click', function () {
                let current = parseInt($qtyInput.val()) || 1;
                if (current > 1) {
                    $qtyInput.val(current - 1);

                    if (!$('#remove_from_cart').hasClass('d-none')) {
                        updateCartQuantity(current - 1);
                    }
                }
            });

            function updateCartQuantity(qty) {
                const previousQty = parseInt($qtyInput.data('last-qty')) || parseInt($qtyInput.val()) || 1;
                qty = clampQty(qty);
                $qtyInput.val(qty);

                $.ajax({
                    url: '{{ route("update_count_product_cart") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        quantity: qty,
                        product_id: '{{ $product->id }}'
                    },
                    success: function (result) {
                        if (result.status === 'success') {
                            $qtyInput.data('last-qty', qty);
                            $('.cart-number').text(result.cart_count);
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: result.message,
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                            updateCartHeader();
                        }
                    },
                    error: function (xhr) {
                        const currentCount = xhr.responseJSON && xhr.responseJSON.data
                            ? xhr.responseJSON.data.current_count
                            : previousQty;
                        $qtyInput.val(currentCount);
                        $qtyInput.data('last-qty', currentCount);
                        const message = (xhr.responseJSON && xhr.responseJSON.message)
                            ? xhr.responseJSON.message
                            : 'خطا در به‌روزرسانی تعداد';
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: message,
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }
                });
            }

            $('#product-qty').on('blur', function () {
                let current = clampQty($qtyInput.val());
                $qtyInput.val(current);
                if (!$('#remove_from_cart').hasClass('d-none')) {
                    updateCartQuantity(current);
                }
            });

            $qtyInput.on('input', function () {
                let value = parseInt($(this).val());
                if (isNaN(value) || value < 1) {
                    $(this).val(1);
                    return;
                }
                if (maxOrderLimit !== null && value > maxOrderLimit) {
                    $(this).val(maxOrderLimit);
                    showLimitError();
                }
            });

            $qtyInput.data('last-qty', clampQty($qtyInput.val()));

            $remove_from_cart.on('click', function () {
                const productId = '{{ $product->id }}';
                $.ajax({
                    url: '{{ route("remove_from_cart") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_id: productId
                    },
                    success: function (result) {
                        if (result.status === 'success') {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: result.message,
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                            $('.cart-number').text(result.cart_count);
                            updateCartHeader();
                            $('#add_product_to_cart').removeClass('d-none');
                            $('#remove_from_cart').addClass('d-none');
                        }
                    },
                    error: function () {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'خطا در حذف محصول از سبد خرید',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }
                });
            });

            function updateCartQuantity(qty) {
                $.ajax({
                    url: '{{ route("update_count_product_cart") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        quantity: qty,
                        product_id: '{{ $product->id }}'
                    },
                    success: function (result) {
                        if (result.status === 'success') {
                            $('.cart-number').text(result.cart_count);
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: result.message,
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                            updateCartHeader();
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let firstError = Object.values(errors)[0][0];

                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: firstError,
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                        } else {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: 'خطایی رخ داد، لطفاً مجدداً تلاش کنید.',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                        }
                    }
                });
            }

            function addToCart() {

                var qty = clampQty($('#product-qty').val());
                $('#product-qty').val(qty);

                $.ajax({
                    url: '{{ route("add_product_to_cart") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        quantity: qty,
                        product_id: '{{ $product->id }}'
                    },
                    success: function (result) {
                        if (result.status === 'success') {
                            $qtyInput.data('last-qty', qty);
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'محصول با موفقیت به سبد خرید اضافه شد.',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                            $('#add_product_to_cart').addClass('d-none');
                            $('#remove_from_cart').removeClass('d-none');
                            $('.cart-number').text(result.cart_count);
                            updateCartHeader();

                        } else if (result.status === 'error') {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: result.message,
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                        }
                    },
                    error: function (xhr) {
                        const message = (xhr.responseJSON && xhr.responseJSON.message)
                            ? xhr.responseJSON.message
                            : (
                                xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors
                                    ? Object.values(xhr.responseJSON.errors)[0][0]
                                    : 'خطایی رخ داد، لطفاً مجدداً تلاش کنید.'
                            );

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: message,
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }
                });
            }

        });

    </script>

    <script>
        $('.product-add-to-favorite button').click(function () {

            event.preventDefault();

            let element = $(this);
            let url = element.data('url');
            let icon = element.find('i.fa-heart');
            let tooltipSpan = element.find('.tooltip-option');

            $.ajax({
                url: url,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function (result) {

                    if (result.status == 1) {
                        icon.addClass('text-danger');
                        tooltipSpan.text('حذف از علاقه‌مندی');
                    } else if (result.status == 2) {
                        icon.removeClass('text-danger');
                        tooltipSpan.text('افزودن به علاقه‌مندی');
                    } else if (result.status == 3) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'warning',
                            title: ' . لطفا برای افزودن به علاقه‌مندی‌ها ابتدا وارد سایت شوید',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }

                    element.tooltip('dispose').tooltip();
                }
            });
        });
    </script>



@endsection
