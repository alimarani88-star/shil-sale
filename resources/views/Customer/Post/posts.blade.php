@extends('Customer.Layout.master')

@section('head-tag')
    <link rel="stylesheet" href="{{asset('assets/post_assets/css/font-icons.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/post_assets/css/style.css')}}"/>

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
                <!-- Posts -->
                <div class="col-lg-8 blog__content">

                    <form id="postFilterForm" action="{{ route('posts') }}" method="GET">
                        @if(request()->has('cat'))
                            <input type="hidden" name="cat" value="{{ request('cat') }}">
                        @endif
                        <div class="d-flex flex-row">
                            <div class="form-group" style="max-width: 150px">
                                <select name="orderBy" class="form-control bg-white" style="padding-right: 29px"
                                        onchange="this.form.submit()">
                                    <option value="publish" {{ request('orderBy') == 'publish' ? 'selected' : '' }}>
                                        جدیدترین
                                    </option>
                                    <option value="view" {{ request('orderBy') == 'view' ? 'selected' : '' }}>
                                        پر بازدیدترین
                                    </option>
                                </select>
                            </div>
                            <div class="form-group" style="max-width: 150px">
                                <select name="sort" class="form-control bg-white" style="padding-right: 29px"
                                        onchange="this.form.submit()">
                                    <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>نزولی
                                    </option>
                                    <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>صعودی</option>
                                </select>
                            </div>
                        </div>
                    </form>
                    @if($category_name)
                        <div class=" pb-2">
                            <h1 class="page-title">
                                {{$category_name}}
                            </h1>
                        </div>
                    @endif

                    <section class="section tab-post mb-16 bg-transparent pt-0">

                        <div class="row card-row  ">
                            @foreach($posts as $post)
                                <div class="col-md-6">
                                    <article class="entry card">
                                        <div class="entry__img-holder card__img-holder">
                                            <a href="{{route('post' , ['slug'=> $post->slug])}}">
                                                <div class="thumb-container thumb-70">
                                                    <img
                                                            data-src="{{$post->main_image ? route('get_post_image_by_id' , ['id'=> $post->main_image?->id]) : asset('assets/img/no-image.jpg')}}"
                                                            src="{{$post->main_image ? route('get_post_image_by_id' , ['id'=> $post->main_image?->id]) : asset('assets/img/no-image.jpg')}}"
                                                            class="entry__img lazyload"
                                                            alt="{{$post->main_image ? $post->main_image?->alt : $post->title}}"
                                                            title="{{$post->title}}"/>
                                                </div>
                                            </a>
                                            @if($post->main_category)
                                                <a href="#"
                                                   class="entry__meta-category entry__meta-category--label entry__meta-category--align-in-corner entry__meta-category--purple">{{$post->main_category}}</a>
                                            @endif

                                        </div>

                                        <div class="entry__body card__body">
                                            <div class="entry__header">

                                                <h2 class="entry__title">
                                                    <a href="{{route('post' , ['slug'=> $post->slug])}}">{{$post->title}}</a>
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
                                                <p>{{$post->summary}}...</p>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            @endforeach
                        </div>
                        {{--                        {{ $posts->links() }}--}}
                        {{ $posts->links('pagination::bootstrap-4') }}

                    </section>

                </div> <!-- end posts -->

                <!-- Sidebar -->
                <aside class="col-lg-4 sidebar sidebar--left pt-lg-5 mt-lg-4">
                    <aside class="widget widget_categories">
                        <h4 class="widget-title">دسته بندی نوشته ها</h4>
                        <ul>
                            <li class="p-2 rounded {{ !request()->has('cat')  ? 'bg-light-custom-primary' : ''}}">
                                <a href="{{route('posts')}}"
                                   class="d-block {{ !request()->has('cat')  ? 'active-menu-link' : ''}}">همه
                                    <span class="categories-count {{ !request()->has('cat')  ? 'custom-primary' : ''}}">{{$all_post_count}}</span>
                                </a>
                            </li>
                            @foreach($current_categories as $category)

                                <li class="p-2 rounded {{request()->has('cat') && request()->cat == $category[0]->slug ? 'bg-light-custom-primary' : ''}}">
                                    <a
                                            href="{{ request()->fullUrlWithQuery(['cat' => $category[0]->slug]) }}"
                                            class="d-block {{request()->has('cat') && request()->cat == $category[0]->slug ? 'active-menu-link' : ''}}"
                                    >{{$category[0]->process_name}}
                                        <span class="categories-count {{request()->has('cat') && request()->cat == $category[0]->slug ? 'custom-primary' : ''}} ">{{sizeof($category)}}</span>
                                    </a>
                                </li>
                            @endforeach
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
    <script src="{{asset('assets/post_assets/js/owl-carousel.min.js')}}"></script>
    <script src="{{asset('assets/post_assets/js/flickity.pkgd.min.js')}}"></script>
    <script src="{{asset('assets/post_assets/js/twitterFetcher_min.js')}}"></script>
    <script src="{{asset('assets/post_assets/js/jquery.newsTicker.min.js')}}"></script>
    <script src="{{asset('assets/post_assets/js/modernizr.min.js')}}"></script>
    <script src="{{asset('assets/post_assets/js/scripts.js')}}"></script>
@endsection

