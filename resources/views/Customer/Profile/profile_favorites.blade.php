@extends('Customer.Layout.master')

@section('content')

    <!-- main -->
    <main class="profile-user-page default">
        <div class="container">
            <div class="row">
                <div class="profile-page col-xl-9 col-lg-8 col-md-12 order-2">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-12">
                                <h1 class="title-tab-content">لیست علاقه مندی ها</h1>
                            </div>
                            <div class="content-section default">
                                <div class="row">
                                    @forelse($productsFavorites as $product)
                                        <div class="col-md-6 col-sm-12">
                                            <div class="profile-recent-fav-row">

                                                <a href="#" class="profile-recent-fav-col profile-recent-fav-col-thumb">
                                                    <img
                                                        src="{{ url('get_image_by_id/' . $product->images->first()->id) }}">
                                                </a>


                                                <div class="profile-recent-fav-col profile-recent-fav-col-title">
                                                    <a href="#">
                                                        <h4 class="profile-recent-fav-name">
                                                            {{ $product->product_name }}
                                                        </h4>
                                                    </a>
                                                    <div class="profile-recent-fav-price">
                                                        {{ number_format($product->price) }} تومان
                                                    </div>
                                                </div>


                                                <div class="profile-recent-fav-col profile-recent-fav-col-actions">
                                                    <button type="button"
                                                            class="btn-action btn-action-remove product-remove-from-favorite "
                                                            data-url="{{ route('remove_from_favorites', $product->id) }}">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>


                                                <div class="col-12 text-left mb-3">
                                                    <a class="btn  custom-primary"
                                                       href="{{ route('show_product_by_id', $product->id) }}">
                                                        مشاهده محصول
                                                    </a>
                                                </div>

                                            </div>

                                        </div>
                                    @empty
                                        <div class="col-12 text-center py-5">
                                            <p class="text-muted">هنوز هیچ آیتمی در لیست علاقه‌مندی‌های شما وجود
                                                ندارد.</p>

                                        </div>
                                    @endforelse

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <x-profile-sidebar :user="$user"/>

            </div>
        </div>
    </main>
    <!-- main -->

@endsection
@section('script')

    <script>
        $(document).on('click', '.product-remove-from-favorite', function () {
            var url = $(this).attr('data-url');
            var row = $(this).closest('.profile-recent-fav-row');

            Swal.fire({
                text: 'آیا از حذف علاقه مندی مطمئن هستید؟',
                showCancelButton: true,
                confirmButtonText: 'بله',
                cancelButtonText: 'خیر',
                reverseButtons: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#777',
            }).then((result) => {
                console.log(result.value);
                if (result.value) {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {_token: '{{ csrf_token() }}'},
                        success: function (result) {
                            if (result.status === 'success') {

                                row.fadeOut(300, function () {
                                    $(this).remove();
                                });

                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: 'از علاقه‌مندی‌ها حذف شد',
                                    showConfirmButton: false,
                                    timer: 2500
                                });
                                location.reload();
                            }
                        }
                    });
                }
            });
        });
    </script>

@endsection
