@extends('Customer.Layout.master')

@section('content')

    <main class="profile-user-page default">
        <div class="container">
            <div class="row">

                <div class="cart-page-content col-xl-12 col-lg-8 col-md-12 order-1">
                    <div class="cart-page-title">
                        <h1>جزییات سفارش {{ $order->code }}</h1>
                    </div>
                    <div class="row" style="background: #eceff1; padding: 20px; border-radius: 13px;">
                        <div class="col-md-4 mb-2">
                            <div class="d-flex align-items-center justify-content-between">
                                <p class="mb-0">وضعیت سفارش:
                                    @if((int) $order->status === \App\Models\Order::STATUS_PENDING_PAYMENT)
                                        <span class="btn btn-warning btn-sm mb-0">{{ $order->resolvedStatusTitle() }}</span>
                                    @elseif((int) $order->status === \App\Models\Order::STATUS_PAYMENT_CONFIRMED)
                                        <span class="btn btn-danger btn-sm mb-0">{{ $order->resolvedStatusTitle() }}</span>
                                    @elseif((int) $order->status === \App\Models\Order::STATUS_PAID)
                                        <span class="btn btn-success btn-sm mb-0">{{ $order->resolvedStatusTitle() }}</span>
                                    @elseif((int) $order->status === \App\Models\Order::STATUS_INVOICED)
                                        <span class="btn btn-primary btn-sm mb-0">{{ $order->resolvedStatusTitle() }}</span>
                                    @elseif((int) $order->status === \App\Models\Order::STATUS_SHIPPED)
                                        <span class="btn btn-primary btn-sm mb-0">{{ $order->resolvedStatusTitle() }}</span>
                                    @elseif((int) $order->status === \App\Models\Order::STATUS_PAYMENT_EXPIRED)
                                        <span class="btn btn-danger btn-sm mb-0">{{ $order->resolvedStatusTitle() }}</span>
                                    @elseif((int) $order->status === \App\Models\Order::STATUS_CANCELLED_TO_WALLET)
                                        <span class="btn btn-danger btn-sm mb-0">{{ $order->resolvedStatusTitle() }}</span>
                                    @else
                                        <span class="btn btn-secondary btn-sm mb-0">{{ $order->resolvedStatusTitle() }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>


                        <div class="col-md-3 mb-2">
                            <p>تاریخ سفارش: {{ $order->created_at_jalali }}</p>
                        </div>


                        <div class="col-md-3 mb-2">
                            <p>مبلغ کل: {{ number_format($order->total_price) }} ریال</p>
                        </div>
                        @if((int) $order->wallet_used_amount > 0)
                            <div class="col-md-3 mb-2">
                                <p>پرداخت از کیف پول: {{ number_format($order->wallet_used_amount) }} ریال</p>
                            </div>
                        @endif

                        <div class="col-md-3 mb-2">
                            <p>تحویل گیرنده:
                                {{ $order->address->recipient_first_name . " " . $order->address->recipient_last_name }}
                            </p>
                        </div>
                        <div class="col-md-7 mb-2">
                            <p>آدرس: {{ $order->address->address}}</p>
                        </div>
                    </div>

                    <div class="row" style="background: #eceff1; padding: 20px; border-radius: 13px; margin-top: 1rem;">
                        <div class="col-12">

                            <div class="headline">
                                <span>خلاصه سفارش</span>
                            </div>
                            <div class="checkout-order-summary">
                                <div class="accordion checkout-order-summary-item" id="accordionExample">
                                    <div class="card">
                                        <div class="card-header checkout-order-summary-header" id="headingOne">
                                            <h2 class="mb-0">
                                                <button class="btn btn-link" type="button" data-toggle="collapse"
                                                    data-target="#collapseOne" aria-expanded="false"
                                                    aria-controls="collapseOne">
                                                    <div class="checkout-order-summary-row">
                                                        <div
                                                            class="checkout-order-summary-col checkout-order-summary-col-post-time">
                                                            مرسوله ۱
                                                            از ۱
                                                            <span class="fs-sm">({{ count($orderItems) }} کالا)</span>
                                                        </div>
                                                        <div
                                                            class="checkout-order-summary-col checkout-order-summary-col-how-to-send">
                                                            <span class="dl-none-sm">نحوه ارسال</span>
                                                            <span class="dl-none-sm">
                                                                {{ $order->send_type }}
                                                            </span>
                                                        </div>
                                                        <div
                                                            class="checkout-order-summary-col checkout-order-summary-col-how-to-send">
                                                            <span>ارسال از</span>
                                                            <span class="fs-sm">
                                                                {{ $order->send_time }}
                                                            </span>
                                                        </div>
                                                        <div
                                                            class="checkout-order-summary-col checkout-order-summary-col-shipping-cost">
                                                            <span>هزینه ارسال</span>
                                                            <span class="fs-sm">
                                                                {{ number_format($order->send_price) }} ریال
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <i class="now-ui-icons arrows-1_minimal-down icon-down"></i>
                                                </button>
                                            </h2>
                                        </div>

                                        <div id="collapseOne" class="collapse" aria-labelledby="headingOne"
                                            data-parent="#accordionExample">
                                            <div class="card-body">

                                                <div class="table-responsive">
                                                    <table class="table table-bordered text-center align-middle">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>تصویر</th>
                                                                <th>نام محصول</th>
                                                                <th>تعداد</th>
                                                                <th>مقدار تخفیف</th>
                                                                <th>مبلغ( با تخفیف)</th>
                                                                <th>قیمت محصول</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($orderItems as $item)
                                                                <tr>
                                                                    <td>
                                                                        <img src="{{ url('get_image_by_id/' . $item->product->images->first()->id) ?? '' }}"
                                                                            alt="تصویر محصول" width="60">
                                                                    </td>
                                                                    <td>{{ $item->product->product_name }}</td>
                                                                    <td>{{ $item->count }}</td>
                                                                    <td style="color: red;">{{ $item->discount ?? 0 }}%</td>
                                                                    <td style="color: red;">{{ number_format($item->product_price_percent * $item->count) }}
                                                                        ریال</td>
                                                                    <td>{{ number_format($item->price * $item->count) }}
                                                                        ریال</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 d-flex flex-wrap" style="gap: 8px;">
                        @if($order->isPendingPayment())
                            <a href="{{ route('cart_payment', ['order' => $order->id]) }}"
                               class="btn js-disable-after-click"
                               style="background-color: #3E5F44; color: #fff;">
                                پرداخت سفارش
                            </a>
                        @endif

                        @if($order->canBeCancelledByCustomer())
                            <form id="cancel-order-form" method="POST" action="{{ route('cancel_order') }}">
                                @csrf
                                <input type="hidden" name="order" value="{{ $order->id }}">
                                <button type="button" class="btn btn-danger js-cancel-order">لغو سفارش</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('script')
    <script>
        (function () {
            function disablePayLink(link) {
                if (!link || link.getAttribute('aria-disabled') === 'true') {
                    return;
                }
                link.setAttribute('aria-disabled', 'true');
                link.style.pointerEvents = 'none';
                link.style.opacity = '0.65';
            }

            document.querySelectorAll('.js-disable-after-click').forEach(function (el) {
                el.addEventListener('click', function () {
                    disablePayLink(el);
                });
            });

            document.querySelectorAll('.js-cancel-order').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    Swal.fire({
                        text: "با لغو سفارش، مبلغ پرداخت‌شده به کیف پول شما برمی‌گردد. ادامه می‌دهید؟",
                        showCancelButton: true,
                        confirmButtonText: "بله، لغو شود",
                        cancelButtonText: "خیر",
                        reverseButtons: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#777"
                    }).then(function (result) {
                        if (result.value) {
                            btn.disabled = true;
                            document.getElementById('cancel-order-form').submit();
                        }
                    });
                });
            });
        })();
    </script>
@endsection