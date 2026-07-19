    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'فروشگاه محصولات شیل ایران')</title>
    @php
        $seoBaseUrl = 'https://www.shil.ir';
        $metaContent = $__env->yieldContent('meta');
        $hasCanonicalMeta = stripos($metaContent, 'rel="canonical"') !== false
            || stripos($metaContent, "rel='canonical'") !== false;
        $hasRobotsMeta = stripos($metaContent, 'name="robots"') !== false
            || stripos($metaContent, "name='robots'") !== false;

        $canonicalUrl = trim($__env->yieldContent('canonical'));
        if ($canonicalUrl === '' && ! $hasCanonicalMeta) {
            $path = parse_url(request()->getRequestUri(), PHP_URL_PATH) ?: '/';
            $path = $path === '/' ? '/' : rtrim($path, '/');
            $canonicalUrl = $seoBaseUrl . $path;
        }

        $robotsContent = trim($__env->yieldContent('robots'));
    @endphp
    @if(! $hasCanonicalMeta && $canonicalUrl !== '')
        <link rel="canonical" href="{{ $canonicalUrl }}">
    @endif
    @if(! $hasRobotsMeta && $robotsContent !== '')
        <meta name="robots" content="{{ $robotsContent }}">
    @endif
    <!-- Icons -->
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/img/logo-icon.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo-icon.png') }}">

    <!-- Fonts and icons -->
    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome.min.css') }}"/>

    <!-- Core CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/css/now-ui-kit.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet"/>
    <link href="{{ asset('customer-assets/css/style.css') }}" rel="stylesheet"/>

    <!-- Plugins CSS -->
    <link href="{{ asset('assets/css/plugins/owl.carousel.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/css/plugins/owl.theme.default.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/css/plugins/bootstrap-slider.min.css') }}" rel="stylesheet"/>
    <link rel="stylesheet" href="{{asset('customer-assets/sweetalert/sweetalert2.css')}}">
    <link href="{{ asset('assets/css/plugins/AddTags.css') }}" rel="stylesheet"/>
    <link rel="stylesheet" href="{{asset('customer-assets/select2/css/select2.min.css') }}">

    <style>
        .online-support-link {
            position: fixed;
            right: 24px;
            bottom: 24px;
            z-index: 1040;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 50px;
            padding: 0 22px;
            border-radius: 999px;
            background: #6a1b9a;
            color: #fff;
            box-shadow: 0 10px 24px rgba(106, 27, 154, 0.28);
            font-size: 16px;
            font-weight: 700;
            line-height: 1;
            transition: transform .2s ease, box-shadow .2s ease, background-color .2s ease;
        }

        .online-support-link:hover,
        .online-support-link:focus {
            color: #fff;
            background: #7b22b3;
            box-shadow: 0 12px 28px rgba(106, 27, 154, 0.34);
            text-decoration: none;
            transform: translateY(-2px);
        }

        .online-support-link i {
            font-size: 21px;
        }

        @media (max-width: 575.98px) {
            .online-support-link {
                right: 16px;
                bottom: 16px;
                min-height: 44px;
                padding: 0 16px;
                font-size: 14px;
            }

            .online-support-link i {
                font-size: 18px;
            }
        }
    </style>

    <link rel="manifest" href="{{ asset('manifest.json') }}">
    
    <meta name="theme-color" content="#6366f1">

    <!-- آیکون‌های PWA -->
    <link rel="apple-touch-icon" sizes="192x192" href="/pwa/icons/logo1.png">
    <link rel="apple-touch-icon" sizes="512x512" href="/pwa/icons/logo1.png">

   
