@if(session('success'))

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const Toast = Swal.mixin({
                toast: true,
                dir: 'rtl',
                position: 'top-end',
                showConfirmButton: false,
                timer: 4500,
                timerProgressBar: true,
                background: '#ffff',
                color: '#000000ff',
                iconColor: '#fff',
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
                title: '{{ session('success') }}'
            });
        });
    </script>
    <style>
        .swal-toast-container {
          direction: rtl !important;
        }

    </style>

@endif
