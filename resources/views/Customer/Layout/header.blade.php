<style>
    .list-group-title {
        opacity: 0.5;
        margin-right: 20px;
    }

    .list-group-item {
        padding: 0.16rem 1.25rem !important;
    }

    .gsearch {
        display: block;
        padding: 5px;
        border-radius: 5px;
    }

    .gsearch:hover {
        background-color: #eaeaea;
    }

    .search-area .search-box-list {
        max-height: 600px;
        overflow: auto;
    }
</style>
<header class="main-header default">
    <div class="container">
        <div class="row">
            <div class="col-lg-2 col-md-3 col-sm-4 col-5">
                <div class="logo-area default">
                    <a href="#">
                        <img src="/assets/img/shiliran-logo-purple-143x36.svg" alt="">
                        {{-- <h3>SHILIRAN</h3> --}}
                    </a>
                </div>
            </div>
            <div class="col-lg-6 col-md-5 col-sm-8 col-7">
                <div class="search-area default">
                    <div class="search">
                        <input type="text" id="main_search"
                               placeholder="نام محصول و یا دسته مورد نظر خود را جستجو کنید…">
                        <ul class="list-group search-box-list">
                            <li class="list-group-title">
                                <a href="#" class="">
                                    نتایج جست و جو در دسته محصولات
                                </a>
                            </li>
                            <li class="list-group-item contsearch">
                                <a href="#" class="gsearch">
                                    <i class="fad fa-search"></i>
                                    کلید مینیاتوری
                                </a>
                            </li>
                            <li class="list-group-item contsearch">
                                <a href="#" class="gsearch">
                                    <i class="fad fa-search"></i>
                                    کنتاکتور
                                </a>
                            </li>
                            <li class="list-group-item contsearch">
                                <a href="#" class="gsearch">
                                    <i class="fad fa-search"></i>
                                    کلید اتوماتیک
                                </a>
                            </li>
                            <li class="list-group-item contsearch">
                                <a href="#" class="gsearch">
                                    <i class="fad fa-search"></i>
                                    اینورتر خورشیدی
                                </a>
                            </li>
                            <li class="list-group-title">
                                <a href="#" class="">
                                    نتایج جست و جو در محصولات
                                </a>
                            </li>
                            <li class="list-group-item contsearch">
                                <a href="#" class="gsearch">
                                    <i class="fad fa-search"></i>
                                    محافظ ولتاژ
                                </a>
                            </li>
                        </ul>
                        <div class="localSearchSimple"></div>
                        <button type="submit" class="custom-primary"><img src="/assets/img/search.png" alt=""></button>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-12">
                <div class="user-login dropdown">
                    @if(Auth::check())
                        <a href="#" class="btn btn-neutral user-login-btn dropdown-toggle mt-1 custom-cl-primary ml-2"
                           data-toggle="dropdown" id="navbarDropdownMenuLink1">
                            <i class="fa fa-user ml-1"></i> {{ Auth::user()->name }}
                        </a>
                    @else
                        <a href="#" class="btn btn-neutral user-login-btn dropdown-toggle mt-1 custom-cl-primary ml-2"
                           data-toggle="dropdown" id="navbarDropdownMenuLink1">
                            <i class="fa fa-user ml-1"></i> ورود / ثبت نام
                        </a>
                    @endif
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink1">
                        @auth
                            <div class="dropdown-item">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn custom-primary">
                                        خروج
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="dropdown-item">
                                <a href="/login" class="btn custom-primary text-white">ورود</a>
                            </div>
                            <div class="dropdown-item font-weight-bold">
                                <span>کاربر جدید هستید؟</span> <a class="register" href="/register">ثبت ‌نام</a>
                            </div>
                        @endauth
                        @auth
                            <hr>
                            <div class="dropdown-item">
                                <a href="{{route('profile_personal_info')}}" class="dropdown-item-link">
                                    <i class="now-ui-icons users_single-02"></i>
                                    پروفایل
                                </a>
                            </div>
                            <div class="dropdown-item">
                                <a href="#" class="dropdown-item-link">
                                    <i class="now-ui-icons shopping_bag-16"></i>
                                    پیگیری سفارش
                                </a>
                            </div>
                        @endauth
                    </ul>
                </div>
                <div class="cart dropdown">
                    <a href="#" class="btn dropdown-toggle custom-primary" data-toggle="dropdown"
                       id="navbarDropdownMenuLink1">
                        <img src="/assets/img/shopping-cart.png" alt="">
                        سبد خرید
                        <span class="count-cart">0</span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink1">
                        <div class="basket-header">
                            <div class="basket-total">
                                <span>مبلغ کل خرید:</span>
                                <span class="total-price">0</span>
                                <span> ریال</span>
                            </div>
                            <a href="{{ route("cart") }}" class="basket-link">
                                <span>مشاهده سبد خرید</span>
                            </a>
                        </div>
                        <ul class="basket-list">

                        </ul>
                        @if (!Auth::check())
                            <a href="{{ route('login') }}" class="basket-submit custom-primary">ورود و ثبت سفارش</a>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="container position-relative">
        <div class="d-flex flex-row align-items-center">
            <div class="list-item position-relative " style="width: 175px ; ">
                <a href="{{route('products')}}" class="category-btn d-block text-dark">
                    <i class="fa fa-list" style="font-size: 15px"></i>
                    دسته بندی محصولات</a>
            </div>
            <nav class="main-menu mt-0 pt-0">
                <ul class="list float-right py-3" style="margin-bottom: 5px">

                    <li class="list-item">
                        <a class=" {{request()->routeIs('home') ? 'active-menu-link' : ''}}" href="{{route('home')}}">صفحه
                            نخست</a>
                    </li>
                    <li class="list-item">
                        <a class=" {{request()->routeIs('posts','post') ? 'active-menu-link' : ''}}"
                           href="{{route('posts')}}">مجله شیل</a>
                    </li>
                    <li class="list-item">
                        <a class=" {{request()->routeIs('contact') ? 'active-menu-link' : ''}}"
                           href="{{route('contact')}}">تماس با ما</a>
                    </li>
                    <li class="list-item">
                        <a class=" {{request()->routeIs('about') ? 'active-menu-link' : ''}}" href="{{route('about')}}">درباره
                            ما</a>
                    </li>
                    <li class="list-item">
                        <a class=" {{request()->routeIs('register_customer') ? 'active-menu-link' : ''}}"
                           href="{{route('register_customer')}}">ثبت اطلاعات مشتریان</a>
                    </li>
                    <li class="list-item">
                        <a class=" {{request()->routeIs('after_sales_service') ? 'active-menu-link' : ''}}"
                           href="{{route('after_sales_service')}}">خدمات پس از فروش</a>
                    </li>
                </ul>
            </nav>
        </div>
        <nav style="display: none" class="mega-menu-wrapper position-absolute">
            <div class="mega-menu-container">
                <!-- Level 1: Horizontal Menu -->
                <ul class="mega-menu-level-1">
                    @foreach($categories as $level1)
                        <li class="mega-menu-item" data-category-id="{{ $level1['id'] }}">
                <span class="mega-menu-link">
                   <a href="{{ route('products_category' , ["slug"=>$level1['slug']]) }}">
                        <span class="menu-text">{{ $level1['name'] }}</span>
                    @if($level1['has_children'])
                           <i class="fa fa-chevron-down"></i>
                       @endif
                   </a>
                </span>

                            @if($level1['has_children'])
                                <!-- Mega Menu Dropdown -->
                                <div class="mega-menu-dropdown">
                                    <div class="mega-menu-content">
                                        <!-- Right Column: Level 2 Only -->
                                        <div class="mega-menu-level-2-column">
                                            <ul class="mega-menu-level-2">
                                                @foreach($level1['children'] as $level2)
                                                    <li class="mega-menu-level-2-item"
                                                        data-category-id="{{ $level2['id'] }}"
                                                        data-image="{{ $level2['image'] ?? '' }}">
                                                        <a href="{{ route('products_category' , ["slug"=>$level2['slug']]) }}"
                                                           class="level-2-link">
                                                            <span class="level-2-text">{{ $level2['name'] }}</span>
                                                            @if($level2['has_children'])
                                                                <i class="fa fa-chevron-left"></i>
                                                            @endif
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>

                                        <!-- Center: Level 3, 4, 5 -->
                                        <div class="mega-menu-center-content">
                                            @foreach($level1['children'] as $level2)
                                                @if($level2['has_children'])
                                                    <div class="mega-menu-level-3" data-parent-id="{{ $level2['id'] }}"
                                                         style="display: none;">
                                                        <div class="level-3-wrapper">
                                                            @foreach($level2['children'] as $level3)
                                                                <div class="level-3-group">
                                                                    @if($level3['has_children'])
                                                                        <!-- Level 4: Title -->
                                                                        <div class="level-4-title">
                                                                            <a href="{{ route('products_category' , ["slug"=>$level3['slug']]) }}">{{ $level3['name'] }}</a>
                                                                        </div>

                                                                        <!-- Level 5: Links -->
                                                                        <ul class="level-5-links">
                                                                            @foreach($level3['children'] as $level4)
                                                                                <li>
                                                                                    @if($level4['has_children'])
                                                                                        <a href="{{ route('products_category' , ["slug"=>$level4['slug']]) }}"
                                                                                           class="level-5-parent">{{ $level4['name'] }}</a>
                                                                                        <ul class="level-5-sub">
                                                                                            @foreach($level4['children'] as $level5)
                                                                                                <li>
                                                                                                    <a href="{{ route('products_category' , ["slug"=>$level5['slug']]) }}"
                                                                                                       class="level-5-item">{{ $level5['name'] }}</a>
                                                                                                </li>
                                                                                            @endforeach
                                                                                        </ul>
                                                                                    @else
                                                                                        <a href="{{ route('products_category' , ["slug"=>$level4['slug']]) }}"
                                                                                           class="level-5-item">{{ $level4['name'] }}</a>
                                                                                    @endif
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    @else
                                                                        <a href="{{ route('products_category' , ["slug"=>$level3['slug']]) }}"
                                                                           class="level-3-link">{{ $level3['name'] }}</a>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>

                                        <!-- Left Side: Category Image -->

                                        @foreach($level1['children'] as $level2)
                                            @if($level2['image'])
                                                <div class="mega-menu-image">
                                                    <div class="image-container">
                                                        <img
                                                            src="{{ route('get_post_image_by_id' , ["id"=>$level2['image']]) }}"
                                                            alt="{{ $level2['name'] }}"
                                                            class="category-image"
                                                            data-category-id="{{ $level2['id'] }}"
                                                            style="display: none;">
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach

                                    </div>
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </nav>

    </div>
