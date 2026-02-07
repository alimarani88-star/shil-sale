@extends('Customer.Layout.master')

@section('content')
    <style>
        /* استایل‌های پایه */
        .cart-page {
            background: #f8f9fa;
            padding: 1rem 0;
        }

        .cart-page-title h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2c3e50;
        }

        /* استایل‌های موبایل */
        @media (max-width: 768px) {
            .cart-page {
                padding: 0.5rem 0;
            }
            

            .container {
                padding: 0;
            }

            /* عنوان صفحه */
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

            /* روش پرداخت */
            .checkout-paymethod {
                padding: 0;
                margin: 0 0 0.75rem 0;
            }

            .checkout-paymethod li {
                list-style: none;
            }

            .checkout-paymethod-item {
                background: #fff;
                border: 2px solid #6a1b9a;
                border-radius: 12px;
                padding: 1rem !important;
                margin: 0 1rem;
            }

            .checkout-paymethod-title {
                margin: 0;
            }

            .checkout-paymethod-title-label {
                font-size: 0.95rem !important;
                font-weight: 600;
                color: #2c3e50;
                margin-bottom: 0.5rem;
            }

            .checkout-paymethod-title span {
                font-size: 0.8rem !important;
                color: #6a1b9a;
                font-weight: 500;
            }

            .radio input[type="radio"] {
                width: 20px;
                height: 20px;
            }

            /* خلاصه سفارش */
            .headline {
                background: #fff;
                padding: 1rem;
                margin: 0 0 0.75rem 0;
                border-bottom: 1px solid #eee;
            }

            .headline span {
                font-size: 1rem;
                font-weight: 600;
                color: #2c3e50;
            }

            /* آکاردیون سفارش */
            .checkout-order-summary {
                margin: 0 1rem 0.75rem;
            }

            .card {
                border: none;
                border-radius: 12px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                overflow: hidden;
            }

            .card-header {
                background: #fff;
                border: none;
                padding: 0;
            }

            .checkout-order-summary-header .btn {
                width: 100%;
                text-align: right;
                padding: 1rem;
                color: #2c3e50;
                text-decoration: none;
            }

            .checkout-order-summary-header .btn:hover {
                text-decoration: none;
            }

            .checkout-order-summary-row {
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
                width: 100%;
            }

            .checkout-order-summary-col {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.5rem 0;
                border-bottom: 1px solid #f5f5f5;
                font-size: 0.9rem;
            }

            .checkout-order-summary-col:last-child {
                border-bottom: none;
            }

            .checkout-order-summary-col span:first-child {
                color: #6c757d;
                font-size: 0.85rem;
            }

            .checkout-order-summary-col span:last-child,
            .checkout-order-summary-col .fs-sm {
                color: #2c3e50;
                font-weight: 600;
                font-size: 0.9rem;
            }

            .dl-none-sm {
                display: block;
            }

            .icon-down {
                position: absolute;
                left: 1rem;
                top: 50%;
                transform: translateY(-50%);
                font-size: 1.2rem;
                color: #6c757d;
            }

            /* محصولات */
            .card-body {
                background: #f8f9fa;
                padding: 1rem !important;
            }

            .product-box-container {
                margin-bottom: 0.75rem;
            }

            .product-box {
                background: #fff;
                border-radius: 12px;
                padding: 1rem;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
                transition: transform 0.2s;
            }

            .product-box:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }

            .product-box-img {
                display: block;
                text-align: center;
                margin-bottom: 0.75rem;
            }

            .product-box-img img {
                width: 100%;
                height: 120px;
                object-fit: contain;
                border-radius: 8px;
            }

            .product-box-title {
                font-size: 0.85rem;
                line-height: 1.5;
                color: #2c3e50;
                text-align: center;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            /* کد تخفیف */
            .checkout-price-options {
                background: #fff;
                border-radius: 12px;
                margin: 0 1rem 0.75rem;
                padding: 1rem;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            }

            .checkout-price-options-header {
                margin-bottom: 0.75rem;
            }

            .checkout-price-options-header span {
                font-size: 1rem;
                font-weight: 600;
                color: #2c3e50;
            }

            .checkout-price-options-description {
                display: none;
            }

            .checkout-price-options-row {
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
            }

            .checkout-price-options-form-field {
                width: 100% !important;
                margin: 0 !important;
            }

            .ui-input-field {
                width: 100%;
                padding: 0.875rem;
                border: 2px solid #e9ecef;
                border-radius: 10px;
                font-size: 0.95rem;
                transition: all 0.3s;
            }

            .ui-input-field:focus {
                outline: none;
                border-color: #6a1b9a;
                box-shadow: 0 0 0 4px rgba(106, 27, 154, 0.1);
            }

            .checkout-price-options-form-button {
                width: 100% !important;
            }

            .checkout-price-options-form-button button {
                width: 100%;
                padding: 0.875rem;
                border-radius: 10px;
                font-weight: 600;
                font-size: 0.95rem;
            }

            /* سایدبار */
            .cart-page-aside {
                order: 3 !important;
                padding: 0 1rem;
            }

            .checkout-aside {
                position: static !important;
                margin-bottom: 1rem;
            }

            .checkout-summary {
                background: #fff;
                border-radius: 12px;
                padding: 1rem;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                margin-bottom: 0.75rem;
            }

            .checkout-summary-summary {
                padding: 0;
                margin: 0 0 1rem 0;
            }

            .checkout-summary-summary li {
                display: flex;
                justify-content: space-between;
                padding: 0.75rem 0;
                border-bottom: 1px solid #f5f5f5;
                font-size: 0.9rem;
                list-style: none;
            }

            .checkout-summary-summary li:last-child {
                border-bottom: none;
            }

            .checkout-summary-summary li span:first-child {
                color: #6c757d;
            }

            .checkout-summary-summary li span:last-child {
                color: #2c3e50;
                font-weight: 600;
            }

            .checkout-summary-devider {
                margin: 1rem 0;
                height: 1px;
                background: linear-gradient(to right, transparent, #ddd, transparent);
            }

            .checkout-summary-price-title {
                font-size: 1rem;
                color: #6c757d;
                margin-bottom: 0.5rem;
            }

            .checkout-summary-price-value {
                font-size: 1.5rem;
                font-weight: 700;
                color: #6a1b9a;
                margin-bottom: 1rem;
            }

            .dk-btn {
                width: 100%;
                padding: 1rem;
                font-size: 1rem !important;
                font-weight: 600;
                border-radius: 10px;
                border: none;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
            }

            .dk-btn i {
                font-size: 1.2rem;
            }

            .checkout-summary-content>div:last-child {
                margin-top: 1rem;
                padding-top: 1rem;
                border-top: 1px solid #f5f5f5;
            }

            .checkout-summary-content>div:last-child span {
                font-size: 0.8rem;
                color: #6c757d;
                line-height: 1.6;
            }

            /* ویژگی‌ها */
            .checkout-feature-aside {
                background: #fff;
                border-radius: 12px;
                padding: 1rem;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            }

            .checkout-feature-aside ul {
                padding: 0;
                margin: 0;
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
            }

            .checkout-feature-aside-item {
                list-style: none;
                padding: 0.75rem 1rem 0.75rem 2.5rem !important;
                background: #f8f9fa;
                border-radius: 8px;
                font-size: 0.85rem;
                color: #2c3e50;
                position: relative;
            }

            .checkout-feature-aside-item::before {
                content: "✓";
                position: absolute;
                right: 0.75rem;
                top: 50%;
                transform: translateY(-50%);
                width: 24px;
                height: 24px;
                background: #6a1b9a;
                color: white;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.85rem;
                font-weight: bold;
            }

            /* ویکی */
            .wiki-holder {
                display: inline-block;
                margin-right: 0.25rem;
            }

            .wiki-sign {
                display: inline-block;
                width: 16px;
                height: 16px;
                background: #6c757d;
                color: white;
                border-radius: 50%;
                text-align: center;
                line-height: 16px;
                font-size: 0.7rem;
                cursor: pointer;
            }

            .wiki-sign::after {
                content: "؟";
            }

            .wiki-container {
                display: none;
            }

            .wiki-holder:hover .wiki-container {
                display: block;
                position: fixed;
                left: 1rem;
                right: 1rem;
                bottom: 5rem;
                background: white;
                border-radius: 12px;
                padding: 1rem;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
                z-index: 1000;
            }

            .wiki-text {
                font-size: 0.85rem;
                line-height: 1.6;
                color: #495057;
                margin: 0;
            }

            /* حذف padding اضافی */
            .page-content {
                padding: 0 !important;
            }

            .row {
                margin: 0;
            }

            [class*="col-"] {
                padding: 0;
            }
        }

        /* دسکتاپ */
        @media (min-width: 769px) {
            .checkout-price-options-description {
                display: block;
            }

            .ui-input-field {
                padding: 0.75rem;
                border: 1px solid #ddd;
                border-radius: 8px;
                font-size: 1rem;
            }

            .ui-input-field:focus {
                outline: none;
                border-color: #6a1b9a;
                box-shadow: 0 0 0 3px rgba(106, 27, 154, 0.1);
            }
        }

        /* تبلت */
        @media (min-width: 769px) and (max-width: 1077px) {
            .checkout-price-options-description {
                display: none;
            }
        }
    </style>
    <div class="wrapper default shopping-page">


        <main class="cart-page default">
            <div class="container">
                <div class="row">
                    <div class="cart-page-content col-xl-9 col-lg-8 col-md-12 order-1">
                        <div class="cart-page-title">
                            <h1>انتخاب شیوه پرداخت</h1>
                        </div>
                        <section class="page-content default">
                            <form action="">
                                <ul class="checkout-paymethod">
                                    <li>
                                        <div class="checkout-paymethod-item checkout-paymethod-item-cc has-options">
                                            <div class="radio">
                                                <input type="radio" name="radio" id="radio1" value="option1" checked>
                                                <label for="radio1">
                                                    <div>
                                                        <h4 class="checkout-paymethod-title">
                                                            <div>
                                                                <p class="checkout-paymethod-title-label">
                                                                    پرداخت اینترنتی ( آنلاین با تمامی کارت‌های بانکی )
                                                                </p>
                                                            </div>
                                                            <span>سرعت بیشتر در ارسال و پردازش سفارش </span>
                                                        </h4>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </form>

                            <div class="row">
                                <div class="col-sm-6 col-12">
                                    <div class="checkout-price-options">
                                        <div class="checkout-price-options-form">
                                            <section class="checkout-price-options-container">
                                                <div class="checkout-price-options-header">
                                                    <span>استفاده از کد تخفیف </span>
                                                </div>
                                                <div class="checkout-price-options-content">
                                                    <p class="checkout-price-options-description">
                                                        با ثبت کد تخفیف، مبلغ کد تخفیف از "مبلغ قابل پرداخت" کسر می‌شود.
                                                    </p>
                                                    <div class="checkout-price-options-row">
                                                        <div class="checkout-price-options-form-field">
                                                            <label class="ui-input">
                                                                <input class="ui-input-field" value="" name="code"
                                                                    type="text" placeholder="مثلا 837A2CS">
                                                            </label>
                                                        </div>
                                                        <div class="checkout-price-options-form-button">
                                                            <button type="button" class="btn custom-primary">
                                                                ثبت کد تخفیف
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                    <aside class="cart-page-aside col-xl-3 col-lg-4 col-md-6 center-section order-2">
                        <div class="checkout-aside">
                            <div class="checkout-summary">
                                <div class="checkout-summary-main">
                                    <ul class="checkout-summary-summary">
                                        <li>
                                            <span>مبلغ کل ({{count($carts)}} کالا)</span>
                                            <span>{{ number_format($amountPayable ?? 0) }} ریال</span>
                                        </li>
                           
                                    </ul>
                                    <div class="checkout-summary-devider">
                                        <div></div>
                                    </div>
                                    <div class="checkout-summary-content">
                                        <div class="checkout-summary-price-title">مبلغ قابل پرداخت:</div>
                                        <div class="checkout-summary-price-value">
                                            <span
                                                class="checkout-summary-price-value-amount">{{ number_format($amountPayable ?? 0) }}</span>
                                            ریال
                                        </div>
                                        <a href="{{ route('cart_payment') }}" class="selenium-next-step-shipping">
                                            <div class="parent-btn">
                                                <button class="dk-btn custom-primary" style="font-size: 16px;">
                                                    <i class="now-ui-icons shopping_basket"></i>
                                                    ادامه ثبت سفارش
                                                </button>
                                            </div>
                                        </a>
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
                                    <li class="checkout-feature-aside-item checkout-feature-aside-item-guarantee">
                                        هفت روز ضمانت تعویض
                                    </li>
                                    <li class="checkout-feature-aside-item checkout-feature-aside-item-cash">
                                        پرداخت در محل با کارت بانکی
                                    </li>
                                    <li class="checkout-feature-aside-item checkout-feature-aside-item-express">
                                        تحویل اکسپرس
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