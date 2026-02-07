@extends('Admin.Layout.master')

@section('head-tag')
    <title>داشبورد اصلی</title>
@endsection

@section('content')
    <section class="row mt-2">
        <section class="col-lg-3 col-md-6 col-12">
            <a href="#" class="text-decoration-none d-block mb-4">
                <section class="card bg-custom-yellow text-white">
                    <section class="card-body">
                        <section class="d-flex justify-content-between">
                            <section class="info-box-body">
                                <h5>30,200 تومان</h5>
                                <p>سود خالص</p>
                            </section>
                            <section class="info-box-icon">
                                <i class="fas fa-chart-line"></i>
                            </section>
                        </section>
                    </section>
                    <section class="card-footer info-box-footer">
                        <i class="fas fa-clock mx-2"></i> به روز رسانی شده در : 21:42 بعد از ظهر
                    </section>
                </section>
            </a>
        </section>
        <!-- <section class="col-lg-3 col-md-6 col-12">
            <a href="#" class="text-decoration-none d-block mb-4">
                <section class="card bg-custom-green text-white">
                    <section class="card-body">
                        <section class="d-flex justify-content-between">
                            <section class="info-box-body">
                                <h5>30,200 تومان</h5>
                                <p>سود خالص</p>
                            </section>
                            <section class="info-box-icon">
                                <i class="fas fa-chart-line"></i>
                            </section>
                        </section>
                    </section>
                    <section class="card-footer info-box-footer">
                        <i class="fas fa-clock mx-2"></i> به روز رسانی شده در : 21:42 بعد از ظهر
                    </section>
                </section>
            </a>
        </section>
        <section class="col-lg-3 col-md-6 col-12">
            <a href="#" class="text-decoration-none d-block mb-4">
                <section class="card bg-custom-pink text-white">
                    <section class="card-body">
                        <section class="d-flex justify-content-between">
                            <section class="info-box-body">
                                <h5>30,200 تومان</h5>
                                <p>سود خالص</p>
                            </section>
                            <section class="info-box-icon">
                                <i class="fas fa-chart-line"></i>
                            </section>
                        </section>
                    </section>
                    <section class="card-footer info-box-footer">
                        <i class="fas fa-clock mx-2"></i> به روز رسانی شده در : 21:42 بعد از ظهر
                    </section>
                </section>
            </a>
        </section>
        <section class="col-lg-3 col-md-6 col-12">
            <a href="#" class="text-decoration-none d-block mb-4">
                <section class="card bg-custom-yellow text-white">
                    <section class="card-body">
                        <section class="d-flex justify-content-between">
                            <section class="info-box-body">
                                <h5>30,200 تومان</h5>
                                <p>سود خالص</p>
                            </section>
                            <section class="info-box-icon">
                                <i class="fas fa-chart-line"></i>
                            </section>
                        </section>
                    </section>
                    <section class="card-footer info-box-footer">
                        <i class="fas fa-clock mx-2"></i> به روز رسانی شده در : 21:42 بعد از ظهر
                    </section>
                </section>
            </a>
        </section>
        <section class="col-lg-3 col-md-6 col-12">
            <a href="#" class="text-decoration-none d-block mb-4">
                <section class="card bg-danger text-white">
                    <section class="card-body">
                        <section class="d-flex justify-content-between">
                            <section class="info-box-body">
                                <h5>30,200 تومان</h5>
                                <p>سود خالص</p>
                            </section>
                            <section class="info-box-icon">
                                <i class="fas fa-chart-line"></i>
                            </section>
                        </section>
                    </section>
                    <section class="card-footer info-box-footer">
                        <i class="fas fa-clock mx-2"></i> به روز رسانی شده در : 21:42 بعد از ظهر
                    </section>
                </section>
            </a>
        </section>
        <section class="col-lg-3 col-md-6 col-12">
            <a href="#" class="text-decoration-none d-block mb-4">
                <section class="card bg-success text-white">
                    <section class="card-body">
                        <section class="d-flex justify-content-between">
                            <section class="info-box-body">
                                <h5>30,200 تومان</h5>
                                <p>سود خالص</p>
                            </section>
                            <section class="info-box-icon">
                                <i class="fas fa-chart-line"></i>
                            </section>
                        </section>
                    </section>
                    <section class="card-footer info-box-footer">
                        <i class="fas fa-clock mx-2"></i> به روز رسانی شده در : 21:42 بعد از ظهر
                    </section>
                </section>
            </a>
        </section>
        <section class="col-lg-3 col-md-6 col-12">
            <a href="#" class="text-decoration-none d-block mb-4">
                <section class="card bg-warning text-white">
                    <section class="card-body">
                        <section class="d-flex justify-content-between">
                            <section class="info-box-body">
                                <h5>30,200 تومان</h5>
                                <p>سود خالص</p>
                            </section>
                            <section class="info-box-icon">
                                <i class="fas fa-chart-line"></i>
                            </section>
                        </section>
                    </section>
                    <section class="card-footer info-box-footer">
                        <i class="fas fa-clock mx-2"></i> به روز رسانی شده در : 21:42 بعد از ظهر
                    </section>
                </section>
            </a>
        </section>
        <section class="col-lg-3 col-md-6 col-12">
            <a href="#" class="text-decoration-none d-block mb-4">
                <section class="card bg-primary text-white">
                    <section class="card-body">
                        <section class="d-flex justify-content-between">
                            <section class="info-box-body">
                                <h5>30,200 تومان</h5>
                                <p>سود خالص</p>
                            </section>
                            <section class="info-box-icon">
                                <i class="fas fa-chart-line"></i>
                            </section>
                        </section>
                    </section>
                    <section class="card-footer info-box-footer">
                        <i class="fas fa-clock mx-2"></i> به روز رسانی شده در : 21:42 بعد از ظهر
                    </section>
                </section>
            </a>
        </section> -->

    </section>

    <section class="row">
        <section class="col-12">
            <section class="main-body-container">
                <section class="main-body-container-header">
                    <h4>
                        قسمت مدیر سیستم
                    </h4>
                    <p>
                        بخش ورود اطلاعات و گزارش ها
                    </p>
                </section>
                <section class="body-content">



                </section>
            </section>
        </section>
    </section>

@endsection

