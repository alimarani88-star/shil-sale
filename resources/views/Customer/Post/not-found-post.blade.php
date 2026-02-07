@extends('Customer.Layout.master')

@section('head-tag')
    <link rel="stylesheet" href="{{asset('assets/post_assets/css/font-icons.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/post_assets/css/style.css')}}"/>
    <style>

    </style>
@endsection
@section('content')
    <!-- Preloader -->
    <div class="loader-mask">
        <div class="loader">
            <div></div>
        </div>
    </div>
    <div class="content-overlay"></div>
    <!-- main -->
    <div class="content-overlay"></div>
    {{--    <main class="main default">--}}
    <div class="main-container container pt-250 pb-80" id="main-container">
        <!-- post content -->
        <div class="blog__content mb-72">
            <div class="container text-center">
                <h1 class="page-404-number">404</h1>
                <h2>صفحه یافت نشد</h2>
                <p>نگران نباشید.جستجو کنید...</p>

                <div class="row justify-content-center mt-40">

                    <div class="col-md-6">
                        <!-- Search form -->
                        <form role="search" method="get" class="search-form relative">
                            <input type="search" class="widget-search-input mb-0" placeholder="جستجو مقاله">
                            <button type="submit" class="widget-search-button btn btn-color"><i
                                        class="ui-search widget-search-icon"></i></button>
                        </form>
                    </div>

                </div> <!-- end row -->

            </div> <!-- end container -->
        </div> <!-- end post content -->
    </div>
    {{--    </main>--}}

    <!-- main -->

@endsection



@section('scripts')
    <script src="{{asset('assets/post_assets/js/easing.min.js')}}"></script>
    <script src="{{asset('assets/post_assets/js/owl-carousel.min.js')}}"></script>
    <script src="{{asset('assets/post_assets/js/flickity.pkgd.min.js')}}"></script>
    <script src="{{asset('assets/post_assets/js/twitterFetcher_min.js')}}"></script>
    <script src="{{asset('assets/post_assets/js/jquery.newsTicker.min.js')}}"></script>
    <script src="{{asset('assets/post_assets/js/modernizr.min.js')}}"></script>
    <script src="{{asset('assets/post_assets/js/scripts.js')}}"></script>
@endsection

