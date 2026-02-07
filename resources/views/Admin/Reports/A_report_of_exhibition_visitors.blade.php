@extends('Admin.Layout.master')

@section('head-tag')
    <title>گزارش مشتریان نمایشگاه</title>
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


        #visit_exhibition_table thead th,
        #visit_exhibition_table tbody td,
        #visit_exhibition_table tfoot th {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
            background-color: #F6EFBD;
            min-width: 160px;
        }


        #visit_exhibition_table thead th {
            background-color: #BACD92;
            font-weight: normal;
        }

        #visit_exhibition_table tfoot th {
            background-color: #FF9843;
        }


        #visit_exhibition_table th:nth-child(1),
        #visit_exhibition_table td:nth-child(1) {
            min-width: 70px;
            max-width: 80px;
        }

        #visit_exhibition_table th:last-child,
        #visit_exhibition_table td:last-child {
            min-width: 130px;
            max-width: 130px;
        }


        #visit_exhibition_table th:nth-child(4),
        #visit_exhibition_table td:nth-child(4) {
            min-width: 140px;
        }


        #visit_exhibition_table thead .filters input {
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
            <li class="breadcrumb-item font-size-12 active" aria-current="page">گزارش آمار بازدید کنددگان نمایشگاه</li>
        </ol>
    </nav>

    <div class="row ">
        <div class="col-12">
            <div class="main-body-container mt-3 px-2">
                <section class="main-body-container-header p-2">
                    <h5>گزارش مشتریان نمایشگاه</h5>
                </section>

                <section class="table-responsive">
                    <table id="visit_exhibition_table" class="table table-striped table-hover h-150px info_responsive">
                        <thead>
                        <tr>
                            <th>ردیف</th>
                            <th>سال</th>
                            <th>ماه</th>
                            <th>نمایشگاه</th>
                            <th>بازدید</th>
                            <th>درصد از کل</th>
                            <th></th>
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
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($exhibitionInfos as $exhibitionInfo)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $exhibitionInfo->year }}</td>
                                <td>{{ $exhibitionInfo->month_name }}</td>
                                <td>{{ $exhibitionInfo->exhibition_name }}</td>
                                <td>{{ $exhibitionInfo->visit_count }} نفر</td>
                                <td></td>
                                <td><a href="{{route('A_report_of_exhibition_customers').'?exhibition='.$exhibitionInfo->exhibition_name}}" class="text-decoration-none">جزئیات</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </section>

                <section class="mt-4">
                    <div style="height:300px;">
                        <canvas id="exhibition_chart"></canvas>
                    </div>
                </section>


            </div>
        </div>
    </div>
@endsection

@section('script')
    {{--    @include('Admin.Alerts.Sweetalert.delete-confirm', ['className' => 'delete'])--}}
    <script src="{{asset('admin-assets/datatable/export/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('admin-assets/datatable/export/jszip.min.js')}}"></script>
    <script src="{{asset('admin-assets/datatable/export/buttons.html5.min.js')}}"></script>
    <script src="{{asset('admin-assets/chartjs/chart.js')}}"></script>
    <script>
        $(document).ready(function() {

            let chartInstance = null;

            function updateExhibitionChart(table) {
                let api = table.api();

                let exhibitionData = {};
                let totalVisits = 0;

                // جمع‌آوری داده از ردیف‌های فیلترشده
                api.rows({ filter: 'applied' }).every(function() {
                    let $row = $(this.node());

                    let exhibitionName = $row.find('td').eq(3).text().trim();
                    let visitText = $row.find('td').eq(4).text();
                    let visitCount = parseInt(visitText.replace(/[^\d]/g, '')) || 0;

                    if (!exhibitionData[exhibitionName]) {
                        exhibitionData[exhibitionName] = 0;
                    }

                    exhibitionData[exhibitionName] += visitCount;
                    totalVisits += visitCount;
                });

                let labels = [];
                let values = [];

                Object.keys(exhibitionData).forEach(function(name) {
                    labels.push(name);
                    let percent = totalVisits > 0
                        ? (exhibitionData[name] / totalVisits * 100).toFixed(1)
                        : 0;
                    values.push(percent);
                });

                // اگر نمودار قبلی وجود دارد، نابودش کن
                if (chartInstance) {
                    chartInstance.destroy();
                }

                let ctx = document.getElementById('exhibition_chart').getContext('2d');

                chartInstance = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values,
                            backgroundColor: [
                                '#4e79a7',
                                '#f28e2b',
                                '#e15759',
                                '#76b7b2',
                                '#59a14f',
                                '#edc949',
                                '#af7aa1',
                                '#ff9da7'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,  // این خط را اضافه کنید
                        plugins: {
                            legend: {
                                position: 'bottom'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.label + ' : ' + context.parsed + '٪';
                                    }
                                }
                            }
                        }
                    }
                });
            }


            var table = $('#visit_exhibition_table').DataTable({
                language: {
                    url: "{{ asset('admin-assets/datatable/farsi.json') }}"
                },
                pageLength: 100,
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: 'خروجی اکسل',
                        className: 'btn btn-success'
                    }
                ],
                autoWidth: false,
                columnDefs: [
                    { width: "50px", targets: 0 },
                    { width: "60px", targets: 1 },
                    { width: "auto", targets: 2 },
                    { width: "auto", targets: 3 },
                    { width: "auto", targets: 4 },
                    { width: "auto", targets: 5 },
                    { width: "auto", targets: 6 },
                ],

                drawCallback: function() {
                    var api = this.api();
                    updateExhibitionChart(this);
                    // جمع کل بازدیدِ ردیف‌های فیلترشده
                    var totalVisits = 0;

                    api.rows({ filter: 'applied' }).every(function() {
                        var visitText = $(this.node()).find('td').eq(4).text();
                        var visitCount = parseInt(visitText.replace(/[^\d]/g, '')) || 0;
                        totalVisits += visitCount;
                    });

                    // محاسبه درصد هر ردیف
                    api.rows({ filter: 'applied' }).every(function() {
                        var $row = $(this.node());
                        var visitText = $row.find('td').eq(4).text();
                        var visitCount = parseInt(visitText.replace(/[^\d]/g, '')) || 0;

                        var percent = totalVisits > 0
                            ? ((visitCount / totalVisits) * 100).toFixed(1)
                            : 0;

                        $row.find('td').eq(5).html(percent + '٪');
                    });


                }

            });

            // سرچ ستونی
            $('.column-search').on('keyup change', function() {
                let columnIndex = $(this).data('column');
                let value = $(this).val();
                table.column(columnIndex).search(value).draw();
            });

        });
    </script>

@endsection
