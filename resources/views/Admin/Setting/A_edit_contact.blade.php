@extends('Admin.Layout.master')

@section('head-tag')
    <title>ویرایش اطلاعات تماس با ما</title>
@endsection

@section('content')
    <div class="container-fluid pt-2">
        <nav aria-label="breadcrumb" class="mt-0 px-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item font-size-12"><a href="#">خانه</a></li>
                <li class="breadcrumb-item font-size-12"><a href="#">تنظیمات</a></li>
                <li class="breadcrumb-item font-size-12 active" aria-current="page">ویرایش اطلاعات تماس با ما</li>
            </ol>
        </nav>


        <section class="row">
            <div class="col-12">
                <section class="main-body-container">
                    <section class="main-body-container-header">
                        <h5>
                            ویرایش اطلاعات تماس با ما
                        </h5>
                    </section>
                    <div class="d-flex justify-content-between align-items-center mt-2 mb-4 border-bottom pb-2">
                        <a href="{{ route('A_home') }}" class="btn btn-info btn-sm">بازگشت</a>
                    </div>
                    <section>
                        <form action="{{ route('A_s_edit_company_info') }}" method="post" enctype="multipart/form-data"id="form">
                            @csrf
                            <section class="row">

                     
                                <section class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="">آدرس شرکت</label>
                                        <input type="text" name="address" value="{{ $company_info['address']}}"
                                            class="form-control form-control-sm">
                                    </div>
                                    @error('address')
                                        <span class="alert_required bg-danger text-white p-1 rounded" role="alert">
                                            <strong>
                                                {{ $message }}
                                            </strong>
                                        </span>
                                    @enderror
                                </section>
                                <section class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label for="">کد پستی شرکت</label>
                                        <input type="text" name="postal_code" value="{{ $company_info['postal_code']}}"
                                            class="form-control form-control-sm">
                                    </div>
                                    @error('postal_code')
                                        <span class="alert_required bg-danger text-white p-1 rounded" role="alert">
                                            <strong>
                                                {{ $message }}
                                            </strong>
                                        </span>
                                    @enderror
                                </section>
                                <section class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label for="">شماره تلفن 5 رقمی شرکت</label>
                                        <input type="text" name="telephone" value="{{ $company_info['telephone']}}"
                                            class="form-control form-control-sm">
                                    </div>
                                    @error('telephone')
                                        <span class="alert_required bg-danger text-white p-1 rounded" role="alert">
                                            <strong>
                                                {{ $message }}
                                            </strong>
                                        </span>
                                    @enderror
                                </section>
                                <section class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label for="">شماره موبایل شرکت</label>
                                        <input type="text" name="mobile" value="{{ $company_info['mobile']}}"
                                            class="form-control form-control-sm">
                                    </div>
                                    @error('mobile')
                                        <span class="alert_required bg-danger text-white p-1 rounded" role="alert">
                                            <strong>
                                                {{ $message }}
                                            </strong>
                                        </span>
                                    @enderror
                                </section>
                                <section class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label for="">ایمیل شرکت</label>
                                        <input type="text" name="email" value="{{ $company_info['email'] }}"
                                            class="form-control form-control-sm">
                                    </div>
                                    @error('email')
                                        <span class="alert_required bg-danger text-white p-1 rounded" role="alert">
                                            <strong>
                                                {{ $message }}
                                            </strong>
                                        </span>
                                    @enderror
                                </section>
                                <section class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label for="">اینستاگرام شرکت</label>
                                        <input type="text" name="instagram" value="{{ $company_info['instagram'] }}"
                                            class="form-control form-control-sm">
                                    </div>
                                    @error('instagram')
                                        <span class="alert_required bg-danger text-white p-1 rounded" role="alert">
                                            <strong>
                                                {{ $message }}
                                            </strong>
                                        </span>
                                    @enderror
                                </section>
                                <section class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label for="">لینکدین شرکت</label>
                                        <input type="text" name="linkedin" value="{{ $company_info['linkedin'] }}"
                                            class="form-control form-control-sm">
                                    </div>
                                    @error('linkedin')
                                        <span class="alert_required bg-danger text-white p-1 rounded" role="alert">
                                            <strong>
                                                {{ $message }}
                                            </strong>
                                        </span>
                                    @enderror
                                </section>

                                <section class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label for="">ساعات کاری شرکت</label>
                                        <input type="text" name="work_hours" value="{{ $company_info['work_hours'] }}"
                                            class="form-control form-control-sm">
                                    </div>
                                    @error('work_hours')
                                        <span class="alert_required bg-danger text-white p-1 rounded" role="alert">
                                            <strong>
                                                {{ $message }}
                                            </strong>
                                        </span>
                                    @enderror
                                </section>


                                <section class="col-12">
                                    <button class="btn btn-primary btn-sm">ویرایش</button>
                                </section>
                            </section>
                        </form>
                    </section>

                </section>
            </div>
        </section>
    </div>
@endsection

@section('script')
    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'موفقیت‌آمیز',
                text: '{{ session('success') }}',
                confirmButtonText: 'باشه'
            })
        </script>
    @endif
@endsection

