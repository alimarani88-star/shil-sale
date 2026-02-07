@extends('Customer.Layout.master')
@section('head-tag')
    <style>
        .tab-pane{
            /*height: calc(100vh - 1px);*/
            /*overflow: auto;*/
            /*padding-bottom: 50px;*/
        }
        .scroll-hidden {
            scrollbar-width: none;
        }

        /* Chrome, Edge, Safari */
        .scroll-hidden::-webkit-scrollbar {
            display: none;
        }

        .loading-more{

        }
        .loading-more {
            width: 30px;
            --b: 8px;
            aspect-ratio: 1;
            border-radius: 50%;
            padding: 1px;
            background: conic-gradient(#0000 10%, #7a7a7a) content-box;
            -webkit-mask:
                repeating-conic-gradient(#0000 0deg,#000 1deg 20deg,#0000 21deg 36deg),
                radial-gradient(farthest-side,#0000 calc(100% - var(--b) - 1px),#000 calc(100% - var(--b)));
            -webkit-mask-composite: destination-in;
            mask-composite: intersect;
            animation:l4 1s infinite steps(10);
        }
        @keyframes l4 {to{transform: rotate(1turn)}}


        .el-product-thumbnail img {
            width: 100%!important;
            max-width: 100%!important;
            aspect-ratio: 1 / 1;
            object-fit: cover;
        }

    /*    side filter*/
        .filter_menu, .filter_submenu {
            list-style: none;
            padding-left: 0;
            margin: 0;
        }

        .filter_menu-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            padding: 6px 10px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .filter_menu-item:hover {
            background-color: #f2f2f2;
        }

        .filter_menu-item .filter_arrow {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-left: 2px solid #666;
            border-bottom: 2px solid #666;
            transform: rotate(-45deg);
            transition: transform 0.3s;
            margin-left: 8px;
        }

        .filter_submenu {
            max-height: 0;
            overflow: hidden;
            transform: scaleY(0);
            transform-origin: top;
            transition: transform 0.3s ease, max-height 0.3s ease;
            margin-left: 15px;
        }

        .filter_submenu.open {
            transform: scaleY(1);
            max-height: 1000px;
        }

        .filter_menu-item.open > .filter_arrow {
            transform: rotate(135deg);
        }

    </style>
@endsection
@section('content')

    <div class="wrapper default">

        <!-- main -->
        <main class="search-page default mt-4">
            <div class="container">
                <div class="row">
                    <aside class="sidebar-page col-12 col-sm-12 col-md-4 col-lg-3 order-1 mt-0 ">
                        <div class="box">
                            <div class="box-header">دسته بندی ها</div>
                            <div class="box-content category-result">
                                <ul class="filter_menu">
                                    @foreach($categories as $lvl1)
                                        <li class="filter-item-li">
                                            <div class="filter_menu-item">
                                                <a href="#" class="filter_category-link" data-group="{{ $lvl1['id'] }}">
                                                    {{ $lvl1['name'] }}
                                                </a>
                                                @if(!empty($lvl1['children']))
                                                    <span class="filter_arrow"></span>
                                                @endif
                                            </div>

                                            @if(!empty($lvl1['children']))
                                                <ul class="filter_submenu">
                                                    @foreach($lvl1['children'] as $lvl2)
                                                        <li class="filter-item-li">
                                                            <div class="filter_menu-item">
                                                                <a href="#" class="filter_category-link" data-group="{{ $lvl2['id'] }}">
                                                                    {{ $lvl2['name'] }}
                                                                </a>
                                                                @if(!empty($lvl2['children']))
                                                                    <span class="filter_arrow"></span>
                                                                @endif
                                                            </div>

                                                            @if(!empty($lvl2['children']))
                                                                <ul class="filter_submenu">
                                                                    @foreach($lvl2['children'] as $lvl3)
                                                                        <li class="filter-item-li">
                                                                            <div class="filter_menu-item">
                                                                                <a href="#" class="filter_category-link" data-group="{{ $lvl3['id'] }}">
                                                                                    {{ $lvl3['name'] }}
                                                                                </a>
                                                                                @if(!empty($lvl3['children']))
                                                                                    <span class="filter_arrow"></span>
                                                                                @endif
                                                                            </div>

                                                                            @if(!empty($lvl3['children']))
                                                                                <ul class="filter_submenu">
                                                                                    @foreach($lvl3['children'] as $lvl4)
                                                                                        <li class="filter-item-li">
                                                                                            <a href="#" class="filter_category-link" data-group="{{ $lvl4['id'] }}">
                                                                                                {{ $lvl4['name'] }}
                                                                                            </a>
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

                            </div>
                        </div>
                    </aside>
                    <div class="col-12 col-sm-12 col-md-8 col-lg-9 order-2">
                        <div class="breadcrumb-section default">
                            <ul class="breadcrumb-list">
                                <li>
                                    <a href="#">
                                        <span>فروشگاه اینترنتی شیل ایران</span>
                                    </a>
                                </li>
                                @if(isset($dataCategory))
                                    <li><span>دسته محصولات</span></li>
                                    <li><strong>{{$dataCategory['name']}}</strong></li>
                                @endif
                            </ul>


                        </div>
                        <div class="listing default">
                            <div class="listing-header default">
                                <ul class="listing-sort nav nav-tabs justify-content-center" role="tablist"
                                    data-label="مرتب‌سازی بر اساس :">

                                    <li>
                                        <a class="active" data-toggle="tab" href="#new" role="tab" aria-expanded="true">جدیدترین</a>
                                    </li>
                                    <li class="d-none">
                                        <a data-toggle="tab" href="#most-seller" role="tab"
                                           aria-expanded="false">پرفروش‌ترین‌</a>
                                    </li>
                                    <li class="d-none">
                                        <a data-toggle="tab" href="#down-price" role="tab"
                                           aria-expanded="false">ارزان‌ترین</a>
                                    </li>
                                    <li class="d-none">
                                        <a data-toggle="tab" href="#top-price" role="tab"
                                           aria-expanded="false">گران‌ترین</a>
                                    </li>
                                    <li class="d-none">
                                        <a class="" data-toggle="tab" href="#related" role="tab"
                                           aria-expanded="false">مرتبط‌ترین</a>
                                    </li>
                                    <li class="d-none">
                                        <a data-toggle="tab" href="#most-view" role="tab"
                                           aria-expanded="false">پربازدیدترین</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content default text-center px-1">
                                <div data-page="{{isset($products) ? '2' : '1'}}" class="scroll-hidden tab-pane active show" id="new" role="tabpanel" aria-expanded="false">
                                    <div class="container no-padding-right">
                                        <ul class="row listing-items">
                                            @if(isset($products))
                                                @foreach($products as $product)
                                                    <li class="col-xl-3 col-lg-4 col-md-6 col-12 no-padding">
                                                        <div class="el-product-card">
                                                            <div class="el-product-thumbnail">
                                                                <a href="{{route('show_product_by_id' , ["id"=>$product['id']])}}">
                                                                    <img src="{{$product['mainImage'] ? $product['mainImage'] : asset('assets/img/no-image.jpg')}}" class="img-fluid"  alt="${item.product_name}">
                                                                </a>
                                                            </div>
                                                            <div class="el-product-card-body">
                                                                <div class="el-product-title">
                                                                    <h6><a href="{{route('show_product_by_id' , ["id"=>$product['id']])}}">{{$product['product_name']}}</a></h6>
                                                                </div>
                                                                <div class="el-product-info">
                                                                    <div class="el-product-status"><i class="fad fa-box-check"></i> موجود در انبار</div>
                                                                    <div class="el-product-rating">
                                                                        <i class="fas fa-stars star"></i>
                                                                        <strong>5</strong>
                                                                    </div>
                                                                </div>
                                                                <div class="el-product-price">
                                                                    <span class="el-price-value">{{$product['price']}}</span>
                                                                    <span class="el-price-currency">{{$product['price_unit']}}</span>
                                                                </div>
                                                            </div>
                                                            <div class="el-product-card-footer">
                                                                <div class="el-product-seller-details">
                                                                    <i class="fad fa-store-alt"></i>
                                                                    <span class="el-product-seller-details-label">فروشنده:</span>
                                                                    <span class="el-product-seller-details--name">شیل ایران</span>
                                                                    <img src="{{asset('assets/img/logo-icon.png')}}" class="el-product-seller-details--logo-small" alt="seller-details--logo-small">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                                <div data-page="1" class="scroll-hidden tab-pane" id="most-view" role="tabpanel" aria-expanded="false">
                                    <div class="container no-padding-right">
                                        <ul class="row listing-items">
                                        </ul>
                                    </div>
                                </div>
                                <div data-page="1" class="scroll-hidden tab-pane" id="most-seller" role="tabpanel" aria-expanded="false">
                                    <div class="container no-padding-right">
                                        <ul class="row listing-items">
                                        </ul>
                                    </div>
                                </div>
                                <div data-page="1" class="scroll-hidden tab-pane" id="down-price" role="tabpanel" aria-expanded="false">
                                    <div class="container no-padding-right">
                                        <ul class="row listing-items">
                                        </ul>
                                    </div>
                                </div>
                                <div data-page="1" class="scroll-hidden tab-pane" id="top-price" role="tabpanel" aria-expanded="false">
                                    <div class="container no-padding-right">
                                        <ul class="row listing-items">
                                        </ul>
                                    </div>
                                </div>
                                <div data-page="1" class="scroll-hidden tab-pane" id="related" role="tabpanel" aria-expanded="true">
                                    <div class="container no-padding-right">
                                        <ul class="row listing-items">

                                        </ul>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </main>
        <!-- main -->

    </div>

@endsection



@section('script')
    <script>
        $(document).ready(function() {

            let init_id = $('.tab-content .tab-pane.active').attr('id');
            loadMoreProducts(init_id);

            $('.listing-sort a').each(function(a_index,a) {
                let a_id = $(a).attr('href')
                $(a_id).append(`
                 <div class="more px-4">
                                <button  class="loadMoreBtn mx-auto btn bg-light d-flex flex-row align-items-center justify-content-center w-100">
                                    <span class="loading-more  mx-2" ></span>
                                    <b class=" mx-2" style="font-size: 16px;color: #7a7a7a"> بارگذاری محصولات</b>
                                </button>
                            </div>`);
                $(a).on('click' , function() {
                    let page = $(a_id).attr('data-page') ;
                    if(parseInt(page) === 1){
                        tab = a_id.replace('#','');
                        loadMoreProducts(tab);
                    }

                })
            })

            $(document).on('click' ,'.loadMoreBtn' , function() {
                let tab = $(this).parents('.tab-pane').attr('id')
                @if(!isset($products))
                loadMoreProducts(tab)
                @endif

            })

            $(window).on('scroll', function() {
                const scrollBottom = $(window).scrollTop() + $(window).innerHeight();
                const scrollHeight = $(document).height();

                if (scrollBottom + 600 >= scrollHeight) {
                    const activeTab = $('.tab-pane.active');
                    const tabId = activeTab.attr('id');
                    if (activeTab.attr('data-page') !== '0' && activeTab.attr('data-loading') === 'false') {
                        activeTab.attr('data-loading', true);
                        loadMoreProducts(tabId);
                    }
                }
            });


        });
        function generate_prd_card(data = [], tab_id = 'new') {
            const $list = $('#' + tab_id).find('.listing-items');
            data.forEach(item => {
                let mainImage = null;

                if (Array.isArray(item.images)) {
                    item.images.forEach(function(image) {
                        if (parseInt(image.position) === 1 && parseInt(image.primary) === 1) {
                            mainImage = image;
                        }
                    });
                }

                const notImage = '{{asset('assets/img/no-image.jpg')}}';
                const logoImage = '{{asset('assets/img/logo-icon.png')}}';

                const html = `
        <li class="col-xl-3 col-lg-4 col-md-6 col-12 no-padding">
            <div class="el-product-card">
                <div class="el-product-thumbnail">
                    <a href="/show_product_by_id/${item.id}">
                        <img src="${mainImage ? '/get_image_by_id/' + mainImage.id : notImage}" class="img-fluid" alt="${item.product_name}">
                    </a>
                </div>
                <div class="el-product-card-body">
                    <div class="el-product-title">
                        <h6><a href="/show_product_by_id/${item.id}">${item.product_name}</a></h6>
                    </div>
                    <div class="el-product-info">
                        <div class="el-product-status"><i class="fad fa-box-check"></i> موجود در انبار</div>
                        <div class="el-product-rating">
                            <i class="fas fa-stars star"></i>
                            <strong>5</strong>
                        </div>
                    </div>
                    <div class="el-product-price">
                        <span class="el-price-value">${item.price}</span>
                        <span class="el-price-currency">ریال</span>
                    </div>
                </div>
                <div class="el-product-card-footer">
                    <div class="el-product-seller-details">
                        <i class="fad fa-store-alt"></i>
                        <span class="el-product-seller-details-label">فروشنده:</span>
                        <span class="el-product-seller-details--name">شیل ایران</span>
                        <img src="${logoImage}" class="el-product-seller-details--logo-small" alt="seller-details--logo-small">
                    </div>
                </div>
            </div>
        </li>`;

                $list.append(html);
            });


        }
        function loadMoreProducts(tabId) {

            const page = $(`#${tabId}`).attr('data-page');

            if(parseInt(page) === 0){
                $('.more button').attr('disabled' , true)
            }
            const params = getUrlParams()
            let groupId = '{{isset($dataCategory) ? $dataCategory['id'] : 0}}'
            if (params.g && /^\d+$/.test(params.g)) {
                groupId = parseInt(params.g);
            }

            $('.more span.loading-more').show()
            $('.more button b').text('در حال بارگذاری')
            $.ajax({
                url: '{{route('ajax_load_products')}}',
                data: { tab: tabId, page , groupId },
                success: function(response) {
                    if (response.status === 'success') {
                        generate_prd_card(response.data.data, tabId);
                        if (response.data.data.length > 0) {
                            $(`#${tabId}`).attr('data-page', parseInt(page)+1);
                        }else {
                            $(`#${tabId}`).attr('data-page', 0);
                            $('.more button ').attr('disabled' , true)
                        }
                        $('.more button b').text(`نمایش ${response.data.to ? response.data.to : response.data.total } از ${response.data.total} محصول`)
                    } else {
                        Swal.fire({
                            toast: true,
                            position: 'bottom-end',
                            icon: 'error',
                            title: 'خطا در بارگیری',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                },
                complete: function() {
                    $('.loading-more').hide()
                    $(`#${tabId}`).attr('data-loading' , false)
                }
            });
        }
        function getUrlParams() {
            const urlParams = new URLSearchParams(window.location.search);
            const params = {};

            for (const [key, value] of urlParams.entries()) {
                params[key] = value;
            }

            return params;
        }
    </script>
    <script>
        $(function() {

            $('.filter_menu').on('click', '.filter-item-li', function(e) {
                if ($(e.target).closest('.filter_category-link').length) {
                    return;
                }
                const $submenu = $(this).children('.filter_submenu');
                const $menuItem = $(this).children('.filter_menu-item');
                if ($submenu.length) {
                    $submenu.toggleClass('open');
                    $menuItem.toggleClass('open');
                }
                e.stopPropagation();
            });

            $('.filter_menu').on('click', '.filter_category-link', function(e) {
                e.preventDefault();
                const id = $(this).data('group');
                const url = new URL(window.location.href);
                url.searchParams.set('g', id);
                window.history.pushState({}, '', url);

                const activeTab = $('.tab-pane.active');
                const tabId = activeTab.attr('id');

                $('.listing-sort a').each(function(a_index,a) {
                   $($(a).attr('href')).attr('data-page' , '1')
                    $($(a).attr('href')).find('ul.listing-items li').remove()
                })

                loadMoreProducts(tabId)

            });


        });
    </script>

@endsection
