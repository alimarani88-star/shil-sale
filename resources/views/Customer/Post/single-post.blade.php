@extends('Customer.Layout.master')

@section('head-tag')
    <link rel="stylesheet" href="{{asset('assets/post_assets/css/font-icons.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/post_assets/css/style.css')}}"/>

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
    <main class="main default">
        <!-- Breadcrumbs -->
        <div class="container">
            <ul class="breadcrumbs">
                <li class="breadcrumbs__item">
                    <a href="{{route('home')}}" class="breadcrumbs__url">خانه</a>
                </li>
                @foreach($breadcrumb as $item)
                    <li class="breadcrumbs__item">
                        <a href="{{route('posts').'?cat='.$item[1]}}" class="breadcrumbs__url">{{$item[0]}}</a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="main-container container" id="main-container">

            <!-- Content -->
            <div class="row flex-column-reverse flex-lg-row">

                <!-- post content -->
                <div class="col-lg-8 blog__content mb-72">
                    <div class="content-box">

                        <!-- standard post -->
                        <article class="entry mb-0">

                            <div class="single-post__entry-header entry__header">
                                @if($category_main)
                                    <a href="{{route('posts').'?cat='.$category_main->slug}}"
                                       class="entry__meta-category entry__meta-category--label entry__meta-category--green">{{$category_main->process_name}}</a>
                                @endif
                                <h1 class="single-post__entry-title">
                                    {{$post->title}}
                                </h1>

                                <div class="entry__meta-holder">
                                    <ul class="entry__meta">
                                        <li class="entry__meta-author">
                                            <span>نویسنده:</span>
                                            <a href="#">{{$post->author_name}}</a>
                                        </li>
                                        <li class="entry__meta-date">
                                            {{$post->published}}
                                        </li>
                                    </ul>

                                    <ul class="entry__meta">
                                        <li class="entry__meta-views">
                                            <i class="ui-eye"></i>
                                            <span>{{$post->view}}</span>
                                        </li>
                                        <li class="entry__meta-comments">
                                            <a href="#">
                                                <i class="ui-chat-empty"></i>۱۳
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div> <!-- end entry header -->
                            <div class="entry__img-holder" itemprop="image" itemscope
                                 itemtype="https://schema.org/ImageObject">
                                <img
                                        src="{{ $post->main_image ? route('get_post_image_by_id' , ["id"=>$post->main_image->id]) : asset('assets/img/no-image.jpg') }}"
                                        alt="{{ $post->main_image?->alt ?? $post->title }}"
                                        class="entry__img"
                                        itemprop="url"
                                >
                            </div>

                            <div class="entry__article-wrap w-100">

                                <article class="entry__article w-100" itemscope itemtype="https://schema.org/Article">

                                    @section('title')
                                        {{ trim($post->title) }}
                                    @endsection
                                    @section('head-tag')
                                        <h1 itemprop="headline">{{ $post->title }}</h1>
                                        <meta itemprop="author" content="{{ $post->author_name ?? 'ادمین' }}">
                                        <meta itemprop="datePublished"
                                              content="{{ $post->created_at->toDateString() }}">
                                        <meta itemprop="dateModified" content="{{ $post->updated_at->toDateString() }}">

                                    @endsection


                                    <section class="border p-3 mb-3 w-100" itemprop="description">
                                        <h2>خلاصه مطلب</h2>
                                        <p class="w-100">{{ $post->summary }}</p>
                                    </section>

                                    <section itemprop="articleBody">
                                        {!! $post->content !!}
                                    </section>
                                </article>
                            </div>

                            <!-- Related Posts -->
                            <section class="section related-posts mt-40 mb-0">
                                <div class="title-wrap title-wrap--line">
                                    <h3 class="section-title">مطالب مرتبط</h3>
                                </div>

                                <!-- Slider -->
                                <div id="owl-posts-3-items" class="owl-carousel owl-theme owl-carousel--arrows-outside">

                                    @foreach($related_post as $_post)
                                        <article class="entry thumb thumb--size-1">
                                            <div class="entry__img-holder thumb__img-holder"
                                                 style="background-image: url('{{$_post->main_image ? asset($_post->main_image?->image_url) : asset('assets/img/no-image.jpg')}}');"
                                                 title="{{$_post->title}}">

                                                <div class="bottom-gradient"></div>
                                                <div class="thumb-text-holder">
                                                    <h2 class="thumb-entry-title">
                                                        <a href="{{route('post' , ['slug'=> $_post->slug])}}">{{$_post->title}} </a>
                                                    </h2>
                                                </div>
                                                <a href="{{route('post' , ['slug'=> $_post->slug])}}"
                                                   class="thumb-url"></a>
                                            </div>
                                        </article>

                                    @endforeach


                                </div>
                                <!-- end slider -->

                            </section> <!-- end related posts -->

                        </article> <!-- end standard post -->

                        @if($post->allow_comments == 1)
                            <!-- Comments -->
                            <div class="entry-comments">
                                <div class="title-wrap title-wrap--line">
                                    <h3 class="section-title">{{sizeof($comments)}} دیدگاه</h3>
                                </div>
                                <ul class="comment-list">
                                    @foreach($comments as $comment)
                                        <li class="comment mt-2">
                                            <div class="comment-body">
                                                <div class="comment-avatar">
                                                    <img alt="{{$comment->user_avatar_alt}}"
                                                         src="{{ is_numeric($comment->user_avatar)? route('get_post_image_by_id'  , ['id'=> $comment->user_avatar]) : asset($comment->user_avatar) }}">
                                                </div>
                                                <div class="comment-text">
                                                    <h6 class="comment-author">{{$comment->user_name}}</h6>
                                                    <div class="comment-metadata">
                                                        <a href="#" class="comment-date">{{$comment->created}}</a>
                                                    </div>
                                                    <p>{{$comment->content}}</p>
                                                    <a href="#respond" class="comment-reply"
                                                       data-id="{{ $comment->id }}">
                                                        پاسخ
                                                    </a>
                                                </div>
                                            </div>

                                            @if(count($comment->replies) > 0)
                                                <ul class="reply-list"
                                                    style="margin-right: 20px; border-right: 3px solid #cdcdcd">
                                                    @foreach($comment->replies as $reply)
                                                        <li class="comment mt-2 reply">
                                                            <div class="comment-body reply-body"
                                                                 style="background-color: #f9f9f9; padding: 10px; border-radius: 5px;">
                                                                <div class="comment-avatar">
                                                                    <img alt="{{$reply->user_avatar_alt}}"
                                                                         src="{{asset($reply->user_avatar)}}">
                                                                </div>
                                                                <div class="comment-text">
                                                                    <h6 class="comment-author">{{$reply->user_name}}</h6>
                                                                    <div class="comment-metadata">
                                                                        <a href="#"
                                                                           class="comment-date">{{$reply->created}}</a>
                                                                    </div>
                                                                    <p>{{$reply->content}}</p>
                                                                    <a href="#respond" class="comment-reply"
                                                                       data-id="{{ $reply->id }}">
                                                                        پاسخ
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            @if(count($reply->replies) > 0)
                                                                <ul class="reply-list"
                                                                    style="margin-right: 20px; border-right: 3px solid #cdcdcd">
                                                                    @foreach($reply->replies as $reply2)
                                                                        <li class="comment reply">
                                                                            <div class="comment-body reply-body"
                                                                                 style="background-color: #f9f9f9; padding: 10px; border-radius: 5px;">
                                                                                <div class="comment-avatar">
                                                                                    <img alt="{{$reply2->user_avatar_alt}}"
                                                                                         src="{{asset($reply2->user_avatar)}}">
                                                                                </div>
                                                                                <div class="comment-text">
                                                                                    <h6 class="comment-author">{{$reply2->user_name}}</h6>
                                                                                    <div class="comment-metadata">
                                                                                        <a href="#"
                                                                                           class="comment-date">{{$reply2->created}}</a>
                                                                                    </div>
                                                                                    <p>{{$reply2->content}}</p>
                                                                                </div>
                                                                            </div>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <!-- end comments -->

                            <!-- Comment Form -->

                            <div id="respond" class="comment-respond">
                                <div class="title-wrap">
                                    <h5 class="comment-respond__title section-title">
                                        دیدگاه شما :
                                        @auth
                                            <span style="opacity: 0.5">{{'( '. auth()->user()->name .' )'}}</span>
                                        @else
                                            <a class="text-info text-sm" href="{{ route('login') }}">ابتدا وارد شوید</a>
                                        @endauth
                                    </h5></div>
                                <form id="form" class="comment-form" method="post" action="{{route('create_comment')}}">
                                    @csrf
                                    <input type="hidden" name="post_id" value="{{$post->id}}">
                                    <input type="hidden" name="reply" id="reply" value="">
                                    <p class="comment-form-comment ">
                                        <label for="comment">دیدگاه</label>
                                        <textarea id="comment" class="form-control w-100" name="comment" rows="5" required="required"></textarea>
                                    </p>


                                    @auth
                                        <p class="comment-form-submit">

                                            <input type="submit" class="btn btn-lg btn-color btn-button"
                                                   value="ارسال دیدگاه"
                                                   id="submit-message">

                                        </p>
                                    @endauth
                                </form>


                            </div> <!-- end comment form -->
                        @endif
                        <div class="my-3" id="message-container">
                            @if ($errors->any())
                                <div class="alert alert-danger rounded">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if (session('success'))
                                <div class="alert alert-success rounded">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger rounded">
                                    {{ session('error') }}
                                </div>
                            @endif

                        </div>
                    </div> <!-- end content box -->
                </div> <!-- end post content -->

                <!-- Sidebar -->
                <aside class="col-lg-4 sidebar sidebar--left ">
                    <aside class="widget widget_categories">
                        <h4 class="widget-title">دسته بندی نوشته ها</h4>
                        <ul>
                            @foreach($current_categories as $category)

                                <li class="p-2 rounded">
                                    <a
                                            href="{{ route('posts').'?cat='.$category[0]->slug }}"
                                            class="d-block"
                                    >{{$category[0]->process_name}}
                                        <span
                                                class="categories-count {{request()->has('cat') && request()->cat == $category[0]->slug ? 'custom-primary' : ''}} ">{{sizeof($category)}}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </aside>
                </aside>


            </div>
        </div>

    </main>

    <!-- main -->
