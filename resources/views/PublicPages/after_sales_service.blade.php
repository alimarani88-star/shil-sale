@extends('Customer.Layout.master')
@section('title', 'درباره شرکت شیل ایران | پیشگام صنعت برق ایران')

@section('meta')
    <meta name="description"
          content="آشنایی با شرکت شیل ایران، تولیدکننده تجهیزات صنعت برق با استانداردهای بین‌المللی. خدمات با کیفیت، تیم حرفه‌ای، و راه‌حل‌های خلاقانه.">
    <meta name="keywords"
          content="شیل ایران, صنعت برق, کلید مینیاتوری, کنتاکتور, کلید اتوماتیک, تولید تجهیزات برق, استاندارد IEC, شرکت برقی, تولید ملی">
    <link rel="canonical" href="{{ url()->current() }}"/>
@endsection

@section('content')
    <main class="single-product default">
        <div class="row mb-5 p-5 rounded-4 text-white text-center align-items-center shadow-lg"
             style="background: linear-gradient(135deg, var(--custom-primary-color), #00293c);">
            <div class="col">
                <h1 class="display-5 fw-bold">خدمات پس از فروش</h1>
                <p class="lead">با افتخار، پیشگام در صنعت برق کشور</p>
            </div>
        </div>

        <div class="container">
            <div class="row justify-content-center g-4 mb-5">
                <!-- ایکون باکس ثبت گارانتی -->
                <div class="col-md-4 col-sm-6">
                    <a href="https://app.shiliran.ir/warranty" class="text-decoration-none">
                        <div class="card border-0 shadow-sm hover-shadow text-center h-100 service-box">
                            <div class="card-body p-4">
                                <div class="icon-wrapper mb-3">
                                    <i class="fas fa-file-contract fa-3x" style="color: #0c7549;"></i>
                                </div>
                                <h5 class="card-title fw-bold" style="color: #0a5c3a;">ثبت گارانتی</h5>
                                <p class="card-text text-muted">
                                    ثبت اطلاعات گارانتی محصولات شیل ایران برای دریافت خدمات پس از فروش
                                </p>
                                <span class="btn btn-outline-success mt-2">
                                    ورود به صفحه <i class="fas fa-arrow-left ms-1"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                </div>

               @if($group_id != null)
                    <div class="col-md-4 col-sm-6">
                        <a href="{{ route('product_guide' , ['idOrSlug'=>$group_id]) }}" class="text-decoration-none">
                            <div class="card border-0 shadow-sm hover-shadow text-center h-100 service-box">
                                <div class="card-body p-4">
                                    <div class="icon-wrapper mb-3">
                                        <i class="fas fa-book-open fa-3x" style="color: #007bff;"></i>
                                    </div>
                                    <h5 class="card-title fw-bold" style="color: #0056b3;">راهنمای محصول</h5>
                                    <p class="card-text text-muted">
                                        دسترسی به راهنمای استفاده، کاتالوگ و مشخصات فنی محصولات شیل ایران
                                    </p>
                                    <span class="btn btn-outline-primary mt-2">
                                    مشاهده راهنما <i class="fas fa-arrow-left ms-1"></i>
                                </span>
                                </div>
                            </div>
                        </a>
                    </div>
               @endif

                <div class="col-md-4 col-sm-6">
                    <a href="{{ url('/online-support') }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm hover-shadow text-center h-100 service-box">
                            <div class="card-body p-4">
                                <div class="icon-wrapper mb-3">
                                    <i class="fas fa-headset fa-3x" style="color: #ff6b35;"></i>
                                </div>
                                <h5 class="card-title fw-bold" style="color: #d45a2a;">پشتیبانی آنلاین</h5>
                                <p class="card-text text-muted">
                                    ارتباط مستقیم با تیم پشتیبانی فنی شیل ایران برای پاسخ به سوالات شما
                                </p>
                                <span class="btn btn-outline-warning mt-2" style="border-color: #ff6b35; color: #ff6b35;">
                                    گفتگو آنلاین <i class="fas fa-arrow-left ms-1"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h3 class="card-title text-center mb-4" style="color: #0c7549;">خدمات پس از فروش شیل ایران</h3>
                            <p class="lead text-center">
                                شرکت شیل ایران با تکیه بر تیم متخصص و متعهد خود، خدمات جامع پس از فروش را به مشتریان محترم ارائه می‌دهد.
                                کیفیت محصولات و رضایت مشتریان، اولویت اصلی ماست.
                            </p>

                            <div class="row mt-4">
                                <div class="col-md-4 text-center">
                                    <i class="fas fa-shield-alt fa-2x mb-3" style="color: #0c7549;"></i>
                                    <h6>گارانتی معتبر</h6>
                                    <p class="small text-muted">گارانتی اصلی محصولات با پوشش کامل</p>
                                </div>
                                <div class="col-md-4 text-center">
                                    <i class="fas fa-tools fa-2x mb-3" style="color: #0c7549;"></i>
                                    <h6>پشتیبانی فنی</h6>
                                    <p class="small text-muted">پشتیبانی 24 ساعته توسط مهندسین مجرب</p>
                                </div>
                                <div class="col-md-4 text-center">
                                    <i class="fas fa-shipping-fast fa-2x mb-3" style="color: #0c7549;"></i>
                                    <h6>خدمات سریع</h6>
                                    <p class="small text-muted">ارسال قطعات و خدمات در کوتاه‌ترین زمان</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('styles')
    <style>
        .service-box {
            transition: all 0.3s ease;
            border-radius: 15px;
            overflow: hidden;
            background: linear-gradient(145deg, #ffffff, #f8f9fa);
        }

        .service-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
        }

        .service-box .icon-wrapper {
            padding: 20px;
            background: rgba(12, 117, 73, 0.1);
            border-radius: 50%;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .service-box:hover .icon-wrapper {
            background: rgba(12, 117, 73, 0.2);
            transform: scale(1.1);
        }

        .hover-shadow {
            transition: box-shadow 0.3s ease;
        }

        .hover-shadow:hover {
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        }

        /* انیمیشن برای دکمه‌ها */
        .service-box .btn {
            transition: all 0.3s ease;
        }

        .service-box:hover .btn-outline-success {
            background-color: #0c7549;
            color: white !important;
        }

        .service-box:hover .btn-outline-primary {
            background-color: #007bff;
            color: white !important;
        }

        .service-box:hover .btn-outline-warning {
            background-color: #ff6b35;
            color: white !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // اسکریپت برای افکت hover
        document.addEventListener('DOMContentLoaded', function() {
            const serviceBoxes = document.querySelectorAll('.service-box');

            serviceBoxes.forEach(box => {
                box.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-10px)';
                });

                box.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
@endpush
