@extends('Admin.Layout.master')
@section('head-tag')
    <title>{{ $pageTitle ?? 'گزارش مشتریان نمایشگاه' }}</title>
    <style>
        input {
            text-align: center;
        }


        .info_responsive {
            overflow-x: auto;
            white-space: nowrap;
            -ms-overflow-style: auto;
            scrollbar-width: thin;
            border-radius: 5px;
            padding: 0;
            background-color: #f9f9f9;
        }


        #producttable thead th,
        #producttable tbody td,
        #producttable tfoot th {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
            background-color: #F6EFBD;
            min-width: 160px;
        }


        #producttable thead th {
            background-color: #BACD92;
            font-weight: normal;
        }

        #producttable tfoot th {
            background-color: #FF9843;
        }


        #producttable th:nth-child(1),
        #producttable td:nth-child(1) {
            min-width: 70px;
            max-width: 80px;
        }

        #producttable th:last-child,
        #producttable td:last-child {
            min-width: 130px;
            max-width: 130px;
        }


        #producttable th:nth-child(4),
        #producttable td:nth-child(4) {
            min-width: 140px;
        }


        #producttable thead .filters input {
            min-width: 120px;
            text-align: center;
            font-size: 12px;
            padding: 3px 6px;
        }


        @media (max-width: 768px) {
            .info_responsive {
                border: 1px solid #ddd;
                border-radius: 8px;
                background: #fff;
                padding: 4px;
            }
        }


        .checkbox-span {
            display: inline-block;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 2px solid #ccc;
        }

        .checkbox-span.active {
            background-color: #28a745;
            border-color: #28a745;
        }

        .checkbox-span.inactive {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .dt-buttons {
            float: left;
            margin-bottom: 10px;
        }

        .dt-search {
            margin-bottom: 10px;
        }
    </style>

@endsection

@section('content')

    <nav aria-label="breadcrumb" class="mt-0 px-3">
        <ol class="breadcrumb p-2">
            <li class="breadcrumb-item font-size-12"><a href="#">خانه</a></li>
            <li class="breadcrumb-item font-size-12 active" aria-current="page">گزارشات</li>
            <li class="breadcrumb-item font-size-12 active" aria-current="page">گزارش مشتریان نمایشگاه</li>
        </ol>
    </nav>

    <section class="row">
        <div class="col-12">
            <section class="main-body-container p-2">
                <section class="main-body-container-header p-2">
                    <div class="d-flex flex-row align-items-baseline">
                        <h5>گزارش مشتریان نمایشگاه</h5>
                        :
                        @if($exhibition)
                            <h4 class="p-1" style="background-color: #02ffff">{{$exhibition}}</h4>
                        @endif
                        @if($city)
                            <h4 class="p-1" style="background-color: #02ffff">بر اساس استان {{$city}}</h4>
                        @endif
                    </div>
                </section>

                <section class="table-responsive">
                    <table id="producttable" class="table table-striped table-hover h-150px info_responsive">
                        <thead>
                        <tr>
                            <th>ردیف</th>
                            <th>نام</th>
                            <th>نام خانوادگی</th>
                            @if(!$exhibition)
                                <th>نمایشگاه</th>
                            @endif
                            <th>موبایل</th>
                            <th>شهر</th>
                            <th>نام شرکت</th>
                            <th>اپراتور</th>
                            <th>درخواست نمایندگی</th>
                        </tr>
                        <tr class="filters">
                            <th></th>
                            <th><input type="text" class="form-control form-control-sm column-search"
                                       placeholder="جستجو..." data-column="1"></th>
                            <th><input type="text" class="form-control form-control-sm column-search"
                                       placeholder="جستجو..." data-column="2"></th>
                            <th><input type="text" class="form-control form-control-sm column-search"
                                       placeholder="جستجو..." data-column="3"></th>
                            <th><input type="text" class="form-control form-control-sm column-search"
                                       placeholder="جستجو..." data-column="4"></th>
                            <th><input type="text" class="form-control form-control-sm column-search"
                                       placeholder="جستجو..." data-column="5"></th>
                            <th><input type="text" class="form-control form-control-sm column-search"
                                       placeholder="جستجو..." data-column="6"></th>
                            @if(!$exhibition)
                                <th><input type="text" class="form-control form-control-sm column-search"
                                           placeholder="جستجو..." data-column="7"></th>
                            @endif
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($customersInfo as $customerInfo)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $customerInfo->first_name }}</td>
                                <td>{{ $customerInfo->last_name }}</td>
                                @if(!$exhibition)
                                    <td>{{ $customerInfo->exhibition_name }}</td>
                                @endif
                                <td>{{ $customerInfo->mobile }}</td>
                                <td>{{ $customerInfo->city_name }}</td>
                                <td>{{ $customerInfo->company_name }}</td>
                                <td>{{ $customerInfo->registrant_name }}</td>
                                <td>
                                    {{ $customerInfo->request_agency ? 'دارد' : 'ندارد' }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </section>
            </section>
        </div>
    </section>
@endsection

@section('script')
    @include('Admin.Alerts.Sweetalert.delete-confirm', ['className' => 'delete'])
    <script src="{{asset('admin-assets/datatable/export/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('admin-assets/datatable/export/jszip.min.js')}}"></script>
    <script src="{{asset('admin-assets/datatable/export/buttons.html5.min.js')}}"></script>
    <script>
        $(document).ready(function() {
            var table = $('#producttable').DataTable({
                language: {
                    url: "{{ asset('admin-assets/datatable/farsi.json') }}"
                },
                pageLength: 100,
                lengthMenu: [10, 25, 50, 100, 1000],
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: 'خروجی اکسل',
                        className: 'btn btn-success'
                    }
                ]
            });

            // سرچ در هر ستون
            $('.column-search').on('keyup change', function() {
                let columnIndex = $(this).data('column');
                let value = $(this).val();
                table.column(columnIndex).search(value).draw();
            });
        });
    </script>
@endsection
