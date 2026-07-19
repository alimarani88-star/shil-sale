@extends('Admin.Layout.master')

@section('head-tag')
    <style>
        .orders-page {
            direction: rtl;
            padding: 2rem 0;
            background: #f8fafc;
            min-height: 100vh;
        }

        .orders-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        }

        .orders-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .orders-header h1 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 800;
            color: #111827;
        }

        .orders-count {
            background: #eff6ff;
            color: #1d4ed8;
            border-radius: 999px;
            padding: .4rem .8rem;
            font-size: .85rem;
            font-weight: 700;
        }

        .orders-table {
            margin: 0;
            min-width: 1250px;
            vertical-align: middle;
        }

        .orders-table thead th {
            background: #f8fafc;
            color: #475569;
            font-size: .85rem;
            font-weight: 700;
            white-space: nowrap;
            border-bottom: 1px solid #e2e8f0;
            padding: .9rem;
        }

        .orders-table tbody td {
            color: #334155;
            font-size: .9rem;
            white-space: nowrap;
            padding: .9rem;
            border-color: #f1f5f9;
        }

        .orders-table tbody tr:hover {
            background: #f8fbff;
        }

        .order-code {
            font-weight: 800;
            color: #2563eb;
        }

        .price {
            font-weight: 700;
            color: #0f172a;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .35rem;
            border-radius: 999px;
            padding: .38rem .75rem;
            font-size: .8rem;
            font-weight: 700;
        }

        .status-pending {
            background: #fff7ed;
            color: #c2410c;
        }

        .status-paid {
            background: #ecfdf5;
            color: #047857;
        }

        .status-cancelled {
            background: #fef2f2;
            color: #b91c1c;
        }

        .status-default {
            background: #f1f5f9;
            color: #475569;
        }

        .deleted-badge {
            background: #fee2e2;
            color: #b91c1c;
            border-radius: 8px;
            padding: .3rem .55rem;
            font-size: .75rem;
            font-weight: 700;
        }

        .active-badge {
            background: #dcfce7;
            color: #15803d;
            border-radius: 8px;
            padding: .3rem .55rem;
            font-size: .75rem;
            font-weight: 700;
        }

        .empty-orders {
            text-align: center;
            padding: 4rem 1rem;
            color: #64748b;
        }

        .empty-orders h3 {
            color: #334155;
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: .5rem;
        }

        .table-responsive {
            scrollbar-width: thin;
        }

        .pagination-wrapper {
            padding: 1rem 1.5rem;
            border-top: 1px solid #e5e7eb;
        }

        @media (max-width: 767.98px) {
            .orders-page {
                padding: 1rem 0;
            }

            .orders-card {
                border-radius: 12px;
            }

            .orders-header {
                padding: 1rem;
            }

            .orders-header h1 {
                font-size: 1.05rem;
            }
        }
    </style>
@endsection

@section('content')
    <main class="orders-page">
        <div class="container-fluid px-3 px-lg-4">
            <div class="orders-card">

                <div class="orders-header">
                    <div>
                        <h1>لیست سفارش‌ها</h1>
                    </div>

                    <span class="orders-count">
{{--                        {{ number_format($orders->total()) }} سفارش--}}
                    </span>
                </div>

                @if($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table orders-table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کد سفارش</th>
                                <th>نام مشتری</th>
                                <th>وضعیت</th>
                                <th>مبلغ کالا</th>
                                <th>هزینه ارسال</th>
                                <th>مبلغ نهایی</th>
                                <th>روش ارسال</th>
                                <th>زمان ارسال</th>
                                <th>شماره آدرس</th>
                                <th>فاکتور</th>
                                <th>تاریخ ثبت</th>
                                <th>آخرین ویرایش</th>
                                <th>وضعیت رکورد</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($orders as $order)
                                @php
                                    $statusClass = match ((int) $order->status) {
                                        0 => 'status-pending',
                                        1 => 'status-paid',
                                        2 => 'status-cancelled',
                                        default => 'status-default',
                                    };

                                    $statusTitle = $order->status_title ?: match ((int) $order->status) {
                                        0 => 'در انتظار پرداخت',
                                        1 => 'پرداخت شده',
                                        2 => 'لغو شده',
                                        default => 'نامشخص',
                                    };

                                    $finalPrice = (int) $order->total_price + (int) $order->send_price;
                                @endphp

                                <tr>
                                    <td>{{ $order->id }}</td>

                                    <td>
                                        <a class="order-code" href="/A_report_of_order_items/{{$order->id}}">
                                            {{ $order->code }}
                                        </a>
                                    </td>

                                    <td>
                                        {{ $order->customer_name ?? '—' }}
                                    </td>

                                    <td>
                                        <span class="status-badge {{ $statusClass }}">
                                            {{ $statusTitle }}
                                        </span>
                                    </td>

                                    <td class="price">
                                        {{ number_format($order->total_price) }}
                                        <small>تومان</small>
                                    </td>

                                    <td>
                                        {{ number_format($order->send_price) }}
                                        <small>تومان</small>
                                    </td>

                                    <td class="price">
                                        {{ number_format($finalPrice) }}
                                        <small>تومان</small>
                                    </td>

                                    <td>
                                        {{ $order->send_type ?? '—' }}
                                    </td>

                                    <td>
                                        {{ $order->send_time ?? '—' }}
                                    </td>

                                    <td>
                                        {{ $order->address_id ?? '—' }}
                                    </td>

                                    <td>
                                        @if($order->invoice)
                                            <span class="active-badge">صادر شده</span>
                                        @else
                                            <span class="status-badge status-default">صادر نشده</span>
                                        @endif
                                    </td>

                                    <td>
                                        {{ optional($order->created_at)->format('Y/m/d H:i') ?? '—' }}
                                    </td>

                                    <td>
                                        {{ optional($order->updated_at)->format('Y/m/d H:i') ?? '—' }}
                                    </td>

                                    <td>
                                        @if($order->deleted_at)
                                            <span class="deleted-badge">حذف شده</span>
                                        @else
                                            <span class="active-badge">فعال</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($orders->hasPages())
                        <div class="pagination-wrapper">
                            {{ $orders->links() }}
                        </div>
                    @endif
                @else
                    <div class="empty-orders">
                        <h3>سفارشی وجود ندارد</h3>
                        <p>هنوز هیچ سفارشی در سیستم ثبت نشده است.</p>
                    </div>
                @endif

            </div>
        </div>
    </main>
@endsection
