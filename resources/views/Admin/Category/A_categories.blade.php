@extends('Admin.Layout.master')

@section('head-tag')
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/select2/select2.min.css') }}">
    <title>دسته بندی های عمومی</title>

    <style>
        #category-table tbody tr td{
            align-content: center;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid pt-2">
        <nav aria-label="breadcrumb" class="mt-0 px-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item font-size-12"><a href="#">خانه</a></li>
                <li class="breadcrumb-item font-size-12 active" aria-current="page"> دسته بندی ها</li>
            </ol>
        </nav>


        <section class="row">
            <div class="col-12">
                <section class="main-body-container">

                    <section class="d-flex justify-content-between align-items-center mt-4 mb-3 border-bottom pb-2">
                        <div class="d-flex flex-row">
                            <a href="{{route('A_create_category')}}" class="btn btn-info btn-sm">
                                <i class="fa fa-plus"></i> ایجاد دسته جدید
                            </a>
                            <a href="{{route('A_show_category_tree')}}" class="btn btn-warning mx-3 btn-sm">
                                <i class="fa fa-sitemap"></i> نمایش درختی گروه ها
                            </a>

                        </div>
                    </section>

                    <section class="table-responsive">
                        <table id="category-table" class="table table-striped table-hover h-150px">
                            <thead>
                            <tr class="">
                                <th class="text-center">#</th>
                                <th class="text-start">عنوان</th>
                                <th>اسلاگ</th>
                                <th>والد</th>
                                <th>نوع</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($categories as $category)
                                <tr>
                                    <td class="text-center" style="width: 60px">{{$loop->iteration }}</td>
                                    <td>{{$category->name}}</td>
                                    <td>{{$category->slug}}</td>
                                    <td>{{$category->parent}}</td>
                                    <td>{{$category->type == 'post' ? 'دسته بندی عمومی' : 'محصولات'}}</td>
                                    <td>
                                        <a class="btn bg-transparent text-info" href="{{route('A_edit_category' , ['id'=>$category->id])}}"><i class="fa fa-pencil"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </section>

                </section>
            </div>
        </section>
    </div>

@endsection


@section('script')
    <script>
        $(document).ready(function () {
            $('#category-table').DataTable({
                "language": {
                    "url": "{{ asset('admin-assets/datatable/farsi.json') }}"
                }
            });
        });
    </script>

@endsection
@section('script')
    <script src="{{ asset('admin-assets/vendor/DataTables/datatables.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            $('.toggle-sidebar-left').on('click', function () {})

        })
    </script>


@endsection
