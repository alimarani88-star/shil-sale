@extends('Admin.Layout.master')

@section('head-tag')
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/jsTree/style.min.css') }}">

    <title>دسته بندی های عمومی</title>


    <style>
        .search-container {
            margin-bottom: 15px;
        }

        .search-input {
            width: 100%;
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: #0ea5a4;
            box-shadow: 0 0 8px rgba(14, 165, 164, 0.2);
        }

        .search-buttons {
            display: flex;
            gap: 8px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .search-buttons button {
            flex: 1;
            min-width: 120px;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.3s ease;
        }

        .clear-search-btn {
            background-color: #f0f0f0;
            color: #333;
        }

        .clear-search-btn:hover {
            background-color: #e0e0e0;
        }

        .expand-all-btn {
            background-color: #0ea5a4;
            color: white;
        }

        .expand-all-btn:hover {
            background-color: #0d9488;
        }

        .collapse-all-btn {
            background-color: #f59e0b;
            color: white;
        }

        .collapse-all-btn:hover {
            background-color: #d97706;
        }

        .search-results-info {
            margin-top: 10px;
            padding: 10px;
            background-color: #f0f9ff;
            border-right: 4px solid #0ea5a4;
            border-radius: 4px;
            font-size: 13px;
            color: #0d9488;
            display: none;
        }

        .search-results-info.show {
            display: block;
        }

        /* جستجو نتایج */
        .jstree-search {
            background: #fef3c7 !important;
            font-weight: bold;
        }

        /* گره‌های منطبق */
        .jstree-node.jstree-matched > .jstree-wholerow {
            background: rgba(14, 165, 164, 0.1) !important;
        }

        /* گره‌های انتخاب شده */
        .jstree-node.jstree-selected > .jstree-wholerow {
            background: linear-gradient(135deg, #0ea5a4 0%, #06b6d4 100%) !important;
            color: white;
        }

        /* ریشه درخت */
        #jstree .root-node {
            font-weight: bold;
            font-size: 15px;
        }

        /* گره‌های نوع */
        #jstree .category-type-node {
            font-weight: 600;
            font-size: 14px;
        }

        /* گره‌های دسته‌بندی */
        #jstree .category-node {
            font-size: 13px;
        }

        /* آیکون‌ها */
        .jstree-default .jstree-icon {
            margin-left: 5px;
        }

        /* درخت */
        #jstree {
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 10px;
            background-color: #fafafa;
        }



        .jstree-default .jstree-wholerow {
            border-radius: 4px;
            margin: 2px 0;
        }

        .jstree-default .jstree-wholerow-hovered {
            background: rgba(14, 165, 164, 0.08) !important;
        }
        .vakata-context-rtl li>a>i{
            margin: 0 -43px 0 0;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid pt-2">
        <nav aria-label="breadcrumb" class="mt-0 px-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item font-size-12"><a href="#">خانه</a></li>
                <li class="breadcrumb-item font-size-12 active" aria-current="page">دسته بندی ها</li>
            </ol>
        </nav>

        <section class="row">
            <div class="col-12">
                <div class="main-body-container">

                    <section class="d-flex justify-content-between align-items-center mt-4 mb-3 border-bottom pb-2">
                        <div class="d-flex flex-row">
                            <a href="{{route('A_create_category')}}" class="btn btn-info btn-sm">
                                <i class="fa fa-plus"></i> ایجاد دسته جدید
                            </a>
                            <a href="{{route('A_categories')}}" class="btn btn-warning btn-sm mx-3">
                                <i class="fa fa-table"></i> نمایش جدولی گروه ها
                            </a>
                        </div>

                        <div class="max-width-16-rem">

                        </div>
                    </section>

                    <!-- جستجو -->
                    <div class="search-container">
                        <input
                            type="text"
                            id="searchInput"
                            class="search-input"
                            placeholder="🔍 جستجو در دسته‌بندی‌ها..."
                        >

                        <div class="search-buttons">
                            <button class="clear-search-btn" id="clearSearchBtn" title="پاک کردن جستجو">
                                <i class="fa fa-times"></i> پاک کردن
                            </button>
                            <button class="expand-all-btn" id="expandAllBtn" title="گسترش تمام گره‌ها">
                                <i class="fa fa-expand"></i> گسترش همه
                            </button>
                            <button class="collapse-all-btn" id="collapseAllBtn" title="تاشو تمام گره‌ها">
                                <i class="fa fa-compress"></i> تاشو همه
                            </button>
                        </div>

                        <div class="search-results-info" id="searchResultsInfo">
                            <i class="fa fa-info-circle"></i>
                            <span id="resultsCount">0</span> نتیجه یافت شد
                        </div>
                    </div>

                    <!-- درخت دسته‌بندی‌ها -->
                    <div id="jstree"></div>

                </div>
            </div>

        </section>
    </div>

@endsection

@section('script')
    <script src="{{ asset('admin-assets/vendor/jsTree/jstree.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            let treeData = @json($categoryTree);
            let treeInstance = null;

            // ایجاد درخت
            treeInstance = $('#jstree').jstree({
                'core': {
                    'data': treeData,
                    'themes': {
                        'name': 'default',
                        'responsive': true,
                        'rtl': true,
                        'dots': true,
                        'icons': true,
                        'stripes': true,


                    },
                    'check_callback': true
                },
                'plugins': ['wholerow', 'search','sort', 'contextmenu'],
                'search': {
                    'case_insensitive': true,
                    'show_only_matches': true,
                    'show_only_matches_children': true
                },
                'sort': function(a, b) { // مرتب سازی بر اساس والدین در بالا
                    let nodeA = this.get_node(a);
                    let nodeB = this.get_node(b);
                    if (nodeA.children.length > 0 && nodeB.children.length === 0) {
                        return -1;
                    }
                    if (nodeA.children.length === 0 && nodeB.children.length > 0) {
                        return 1;
                    }
                    return nodeA.text.localeCompare(nodeB.text, 'fa');
                },
                'contextmenu': {
                    'items': function(node) {
                        let items = {
                            'edit': {
                                'label': '<i class="fa fa-edit"></i> ویرایش',
                                'action': function(obj) {
                                    let url = "{{ route('A_edit_category', ':id') }}".replace(':id', node.id);
                                    window.location.href = url;

                                },
                            },

                        };

                        return items;
                    }
                },

            }).jstree(true);


            // ────────────────────────────────────────────────────────
            // جستجو در حین تایپ
            // ────────────────────────────────────────────────────────
            $('#searchInput').on('keyup', function() {
                let searchValue = this.value.trim();

                if (searchValue.length === 0) {
                    treeInstance.clear_search();
                    $('#searchResultsInfo').removeClass('show');
                    return;
                }

                treeInstance.search(searchValue);
                let matchedNodes = $('#jstree').find('.jstree-matched').length;
                updateSearchResults(matchedNodes);
            });

            // ────────────────────────────────────────────────────────
            // پاک کردن جستجو
            // ────────────────────────────────────────────────────────
            $('#clearSearchBtn').on('click', function() {
                $('#searchInput').val('');
                treeInstance.clear_search();
                $('#searchResultsInfo').removeClass('show');
            });

            // ────────────────────────────────────────────────────────
            // گسترش تمام گره‌ها
            // ────────────────────────────────────────────────────────
            $('#expandAllBtn').on('click', function() {
                treeInstance.open_all();
            });

            // ────────────────────────────────────────────────────────
            // تاشو تمام گره‌ها
            // ────────────────────────────────────────────────────────
            $('#collapseAllBtn').on('click', function() {
                treeInstance.close_all();
            });


            // ────────────────────────────────────────────────────────
            // تابع نمایش نتایج جستجو
            // ────────────────────────────────────────────────────────
            function updateSearchResults(matchedCount) {
                let resultsInfo = $('#searchResultsInfo');
                let resultsCount = $('#resultsCount');

                if (matchedCount > 0) {
                    resultsCount.text(matchedCount);
                    resultsInfo.addClass('show');
                } else {
                    resultsInfo.removeClass('show');
                }
            }

            // ────────────────────────────────────────────────────────
            // حذف جستجو هنگام خالی کردن input
            // ────────────────────────────────────────────────────────
            $('#searchInput').on('input', function() {
                if (this.value.length === 0) {
                    treeInstance.clear_search();
                    $('#searchResultsInfo').removeClass('show');
                }
            });

            // ────────────────────────────────────────────────────────
            // Enter برای جستجو
            // ────────────────────────────────────────────────────────
            $('#searchInput').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    let searchValue = this.value.trim();

                    if (searchValue.length > 0) {
                        treeInstance.open_all();
                    }
                }
            });
        });
    </script>

@endsection