</header>

<div class="overlay-search-box"></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        $(document).ready(function() {
            const $body = $('body');
            const $overlay = $('<div class="mega-menu-overlay"></div>').appendTo('body');
            const $level2Items = $('.mega-menu-level-2-item');
            const $megaMenuItems = $('.mega-menu-item');
            const $mobileToggle = $('.mobile-menu-toggle');
            const $level1Menu = $('.mega-menu-level-1');

            // Level 2 Hover Effect
            $level2Items.on('mouseenter', function() {

                if ($(window).width() > 992) {
                    const categoryId = $(this).data('category-id');
                    const $parentDropdown = $(this).closest('.mega-menu-dropdown');
                    $parentDropdown.find('.mega-menu-level-2-item').removeClass('active');
                    $(this).addClass('active');

                    $parentDropdown.find('.mega-menu-level-3').removeClass('active');
                    $parentDropdown.find(`.mega-menu-level-3[data-parent-id="${categoryId}"]`).addClass('active');

                    $parentDropdown.find('.category-image').hide().removeClass('active');
                    const $currentImage = $parentDropdown.find(`.category-image[data-category-id="${categoryId}"]`);
                    if ($currentImage.length) {
                        $currentImage.show();
                        setTimeout(() => $currentImage.addClass('active'), 10);
                    }
                }
            });

            // Show first item by default on hover
            $megaMenuItems.on('mouseenter', function() {
                if ($(window).width() > 992) {
                    const $dropdown = $(this).find('.mega-menu-dropdown').first();
                    if ($dropdown.length) {
                        const $firstLevel2 = $dropdown.find('.mega-menu-level-2-item').first();
                        if ($firstLevel2.length) {
                            const firstCategoryId = $firstLevel2.data('category-id');

                            $firstLevel2.addClass('active');
                            $dropdown.find(`.mega-menu-level-3[data-parent-id="${firstCategoryId}"]`).addClass('active');

                            const $firstImage = $dropdown.find(`.category-image[data-category-id="${firstCategoryId}"]`);
                            if ($firstImage.length) {
                                $firstImage.show();
                                setTimeout(() => $firstImage.addClass('active'), 10);
                            }
                        }
                    }
                }
            }).on('mouseleave', function() {
                if ($(window).width() > 992) {
                    const $dropdown = $(this).find('.mega-menu-dropdown');
                    $dropdown.find('.mega-menu-level-2-item, .mega-menu-level-3').removeClass('active');
                    $dropdown.find('.category-image').hide().removeClass('active');
                }
            });

            // Mobile Menu Toggle
            $mobileToggle.on('click', function() {
                $(this).toggleClass('active');
                $level1Menu.toggleClass('active');
                $overlay.toggleClass('active');
                $body.toggleClass('menu-open');
            });

            // Overlay click to close
            $overlay.on('click', function() {
                $level1Menu.removeClass('active');
                $mobileToggle.removeClass('active');
                $overlay.removeClass('active');
                $body.removeClass('menu-open');
            });

            // Mobile Accordion
            if ($(window).width() <= 992) {
                const $level1Links = $('.mega-menu-item > .mega-menu-link');
                $level1Links.on('click', function(e) {
                    const $parent = $(this).parent();
                    const $dropdown = $parent.find('.mega-menu-dropdown');
                    if ($dropdown.length) {
                        e.preventDefault();
                        $('.mega-menu-item').not($parent).removeClass('active');
                        $parent.toggleClass('active');
                    }
                });

                $level2Items.on('click', function(e) {
                    e.preventDefault();
                    const categoryId = $(this).data('category-id');
                    const $parentDropdown = $(this).closest('.mega-menu-dropdown');

                    $parentDropdown.find('.mega-menu-level-2-item').removeClass('active');
                    $(this).addClass('active');

                    $parentDropdown.find('.mega-menu-level-3').removeClass('active');
                    $parentDropdown.find(`.mega-menu-level-3[data-parent-id="${categoryId}"]`).addClass('active');
                });
            }

            // Reset menu on resize
            $(window).on('resize', function() {
                if ($(window).width() > 992) {
                    $level1Menu.removeClass('active');
                    $mobileToggle.removeClass('active');
                    $overlay.removeClass('active');
                    $body.removeClass('menu-open');
                }
            });
        });

        $(document).ready(function() {
            const categoryBtn = $('.category-btn');
            const megaMenuWrapper = $('.mega-menu-wrapper');
            categoryBtn.on('mouseenter', function() {
                megaMenuWrapper.stop(true, true).slideDown(200);
            });
            categoryBtn.on('mouseleave', function() {
                setTimeout(function() {
                    if (!megaMenuWrapper.is(':hover')) {
                        megaMenuWrapper.stop(true, true).slideUp(200);
                    }
                }, 200);
            });
            megaMenuWrapper.on('mouseenter', function() {
                megaMenuWrapper.stop(true, true).slideDown(200);
            });
            megaMenuWrapper.on('mouseleave', function() {
                megaMenuWrapper.stop(true, true).slideUp(200);
            });
        });

        $(document).ready(function() {
            $('#main_search').on('input', function() {
                $.ajax({
                    url: '{{route('ajax_main_header_search')}}',
                    success: function(response) {
                        if (response.status === 'success') {
                            if (response.data.data.length > 0) {

                            } else {

                            }
                        } else {

                        }
                    },
                    complete: function() {

                    }
                });
            });
        });
    });
