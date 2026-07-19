@extends('Admin.Layout.master')

@section('head-tag')
    <title>گزارش مشتریان نمایشگاه</title>
    <style>
        /* پایه جدول */
        .custom-table {
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.85rem;
        }

        .custom-table th {
            background-color: #f1f3f5;
            font-weight: 700;
            color: #343a40;
            border-bottom: 2px solid #dee2e6;
            text-wrap: nowrap;
        }

        .custom-table td {
            vertical-align: middle;
            border-color: #e9ecef;
            text-wrap: nowrap;
        }

        /* ====== تفکیک استان‌ها (یکی در میان) ====== */

        /* استان‌های فرد */
        .province-group-1,
        .province-group-3,
        .province-group-5 {
            background-color: #f8fafc !important;
        }

        /* استان‌های زوج */
        .province-group-2,
        .province-group-4 {
            background-color: #fffdf8 !important;
        }

        /* خط جداکننده بالای هر استان */
        .first-row-in-group td {
            border-top: 3px solid #adb5bd !important;
        }

        /* خط جداکننده پایین هر استان */
        .last-row-in-group td {
            border-bottom: 3px solid #adb5bd !important;
        }

        /* ====== سل استان (ستون اول) ====== */
        .province-cell {
            background: linear-gradient(180deg, #495057, #343a40) !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            letter-spacing: 0.3px;
            border-left: 6px solid #212529 !important;
            box-shadow: inset -4px 0 8px rgba(255, 255, 255, 0.08);
        }

        /* ====== نام نمایشگاه ====== */
        .exhibition-name {
            font-weight: 600;
            color: #212529;
        }

        /* ====== تعداد بازدید ====== */
        .visit-count {
            background-color: #edf7ed;
            color: #1b5e20;
            font-weight: 700;
            border-radius: 6px;
        }

        /* ====== درصد ====== */
        .percentage-cell {
            background-color: #fff8e1;
            color: #e65100;
            font-weight: 700;
            border-radius: 6px;
        }

        /* نوار درصد ملایم‌تر */
        .percentage-cell > div > div {
            background: linear-gradient(90deg, #ffb703, #fb8500) !important;
        }

        /* ====== Hover هوشمند (کل استان) ====== */
        .custom-table tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.04) !important;
        }

        /* ====== انیمیشن بسیار ظریف ====== */
        .custom-table tbody tr {
            animation: fadeIn 0.2s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(4px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

    </style>

@endsection

@section('content')

    <nav aria-label="breadcrumb" class="mt-0 px-3">
        <ol class="breadcrumb p-2">
            <li class="breadcrumb-item font-size-12"><a href="#">خانه</a></li>
            <li class="breadcrumb-item font-size-12 active" aria-current="page">گزارشات</li>
            <li class="breadcrumb-item font-size-12 active" aria-current="page">گزارش آمار بازدید کنندگان نمایشگاه بر
                اساس شهر
            </li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-12">
            <div class="main-body-container mt-3 px-2">
                <section class="report-header">
                    <h5 class="mb-0">
                        📊 گزارش مشتریان نمایشگاه بر اساس شهر
                    </h5>
                </section>

                <!-- باکس جستجو -->
                <section class="search-box-container py-3">
                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="search-label">📅 انتخاب سال</label>
                            <select id="searchYear" class="form-control">
                                <option value="0">همه</option>
                                @foreach($years as $y)
                                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="search-label">🔍 جستجوی استان</label>
                            <input type="text" id="searchProvince" class="form-control search-input"
                                   placeholder="نام استان را وارد کنید...">
                        </div>
                    </div>
                </section>

                <section class="table-responsive">
                    <table class="table table-bordered text-center align-middle mb-0 custom-table" id="mainTable">
                        <thead class="table-light">
                        <tr>
                            <th style="width: 20%">استان بازدیدکننده</th>
                            <th style="width: 35%">نام نمایشگاه</th>
                            <th style="width: 15%">تعداد بازدیدکننده</th>
                            <th style="width: 30%">درصد نسبت به کل بازدیدکننده‌های نمایشگاه</th>
                        </tr>
                        </thead>
                        <tbody>

                        @forelse($exhibition_by_province as $province => $exhibitions)

                            @php
                                $rowspan = count($exhibitions);
                                $i = 0;
                                $groupColor = (($loop->index % 5) + 1);
                            @endphp

                            @foreach($exhibitions as $exhibition_name => $data)
                                <tr class="province-group-{{ $groupColor }} same-province-{{ $loop->parent->index }}
                                    {{ $i === 0 ? 'first-row-in-group' : '' }}
                                    {{ $i === ($rowspan - 1) ? 'last-row-in-group' : '' }}"
                                    data-province="{{ !empty($province) ? $province : 'ثبت نشده' }}"
                                    data-exhibition="{{ $exhibition_name }}"
                                    data-visit="{{ $data['visit'] }}">

                                    @if($i === 0)
                                        <td rowspan="{{ $rowspan }}" class="province-cell">
                                            <div
                                                class="d-flex flex-column align-items-center justify-content-center h-100">
                                                <span
                                                    style="font-size: 1.2rem;">{{ !empty($province) ? $province : 'ثبت نشده' }}</span>
                                                <span class="exhibition-count-badge mt-2">
                                                    {{ $rowspan }} نمایشگاه
                                                </span>
                                            </div>
                                        </td>
                                    @endif

                                    <td class="exhibition-name">
                                        {{ $exhibition_name }}
                                    </td>

                                    <td>
                                        <span class="visit-count">
                                            {{ number_format($data['visit']) }} نفر
                                        </span>
                                    </td>

                                    <td class="percentage-cell">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <div class="percentage-bar-container">
                                                <div class="percentage-bar"
                                                     style="width: {{ $data['percent'] }}%;"></div>
                                            </div>
                                            <span class="fw-bold">{{ $data['percent'] }}%</span>
                                        </div>
                                    </td>
                                </tr>
                                @php $i++; @endphp
                            @endforeach

                        @empty
                            <tr>
                                <td colspan="4" class="empty-state">
                                    <i class="fa fa-inbox fa-4x mb-3 d-block"></i>
                                    <h6 class="text-muted">داده‌ای برای نمایش وجود ندارد</h6>
                                </td>
                            </tr>
                        @endforelse

                        </tbody>
                    </table>
                </section>

            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{asset('admin-assets/chartjs/chart.js')}}"></script>
    <script>
        $(document).ready(function() {
            let $rows = $('#mainTable tbody tr');
            let $searchInput = $('#searchProvince');

            let $resultCount = $('#resultCount');

            function filterTable() {
                let filterText = $searchInput.val().trim().toLowerCase();
                let visibleCount = 0;

                $rows.each(function() {
                    let province = $(this).data('province') ? $(this).data('province').toLowerCase() : '';

                    if (province.includes(filterText)) {
                        $(this).show();
                        visibleCount++;
                    } else {
                        $(this).hide();
                    }
                });

                if (filterText) {
                    $resultCount.text('تعداد نتایج: ' + visibleCount);
                } else {
                    $resultCount.text('');
                }
            }

            $searchInput.on('input', filterTable);


            $('#searchYear').on('change', function() {
                let selectedYear = $(this).val();
                window.location.href = "{{ url()->current() }}" + "?year=" + selectedYear;
            });

        });
    </script>


@endsection
