@extends('Admin.Layout.master')

@section('head-tag')
    <title>فروش شگفت انگیز</title>
@endsection

@section('content')
    <div class="container-fluid pt-2">
        <nav aria-label="breadcrumb" class="mt-0 px-3">
            <ol class="breadcrumb p-2">
                <li class="breadcrumb-item font-size-12"><a href="{{route('A_home')}}">خانه</a></li>
                <li class="breadcrumb-item font-size-12 active" aria-current="page">تخفیف ها</li>
                <li class="breadcrumb-item font-size-12 active" aria-current="page">فروش شگفت انگیز</li>
            </ol>
        </nav>


        <section class="row">
            <div class="col-12">
                <section class="main-body-container">
                    <section class="main-body-container-header">
                        <h5>
                            فروش شگفت انگیز
                        </h5>
                    </section>

                    <section class="d-flex justify-content-between align-items-center mt-4 mb-3 border-bottom pb-2">
                        <a href="{{route('A_create_amazingsale')}}" class="btn btn-info btn-sm">افزودن کالا به لیست فروش
                            شگفت انگیز </a>
                    </section>

                    <section class="table-responsive">
                        <table id="producttable" class="table table-striped table-hover h-150px">
                            <thead>
                            <tr>
                                <th>ردیف</th>
                                <th>عنوان تخفیف</th>
                                <th>نام کالا</th>
                                <th>درصد تخفیف</th>
                                <th>نوع تخفیف</th>
                                <th>تاریخ شروع</th>
                                <th>تاریخ پایان</th>
                                <th>وضعیت</th>
                                <th class="width-8-rem text-center"><i class="fa fa-cogs"></i> تنظیمات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($discounts as $discount)
                                @foreach($discount->products as $product)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <th>
                                            <a href="{{ route('A_edit_discount', $discount->id) }}">
                                               {{ $discount->discount_name }}
                                            </a>
                                        </th>
                                        <td>{{ $product->pivot->target_name}}</td>

                                        <td>{{ $product->pivot->percentage }} %</td>
                                        <td>{{ $discount->discount_type == 'amazingsale' ? 'فروش شگفت انگیز' : '' }}</td>
                                        <td>{{ $discount->start_date }} </td>
                                        <td>{{ $discount->end_date }} </td>
                                        <td>{{ $discount->status == 1 ? 'فعال' : 'غیر فعال'}} </td>


                                        <td class="width-8-rem text-center">
                                            <a href="{{ route('A_edit_amazingsale',['id' => $product->pivot->id]) }}"
                                               class="btn btn-primary btn-sm" title="ویرایش">
                                                <i class="fa fa-edit"></i> ویرایش
                                            </a>

                                            <form
                                                action="{{ route('A_inactive_amazingsale',  ['id' => $product->pivot->id]) }}"
                                                method="POST"
                                                class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="حذف">
                                                    <i class="fa fa-trash"></i> حذف
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                @endforeach
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

    @include('Admin.Alerts.Sweetalert.delete-confirm', ['className' => 'delete'])
    <script>
        document.addEventListener('submit', function (e) {
            const form = e.target.closest('.delete-form');
            if (!form) return;

            e.preventDefault();

            Swal.fire({
                title: 'آیا از حذف تخفیف مطمئن هستید؟',
                text: "این عملیات غیرقابل بازگشت است!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'بله، حذف شود',
                cancelButtonText: 'انصراف'
            }).then((result) => {
                if (result.value) {
                    form.submit();
                }
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $('#producttable').DataTable({
                "language": {
                    "url": "{{ asset('admin-assets/datatable/farsi.json') }}"
                }
            });
        });
    </script>

@endsection
