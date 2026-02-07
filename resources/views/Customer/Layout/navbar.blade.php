<style>
    .search-nav .search-box-list {
        position: absolute;
        top: 42px;
        left: 0;
        width: 100% !important;
        background: #fff;
        border-radius: 10px;
        border: 1px solid #eeeeee;
        overflow: auto;
        max-height: 400px;
    }

</style>
<nav class="navbar direction-ltr fixed-top header-responsive">
    <div class="container">
        <div class="navbar-translate">
            <a class="navbar-brand" href="#pablo">
                <img src="/assets/img/shiliran-logo-purple-143x36.svg" height="30px" alt="">
            </a>
            <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse"
                    data-target="#navigation" aria-controls="navigation-index" aria-expanded="false"
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-bar bar1"></span>
                <span class="navbar-toggler-bar bar2"></span>
                <span class="navbar-toggler-bar bar3"></span>
            </button>
            <div class="search-nav default">
                <div class="search-form-box" style="position: relative">
                    <input id="main_search_mobile" type="text" placeholder="جستجو ...">
                    <button type="submit" style="background:#6a1b9a !important;"><img src="/assets/img/search.png"
                                                                                      alt=""></button>
                    <ul style="display: none" class="list-group search-box-list">
                    </ul>
                </div>
            </div>

        </div>

        <div class="collapse navbar-collapse justify-content-end" id="navigation">
            <div class="logo-nav-res default text-center">
                <a href="#">
                    <img src="/assets/img/shiliran-logo-purple-143x36.svg" height="36px" alt="">
                </a>
            </div>
            <ul class="navbar-nav default">
                <li class="sub-menu px-2">
                    <a href="#" style="background-color: #6a1b9a38 ; border-radius: 7px ; display: block !important;"><i
                            class="fa fa-list" style="font-size: 15px"></i>
                        دسته بندی محصولات</a>
                    <ul style="border-radius: 7px">
                        @foreach($categories as $level1)
                            <!-- سطح اول -->
                            <li class="{{ $level1['has_children'] ? 'sub-menu' : 'list-item' }}">
                                <a href="{{route('products_category' , ['slug'=>$level1['slug'] ])}}">{{ $level1['name'] }}</a>

                                @if($level1['has_children'])
                                    <ul>
                                        @foreach($level1['children'] as $level2)
                                            <!-- سطح دوم -->
                                            <li class="{{ $level2['has_children'] ? 'sub-menu' : 'list-item' }}">
                                                <a href="{{route('products_category',['slug'=>$level2['slug']])}}">{{ $level2['name'] }}</a>

                                                @if($level2['has_children'])
                                                    <ul>
                                                        @foreach($level2['children'] as $level3)
                                                            <!-- سطح سوم -->
                                                            <li class="{{ $level3['has_children'] ? 'sub-menu' : 'list-item' }}">
                                                                <a href="{{route('products_category' , ['slug'=>$level3['slug']])}}">{{ $level3['name'] }}</a>

                                                                @if($level3['has_children'])
                                                                    <ul>
                                                                        @foreach($level3['children'] as $level4)
                                                                            <!-- سطح چهارم -->
                                                                            <li class="{{ $level4['has_children'] ? 'sub-menu' : 'list-item' }}">
                                                                                <a href="{{route('products_category',['slug'=>$level4['slug']])}}">{{ $level4['name'] }}</a>

                                                                                @if($level4['has_children'])
                                                                                    <ul>
                                                                                        @foreach($level4['children'] as $level5)
                                                                                            <!-- سطح پنجم -->
                                                                                            <li class="list-item">
                                                                                                <a href="{{route('products_category' , ['slug'=>$level5['slug']])}}">{{ $level5['name'] }}</a>
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
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </li>
                <li class="list-item">
                    <a class=" {{request()->routeIs('home') ? 'active-menu-link' : ''}}" href="/" >صفحه نخست</a>
                </li>
                <li class="list-item">
                    <a class=" {{request()->routeIs('posts','post') ? 'active-menu-link' : ''}}" href="{{route('posts')}}" >مجله شیل</a>
                </li>

                <li class="list-item">
                    <a class="" href="{{ route('about') }}" >درباره ما</a>
                </li>
                <li class="list-item">
                    <a class="" href="{{ route('contact') }}" >تماس با ما</a>
                </li>
                <li class="list-item">
                    <a class="" href="{{ route('register_customer') }}" >ثبت اطلاعات مشتریان</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="mobile-bottom-nav">
    <a href="{{ auth()->check() ? route('profile_personal_info') : route('login') }}">
        <i class="now-ui-icons users_single-02"></i>
        <span>پروفایل</span>
    </a>
    <a href="{{ auth()->check() ? route('cart') : route('login') }}">
        <i class="now-ui-icons shopping_basket"></i>
        <span>سبد خرید</span>
    </a>
    <a href="">
        <i class="now-ui-icons design_bullet-list-67"></i>
        <span>دسته‌بندی</span>
    </a>
    <a href="{{route('home')}}">
        <i class="now-ui-icons shopping_shop"></i>
        <span>خانه</span>
    </a>
