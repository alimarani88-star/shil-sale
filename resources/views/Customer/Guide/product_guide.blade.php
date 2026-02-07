@extends('Customer.Layout.master')

@section('head-tag')
    <link rel="stylesheet" href="{{ asset('assets/post_assets/css/font-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/post_assets/css/style.css') }}" />

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
    </style>
@endsection

@section('content')
    <main class="main default" role="main">
        <div class="main-container container" id="main-container">

            <!-- Hero بنر پست -->
            <div class="post-hero mb-4"
                 style="background-image: url('{{ $post->main_image ? route('get_post_image_by_id', ['id'=>$post->main_image->id]) : asset('assets/img/no-image.jpg') }}');"
                 aria-label="Post cover">
                <div class="overlay"></div>
                <div class="hero-content text-white">
                    <h1 class="display-5 hero-title">{{ $post->title }}</h1>
                </div>
            </div>

            <!-- محتوا -->
            <div class="row flex-column-reverse flex-lg-row">
                <div class="col-lg-12 blog__content mb-72">

                    <div class="content-box">

                        <article class="entry mb-0" itemscope itemtype="https://schema.org/Article">

                            <!-- سربرگ محتوا -->
                            <div class="entry__article-wrap">

                                <section class="entry__article" itemprop="articleBody">

                                    @section('title')
                                        {{ trim($post->title) }}
                                    @endsection

                                    @php $groupedFields = $post->groupedFields; @endphp

                                        @if(!empty($groupedFields))

                                            {{-- ویدیوها --}}
                                            @if(!empty($groupedFields['video']))
                                                <div class="row g-3 mb-4">
                                                    @foreach($groupedFields['video'] as $field)
                                                        <div class="col-12 col-md-4 col-lg-3">
                                                            <div class="group-card p-2">
                                                                <div class="card-body p-0">
                                                                    <h5 class="card-title fw-bold text-dark mb-2">{{ $field['fa_name'] ?? 'بدون عنوان' }}</h5>
                                                                    <video controls width="100%" style="border-radius:10px;">
                                                                        <source src="{{ $field['value'] }}" type="video/mp4">
                                                                        مرورگر شما از ویدیو پشتیبانی نمی‌کند.
                                                                    </video>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif

                                            {{-- فایل‌های صوتی --}}
                                            @if(!empty($groupedFields['audio']))
                                                <div class="row g-3 mb-4">
                                                    @foreach($groupedFields['audio'] as $field)
                                                        <div class="col-12 col-md-4 col-lg-3">
                                                            <div class="group-card p-2">
                                                                <div class="card-body p-0">
                                                                    <h5 class="card-title fw-bold text-dark mb-2">{{ $field['fa_name'] ?? 'بدون عنوان' }}</h5>
                                                                    <audio controls style="width:100%;">
                                                                        <source src="{{ $field['value'] }}" type="audio/mpeg">
                                                                        مرورگر شما از پخش صوت پشتیبانی نمی‌کند.
                                                                    </audio>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif

                                            {{-- متنی --}}
                                            @if(!empty($groupedFields['text']))
                                                <div class="row g-3 mb-4">
                                                    @foreach($groupedFields['text'] as $field)
                                                        <div class="col-12">
                                                            <div class="group-card">
                                                                <div class="card-body p-2">
                                                                    <h5 class="card-title fw-bold text-dark mb-1">{{ $field['fa_name'] ?? 'بدون عنوان' }}</h5>
                                                                    <p class="card-text mb-0">{{ $field['value'] }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif

                                            {{-- لینک‌ها و فایل‌ها --}}
                                            @if(!empty($groupedFields['link']) || !empty($groupedFields['file']))
                                                <div class="row g-3 mb-4 bg-light py-3">
                                                    @php
                                                        $mergedLinks = array_merge(
                                                            $groupedFields['link'] ?? [],
                                                            $groupedFields['file'] ?? []
                                                        );
                                                    @endphp

                                                    @foreach($mergedLinks as $field)
                                                        @php
                                                            $isFile = ($field['field_type'] ?? '') === 'file';
                                                            $btnClass = $isFile ? 'btn-success' : 'btn-primary';
                                                            $icon = $isFile ? '<i class="fa fa-download mx-1"></i>' : '<i class="fa fa-link mx-1"></i>';
                                                            $href = $isFile ? route('get_file_by_id' , ["id"=> $field['value']]) : $field['value'];
                                                            $extra = $isFile ? 'download' : 'target="_blank"';
                                                        @endphp

                                                        <div class="col-12 col-md-6 col-lg-4">
                                                            <a href="{{ $href }}" {!! $extra !!} class="btn btn-sm {{ $btnClass }} w-100">
                                                                {!! $icon !!} {{ $field['fa_name'] ?? 'بدون عنوان' }}
                                                            </a>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif


                                        @endif


                                        <section class="border p-3 mb-3 w-100" itemprop="description">

                                        <h2>خلاصه مطلب</h2>
                                        <p>{{ $post->summary }}</p>
                                    </section>

                                    <section itemprop="articleBody" class="mb-3">
                                        {!! $post->content !!}
                                    </section>

                                    @foreach($tags as $tag_id=>$tag)
                                        <span class="badge rounded-pill bg-primary mx-1"
                                              style="font-size:.8rem">{{ $tag }}</span>
                                    @endforeach
                                </section>
                            </div>


                            <div class="entry__meta-holder mb-2 text-muted">
                                نویسنده: <strong>{{ $post->author_name }}</strong>
                                <span class="mx-2"></span>
                                <span>{{ $post->published }}</span>
                            </div>
                        </article>

                        @if($post->allow_comments == 1)
                            <div class="entry-comments mb-5">
                                <div class="title-wrap mb-2">
                                    <h3 class="section-title">{{ sizeof($comments) }} دیدگاه</h3>
                                </div>

                                <ul class="comment-list list-unstyled">
                                    @foreach($comments as $comment)
                                        <li class="comment media mb-3">
                                            <img class="align-self-start rounded-circle me-2"
                                                 alt="{{ $comment->user_avatar_alt }}"
                                                 src="{{ is_numeric($comment->user_avatar) ? route('get_post_image_by_id', ['id'=> $comment->user_avatar]) : asset($comment->user_avatar) }}"
                                                 width="40" height="40">
                                            <div class="media-body">
                                                <h6 class="mt-0 mb-1">{{ $comment->user_name }}</h6>
                                                <div class="comment-metadata text-muted mb-1">
                                                    <a href="#" class="comment-date">{{ $comment->created }}</a>
                                                </div>
                                                <p class="mb-1">{{ $comment->content }}</p>
                                                <a href="#respond" class="comment-reply" data-id="{{ $comment->id }}">پاسخ</a>
                                                @if(count($comment->replies) > 0)
                                                    <ul class="reply-list list-unstyled ms-3 mt-2">
                                                        @foreach($comment->replies as $reply)
                                                            <li class="comment mt-2 media">
                                                                <img class="align-self-start rounded-circle me-2"
                                                                     alt="{{ $reply->user_avatar_alt }}"
                                                                     src="{{ asset($reply->user_avatar) }}" width="34"
                                                                     height="34">
                                                                <div class="media-body">
                                                                    <h6 class="mt-0 mb-1">{{ $reply->user_name }}</h6>
                                                                    <div class="comment-metadata text-muted mb-1">
                                                                        <a href="#"
                                                                           class="comment-date">{{ $reply->created }}</a>
                                                                    </div>
                                                                    <p class="mb-1">{{ $reply->content }}</p>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div id="respond" class="comment-respond mt-4">
                                <div class="title-wrap mb-2">
                                    <h5 class="section-title mb-0">
                                        دیدگاه شما :
                                        @auth
                                            <span class="text-muted"
                                                  style="opacity:.6">({{ auth()->user()->name }})</span>
                                        @else
                                            <a class="text-info" href="{{ route('login') }}">ابتدا وارد شوید</a>
                                        @endauth
                                    </h5>
                                </div>

                                <form id="form" class="comment-form" method="post"
                                      action="{{ route('create_comment') }}">
                                    @csrf
                                    <input type="hidden" name="post_id" value="{{ $post->id }}">
                                    <input type="hidden" name="reply" id="reply" value="">

                                    <div class="mb-3">
                                        <label for="comment" class="form-label">دیدگاه</label>
                                        <textarea id="comment" name="comment" rows="5" class=""
                                                  required></textarea>
                                    </div>

                                    @auth
                                        <button type="submit" class="btn btn-lg btn-color btn-primary"
                                                id="submit-message">ارسال دیدگاه
                                        </button>
                                    @endauth
                                </form>
                            </div>
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
                                <div class="alert alert-success rounded">{{ session('success') }}</div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger rounded">{{ session('error') }}</div>
                            @endif
                        </div>

                    </div> <!-- end content-box -->
                </div> <!-- end post content -->
            </div>
        </div>

        </div>
    </main>
@endsection

@section('scripts')
    <script src="{{ asset('assets/post_assets/js/easing.min.js') }}"></script>
    <script src="{{ asset('assets/post_assets/js/owl-carousel.min.js') }}"></script>
    <script src="{{ asset('assets/post_assets/js/flickity.pkgd.min.js') }}"></script>
    <script src="{{ asset('assets/post_assets/js/twitterFetcher_min.js') }}"></script>
    <script src="{{ asset('assets/post_assets/js/jquery.newsTicker.min.js') }}"></script>
    <script src="{{ asset('assets/post_assets/js/modernizr.min.js') }}"></script>
    <script src="{{ asset('assets/post_assets/js/scripts.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.comment-reply').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    let commentId = this.getAttribute('data-id');
                    document.getElementById('reply').value = commentId;
                    const text = $(this).parent().find('p').text();
                    const user = $(this).parent().find('.comment-author').text();
                    $('.comment-form-comment label').html(`پاسخ به دیدگاه @${user} <button type="button" onclick="this.closest('label').innerHTML='دیدگاه' ; document.getElementById('reply').value=''" class="mx-3 btn">لغو پاسخ</button>`);
                    document.getElementById('form').scrollIntoView({ behavior: 'smooth' });
                });
            });

            if (document.querySelector('#message-container .alert')) {
                document.querySelector('#message-container').scrollIntoView({ behavior: 'smooth' });
            }
        });
    </script>
@endsection