@endsection




@section('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.comment-reply').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    let commentId = this.getAttribute('data-id');
                    document.getElementById('reply').value = commentId;
                    const text = $(this).parent().find('p').text()
                    const user = $(this).parent().find('.comment-author').text()
                    $('.comment-form-comment label').html(`پاسخ به دیدگاه @${user} <button type="button" onclick="this.closest('label').innerHTML='دیدگاه' ; document.getElementById('reply').value=''" class="mx-3 btn">لغو پاسخ</button>`)
                    // اسکرول به فرم
                    document.getElementById('form').scrollIntoView({behavior: 'smooth'});
                });
            });

            if (document.querySelector('#message-container .alert')) {
                document.querySelector('#message-container').scrollIntoView({
                    behavior: 'smooth'
                });
            }

        });
    </script>
    <script src="{{asset('assets/post_assets/js/easing.min.js')}}"></script>
    <script src="{{asset('assets/post_assets/js/owl-carousel.min.js')}}"></script>
    <script src="{{asset('assets/post_assets/js/flickity.pkgd.min.js')}}"></script>
    <script src="{{asset('assets/post_assets/js/twitterFetcher_min.js')}}"></script>
    <script src="{{asset('assets/post_assets/js/jquery.newsTicker.min.js')}}"></script>
    <script src="{{asset('assets/post_assets/js/modernizr.min.js')}}"></script>
    <script src="{{asset('assets/post_assets/js/scripts.js')}}"></script>
@endsection

