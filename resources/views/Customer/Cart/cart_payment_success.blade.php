@extends('Customer.Layout.master')

@section('content')


 <!-- main-shopping -->
        <main class="cart-page default">
            <div class="container">
                <div class="row">
                    <div class="cart-page-content col-12 order-1">
                        <section class="page-content default">
                            <div class="success-checkout text-center default">
                                <div class="icon-success">
                                    <i class="fa fa-check"></i>
                                </div>
                                <h1>سفارش <a href="#">DKC-22222222</a>با موفقیت در سیستم ثبت شد.</h1>
                                <p>سفارش نهایتا تا یک روز آماده ارسال خواهد شد.</p>
                            </div>
                            <div class="order-info default">
                                <h3>کد سفارش: <span>DKC-22222222</span></h3>
                                <p>سفارش شما با موفقیت در سیستم ثبت شد و هم اکنون <span
                                        class="badge badge-success">تکمیل شده</span> است.جزئیات این سفارش را می توانید
                                    با کلیک بر روی دکمه <a href="#" class="btn-link-border">پیگیری سفارش</a>مشاهده
                                    نمایید.</p>
                                <button type="button" class="btn-primary">
                                    پیگیری سفارش
                                </button>
                                <div class="table-responsive default mt-3 mb-3">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col">نام تحویل گیرنده : جلال بهرامی راد</th>
                                                <th scope="col">شماره تماس : 09030000000</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th scope="row">تعداد مرسوله : 1</th>
                                                <td>مبلغ کل : 15390000</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">وضعیت پرداخت : پرداخت آنلاین(موفق)</th>
                                                <td>وضعیت سفارش: در انتظار پرداخت</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">آدرس : استان خراسان شمالی،بجنورد</th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </main>
        <!-- main-shopping -->


@endsection