</script>
@if(auth()->check())

    <script>
        document.addEventListener('DOMContentLoaded' , function(){
            $(document).ready(function() {
                $.ajax({
                    type: 'GET',
                    url: '{{ route("ajax_cart_header") }}',
                    success: function(response) {
                        $('.count-cart').text(response.count);
                        let formatted = response.totalPrice.toLocaleString('en-US');
                        $('.total-price').text(formatted);

                        const basketList = $('.basket-list');
                        basketList.empty();

                        if (response.cart.length === 0) {
                            basketList.append('<li class="text-center py-2 text-muted">سبد خرید خالی است</li>');
                            return;
                        }

                        response.cart.forEach(item => {
                            let image = '/assets/img/default.jpg';
                            if (item.product.images.length > 0) {
                                const imageId = item.product.images[0].id;
                                image = `/get_image_by_id/${imageId}`;
                            }

                            const html = `
                    <li>
                        <a href="#" class="basket-item">
                            <button class="basket-item-remove" data-id="${item.id}"></button>
                            <div class="basket-item-content">
                                <div class="basket-item-image">
                                    <img src="${image}" alt="">
                                </div>
                                <div class="basket-item-details">
                                    <div class="basket-item-title">${item.product.product_name}</div>
                                    <div class="basket-item-params">
                                        <div class="basket-item-props">
                                            <span style="margin-left: 1rem;">${item.count} عدد</span>
                                            <span style="color:red">${item.duscount_name}</span>
                                            <span style="color:red">${item.percentage}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>`;
                            basketList.append(html);
                        });
                    },
                    error: function() {
                        console.log('Error loading cart header');
                    }
                });
            });
        })

    </script>

@endif


