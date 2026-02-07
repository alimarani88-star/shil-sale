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
                <h1 class="display-5 fw-bold">درباره ما</h1>
                <p class="lead">با افتخار، پیشگام در صنعت برق کشور</p>
            </div>
        </div>
        <div class="container">

            <!-- Hero Section -->


            <!-- Why Shil Iran -->
            <div class="row align-items-center mb-5 mr-1">
                <div class="col-md-6 row">
                    <div class="row about-title mr-2">
                        <h3 class="fw-bold mb-3">چرا </h3>
                        <h3 class="mr-2 custom-cl-primary">شیل ایران؟</h3>
                    </div>
                    {!! $company_info['text_about'] !!}
                </div>
                <div class="col-md-6">
                    <img src="{{ asset('assets/img/about/20943574.jpg') }}" alt=" درباره شیل ایران"
                         class="img-fluid rounded-4 moving-image">
                </div>
            </div>

            <!-- Standards -->
            <div class="row text-center mb-5">
                <div class="col-md-12">
                    <h2 class="fw-bold">استاندارد های ما</h2>
                    <p class="text-muted">تمام تلاش ما در گروه شیل ایران در جهت تولید محصولاتی با کیفیت و قابل مقایسه با
                        انواع خارجی آن است.</p>
                </div>
            </div>

            <!-- Features -->
            <div class="row text-center mb-5">
                <div class="col-md-3 mb-4">
                    <div class="p-4 rounded-4  hover-box shadow-lg">
                        <div class="icon icon-about mb-3">
                            <i class="fas fa-solid fa-house custom-primary"></i>
                        </div>
                        <h5 class="fw-bold">خدمات عالی</h5>
                        <p class="text-muted small text-justify">
                            کلید رشد و کامیابی هر شرکتی در ارائه خدمات عالی به مشتریان است. خدمات پس از فروش و گارانتی
                            معتبر
                            راهی برای اثبات وفاداری ما است.
                        </p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="p-4 rounded-4  hover-box shadow-lg">
                        <div class="icon icon-about mb-3">
                            <i class="fas fa-solid fa-atom custom-primary"></i>
                        </div>
                        <h5 class="fw-bold">بالاترین استانداردها</h5>
                        <p class="text-muted small text-justify">
                            شیل ایران با بهره‌گیری از آزمایشگاه‌های پیشرفته موفق به تولید مطابق با استانداردهای
                            بین‌المللی و
                            اخذ تاییدیه توانیر شده است.
                        </p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="p-4 rounded-4  hover-box  shadow-lg">
                        <div class="icon icon-about mb-3">
                            <i class="fas fa-solid fa-users custom-primary"></i>
                        </div>
                        <h5 class="fw-bold">تیم قدرتمند</h5>
                        <p class="text-muted small text-justify">
                            ما در شیل ایران با تشکیل تیمی از بهترین‌های صنعت برق، روحیه همکاری و تخصص را در کنار هم به
                            کار
                            گرفته‌ایم.
                        </p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="p-4 rounded-4  hover-box h-100  shadow-lg">
                        <div class="icon icon-about mb-3">
                            <i class="fas fa-solid fa-lightbulb custom-primary"></i>
                        </div>
                        <h5 class="fw-bold">راه حل‌های خلاقانه</h5>
                        <p class="text-muted small text-justify">
                            ایده‌های نو و اجرای آن‌ها کلید تنوع محصولات و پاسخ به نیازهای متغیر بازار است.
                        </p>
                    </div>
                </div>
            </div>
            <!-- Standards -->
            <div class="row text-center mb-5">
                <div class="col-md-12">
                    <h2 class="fw-bold mt-4">گواهینامه ها</h2>

                </div>
            </div>

            <!-- Features -->
            <div class="row text-center mb-5">
                <div class="col-md-3 mb-4">
                    <a href="{{ asset('assets/img/sertificates/sertificate1.jpg') }}" target="_blank"
                       rel="noopener noreferrer">
                        <img src="{{ asset('assets/img/sertificates/sertificate1.jpg') }}"
                             alt="گواهینامه کلید مینیاتوری KA 10 شیلیران" class="img-fluid"
                             style="border-radius: 15px;">
                    </a>
                </div>
                <div class="col-md-3 mb-4">
                    <a href="{{ asset('assets/img/sertificates/sertificate2.jpg') }}" target="_blank"
                       rel="noopener noreferrer">
                        <img src="{{ asset('assets/img/sertificates/sertificate2.jpg') }}"
                             alt="گواهینامه مینیاتوری KA 6 شیلیران" class="img-fluid" style="border-radius: 15px;">
                    </a>
                </div>
                <div class="col-md-3 mb-4">
                    <a href="{{ asset('assets/img/sertificates/sertificate3.jpg') }}" target="_blank"
                       rel="noopener noreferrer">
                        <img src="{{ asset('assets/img/sertificates/sertificate3.jpg') }}"
                             alt="گواهینامه کنتاکتور شیلیران"
                             class="img-fluid" style="border-radius: 15px;">
                    </a>
                </div>
                <div class="col-md-3 mb-4">
                    <a href="{{ asset('assets/img/sertificates/sertificate4.jpg') }}" target="_blank"
                       rel="noopener noreferrer">
                        <img src="{{ asset('assets/img/sertificates/sertificate4.jpg') }}"
                             alt="گواهینامه کلید اتوماتیک شیلیران" class="img-fluid" style="border-radius: 15px;">
                    </a>
                </div>
            </div>


            <div class="row text-center">
                <div class="col-md-12">
                    <a href="{{ route('contact') }}" class="btn btn-lg rounded-pill px-5 text-white custom-primary">ارتباط
                        با ما</a>
                </div>
            </div>

        </div>
    </main>
@endsection
