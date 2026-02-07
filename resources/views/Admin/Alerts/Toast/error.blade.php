@if(session('toast-error'))
    <div aria-live="polite" aria-atomic="true" style="position: fixed; top: 60px; left: 10px; z-index: 9999;">
        <div class="toast bg-danger text-white" role="alert" aria-live="assertive" aria-atomic="true"
             data-delay="10000" data-autohide="true"
             style="width: auto; max-width: 400px;">

            <div class="toast-header bg-danger text-white d-flex flex-row-reverse">
                <strong class="mr-auto">خطا</strong>
                <button type="button" class="ml-2 mb-1 close" data-bs-dismiss="toast" aria-label="بستن">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="toast-body">
                {{ session('toast-error') }}
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('.toast').toast('show');
        });
    </script>
@endif
