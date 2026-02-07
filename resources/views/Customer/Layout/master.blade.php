<!DOCTYPE html>
<html lang="en">

<head>
    @yield('meta')
    @include('Customer.Layout.head-tag')
    @yield('head-tag')
</head>

<body class="index-page sidebar-collapse">

<!-- responsive-header -->
@include('Customer.Layout.navbar')



<!-- responsive-header -->

<div class="wrapper default">
    <!-- header -->
    @include('Customer.Layout.header')

    <!-- header -->

    @yield('content')

    <!-- footer -->
    @include('Customer.Layout.footer')

    <!-- footer -->
</div>

@include('Customer.Layout.script')
@yield('script')

@include('Customer.Alerts.Sweetalert.error')
@include('Customer.Alerts.Sweetalert.success')

<!-- ثبت سرویس ورکر -->

<!-- <script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js')
                .then(reg => console.log('Service Worker ثبت شد', reg))
                .catch(err => console.log('خطا در ثبت SW', err));
        });
    }
</script> -->



</body>
</html>

