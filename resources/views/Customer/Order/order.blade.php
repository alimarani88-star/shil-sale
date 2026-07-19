@extends('Customer.Layout.master')

@section('content')
    <link href="assets/css/main.css" rel="stylesheet" />

    <style>
        @media (max-width: 768px) {
            .cart-page-title {
                background: #fff;
                padding: 1rem;
                margin-bottom: 0.75rem;
                border-bottom: 1px solid #eee;
            }

            .cart-page-title h1 {
                font-size: 1.1rem;
                margin: 0;
                text-align: center;
            }
        }
    </style>
    <div class="shopping-page">
        <!-- main-shopping -->
        <main class="cart-page default">
            <div class="container">
                <div class="row">
                    <!-- ستون اصلی (آدرس + نحوه ارسال + محصولات) -->
                    <div class="cart-page-content col-xl-9 col-lg-8 col-md-12 order-1">
                        <div class="cart-page-title">
                            <h1>انتخاب آدرس تحویل سفارش</h1>
                        </div>

                        <section class="page-content default">

                            <!-- لیست آدرس‌ها -->
                            @foreach ($addresses as $address)
                                <div class="address-section" data-address-id="{{ $address->id }}">
                                    <div class="checkout-contact">
                                        <div class="checkout-contact-content">
                                            <ul class="checkout-contact-items">
                                                <li class="checkout-contact-item">
                                                    گیرنده:
                                                    <span class="full-name">{{ $address->recipient_first_name }}</span>
                                                    <a class="checkout-contact-btn-edit">اصلاح این آدرس</a>
                                                </li>
                                                <li class="checkout-contact-item">
                                                    <div class="checkout-contact-item checkout-contact-item-mobile">
                                                        شماره تماس:
                                                        <span class="mobile-phone">{{ $address->mobile }}</span>
                                                    </div>
                                                    <div class="checkout-contact-item-message">
                                                        کد پستی:
                                                        <span class="post-code">{{ $address->postal_code }}</span>
                                                    </div>
                                                    <br>
                                                    استان
                                                    <span class="state">{{ $address->province->title }}</span>
                                                    ، شهر
                                                    <span class="city">{{ $address->city->title }}</span>
                                                    ،
                                                    <span class="address-part">{{ $address->province->title }} -
                                                        {{ $address->city->title }}</span>
                                                </li>
                                            </ul>
                                            <div class="checkout-contact-badge" style="opacity: 0;">
                                                <i class="now-ui-icons ui-1_check"></i>
                                            </div>
                                        </div>
                                        <a href="#" class="checkout-contact-location">انتخاب آدرس ارسال</a>
                                    </div>
                                </div>
                            @endforeach

                            <!-- نحوه ارسال -->
                            <div class="headline"><span>انتخاب نحوه ارسال</span></div>
                            <div class="checkout-shipment">
                                <div class="radio">
                                    <input type="radio" name="shipping_method" id="radio1" value="normal">
                                    <label for="radio1">عادی</label>
                                </div>
                                <div class="radio">
                                    <input type="radio" name="shipping_method" id="radio2" value="fast" checked>
                                    <label for="radio2">سریع‌ (مرسوله‌ها در سریع‌ترین زمان ممکن ارسال می‌شوند)</label>
                                </div>
                            </div>

                            <!-- مرسوله -->
                            <div class="headline"><span>مرسوله ۱ از ۱</span></div>
                            <div class="checkout-pack">
                                <section class="products-compact">
                                    <div class="box">
                                        <div class="row">
                                            @foreach ($carts as $cart)
                                                <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                                                    <div class="product-box-container">
                                                        <div class="product-box product-box-compact">
                                                            <a class="product-box-img">
                                                                <img
                                                                    src="{{ url('get_image_by_id/' . $cart->product->images->first()->id) }}">
                                                            </a>
                                                            <div class="product-box-title">
                                                                {{ $cart->product->product_name }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </section>

                                <div class="row">
                                    <div class="checkout-time-table checkout-time-table-time">
                                        <span class="checkout-additional-options-checkbox-image"></span>
                                        <div>
                                            <div class="checkout-time-table-title-bar checkout-time-table-title-bar-city">
                                      بازه تحویل سفارش: زمان تقریبی تحویل 
                                                5 روز پس از سفارش
                                            </div>
                                            <ul class="checkout-time-table-subtitle-bar">
                                                <!-- <li>شیوه ارسال : پست پیشتاز با ظرفیت اختصاصی برای دیجی کالا</li> -->
                                                <!-- <li>هزینه ارسال: <span class="">رایگان</span></li> -->
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- صدور فاکتور -->
                            <div class="headline"><span>صدور فاکتور</span></div>
                            <div class="checkout-invoice">
                                <div class="checkout-invoice-headline">
                                    <div class="form-account-agree">
                                        <label class="checkbox-form checkbox-primary">
                                            <input type="checkbox" checked id="agree">
                                            <span class="checkbox-check"></span>
                                        </label>
                                        <label for="agree">درخواست ارسال فاکتور خرید</label>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                    <!-- ستون کناری (خلاصه + دکمه) -->
                    <aside class="cart-page-aside col-xl-3 col-lg-4 col-md-6 center-section order-2">
                        <div class="checkout-aside">
                            <div class="checkout-summary">
                                <div class="checkout-summary-main">
                                    <ul class="checkout-summary-summary">
                                        <li><span>مبلغ کل ({{ $carts->count() }} کالا)</span>
                                            <span>{{ number_format($amountPayable) }} ریال</span>
                                        </li>
                                    </ul>

                                    <div class="checkout-summary-devider">
                                        <div></div>
                                    </div>

                                    <div class="checkout-summary-content">
                                        <!-- فرم اصلی ارسال داده‌ها -->
                                        <form method="POST" id="shipping-data-form" action="{{ route('order_payment') }}"
                                            style="display: none;">
                                            @csrf
                                            <input type="hidden" name="selected_address_id" id="selected_address_id"
                                                value="">
                                            <input type="hidden" name="shipping_method" id="hidden_shipping_method"
                                                value="fast">
                                            <input type="hidden" name="request_invoice" id="hidden_request_invoice"
                                                value="1">
                                        </form>

                                        <div class="checkout-summary-price-title">مبلغ قابل پرداخت:</div>
                                        <div class="checkout-summary-price-value">
                                            <span class="checkout-summary-price-value-amount">
                                                {{ number_format($amountPayable) }}
                                            </span> ریال
                                        </div>

                                        <div class="parent-btn selenium-next-step-shipping">
                                            <button type="submit" form="shipping-data-form" class="dk-btn custom-primary"
                                                style="font-size: 16px;">
                                                ادامه ثبت سفارش
                                                <i class="now-ui-icons shopping_basket"></i>
                                            </button>
                                        </div>

                                        <div>
                                            <span>
                                                کالاهای موجود در سبد شما ثبت و رزرو نشده‌اند، برای ثبت سفارش مراحل بعدی را
                                                تکمیل کنید.
                                            </span>
                                            <span class="wiki wiki-holder">
                                                <span class="wiki-sign"></span>
                                                <div class="wiki-container is-right">
                                                    <div class="wiki-arrow"></div>
                                                    <p class="wiki-text">
                                                        محصولات موجود در سبد خرید شما تنها در صورت ثبت و پرداخت سفارش برای
                                                        شما رزرو می‌شوند.
                                                        در صورت عدم ثبت سفارش، تاپ کالا هیچگونه مسئولیتی در قبال تغییر قیمت
                                                        یا موجودی این کالاها ندارد.
                                                    </p>
                                                </div>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="checkout-feature-aside">
                                <ul>
                                    <li class="checkout-feature-aside-item checkout-feature-aside-item-guarantee">هفت روز
                                        ضمانت تعویض</li>
                                    <li class="checkout-feature-aside-item checkout-feature-aside-item-cash">پرداخت در محل
                                        با کارت بانکی</li>
                                    <li class="checkout-feature-aside-item checkout-feature-aside-item-express">تحویل اکسپرس
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </main>
    </div>
@endsection

@section('script')
    <script>


        document.addEventListener('DOMContentLoaded', function () {
            @if ($errors->any())
                // جمع‌آوری همه خطاها
                let errorMessages = [
                    @foreach ($errors->all() as $error)
                        "{{ addslashes($error) }}",
                    @endforeach
                ];

                // نمایش همه خطاها به صورت لیست
                Swal.fire({
                    icon: 'error',
                    title: 'SHILIRAN',
                    html: '<ul style="text-align: right; direction: rtl; margin: 0; padding-right: 20px; list-style: persian;">' +
                        errorMessages.map(msg => `<li style="margin-right: 3rem;">${msg}</li>`).join('') +
                        '</ul>',
                    showConfirmButton: true,
                    confirmButtonText: 'متوجه شدم',
                    timer: 5000, // ۵ ثانیه خودکار بسته بشه
                    timerProgressBar: true,
                    customClass: {
                        popup: 'animated fadeIn',
                        confirmButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                });
            @endif
        });

        $(document).ready(function () {
            let selectedAddressId = null;

            $(document).on('click', '.checkout-contact-location', function (e) {
                e.preventDefault();
                const $button = $(this);
                const $section = $button.closest('.address-section');
                const $contact = $section.find('.checkout-contact');
                const addressId = $section.data('address-id');

                // ریست همه
                $('.checkout-contact').css({
                    'border': '2px solid transparent',
                    'background': '#fff'
                });
                $('.checkout-contact-location').css({
                    'background': '#ededed',
                    'color': '#6d6d6d',
                    'border': '1px solid #ddd'
                });

                $contact.css({ 'border': '2px solid #eee' });
                $button.css({
                    'background': 'var(--custom-primary-color)',
                    'color': 'white',
                    'border-color': 'var(--custom-primary-color)'
                });
                $contact.find('.checkout-contact-badge').css({ 'opacity': '1' });
                $('.checkout-contact').not($contact).find('.checkout-contact-badge').css({ 'opacity': '0' });

                selectedAddressId = addressId;
                $('#selected_address_id').val(addressId);
            });

            $('input[name="shipping_method"]').on('change', function () {
                $('#hidden_shipping_method').val($(this).val());
            });

            $('#agree').on('change', function () {
                $('#hidden_request_invoice').val($(this).is(':checked') ? '1' : '0');
            });

            $('#shipping-data-form').on('submit', function (e) {
                if (!selectedAddressId) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'SHILIRAN',
                        text: "لطفا یک آدرس برای ارسال محصول انتخاب نمایید",
                        showConfirmButton: false,
                        timer: 2000
                    });
                    return false;
                }
            });
        });
    </script>
@endsection