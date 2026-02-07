@extends('Admin.Layout.master')

@section('head-tag')

    <title>مدیریت پست‌ها</title>
    <style>
        .lozad {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            transition: opacity 0.3s ease;
            opacity: 0.7;
        }

        .lozad.loaded {
            background: none;
            animation: none;
            opacity: 1;
        }

        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* جلوگیری از لرزش هنگام لود */
        .lozad {
            min-height: 50px;
            display: inline-block;
        }
        .lozad {
            position: relative;
        }



        /* ==================== استایل جدول ==================== */

        .dataTables_wrapper {
            direction: rtl;
        }

        .column-search {
            width: 100%;
        }

        .table th {
            text-align: center;
        }

        .dataTables_filter {
            display: none;
        }
        .filters span.dt-column-order{
            display: none;
        }
    </style>

@endsection

@section('content')
    <div class="container-fluid pt-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">خانه</a></li>
                <li class="breadcrumb-item active">پست‌ها</li>
            </ol>
        </nav>

        <section class="row">
            <div class="col-12">
                <section class="main-body-container">
                    <section class="d-flex justify-content-between align-items-center mt-4 mb-3 border-bottom pb-2">
                        <a href="{{ route('A_create_post') }}" class="btn btn-info btn-sm">
                            <i class="fa fa-plus"></i> ایجاد پست جدید
                        </a>
                    </section>

                    <section class="table-responsive">
                        <table id="posts-table" class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>تصویر</th>
                                <th>عنوان</th>
                                <th>نوع</th>
                                <th>دسته‌بندی</th>
                                <th>برچسب</th>
                                <th>نویسنده</th>
                                <th>وضعیت</th>
                                <th>تاریخ ایجاد</th>
                                <th>عملیات</th>
                            </tr>
                            <tr class="filters">
                                <th></th>
                                <th></th>
                                <th><input type="text" class="form-control form-control-sm column-search" placeholder="جستجو..." data-column="2"></th>
                                <th>
                                <select class="form-control form-control-sm column-search"  data-column="3">
                                    <option value="">همه</option>
                                    <option value="product">محصول/گروه</option>
                                    <option value="category">عمومی</option>
                                </select>
                                </th>
                                <th><input type="text" class="form-control form-control-sm column-search" placeholder="جستجو..." data-column="4"></th>
                                <th><input type="text" class="form-control form-control-sm column-search" placeholder="جستجو..." data-column="5"></th>
                                <th><input type="text" class="form-control form-control-sm column-search" placeholder="جستجو..." data-column="6"></th>
                                <th>
{{--                                    <input type="text" class="form-control form-control-sm column-search" placeholder="جستجو..." data-column="7">--}}
                                    <select class="form-control form-control-sm column-search"  data-column="7">
                                        <option value="">همه</option>
                                        <option value="published">منتشر شده</option>
                                        <option value="trash">زباله دان</option>
                                        <option value="draft">پیشنویس</option>
                                    </select>
                                </th>
                                <th><input type="text" class="form-control form-control-sm column-search" placeholder="جستجو..." data-column="8"></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </section>
                </section>
            </div>
        </section>
    </div>
@endsection

@section('script')
    <script src="{{asset('admin-assets/vendor/lozad/lozad.min.js')}}"></script>

    <script>
        $(document).ready(function() {

            // ==================== تنظیمات اولیه ====================

            let searchTimeout = null;
            function initLozad() {
                lozadObserver = lozad('.lozad', {
                    rootMargin: '100px 0px',
                    threshold: 0.01,
                    enableAutoReload: true,
                    loaded: function(el) {
                        if (el.tagName.toLowerCase() === 'img') {
                            el.addEventListener('load', function() {
                                el.classList.add('loaded');
                            }, { once: true });
                        } else {
                            el.classList.add('loaded');
                        }
                    }
                });

                lozadObserver.observe();
            }
            // ==================== DataTable ====================

            const table = $('#posts-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('A_ajax_get_posts') }}",
                    type: 'GET'
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'main_image', name: 'main_image', orderable: false, searchable: false },
                    { data: 'title', name: 'title' },
                    { data: 'type', name: 'type' },
                    { data: 'category', name: 'category' },
                    { data: 'tag', name: 'tag' },
                    { data: 'author', name: 'author' },
                    { data: 'status', name: 'status' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[8, 'desc']],
                pageLength: 10,
                language: {
                    "sEmptyTable": "هیچ داده‌ای وجود ندارد",
                    "sInfo": "نمایش _START_ تا _END_ از _TOTAL_ رکورد",
                    "sInfoEmpty": "نمایش 0 تا 0 از 0 رکورد",
                    "sInfoFiltered": "(فیلتر از _MAX_ رکورد)",
                    "sLengthMenu": "نمایش _MENU_ رکورد",
                    "sLoadingRecords": "در حال بارگذاری...",
                    "sProcessing": "در حال پردازش...",
                    "sSearch": "جستجو:",
                    "sZeroRecords": "رکوردی یافت نشد",
                    "oPaginate": {
                        "sFirst": "ابتدا",
                        "sLast": "انتها",
                        "sNext": "بعدی",
                        "sPrevious": "قبلی"
                    }
                },
                drawCallback: function() {
                    requestAnimationFrame(function() {
                        initLozad();
                    });
                }
            });

            // ==================== جستجو در ستون‌ها ====================

            $('.column-search').on('keyup change', function() {
                const columnIndex = $(this).data('column');
                const value = this.value;

                clearTimeout(searchTimeout);

                searchTimeout = setTimeout(function() {
                    table.column(columnIndex).search(value).draw();
                }, 300);
            });


            $('.filters *').on('click' , function (e) {
                e.stopPropagation()
            })

        });
    </script>

@endsection
