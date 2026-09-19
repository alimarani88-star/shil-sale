@extends('Customer.Layout.master')

@section('head-tag')
    <style>
        .wallet-balance-box {
            background: #fff;
            border: 1px solid #ebebeb;
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 16px;
        }

        .wallet-balance-label {
            color: #777;
            font-size: 13px;
            margin-bottom: 6px;
        }

        .wallet-balance-value {
            font-size: 22px;
            font-weight: 700;
            color: #333;
        }

        .wallet-pending-balance {
            margin-top: 8px;
            color: #f57f17;
            font-size: 13px;
        }

        .wallet-tx-credit {
            color: #2e7d32;
            font-weight: 600;
        }

        .wallet-tx-debit {
            color: #c62828;
            font-weight: 600;
        }
    </style>
@endsection

@section('content')
    <main class="profile-user-page default">
        <div class="container">
            <div class="row">
                <div class="profile-page col-xl-9 col-lg-8 col-md-12 order-2">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-12">
                                <h1 class="title-tab-content">کیف پول</h1>
                            </div>
                            <div class="content-section default">
                                <div class="wallet-balance-box">
                                    <div class="wallet-balance-label">موجودی قابل استفاده</div>
                                    <div class="wallet-balance-value">{{ number_format($walletBalance) }} ریال</div>
                                    @if(($walletPendingBalance ?? 0) > 0)
                                        <div class="wallet-pending-balance">
                                            در انتظار تایید مالی: {{ number_format($walletPendingBalance) }} ریال
                                        </div>
                                    @endif
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-order">
                                        <thead class="thead custom-primary">
                                            <tr>
                                                <th>#</th>
                                                <th>تاریخ</th>
                                                <th>شرح</th>
                                                <th>وضعیت</th>
                                                <th>مبلغ</th>
                                                <th>موجودی بعد از تراکنش</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($walletTransactions as $transaction)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $transaction->created_at_jalali }}</td>
                                                    <td>{{ $transaction->resolvedReasonTitle() }}</td>
                                                    <td>{{ $transaction->resolvedStatusTitle() }}</td>
                                                    <td class="{{ $transaction->isCredit() ? 'wallet-tx-credit' : 'wallet-tx-debit' }}">
                                                        {{ $transaction->isCredit() ? '+' : '-' }}{{ number_format($transaction->amount) }}
                                                    </td>
                                                    <td>{{ number_format($transaction->balance_after) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6">تراکنشی برای کیف پول ثبت نشده است.</td>
                                                </tr>
                                            @endforelse
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
@endsection
