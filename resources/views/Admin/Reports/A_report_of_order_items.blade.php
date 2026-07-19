@extends('Admin.Layout.master')

@section('head-tag')
    <style>
        .order-report-page {
            direction: rtl;
            padding: 24px 0;
            background: #f8fafc;
            min-height: 100vh;
        }

        .report-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 12px 35px rgba(15, 23, 42, 0.07);
        }

        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            padding: 20px 24px;
            border-bottom: 1px solid #e2e8f0;
            background: linear-gradient(135deg, #f8fbff, #eef5ff);
        }

        .report-title {
            margin: 0;
            color: #0f172a;
            font-size: 1.25rem;
            font-weight: 800;
        }

        .order-code {
            display: inline-flex;
            align-items: center;
            background: #dbeafe;
            color: #1d4ed8;
            border-radius: 999px;
            padding: 7px 14px;
            font-size: .85rem;
            font-weight: 700;
        }

        .order-summary {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            padding: 20px 24px;
            border-bottom: 1px solid #e2e8f0;
        }

        .summary-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px;
        }

        .summary-label {
            display: block;
            color: #64748b;
            font-size: .8rem;
            margin-bottom: 6px;
        }

        .summary-value {
            color: #0f172a;
            font-weight: 800;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 6px 12px;
            font-size: .8rem;
            font-weight: 700;
        }

        .status-paid {
            background: #dcfce7;
            color: #15803d;
        }

        .status-pending {
            background: #ffedd5;
            color: #c2410c;
        }

        .status-deleted {
            background: #fee2e2;
            color: #b91c1c;
        }

        .items-table {
            margin: 0;
            min-width: 950px;
            vertical-align: middle;
        }

        .items-table thead th {
            white-space: nowrap;
            background: #f8fafc;
            color: #475569;
            padding: 14px;
            border-bottom: 1px solid #e2e8f0;
            font-size: .85rem;
        }

        .items-table tbody td {
            padding: 14px;
            color: #334155;
            border-color: #f1f5f9;
            vertical-align: middle;
        }

        .items-table tbody tr:hover {
            background: #f8fbff;
        }

        .product-info {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 230px;
        }

        .product-image {
            width: 58px;
            height: 58px;
            border-radius: 10px;
            object-fit: cover;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .product-name {
            color: #0f172a;
            font-weight: 700;
        }

        .product-id {
            color: #94a3b8;
            font-size: .75rem;
            margin-top: 4px;
        }

        .price-value {
            white-space: nowrap;
            color: #0f172a;
            font-weight: 700;
        }

        .discount-badge {
            background: #fef3c7;
            color: #b45309;
            border-radius: 8px;
            padding: 5px 9px;
            font-size: .78rem;
            font-weight: 700;
        }

        .total-row {
            background: #f8fafc;
            font-weight: 800;
        }

        .empty-state {
            padding: 55px 20px;
            text-align: center;
            color: #64748b;
        }

        @media (max-width: 991px) {
            .order-summary {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 575px) {
            .report-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .order-summary {
                grid-template-columns: 1fr;
                padding: 14px;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $itemsTotal = $orderItems->sum(function ($item) {
            $price = (int) $item->price;
            $count = (int) $item->count;
            $discount = (float) ($item->discount ?? 0);

            $discountAmount = $price * ($discount / 100);

            return ($price - $discountAmount) * $count;
        });
    @endphp

    <main class="order-report-page">
        <div class="container-fluid px-3 px-lg-4">
            <div class="report-card">

                <div class="report-header">
                    <div>
                        <h1 class="report-title">گزارش اقلام سفارش</h1>
                    </div>

                    <span class="order-code">
                        کد سفارش: {{ $order->code }}
                    </span>
                </div>

                <div class="order-summary">
                    <div class="summary-item">
                        <span class="summary-label">نام مشتری</span>
                        <span class="summary-value">
                            {{ $order->customer_name ?? '—' }}
                        </span>
                    </div>

                    <div class="summary-item">
                        <span class="summary-label">وضعیت سفارش</span>

                        @if($order->deleted_at)
                            <span class="status-badge status-deleted">
                                حذف شده
                            </span>
                        @elseif((int) $order->status === 1)
                            <span class="status-badge status-paid">
                                {{ $order->status_title ?? 'پرداخت شده' }}
                            </span>
                        @else
                            <span class="status-badge status-pending">
                                {{ $order->status_title ?? 'در انتظار پرداخت' }}
                            </span>
                        @endif
                    </div>

                    <div class="summary-item">
                        <span class="summary-label">تعداد اقلام</span>
                        <span class="summary-value">
                            {{ number_format($orderItems->sum('count')) }}
                        </span>
                    </div>

                    <div class="summary-item">
                        <span class="summary-label">تاریخ سفارش</span>
                        <span class="summary-value">
                            {{ optional($order->created_at)->format('Y/m/d H:i') ?? '—' }}
                        </span>
                    </div>
                </div>

                @if($orderItems->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table items-table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>محصول</th>
                                <th>قیمت واحد</th>
                                <th>تعداد</th>
                                <th>تخفیف</th>
                                <th>مبلغ قبل تخفیف</th>
                                <th>مبلغ نهایی</th>
                                <th>تاریخ ثبت</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($orderItems as $index => $item)
                                @php
                                    $unitPrice = (int) $item->price;
                                    $count = (int) $item->count;
                                    $discount = (float) ($item->discount ?? 0);

                                    $rowTotalBeforeDiscount = $unitPrice * $count;
                                    $rowDiscountAmount = $rowTotalBeforeDiscount * ($discount / 100);
                                    $rowFinalPrice = $rowTotalBeforeDiscount - $rowDiscountAmount;

                                    $productName = $item->product_name
                                        ?? $item->product?->product_name
                                        ?? 'محصول حذف شده یا نامشخص';

                                    $imagePath = $item->product?->images?->first()?->path
                                        ?? null;
                                @endphp

                                <tr>
                                    <td>{{ $index + 1 }}</td>

                                    <td>
                                        <div class="product-info">
                                            @if($imagePath)
                                                <img
                                                    src="{{ asset($imagePath) }}"
                                                    alt="{{ $productName }}"
                                                    class="product-image"
                                                >
                                            @else
                                                <div class="product-image d-flex align-items-center justify-content-center">
                                                    <span>📦</span>
                                                </div>
                                            @endif

                                            <div>
                                                <div class="product-name">
                                                    {{ $productName }}
                                                </div>

                                                <div class="product-id">
                                                    شناسه محصول: {{ $item->product_id }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="price-value">
                                        {{ number_format($unitPrice) }}
                                        تومان
                                    </td>

                                    <td>
                                        {{ number_format($count) }}
                                    </td>

                                    <td>
                                        @if($discount > 0)
                                            <span class="discount-badge">
                                                {{ number_format($discount) }}٪
                                            </span>
                                        @else
                                            —
                                        @endif
                                    </td>

                                    <td class="price-value">
                                        {{ number_format($rowTotalBeforeDiscount) }}
                                        تومان
                                    </td>

                                    <td class="price-value">
                                        {{ number_format($rowFinalPrice) }}
                                        تومان
                                    </td>

                                    <td>
                                        {{ optional($item->created_at)->format('Y/m/d H:i') ?? '—' }}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>

                            <tfoot>
                            <tr class="total-row">
                                <td colspan="6">
                                    جمع کل اقلام سفارش
                                </td>

                                <td colspan="2" class="price-value">
                                    {{ number_format($itemsTotal) }}
                                    تومان
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <h3>اقلامی برای این سفارش وجود ندارد</h3>
                        <p>هیچ محصولی برای این سفارش ثبت نشده است.</p>
                    </div>
                @endif

            </div>
        </div>
    </main>
@endsection
