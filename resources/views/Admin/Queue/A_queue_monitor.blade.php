@extends('Admin.Layout.master')

@section('head-tag')
    <title>مانیتورینگ جاب‌ها</title>
    <style>
        .itq-page {
            direction: rtl;
            max-width: 1200px;
            margin: 8px auto 24px;
            padding: 8px 8px 24px;
        }

        .itq-card {
            background: #fff;
            border: 1px solid rgba(124, 77, 255, 0.16);
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(45, 24, 94, 0.06);
            overflow: hidden;
            margin-bottom: 16px;
        }

        .itq-card__top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
            padding: 18px 22px 14px;
            background: linear-gradient(270deg, rgba(124, 77, 255, 0.10) 0%, rgba(255, 255, 255, 0) 62%);
            border-bottom: 1px solid rgba(124, 77, 255, 0.12);
        }

        .itq-card__head {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .itq-card__icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: #4c2694;
            color: #fff;
            font-size: 18px;
        }

        .itq-card__title {
            margin: 0;
            font-size: 1.15rem;
            font-weight: 800;
            color: #2b145f;
        }

        .itq-card__subtitle {
            margin: 3px 0 0;
            font-size: 13px;
            color: #64748b;
        }

        .itq-card__body {
            padding: 16px 18px 20px;
        }

        .itq-stats {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 16px;
        }

        .itq-stat {
            padding: 12px;
            border-radius: 12px;
            background: #f8f5ff;
            border: 1px solid rgba(124, 77, 255, 0.14);
        }

        .itq-stat__label {
            margin: 0 0 4px;
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
        }

        .itq-stat__value {
            margin: 0;
            font-size: 18px;
            font-weight: 800;
            color: #2b145f;
        }

        .itq-stat--ok .itq-stat__value { color: #047857; }
        .itq-stat--off .itq-stat__value { color: #b91c1c; }

        .itq-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        .itq-page .btn-enable,
        .itq-page .btn-stop,
        .itq-page .btn-ghost {
            min-height: 42px;
            padding: 0 16px;
            border: 0;
            border-radius: 10px;
            font-weight: 800;
            font-size: 14px;
            cursor: pointer;
        }

        .itq-page .btn-enable {
            background: #4c2694;
            color: #fff;
        }

        .itq-page .btn-stop {
            background: #b91c1c;
            color: #fff;
        }

        .itq-page .btn-ghost {
            background: #f3efff;
            color: #4c2694;
        }

        .itq-note {
            margin: 12px 0 0;
            padding: 10px 12px;
            border-radius: 10px;
            background: #fffbeb;
            color: #b45309;
            font-size: 13px;
            font-weight: 700;
        }

        .itq-table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .itq-page table {
            width: max-content;
            min-width: 100%;
            margin: 0;
        }

        .itq-page th,
        .itq-page td {
            white-space: nowrap;
            vertical-align: middle;
            padding: 8px 10px;
            font-size: 13px;
        }

        .itq-page thead th {
            background: #f3efff;
            color: #2b145f;
            font-weight: 800;
        }

        .itq-empty {
            margin: 0;
            padding: 16px;
            text-align: center;
            color: #64748b;
            font-weight: 700;
        }

        .itq-ex {
            white-space: normal !important;
            max-width: 420px;
            font-size: 12px;
            color: #7c2d12;
        }

        @media (min-width: 768px) {
            .itq-stats {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        @media (max-width: 767px) {
            .itq-page .btn-enable,
            .itq-page .btn-stop,
            .itq-page .btn-ghost {
                width: 100%;
            }
        }
    </style>
@endsection

@section('content')
    <div class="itq-page" dir="rtl">
        <div class="itq-card">
            <div class="itq-card__top">
                <div class="itq-card__head">
                    <div class="itq-card__icon"><i class="fa fa-tasks"></i></div>
                    <div>
                        <h5 class="itq-card__title">مانیتورینگ جاب‌ها</h5>
                        <p class="itq-card__subtitle">صف ارسال پیامک — بدون تغییر منطق ارسال</p>
                    </div>
                </div>
                <a href="{{ route('A_home') }}" class="btn-ghost" style="display:inline-flex;align-items:center;text-decoration:none;">بازگشت به داشبورد</a>
            </div>
            <div class="itq-card__body">
                <div class="itq-stats">
                    <div class="itq-stat">
                        <p class="itq-stat__label">نوع صف</p>
                        <p class="itq-stat__value">{{ $connection }}</p>
                    </div>
                    <div class="itq-stat {{ $workerRunning ? 'itq-stat--ok' : 'itq-stat--off' }}">
                        <p class="itq-stat__label">وضعیت Worker</p>
                        <p class="itq-stat__value">{{ $workerRunning ? 'فعال' : 'خاموش' }}</p>
                    </div>
                    <div class="itq-stat">
                        <p class="itq-stat__label">در انتظار</p>
                        <p class="itq-stat__value">{{ number_format($pendingCount) }}</p>
                    </div>
                    <div class="itq-stat">
                        <p class="itq-stat__label">ناموفق</p>
                        <p class="itq-stat__value">{{ number_format($failedCount) }}</p>
                    </div>
                </div>

                <div class="itq-actions">
                    @if($isDatabase && !$workerRunning)
                        <form method="post" action="{{ route('A_queue_monitor_start') }}">
                            @csrf
                            <button type="submit" class="btn-enable">روشن کردن Worker</button>
                        </form>
                    @endif
                    @if($workerRunning)
                        <form method="post" action="{{ route('A_queue_monitor_stop') }}" onsubmit="return confirm('Worker این پروژه متوقف شود؟');">
                            @csrf
                            <button type="submit" class="btn-stop">توقف Worker</button>
                        </form>
                    @endif
                    <a href="{{ route('A_queue_monitor') }}" class="btn-ghost" style="display:inline-flex;align-items:center;text-decoration:none;">بروزرسانی صفحه</a>
                </div>

                @if(!$isDatabase)
                    <p class="itq-note">الان صف sync است. QUEUE_CONNECTION در .env باید database باشد تا Worker جاب‌ها را از جدول jobs بردارد.</p>
                @elseif(!$workerRunning)
                    <p class="itq-note">Worker خاموش است. پیامک‌ها در جدول jobs می‌مانند تا Worker روشن شود.</p>
                @endif
            </div>
        </div>

        <div class="itq-card">
            <div class="itq-card__top">
                <div class="itq-card__head">
                    <div class="itq-card__icon"><i class="fa fa-clock-o"></i></div>
                    <div>
                        <h6 class="itq-card__title">جاب‌های در انتظار</h6>
                        <p class="itq-card__subtitle">حداکثر ۵۰ مورد اخیر از جدول jobs</p>
                    </div>
                </div>
            </div>
            <div class="itq-card__body">
                <div class="itq-table-wrap">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>شناسه</th>
                            <th>نام جاب</th>
                            <th>صف</th>
                            <th>تلاش</th>
                            <th>زمان اجرا</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($pendingJobs as $job)
                            <tr>
                                <td class="text-center">{{ $job['id'] }}</td>
                                <td>{{ $job['name'] }}</td>
                                <td class="text-center">{{ $job['queue'] }}</td>
                                <td class="text-center">{{ $job['attempts'] }}</td>
                                <td class="text-center">{{ $job['available_at'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5"><p class="itq-empty">جاب در انتظاری نیست</p></td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="itq-card">
            <div class="itq-card__top">
                <div class="itq-card__head">
                    <div class="itq-card__icon"><i class="fa fa-exclamation-circle"></i></div>
                    <div>
                        <h6 class="itq-card__title">جاب‌های ناموفق</h6>
                        <p class="itq-card__subtitle">از جدول failed_jobs — می‌توان دوباره به صف فرستاد</p>
                    </div>
                </div>
                @if($failedCount > 0)
                    <form method="post" action="{{ route('A_queue_monitor_retry_all') }}" onsubmit="return confirm('همه جاب‌های ناموفق دوباره به صف بروند؟');">
                        @csrf
                        <button type="submit" class="btn-enable">تلاش دوباره همه</button>
                    </form>
                @endif
            </div>
            <div class="itq-card__body">
                <div class="itq-table-wrap">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>زمان</th>
                            <th>نام جاب</th>
                            <th>صف</th>
                            <th>خطا</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($failedJobs as $job)
                            <tr>
                                <td class="text-center">{{ $job['failed_at'] }}</td>
                                <td>{{ $job['name'] }}</td>
                                <td class="text-center">{{ $job['queue'] }}</td>
                                <td class="itq-ex">{{ $job['exception'] }}</td>
                                <td class="text-center">
                                    <form method="post" action="{{ route('A_queue_monitor_retry') }}">
                                        @csrf
                                        <input type="hidden" name="uuid" value="{{ $job['uuid'] }}">
                                        <button type="submit" class="btn-ghost">تلاش دوباره</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5"><p class="itq-empty">جاب ناموفقی ثبت نشده</p></td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
