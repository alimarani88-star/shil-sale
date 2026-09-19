@extends('Customer.Layout.master')
@section('title', 'تماس با ما | شرکت شارین پارس ایرانیان | تولیدکننده تجهیزات برق در اصفهان')
@section('head-tag')
    <link href="{{ asset('assets/css/plugins/AddTags.css') }}" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ asset('plugins/leaflet/leaflet.css') }}" />
    <style>
        .contact-hp {
            position: absolute;
            left: -9999px;
            width: 1px;
            height: 1px;
            overflow: hidden;
        }
    </style>
@endsection
@section('meta')
    <meta name="description"
        content="راه‌های تماس با شرکت شارین پارس ایرانیان در اصفهان — آدرس، شماره تماس، ایمیل و فرم ارسال پیام آنلاین برای ارتباط سریع با بخش فروش و پشتیبانی.">
    <meta name="keywords"
        content="تماس با ما شارین پارس, شارین پارس ایرانیان, تجهیزات برق اصفهان, شماره تماس شارین پارس, آدرس شارین پارس">
    <meta name="robots" content="index, follow" />
    <link rel="canonical" href="https://www.shil.ir/contact" />

    <!-- Open Graph / Social -->
    <meta property="og:type" content="website" />
    <meta property="og:title" content="تماس با ما | شرکت شارین پارس ایرانیان" />
    <meta property="og:description"
        content="آدرس، شماره تماس، ایمیل و فرم ارسال پیام برای ارتباط سریع با شرکت شارین پارس ایرانیان - تولیدکننده تجهیزات برق در اصفهان." />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:site_name" content="شارین پارس ایرانیان" />
    <meta property="og:image" content="{{ asset('assets/img/logo.png') }}" />
@endsection

@section('content')
    <main class="contact-page py-5 shadow-lg  mx-md-6 contact-box">
        <div class="row gy-4">
            <!-- بخش تصویر بلور -->
            <div class="col-12 col-lg-6 order-1 order-lg-3">
                <div class="contact-page bg-blur py-5 z-nl w-100 h-100" style="min-height: 300px">
                    <!-- پس‌زمینه بلور یا تصویر تزئینی -->
                </div>
            </div>

            <!-- اطلاعات تماس و نقشه -->
            <div class="col-12 col-lg-3 order-2 order-lg-2">
                <div class="rounded-4 p-4 border-0 bg-white mb-4">
                    <h5 class="mb-4">
                        اطلاعات تماس
                        <i class="fa fa-address-card custom-cl-primary me-2"></i>
                    </h5>

                    <p>
                        <strong>آدرس:</strong> {{ $company_info['address'] }}
                        <i class="fa fa-map-marker-alt  me-2 custom-cl-primary"></i>
                    </p>
                    <p>
                        <strong>کد پستی:</strong> {{ $company_info['postal_code'] }}
                        <i class="fa fa-map-pin  me-2 custom-cl-primary"></i>
                    </p>

                    <p>
                        <strong>تلفن:</strong>
                        <a href="tel:{{ $company_info['telephone'] }}" class="text-decoration-none">
                            {{ $company_info['telephone'] }}
                        </a>
                        <i class="fa fa-phone-alt me-2 custom-cl-primary"></i>

                    </p>

                    <p>
                        <strong>موبایل:</strong>
                        <a href="tel:{{ $company_info['mobile'] }}" class="text-decoration-none">
                            {{ $company_info['mobile'] }}
                        </a>
                        <i class="fa fa-mobile-alt me-2 custom-cl-primary"></i>
                    </p>

                    <p>
                        <strong>ایمیل:</strong> {{ $company_info['email'] }}
                        <i class="fa fa-envelope me-2 custom-cl-primary"></i>
                    </p>

                    <p>
                        <strong>ساعات کاری:</strong> {{ $company_info['work_hours'] }}
                        <i class="fa fa-clock custom-cl-primary me-2"></i>
                    </p>
                    <p>
                        <a href="{{$company_info['instagram']}}">
                            <i class="fab fa-instagram custom-cl-primary me-2 ml-4" style="font-size: 33px"></i>
                        </a>
                        <a href="{{$company_info['linkedin']}}">
                            <i class="fab fa-linkedin  custom-cl-primary me-2" style="font-size: 33px"></i>
                        </a>
                    </p>
                </div>

                <div class="rounded-4 overflow-hidden" style="height: 300px;">
                    <div id="map" style="height: 57%; width: 100%;"></div>
                </div>
            </div>

            <!-- فرم تماس -->
            <div class="col-12 col-lg-3 order-3 order-lg-1">
                <div class="rounded-4 p-4 border-0 bg-white h-100">
                    <h5 class="mb-4">
                        ارسال پیام
                        <i class="fa fa-paper-plane custom-cl-primary me-2"></i>
                    </h5>

                    <form action="{{ route('contact.send') }}" method="POST" autocomplete="off">
                        @csrf

                        <div class="contact-hp" aria-hidden="true">
                            <label for="website">وب‌سایت</label>
                            <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="contact-name">
                                نام شما
                                <i class="fa fa-user me-2 custom-cl-primary"></i>
                            </label>
                            <input type="text" name="name" id="contact-name" class="form-control"
                                value="{{ old('name') }}" placeholder="نام و نام خانوادگی" maxlength="80" required>
                            @error('name')
                                <span class="text-danger d-block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        @if(auth()->user()?->contactMobile())
                            <p class="mb-3 text-muted" style="font-size: 13px;">
                                شماره موبایل از حساب کاربری شما استفاده می‌شود.
                            </p>
                        @else
                        <div class="mb-3">
                            <label class="form-label" for="contact-mobile">
                                شماره موبایل
                                <i class="fa fa-mobile-alt me-2 custom-cl-primary"></i>
                            </label>
                            <input type="text" name="mobile" id="contact-mobile" class="form-control"
                                value="{{ old('mobile') }}" placeholder="09123456789" maxlength="11"
                                inputmode="numeric" autocomplete="tel" required>
                            @error('mobile')
                                <span class="text-danger d-block mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label" for="contact-subject">
                                موضوع
                                <i class="fa fa-info-circle me-2 custom-cl-primary"></i>
                            </label>
                            <input type="text" name="subject" id="contact-subject" class="form-control"
                                value="{{ old('subject') }}" placeholder="عنوان پیام" maxlength="120" required>
                            @error('subject')
                                <span class="text-danger d-block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="contact-message">
                                متن پیام
                                <i class="fa fa-comment-dots me-2 custom-cl-primary"></i>
                            </label>
                            <textarea name="message" id="contact-message" class="form-control" rows="5"
                                placeholder="پیام خود را بنویسید..." maxlength="2000" required>{{ old('message') }}</textarea>
                            @error('message')
                                <span class="text-danger d-block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn w-100 text-white mt-5 custom-primary">
                            ارسال پیام
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- اسکریپت نقشه Leaflet -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const map = L.map('map').setView([32.689828, 52.0288907], 13);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {}).addTo(map);

            L.marker([32.678398, 52.0318907]).addTo(map)
                .bindPopup('شارین پارس ایرانیان')
                .openPopup();

        });
    </script>


@endsection
