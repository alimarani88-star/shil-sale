@extends('Customer.Layout.master')

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
                                <div class="table-responsive">
                                    <table class="table table-order">
                                        <thead class="thead custom-primary">
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">شماره سفارش</th>
                                                <th scope="col">تاریخ ثبت سفارش</th>
                                                <th scope="col">مبلغ کل</th>
                                                <th scope="col"></th>
                                                <th scope="col">عملیات پرداخت</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($orders as $order)
                                                <tr>
                                                    <td>1</td>
                                                    <td class="order-code">{{$order->code}}</td>
                                                    <td>{{$order->created_at_jalali}}</td>
                                                    <td>{{ number_format($order->total_price) }}</td>
                                                    <td style="color: #f57f17;">سفارش در صورت عدم پرداخت تا ۵ دقیقه, ۵۹ ثانیه دیگر لغو خواهد شد.</td>
                                                    @if($order->status == 0)
                                                        <td>
                                                            <a href="{{ route('order_detail', ['order' => $order->id]) }}"
                                                                class="btn btn-sm"
                                                                style="background-color: white; color: black; border: 2px solid #6a1b9a;border-radius: 5px;">
                                                                جزییات سفارش
                                                            </a>

                                                            <a href="{{ route('cart', $order->id) }}"
                                                                class="btn custom-primary btn-sm">پرداخت سفارش</a>
                                                        </td>
                                                    @else
                                                        <td><span class="text-success">پرداخت شده</span></td>
                                                    @endif
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
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