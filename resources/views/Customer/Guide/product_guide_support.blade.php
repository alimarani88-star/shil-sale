@extends('Customer.Layout.master')

@section('head-tag')
    <!-- <link rel="stylesheet" href="{{ asset('assets/post_assets/css/font-icons.css') }}" /> -->
    <!-- <link rel="stylesheet" href="{{ asset('assets/post_assets/css/style.css') }}" /> -->

    <style>

        .post-hero {
            position: relative;
            min-height: 320px;
            border-radius: 15px;
            overflow: hidden;
            background: #000 center/cover no-repeat;
        }

        .post-hero .overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, .0), rgba(0, 0, 0, .95));
        }

        .post-hero .hero-content {
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            padding: 28px;
            z-index: 2;
        }

        .hero-title {
            font-weight: 700;
            text-shadow: 0 2px 8px rgba(0, 0, 0, .4);
            margin-top: 6px;
            color: #fff0ff;
            text-align: center;
        }

        /* کارت‌ها و فیلدهای گروه‌بندی شده – بدون Shadow روی زمینه سفید */
        .group-card {
            background: rgba(255, 255, 255, .05);
            border: 1px solid rgba(255, 255, 255, .12);
            padding: 0.75rem;
            /* shadow removed برای جلوگیری از shadow روی پس‌زمینه سفید */
            box-shadow: none;
        }


        @media (max-width: 991.98px) {
            .post-hero {
                min-height: 260px;
            }

            .hero-content {
                padding: 20px;
            }
        }

        .guide-section {
            padding: 2.5rem 0;
            background:
                radial-gradient(circle at 85% 10%, rgba(59, 130, 246, 0.12), transparent 35%),
                radial-gradient(circle at 15% 85%, rgba(16, 185, 129, 0.10), transparent 32%),
                linear-gradient(180deg, #f8fbff 0%, #f4f8ff 100%);
            border-radius: 18px;
        }

        .guide-card {
            height: 100%;
            border: 1px solid rgba(59, 130, 246, 0.18);
            border-radius: 18px;
            background: #ffffff;
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
            position: relative;
            overflow: hidden;
        }

        .guide-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #2563eb 0%, #0ea5e9 100%);
        }

        .support-card {
            background:
                radial-gradient(circle at 88% 12%, rgba(251, 146, 60, 0.22), transparent 40%),
                linear-gradient(155deg, #fffaf3 0%, #ffe8d6 100%);
            border-color: rgba(249, 115, 22, 0.28);
        }

        .support-card::before {
            background: linear-gradient(90deg, #ea580c 0%, #f59e0b 100%);
        }

        .manual-card {
            background:
                radial-gradient(circle at 12% 85%, rgba(16, 185, 129, 0.11), transparent 36%),
                linear-gradient(155deg, #ffffff 0%, #effcf6 100%);
            border-color: rgba(5, 150, 105, 0.22);
        }

        .manual-card::before {
            background: linear-gradient(90deg, #059669 0%, #22c55e 100%);
        }

        .guide-card:hover {
            transform: translateY(-6px);
            border-color: rgba(37, 99, 235, 0.35);
            box-shadow: 0 18px 35px rgba(30, 64, 175, 0.15);
        }

        .guide-card .card-body {
            padding: 1.6rem;
        }

        .guide-card-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: .75rem;
            color: #1f2937;
        }

        .guide-card-text {
            color: #4b5563;
            margin-bottom: 1rem;
            line-height: 1.8;
        }

        .guide-badge {
            display: inline-block;
            background: linear-gradient(90deg, #dbeafe 0%, #e0f2fe 100%);
            color: #1d4ed8;
            font-size: .85rem;
            font-weight: 600;
            padding: .38rem .78rem;
            border-radius: 999px;
            margin-bottom: .8rem;
        }

        .guide-card .btn {
            border-radius: 10px;
            padding: .55rem 1rem;
            font-weight: 600;
        }

        .guide-card .btn-primary {
            background: linear-gradient(90deg, #2563eb 0%, #1d4ed8 100%);
            border: none;
        }

        .guide-card .btn-outline-primary {
            border-color: #2563eb;
            color: #2563eb;
        }

        .guide-card .btn-outline-primary:hover {
            background-color: #2563eb;
            color: #fff;
        }

        @media (max-width: 767.98px) {
            .guide-section {
                border-radius: 14px;
                padding: 1.8rem .4rem;
            }
        }
    </style>
@endsection

@section('content')
    <main class="main default" role="main">
        <section class="guide-section">
            <div class="container">
                <div class="row g-4">
                    <div class="col-12 col-md-6">
                        <div class="card guide-card support-card">
                            <div class="card-body">
                                <span class="guide-badge">پشتیبانی</span>
                                <h2 class="guide-card-title">پشتیبانی آنلاین</h2>
                                <p class="guide-card-text">
                                    اگر در خرید یا استفاده از محصول سوال دارید، از طریق پشتیبانی آنلاین سریع‌ترین پاسخ را
                                    دریافت کنید.
                                </p>
                                <a href="https://gap.shiliran.ir/" class="btn btn-primary" target="_blank" rel="noopener noreferrer">شروع گفت‌وگو</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="card guide-card manual-card">
                            <div class="card-body">
                                <span class="guide-badge">آموزش</span>
                                <h2 class="guide-card-title">راهنمای محصول</h2>
                                <p class="guide-card-text">
                                    برای آشنایی کامل با امکانات و نحوه استفاده، راهنمای محصولات را مرحله‌به‌مرحله مشاهده
                                    کنید.
                                </p>
                                @if(!empty($guide_documents) && count($guide_documents) > 0)
                                @foreach($guide_documents as $guide_document)
                                    <a href="{{$guide_document->url}}" class="btn btn-outline-primary">{{$guide_document->name}}</a>
                                @endforeach
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
