@extends('Admin.Layout.master')

@section('head-tag')
    <title>ایجاد دسته بندی عمومی</title>
    <link rel="stylesheet" href="">
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/select2/select2.min.css') }}">
    <style>
        .preview {
            border: 3px dashed #e6e6e6;
            border-radius: 10px;
            width: 100%;
            max-height: 400px;
            aspect-ratio: 3 / 4; /* نسبت 3 به 4 */
            object-fit: cover;   /* اگر داخلش تصویر باشد */
        }
        .preview img {
            max-width: 100%;
            max-height: 100%;
        }
        /*استایل فلش زدن */
        @keyframes flash {
            0% {
                background-color: transparent;
                color: #374151;
            }
            50% {
                background-color: #bababa;
                color: #b45309;
            }
            100% {
                background-color: transparent;
                color: #9ca3af;
            }
        }

        .flash-animation {
            animation: flash 0.6s ease-in-out;
        }
    </style>
@endsection

@section('content')

    <div class="container-fluid pt-2">
        <nav aria-label="breadcrumb" class="mt-0 px-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item font-size-12"><a href="#">خانه</a></li>
                <li class="breadcrumb-item font-size-12"><a href="{{route('A_categories')}}">دسته ها</a></li>
                <li class="breadcrumb-item font-size-12 active" aria-current="page">ایجاد دسته جدید</li>
            </ol>
        </nav>
        <section class="main-body-container">
            <div class="main-body-container-header">
                @if(isset($categoryData['id']))
                   <h4> ویرایش دسته بندی {{$categoryData['name']}}</h4>
                @endif

            </div>

            <div class="d-flex justify-content-between align-items-center mt-2 mb-4 border-bottom pb-2">
                <a href="{{ url()->previous() }}" class="btn btn-info btn-sm">بازگشت</a>
            </div>

            <form
                action="{{ route('A_s_edit_category' , ["id"=>$categoryData['id']]) }}"
                method="post" enctype="multipart/form-data" id="form">
                @csrf

                <input id="delete_image" type="hidden" name="delete_image" value="0" >
                <div class="row">
                    {{-- عنوان --}}
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label for="name">عنوان :<span class="text-danger mx-2">*</span></label>
                            <input name="name"
                                   class="form-control form-control-lg @error('name') is-invalid @enderror"
                                   type="text"
                                   placeholder="عنوان دسته بندی"
                                   value="{{$categoryData['name']??'' }}">
                            @error('name')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    {{-- اسلاگ --}}
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label for="slug">اسلاگ :<span class="text-danger mx-2">*</span></label>
                            <input name="slug"
                                   class="form-control form-control-lg @error('slug') is-invalid @enderror"
                                   type="text"
                                   placeholder="اسلاگ دسته بندی"
                                   value="{{ $categoryData['slug']??'' }}">
                            @error('slug')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label for="type">نوع محتوا :<span class="text-danger mx-2">*</span></label>
                            <select id="content-type" class=" form-control" name="type">
                                <option value="">انتخاب نوع</option>
                                <option @selected($categoryData['type']== 'product') value="product">گروه محصولات</option>
                                <option @selected($categoryData['type']== 'post') value="post">پست های عمومی</option>
                            </select>
                            @error('type')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label for="parent_id">والد :</label>
                           <select id="parent_ids_list" class="multi-select form-control" name="parent_id">
                               <option value="">انتخاب دسته والد</option>
                               @foreach($categories as $category)
                                   <option @selected($categoryData['parent_id'] == $category->id) class="option-item" value="{{$category->id}}">{{$category->name}}</option>
                               @endforeach
                           </select>
                            @error('parent_id')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12 col-md-3 app-relation-main">
                        <div class="form-group">
                            <label for="parent_id">گروه اصلی در App :</label>
                           <select id="app_main_group" class="multi-select form-control" name="app_main_group">
                               <option value="">انتخاب گروه اصلی App</option>
                               @foreach($app_main_groups as $app_main_group)
                                   <option @selected($app_main_group['id'] == $categoryData['app_id'])  value="{{$app_main_group['id']}}">{{$app_main_group['name']}}</option>
                               @endforeach
                           </select>
                            @error('app_main_group')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12 col-md-3 app-relation-group">
                        <div class="form-group">
                            <label for="app_group">گروه در App :</label>
                           <select id="app_group" class="multi-select form-control" name="app_group">
                               <option value="">انتخاب گروه App</option>
                               @foreach($app_groups as $app_group)
                                   <option @selected($app_group['id'] == $categoryData['app_id'])  value="{{$app_group['id']}}">{{$app_group['title']}}</option>
                               @endforeach
                           </select>
                            @error('app_group')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="d-none d-md-block  col-md-5 ">
                    </div>



                    {{-- آپلود تصویر --}}
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="image">تصویر دسته :
                                <small class="opacity-50">پیشنهادی : (400 * 300 پیکسل)</small>
                            </label>
                            <input id="image"
                                   name="image"
                                   class="form-control form-control-lg @error('image') is-invalid @enderror"
                                   type="file">
                            @error('image')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                    </div>

                    {{-- پیش نمایش تصویر --}}
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="preview">پیش نمایش تصویر: </label>
                            <div class="preview d-flex flex-column justify-content-center align-items-center"
                                 style="width:100%; height:400px; border:1px dashed #ccc; border-radius:8px; overflow:hidden;">
                                @if(isset($categoryData['image']) && $categoryData['image'])
                                    <img  src="{{route('get_post_image_by_id' , ["id"=>$categoryData['image']])}}"
                                         style="width:100%; height:100%; object-fit:cover;" alt="">
                                @endif
                            </div>
                        </div>
                        @if(isset($categoryData['image']) && $categoryData['image'])
                            <button type="submit" class="btn btn-outline-danger btn-block"
                                    onclick="$('#delete_image').val('1');">
                                <i class="fa fa-trash mx-1"></i>حذف تصویر
                            </button>
                        @endif
                    </div>

                    {{-- دکمه ذخیره --}}
                    <div class="col-12">
                       @if(isset($categoryData['id']))
                            <button class="btn btn-primary" type="submit">به روزرسانی</button>
                       @endif
                    </div>
                </div>
            </form>

        </section>

    </div>
