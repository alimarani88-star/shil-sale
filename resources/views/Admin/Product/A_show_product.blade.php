@extends('Admin.Layout.master')

@section('head-tag')
    <title>کالاها</title>
@endsection

@section('content')
    <div class="container-fluid pt-2">
        <nav aria-label="breadcrumb" class="mt-0 px-3">
            <ol class="breadcrumb p-2">
                <li class="breadcrumb-item font-size-12"><a href="#">خانه</a></li>
                <li class="breadcrumb-item font-size-12 active" aria-current="page"> کالاها</li>
            </ol>
        </nav>

        <section class="row">
            <div class="col-12">
                <section class="main-body-container">
                    <section class="main-body-container-header">
                        <h5>
                            کالاها
                        </h5>
                    </section>

                    <section class="d-flex justify-content-between align-items-center mt-4 mb-3 border-bottom pb-2">
                        <a href="{{route('A_create_product')}}" class="btn btn-info btn-sm">ایجاد کالای جدید </a>
                    </section>

                    <section class="table-responsive">
                        <table id="producttable" class="table table-striped table-hover h-150px">
                            <thead>
                            <tr>
                                <th>ردیف</th>
                                <th>نام کالا</th>
                                <th> تصویر کالا</th>
                                <th>قیمت</th>
                                <th>قابلیت فروش</th>
                                <th>وضعیت</th>
                                <th class="width-8-rem text-center"><i class="fa fa-cogs"></i>تنظیمات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($products as $product)

                                <tr>
                                    <th>{{ $loop->iteration }}</th>
                                    <td>{{ $product->product_name }}</td>
                                    <td>

                                        @if($product->images->isNotEmpty())
                                            <img src="/get_image_by_id/{{$product->images->first()->id}}" width="100"
                                                 height="120"
                                                 class="img-rounded img_big img_border"
                                                 alt="Product Image">

                                        @endif
                                    </td>

                                    <td>{{ number_format($product->price) }} {{$product->price_unit}}</td>

                                    <td>
                                        <span
                                            class="checkbox-span {{ $product->marketable ? 'active' : 'inactive' }}"></span>
                                    </td>
                                    <td>
                                        <span
                                            class="checkbox-span {{ $product->status ? 'active' : 'inactive' }}"></span>
                                    </td>


                                    <td class="width-8-rem text-center">
                                        <a href="{{ route('A_edit_product', $product->id) }}"
                                           class="btn btn-primary btn-sm" title="ویرایش">
                                            <i class="fa fa-edit"></i> ویرایش
                                        </a>

                                        <form action="{{ route('A_inactive_product', $product->id) }}" method="POST"
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
                title: 'آیا از حذف کالا مطمئن هستید؟',
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
