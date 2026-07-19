@extends('Customer.Layout.master')

@section('content')
    <main class="cart-page default">
        <div class="container">
            <div class="row">
                <div class="cart-page-content col-12 order-1">
                    <section class="page-content default">
                        <div class="success-checkout text-center default">
                            <div class="icon-success">
                                @if ($isSuccess === true)
                                    <i class="fa fa-check"></i>
                                @elseif ($isSuccess === false)
                                    <i class="fa fa-times"></i>
                                @else
                                    <i class="fa fa-info-circle"></i>
                                @endif
                            </div>

                            <h1>
                                {{ $isSuccess === true ? 'نتیجه پرداخت: موفق' : ($isSuccess === false ? 'نتیجه پرداخت: ناموفق' : 'نتیجه پرداخت') }}
                            </h1>
                            <p>{{ $message }}</p>
                        </div>

                        <div class="order-info default text-center">
                            @if ($order)
                                <h3>کد سفارش: <span>{{ $order->code }}</span></h3>
                                <p>
                                    وضعیت سفارش:
                                    <span class="badge {{ $isSuccess === true ? 'badge-success' : ($isSuccess === false ? 'badge-danger' : 'badge-secondary') }}">
                                        {{ $order->status_title }}
                                    </span>
                                </p>
                            @endif

                            <div class="d-flex justify-content-center" style="gap: 10px; flex-wrap: wrap;">
                                @if ($order && auth()->check())
                                    <a href="{{ route('order_detail', ['order' => $order->id]) }}" class="btn btn-primary">
                                        مشاهده جزئیات سفارش
                                    </a>
                                @endif

                                <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                                    بازگشت به صفحه اصلی
                                </a>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </main>
@endsection
