@extends('Admin.Layout.master')

@section('head-tag')
    <title>ایجاد پست</title>
    <link rel="stylesheet" href="">
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/ckeditor/ckeditor5.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/jsTree/style.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/select2/select2.min.css') }}">
    <style>
        .select2-search__field {
            font-family: inherit !important;
        }

        .sidebar-left {
            transition: all 0.3s ease;
        }

        @media (max-width: 767.98px) {
            .sidebar-left {
                position: fixed;
                right: 0;
                top: 0;
                height: 100vh;
                z-index: 1000;
                transform: translateX(0);
                transition: transform 0.3s ease;
            }

            .sidebar-left.collapsed {
                transform: translateX(100%);
            }
        }

        /* برای دسکتاپ */
        @media (min-width: 768px) {
            .sidebar-left.collapsed {
                display: none;
            }

            .main-content.expanded {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }

        .image-align-left {
            float: left;
            margin-right: 10px;
        }

        .image-align-right {
            float: right;
            margin-left: 10px;
        }

        .image-align-center {
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .image-inline {
            display: inline-block;
        }

        .image-side {
            float: right;
            margin-left: 10px;
        }

        .titled-hr {
            position: relative;
            border: none;
            height: 1px;
            background: linear-gradient(to right, #ccc 0%, #ccc 45%, transparent 45%, transparent 55%, #ccc 55%, #ccc 100%);
            margin: 40px 0;
        }

        .titled-hr::before {
            content: attr(title);
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 0 20px;
            color: #333;
            font-size: 16px;
            font-weight: bold;
            white-space: nowrap;
        }

        .primary-label {
            font-size: 0.85em;
            color: #6c757d;
        }

        .set-primary-btn {
            padding: 2px 6px;
            font-size: 0.75em;
        }
    </style>
    <style>
        .ck-editor__editable.ck-content {
            background-color: #fdfbf5 !important;
            border: 1px solid #fff2ab !important;
            border-radius: 4px !important;
            padding: 10px !important;
            direction: rtl !important;
            text-align: right !important;
            font-family: Tahoma, Arial, sans-serif !important;

        }

        .ck_wrap_content .ck-editor__editable.ck-content {
            min-height: 300px;
        }

        .ck-editor__editable.ck-content:focus {
            background-color: #fffefb !important;
            border: 1px solid #fff2ab !important;
        }

        .ck-toolbar {
            background-color: #fff5d7 !important;
        }
        .tooltip-inner{
            font-family: 'Vazir', sans-serif !important;
        }
    </style>
@endsection
@section('content')
    <div class="container-fluid pt-2">
        <nav aria-label="breadcrumb" class="mt-0 px-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item font-size-12"><a href="#">خانه</a></li>
                <li class="breadcrumb-item font-size-12"><a href="{{route('A_posts')}}">پست ها</a></li>
                <li class="breadcrumb-item font-size-12 active" aria-current="page">ایجاد پست جدید</li>
            </ol>
        </nav>
        <section class="">
            <form
                action="{{  route('A_s_create_post') }}"
                method="post" enctype="multipart/form-data" id="form">
                @csrf

                <!-- وضعیت (مخفی) -->
                <input type="hidden" id="publishType" name="status" value="published">
                <input type="hidden" id="main-image-delete-input" name="main_image_delete" value="0">
                <input type="hidden" id="primary-category" name="primary_category" value="">

                <div class="row px-3 flex-column-reverse flex-md-row">
                    <div class="col-12 col-md-8 col-lg-9 main-content">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <h4>ایجاد پست جدید</h4>

                                    </div>
                                    <button class="btn btn-sm toggle-sidebar-left d-none d-md-block" type="button">
                                        <i class="fa fa-arrow-left"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="row">

                                    <!-- عنوان -->
                                    <div class="col-12 py-3" style="background-color: #fdfbf5">
                                        <div class="form-group position-relative">
                                            <label for="title"> عنوان :<span class="text-danger mx-2">*</span> <img data-bs-toggle="tooltip" data-bs-placement="top" class="mx-2 help-field" data-bs-title="مهمترین بخش پست که عنوان حتما باید شامل کلمه کلیدی باشد" src="{{asset('assets/img/question.svg')}}" width="14"  alt="?" /></label>
                                            <input name="title" id="title" class="form-control form-control-lg"
                                                   type="text"
                                                   placeholder="عنوان پست" required
                                                   value="{{ old('title')}}">
                                            <span class="input-loader position-absolute "
                                                  style="left: 8px;top: 42px ; display: none"></span>
                                        </div>
                                        @error('title')
                                        <span
                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                            role="alert"><strong>{{ $message }}</strong>
                                        </span>
                                        @enderror

                                        <div class="form-group mt-3 position-relative">
                                            <label for="slug">نامک :  <img data-bs-toggle="tooltip" data-bs-placement="top" class="mx-2 help-field" data-bs-title="نامک یا اسلاگ شناسه‌ای خوانا و قابل‌استفاده در URL یک صفحه وب است که معمولاً به صورت یک رشته‌ی متن ساده و کوتاه ایجاد می‌شود و به جای استفاده از شناسه‌های عددی یا پیچیده، در آدرس صفحات وب قرار می‌گیرد و بهتر است از کلمات با مفهوم انگلیسی استفاده شود." src="{{asset('assets/img/question.svg')}}" width="14"  alt="?" /> </label>
                                            <input id="slug" name="slug" class="form-control" type="text"
                                                   placeholder="نامک" value="{{ old('slug') }}">
                                            <span class="input-loader position-absolute "
                                                  style="left: 8px;top: 36px ; display: none"></span>
                                        </div>
                                        @error('slug')
                                        <span
                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                            role="alert"><strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>

                                    <!-- تصویر اصلی -->
                                    <div class="col-12 py-3 mt-3" style="background-color: #fdfbf5">
                                        <div class="d-flex flex-row flex-wrap flex-md-nowrap">
                                            <div class="form-group" style="width: 100%">
                                                <label for="main-image">تصویر اصلی :</label>
                                                <div class="custom-file">
                                                    <input type="file" accept=".png,.jpg,.jpeg,.webp" name="main_image"
                                                           class="custom-file-input" id="main-image">
                                                    <label class="custom-file-label" for="main-image"
                                                           style="padding-right: 90px !important">
                                                        {{ old('main_image') ? basename(old('main_image')) : 'فایلی انتخاب نشده' }}
                                                    </label>
                                                </div>
                                                @error('main_image')
                                                <span
                                                    class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                                    role="alert"><strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>

                                            <div class="form-group mr-2" style="width: 200px">
                                                <label for="main-image">Alt تصویر : <img data-bs-toggle="tooltip" data-bs-placement="top" class="mx-2 help-field" data-bs-title="اگر تصویر لود نشود به جای آن Alt قرار میگیرد و همچنین تاثیر زیادی در سئو دارد." src="{{asset('assets/img/question.svg')}}" width="14"  alt="?" /></label>
                                                <input type="text" class="form-control" name="alt_main_image"
                                                       value="{{ old('alt_main_image') }}">
                                            </div>
                                        </div>

                                        @error('alt_main_image')
                                        <span
                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                            role="alert"><strong>{{ $message }}</strong>
                                        </span>
                                        @enderror

                                        <!-- نمایش تصویر فعلی در حالت ویرایش -->
                                        @if(old('main_image'))
                                            <div class="mt-2 main-image-preview">
                                                <img src="{{ asset(old('main_image')) }}"
                                                     alt="تصویر اصلی"
                                                     class="img-thumbnail" style="max-height: 150px;">
                                            </div>
                                            <a href="#" id="main-image-delete" class=" btn btn-outline-danger mt-3">حذف
                                                تصویر اصلی</a>
                                        @endif
                                    </div>

                                    <!-- خلاصه -->
                                    <div class="col-12 mt-3">
                                        <div class="form-group">
                                            <label for="summary" >متن خلاصه : <span class="text-danger mx-2">*</span><img data-bs-toggle="tooltip" data-bs-placement="top" class="mx-2 help-field" data-bs-title="نمایش در کارت های پست ها" src="{{asset('assets/img/question.svg')}}" width="14"  alt="?" /></label>
                                            <textarea class="form-control" name="summary"
                                                      id="summary">{{ old('summary') }}</textarea>
                                        </div>
                                        @error('summary')
                                        <span
                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                            role="alert"><strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>

                                    <!-- محتوا -->
                                    <div class="col-12 mt-3">
                                        <div class="form-group ck_wrap_content">
                                            <label for="content">توضیحات : <span class="text-danger mx-2">*</span><img data-bs-toggle="tooltip" data-bs-placement="top" class="mx-2 help-field" data-bs-title="بدنه و محتوای اصلی پست" src="{{asset('assets/img/question.svg')}}" width="14"  alt="?" /></label>
                                            <textarea class="form-control" name="content"
                                                      id="content">{{ old('content') }}</textarea>
                                        </div>
                                        @error('content')
                                        <span
                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                            role="alert"><strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <hr class="titled-hr" title="طبقه بندی محتوا">
                                    </div>

                                    <div class="col-12  py-3" style="background-color: #fdfbf5 ;">
                                        <div class="form-group" style="max-width: 200px">
                                            <label for="type">نوع پست :<span class="text-danger mx-2">*</span><img data-bs-toggle="tooltip" data-bs-placement="top" class="mx-2 help-field" data-bs-title="کاربرد این پست برای وبلاگ یا محصول می تواند باشد." src="{{asset('assets/img/question.svg')}}" width="14"  alt="?" /></label>
                                            <select id="type" name="type" class="form-control">
                                                <option
                                                    @selected(old('type') == 'product') value="product">
                                                    محصولات
                                                </option>
                                                <option
                                                    @selected(old('type') == 'guide') value="guide">
                                                    راهنمای محصولات
                                                </option>
                                                <option
                                                    @selected(old('type') == 'category') value="category">
                                                    عمومی
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- دسته‌بندی -->
                                    <div class="col-12  py-3" style="background-color: #fdfbf5;  ">
                                        <label for="category"><span>دسته بندی عمومی :</span><span class="text-danger mx-2">*</span><img data-bs-toggle="tooltip" data-bs-placement="top" class="mx-2 help-field" data-bs-title="دسته بندی های عمومی برای وبلاگ، اخبار، اطلاعیه و... کاربرد دارد." src="{{asset('assets/img/question.svg')}}" width="14"  alt="?" /></label>
                                        <div id="jstree"></div>
                                        <input type="hidden" name="categories" id="selected-categories-input"
                                               value="{{ old('categories', '[]') }}">
                                        @error('categories')
                                        <span
                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                            role="alert"><strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="col-12  py-3" style="background-color: #fdfbf5; ">
                                        <label for="category"><span>دسته بندی محصولات :</span><span class="text-danger mx-2">*</span><img data-bs-toggle="tooltip" data-bs-placement="top" class="mx-2 help-field" data-bs-title="دسته بندی های محصولات برای نمایش محتوای محصولات کاربرد دارد." src="{{asset('assets/img/question.svg')}}" width="14"  alt="?" /></label>
                                        <div id="jstreeProduct"></div>
                                        <input type="hidden" name="prd_categories" id="selected-products-input"
                                               value="{{ old('prd_categories', '[]') }}">
                                        @error('prd_categories')
                                        <span
                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                            role="alert"><strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <!-- برچسب‌ها -->
                                    <div class="col-12" style="background-color: #fdfbf5">
                                        <div class="form-group">
                                            <label for="tag">برچسب :<img data-bs-toggle="tooltip" data-bs-placement="top" class="mx-2 help-field" data-bs-title="برخی از ویژگی ها که در دسته بندی ها شامل نمی شود." src="{{asset('assets/img/question.svg')}}" width="14"  alt="?" /></label>
                                            <select class="multi-select form-control" id="tags" name="tags[]"
                                                    multiple="multiple">
                                                @foreach($tags as $tag)
                                                    <option value="{{ $tag->id }}"
                                                        @selected(in_array($tag->id, old('tags', [])))>
                                                        {{ $tag->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="my-2 rounded" style="color: #d3ba6e ; font-size: 0.9rem">
                                            با فشردن کلید Enter برچسب جدید ایجاد می شود.
                                        </div>
                                    </div>



                                    <div class="col-12">
                                        <hr class="titled-hr" title="تنظیمات سئو">
                                    </div>

                                    <!-- کلمات کلیدی -->
                                    <div class="col-12 col-md-6 py-3" style="background-color: #fdfbf5">
                                        <div class="form-group">
                                            <label for="meta_keywords">کلمه های کلیدی :<img data-bs-toggle="tooltip" data-bs-placement="top" class="mx-2 help-field" data-bs-title="کلمه کلیدی واژه‌ای است که کاربران برای جستجو در گوگل یا دیگر موتورهای جستجو استفاده می‌کنند تا به محتوای مرتبط برسند." src="{{asset('assets/img/question.svg')}}" width="14"  alt="?" /></label>
                                            <textarea placeholder="مثال : کلید1 , کلید2 , کلید3" class="form-control"
                                                      name="meta_keywords"
                                                      id="meta_keywords">{{ old('meta_keywords') }}</textarea>
                                        </div>
                                        @error('meta_keywords')
                                        <span
                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                            role="alert"><strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                        <div class=" my-2 rounded" style="color: #d3ba6e ; font-size: 0.9rem">
                                            بعد از هر کلمه کلیدی کلید TAB فشرده شود.
                                        </div>
                                    </div>

                                    <!-- توضیحات متا -->
                                    <div class="col-12 col-md-6 py-3" style="background-color: #fdfbf5">
                                        <div class="form-group">
                                            <label for="meta_description">توضیحات متا :<img data-bs-toggle="tooltip" data-bs-placement="top" class="mx-2 help-field" data-bs-title="توضیحات متا (Meta Description) متنی کوتاه است که زیر عنوان صفحه در نتایج جستجوی گوگل نمایش داده می‌شود و محتوای صفحه را خلاصه می‌کند." src="{{asset('assets/img/question.svg')}}" width="14"  alt="?" /></label>
                                            <textarea class="form-control" name="meta_description"
                                                      id="meta_description">{{ old('meta_description' ) }}</textarea>
                                        </div>
                                        @error('meta_description')
                                        <span
                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                            role="alert"><strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- دکمه‌های ارسال -->
                            <div class="card-footer py-3 d-flex flex-row-reverse">
                                <button class="btn btn-primary" type="submit">ذخیره و انتشار</button>
                                <button onclick="$('#publishType').val('draft');$('#form').submit()"
                                        id="submit_as_draft" class="btn text-primary ml-3" type="button">ذخیره پیشنویس
                                </button>
                            </div>
                            @if ($errors->any())
                                <div class="d-flex align-items-center px-3" id="message-container">
                                    @if ($errors->any())
                                        <div class="d-flex align-items-center px-3" id="message-container">
                                            <div class="alert alert-danger w-100" role="alert">
                                                <ul class="mb-0">
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- سایدبار اطلاعات -->
                    <div class="col-12 col-md-4 col-lg-3 sidebar-left1" id="sidebar-left">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="d-flex justify-content-between align-items-center w-100">
                                    <h4>اطلاعات</h4>
                                    <button class="btn btn-sm toggle-sidebar-setting d-md-none show-down" type="button">
                                        <i class="fa fa-chevron-down"></i>
                                    </button>
                                    <button class="btn btn-sm toggle-sidebar-setting d-md-none show-minus" type="button"
                                            style="display: none;">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush list-unstyled" style="font-size: 1rem">

                                    <!-- وضعیت -->
                                    <li class="list-group-item d-flex flex-row justify-content-between flex-wrap px-1 pb-3">
                                        <b>وضعیت :</b>
                                        <small
                                            class="text-dark">{{ __( 'public.'.old('status' ,'draft')  ) }}</small>
                                    </li>

                                    <!-- نویسنده -->
                                    <li class="list-group-item d-flex flex-row justify-content-between px-1">
                                        <b>نویسنده :</b>
                                        <small
                                            class="text-dark">{{ auth()->user()->name }}</small>
                                    </li>

                                    <!-- تاریخ ایجاد -->
                                    <li class="list-group-item d-flex flex-row justify-content-between px-1">
                                        <b>تاریخ ایجاد :</b>
                                        <small
                                            class="text-dark">اکنون</small>
                                    </li>

                                    <!-- تاریخ انتشار -->
                                    <li class="list-group-item d-flex flex-row flex-wrap justify-content-between px-1 position-relative">
                                        <b>تاریخ انتشار :</b>
                                        <a href="#" id="publishDateBtn"
                                           class="">{{ old('publishDate', 'فوری') }}</a>
                                        <input class="form-control form-control-sm mt-3" id="publishDate"
                                               name="publishDate"
                                               value="{{old('publishDate')}}"
                                               style="display: none" type="text"/>
                                        <span id="publishDateBox" class="w-100"></span>
                                        <a id="publishDateReset"
                                           class="text-danger position-absolute text-decoration-none"
                                           style="left: 14px;bottom: 15px ; display: none;" href="#">x</a>
                                    </li>
                                    <!-- انقضا -->
                                    <li class="list-group-item d-flex flex-row flex-wrap justify-content-between px-1 position-relative">
                                        <b>تاریخ انقضاء :</b>
                                        <a href="#" id="expiredDateBtn"
                                           class=" ">{{old('expiredDate' ,'تعین نشده')}}</a>
                                        <input class="form-control form-control-sm mt-3" name="expiredDate"
                                               id="expiredDate"
                                               value="{{old('expiredDate')}}"
                                               style="display: none" type="text"/>
                                        <span id="expiredDateBox" class="w-100"></span>
                                        <a id="expiredDateReset"
                                           class="text-danger position-absolute text-decoration-none"
                                           style="left: 14px;bottom: 15px ; display: none" href="#">x</a>
                                    </li>

                                    <!-- زمان مطالعه -->
                                    <li class="list-group-item d-flex flex-row justify-content-between align-items-center flex-wrap px-1">
                                        <b class="mt-1">زمان مطالعه<small>(دقیقه)</small> :</b>
                                        <input type="text" name="reading_time" class="text-left form-control"
                                               style="width: 70px" placeholder="مثلا 10"
                                               value="{{ old('reading_time') }}">
                                        @error('reading_time')
                                        <span
                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                            role="alert"><strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </li>

                                    <!-- نظرات -->
                                    <li class="list-group-item d-flex flex-row justify-content-between align-items-center flex-wrap px-1">
                                        <b>نظر دهی فعال باشد؟</b>
                                        <div class="form-check form-check-inline pr-0">
                                            <label class="form-check-label" for="allow_comments">بله</label>
                                            <input class="form-check-input" name="allow_comments" type="checkbox"
                                                   id="allow_comments"
                                                   value="1" @checked(old('allow_comments' ,true))>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </div>
@endsection
@section('script')
    <script src="{{ asset('admin-assets/vendor/ckeditor/ckeditor5.umd.js') }}"></script>
    <script src="{{ asset('admin-assets/vendor/jsTree/jstree.min.js') }}"></script>
    <script src="{{ asset('admin-assets/vendor/select2/select2.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            $('.toggle-sidebar-left').on('click', function () {
                var $sidebar_left = $('#sidebar-left');
                var $mainContent = $('.main-content');

                // برای موبایل
                if ($(window).width() < 768) {
                    $sidebar_left.toggleClass('collapsed');

                    // مدیریت overlay
                    if ($sidebar_left.hasClass('collapsed')) {
                        $('.overlay').remove();
                    } else {
                        var $overlay = $('<div class="overlay"></div>');
                        $('body').append($overlay);

                        $overlay.on('click', function () {
                            $sidebar_left.addClass('collapsed');
                            $(this).remove();
                        });
                    }
                } else {
                    $sidebar_left.toggleClass('collapsed');
                    $mainContent.toggleClass('expanded');
                }
            });
            $('.toggle-sidebar-setting').on('click', function () {
                var $sidebar = $('#sidebar-left .card-body');
                var $currentButton = $(this);
                var $otherButton = $('.toggle-sidebar-setting').not($currentButton);
                $currentButton.hide();
                $otherButton.show();
                if ($sidebar.is(':visible')) {
                    $sidebar.fadeOut(100).slideUp(300);
                } else {
                    $sidebar.slideDown(300).fadeIn(100);
                }
            });
        })
        $(document).ready(function () {

            const {
                ClassicEditor,
                Essentials,
                Bold,
                Italic,
                Font,
                Paragraph,
                Heading,
                Alignment,
                List,
                Link,
                Table,
                TableToolbar,
                BlockQuote,
                Code,
                CodeBlock,
                Highlight,
                Autoformat,
                Indent,
                IndentBlock,
                Undo,
                Redo,
                MediaEmbed,
                RemoveFormat,
                Image,
                ImageToolbar,
                ImageStyle,
                ImageTextAlternative,
                ImageUpload,
                ImageResizeEditing,
                ImageResizeHandles,
                FileRepository,

            } = CKEDITOR;

            ClassicEditor
                .create(document.querySelector(`#content`), {
                    language: 'fa',
                    contentsLangDirection: 'rtl',
                    plugins: [
                        Essentials, Bold, Italic, Font, Paragraph, Heading, Alignment, List, Link, Table, TableToolbar,
                        BlockQuote, Code, CodeBlock, Highlight, Autoformat, Indent, IndentBlock,
                        Image, ImageToolbar, ImageStyle, ImageTextAlternative, ImageUpload,
                        ImageResizeEditing, ImageResizeHandles, FileRepository,
                    ],
                    toolbar: [
                        'undo', 'redo', '|',
                        'heading', '|',
                        'bold', 'italic', '|',
                        'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', '|',
                        'alignment', '|',
                        'bulletedList', 'numberedList', '|',
                        'link', 'blockquote', 'code', 'codeBlock', '|',
                        'insertTable', 'mediaEmbed', '|',
                        'highlight', 'removeFormat',
                        'imageUpload', '|',
                    ],
                    table: {
                        contentToolbar: [
                            'tableColumn', 'tableRow', 'mergeTableCells',
                        ],
                    },
                    image: {
                        toolbar: [
                            'imageStyle:inline',
                            'imageStyle:block',
                            'imageStyle:side',
                            '|',
                            'toggleImageCaption',
                            'imageTextAlternative',
                            '|',
                            'imageStyle:alignLeft',
                            'imageStyle:alignCenter',
                            'imageStyle:alignRight',
                        ],
                        styles: [
                            {
                                name: 'inline',
                                title: 'Inline',
                                model: 'inline',
                                view: 'inline',
                            },
                            {
                                name: 'block',
                                title: 'Block',
                                model: 'block',
                                view: 'block',
                            },
                            {
                                name: 'side',
                                title: 'Side',
                                model: 'side',
                                view: 'side',
                            },
                            {
                                name: 'alignLeft',
                                title: 'چپ',
                                model: 'alignLeft',
                                view: {
                                    name: 'imageStyle:alignLeft',
                                    classes: 'image-align-left'
                                },
                            },
                            {
                                name: 'alignCenter',
                                title: 'وسط',
                                model: 'alignCenter',
                                view: {
                                    name: 'imageStyle:alignCenter',
                                    classes: 'image-align-center'
                                },
                            },
                            {
                                name: 'alignRight',
                                title: 'راست',
                                model: 'alignRight',
                                view: {
                                    name: 'imageStyle:alignRight',
                                    classes: 'image-align-right'
                                },
                            }
                        ],
                        resizeOptions: [
                            {
                                name: 'resizeImage:original',
                                value: null,
                                icon: 'original'
                            },
                            {
                                name: 'resizeImage:25',
                                value: '25',
                                icon: 'small'
                            },
                            {
                                name: 'resizeImage:50',
                                value: '50',
                                icon: 'medium'
                            },
                            {
                                name: 'resizeImage:75',
                                value: '75',
                                icon: 'large'
                            },
                            {
                                name: 'resizeImage:100',
                                value: '100',
                                icon: 'full'
                            }
                        ],
                        resizeUnit: '%'
                    },
                })
                .then(editor => {
                    const editable = editor.ui.view.editable.element;

                    editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
                        return new MyUploadAdapter(loader);
                    };
                })
                .catch(error => {
                    console.error(error);
                });


        })
        $(document).ready(function () {
            let treeData = @json($categoryTree);
            $('#jstree').jstree({
                'core': {
                    'data': treeData,
                    'themes': {
                        'name': 'default',
                        'responsive': true,
                        'rtl': true,
                        'dots': true,
                        'icons': true
                    },
                    'check_callback': true // برای اجازه دادن به تغییرات DOM
                },
                'plugins': ['wholerow', 'checkbox'],
                'checkbox': {
                    'keep_selected_style': false,
                    'three_state': true,
                    'cascade': 'undetermined',
                    'tie_selection': true
                }
            });

// تابع برای رندر کردن محتوای سفارشی نودها
            $('#jstree').on('open_node.jstree', function (e, data) {
                customizeNodeContent();
            }).on('ready.jstree', function () {
                customizeNodeContent();

                $(this).on('changed.jstree', function (e, data) {
                    $('#selected-categories-input').val(JSON.stringify(data.selected));
                    customizeNodeContent(); // هر بار که انتخاب تغییر کرد، UI را آپدیت کن
                });

                // بازگردانی انتخاب‌های قبلی
                let oldCategories = @json(old('categories', []));
                if (oldCategories && oldCategories.length > 0) {
                    const categoriesArray = typeof oldCategories === 'string' ? JSON.parse(oldCategories) : oldCategories;
                    const selectedIds = Array.isArray(categoriesArray) ? categoriesArray : [categoriesArray];
                    $('#jstree').jstree('select_node', selectedIds.map(String));
                }

                // بازگردانی دسته اصلی
                let primaryCategory = @json(old('primary_category',  null));
                if (primaryCategory) {
                    setPrimaryCategory(primaryCategory);
                }
            });

            function customizeNodeContent() {
                $('#jstree').find('li').each(function () {
                    let $li = $(this);
                    let nodeId = $li.attr('id');
                    let node = $('#jstree').jstree(true).get_node(nodeId);
                    let $a = $li.find('> a');
                    let originalText = node.text;
                    // حذف محتوای قبلی برای جلوگیری از تکرار
                    $a.find('.primary-label, .set-primary-btn').remove();
                    // اگر این نود دسته اصلی است
                    if (node.a_attr.is_primary) {
                        $a.append('<span class="primary-label text-muted ms-2">(اصلی)</span>');
                    } else if ($('#jstree').jstree(true).is_selected(nodeId)) {// اگر این نود انتخاب شده است
                        $a.append(
                            '<button type="button" class="btn btn-sm border-0 text-primary set-primary-btn ms-2">تغییر به اصلی</button>'
                        );
                    }
                });

                // اتصال رویداد کلیک به دکمه‌های "تغییر به اصلی"
                $('.set-primary-btn').off('click').on('click', function (e) {
                    e.preventDefault()
                    e.stopPropagation();
                    let $li = $(this).closest('li');
                    let nodeId = $li.attr('id');

                    setPrimaryCategory(nodeId);
                });
            }

            function setPrimaryCategory(categoryId) {
                // حذف وضعیت اصلی از همه نودها
                $('#jstree').find('li').each(function () {
                    let node = $('#jstree').jstree(true).get_node($(this).attr('id'));
                    if (node && node.a_attr.is_primary) {
                        node.a_attr.is_primary = false;
                        $(this).find('.primary-label').remove();
                    }
                });

                // تنظیم نود جدید به عنوان اصلی
                let node = $('#jstree').jstree(true).get_node(categoryId);
                if (node) {
                    $('#primary-category').val(categoryId);
                    node.a_attr.is_primary = true;
                    // $('#primary-category-input').val(categoryId);
                }

                // آپدیت UI
                customizeNodeContent();
            }


            let treeDataProduct = @json($productsTree);
            $('#jstreeProduct').jstree({
                'core': {
                    'data': treeDataProduct,
                    'themes': {
                        'name': 'default',
                        'responsive': false,
                        'rtl': true,
                        'dots': true,
                        'icons': true
                    },
                    'multiple': true
                },
                'plugins': ['wholerow', 'checkbox'], // اضافه کردن checkbox
                'checkbox': {
                    // 'keep_selected_style': false,
                    'three_state': false, // انتخاب والد باعث انتخاب فرزندان می‌شود
                    // 'cascade': 'undetermined', // حالت cascade
                    // 'tie_selection': true // جدا کردن انتخاب از selection
                }
            });

            $('#jstreeProduct').on('ready.jstree', function () {

                $(this).on('changed.jstree', function (e, data) {

                    $('#selected-products-input').val(JSON.stringify(data.selected))
                });
            });
            prd_oldCategories = @json(old('prd_categories', []));
            if (prd_oldCategories && prd_oldCategories.length > 0) {
                const prd_categoriesArray = typeof prd_oldCategories === 'string' ? JSON.parse(prd_oldCategories) : prd_oldCategories;
                const selectedIds = Array.isArray(prd_categoriesArray) ? prd_categoriesArray : [prd_categoriesArray];
                $('#jstreeProduct').jstree('select_node', selectedIds.map(String));
            }

            // // لود اولیه
            //
            // switch ($('#type').val()) {
            //     case 'product':
            //         $('#jstreeProduct').parent().slideDown().find('label span:first-child').text('دسته بندی محصولات')
            //         $('#jstree').parent().slideUp();
            //         break;
            //     case 'guide':
            //         $('#jstreeProduct').parent().slideDown().find('label span:first-child').text('دسته بندی راهنمای محصولات')
            //         $('#jstree').parent().slideUp();
            //         break;
            //     case 'category':
            //         $('#jstreeProduct').parent().slideUp()
            //         $('#jstree').parent().slideDown();
            //         break;
            // }
            // $('#type').on('change', function (event) {
            //
            //     switch ($(this).val()) {
            //         case 'product':
            //             $('#jstreeProduct').parent().slideDown().find('label span:first-child').text('دسته بندی محصولات')
            //             $('#jstree').parent().slideUp();
            //             break;
            //         case 'guide':
            //             $('#jstreeProduct').parent().slideDown().find('label span:first-child').text('دسته بندی راهنمای محصولات')
            //             $('#jstree').parent().slideUp();
            //             break;
            //         case 'category':
            //             $('#jstreeProduct').parent().slideUp()
            //             $('#jstree').parent().slideDown();
            //             break;
            //     }
            // })

            // if ($('#type').val() == 'product') {
            //     $('#jstree').parent().css('display', 'none');
            // } else {
            //     $('#jstreeProduct').parent().css('display', 'none');
            // }
        });
        $(document).ready(function () {

            $('#main-image').on('change', function (event) {
                const file = event.target.files[0];
                if (file) {
                    $(this).parent().find('label').text('نام فایل:' + file.name);
                } else {
                    $(this).parent().find('label').text('هیچ فایلی انتخاب نشده');
                }
            });
        })
        $(function () {
            // تاریخ انتشار
            $("#publishDate, #publishDateBox").persianDatepicker({
                onSelect: function () {
                    const selectedDate = $('#publishDate').val();
                    $('#publishDateBtn').text(selectedDate);
                }
            });

            // تاریخ انقضا
            $("#expiredDate, #expiredDateBox").persianDatepicker({
                onSelect: function () {
                    const selectedDate = $('#expiredDate').val();
                    $('#expiredDateBtn').text(selectedDate);
                }
            });
            $('#publishDateBtn').on('click', function (e) {
                e.preventDefault()
                $('#publishDate').slideToggle();
                $('#publishDateReset').slideToggle();
            })
            $('#expiredDateBtn').on('click', function (e) {
                e.preventDefault()
                $('#expiredDate').slideToggle();
                $('#expiredDateReset').slideToggle();
            })
            $('#expiredDateReset').on('click', function (e) {
                e.preventDefault()
                $('#expiredDate').val('');
                $('#expiredDateBtn').text('تعین نشده');
            })
            $('#publishDateReset').on('click', function (e) {
                e.preventDefault()
                $('#publishDate').val('');
                $('#publishDateBtn').text('فوری');
            })
            $('#main-image-delete').on('click', function (e) {
                e.preventDefault()
                $('.main-image-preview').remove();
                $('#main-image-delete-input').val(1);
                $(this).remove()
            })

        });

        class MyUploadAdapter {
            constructor(loader) {
                this.loader = loader;
            }

            upload() {
                return new Promise((resolve, reject) => {
                    this.loader.file
                        .then(file => {
                            const data = new FormData();
                            data.append('upload', file);
                            @if(isset($id) && $id)
                            data.append('id', {{$id}});
                            @endif
                            $.ajax({
                                url: '{{route('A_ajax_image_uploader')}}',
                                method: 'POST',
                                data: data,
                                processData: false,
                                contentType: false,
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            }).done(response => {
                                if (response.status == 'success') {
                                    resolve({default: response.data});
                                } else {
                                    reject(response.error || 'خطا در آپلود تصویر.');
                                }
                            })
                                .fail((xhr, status, error) => {
                                    console.error('Ajax failed:', {
                                        status: xhr.status,
                                        response: xhr.responseText,
                                        error: error
                                    });
                                    reject('خطا در ارتباط با سرور.');
                                });
                        }).catch(error => {
                        console.error('File loading failed:', error);
                        reject('خطا در بارگذاری فایل.');
                    });
                });
            }
        }


        $(document).ready(function () {
            $('#tags').select2({
                tags: true, // اجازه اضافه کردن تگ جدید
                tokenSeparators: [','],
                placeholder: "انتخاب یا ایجاد برچسب جدید"
            });
        });

        $(document).ready(function () {
            function checkField(field, value, target) {
                if (value.trim() === '') return;

                $.ajax({
                    url: '/ajax_check_slug_title',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        field: field,
                        value: value
                    },
                    success: function (response) {
                        $('.input-loader').hide()
                        if (response.status === 'error') {
                            $(target).next('.field-error').remove();
                            $(target).after(`<div class="py-2 field-error"><span class=" p-2 rounded text-white bg-danger text-sm"> ${response.message} </span></div>`);
                        } else {
                            $(target).next('.field-error').remove();
                            $(target).after(`<div class="py-2 field-error"><span class=" p-2 rounded text-white bg-success text-sm">${response.message}</span></div>`);
                        }
                    }
                });
            }

            $('#slug').on('input', function () {
                const val = $(this).val();
                if (val != '') {
                    $('#slug').parent().find('.input-loader').show();
                    checkField('slug', val, this);
                } else {
                    $('#slug').parent().find('.input-loader').hide();
                    $('.field-error').remove()
                }

            });

            $('#title').on('input', function () {
                const val = $(this).val();
                if (val != '') {
                    $('#title').parent().find('.input-loader').show();
                    checkField('title', val, this);
                } else {
                    $('#title').parent().find('.input-loader').hide();
                    $('.field-error').remove()
                }

            });
        });
        if (document.querySelector('#message-container .alert')) {
            window.scrollTo({
                top: document.documentElement.scrollHeight,
                behavior: 'smooth'
            });
        }
        $(document).ready(function() {
            $('#meta_keywords').on('keydown', function(e) {
                if (e.key === 'Tab') {
                    e.preventDefault();
                    let currentValue = $(this).val();
                    $(this).val(currentValue + ', ');
                }
            });
        });
        $(function () {
            function toggleTrees(value) {
                if (value === 'product' || value === 'guide') {
                    $('#jstreeProduct')
                        .parent()
                        .slideDown()
                        .find('label span:first-child')
                        .text(value === 'product' ? 'دسته بندی محصولات' : 'دسته بندی راهنمای محصولات');
                    $('#jstree').parent().slideUp();
                } else if (value === 'category') {
                    $('#jstreeProduct').parent().slideUp();
                    $('#jstree').parent().slideDown();
                }
            }

            toggleTrees($('#type').val());

            $('#type').on('change', function () {
                toggleTrees($(this).val());
            });
        });



    </script>

@endsection



