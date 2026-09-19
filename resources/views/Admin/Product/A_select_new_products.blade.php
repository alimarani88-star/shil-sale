@extends('Admin.Layout.master')

@section('head-tag')
    <title>انتخاب محصول جدید ترین ها</title>
@endsection

@section('content')
    <div class="container-fluid pt-2">
        <nav aria-label="breadcrumb" class="mt-0 px-3">
            <ol class="breadcrumb p-2">
                <li class="breadcrumb-item font-size-12"><a href="{{ route('A_home') }}">خانه</a></li>
                <li class="breadcrumb-item font-size-12"><a href="{{ route('A_show_product') }}">کالاها</a></li>
                <li class="breadcrumb-item font-size-12 active" aria-current="page">انتخاب محصول جدید ترین ها</li>
            </ol>
        </nav>

        <section class="row">
            <div class="col-12">
                <section class="main-body-container">
                    <section class="main-body-container-header">
                        <h5>انتخاب محصول جدید ترین ها</h5>
                    </section>

                    <section class="d-flex justify-content-between align-items-center mt-4 mb-3 border-bottom pb-2">
                        <p class="mb-0 text-muted">محصولات انتخاب‌شده در بخش «جدید ترین ها» صفحه اصلی نمایش داده می‌شوند.</p>
                        <a href="{{ route('A_show_product') }}" class="btn btn-info btn-sm">بازگشت به کالاها</a>
                    </section>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="select-new-products-form" action="{{ route('A_s_select_new_products') }}" method="POST">
                        @csrf

                        <section class="table-responsive">
                            <table id="newproducttable" class="table table-striped table-hover h-150px">
                                <thead>
                                <tr>
                                    <th class="text-center" style="width: 4rem;">انتخاب</th>
                                    <th>ردیف</th>
                                    <th>نام کالا</th>
                                    <th>تصویر کالا</th>
                                    <th>قیمت</th>
                                    <th>وضعیت</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse ($products as $product)
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox"
                                                   class="new-product-checkbox"
                                                   name="product_ids[]"
                                                   value="{{ $product->id }}"
                                                   {{ (int) $product->new_product === 1 ? 'checked' : '' }}>
                                        </td>
                                        <th>{{ $loop->iteration }}</th>
                                        <td>{{ $product->product_name }}</td>
                                        <td>
                                            @if($product->images->isNotEmpty())
                                                <img src="/get_image_by_id/{{ $product->images->first()->id }}"
                                                     width="100"
                                                     height="120"
                                                     class="img-rounded img_big img_border"
                                                     alt="{{ $product->product_name }}">
                                            @endif
                                        </td>
                                        <td>{{ number_format($product->price) }} {{ $product->price_unit }}</td>
                                        <td>
                                            <span class="checkbox-span {{ $product->status ? 'active' : 'inactive' }}"></span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">محصولی یافت نشد.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </section>

                        <div class="mt-3 mb-4">
                            <button type="submit" class="btn btn-success">ذخیره محصولات جدیدترین‌ها</button>
                        </div>
                    </form>
                </section>
            </div>
        </section>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            // مرتب‌سازی ستون چک‌باکس بر اساس checked/unchecked
            $.fn.dataTable.ext.order['dom-checkbox'] = function (settings, col) {
                return this.api().column(col, {order: 'index'}).nodes().map(function (td) {
                    return $('input.new-product-checkbox', td).prop('checked') ? 1 : 0;
                });
            };

            var table = $('#newproducttable').DataTable({
                "language": {
                    "url": "{{ asset('admin-assets/datatable/farsi.json') }}"
                },
                "order": [],
                "columnDefs": [
                    {
                        "targets": 0,
                        "orderDataType": "dom-checkbox",
                        "type": "numeric"
                    }
                ]
            });

            // DataTables فقط ردیف‌های صفحه جاری را در DOM نگه می‌دارد؛
            // قبل از ارسال، تیک‌های همه صفحات را جمع می‌کنیم.
            $('#select-new-products-form').on('submit', function () {
                var $form = $(this);

                $form.find('input.dt-selected-product').remove();
                table.$('input.new-product-checkbox').prop('disabled', true);

                table.$('input.new-product-checkbox:checked').each(function () {
                    $form.append(
                        $('<input>', {
                            type: 'hidden',
                            name: 'product_ids[]',
                            value: $(this).val(),
                            class: 'dt-selected-product'
                        })
                    );
                });
            });
        });
    </script>
@endsection