@endsection
@section('script')
    <script src="{{ asset('admin-assets/vendor/select2/select2.min.js') }}"></script>
    <script>
        document.getElementById('image').addEventListener('change', function (event) {
            const preview = document.querySelector('.preview');
            preview.innerHTML = ""; // متن "بدون تصویر" پاک شود

            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = document.createElement("img");
                    img.src = e.target.result;
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = '<b style="font-size: 3rem; color: #e1e1e1">بدون تصویر</b>';
            }
        });
        $(document).ready(function() {
            $('#content-type').on('change', function() {
                if(!$(this).val()){return}
                $.ajax({
                    url: '{{route('A_parents_categories')}}',
                    method: 'GET',
                    data: {
                        type: $(this).val()
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })
                    .done(function(res) {
                        $('.option-item').remove();
                        if(res.status === 'success'){
                            const categories = res.data ;
                            categories.forEach(function(item) {
                                $('#parent_ids_list').append(`<option class="option-item" value="${item.id}">${item.name}</option>`)
                            })
                        }else {
                            Swal.fire({
                                title: 'خطا!',
                                text: res.message,
                                icon: 'error',
                                confirmButtonText: 'متوجه شدم',
                            }) ;
                        }
                    })
            })
        })

        $(document).ready(function() {
            // ─────────────────────────────────────────────────────────
            //  Select2 با allowClear -- اجرا دوباره select2 به دلیل نیاز به پاک کردن دیگری
            // ─────────────────────────────────────────────────────────
            $('.app-relation-main select').select2({
                language: 'fa',
                dir: 'rtl',
                allowClear: true,
                placeholder: 'انتخاب گروه اصلی App',
                width: '100%'
            });

            $('.app-relation-group select').select2({
                language: 'fa',
                dir: 'rtl',
                allowClear: true,
                placeholder: 'انتخاب گروه App',
                width: '100%'
            });
            const $mainSelect = $('.app-relation-main select');
            const $groupSelect = $('.app-relation-group select');
            const initialMainValue = $mainSelect.val() || '';
            const initialGroupValue = $groupSelect.val() || '';
            let isProgrammaticChange = false;
            // ─────────────────────────────────────────────────────────
            // تغییر Main Select
            // ─────────────────────────────────────────────────────────
            $mainSelect.on('select2:select', function(e) {
                let currentValue = e.params.data.id;
                if (currentValue && currentValue !== initialMainValue) {
                    isProgrammaticChange = true;
                    $groupSelect.val(null).trigger('change');
                    showFlash($groupSelect);
                    setTimeout(() => {
                        isProgrammaticChange = false;
                    }, 50);
                }
            });
            // ─────────────────────────────────────────────────────────
            // تغییر Group Select
            // ─────────────────────────────────────────────────────────
            $groupSelect.on('select2:select', function(e) {
                let currentValue = e.params.data.id;
                if (currentValue && currentValue !== initialGroupValue) {
                    isProgrammaticChange = true;
                    $mainSelect.val(null).trigger('change');
                    showFlash($mainSelect);
                    setTimeout(() => {
                        isProgrammaticChange = false;
                    }, 50);
                }
            });
            // ─────────────────────────────────────────────────────────
            // تابع نمایش فلش
            // ─────────────────────────────────────────────────────────
            function showFlash($element) {
                let $box = $element.closest('[class*="app-relation-"]');
                if ($box.length) {
                    $box.addClass('flash-animation');
                    setTimeout(() => {
                        $box.removeClass('flash-animation');
                    }, 600);
                }
            }
        });
    </script>


@endsection



