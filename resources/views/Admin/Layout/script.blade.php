<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap 4 -->
{{--<script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>--}}

<script src="{{asset('admin-assets/js/bootstrap.bundle.min.js')}}"></script>


<!-- AdminLTE App -->
<script src="{{asset('dist/js/adminlte.min.js')}}"></script>
<script src="{{asset('admin-assets/vendor/persianDatepicker-master/js/persianDatepicker.js')}}"></script>
{{--<script src="{{asset('plugins/select2/select2.css')}}"></script>--}}

<script src="{{ asset('admin-assets/select2/js/select2.min.js') }}"></script>

<script type="text/javascript" src="{{asset('./persianDatepicker-master/js/persianDatepicker.min.js')}}"></script>

<script src="{{asset('admin-assets/sweetalert/sweetalert2.min.js')}}"></script>

<script src="{{asset('admin-assets/toast/toastr.min.js')}}"></script>

<script src="{{asset('admin-assets/datatable/dataTables.min.js')}}"></script>

<script> // کامنت نشود
    $(document).ready(function () {
        $('.multi-select').select2();
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(el => new bootstrap.Tooltip(el))
    })
</script>












