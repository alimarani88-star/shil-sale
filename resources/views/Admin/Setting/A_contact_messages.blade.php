@extends('Admin.Layout.master')

@section('head-tag')
    <title>پیام‌های تماس با ما</title>
@endsection

@section('content')
    <div class="container-fluid pt-2">
        <nav aria-label="breadcrumb" class="mt-0 px-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item font-size-12"><a href="#">خانه</a></li>
                <li class="breadcrumb-item font-size-12"><a href="#">تنظیمات</a></li>
                <li class="breadcrumb-item font-size-12 active" aria-current="page">پیام‌های تماس با ما</li>
            </ol>
        </nav>

        <section class="row">
            <div class="col-12">
                <section class="main-body-container">
                    <section class="main-body-container-header">
                        <h5>پیام‌های تماس با ما</h5>
                    </section>
                    <div class="d-flex justify-content-between align-items-center mt-2 mb-4 border-bottom pb-2">
                        <a href="{{ route('A_home') }}" class="btn btn-info btn-sm">بازگشت</a>
                    </div>
                    <section class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>نام</th>
                                <th>موبایل</th>
                                <th>موضوع</th>
                                <th>پیام</th>
                                <th>تاریخ</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($contactMessages as $contactMessage)
                                <tr>
                                    <td>{{ $contactMessages->firstItem() + $loop->index }}</td>
                                    <td>{{ $contactMessage->name }}</td>
                                    <td>{{ $contactMessage->mobile }}</td>
                                    <td>{{ $contactMessage->subject }}</td>
                                    <td style="white-space: pre-wrap; max-width: 360px;">{{ $contactMessage->message }}</td>
                                    <td>{{ $contactMessage->created_at_jalali }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">پیامی ثبت نشده است.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </section>
                    {{ $contactMessages->links() }}
                </section>
            </div>
        </section>
    </div>
@endsection
