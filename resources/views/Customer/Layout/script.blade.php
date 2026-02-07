<!--   Core JS Files   -->
<script src="{{asset('assets/js/core/jquery.3.2.1.min.js')}}" type="text/javascript"></script>

<script src="{{asset('assets/js/core/popper.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/js/core/bootstrap.min.js')}}" type="text/javascript"></script>
<!--  Plugin for Switches, full documentation here: http://www.jque.re/plugins/version3/bootstrap.switch/ -->
<script src="{{asset('assets/js/plugins/bootstrap-switch.js')}}"></script>
<!--  Plugin for the Sliders, full documentation here: http://refreshless.com/nouislider/ -->
<script src="{{asset('assets/js/plugins/nouislider.min.js')}}" type="text/javascript"></script>
<!--  Plugin for the DatePicker, full documentation here: https://github.com/uxsolutions/bootstrap-datepicker -->
<script src="{{asset('assets/js/plugins/bootstrap-datepicker.js')}}" type="text/javascript"></script>

<!-- Share Library etc -->
<script src="{{asset('assets/js/plugins/jquery.sharrre.js')}}" type="text/javascript"></script>
<!-- Control Center for Now Ui Kit: parallax effects, scripts for the example Pages etc -->
<script src="{{asset('assets/js/now-ui-kit.js')}}" type="text/javascript"></script>
<!--  CountDown -->
<script src="{{asset('assets/js/plugins/countdown.min.js')}}" type="text/javascript"></script>
<!--  Plugin for Sliders -->
<script src="{{asset('assets/js/plugins/owl.carousel.min.js')}}" type="text/javascript"></script>
<!--  Jquery easing -->
<script src="{{asset('assets/js/plugins/jquery.easing.1.3.min.js')}}" type="text/javascript"></script>
<!--  LocalSearch -->
<script src="{{asset('assets/js/plugins/JsLocalSearch.js')}}" type="text/javascript"></script>
<!--  Plugin ez-plus -->
<script src="{{asset('assets/js/plugins/jquery.ez-plus.js')}}" type="text/javascript"></script>
<!--  Bootstrap-slider -->
<script src="{{asset('assets/js/plugins/bootstrap-slider.min.js')}}"></script>
<!--  AddTags -->
<script src="{{asset('assets/js/plugins/Obj.js')}}"></script>
<script src="{{asset('assets/js/plugins/AddTags.js')}}"></script>

<script src="{{asset('admin-assets/sweetalert/sweetalert2.min.js')}}"></script>

<!-- Main Js -->
<script src="{{asset('assets/js/main.js')}}" type="text/javascript"></script>

<script src="{{ asset('customer-assets/select2/js/select2.min.js') }}"></script>
@yield('scripts')
@stack('scripts')
@if(auth()->check())
    <script>


        function loadCartHeader() {
            $.ajax({
                type: "GET",
                url: '{{ route("ajax_cart_header") }}',
                success: function (response) {
                    $('.count-cart').text(response.count);

                    let formatted = response.totalPrice.toLocaleString('en-US');
                    $('.total-price').text(formatted);

                    const basketList = $('.basket-list');
                    basketList.empty();

                    if (response.cart.length === 0) {
                        basketList.append('<li class="text-center py-2 text-muted">سبد خرید خالی است</li>');
                        return;
                    }

                    response.cart.forEach(item => {
                        let image = '/assets/img/default.jpg';
                        if (item.product.images.length > 0) {
                            const imageId = item.product.images[0].id;
                            image = `/get_image_by_id/${imageId}`;
                        }

                        const html = `
                                        <li>
                                            <a href="#" class="basket-item">
                                                <button class="basket-item-remove" data-id="${item.product_id}"></button>
                                                <div class="basket-item-content">
                                                    <div class="basket-item-image">
                                                        <img src="${image}" alt="">
                                                    </div>
                                                    <div class="basket-item-details">
                                                        <div class="basket-item-title">${item.product.product_name}</div>
                                                        <div class="basket-item-params">
                                                            <div class="basket-item-props">
                                                                <span style="margin-left: 1rem;">${item.count} عدد</span>
                                                                <span style="color:red">${item.discount_name}</span>
                                                                <span style="color:red">${item.percentage}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>`;
                        basketList.append(html);
                    });
                },
                error: function () {
                    console.log('Error loading cart header');
                }
            });
        }

        $(document).ready(function () {
            loadCartHeader();
        });

        function updateCartHeader() {
            loadCartHeader();
        }

        $(document).on('click', '.basket-item-remove', function (e) {
            e.preventDefault();
            const productId = $(this).data('id');
            const li = $(this).closest('li');

            $.ajax({
                url: '{{ route("remove_from_cart") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: productId
                },
                success: function (result) {
                    if (result.status === 'success') {
                        li.remove();
                        $('.total_price').text(result.data.totalPrice.toLocaleString('en-US'));
                        $('.AmountPayable').text(result.data.AmountPayable.toLocaleString('en-US'));
                        $('.total_price_profit').text(result.data.totalProfit?.toLocaleString('en-US') || 0);
                        updateCartHeader();
                    }
                },
                error: function (xhr) {
                    console.error('خطا در حذف محصول', xhr);
                }
            });
        });


    </script>
@endif
