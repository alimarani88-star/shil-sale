@extends('Customer.Layout.master')

@section('head-tag')
    <link rel="stylesheet" href="{{asset('assets/post_assets/css/font-icons.css')}}"/>
{{--    <link rel="stylesheet" href="{{asset('assets/post_assets/css/style.css')}}"/>--}}
    <style>
        .highlight {
            background-color: yellow; /* رنگ بک‌گراند */
            padding: 0 2px;
            border-radius: 3px;
        }
    </style>
@endsection
@section('content')

    <div class="loader-mask">
        <div class="loader">
            <div></div>
        </div>
    </div>
    <div class="content-overlay"></div>
    <main class="main default">
        <!-- Trending Now -->


        <div class="main-container container pt-24" id="main-container">


            <!-- Content -->
            <div class="row flex-column-reverse flex-lg-row">

                <div class="col-12 mb-4">
                    <form action="{{ route('search') }}" method="GET" class="form-group position-relative">
                        <input type="text" name="q"
                               class="form-control bg-white py-0 m-0"
                               placeholder="جست و جو در پست ها و دسته ها"
                               value="{{ request('q') }}">

                        <button type="submit"
                                class="position-absolute custom-primary btn m-0"
                                style="left: 0; top:0; border-radius: 20px 0 0 20px;">
                            <svg width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0
                                1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12
                                6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                            </svg>
                        </button>
                    </form>

                </div>
                <!-- Posts -->
                <div class="col-lg-8 blog__content">


                    <section class="section tab-post mb-16 bg-transparent pt-0">

                        <h1 class="page-title">نتیجه جست و جو برای : {{ $query }}</h1>
                        @if($posts->count())
                            @foreach($posts as $post)
                                <article class="entry card post-list result-text">
                                    <div class="entry__img-holder post-list__img-holder card__img-holder"
                                         style="background-image: url({{$post->main_image ? asset($post->main_image?->image_url) : asset('assets/img/no-image.jpg')}})">
                                        <a href="#" class="thumb-url"></a>
                                        <img src="{{$post->main_image ? asset($post->main_image?->image_url) : asset('assets/img/no-image.jpg')}}"
                                             alt="" class="entry__img d-none">

                                        @if($post->main_category)
                                            <a href="categories.html"
                                               class="entry__meta-category entry__meta-category--label entry__meta-category--align-in-corner entry__meta-category--blue">{{$post->main_category}}</a>
                                        @endif

                                    </div>

                                    <div class="entry__body post-list__body card__body">
                                        <div class="entry__header">
                                            <h2 class="entry__title">
                                                <a href="{{route('post' , ['slug'=>$post->slug])}}">{{$post->title}}</a>
                                            </h2>
                                            <ul class="entry__meta">
                                                <li class="entry__meta-author">
                                                    <span>نویسنده:</span>
                                                    <a href="#">{{$post->author_name}}</a>
                                                </li>
                                                <li class="entry__meta-date">
                                                    {{$post->published}}
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="entry__excerpt">
                                            <p>{{$post->summary}} ...</p>
                                        </div>
                                    </div>
                                </article>
                            @endforeach

                            {{ $posts->links('pagination::bootstrap-5') }}
                        @else
                            <p class="text-muted">نتیجه‌ای یافت نشد.</p>
                        @endif


                    </section>
                    {{ $posts->links('pagination::bootstrap-4') }}
                </div> <!-- end posts -->

                <!-- Sidebar -->
                <aside class="col-lg-4 sidebar sidebar--left pt-lg-5 mt-lg-4">
                    <aside class="widget widget_categories">
                        <h4 class="widget-title">دسته بندی نوشته ها</h4>
                        <ul>
                            <li><a href="categories.html">تکنولوژی <span class="categories-count">۵</span></a></li>
                            <li><a href="categories.html">فرهنگ و هنر <span class="categories-count">۷</span></a></li>
                            <li><a href="categories.html">موبایل و گجت <span class="categories-count">۵</span></a></li>
                            <li><a href="categories.html">کتاب <span class="categories-count">۸</span></a></li>
                            <li><a href="categories.html">سامت و زیبایی <span class="categories-count">۱۰</span></a>
                            </li>
                            <li><a href="categories.html">سبک زندگی <span class="categories-count">۶</span></a></li>
                        </ul>
                    </aside>
                </aside> <!-- end sidebar -->

            </div> <!-- end content -->


        </div> <!-- end main container -->

    </main>

    <!-- main -->

@endsection




@section('scripts')
    <script src="{{asset('assets/post_assets/js/easing.min.js')}}"></script>

    <script src="{{asset('assets/post_assets/js/flickity.pkgd.min.js')}}"></script>
    <script src="{{asset('assets/post_assets/js/twitterFetcher_min.js')}}"></script>
    <script src="{{asset('assets/post_assets/js/jquery.newsTicker.min.js')}}"></script>
    <script src="{{asset('assets/post_assets/js/modernizr.min.js')}}"></script>
    <script src="{{asset('assets/post_assets/js/scripts.js')}}"></script>

    <script>
        $(document).ready(function () {
            let query = "{{ $query }}"; // مقدار سرچ شده از سمت سرور
            if (query.trim() !== "") {
                $(".result-text").each(function () {
                    let content = $(this).html();
                    let regex = new RegExp("(" + query + ")", "gi");
                    let highlighted = content.replace(regex, '<span style="background-color: yellow;">$1</span>');
                    $(this).html(highlighted);
                });
            }
        });
    </script>
@endsection

