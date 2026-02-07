@extends('Admin.Layout.master')

@section('head-tag')
    <title>ویرایش اطلاعات درباره</title>
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/ckeditor/ckeditor5.css') }}">
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
    
    </style>
@endsection



@section('content')
    <div class="container-fluid pt-2">
        <nav aria-label="breadcrumb" class="mt-0 px-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item font-size-12"><a href="#">خانه</a></li>
                <li class="breadcrumb-item font-size-12"><a href="#">تنظیمات</a></li>
                <li class="breadcrumb-item font-size-12 active" aria-current="page">ویرایش اطلاعات درباره ما</li>
            </ol>
        </nav>


        <section class="row">
            <div class="col-12">
                <section class="main-body-container">
                    <section class="main-body-container-header">
                        <h5>
                            ویرایش اطلاعات درباره ما
                        </h5>
                    </section>
                    <div class="d-flex justify-content-between align-items-center mt-2 mb-4 border-bottom pb-2">
                        <a href="{{ route('A_home') }}" class="btn btn-info btn-sm">بازگشت</a>
                    </div>
                    <section>
                        <form action="{{ route('A_s_edit_about') }}" method="post" enctype="multipart/form-data"
                            id="form">
                            @csrf
                            <section class="row">

                                <section class="col-12 col-md-12">

                                    <div class="col-12 mt-3">
                                        <div class="form-group ck_wrap_content">
                                            <label for="about_text">توضیحات :</label>
                                            <textarea class="form-control" name="about_text" id="content">{{ old('about_text', $company_info->value ?? '') }}</textarea>
                                        </div>
                                        @error('about_text')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </section>
                                <section class="col-12">
                                    <button class="btn btn-primary btn-sm">ویرایش</button>
                                </section>
                            </section>
                        </form>
                    </section>

                </section>
            </div>
        </section>
    </div>
@endsection

@section('script')
<script src="{{ asset('admin-assets/vendor/ckeditor/ckeditor5.umd.js') }}"></script>

    <script>
        $(document).ready(function() {

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
                        Essentials, Bold, Italic, Font, Paragraph, Heading, Alignment, List, Link, Table,
                        TableToolbar,
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
                        styles: [{
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
                        resizeOptions: [{
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
    </script>
     @if(session('success'))
     <script>
         Swal.fire({
             icon: 'success',
             title: 'موفقیت‌آمیز',
             text: '{{ session('success') }}',
             confirmButtonText: 'باشه'
         })
     </script>
 @endif
@endsection