</div>
@section('scripts')
    <script>
        $(document).ready(function() {
            $('#main_search_mobile , #main_search').on('input', function() {
                let search = $(this).val();
                if (search.length > 1) {
                    $('.close-search').remove()
                    $(this).parent().append(`<a href="#" class="close-search"><img alt="بستن" style="height: 18px;position: absolute;left: 75px;top: 9px;" src="{{asset('assets/img/circle-xmark-solid.svg')}}" ></a>`);
                    $.ajax({
                        url: '{{route('ajax_main_header_search')}}',
                        method: 'GET',
                        data: { search },
                        success: function(response) {
                            $('.search-box-list li').remove()
                            if (response.status === 'success') {

                                const result = response.data;
                                for (let dataKey in result) {
                                    if (result[dataKey].length > 0) {
                                        const titles = {
                                            product_category: 'نتایج جست و جو در دسته محصولات',
                                            post_category: 'نتایج جست و جو در دسته بندی ها',
                                            post: 'نتایج جست و جو در پست ها',
                                            product: 'نتایج جست و جو در محصولات',
                                            tag: 'نتایج جست و جو در تگ ها'
                                        };
                                        if (titles[dataKey]) {
                                            $('.search-box-list').append(`
                                                <li class="list-group-title">
                                                    <a href="#">${titles[dataKey]}</a>
                                                </li>
                                            `);
                                            if(dataKey === 'product_category'){
                                                result[dataKey].forEach(function(item, index) {
                                                    $('.search-box-list').append(`
                                                <li class="list-group-item contsearch">
                                                    <a href="/products_category/${item.slug}" class="gsearch">
                                                        <i class="fad fa-search"></i>
                                                        ${item.name}
                                                    </a>
                                                </li>`);
                                                });
                                            }
                                            else if (dataKey === 'post_category'){
                                                result[dataKey].forEach(function(item, index) {
                                                    $('.search-box-list').append(`
                                                <li class="list-group-item contsearch">
                                                    <a href="/posts?cat=${item.slug}" class="gsearch">
                                                        <i class="fad fa-search"></i>
                                                        ${item.name}
                                                    </a>
                                                </li>`);
                                                });
                                            }
                                            else if (dataKey === 'post'){
                                                result[dataKey].forEach(function(item, index) {
                                                    let url = '';
                                                    if(item.type == 'guide'){
                                                        url = '/product_guide/' + item.slug;
                                                    }else if(item.type == 'category'){
                                                        url = '/post/' + item.slug;
                                                    }
                                                    $('.search-box-list').append(`
                                                        <li class="list-group-item contsearch">
                                                            <a href="${url}" class="gsearch">
                                                                <i class="fad fa-search"></i>
                                                                ${item.title}
                                                            </a>
                                                        </li>
                                                    `);
                                                });
                                            }
                                            else if (dataKey === 'product'){
                                                result[dataKey].forEach(function(item, index) {
                                                    $('.search-box-list').append(`
                                                <li class="list-group-item contsearch">
                                                    <a href="/show_product_by_id/${item.id}" class="gsearch">
                                                        <i class="fad fa-search"></i>
                                                        ${item.product_name}
                                                    </a>
                                                </li>`);
                                                });
                                            }

                                        }


                                    }

                                }


                            }
                        },
                        complete: function() {
                            $('.search-box-list').show();
                        }
                    });
                } else {
                    $('.search-box-list').hide();
                    $('.search-box-list li').remove()
                    $('.close-search').remove()
                }

            });

            $(document).on('click' , '.close-search' , function(e) {
                e.preventDefault()
                $(this).siblings('input').val('')
                $('.search-box-list').hide();
                $('.search-box-list li').remove()
                $(this).remove()
            })
        });
    </script>
@endsection
