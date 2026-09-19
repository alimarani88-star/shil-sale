@extends('Customer.Layout.master')

@section('head-tag')
    <style>
        .orders-mobile-list {
            display: none;
        }

        .order-mobile-card {
            background: #fff;
            border: 1px solid #ebebeb;
            border-radius: 10px;
            padding: 14px 14px 12px;
            margin-bottom: 12px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .04);
        }

        .order-mobile-card .order-mobile-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
            padding: 6px 0;
            border-bottom: 1px dashed #eee;
            font-size: 13px;
        }

        .order-mobile-card .order-mobile-row:last-child {
            border-bottom: none;
        }

        .order-mobile-card .order-mobile-label {
            color: #777;
            flex: 0 0 auto;
            white-space: nowrap;
        }

        .order-mobile-card .order-mobile-value {
            text-align: left;
            font-weight: 600;
            color: #333;
            word-break: break-word;
        }

        .order-mobile-card .order-mobile-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: flex-start;
            margin-top: 10px;
        }

        .order-mobile-card .order-mobile-actions .btn {
            flex: 1 1 auto;
            min-width: 120px;
        }

        .order-mobile-card .order-mobile-warning {
            color: #f57f17;
            font-size: 12px;
            line-height: 1.7;
            margin-top: 10px;
            padding: 8px 10px;
            background: #fff8e1;
            border-radius: 8px;
            text-align: right;
        }

        .order-mobile-card .order-code {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        @media (max-width: 767.98px) {
            .orders-desktop-table {
                display: none !important;
            }

            .orders-mobile-list {
                display: block;
            }

            main.profile-user-page .content-section.default {
                padding-left: 8px;
                padding-right: 8px;
            }
        }

        @media (min-width: 768px) {
            .table-order th:nth-child(5),
            .table-order td.order-deadline {
                max-width: 220px;
                white-space: normal;
                font-size: 12px;
                line-height: 1.6;
            }

            .table-order td.order-actions .btn {
                margin: 2px;
            }
        }
    </style>
@endsection

@section('content')

    <!-- main -->
    <main class="profile-user-page default">
        <div class="container">
            <div class="row">
                <div class="profile-page col-xl-9 col-lg-8 col-md-12 order-2">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-12">
                                <h1 class="title-tab-content">همه سفارش ها</h1>
                            </div>
                            <div class="content-section default">
                                <div class="table-responsive orders-desktop-table">
                                    <table class="table table-order">
                                        <thead class="thead custom-primary">
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">شماره سفارش</th>
                                                <th scope="col">تاریخ ثبت سفارش</th>
                                                <th scope="col">مبلغ کل</th>
                                                <th scope="col">مهلت پرداخت</th>
                                                <th scope="col">عملیات پرداخت</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($orders as $order)
                                                @php
                                                    $isPending = (int) $order->status === \App\Models\Order::STATUS_PENDING_PAYMENT;
                                                    $isExpired = (int) $order->status === \App\Models\Order::STATUS_PAYMENT_EXPIRED;
                                                    $isCancelled = (int) $order->status === \App\Models\Order::STATUS_CANCELLED_TO_WALLET;
                                                    $deadlineTs = $isPending && $order->payment_deadline_at
                                                        ? $order->payment_deadline_at->timestamp
                                                        : null;
                                                @endphp
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td class="order-code">{{ $order->code }}</td>
                                                    <td>{{ $order->created_at_jalali }}</td>
                                                    <td>{{ number_format($order->total_price) }}</td>
                                                    <td class="order-deadline" style="color: #f57f17;">
                                                        @if($isPending && $deadlineTs)
                                                            <span class="js-payment-countdown" data-deadline="{{ $deadlineTs }}">
                                                                در حال محاسبه مهلت پرداخت...
                                                            </span>
                                                        @elseif($isExpired)
                                                            مهلت پرداخت به پایان رسیده است.
                                                        @else
                                                            —
                                                        @endif
                                                    </td>
                                                    @if($isPending)
                                                        <td class="order-actions">
                                                            <a href="{{ route('order_detail', ['order' => $order->id]) }}"
                                                                class="btn btn-sm"
                                                                style="background-color: white; color: black; border: 2px solid #6a1b9a;border-radius: 5px;">
                                                                جزییات سفارش
                                                            </a>
                                                            <a href="{{ route('cart_payment', ['order' => $order->id]) }}"
                                                                class="btn custom-primary btn-sm js-disable-after-click">پرداخت سفارش</a>
                                                        </td>
                                                    @elseif($isExpired)
                                                        <td class="order-actions">
                                                            <a href="{{ route('order_detail', ['order' => $order->id]) }}"
                                                                class="btn btn-sm"
                                                                style="background-color: white; color: black; border: 2px solid #6a1b9a;border-radius: 5px;">
                                                                جزییات سفارش
                                                            </a>
                                                            <span class="text-danger">منقضی شده</span>
                                                        </td>
                                                    @elseif($isCancelled)
                                                        <td class="order-actions">
                                                            <a href="{{ route('order_detail', ['order' => $order->id]) }}"
                                                                class="btn btn-sm"
                                                                style="background-color: white; color: black; border: 2px solid #6a1b9a;border-radius: 5px;">
                                                                جزییات سفارش
                                                            </a>
                                                            <span class="text-danger">{{ $order->resolvedStatusTitle() }}</span>
                                                        </td>
                                                    @else
                                                        <td class="order-actions">
                                                            <a href="{{ route('order_detail', ['order' => $order->id]) }}"
                                                                class="btn btn-sm"
                                                                style="background-color: white; color: black; border: 2px solid #6a1b9a;border-radius: 5px;">
                                                                جزییات سفارش
                                                            </a>
                                                            <span class="text-success">{{ $order->resolvedStatusTitle() }}</span>
                                                        </td>
                                                    @endif
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6">سفارشی ثبت نشده است.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <div class="orders-mobile-list">
                                    @forelse($orders as $order)
                                        @php
                                            $isPending = (int) $order->status === \App\Models\Order::STATUS_PENDING_PAYMENT;
                                            $isExpired = (int) $order->status === \App\Models\Order::STATUS_PAYMENT_EXPIRED;
                                            $isCancelled = (int) $order->status === \App\Models\Order::STATUS_CANCELLED_TO_WALLET;
                                            $deadlineTs = $isPending && $order->payment_deadline_at
                                                ? $order->payment_deadline_at->timestamp
                                                : null;
                                        @endphp
                                        <div class="order-mobile-card">
                                            <div class="order-mobile-row">
                                                <span class="order-mobile-label">شماره سفارش</span>
                                                <span class="order-mobile-value order-code">{{ $order->code }}</span>
                                            </div>
                                            <div class="order-mobile-row">
                                                <span class="order-mobile-label">تاریخ ثبت</span>
                                                <span class="order-mobile-value">{{ $order->created_at_jalali }}</span>
                                            </div>
                                            <div class="order-mobile-row">
                                                <span class="order-mobile-label">مبلغ کل</span>
                                                <span class="order-mobile-value">{{ number_format($order->total_price) }} ریال</span>
                                            </div>
                                            <div class="order-mobile-row">
                                                <span class="order-mobile-label">وضعیت</span>
                                                <span class="order-mobile-value">
                                                    @if($isPending)
                                                        در انتظار پرداخت
                                                    @elseif($isExpired)
                                                        <span class="text-danger">منقضی شده</span>
                                                    @elseif($isCancelled)
                                                        <span class="text-danger">{{ $order->resolvedStatusTitle() }}</span>
                                                    @else
                                                        <span class="text-success">{{ $order->resolvedStatusTitle() }}</span>
                                                    @endif
                                                </span>
                                            </div>

                                            @if($isPending)
                                                <div class="order-mobile-warning">
                                                    <span class="js-payment-countdown" data-deadline="{{ $deadlineTs }}">
                                                        در حال محاسبه مهلت پرداخت...
                                                    </span>
                                                </div>
                                                <div class="order-mobile-actions">
                                                    <a href="{{ route('order_detail', ['order' => $order->id]) }}"
                                                        class="btn btn-sm"
                                                        style="background-color: white; color: black; border: 2px solid #6a1b9a;border-radius: 5px;">
                                                        جزییات سفارش
                                                    </a>
                                                    <a href="{{ route('cart_payment', ['order' => $order->id]) }}"
                                                        class="btn custom-primary btn-sm js-disable-after-click">پرداخت سفارش</a>
                                                </div>
                                            @elseif($isExpired)
                                                <div class="order-mobile-actions">
                                                    <a href="{{ route('order_detail', ['order' => $order->id]) }}"
                                                        class="btn btn-sm"
                                                        style="background-color: white; color: black; border: 2px solid #6a1b9a;border-radius: 5px;">
                                                        جزییات سفارش
                                                    </a>
                                                </div>
                                            @else
                                                <div class="order-mobile-actions">
                                                    <a href="{{ route('order_detail', ['order' => $order->id]) }}"
                                                        class="btn btn-sm"
                                                        style="background-color: white; color: black; border: 2px solid #6a1b9a;border-radius: 5px;">
                                                        جزییات سفارش
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="text-center py-4 text-muted">سفارشی ثبت نشده است.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <x-profile-sidebar :user="$user" />
            </div>
        </div>
    </main>
    <!-- main -->

@endsection

@section('script')
    <script>
        (function () {
            function formatRemaining(totalSeconds) {
                var minutes = Math.floor(totalSeconds / 60);
                var seconds = totalSeconds % 60;
                return 'سفارش در صورت عدم پرداخت تا ' + minutes + ' دقیقه، ' + seconds + ' ثانیه دیگر لغو خواهد شد.';
            }

            function tickCountdowns() {
                var now = Math.floor(Date.now() / 1000);
                document.querySelectorAll('.js-payment-countdown').forEach(function (el) {
                    var deadline = parseInt(el.getAttribute('data-deadline'), 10);
                    if (!deadline) {
                        el.textContent = 'مهلت پرداخت مشخص نیست.';
                        return;
                    }
                    var remaining = deadline - now;
                    if (remaining <= 0) {
                        el.textContent = 'مهلت پرداخت به پایان رسیده است.';
                        return;
                    }
                    el.textContent = formatRemaining(remaining);
                });
            }

            tickCountdowns();
            setInterval(tickCountdowns, 1000);

            function disablePayLink(link) {
                if (!link || link.getAttribute('aria-disabled') === 'true') {
                    return;
                }
                link.setAttribute('aria-disabled', 'true');
                link.classList.add('disabled');
                link.style.pointerEvents = 'none';
                link.style.opacity = '0.65';
            }

            document.querySelectorAll('.js-disable-after-click').forEach(function (el) {
                el.addEventListener('click', function (e) {
                    if (el.getAttribute('aria-disabled') === 'true') {
                        e.preventDefault();
                        e.stopPropagation();
                        return;
                    }
                    var href = el.getAttribute('href');
                    document.querySelectorAll('.js-disable-after-click').forEach(function (link) {
                        if (link.getAttribute('href') === href) {
                            disablePayLink(link);
                        }
                    });
                }, true);
            });
        })();
    </script>
@endsection
