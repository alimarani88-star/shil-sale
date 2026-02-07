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
                            <div class="address-section" data-address-id="{{ $default_address->id ?? "" }}">
                                <div class="checkout-contact ">
                                    <div class="checkout-contact-content">
                                        <ul class="checkout-contact-items">
                                            <li class="checkout-contact-item">
                                                گیرنده:
                                                <span
                                                    class="full-name">{{ $default_address->recipient_first_name ?? "" }}</span>
                                                <a class="checkout-contact-btn-edit">اصلاح این آدرس</a>
                                            </li>
                                            <li class="checkout-contact-item">
                                                <div class="checkout-contact-item checkout-contact-item-mobile">
                                                    شماره تماس:
                                                    <span class="mobile-phone">{{ $default_address->mobile ?? "" }}</span>
                                                </div>
                                                <div class="checkout-contact-item-message">
                                                    کد پستی:
                                                    <span class="post-code">{{ $default_address->postal_code ?? "" }}</span>
                                                </div>
                                                <br>
                                                استان
                                                <span class="state">{{ $default_address->province->title ?? ""}}</span>
                                                ، شهر
                                                <span class="city">{{ $default_address->city->title ?? ""}}</span>
                                                ،
                                                <span class="address-part">{{ $default_address->province->title ?? ""}} -
                                                    {{ $default_address->city->title ?? ""}}</span>
                                            </li>
                                        </ul>
                                        <div class="checkout-contact-badge">
                                            <i class="now-ui-icons ui-1_check"></i>
                                        </div>
                                    </div>
                                    <button href="#" class="checkout-contact-location" data-toggle="modal"
                                        data-target="#addAddressModal">تغییر آدرس </button>
                                </div>
                            </div>
                            <!-- مرسوله -->
                            <div class="headline"><span>مرسوله </span></div>
                            <div class="checkout-pack">
                                <section class="products-compact">
                                    <div class="box">
                                        <div class="row">
                                            @if(isset($carts))
                                                @foreach ($carts as $cart)
                                                    <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                                                        <div class="product-box-container">
                                                            <div class="product-box product-box-compact">
                                                                <a class="product-box-img">
                                                                    <img
                                                                        src="{{ url('get_image_by_id/' . $cart->product->images->first()->id) ?? ""}}">
                                                                </a>
                                                                <div class="product-box-title">
                                                                    {{ $cart->product->product_name ?? ""}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <p>هیچ کالایی در سبد خرید شما وجود ندارد.</p>
                                            @endif

                                        </div>
                                    </div>
                                </section>

                                <div class="row">
                                    <div class="checkout-time-table checkout-time-table-time">
                                        <span class="checkout-additional-options-checkbox-image"></span>
                                        <div>
                                            <div class="checkout-time-table-title-bar checkout-time-table-title-bar-city">
                                                بازه تحویل سفارش: زمان تقریبی تحویل
                                                <span class="delivery_time">{{ $slaDays }}</span>
                                            </div>
                                            <ul class="checkout-time-table-subtitle-bar">
                                                <li>شیوه ارسال : {{$serviceName}} </li>
                                                <li>هزینه ارسال: <span style="color: #fb3449;"
                                                        class="price_send">{{ number_format($price_send ?? 0) }} ریال</span>
                                                </li>
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
                    <div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h6 class="modal-title" id="addAddressModalLabel">انتخاب آدرس</h6>
                                </div>
                                <div class="modal-body">
                                    @foreach ($addresses as $address)
                                        <div class="address-section address-modal" data-address-id="{{ $address->id ?? ""}}">
                                            <div class="checkout-contact">
                                                <div class="checkout-contact-content">
                                                    <ul class="checkout-contact-items">
                                                        <li class="checkout-contact-item">
                                                            گیرنده:
                                                            <span
                                                                class="full-name">{{ $address->recipient_first_name ?? ""}}</span>
                                                            <a class="checkout-contact-btn-edit">اصلاح این آدرس</a>
                                                        </li>
                                                        <li class="checkout-contact-item">
                                                            <div class="checkout-contact-item checkout-contact-item-mobile">
                                                                شماره تماس:
                                                                <span class="mobile-phone">{{ $address->mobile ?? ""}}</span>
                                                            </div>
                                                            <div class="checkout-contact-item-message">
                                                                کد پستی:
                                                                <span class="post-code">{{ $address->postal_code ?? ""}}</span>
                                                            </div>
                                                            <br>
                                                            استان
                                                            <span class="state">{{ $address->province->title ?? ""}}</span>
                                                            ، شهر
                                                            <span class="city">{{ $address->city->title ?? ""}}</span>
                                                            ،
                                                            <span class="address-part">{{ $address->province->title ?? ""}} -
                                                                {{ $address->city->title ?? ""}}</span>
                                                        </li>
                                                    </ul>
                                                    <div class="checkout-contact-badge" style="opacity: 0;">
                                                        <i class="now-ui-icons ui-1_check"></i>
                                                    </div>
                                                </div>
                                                <button href="#" class="checkout-contact-location " data-toggle="modal"
                                                    data-target="#addAddressModal" id="select_address">انتخاب </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- ستون کناری (خلاصه + دکمه) -->
                    <aside class="cart-page-aside col-xl-3 col-lg-4 col-md-6 center-section order-2">
                        <div class="checkout-aside">
                            <div class="checkout-summary">
                                <div class="checkout-summary-main">
                                    <ul class="checkout-summary-summary">
                                        <li><span>مبلغ کل ({{ $carts->count() ?? "" }} کالا)</span>
                                            <span>{{ number_format($totalPrice) ?? ""}} ریال</span>
                                        </li>
                                        <li>
                                            <span>هزینه ارسال</span>
                                            <span style="color: #fb3449;" class="price_send">{{ number_format($price_send)}}
                                                ریال</span>
                                        </li>
                                    </ul>

                                    <div class="checkout-summary-devider">
                                        <div></div>
                                    </div>

                                    <div class="checkout-summary-content">
                                        <!-- فرم اصلی ارسال داده‌ها -->
                                        <form method="POST" id="shipping-data-form" action="{{ route('cart_select_payment_type') }}"
                                            style="display: none;">
                                            @csrf

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

                        </div>
                    </aside>
                </div>
            </div>
        </main>
    </div>
@endsection

@section('script')
    <script>

        $(document).ready(function () {
            let selectedAddressId = "{{ $default_address->id }}";
            $('#selected_address_id').val(selectedAddressId);

            $('input[name="shipping_method"]').on('change', function () {
                $('#hidden_shipping_method').val($(this).val());
            });

            $('#agree').on('change', function () {
                $('#hidden_request_invoice').val($(this).is(':checked') ? '1' : '0');
            });

            $('.address-modal #select_address').on('click', function (e) {
                e.preventDefault();

                let addressSection = $(this).closest('.address-section');
                let addressId = addressSection.data('address-id');

                let fullName = addressSection.find('.full-name').text();
                let mobile = addressSection.find('.mobile-phone').text();
                let postCode = addressSection.find('.post-code').text();
                let state = addressSection.find('.state').text();
                let city = addressSection.find('.city').text();
                let addressPart = addressSection.find('.address-part').text();

                let mainAddressSection = $('.cart-page-content .address-section').first();
                mainAddressSection.attr('data-address-id', addressId);
                mainAddressSection.find('.full-name').text(fullName);
                mainAddressSection.find('.mobile-phone').text(mobile);
                mainAddressSection.find('.post-code').text(postCode);
                mainAddressSection.find('.state').text(state);
                mainAddressSection.find('.city').text(city);
                mainAddressSection.find('.address-part').text(addressPart);

                selectedAddressId = addressId;
                $('#selected_address_id').val(addressId);

                $('#addAddressModal').modal('hide');

                $.ajax({
                    url: '{{ route('ajax_change_address_default_cart') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        addressId: selectedAddressId
                    },
                    success: function (response) {
                        console.log(response);

                        $('.price_send').text(Number(response.price_send).toLocaleString('fa-IR') + " ریال");
                        $('.checkout-summary-price-value-amount').text(Number(response.amountPayable).toLocaleString('fa-IR'));
                        let deliveryText = response.delivery_time; // مثلاً "۲ تا ۴ روز کاری"

                        $('.delivery_time').text(deliveryText);
                        Swal.fire({
                            icon: 'success',
                            title: 'موفق',
                            text: 'آدرس با موفقیت تغییر یافت',
                            showConfirmButton: false,
                            timer: 1500,
                            toast: true,
                            position: 'top-end'
                        });
                    },
                    error: function (xhr) {
                        showToast('error', 'خطایی رخ داد. لطفا دوباره تلاش کنید.');
                    }
                });


            });

            $('#shipping-data-form').on('submit', function (e) {
                if (!selectedAddressId) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'SHILIRAN',
                        text: "لطفا یک آدرس برای ارسال محصول انتخاب نمایید",
                        showConfirmButton: true,
                        confirmButtonText: 'متوجه شدم',
                        timer: 3000
                    });
                    return false;
                }
            });

            @if ($errors->any())
                let errorMessages = [
                    @foreach ($errors->all() as $error)
                        "{{ addslashes($error) }}",
                    @endforeach
                                                        ];

                Swal.fire({
                    icon: 'error',
                    title: 'SHILIRAN',
                    html: '<ul style="text-align: right; direction: rtl; margin: 0; padding-right: 20px; list-style: persian;">' +
                        errorMessages.map(msg => `<li style="margin-right: 3rem;">${msg}</li>`).join('') +
                        '</ul>',
                    showConfirmButton: true,
                    confirmButtonText: 'متوجه شدم',
                    timer: 5000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'animated fadeIn',
                        confirmButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                });
            @endif
                        });
    </script>
@endsection
