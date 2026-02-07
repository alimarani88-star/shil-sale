@extends('Admin.Layout.master')

@section('head-tag')

    <title>بسته بندی مرسولات پستی</title>

@endsection

@section('content')
    <div class="container-fluid pt-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">خانه</a></li>
                <li class="breadcrumb-item ">بسته بندی</li>
                <li class="breadcrumb-item active">بسته بندی پستی</li>
            </ol>
        </nav>

        <div class="card ">
            <div class="card-header">
                <h3>الگو های بسته بندی کارتن های پستی</h3>
            </div>
            <div class="card-body">
                <div class="row py-3">
                    <div class="col-12 col-sm-6 col-md-3">
                        <label>انتخاب کارتن پست</label>
                        <select class="single-select form-select" >
                            <option value="0"> کارتن پست(طول * عرض * ارتفاع)</option>
                            @foreach($cartons as $carton)
                                <option class="{{$carton->id}}">{{$carton->name}} ({{$carton->length}} * {{$carton->width}} * {{$carton->height}})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-12 col-sm-6 col-md-4 ">
                        <div class="card text-bg-light mb-3 " >
                            <div class="card-header bg-success">
                               <div class="d-flex flex-row justify-content-between w-100">
                                   <h3 class="text-white">الگو 1</h3>
                                   <div class="dropdown">
                                       <button class="btn float-left dropdown-toggle text-white" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                          عملیات
                                       </button>
                                       <ul class="dropdown-menu">
                                           <li><a class="dropdown-item" style="color: #414141!important;" href="#"><i class="fa fa-pencil mx-1" aria-hidden="true"></i>ویرایش</a></li>
                                           <li><a class="dropdown-item" style="color: #ed3e3e!important;" href="#"><i class="fa fa-trash mx-1" aria-hidden="true"></i>حذف</a></li>
                                       </ul>
                                   </div>
                               </div>
                            </div>
                            <div class="card-body">
                                <p class="card-text mb-0">کارتن شماره 2 : <b class="float-left">8 عدد</b></p>
                                <p class="card-text mb-0">کارتن شماره 3 : <b class="float-left">8 عدد</b></p>
                                <p class="card-text mb-0">کارتن شماره 5 : <b class="float-left">8 عدد</b></p>
                                <p class="card-text mb-0">کارتن شماره 10 : <b class="float-left">8 عدد</b></p>
                            </div>
                        </div>
                    </div>



                </div>

            </div>
            <div class="card-footer"></div>
        </div>
    </div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('.single-select').select2({
            allowClear: true,
            language: 'fa',
            dir: 'rtl',
            placeholder: 'انتخاب کارتن',
        });
    })
</script>

@endsection
