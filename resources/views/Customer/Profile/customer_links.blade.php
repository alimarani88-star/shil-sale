@extends('Customer.Layout.master')

@php
    $hideFooter = true; // برای مخفی کردن فوتر
@endphp

@section('content')

    <main class="profile-user-page default">
        <div class="container">
            <div class="row justify-content-center">
                <!-- عنوان صفحه -->
                <div class="col-12 text-center mb-5">
                    <h1 class="title-tab-content">لیست قیمت و کاتالوگ محصولات شیل ایران</h1>
                    <p>جهت دریافت فایل ها روی لینک مورد نظر کلیک نمایید</p>
                </div>



                <!-- کارت لیست قیمت -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <a href="{{ url('/documents/pricelist.pdf') }}" target="_blank" class="card-download text-decoration-none">
                        <div class="card shadow-sm h-100">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <i class="fa fa-file-invoice-dollar fa-3x mb-3 text-primary"></i>
                                <h5 class="card-title">دانلود لیست قیمت</h5>
                                <p class="card-text text-muted text-center">آخرین نسخه لیست قیمت محصولات شیل ایران</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- کارت کاتالوگ -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <a href="{{ url('/documents/catalog1.pdf') }}" target="_blank" class="card-download text-decoration-none">
                        <div class="card shadow-sm h-100">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <i class="fa fa-book fa-3x mb-3 text-success"></i>
                                <h5 class="card-title">دانلود کاتالوگ</h5>
                                <p class="card-text text-muted text-center">کاتالوگ کامل محصولات شیل ایران</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-6 col-lg-4 mb-4">
                    <a href="{{ url('/documents/representatives_list.pdf') }}" target="_blank" class="card-download text-decoration-none">
                        <div class="card shadow-sm h-100">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <i class="fa fa-store fa-3x mb-3 text-info"></i>
                                <h5 class="card-title">لیست نمایندگان</h5>
                                <p class="card-text text-muted text-center">لیست کامل نمایندگان شیل ایران</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- کارت اینستاگرام -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <a href="https://www.instagram.com/shiliran?r=nametag" target="_blank" class="card-download text-decoration-none">
                        <div class="card shadow-sm h-100">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <i class="fab fa-instagram fa-3x mb-3" style="color:#E1306C;"></i>
                                <h5 class="card-title">اینستاگرام ما</h5>
                                <p class="card-text text-muted text-center">ما را در اینستاگرام دنبال کنید</p>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </main>

@endsection

@section('styles')
    <style>
        /* کارت‌ها */
        .card-download .card {
            transition: transform 0.3s, box-shadow 0.3s;
            border-radius: 0.8rem;
            cursor: pointer;
            border: 2px solid green;
        }

        .card-download .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 25px rgba(0,0,0,0.2);
        }

        .card-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .card-text {
            font-size: 0.9rem;
        }

        /* ریسپانسیو */
        @media (max-width: 768px) {
            .card-download .card-body {
                padding: 1.5rem 1rem;
            }
        }
    </style>
@endsection
