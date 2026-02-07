@if(session('toast-success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-start',
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true,
                background: 'green',

                iconColor: 'white',
                customClass: {
                    container: 'swal-toast-container'
                },
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: 'success',
                title: '{{ session('toast-success') }}'
            });
        });
    </script>

    <style>
        .swal-toast-container {
            z-index: 99999 !important;
            background: green;
        }

    </style>
@endif
