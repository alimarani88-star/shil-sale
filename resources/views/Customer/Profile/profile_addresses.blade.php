@extends('Customer.Layout.master')

@section('content')

    <!-- main -->
    <main class="profile-user-page default">
        <div class="container">
            <div class="row">
                <div class="profile-page col-xl-9 col-lg-8 col-md-12 order-2">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h1 class="title-tab-content">آدرس ها</h1>
                                <button type="button" class="btn  custom-primary" data-toggle="modal"
                                        data-target="#addAddressModal">
                                    افزودن آدرس جدید
                                </button>
                            </div>

                            <div class="row">
                                @forelse($addresses as $address)
                                    <div class="col-md-12 col-sm-12">
                                        <div class="profile-recent-fav-row">
                                            <div
                                                class="card address-card shadow-sm border rounded p-3 address-card-info">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-2 text-primary">
                                                            <i class="fa fa-home me-2"></i> {{ $address->title ?? 'نشانی' }}
                                                        </h6>
                                                        <p class="mb-1">{{ $address->address }}</p>
                                                        <p class="mb-1">
                                                            کد پستی : {{ $address->postal_code ?? '-' }}
                                                        </p>
                                                        <p class="text-muted mb-1">
                                                            <strong> گیرنده
                                                                :</strong> {{ $address->recipient_first_name }} {{ $address->recipient_last_name }}
                                                            | {{ $address->mobile }}
                                                        </p>
                                                    </div>

                                                    <div class="text-end">
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-secondary btn-edit-address"
                                                                data-url="{{ route('ajax_get_address', $address->id) }}">
                                                            <i class="fa fa-pencil-alt"></i>
                                                        </button>

                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-danger btn-delete-address"
                                                                data-url="{{ route('profile_remove_address', $address->id) }}">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center py-5">
                                        <p class="text-muted">هیچ آدرسی ثبت نشده است.</p>

                                    </div>
                                @endforelse

                            </div>

                        </div>
                    </div>
                </div>

                <x-profile-sidebar :user="$user" />
                <!-- مودال افزودن آدرس جدید -->
                <div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel"
                     aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h6 class="modal-title" id="addAddressModalLabel">افزودن آدرس جدید</h6>

                            </div>

                            <form id="addAddressForm" method="POST" action="{{ route('s_profile_add_address') }}">
                                @csrf
                                <div class="modal-body">

                                    <div class="row">
                                        <div class="col-sm-12 col-md-6">
                                            <div class="form-account-title">استان<span class="text-danger">*</span>
                                            </div>
                                            <div class="form-account-row">
                                                <select class="input-field text-right select2"
                                                        name="province"
                                                        id="province"
                                                        onchange="load_cities()">
                                                    <option value="">انتخاب استان</option>
                                                    @foreach($provinces as $province)
                                                        <option
                                                            value="{{ $province['id'] }}" {{ old('province') == $province['id'] ? 'selected' : '' }}>
                                                            {{ $province['title'] }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                @error('province')
                                                <span
                                                    class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                                    role="alert">
                                                    <strong>{{ $message }}</strong>
                                                 </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-sm-12 col-md-6">
                                            <div class="form-account-title">شهر<span class="text-danger">*</span></div>
                                            <div class="form-account-row">
                                                <select class="input-field  select2 text-right"
                                                        name="city"
                                                        id="city">
                                                </select>

                                                @error('city')
                                                <span
                                                    class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                                    role="alert">
                                            <strong>{{ $message }}</strong>
                                              </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">آدرس کامل <span class="text-danger">*</span></label>
                                        <textarea name="address" id="address"
                                                  class="form-control form-control-sm border border-black" rows="3"
                                                  placeholder="مثلاً خیابان، کوچه، پلاک و سایر جزئیات"></textarea>
                                        @error('address')
                                        <span
                                            class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                            role="alert">
                                            <strong>{{ $message }}</strong>
                                              </span>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">کد پستی <span class="text-danger">*</span></label>
                                            <input type="text" name="postal_code" id="postal_code"
                                                   class="input-field text-right"
                                                   placeholder="باید ۱۰ رقم باشد">
                                            @error('postal_code')
                                            <span
                                                class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                                role="alert">
                                            <strong>{{ $message }}</strong>
                                              </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">پلاک <span class="text-danger">*</span></label>
                                            <input type="text" name="no" id="no" class="input-field text-right"
                                            >
                                            @error('no')
                                            <span
                                                class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                                role="alert">
                                            <strong>{{ $message }}</strong>
                                              </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">واحد</label>
                                            <input type="text" name="unit" id="unit" class="input-field text-right">
                                            @error('unit')
                                            <span
                                                class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                                role="alert">
                                            <strong>{{ $message }}</strong>
                                              </span>
                                            @enderror
                                        </div>

                                    </div>

                                    <hr>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">نام گیرنده <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="recipient_first_name"
                                                   id="recipient_first_name"
                                                   class="input-field text-right"
                                            >
                                            @error('recipient_first_name')
                                            <span
                                                class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                                role="alert">
                                            <strong>{{ $message }}</strong>
                                              </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">نام خانوادگی گیرنده <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="recipient_last_name" id="recipient_last_name"
                                                   class="input-field text-right"
                                            >
                                            @error('recipient_last_name')
                                            <span
                                                class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                                role="alert">
                                            <strong>{{ $message }}</strong>
                                              </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">شماره موبایل گیرنده <span
                                                    class="text-danger">*</span></label>
                                            <input type="tel"
                                                   name="mobile"
                                                   id="mobile"
                                                   class="input-field text-right"
                                                   inputmode="numeric"
                                                   pattern="[0-9]*"
                                                   maxlength="11"
                                                   placeholder="مثلاً 09121234567">
                                            @error('mobile')
                                            <span
                                                class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1"
                                                role="alert">
                                            <strong>{{ $message }}</strong>
                                              </span>
                                            @enderror
                                        </div>
                                    </div>

                                </div>

                                <div class="modal-footer">
                                    <button type="submit" class="btn custom-primary px-4">تأیید و ادامه</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>
    <!-- main -->

@endsection


@section('script')

    <script>
        $(document).ready(function() {
            @if ($errors->any())
            $('#addAddressModal').modal({ backdrop: 'static', keyboard: false }).modal('show');
            @endif

            @if($addresses->isEmpty())
            $('#addAddressModal').modal({ backdrop: 'static', keyboard: false }).modal('show');
            @endif

            $('.select2').select2({ placeholder: 'انتخاب کنید', width: '100%', allowClear: true, dir: 'rtl' });
            $('#province').select2({ dropdownParent: $('#addAddressModal'), dir: 'rtl' });
            $('#city').select2({ dropdownParent: $('#addAddressModal'), dir: 'rtl' });

            $('#addAddressForm').on('submit', function(e) {
                e.preventDefault();
                const $form = $(this);
                const action = $form.attr('action');

                $form.find('.alert_required').remove();

                $.ajax({
                    url: action,
                    type: 'POST',
                    data: $form.serialize(),
                    headers: { 'X-CSRF-TOKEN': $form.find('input[name=_token]').val() },
                    success: function(res) {
                        $('#addAddressModal').modal('hide');

                        @if (session('to_url'))
                        window.location.href = "/{{ session('to_url') }}";
                        @endif

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: res.message || 'آدرس ثبت شد',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true

                        }).then(() => {

                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON?.errors || {};
                            Object.entries(errors).forEach(([name, msgs]) => {
                                const $field = $form.find('[name="' + name + '"]');
                                const message = Array.isArray(msgs) ? msgs[0] : msgs;
                                const $container = $field.closest('.form-account-row, .mb-3, .col-md-4');
                                $('<span class="alert_required bg-danger text-white p-1 rounded d-inline-block mt-1" role="alert"><strong>' + message + '</strong></span>').appendTo($container);
                            });
                            $('#addAddressModal').modal({ backdrop: 'static', keyboard: false }).modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'SHILIRAN',
                                text: 'خطای غیرمنتظره',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    }
                });
            });

            $(function() {
                const $modal = $('#addAddressModal');
                const $form = $('#addAddressForm');

                function resetForm() {
                    $form[0].reset();
                    $form.find('.alert_required').remove();
                    $('#province,#city').val(null).trigger('change');
                    $('#city').empty().append('<option value="">انتخاب شهر</option>');
                }


                $modal.on('show.bs.modal', function() {
                    if ($form.find('.alert_required').length) return;
                    resetForm();
                });


                $modal.on('hidden.bs.modal', function() {
                    resetForm();
                });


            });
        });

        function load_cities() {
            const val = $('#province').val();
            if (val) {
                get_cities();
            }
        }

        function get_cities() {
            const province_id = $('#province').val();
            if (!province_id) return;
            $.ajax({
                url: '/ajax_register_customer', type: 'GET', data: { province_id }, success: function(response) {
                    console.log(response);
                    if (response.status === 'success') {
                        add_cities(response.data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'SHILIRAN',
                            text: response.message || 'خطایی رخ داده است',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                }, error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'SHILIRAN',
                        text: 'خطا در ارسال!',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            });
        }

        function add_cities(dataArray) {
            const $select = $('#city');
            $select.empty().append(new Option('انتخاب شهر', ''));
            if (Array.isArray(dataArray)) {
                dataArray.forEach(item => {
                    $select.append(new Option(item.title, item.id));
                });
            }
            const oldCities = "{{ old('city') }}";
            if (oldCities) {
                $select.val(oldCities).trigger('change.select2');
            } else {
                $select.trigger('change.select2');
            }
        }

        $(document).on('click', '.btn-edit-address', function() {
            var url = $(this).attr('data-url');

            console.log(url);
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    if (response.status === 'success') {
                        const data = response.data;

                        $('#addAddressModal').modal('show');

                        setTimeout(() => {
                            // پر کردن فیلدها
                            $('#province').val(String(data.province_id)).trigger('change');
                            setTimeout(() => {
                                $('#city').val(String(data.city_id)).trigger('change');
                            }, 500);

                            $('#address').val(data.address);
                            $('#postal_code').val(data.postal_code);
                            $('#no').val(data.no ?? '');
                            $('#unit').val(data.unit ?? '');
                            $('#recipient_first_name').val(data.recipient_first_name);
                            $('#recipient_last_name').val(data.recipient_last_name);
                            $('#mobile').val(data.mobile);

                            $('#addAddressModalLabel').text('ویرایش آدرس');
                            $('#addAddressForm').attr('action', `/s_profile_edit_address/${data.id}`);
                        }, 300);
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطا',
                        text: 'دریافت اطلاعات آدرس با مشکل مواجه شد'
                    });
                }
            });
        });

        $(document).on('click', '.btn-delete-address', function() {
            console.log('khosro');
            var url = $(this).attr('data-url');
            var row = $(this).closest('.profile-recent-fav-row');

            Swal.fire({
                text: 'آیا از حذف این آدرس مطمئن هستید؟',
                showCancelButton: true,
                confirmButtonText: 'بله',
                cancelButtonText: 'خیر',
                reverseButtons: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#777'
            }).then((result) => {
                console.log(result.value);
                if (result.value) {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(result) {
                            if (result.status === 'success') {
                                row.fadeOut(300, function() {
                                    $(this).remove();
                                });

                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: 'آدرس مورد نظر حذف شد',
                                    showConfirmButton: false,
                                    timer: 2500
                                });
                            }
                        }
                    });
                }
            });
        });

    </script>

@endsection
