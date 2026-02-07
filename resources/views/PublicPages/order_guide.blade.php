@extends('Customer.Layout.master')
@section('title', 'راهنمای سفارشات شرکت شیل ایران | پیشگام صنعت برق ایران')

@section('meta')
    <meta name="description"
        content="آشنایی با شرکت شیل ایران، تولیدکننده تجهیزات صنعت برق با استانداردهای بین‌المللی. خدمات با کیفیت، تیم حرفه‌ای، و راه‌حل‌های خلاقانه.">
    <meta name="keywords"
        content="شیل ایران, صنعت برق, کلید مینیاتوری, کنتاکتور, کلید اتوماتیک, تولید تجهیزات برق, استاندارد IEC, شرکت برقی, تولید ملی">
    <link rel="canonical" href="{{ url()->current() }}" />
@endsection

@section('content')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .container-Pm {
            min-height: 100vh;
            padding: 80px 20px;
            position: relative;
            overflow: hidden;
        }

        .container-Pm::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            /* background:
                                radial-gradient(circle at 20% 30%, rgba(138, 43, 226, 0.15) 0%, transparent 50%),
                                radial-gradient(circle at 80% 70%, rgba(147, 51, 234, 0.15) 0%, transparent 50%); */
            pointer-events: none;
        }

        .container-pm {
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .header {
            text-align: center;
            margin-bottom: 100px;
        }

        .header h1 {
            color: #fff;
            font-size: 3.5em;
            font-weight: 700;
            margin-bottom: 16px;
            background: linear-gradient(135deg, var(--custom-primary-color), var(--custom-primary-color-liner-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.3em;
            font-weight: 300;
        }

        .spiral-timeline {
            position: relative;
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
        }

        .timeline-step {
            position: relative;
            margin-bottom: 120px;
            opacity: 0;
            animation: slideInSpiral 1s ease forwards;
        }

        .timeline-step:nth-child(1) {
            animation-delay: 0.3s;
            transform-origin: right center;
        }

        .timeline-step:nth-child(2) {
            animation-delay: 0.6s;
            transform-origin: left center;
        }

        .timeline-step:nth-child(3) {
            animation-delay: 0.9s;
            transform-origin: right center;
        }

        @keyframes slideInSpiral {
            from {
                opacity: 0;
                transform: scale(0.8) rotate(-5deg);
            }

            to {
                opacity: 1;
                transform: scale(1) rotate(0deg);
            }
        }

        .step-container {
            display: flex;
            align-items: center;
            gap: 60px;
            position: relative;
        }

        .timeline-step:nth-child(odd) .step-container {
            flex-direction: row;
            justify-content: flex-end;
        }

        .timeline-step:nth-child(even) .step-container {
            flex-direction: row-reverse;
            justify-content: flex-end;
        }


        @keyframes growLine {
            from {
                transform: scaleX(0);
            }

            to {
                transform: scaleX(1);
            }
        }

        .step-number-wrapper {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
        }

        .step-number {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--custom-primary-color), var(--custom-primary-color-liner-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2em;
            font-weight: 800;
            color: white;
            box-shadow: 0 0 30px rgba(168, 85, 247, 0.5),
                0 0 60px rgba(236, 72, 153, 0.3);
            border: 4px solid rgba(255, 255, 255, 0.2);
            position: relative;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                box-shadow: 0 0 30px rgba(168, 85, 247, 0.5),
                    0 0 60px rgba(236, 72, 153, 0.3);
            }

            50% {
                transform: scale(1.05);
                box-shadow: 0 0 40px rgba(168, 85, 247, 0.7),
                    0 0 80px rgba(236, 72, 153, 0.5);
            }
        }

        .step-number::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 2px solid var(--custom-primary-color);
            animation: ripple 2s infinite;
        }

        @keyframes ripple {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            100% {
                transform: scale(1.5);
                opacity: 0;
            }
        }

        .step-card {
            background: linear-gradient(135deg, var(--custom-primary-color) 0%, var(--custom-primary-color-liner-middle) 50%, var(--custom-primary-color-liner-dark) 100%);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(168, 85, 247, 0.3);
            border-radius: 24px;
            padding: 40px;
            width: 450px;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .step-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg,
                    transparent,
                    rgba(168, 85, 247, 0.1),
                    transparent);
            transition: left 0.5s ease;
        }

        .step-card:hover::before {
            left: 100%;
        }

        .step-card:hover {
            border-color: rgba(168, 85, 247, 0.6);
            box-shadow: 0 20px 60px rgba(168, 85, 247, 0.3),
                inset 0 0 80px rgba(168, 85, 247, 0.1);
            transform: translateY(-8px);
        }

        .step-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 24px;
        }

        .step-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, rgba(168, 85, 247, 0.2), rgba(236, 72, 153, 0.2));
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2em;
            color: #fff;
            transition: all 0.3s ease;
        }

        .step-card:hover .step-icon {
            transform: scale(1.1) rotate(-5deg);
            background: linear-gradient(135deg, rgba(168, 85, 247, 0.3), rgba(236, 72, 153, 0.3));
        }

        .step-title {
            color: #fff;
            font-size: 1.8em;
            font-weight: 600;
            margin: 0;
        }

        .step-description {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.8;
            font-size: 1.05em;
            margin-bottom: 28px;
        }

        .step-features {
            display: grid;
            gap: 14px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }

        .feature-item:hover {
            background: rgba(168, 85, 247, 0.1);
            border-color: rgba(168, 85, 247, 0.3);
            transform: translateX(-5px);
        }

        .feature-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--custom-primary-color), var(--custom-primary-color-liner-dark));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1em;
            color: white;
            flex-shrink: 0;
        }

        .feature-text {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.95em;
        }



        .completion::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(168, 85, 247, 0.3) 0%, transparent 70%);
            animation: rotate 10s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: translate(-50%, -50%) rotate(0deg);
            }

            to {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        .completion h2 {
            color: #fff;
            font-size: 2.5em;
            font-weight: 700;
            margin-bottom: 16px;
            background: linear-gradient(135deg, #ffffffff 0%, #f0f0f0ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
            z-index: 1;
        }

        .completion p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.2em;
            position: relative;
            z-index: 1;
        }

        .completion-icon {
            font-size: 3.5em;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #FFF, #fff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
            z-index: 1;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        @media (max-width: 1024px) {
            .step-card {
                width: 400px;
            }

        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2.5em;
            }

            .timeline-step {
                margin-bottom: 100px;
            }

            .step-container {
                flex-direction: column !important;
                gap: 30px;
            }


            .step-number-wrapper {
                position: relative;
                left: auto;
                transform: none;
            }

            .step-card {
                width: 100%;
            }

            .step-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .step-title {
                font-size: 1.5em;
            }
        }

        @media (max-width: 480px) {
            .header h1 {
                font-size: 2em;
            }

            .step-number {
                width: 60px;
                height: 60px;
                font-size: 1.5em;
            }

            .step-card {
                padding: 28px 20px;
            }

            .step-icon {
                width: 60px;
                height: 60px;
                font-size: 1.8em;
            }

            .completion h2 {
                font-size: 2em;
            }

            .completion-icon {
                font-size: 2.5em;
            }
        }
    </style>

    <div class="container-Pm">
        <div class="container-pm">
            <div class="header">
                <h1>مسیر سفارش شما</h1>
                <p>از انتخاب تا دریافت، هر قدم با شماست</p>
            </div>

            <div class="spiral-timeline">
                <div class="timeline-step">
                    <div class="step-container">
                        <div class="step-number-wrapper">
                            <div class="step-number">01</div>
                        </div>
                        <div class="step-card">
                            <div class="step-header">
                                <div class="step-icon">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <h2 class="step-title">ثبت سفارش</h2>
                            </div>
                            <p class="step-description">
                                محصولات مورد علاقه خود را با چند کلیک ساده انتخاب کنید و تجربه خرید لذت‌بخشی داشته باشید
                            </p>
                            <div class="step-features">
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <i class="fas fa-search"></i>
                                    </div>
                                    <div class="feature-text">جستجو و مقایسه هوشمند محصولات</div>
                                </div>
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <i class="fas fa-cart-plus"></i>
                                    </div>
                                    <div class="feature-text">افزودن سریع به سبد خرید</div>
                                </div>
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <i class="fas fa-edit"></i>
                                    </div>
                                    <div class="feature-text">ثبت اطلاعات با رابط کاربری ساده</div>
                                </div>
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <i class="fas fa-eye"></i>
                                    </div>
                                    <div class="feature-text">پیش‌نمایش و بررسی نهایی سفارش</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="timeline-step">
                    <div class="step-container">
                        <div class="step-number-wrapper">
                            <div class="step-number">02</div>
                        </div>
                        <div class="step-card">
                            <div class="step-header">
                                <div class="step-icon">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <h2 class="step-title">پرداخت امن</h2>
                            </div>
                            <p class="step-description">
                                پرداخت سریع و امن با بالاترین استانداردهای امنیتی و دریافت فوری تاییدیه
                            </p>
                            <div class="step-features">
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <div class="feature-text">درگاه پرداخت امن و رمزگذاری شده</div>
                                </div>
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <i class="fas fa-bolt"></i>
                                    </div>
                                    <div class="feature-text">پردازش آنی و دریافت کد رهگیری</div>
                                </div>
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <i class="fas fa-mobile-alt"></i>
                                    </div>
                                    <div class="feature-text">اطلاع‌رسانی لحظه‌ای از طریق پیامک</div>
                                </div>
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                    <div class="feature-text">امکان پرداخت اقساطی و اعتباری</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="timeline-step">
                    <div class="step-container">
                        <div class="step-number-wrapper">
                            <div class="step-number">03</div>
                        </div>
                        <div class="step-card">
                            <div class="step-header">
                                <div class="step-icon">
                                    <i class="fas fa-rocket"></i>
                                </div>
                                <h2 class="step-title">ارسال و تحویل</h2>
                            </div>
                            <p class="step-description">
                                بسته‌بندی حرفه‌ای و ارسال فوری با امکان رهگیری لحظه‌ای مرسوله شما
                            </p>
                            <div class="step-features">
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <i class="fas fa-box"></i>
                                    </div>
                                    <div class="feature-text">بسته‌بندی ضد ضربه و زیبا</div>
                                </div>
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <i class="fas fa-truck"></i>
                                    </div>
                                    <div class="feature-text">ارسال سریع با بهترین باربری‌ها</div>
                                </div>
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <i class="fas fa-map-marked-alt"></i>
                                    </div>
                                    <div class="feature-text">رهگیری آنلاین و لحظه‌ای مرسوله</div>
                                </div>
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <i class="fas fa-home"></i>
                                    </div>
                                    <div class="feature-text">تحویل درب منزل با تماس قبلی</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="completion">
                    <div class="completion-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h2>دریافت سفارش</h2>
                    <p>کیفیت بالا، تحویل سریع، رضایت تضمینی</p>
                </div>
            </div>
        </div>
    </div>
@endsection