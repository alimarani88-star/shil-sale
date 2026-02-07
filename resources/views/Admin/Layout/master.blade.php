<!DOCTYPE html>
<html dir="rtl">

<head>
    @include('Admin.Layout.head-tag')
    @yield('head-tag')

</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">

    @include('Admin.Layout.header')

    @include('Admin.Layout.sidebar')
    <div class="content-wrapper">
        <section class="content">


            @yield('content')

        </section>

    </div>
    @include('Admin.Layout.footer')

</div>


@include('Admin.Layout.script')
@yield('script')

<section class="toast-wrapper flex-row-reverse">
    @include('Admin.Alerts.Toast.success')
    @include('Admin.Alerts.Toast.error')
</section>

@include('Admin.Alerts.Sweetalert.error')
@include('Admin.Alerts.Sweetalert.success')

</body>
</html>
