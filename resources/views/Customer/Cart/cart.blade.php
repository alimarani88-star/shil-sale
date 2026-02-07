@extends('Customer.Layout.master')

@section('content')
    <style>
        .cart-item-discount {
            display: inline-block;
            background: #6a1b9a;
            color: white;
            font-size: 0.75rem;
            padding: 2px 6px;
            border-radius: 4px;
            margin-left: 4px;
        }

        /* استایل عمومی کارت در موبایل */
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

            .cart-table-desktop {
                display: none !important;
            }

            .cart-card-mobile {
                display: block !important;
            }

            .cart-item-card {
                background: #fff;
                border: 1px solid #eee;
                border-radius: 12px;
                padding: 1rem;
                margin-bottom: 1rem;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                position: relative;
            }

            .cart-item-card img {
                width: 80px;
                height: 80px;
                object-fit: cover;
                border-radius: 8px;
                border: 1px solid #f0f0f0;
            }

            .cart-item-header {
                display: flex;
                align-items: flex-start;
                gap: 12px;
                margin-bottom: 12px;
            }

            .cart-item-title {
                font-size: 1rem;
                font-weight: 600;
                color: #2c3e50;
                margin: 0;
                flex: 1;
            }



            .cart-item-price-section {
                margin-top: 12px;
                font-size: 0.95rem;
            }

            .price-del {
                color: #6a1b9a;
                font-weight: 500;
            }

            .price-product {
                font-weight: 600;
                font-size: 1.1rem;
            }

            .cart-item-qty {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-top: 12px;
                gap: 8px;
            }

            .qty-btn {
                width: 36px;
                height: 36px;
                border-radius: 8px;
                font-size: 1.2rem;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .product-qty {
                width: 60px !important;
                text-align: center;
                border-radius: 8px;
            }

            .checkout-btn-remove {
                position: absolute;
                top: 12px;
                left: 12px;
                background: #fff;
                border: 1px solid #ddd;
                width: 32px;
                height: 32px;
                border-radius: 50%;
                cursor: pointer;
                font-size: 1.2rem;
                color: #999;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .checkout-btn-remove:hover {
                background: #f8f9fa;
                color: #6a1b9a;
                border-color: #6a1b9a;
            }


        }

        /* دسکتاپ: مخفی کردن کارت */
        @media (min-width: 769px) {
            .cart-card-mobile {
                display: none !important;
            }
        }
    </style>


    <div class="wrapper default shopping-page">

        <!-- main -->
        <main class="cart-page default">
            <div class="container">
                @if($carts->count() > 0)
                    <div class="row">
                        <div class="cart-page-content col-xl-9 col-lg-8 col-md-12 order-1">
                            <div class="cart-page-title text-md-start">
                                <h1>سبد خرید</h1>
                            </div>

                            <!-- نسخه دسکتاپ: جدول -->
                            <div class="table-responsive checkout-content default cart-table-desktop">
                                <table class="table">
                                    <tbody>
                                        @foreach($carts as $cart)
                                            <tr class="checkout-item" data-id="{{ $cart->product_id }}">
                                                <td>
                                                    <img style="width: 6rem;"
                                                        src="{{ url('get_image_by_id/' . $cart->product->images->first()->id) }}"
                                                        alt="">
                                                    <br><br>
                                                    @if ($cart->discount_name)
                                                        <span class="cart-item-discount">
                                                            {{ $cart->discount_name }} {{ $cart->percentage }}
                                                        </span>
                                                    @endif

                                                    <button class="checkout-btn-remove" data-id="{{ $cart->product_id }}"></button>
                                                </td>
                                                <td>
                                                    <h3 class="checkout-title">{{ $cart->product->product_name }}</h3>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-secondary qty-btn qty-decrease">−</button>
                                                        <input type="number" min="1" max="10000" value="{{ $cart->count ?? 1 }}"
                                                            class="form-control text-center mx-2 product-qty" style="width:80px;">
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-secondary qty-btn qty-increase custom-primary">+</button>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="price_del" style="font-weight: 500; color: #fb3449;">
                                                        {{ number_format($cart->totalDiscount) }} ریال تخفیف
                                                    </span>
                                                    <br>
                                                    <span
                                                        class="price_product">{{ number_format($cart->productPrice * $cart->count) }}</span>
                                                    ریال
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- نسخه موبایل: کارت -->
                            <div class="cart-card-mobile" style="display: none;">
                                @foreach($carts as $cart)
                                    <div class="cart-item-card" data-id="{{ $cart->product_id }}">
                                        <!-- دکمه حذف -->
                                        <button class="checkout-btn-remove" data-id="{{ $cart->product_id }}"></button>

                                        <div class="cart-item-header">
                                            <img src="{{ url('get_image_by_id/' . $cart->product->images->first()->id) }}" alt="">
                                            <div style="flex: 1;">
                                                <h3 class="cart-item-title">{{ $cart->product->product_name }}</h3>
                                                @if($cart->discount_name)
                                                    <span class="cart-item-discount">
                                                        {{ $cart->discount_name }} {{ $cart->percentage }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="cart-item-price-section">
                                            <div class="row">
                                                <div class="col-7">
                                                    @if($cart->totalDiscount > 0)
                                                        <div class="price-del" style="color: #fb3449;">
                                                            {{ number_format($cart->totalDiscount) }} ریال تخفیف
                                                        </div>
                                                    @endif
                                                    <div class="price-product">
                                                        {{ number_format($cart->productPrice * $cart->count) }} ریال
                                                    </div>
                                                </div>
                                                <!-- <div class="cart-item-qty"> -->
                                                <div class="col-5">
                                                    <div class="row" style="align-items: center;">
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-secondary qty-btn qty-decrease">−</button>
                                                        <input type="number" min="1" max="10000" value="{{ $cart->count ?? 1 }}"
                                                            class="form-control text-center product-qty">
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-secondary qty-btn qty-increase custom-primary">+</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- </div> -->
                                        </div>


                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <aside class="cart-page-aside col-xl-3 col-lg-4 col-md-6 center-section order-2">
                            <div class="checkout-aside">
                                <div class="checkout-summary">
                                    <div class="checkout-summary-main">
                                        <ul class="checkout-summary-summary">
                                            <li><span>مبلغ کل</span><span class="total_price"
                                                    style="margin-left: 0.3rem;">{{ number_format($totalPrice) }}
                                                </span>ریال
                                            <li><span>سود شما از خرید</span><span class="total_price_profit"
                                                    style="color: red; margin-left: 0.3rem;">{{ number_format($totalProfit) }}
                                                </span>ریال
                                            </li>

                                        </ul>
                                        <div class="checkout-summary-devider">
                                            <div></div>
                                        </div>
                                        <div class="checkout-summary-content">
                                            <div class="checkout-summary-price-title">مبلغ قابل پرداخت:</div>
                                            <div class="checkout-summary-price-value">
                                                <span
                                                    class="checkout-summary-price-value-amount AmountPayable">{{number_format($amountPayable)}}</span>
                                                ریال
                                            </div>
                                            <a href="{{ route('cart_select_address') }}" class="selenium-next-step-shipping">
                                                <div class="parent-btn">
                                                    <button class="dk-btn custom-primary" style="font-size: 16px;">
                                                        ادامه ثبت سفارش
                                                        <i class="now-ui-icons shopping_basket"></i>
                                                    </button>
                                                </div>
                                            </a>
                                            <div>
                                                <span>
                                                    کالاهای موجود در سبد شما ثبت و رزرو نشده‌اند، برای ثبت سفارش مراحل بعدی
                                                    را تکمیل
                                                    کنید.
                                                </span>
                                                <span class="wiki wiki-holder"><span class="wiki-sign"></span>
                                                    <div class="wiki-container is-right">
                                                        <div class="wiki-arrow"></div>
                                                        <p class="wiki-text">
                                                            محصولات موجود در سبد خرید شما تنها در صورت ثبت و پرداخت سفارش
                                                            برای شما رزرو
                                                            می‌شوند. در
                                                            صورت عدم ثبت سفارش، تاپ کالا هیچگونه مسئولیتی در قبال تغییر
                                                            قیمت یا موجودی
                                                            این کالاها
                                                            ندارد.
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
                @else
                    <main class="cart default">
                        <div class="container text-center">
                            <div class="cart-empty">
                                <div class="cart-empty-icon">
                                    <i class="now-ui-icons shopping_basket"></i>
                                </div>
                                <div class="cart-empty-title">سبد خرید شما خالیست!</div>
                                <div class="parent-btn">
                                    <a href="{{ route('home') }}" class="dk-btn dk custom-primary">
                                        بازگشت به صفحه محولات
                                        <i class="fa fa-sign-in"></i>
                                    </a>
                                </div>

                            </div>
                        </div>
                    </main>
                @endif

            </div>
        </main>
    </div>




@endsection

@section('script')

    <script>
        $(document).on('click', '.checkout-btn-remove', function () {
            const productId = $(this).data('id');
            const container = $(this).closest('tr, .cart-item-card'); // هم tr و هم div

            $.ajax({
                url: '{{ route("remove_from_cart") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: productId
                },
                success: function (result) {
                    if (result.status === 'success') {
                        console.log(result);
                        container.remove();
                        let totalPriceFormated = result.data.totalPrice.toLocaleString('en-US');
                        let AmountPayableFormated = result.data.AmountPayable.toLocaleString('en-US');
                        $('.total_price').text(totalPriceFormated);
                        $('.AmountPayable').text(AmountPayableFormated);
                        $('.total_price_profit').text(result.data.totalProfit);
                        updateCartHeader();
                    }
                },
                error: function (xhr) {
                    console.error('خطا در حذف محصول', xhr);
                }
            });
        });

        $(document).on('click', '.qty-increase', function () {
            const container = $(this).closest('tr, .cart-item-card'); // هم tr و هم div
            const input = container.find('.product-qty');
            const productId = container.data('id');

            let current = parseInt(input.val()) || 1;
            input.val(current + 1);
            updateCartQuantity(productId, current + 1);
        });

        $(document).on('click', '.qty-decrease', function () {
            const container = $(this).closest('tr, .cart-item-card'); // هم tr و هم div
            const input = container.find('.product-qty');
            const productId = container.data('id');

            let current = parseInt(input.val()) || 1;
            if (current > 1) {
                input.val(current - 1);
                updateCartQuantity(productId, current - 1);
            }
        });

        $(document).on('blur', '.product-qty', function () {
            const container = $(this).closest('tr, .cart-item-card'); // هم tr و هم div
            const productId = container.data('id');
            const qty = parseInt($(this).val()) || 1;
            updateCartQuantity(productId, qty);
        });

        function updateCartQuantity(productId, qty) {
            $.ajax({
                url: '{{ route("update_count_product_cart") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: productId,
                    quantity: qty
                },
                success: function (result) {
                    if (result.status === 'success') {
                        const data = result.data;
                        const cart = data.cart;

                        // آپدیت هم نسخه دسکتاپ و هم موبایل
                        const containers = $(`tr[data-id="${cart.product_id}"], .cart-item-card[data-id="${cart.product_id}"]`);

                        containers.each(function () {
                            const container = $(this);

                            if (cart.percentage && cart.percentage !== "0%") {
                                container.find('span[style*="color:red"]').eq(1).text(cart.percentage);
                            }

                            container.find('.price_del, .price-del').text(`${cart.totalDiscount.toLocaleString('en-US')} ریال تخفیف`);
                            container.find('.price_product, .price-product').text(`${(cart.product_price * qty).toLocaleString('en-US')}`);
                        });

                        $('.total_price').text(data.totalPrice.toLocaleString('en-US'));
                        $('.total_price_profit').text(data.totalProfit.toLocaleString('en-US'));
                        $('.AmountPayable').text(data.AmountPayable.toLocaleString('en-US'));

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: result.message,
                            showConfirmButton: false,
                            timer: 1500
                        });

                        if (typeof updateCartHeader === 'function') updateCartHeader();
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
    </script>
@endsection